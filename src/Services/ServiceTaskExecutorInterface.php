<?php

namespace Stackflows\StackflowsPlugin\Services;

use Stackflows\GatewayApi\Model\ServiceTask;

interface ServiceTaskExecutorInterface
{
    /**
     * @return string[]
     */
    public function getReference(): array;

    /**
     * Get the duration of blocking service tasks in milliseconds.
     *
     * @return int
     */
    public function getLockDuration(): int;

    /**
     * Execute the service task.
     *
     * @param ServiceTask $task
     * @return ServiceTask
     *
     * @throws \Exception
     */
    public function execute(ServiceTask $task): ServiceTask;
}
