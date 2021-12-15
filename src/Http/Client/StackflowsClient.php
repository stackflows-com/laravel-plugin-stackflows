<?php

namespace Stackflows\StackflowsPlugin\Http\Client;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
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
        return $this->makeGetRequest('external-task', [
            'json' => [
                'topic' => $topic,
                'tenantId' => $tenantId,
            ],
        ]);
    }

    public function fetchAndLock(string $tenantId, string $topic, $duration, $workerId)
    {
        return $this->makePostRequest('external-task/fetchAndLock', [
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
    }

    public function getTasks(array $parameters = [])
    {
        return $this->makeGetRequest('task', [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'query' => $parameters,
        ]);
    }

    public function getTaskCount(array $parameters = [])
    {
        return $this->makeGetRequest('task/count', [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'query' => $parameters,
        ]);
    }

    public function completeTask($taskId, $variables = [])
    {
        return $this->makePostRequest("task/{$taskId}/complete", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'variables' => $variables,
            ],
        ]);
    }

    public function escalateTask(string $taskId, string $escalationCode, $variables = [])
    {
        return $this->makePostRequest("task/{$taskId}/escalate", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'escalationCode' => $escalationCode,
                'variables' => $variables,
            ],
        ]);
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

        return $this->makePostRequest('external-task/complete', [
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
    }

    public function unlock(string $taskId)
    {
        return $this->makePostRequest(
            'external-task/unlock/'.$taskId,
            [
                'headers' => [
                    'Authorization' => $this->authToken,
                ],
            ]
        );
    }

    public function authenticateToken(string $token)
    {
        return $this->makePostRequest('environment/auth', [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'token' => $token,
            ],
        ]);
    }

    public function getFormVariables(string $definitionId, string $envId, string $version)
    {
        return $this->makeGetRequest("process-definition/{$definitionId}/form-variables", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'env_id' => $envId,
                'version' => $version,
            ],
        ]);
    }

    public function startForm(string $definitionId, array $variables, string $envId, string $version)
    {
        return $this->makePostRequest("process-definition/{$definitionId}/start", [
            'headers' => [
                'Authorization' => $this->authToken,
            ],
            'json' => [
                'variables' => $variables,
                'env_id' => $envId,
                'version' => $version,
            ],
        ]);
    }

    private function makeGetRequest(string $uri, array $params)
    {
        try {
            $response = $this->client->get($uri, $params);
        } catch (RequestException $exception) {
            return $this->createErrorResponse($exception->getResponse());
        }

        return $this->parseResponse($response);
    }

    private function makePostRequest(string $uri, array $params)
    {
        try {
            $response = $this->client->post($uri, $params);
        } catch (ClientException $exception) {
            return $this->createErrorResponse($exception->getResponse());
        }

        return $this->parseResponse($response);
    }

    private function createErrorResponse($response): array
    {
        $response = $this->parseResponse($response);

        $message = $response['error'] ?? null;
        if (isset($response['errorCode'])) {
            $message = ErrorMap::map($response['errorCode'], $message);
        }

        return ['error' => $message];
    }

    private function parseResponse(ResponseInterface $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
