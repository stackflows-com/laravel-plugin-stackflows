<?php

namespace Stackflows\StackflowsPlugin\Services\ServiceTask;

interface ServiceTaskExecutorInterface
{
    /**
     * @return string
     */
    public function getTopic(): string;

    /**
     * Get the duration of blocking service tasks in milliseconds.
     *
     * @return int
     */
    public function getLockDuration(): int;

    /**
     * Execute the service task.
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function execute(): ?array;
}
