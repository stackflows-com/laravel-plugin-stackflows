<?php

return [
    // Address of the Stack Flow Gateway API.
    'apiHost' => env('STACKFLOWS_API_HOST'),

    // Gateway Auth token
    'authToken' => env('STACKFLOWS_AUTH_TOKEN'),

    /*
     * External task executors are classes that handle Stackflows external tasks.
     * Must implements interface Stackflows\StackflowsPlugin\Bpmn\ExternalTasks\ExternalTaskExecutorInterface
     */
    'external_task_executors' => [],

    // Implementation is in the development
    'user_task_sync' => [],
];
