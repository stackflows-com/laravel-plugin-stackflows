<?php

return [
    // For debugging purposes you might want to set this to false, otherwise it should always be true
    'protocol' => env('STACKFLOWS_SECURE', 'https'),

    // Host of the Stackflows API
    'host' => env('STACKFLOWS_HOST', 'backoffice.stackflows.com'),

    // Company environment token that is used for application wide authentication
    'token' => env('STACKFLOWS_TOKEN'),
];
