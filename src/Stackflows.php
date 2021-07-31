<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\GatewayApi\Api\ServiceTaskApi;
use Stackflows\GatewayApi\Api\SignalApi;
use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Channels\SignalChannel;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;
use Stackflows\StackflowsPlugin\Http\ClientFactory;

class Stackflows
{
    private Configuration $conf;
    private ClientFactory $clientFactory;

    public function __construct(Configuration $conf, ClientFactory $clientFactory)
    {
        $this->conf = $conf;
        $this->clientFactory = $clientFactory;
    }

    public function getSignalChannel(): SignalChannel
    {
        return new SignalChannel(
            new SignalApi($this->clientFactory->create(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }

    public function getServiceTaskChannel(): ServiceTaskChannel
    {
        return new ServiceTaskChannel(
            new ServiceTaskApi($this->clientFactory->create(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }

    public function getUserTaskChannel(): UserTaskChannel
    {
        return new UserTaskChannel(
            new UserTaskApi($this->clientFactory->create(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }
}
