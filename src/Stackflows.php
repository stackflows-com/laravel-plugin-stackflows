<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;
use StackflowsPlugin\StackflowsConfiguration;

class Stackflows
{
    private StackflowsConfiguration $configuration;
    private GatewayClient $gatewayClient;

    public function __construct(StackflowsConfiguration $configuration, GatewayClient $gatewayClient)
    {
        $this->configuration = $configuration;
        $this->gatewayClient = $gatewayClient;
    }

    public function getConfiguration(): StackflowsConfiguration
    {
        return $this->configuration;
    }

    public function getGatewayClient(): GatewayClient
    {
        return $this->gatewayClient;
    }
}
