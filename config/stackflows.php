<?php

return [
    /*
     * Address of the Stack Flow Gateway API.
     */
    'host' => env('STACKFLOWS_HOST'),

    /*
     * Stackflows instance UUID.
     */
    'instance' => env('STACKFLOWS_INSTANCE'),

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
