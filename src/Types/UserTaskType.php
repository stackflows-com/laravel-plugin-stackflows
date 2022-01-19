<?php

namespace Stackflows\Types;

use Illuminate\Contracts\Support\Arrayable;

class UserTaskType implements Arrayable, \JsonSerializable
{
    private string $reference;
    private string $subject;

    /**
     * @param array $userTask
     */
    public function __construct(array $userTask)
    {
        $this->reference = $userTask['reference'];
        $this->subject = $userTask['subject'];
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
