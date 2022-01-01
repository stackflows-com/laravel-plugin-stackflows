<?php

namespace Stackflows\Http\Client;

class ErrorMap
{
    private const MAP = [
        1 => 'BusinessProcesses resources cannot be parsed',
        2 => 'Process definition not created',
        3 => 'Deployment or a deployment resource for the given deployment does not exist',
        4 => 'Unexpected deployment exception',
        10 => 'Query parameters are invalid',
        11 => 'Unsuccessful fetch',
        12 => 'Complete unsuccessful. Task\'s most recent lock was not acquired',
        13 => 'Task does not exist',
        14 => 'Process instance could not be resumed successfully',
        15 => 'Unexpected external task exception',
        20 => 'Incident list query parameters are invalid',
        21 => 'Unexpected incident exception',
        30 => 'Process definition with given key does not exist',
        31 => 'Unexpected process start variable exception',
        32 => 'Invalid variable value or type',
        33 => 'The instance could not be created successfully',
        34 => 'Unexpected form start exception',
        40 => 'Query parameters are invalid',
        41 => 'Unexpected instance exception',
        51 => 'Invalid task retrieval query parameters',
        52 => 'Task does not exist or the corresponding process instance could not be resumed successfully.',
        53 => 'Escalation code is not provided in the request',
        54 => 'User unauthorized to update the process instance',
        55 => 'Unexpected task exception',
        61 => 'Identity service is read-only',
    ];

    public static function map(int $code, string $defaultMessage = null): ?string
    {
        return self::MAP[$code] ?? $defaultMessage;
    }
}
