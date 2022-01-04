<?php

namespace Stackflows\BusinessProcesses\ServiceTasks;

use Stackflows\BusinessProcesses\ServiceTasks\Inputs\ServiceTaskInputInterface;
use Stackflows\BusinessProcesses\ServiceTasks\Outputs\ServiceTaskOutputInterface;

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
     * @param ServiceTaskInputInterface $task
     * @return ServiceTaskOutputInterface
     */
    public function execute(ServiceTaskInputInterface $task): ?ServiceTaskOutputInterface;

    /**
     * Defines camunda external task request object class that has
     * all required variables and implements ServiceTaskRequestInterface.
     *
     * @return string
     */
    public function getInputClass(): string;
}
