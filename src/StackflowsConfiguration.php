<?php

namespace StackflowsPlugin;

class StackflowsConfiguration
{
    /** @var string Address of the StackFlows Gateway API */
    private string $gatewayHost;

    private string $authToken;

    private bool $debug;

    public function __construct(string $gatewayHost, string $authToken, bool $debug)
    {
        $this->gatewayHost = $gatewayHost;
        $this->authToken = $authToken;
        $this->debug = $debug;
    }

    public function getGatewayHost(): string
    {
        return $this->gatewayHost;
    }

    public function setGatewayHost(string $gatewayHost): self
    {
        $this->gatewayHost = $gatewayHost;
        return $this;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
}
