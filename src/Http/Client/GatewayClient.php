<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use GuzzleHttp\Client;
use Stackflows\StackflowsPlugin\Bpmn\Outputs\ExternalTaskOutputInterface;
use Stackflows\StackflowsPlugin\Bpmn\Outputs\Variable;

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
        $response = $this->client->get('external-task', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function fetchAndLock(string $tenantId, string $topic, $duration, $workerId)
    {
        $response = $this->client->post('external-task/fetchAndLock', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
                'lockDuration' => $duration,
                'workerId' => $workerId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function complete($taskId, $workerId, ExternalTaskOutputInterface $task)
    {
        $variables = [];

        foreach ($task->getVariables() as $name => $variable) {
            $variables[$name] = array_filter(
                [
                    'value' => $variable,
                ]
            );
        }

        $response = $this->client->post('external-task/complete', [
            'json' => [
                'taskId' => $taskId,
                'workerId' => $workerId,
                'variables' => $variables,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function unlock(string $taskId)
    {
        $response = $this->client->post('external-task/unlock/'.$taskId);

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
