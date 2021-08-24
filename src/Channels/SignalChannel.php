<?php

namespace Stackflows\StackflowsPlugin\Channels;

use Stackflows\GatewayApi\Api\SignalApi;
use Stackflows\GatewayApi\Model\ThrowSignalRequest;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\StackflowsConfiguration;

class SignalChannel
{
    private SignalApi $api;
    private StackflowsConfiguration $conf;

    public function __construct(SignalApi $api, StackflowsConfiguration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * Throw a Signal.
     *
     * @param string $name
     * @param Variable[] $vars
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function throw(string $name, array $vars): void
    {
        $request = new ThrowSignalRequest(['engine' => $this->conf->getEngine()]);
        $request->setName($name);
        $request->setVariables($vars);

        $this->api->throwSignal($request);
    }
}
