<?php

namespace Stackflows\StackflowsPlugin\Bpmn\Outputs;

class Variable
{
    private string $name;

    /** @var mixed */
    private $value = null;

    // Reserved for the future
//    private ?string $type = null;
//    private ?string $objectTypeName = null;
//    private ?string $serializationDataFormat = null;
//    private ?string $filename = null;
//    private ?string $mimetype = null;
//    private ?string $encoding = null;
//    private ?string $transient = null;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }
}
