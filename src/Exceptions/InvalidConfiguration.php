<?php

namespace Stackflows\StackflowsPlugin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidConfiguration extends Exception
{
    #[Pure]
    public static function hostNotSpecified(): self
    {
        return new static(
            'There was no Stackflows Gateway host specified. You must provide a valid host to fetch data.'
        );
    }

    #[Pure]
    public static function instanceNotSpecified(): self
    {
        return new static('There was no Stackflows Instance specified.');
    }

    #[Pure]
    public static function backofficeHostNotSpecified(): self
    {
        return new static(
            'There was no Stackflows Backoffice host specified.'
        );
    }

    #[Pure]
    public static function invalidTokenProvider(string $class): self
    {
        return new static(
            "The token provider {$class} must implement interface TokenProviderInterface."
        );
    }
}
