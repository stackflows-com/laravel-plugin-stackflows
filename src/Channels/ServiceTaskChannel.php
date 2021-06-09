<?php

namespace Stackflows\StackflowsPlugin\Channels;

use Stackflows\GatewayApi\Api\ServiceTaskApi;
use Stackflows\GatewayApi\Model\CompleteServiceTaskRequest;
use Stackflows\GatewayApi\Model\GetPendingServiceTaskRequest;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\Configuration;
use Stackflows\StackflowsPlugin\Services\ServiceTaskExecutorInterface;

final class ServiceTaskChannel
{
    private ServiceTaskApi $api;
    private Configuration $conf;

    /** @var int Maximum number of tasks at a time. */
    private int $limit = 10;

    public function __construct(ServiceTaskApi $api, Configuration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * Get pending service tasks.
     *
     * @param ServiceTaskExecutorInterface $handler
     * @return ServiceTask[]
     *
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function getPending(ServiceTaskExecutorInterface $handler): array
    {
        $request = new GetPendingServiceTaskRequest(
            [
                'engine' => $this->conf->getEngine(),
                'limit' => $this->limit,
                'topics' => $handler->getReference(),
                'lockDuration' => $handler->getLockDuration(),
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
