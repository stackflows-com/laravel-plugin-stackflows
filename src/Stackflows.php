<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\GatewayApi\Api\ProcessApi;
use Stackflows\GatewayApi\Api\ServiceTaskApi;
use Stackflows\GatewayApi\Api\SignalApi;
use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Channels\ProcessChannel;
use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Channels\SignalChannel;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;
use Stackflows\StackflowsPlugin\Http\Client\ClientFactory;

class Stackflows
{
    private Configuration $conf;
    private ClientFactory $clientFactory;
    private BackofficeAuth $auth;

    public function __construct(Configuration $conf, ClientFactory $clientFactory, BackofficeAuth $auth)
    {
        $this->conf = $conf;
        $this->clientFactory = $clientFactory;
        $this->auth = $auth;
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

    public function getProcessChannel(): ProcessChannel
    {
        return new ProcessChannel(
            new ProcessApi($this->clientFactory->create(), $this->conf->getApiConfiguration()),
            $this->conf,
        );
    }

    public function getAuth(): BackofficeAuth
    {
        return $this->auth;
    }
}
