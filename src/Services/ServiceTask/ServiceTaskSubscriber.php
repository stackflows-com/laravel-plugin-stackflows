<?php

namespace Stackflows\StackflowsPlugin\Services\ServiceTask;

use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;

final class ServiceTaskSubscriber
{
    /** ServiceTaskExecutorInterface[] */
    private iterable $executors;

    /** @var array<string, int> */
    private array $errors;

    private GatewayClient $client;

    public function __construct(GatewayClient $client, iterable $executors)
    {
        $this->client = $client;
        $this->executors = $executors;
        $this->errors = $this->getErrorMap($executors);
    }

    public function handle(): void
    {
        foreach ($this->executors as $executor) {
            $tasks = $this->fetch($executor);
            $this->execute($executor, $tasks);
        }
    }

    /**
     * @param ServiceTaskExecutorInterface $executor
     * @return mixed[]
     */
    private function fetch(ServiceTaskExecutorInterface $executor): array
    {
//        return $this->api->getPending($executor->getReference(), $executor->getLockDuration());
        return [];
    }

    private function execute(ServiceTaskExecutorInterface $executor, array $tasks): void
    {
        foreach ($tasks as $task) {
            try {
                $executedTask = $executor->execute($task);
                $this->complete($executedTask);
                $this->errors[get_class($executor)] = 0;
            } catch (\Exception $e) {
//                $this->logger->error(sprintf("%s %s(%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
//                $this->errors[get_class($executor)] += 1;
            }
        }
    }

    private function complete($task)
    {
//        $this->api->complete($task->getId(), $task->getVariables());
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
