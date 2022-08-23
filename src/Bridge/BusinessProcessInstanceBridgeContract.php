<?php

namespace Stackflows\Bridge;

use Illuminate\Support\Collection;
use Stackflows\DataTransfer\Types\BusinessProcessInstanceType;

interface BusinessProcessInstanceBridgeContract
{
    /**
     * @param string $reference
     * @return BusinessProcessInstanceType
     */
    public function get(string $reference): BusinessProcessInstanceType;

    /**
     * @return Collection
     */
    public function logs(): Collection;
}
