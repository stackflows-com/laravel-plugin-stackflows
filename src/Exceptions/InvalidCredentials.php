<?php

namespace Stackflows\StackflowsPlugin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidCredentials extends Exception
{
    #[Pure]
    public static function emailOrPassword(): self
    {
        return new self('Invalid email or password.');
    }

    #[Pure]
    public static function token(): self
    {
        return new self('The authentication token is invalid.');
    }
}
