<?php

namespace Stackflows;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Stackflows\Stackflows
 */
class StackflowsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'stackflows';
    }
}
