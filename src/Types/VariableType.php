<?php

namespace Stackflows\Types;

use Illuminate\Contracts\Support\Arrayable;

class VariableType implements Arrayable, \JsonSerializable
{
    private string $id;

    private string $name;

    private string $type;

    private string $values;

    private array $options;

    private array $types;

    /**
     * @param array $variable
     */
    public function __construct(array $variable)
    {
        $this->id = $variable['id'];
        $this->name = $variable['name'];
        $this->type = $variable['type'];
        $this->values = $variable['values'] ?? '';
        $this->options = $variable['options'] ?? [];

        $this->types = config('stackflows.variable_types');
    }

    /**
     * @return mixed|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed|string
     */
    public function getType()
    {
        if (isset($this->types[$this->type])) {
            return $this->types[$this->type]['name'];
        }

        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return mixed|array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function toArray():array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'values' => $this->values,
            'options' => $this->options,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
