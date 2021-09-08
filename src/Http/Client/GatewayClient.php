<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use GuzzleHttp\Client;
use Stackflows\StackflowsPlugin\Bpmn\Outputs\ExternalTaskOutputInterface;

class GatewayClient
{
    private string $authToken;

    private string $gatewayEndpoint;

    private Client $client;

    public function __construct($authToken, $gatewayEndpoint)
    {
        $this->authToken = $authToken;
        $this->gatewayEndpoint = $gatewayEndpoint;

        $this->client = new Client([
            'base_uri' => $this->gatewayEndpoint,
            'timeout' => 2.0,
        ]);
    }

    public function getExternalTasks(string $tenantId, string $topic)
    {
        $response = $this->client->get('external-task/tasks', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function fetchAndLock(string $tenantId, string $topic, $duration)
    {
        $response = $this->client->post('external-task/fetchAndLock', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
                'lockDuration' => $duration,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function complete(ExternalTaskOutputInterface $task)
    {
        $response = $this->client->post('external-task/fetchAndLock', [
            'json' => [

            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function unlock(string $taskId)
    {
        $response = $this->client->post('external-task/unlock', ['query' => $taskId]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function authenticateToken(string $token)
    {
        $response = $this->client->post('token/authenticate', [
            'json' => [
                'token' => $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
