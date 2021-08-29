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

    public function getExternalTasks(string $tenantId, array $topics)
    {
        $tasks = $this->client->get('tasks', [
            'json' => [

            ]
        ]);
        return [];
    }
}
