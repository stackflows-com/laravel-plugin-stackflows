<?php

namespace Stackflows\Exceptions;

use Exception;

class SubmissionItemUnexpectedTypeException extends Exception
{
    public function __construct(string $type)
    {
        parent::__construct(sprintf('Type "%s" is not supported', $type));
    }
}
