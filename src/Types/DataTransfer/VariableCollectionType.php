<?php

namespace Stackflows\Types\DataTransfer;

use Spatie\DataTransferObject\DataTransferObjectCollection;

class VariableCollectionType extends DataTransferObjectCollection
{
    public function current(): VariableType
    {
        return parent::current();
    }
}
