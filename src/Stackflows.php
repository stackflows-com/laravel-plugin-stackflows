<?php

namespace Stackflows\StackflowsPlugin;

class Stackflows
{
    private StackflowsConfiguration $conf;
    private ClientFactory $clientFactory;
    private BackofficeAuth $auth;

    public function __construct(StackflowsConfiguration $conf, ClientFactory $clientFactory, BackofficeAuth $auth)
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
            new UserTaskApi($this->clientFactory->create(30), $this->conf->getApiConfiguration()),
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

    public function getConfiguration(): StackflowsConfiguration
    {
        return $this->conf;
    }
}
