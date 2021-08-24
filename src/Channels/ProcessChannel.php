<?php

namespace Stackflows\StackflowsPlugin\Channels;

use SplFileObject;
use Stackflows\GatewayApi\Api\ProcessApi;
use Stackflows\GatewayApi\Model\Process;
use Stackflows\GatewayApi\Model\StartProcessRequest;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\StackflowsConfiguration;

class ProcessChannel
{
    private ProcessApi $api;
    private StackflowsConfiguration $conf;

    public function __construct(ProcessApi $api, StackflowsConfiguration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * @param string $name
     * @param Variable[]|null $variables
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function start(string $name, array $variables = null)
    {
        $request = new StartProcessRequest(['name' => $name]);
        $request->setInstances([$this->conf->getEngine()]);
        $request->setVariables($variables);

        $this->api->startProcess($request);
    }

    /**
     * @param string $name
     * @param SplFileObject $notation
     * @return Process[]
     *
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function create(string $name, SplFileObject $notation): array
    {
        return $this->api->createProcess($name, [$this->conf->getEngine()], $notation);
    }
}
