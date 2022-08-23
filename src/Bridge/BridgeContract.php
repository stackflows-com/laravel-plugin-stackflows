<?php

namespace Stackflows\Bridge;

use Stackflows\Types\EnvironmentType;

interface BridgeContract
{
    public function getEnvironment(): EnvironmentType;
}
