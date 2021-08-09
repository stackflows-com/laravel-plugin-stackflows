<?php

return [
    // Address of the Stack Flow Gateway API.
    'host' => env('STACKFLOWS_HOST'),

    // Address of the Stack Flow Gateway API.
    'backofficeHost' => env('STACKFLOWS_BACKOFFICE_HOST'),

    // Stackflows instance UUID.
    'instance' => env('STACKFLOWS_INSTANCE'),

    // Stackflows credentials.
    'email' => env('STACKFLOWS_EMAIL'),
    'password' => env('STACKFLOWS_PASSWORD'),

    /*
     * The Token Provider manages of the token string.
     * Must implements interface \Stackflows\StackflowsPlugin\Auth\TokenProviderInterface
     */
    'token_provider' => \Stackflows\StackflowsPlugin\Auth\InMemoryTokenProvider::class,

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
