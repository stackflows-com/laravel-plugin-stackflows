<?php

namespace Stackflows\Types;

use Illuminate\Contracts\Support\Arrayable;

class UserTaskType implements Arrayable, \JsonSerializable
{
    private string $reference;
    private string $subject;

    /**
     * @param string $reference
     * @param string $subject
     */
    public function __construct(string $reference, string $subject)
    {
        $this->reference = $reference;
        $this->subject = $subject;
    }

    /**
     * @return mixed|string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return mixed|string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    public function toArray()
    {
        return [
            'reference' => $this->reference,
            'subject' => $this->subject,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
