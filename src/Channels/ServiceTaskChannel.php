<?php

namespace Stackflows\StackflowsPlugin\Channels;

use Stackflows\GatewayApi\Api\ServiceTaskApi;
use Stackflows\GatewayApi\Model\CompleteServiceTaskRequest;
use Stackflows\GatewayApi\Model\GetPendingServiceTaskRequest;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\StackflowsConfiguration;

class ServiceTaskChannel
{
    private ServiceTaskApi $api;
    private StackflowsConfiguration $conf;

    /** @var int Maximum number of tasks at a time. */
    private int $limit = 10;

    public function __construct(ServiceTaskApi $api, StackflowsConfiguration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * Get pending service tasks.
     *
     * @param array $topics
     * @param int $lockDuration
     * @return ServiceTask[]
     *
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function getPending(array $topics, int $lockDuration): array
    {
        $request = new GetPendingServiceTaskRequest(
            [
                'engine' => $this->conf->getEngine(),
                'limit' => $this->limit,
                'topics' => $topics,
                'lockDuration' => $lockDuration,
            ]
        );

        return $this->api->getPending($request);
    }

    /**
     * @param string $id Task id.
     * @param Variable[] $variables
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function complete(string $id, array $variables): void
    {
        $request = new CompleteServiceTaskRequest(['engine' => $this->conf->getEngine()]);
        $request->setVariables($variables);

        $this->api->complete($id, $request);
    }
}
