<?php

namespace Stackflows\Exceptions;

use Exception;

class MissingInheritanceException extends Exception
{
    public function __construct($subject, string $inheritance)
    {
        parent::__construct(sprintf(
            'Required "%s" inheritance is missing for class "%s"',
            $inheritance,
            get_class($subject)
        ));
    }
}
