<?php

namespace Stackflows\Types\DataTransfer;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class ServiceTaskCollectionType extends DataTransferObjectCollection
{
    public function current(): ServiceTaskType
    {
        return parent::current();
    }
}
