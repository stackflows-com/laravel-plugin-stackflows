<?php

namespace Stackflows\DataTransfer\Types;

use Stackflows\DataTransfer\Collections\DataPointCollection;
use Spatie\DataTransferObject\DataTransferObject;

class UserTaskType extends DataTransferObject
{
    public ?string $processInstanceReference;
    public ?string $reference;
    public ?string $subject;
    public ?string $description;
    public ?\DateTime $followUpAt;
    public ?\DateTime $dueAt;

    /**
     * @var DataPointCollection|null
     */
    public ?DataPointCollection $attributes;

    /**
     * @var DataPointCollection|null
     */
    public ?DataPointCollection $fields;
}
