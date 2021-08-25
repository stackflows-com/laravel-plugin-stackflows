<?php

return [
    // Address of the Stack Flow Gateway API.
    'gatewayHost' => env('STACKFLOWS_GATEWAY_HOST'),

    // Gateway Auth token
    'authToken' => env('STACKFLOWS_AUTH_TOKEN'),

    /*
     * Service task executors are classes that handle Stackflows service tasks.
     * Must implements interface \Stackflows\StackflowsPlugin\Services\ServiceTask\ServiceTaskExecutorInterface
     */
    'service_task_executors' => [],

    /*
     * User task synchronizers are classes that handle Stackflows service tasks.
     * Must implements interface \Stackflows\StackflowsPlugin\Services\UserTask\UserTaskSyncInterface
     */
    'user_task_sync' => [],
];
