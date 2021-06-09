<?php

namespace Stackflows\StackflowsPlugin\Channels;

use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\StackflowsPlugin\Configuration;

class UserTaskChannel
{
    private UserTaskApi $api;
    private Configuration $conf;

    public function __construct(UserTaskApi $api, Configuration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * Throw a Signal.
     *
     * @return \Stackflows\GatewayApi\Model\UserTask[]
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function getList(): array
    {
        return $this->api->getList($this->conf->getEngine());
    }
}
