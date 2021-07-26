<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

use Stackflows\GatewayApi\Model\UserTask;

interface UserTaskSyncInterface
{
    /**
     * Synchronize user tasks.
     *
     * @param array|UserTask[] $items User tasks.
     * @param array $params Context.
     */
    public function sync(array $items, array $params = []): void;
}
