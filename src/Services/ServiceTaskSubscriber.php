<?php

namespace Stackflows\StackflowsPlugin\Services;

use Illuminate\Log\LogManager;
use Stackflows\GatewayApi\ApiException;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;

final class ServiceTaskSubscriber
{
    private ServiceTaskChannel $api;
    private LogManager $logger;
    private bool $stopped = false;

    /** @var ServiceTaskExecutorInterface[] */
    private iterable $executors;

    /** @var array<string, int> */
    private array $executorErrors;

    public function __construct(ServiceTaskChannel $api, LogManager $logger)
    {
        $this->api = $api;
        $this->logger = $logger;
    }

    public function setExecutors(iterable $executors): self
    {
        $this->executors = $executors;
        $this->executorErrors = $this->getErrorMap($executors);

        return $this;
    }

    /**
     * @throws TooManyErrors
     */
    public function listen(): void
    {
        if (empty($this->executors)) {
            return;
        }

        while (! $this->stopped) {
            foreach ($this->executors as $executor) {
                $tasks = $this->fetch($executor);
                if (is_null($tasks)) {
                    continue;
                }
                $this->handle($executor, $tasks);
            }
            // Todo: make more gracefully
            sleep(1);
        }
    }

    public function stop(): void
    {
        $this->stopped = true;
    }

    /**
     * @throws TooManyErrors
     */
    private function handle(ServiceTaskExecutorInterface $executor, array $tasks): void
    {
        foreach ($tasks as $task) {
            try {
                $executedTask = $executor->execute($task);
                $this->complete($executedTask);
                $this->executorErrors[$executor::class] = 0;
            } catch (\Throwable $e) {
                $this->logger->error(sprintf("%s %s(%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
                $this->executorErrors[$executor::class] += 1;
            }
        }

        if ($this->executorErrors[$executor::class] >= 7) {
            throw TooManyErrors::executorHasTooManyErrors($executor::class);
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
     * @return ServiceTask[]|null
     * @throws TooManyErrors
     * @throws ApiException
     */
    private function fetch(ServiceTaskExecutorInterface $executor): array | null
    {
        return $this->api->getPending($executor);
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
