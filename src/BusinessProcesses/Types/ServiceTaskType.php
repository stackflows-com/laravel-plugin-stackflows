<?php

namespace Stackflows\BusinessProcesses\Types;

class ServiceTaskType
{
    private string $reference;

    private ?string $topic;

    private ?string $priority;

    /**
     * @param string $reference
     * @param string|null $topic
     * @param string|null $priority
     */
    public function __construct(string $reference, ?string $topic, ?string $priority)
    {
        $this->reference = $reference;
        $this->topic = $topic;
        $this->priority = $priority;
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
    public function getTopic(): ?string
    {
        return $this->topic;
    }

    /**
     * @return string|null
     */
    public function getPriority(): ?string
    {
        return $this->priority;
    }
}
