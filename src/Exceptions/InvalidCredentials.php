<?php

namespace Stackflows\StackflowsPlugin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidCredentials extends Exception
{
    #[Pure]
    public static function emailOrPassword(): self
    {
        return new static('Invalid email or password.');
    }

    #[Pure]
    public static function token(): self
    {
        return new static('The authentication token is invalid.');
    }
}
