<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;

class Stackflows
{
    private GatewayClient $gatewayClient;

    public function __construct(GatewayClient $gatewayClient)
    {
        $this->gatewayClient = $gatewayClient;
    }

    public function getGatewayClient(): GatewayClient
    {
        return $this->gatewayClient;
    }
}
