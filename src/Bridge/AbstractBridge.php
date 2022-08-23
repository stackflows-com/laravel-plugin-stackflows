<?php

namespace Stackflows\Bridge;

use Stackflows\Types\EnvironmentType;

abstract class AbstractBridge implements BridgeContract
{
    protected EnvironmentType $environment;

    /**
     * @param EnvironmentType $environment
     */
    public function __construct(EnvironmentType $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return EnvironmentType
     */
    public function getEnvironment(): EnvironmentType
    {
        return $this->environment;
    }
}
