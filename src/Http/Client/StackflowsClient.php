<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use GuzzleHttp\Client;
use Stackflows\StackflowsPlugin\Bpmn\Outputs\ExternalTaskOutputInterface;

class StackflowsClient
{
    private string $authToken;

    private string $apiEndpoint;

    private Client $client;

    public function __construct($authToken, $gatewayEndpoint)
    {
        $this->authToken = $authToken;
        $this->apiEndpoint = $gatewayEndpoint;

        $this->client = new Client([
            'base_uri' => $this->apiEndpoint,
            'timeout' => 5.0,
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
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
                'lockDuration' => $duration,
                'workerId' => $workerId,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function complete($taskId, $workerId, $tenantId, ExternalTaskOutputInterface $task)
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
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'taskId' => $taskId,
                'workerId' => $workerId,
                'tenantId' => $tenantId,
                'variables' => $variables,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function unlock(string $taskId)
    {
        $response = $this->client->post(
            'external-task/unlock/'.$taskId,
            [
                'headers' => [
                    'Authorization' => $this->authToken,
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function authenticateToken(string $token)
    {
        $response = $this->client->post('environment/auth', [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'token' => $token,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
