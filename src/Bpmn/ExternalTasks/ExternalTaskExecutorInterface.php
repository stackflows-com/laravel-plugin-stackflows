<?php

namespace Stackflows\StackflowsPlugin\Bpmn\ExternalTasks;

use Stackflows\StackflowsPlugin\Bpmn\Inputs\ExternalTaskRequestInterface;
use Stackflows\StackflowsPlugin\Bpmn\Outputs\ExternalTaskOutputInterface;

interface ExternalTaskExecutorInterface
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
     * @param ExternalTaskRequestInterface $task
     * @return ExternalTaskOutputInterface
     */
    public function execute(ExternalTaskRequestInterface $task): ExternalTaskOutputInterface;

    /**
     * Defines camunda external task request object class that has
     * all required variables and implements ExternalTaskRequestInterface.
     *
     * @return string
     */
    public function getRequestObjectClass(): string;
}
