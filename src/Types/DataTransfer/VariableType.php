<?php

namespace Stackflows\Types\DataTransfer;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\DataTransferObject;

class VariableType extends DataTransferObject
{
    /**
     *
     */
    public string $name;

    /**
     * @var \Illuminate\Support\Collection|null
     */
    public ?Collection $valueCollection = null;

    /**
     * @var \Spatie\DataTransferObject\DataTransferObject|null
     */
    public ?DataTransferObject $valueObject = null;

    /**
     * @var string|null
     */
    public ?string $valueString = null;

    /**
     * @var int|null
     */
    public ?int $valueInt = null;

    /**
     * @var float|null
     */
    public ?float $valueFloat = null;

    /**
     * @var bool|null
     */
    public ?bool $valueBool = null;

    /**
     * @var \DateTime|null
     */
    public ?\DateTime $valueDateTime = null;
}
