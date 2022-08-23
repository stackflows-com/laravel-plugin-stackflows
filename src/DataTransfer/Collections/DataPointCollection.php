<?php

namespace Stackflows\DataTransfer\Collections;

use Stackflows\DataTransfer\Types\DataPointType;
use Illuminate\Support\Collection;

class DataPointCollection extends Collection
{
    public function offsetGet($key): DataPointType
    {
        return parent::offsetGet($key);
    }
}
