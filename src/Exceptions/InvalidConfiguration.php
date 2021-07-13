<?php

namespace Stackflows\StackflowsPlugin\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;

class InvalidConfiguration extends Exception
{
    #[Pure]
    public static function hostNotSpecified(): static
    {
        return new static(
            'There was no Stackflows Gateway host specified. You must provide a valid host to fetch data.'
        );
    }

    #[Pure]
    public static function instanceNotSpecified(): static
    {
        return new static('There was no Stackflows Instance specified.');
    }

    #[Pure]
    public static function backofficeHostNotSpecified(): static
    {
        return new static(
            'There was no Stackflows Backoffice host specified.'
        );
    }
}
