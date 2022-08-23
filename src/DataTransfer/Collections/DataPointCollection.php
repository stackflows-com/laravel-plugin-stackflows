<?php

namespace Stackflows\DataTransfer\Collections;

use Illuminate\Support\Collection;
use Stackflows\DataTransfer\Types\DataPointType;

class DataPointCollection extends Collection
{
    public function offsetGet($key): DataPointType
    {
        return parent::offsetGet($key);
    }
}
