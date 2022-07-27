<?php

namespace Stackflows\Exceptions;

class ExecutorMissingDataException extends ExecutorException
{
    public function __construct(array $missing, array $context = [])
    {
        parent::__construct('Executor is missing data', ['missing' => $missing] + $context);
    }
}
