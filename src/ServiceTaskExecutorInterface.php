<?php

namespace Stackflows\StackflowsPlugin;

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

    public function execute(ServiceTask $task): void;
}
