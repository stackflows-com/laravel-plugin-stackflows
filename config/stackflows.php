<?php

return [
    // Address of the Stack Flows API.
    'apiHost' => env('STACKFLOWS_API_HOST'),

    // API Auth token
    'authToken' => env('STACKFLOWS_AUTH_TOKEN'),
    'environmentToken' => env('STACKFLOWS_ENVIRONMENT_TOKEN'),

    /*
     * External task executors are classes that handle Stackflows external tasks.
     * Must implements interface Stackflows\StackflowsPlugin\Bpmn\ExternalTasks\ExternalTaskExecutorInterface
     */
    'external_task_executors' => [],

    // Implementation is in the development
    'user_task_sync' => [],
];
