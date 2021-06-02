<?php

namespace Stackflows\StackflowsPlugin;

use GuzzleHttp\Client;
use Stackflows\GatewayApi\Api\SignalApi;
use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\StackflowsPlugin\Channels\SignalChannel;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;

class Stackflows
{
    private Configuration $conf;

    public function __construct(Configuration $conf)
    {
        $this->conf = $conf;
    }

    public function getSignalChannel(): SignalChannel
    {
        return new SignalChannel(
            new SignalApi(new Client(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }

    public function getUserTaskChannel(): UserTaskChannel
    {
        return new UserTaskChannel(
            new UserTaskApi(new Client(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }
}
