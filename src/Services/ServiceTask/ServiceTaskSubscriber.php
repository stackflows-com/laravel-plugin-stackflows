<?php

namespace Stackflows\StackflowsPlugin\Services\ServiceTask;

use Psr\Log\LoggerInterface;
use Stackflows\GatewayApi\ApiException;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\LoopHandlerInterface;

final class ServiceTaskSubscriber implements LoopHandlerInterface
{
    private ServiceTaskChannel $api;
    private LoggerInterface $logger;

    /** ServiceTaskExecutorInterface[] */
    private iterable $executors;

    /** @var array<string, int> */
    private array $errors;

    public function __construct(ServiceTaskChannel $api, LoggerInterface $logger, iterable $executors)
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
     * @return ServiceTask[]|null
     * @throws TooManyErrors|ApiException
     */
    private function fetch(ServiceTaskExecutorInterface $executor): array | null
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
            $errorMap[$obj::class] = 0;
        }

        return $errorMap;
    }
}
