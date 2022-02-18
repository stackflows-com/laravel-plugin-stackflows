<?php

use Stackflows\Enum\VariableTypes;
use Stackflows\Enum\VariableValueFormats;

return [
    // For debugging purposes you might want to set this to false, otherwise it should always be true
    'secure' => env('STACKFLOWS_SECURE', true),

    // Host of the Stackflows API
    'host' => env('STACKFLOWS_HOST', 'backoffice.stackflows.com'),

    // You can use specific version for calling Stackflows API
    'version' => env('STACKFLOWS_VERSION', '2'),

    // Company environment token that is used for application wide authentication
    'token' => env('STACKFLOWS_TOKEN'),

    // Variable types configuration
    'variable_types' => [
        VariableTypes::TYPE_STRING => [
            'name' => 'String',
            'value_format' => VariableValueFormats::FORMAT_STRING,
        ],
        VariableTypes::TYPE_DATE => [
            'name' => 'Date',
            'value_format' => VariableValueFormats::FORMAT_DATE,
        ],
        VariableTypes::TYPE_DATE_TIME => [
            'name' => 'Date and time',
            'value_format' =>  VariableValueFormats::FORMAT_DATE_TIME,
        ],
        VariableTypes::TYPE_BOOLEAN => [
            'name' => 'Boolean',
            'value_format' => VariableValueFormats::FORMAT_BOOLEAN,
        ],
        VariableTypes::TYPE_ARRAY => [
            'name' => 'Array',
            'value_format' => VariableValueFormats::FORMAT_ARRAY,
        ],
        VariableTypes::TYPE_NUMBER => [
            'name' => 'Number',
            'value_format' => VariableValueFormats::FORMAT_NUMBER,
        ],
        VariableTypes::TYPE_INTEGER => [
            'name' => 'Integer',
            'value_format' => VariableValueFormats::FORMAT_NUMBER,
        ],
        VariableTypes::TYPE_CUSTOM_EXAMPLE => [
            'name' => 'Custom example',
            'value_format' => VariableValueFormats::FORMAT_BUILDER,
        ],
    ]
];
