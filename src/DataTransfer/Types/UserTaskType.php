<?php

namespace Stackflows\DataTransfer\Types;

use Spatie\DataTransferObject\DataTransferObject;
use Stackflows\DataTransfer\Collections\DataPointCollection;

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
