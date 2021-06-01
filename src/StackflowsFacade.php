<?php

namespace Stackflows\StackflowsPlugin;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stackflows\StackflowsPlugin\Stackflows
 */
class StackflowsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stackflows';
    }
}
