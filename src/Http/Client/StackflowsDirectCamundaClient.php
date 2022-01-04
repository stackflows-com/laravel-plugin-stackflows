<?php

namespace Stackflows\Http\Client;

use Carbon\Carbon;
use Stackflows\BusinessProcesses\ServiceTasks\Outputs\ServiceTaskOutputInterface;

/**
 * @deprecated Using direct engine access is discouraged and will be removed later
 */
class StackflowsDirectCamundaClient extends AbstractStackflowsClient
{
    protected function getBaseUriSuffix(): ?string
    {
        return 'direct/camunda/';
    }

    public function getExternalTasks(string $topic)
    {
        return $this->makeGetRequest('external-task', [
            'json' => [
                'topic' => $topic,
            ],
        ]);
    }

    public function fetchAndLock(string $topic, $duration, $workerId)
    {
        return $this->makePostRequest('external-task/fetch-and-lock', [
            'json' => [
                'topic' => $topic,
                'lockDuration' => $duration,
                'workerId' => $workerId,
            ],
        ]);
    }

    public function getTasks(array $parameters = [])
    {
        return $this->makeGetRequest('task', [
            'query' => $parameters,
        ]);
    }

    public function getTaskCount(array $parameters = [])
    {
        return $this->makeGetRequest('task/count', [
            'query' => $parameters,
        ]);
    }

    public function completeTask($taskId, $variables = [])
    {
        return $this->makePostRequest("task/{$taskId}/complete", [
            'json' => [
                'variables' => $variables,
            ],
        ]);
    }

    public function escalateTask(string $taskId, string $escalationCode, $variables = [])
    {
        return $this->makePostRequest("task/{$taskId}/escalate", [
            'json' => [
                'escalationCode' => $escalationCode,
                'variables' => $variables,
            ],
        ]);
    }

    public function completeExternalTask($taskId, $workerId, ServiceTaskOutputInterface $task)
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
            'json' => [
                'taskId' => $taskId,
                'workerId' => $workerId,
                'variables' => $variables,
            ],
        ]);
    }

    public function unlock(string $taskId)
    {
        return $this->makePostRequest('external-task/unlock/'.$taskId);
    }

    public function getFormVariables(string $definitionId, string $envId, string $version)
    {
        return $this->makeGetRequest("process-definition/{$definitionId}/form-variables", [
            'json' => [
                'env_id' => $envId,
                'version' => $version,
            ],
        ]);
    }

    public function startForm(string $definitionId, array $variables, string $envId, string $version)
    {
        return $this->makePostRequest("process-definition/{$definitionId}/start", [
            'json' => [
                'variables' => $variables,
                'env_id' => $envId,
                'version' => $version,
            ],
        ]);
    }

    public function getProcessesByTag(string $tag)
    {
        return $this->makeGetRequest("process-definition/get-by-tag/{$tag}");
    }
}
