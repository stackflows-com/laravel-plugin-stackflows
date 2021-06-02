<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\GatewayApi\Configuration as ApiConfiguration;

class Configuration
{
    /** @var string Address of the Stack Flow Gateway API */
    private string $host;

    /** @var string Stackflows engine uuid. */
    private string $engine;

    private ApiConfiguration $apiConf;

    public function __construct(string $host, string $engine)
    {
        $this->host = $host;
        $this->engine = $engine;

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
}
