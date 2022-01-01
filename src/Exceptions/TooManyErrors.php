<?php

namespace Stackflows\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class TooManyErrors extends Exception
{
    #[Pure]
    public static function executorHasTooManyErrors(string $class): self
    {
        return new self("The executor {$class} has too many errors.");
    }

    #[Pure]
    public static function synchronizerHasTooManyErrors(string $class): self
    {
        return new self("The synchronizer {$class} has too many errors.");
    }

    #[Pure]
    public static function tooManyHttpErrors(string $msg): self
    {
        return new self("Too many http errors. {$msg}");
    }
}
