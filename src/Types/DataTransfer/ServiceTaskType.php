<?php

namespace Stackflows\Types\DataTransfer;

use Spatie\DataTransferObject\DataTransferObject;

class ServiceTaskType extends DataTransferObject
{
    public string $reference;

    public string $topic;

    public ?string $priority;
}
