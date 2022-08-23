<?php

namespace Stackflows\DataTransfer\Types;

use Stackflows\Exceptions\UnexpectedPropertyValueException;
use Spatie\DataTransferObject\DataTransferObject;

class DataAttributeType extends DataTransferObject
{
    public const TYPE_COLLECTION = 'collection';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';
    public const TYPE_INTEGER = 'integer';
    public const TYPE_FLOAT = 'float';
    public const TYPE_DOUBLE = 'double';
    public const TYPE_BOOLEAN = 'boolean';
    public const TYPE_DATE_TIME = 'date_time';

    public const KEY_REFERENCE = 'reference';
    public const KEY_ENGINE_REFERENCE = 'engine_reference';
    public const KEY_TYPE = 'type';
    public const KEY_LABEL = 'label';

    public string $reference;
    public ?string $engineReference;
    public string $type;
    public ?string $label = null;

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

    /**
     * @param string $reference
     * @param string $engineReference
     * @param string $type
     * @param string|null $label
     * @throws UnexpectedPropertyValueException
     */
    public function __construct(array $dataAttribute)
    {
        $this->reference = $dataAttribute[static::KEY_REFERENCE];
        $this->engineReference = $dataAttribute[static::KEY_ENGINE_REFERENCE] ?? null;
        $this->type = $dataAttribute[static::KEY_TYPE];
        $this->label = $dataAttribute[static::KEY_LABEL] ?? null;

        if (!in_array($this->type, static::getTypeOptions())) {
            throw new UnexpectedPropertyValueException(sprintf(
                'Type \'%s\' is not an option. Supported options are %s.',
                $this->type,
                implode(', ', static::getTypeOptions())
            ));
        }
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return string|null
     */
    public function getEngineReference(): ?string
    {
        return $this->engineReference;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?? $this->getEngineReference();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'reference' => $this->getReference(),
            'engine_reference' => $this->getEngineReference(),
            'type' => $this->getType(),
            'label' => $this->getLabel(),
        ];
    }
}
