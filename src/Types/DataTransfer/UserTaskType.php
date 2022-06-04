<?php

namespace Stackflows\Types\DataTransfer;

use Spatie\DataTransferObject\DataTransferObject;

class UserTaskType extends DataTransferObject
{
    /**
     * @var string
     */
    public string $reference;

    /**
     * @var string
     */
    public string $subject;

    /**
     * @var VariableCollectionType|null
     */
    public ?VariableCollectionType $properties;
}
