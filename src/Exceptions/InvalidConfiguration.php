<?php

namespace Stackflows\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidConfiguration extends Exception
{
    #[Pure]
    public static function hostNotSpecified(): self
    {
        return new self(
            'There was no Stackflows Gateway host specified.'
        );
    }

    #[Pure]
    public static function versionNotSpecified(): self
    {
        return new self(
            "There was no version provided for Stackflows Gateway."
        );
    }

    #[Pure]
    public static function tokenNotSpecified(): self
    {
        return new self(
            "There was no auth token provided to authorize Stackflows Gateway."
        );
    }
}
