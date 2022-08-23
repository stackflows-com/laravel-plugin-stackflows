<?php

namespace Stackflows\DataTransfer\Types;

use Spatie\DataTransferObject\DataTransferObject;
use Stackflows\DataTransfer\Collections\DataPointCollection;

class TaskContextType extends DataTransferObject
{
    public DataPointCollection $attributes;

    public function __construct(array $attributes = [])
    {
        $attributes = (new DataPointCollection($attributes))->map(fn (array $attribute) => new DataPointType([
            'attribute' => new DataAttributeType($attribute['attribute']),
            'value' => $attribute['value'],
        ]));
        $attributes->keyBy(fn (DataPointType $attribute) => $attribute->attribute->getReference());

        parent::__construct(['attributes' => $attributes]);
    }

    public function __isset(string $name): bool
    {
        return in_array($name, ['attributes']);
    }

    public function __get(string $name)
    {
        switch ($name) {
            case 'attributes':
                return $this->getAttributes();
            default:
                return null;
        }
    }

    /**
     * @return DataPointCollection
     */
    public function getAttributes(): DataPointCollection
    {
        return $this->attributes;
    }

    /**
     * @param DataPointCollection $attributes
     *
     * @return $this
     */
    public function setAttributes(DataPointCollection $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'attributes' => $this->attributes->toArray(),
        ];
    }
}
