<?php

namespace Stackflows\Exceptions;

use Exception;

class ExecutorException extends Exception
{
    protected array $context;

    public function __construct(string $message, array $context = [])
    {
        $this->context = $context;

        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
