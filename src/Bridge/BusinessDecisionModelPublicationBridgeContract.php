<?php

namespace Stackflows\Bridge;

use App\Models\BusinessDecisionModelPublication;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\BusinessDecisionScoreType;

interface BusinessDecisionModelPublicationBridgeContract extends BusinessModelPublicationBridgeContract
{
    public function evaluate(
        BusinessDecisionModelPublication $publication,
        DataPointCollection $submission = null
    ): BusinessDecisionScoreType;
}
