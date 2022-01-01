<?php

return [
    // For debugging purposes you might want to set this to false, otherwise it should always be true
    'secure' => env('STACKFLOWS_SECURE', true),

    // Host of the Stackflows API
    'host' => env('STACKFLOWS_HOST', 'backoffice.stackflows.com'),

    // You can use specific version for calling Stackflows API
    'version' => env('STACKFLOWS_VERSION', '2'),

    // Company environment token that is used for application wide authentication
    'token' => env('STACKFLOWS_TOKEN'),
];
