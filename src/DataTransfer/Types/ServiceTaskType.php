<?php

namespace Stackflows\DataTransfer\Types;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\DataTransferObject\DataTransferObject;
use Stackflows\DataTransfer\Collections\DataPointCollection;

class ServiceTaskType extends DataTransferObject implements Arrayable
{
    /**
     * @var string
     */
    public string $reference;

    /**
     * @var string
     */
    public string $topic;

    /**
     * @var bool
     */
    public bool $suspended;

    /**
     * @var int
     */
    public int $priority;

    /**
     * @var ActivityType
     */
    public ActivityType $activity;

    /**
     * @var BusinessProcessInstanceType
     */
    public BusinessProcessInstanceType $instance;

    /**
     * @var DataPointCollection
     */
    public DataPointCollection $attributes;

    public function __construct(...$args)
    {
        $this->attributes = new DataPointCollection();

        parent::__construct(...$args);
    }
}
