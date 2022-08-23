<?php

namespace Stackflows\DataTransfer\Types;

use Spatie\DataTransferObject\DataTransferObject;

class EventType extends DataTransferObject
{
    public string $name;

    public string $type;
}
