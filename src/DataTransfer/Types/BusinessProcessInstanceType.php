<?php

namespace Stackflows\DataTransfer\Types;

use App\Models\BusinessProcessModelPublication;
use Spatie\DataTransferObject\DataTransferObject;

/**
 * ArrayAccess is needed for Nova in order to make properties retrieval work as expected
 */
class BusinessProcessInstanceType extends DataTransferObject
{
    /**
     * @var string
     */
    public string $reference;

    /**
     * @var BusinessProcessModelPublication|null
     */
    public ?BusinessProcessModelPublication $publication;

    /**
     * @var string|null
     */
    public ?string $context;
}
