<?php

namespace Stackflows\StackflowsPlugin\Bpmn\Responses;

class Variable
{
    private string $name;

    /** @var mixed */
    private $value;

    private string $type;
    private string $objectTypeName;
    private string $serializationDataFormat;
    private string $filename;
    private string $mimetype;
    private string $encoding;
    private string $transient;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Variable
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Variable
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Variable
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectTypeName(): string
    {
        return $this->objectTypeName;
    }

    /**
     * @param string $objectTypeName
     * @return Variable
     */
    public function setObjectTypeName(string $objectTypeName): self
    {
        $this->objectTypeName = $objectTypeName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSerializationDataFormat(): string
    {
        return $this->serializationDataFormat;
    }

    /**
     * @param string $serializationDataFormat
     * @return Variable
     */
    public function setSerializationDataFormat(string $serializationDataFormat): self
    {
        $this->serializationDataFormat = $serializationDataFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     * @return Variable
     */
    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     * @return Variable
     */
    public function setMimetype(string $mimetype): self
    {
        $this->mimetype = $mimetype;
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     * @return Variable
     */
    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * @return string
     */
    public function getTransient(): string
    {
        return $this->transient;
    }

    /**
     * @param string $transient
     * @return Variable
     */
    public function setTransient(string $transient): self
    {
        $this->transient = $transient;
        return $this;
    }

}
