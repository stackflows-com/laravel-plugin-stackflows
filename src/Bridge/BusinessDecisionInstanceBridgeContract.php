<?php

namespace Stackflows\Bridge;

use Illuminate\Support\Collection;

interface BusinessDecisionInstanceBridgeContract
{
    /**
     * @return Collection
     */
    public function logs(): Collection;
}
