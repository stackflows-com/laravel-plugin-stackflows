<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\GatewayApi\Configuration as ApiConfiguration;

class Configuration
{
    /** @var string Address of the StackFlows Gateway API */
    private string $host;

    /** @var string Address of the StackFlows Backoffice */
    private string $backofficeHost;

    /** @var string Stackflows engine uuid. */
    private string $engine;

    private ApiConfiguration $apiConf;

    public function __construct(string $host, string $engine, string $backoffice)
    {
        $this->host = $host;
        $this->engine = $engine;
        $this->backofficeHost = $backoffice;

        $conf = new ApiConfiguration();
        $conf->setHost($host);
        $this->apiConf = $conf;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function getApiConfiguration(): ApiConfiguration
    {
        return $this->apiConf;
    }

    public function setToken(string $token)
    {
        $this->apiConf->setAccessToken($token);
    }

    public function getToken()
    {
        return $this->apiConf->getAccessToken();
    }

    public function getBackofficeHost(): string
    {
        return $this->backofficeHost;
    }
}
