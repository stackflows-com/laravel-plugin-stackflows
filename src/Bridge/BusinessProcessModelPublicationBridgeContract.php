<?php

namespace Stackflows\Bridge;

use App\Models\BusinessProcessModelPublication;
use Illuminate\Support\Collection;
use Stackflows\DataTransfer\Collections\DataPointCollection;

interface BusinessProcessModelPublicationBridgeContract extends BusinessModelPublicationBridgeContract
{
    public function start(BusinessProcessModelPublication $publication, DataPointCollection $submission = null);

    public function getActivityStatistics(BusinessProcessModelPublication $publication): Collection;
}
