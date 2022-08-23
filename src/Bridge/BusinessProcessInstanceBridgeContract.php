<?php

namespace Stackflows\Bridge;

use Stackflows\DataTransfer\Types\BusinessProcessInstanceType;
use Illuminate\Support\Collection;

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
