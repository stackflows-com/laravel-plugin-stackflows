<?php

namespace Stackflows\Types\DataTransfer;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class UserTaskCollectionType extends DataTransferObjectCollection
{
    public function current(): UserTaskType
    {
        return parent::current();
    }
}
