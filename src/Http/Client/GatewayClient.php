<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

class GatewayClient
{
    private $authToken;

    private $gatewayEndpoint;

    public function __construct($authToken, $gatewayEndpoint)
    {
        $this->authToken = $authToken;
        $this->gatewayEndpoint = $gatewayEndpoint;
    }
}
