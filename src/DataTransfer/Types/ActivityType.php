<?php

namespace Stackflows\DataTransfer\Types;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;

class ActivityType extends DataTransferObject implements Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $reference;
}
