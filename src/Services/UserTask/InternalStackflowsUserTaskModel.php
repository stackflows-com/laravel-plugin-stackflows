<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

interface InternalStackflowsUserTaskModel
{
    /**
     * Get the task ID in the Stackflows.
     */
    public function getStackflowsUserTaskKey(): string;

    /**
     * Get the internal model ID.
     * @return mixed
     */
    public function getKey();

    /**
     * Get the Stackflows taskDefinitionKey.
     */
    public function getStackflowsUserTaskDefinitionKey(): string;
}
