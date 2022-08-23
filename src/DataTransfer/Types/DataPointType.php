<?php

namespace Stackflows\DataTransfer\Types;

use Spatie\DataTransferObject\DataTransferObject;

class DataPointType extends DataTransferObject implements \JsonSerializable
{
    public DataAttributeType $attribute;
    public $value;

    public function __isset(string $name): bool
    {
        return in_array($name, ['attribute', 'value']);
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'attribute':
                return $this->attribute;
            case 'value':
                return $this->value;
            default:
                return null;
        }
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
