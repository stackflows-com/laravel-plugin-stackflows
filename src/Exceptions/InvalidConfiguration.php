<?php

namespace Stackflows\StackflowsPlugin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidConfiguration extends Exception
{
    #[Pure]
    public static function apiHostNotSpecified(): self
    {
        return new self(
            'There was no Stackflows Gateway host specified.'
        );
    }

    #[Pure]
    public static function authTokenNotSpecified(): self
    {
        return new self(
            "There was no auth token provided to authorize Stackflows Gateway."
        );
    }
}
