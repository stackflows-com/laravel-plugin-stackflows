<?php

namespace Stackflows\Bridge;

use App\Models\BusinessBaseModelPublication;

interface BusinessModelPublicationBridgeContract
{
    /**
     * @param BusinessBaseModelPublication $publication
     * @return BusinessBaseModelPublication
     */
    public function delete(BusinessBaseModelPublication $publication): BusinessBaseModelPublication;
}
