<?php

namespace Stackflows\Types;

use Illuminate\Support\Collection;
use Stackflows\Exceptions\SubmissionItemUnexpectedTypeException;

class SubmissionType implements \JsonSerializable
{
    public const TYPE_COLLECTION = 'collection';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_FLOAT = 'float';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_DATE_TIME = 'date_time';

    protected ?array $items = null;

    /**
     * @return string[]
     */
    public static function getTypeOptions(): array
    {
        return [
            static::TYPE_COLLECTION,
            static::TYPE_OBJECT,
            static::TYPE_STRING,
            static::TYPE_INTEGER,
            static::TYPE_FLOAT,
            static::TYPE_DOUBLE,
            static::TYPE_BOOLEAN,
            static::TYPE_DATE_TIME,
        ];
    }

    public static function create(): self
    {
        return new static();
    }

    public function add(string $name, string $type, $value = null): self
    {
        if (! in_array($type, static::getTypeOptions())) {
            throw new SubmissionItemUnexpectedTypeException($type);
        }

        $this->items[$name] = [
            'type'  => $type,
            'value' => $value,
        ];

        return $this;
    }

    public function addCollection(string $name, Collection $value = null): self
    {
        return $this->add($name, static::TYPE_COLLECTION, $value);
    }

    public function addObject(string $name, $value = null): self
    {
        return $this->add($name, static::TYPE_OBJECT, $value);
    }

    public function addString(string $name, string $value = null): self
    {
        return $this->add($name, static::TYPE_STRING, $value);
    }

    public function addInteger(string $name, int $value = null): self
    {
        return $this->add($name, static::TYPE_INTEGER, $value);
    }

    public function addFloat(string $name, float $value = null): self
    {
        return $this->add($name, static::TYPE_FLOAT, $value);
    }

    public function addBool(string $name, bool $value = null): self
    {
        return $this->add($name, static::TYPE_BOOLEAN, $value);
    }

    public function addDateTime(string $name, \DateTime $value = null): self
    {
        return $this->add($name, static::TYPE_DATE_TIME, $value);
    }

    public function jsonSerialize()
    {
        return $this->items;
    }
}
