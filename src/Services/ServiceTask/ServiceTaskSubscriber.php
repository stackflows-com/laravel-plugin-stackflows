<?php

namespace Stackflows\StackflowsPlugin\Services\ServiceTask;

use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;

final class ServiceTaskSubscriber implements LoopHandlerInterface
{
    private ServiceTaskChannel $api;
    private LoopLogger $logger;

    /** ServiceTaskExecutorInterface[] */
    private iterable $executors;

    /** @var array<string, int> */
    private array $errors;

    public function __construct(ServiceTaskChannel $api, LoopLogger $logger, iterable $executors)
    {
        $this->api = $api;
        $this->logger = $logger;
        $this->executors = $executors;
        $this->errors = $this->getErrorMap($executors);
    }

    /**
     * @throws TooManyErrors|ApiException
     */
    public function handle(): void
    {
        foreach ($this->executors as $executor) {
            $tasks = $this->fetch($executor);
            $this->execute($executor, $tasks);
        }
    }

    /**
     * @param ServiceTaskExecutorInterface $executor
     * @return ServiceTask[]
     * @throws TooManyErrors|ApiException
     */
    private function fetch(ServiceTaskExecutorInterface $executor): array
    {
        return $this->api->getPending($executor->getReference(), $executor->getLockDuration());
    }

    /**
     * @throws TooManyErrors
     */
    private function execute(ServiceTaskExecutorInterface $executor, array $tasks): void
    {
        foreach ($tasks as $task) {
            try {
                $executedTask = $executor->execute($task);
                $this->complete($executedTask);
                $this->errors[get_class($executor)] = 0;
            } catch (\Exception $e) {
                $this->logger->error(sprintf("%s %s(%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
                $this->errors[get_class($executor)] += 1;
            }
        }

        if ($this->errors[get_class($executor)] >= 7) {
            throw TooManyErrors::executorHasTooManyErrors(get_class($executor));
        }
    }

    /**
     * @throws ApiException
     */
    private function complete(ServiceTask $task)
    {
        $this->api->complete($task->getId(), $task->getVariables());
    }

    /**
     * @return array<string, int>
     */
    private function getErrorMap(iterable $executors): array
    {
        $errorMap = [];
        foreach ($executors as $obj) {
            $errorMap[get_class($obj)] = 0;
        }

        return $errorMap;
    }
}
