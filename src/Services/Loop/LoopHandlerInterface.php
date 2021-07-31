<?php

namespace Stackflows\StackflowsPlugin\Services\Loop;

use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;

interface LoopHandlerInterface
{
    /**
     * @throws TooManyErrors
     */
    public function handle(): void;
}
