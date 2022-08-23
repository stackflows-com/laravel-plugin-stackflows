<?php

namespace Stackflows\Bridge;

use Stackflows\DataTransfer\Collections\DataPointCollection;
use App\Models\BusinessProcessModelPublication;
use Illuminate\Support\Collection;

interface BusinessProcessModelPublicationBridgeContract extends BusinessModelPublicationBridgeContract
{
    public function start(BusinessProcessModelPublication $publication, DataPointCollection $submission = null);
    public function getActivityStatistics(BusinessProcessModelPublication $publication): Collection;
}
