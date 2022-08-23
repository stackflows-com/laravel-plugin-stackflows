<?php

namespace Stackflows\DataTransfer\Types;

use App\Models\BusinessDecisionModelPublication;
use Spatie\DataTransferObject\DataTransferObject;
use Stackflows\DataTransfer\Collections\DataPointCollection;

/**
 * ArrayAccess is needed for Nova in order to make properties retrieval work as expected
 */
class BusinessDecisionScoreType extends DataTransferObject
{
    /**
     * @var BusinessDecisionModelPublication
     */
    public BusinessDecisionModelPublication $publication;

    /**
     * @var DataPointCollection
     */
    public DataPointCollection $score;
}
