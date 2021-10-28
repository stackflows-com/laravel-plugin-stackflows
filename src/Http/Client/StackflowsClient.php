<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use Carbon\Carbon;
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

    public function getTasks(array $parameters = [])
    {
        $response = $this->client->get('task', [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'query' => $parameters,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function completeTask($taskId, $variables = [])
    {
        $response = $this->client->post("task/{$taskId}/complete", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'variables' => $variables,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function escalateTask(string $taskId, string $escalationCode, $variables = [])
    {
        $response = $this->client->post("task/{$taskId}/escalate", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'escalationCode' => $escalationCode,
                'variables' => $variables,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function completeExternalTask($taskId, $workerId, $tenantId, ExternalTaskOutputInterface $task)
    {
        $variables = [];

        foreach ($task->getVariables() as $name => $variable) {
            if ($variable instanceof \JsonSerializable) {
                $variable = $variable->jsonSerialize();
            }
            if ($variable instanceof Carbon) {
                $variable = $variable->toIso8601String();
            }
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

    public function getFormVariables(string $definitionId)
    {
        $response = $this->client->get("process-definition/{$definitionId}/form-variables", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function startForm(string $definitionId, array $variables)
    {
        $response = $this->client->post("process-definition/{$definitionId}/start", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'variables' => $variables,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
