<?php

namespace Stackflows\Types;

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

    public function addItem(string $name, string $type, $value): self
    {
        if (!in_array($type, static::getTypeOptions())) {
            throw new SubmissionItemUnexpectedTypeException($type);
        }

        $this->items[$name] = [
            'type' => $type,
            'value' => $value,
        ];

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->items;
    }
}
