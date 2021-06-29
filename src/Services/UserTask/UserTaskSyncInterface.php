<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

interface UserTaskSyncInterface
{
    /**
     * Synchronize user tasks.
     *
     * @param array $items User tasks.
     * @param array $params Context.
     */
    public function sync(array $items, array $params = []): void;
}
