<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use GuzzleHttp\Client;

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
            'timeout'  => 2.0,
        ]);
    }

    public function getExternalTasks(string $tenantId, string $topic)
    {
        $response = $this->client->get('external-task/tasks', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function fetchAndLock(string $tenantId, string $topic, $duration)
    {
        $response = $this->client->post('external-task/fetchAndLock', [
            'json' => [
                'topic'         => $topic,
                'tenantId'      => $tenantId,
                'lockDuration'  => $duration,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function complete(string $tenantId, string $topic, $duration)
    {
        $response = $this->client->post('external-task/fetchAndLock', [
            'json' => [
                'topic'         => $topic,
                'tenantId'      => $tenantId,
                'lockDuration'  => $duration,
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function unlock(string $taskId)
    {
        $response = $this->client->post('external-task/unlock', ['query' => $taskId]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
