<?php

namespace Stackflows\Bridge;

use Stackflows\DataTransfer\Types\BusinessDecisionScoreType;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use App\Models\BusinessDecisionModelPublication;

interface BusinessDecisionModelPublicationBridgeContract extends BusinessModelPublicationBridgeContract
{
    public function evaluate(
        BusinessDecisionModelPublication $publication,
        DataPointCollection $submission = null
    ): BusinessDecisionScoreType;
}
