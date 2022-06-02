<?php

namespace Stackflows\Http\Client;

use Illuminate\Support\Collection;
use Stackflows\BusinessProcesses\ServiceTasks\Outputs\ServiceTaskOutputInterface;
use Stackflows\BusinessProcesses\Types\ServiceTaskType;
use Stackflows\Types\UserTaskType;

class StackflowsClient extends AbstractStackflowsClient
{
    public function startTaggedProcessModels(array $tags, array $variables = [])
    {
        return $this->fetchResponseData(
            $this->client->post(
                "tagged/process-models",
                [
                    'json' => [
                        'tags'      => $tags,
                        'variables' => $variables,
                    ],
                ]
            )
        );
    }

    public function getUserTasks(): Collection
    {
        $collection = collect($this->fetchResponseData($this->client->get("user-tasks")));

        return $collection->map(function ($data) {
            return new UserTaskType($data['reference'], $data['subject']);
        });
    }

    public function completeUserTask(string $id)
    {
        $data = $this->fetchResponseData($this->client->post("user-tasks/{$id}/complete"));

        return new UserTaskType($data['reference'], $data['subject']);
    }

    public function escalateUserTask(string $id)
    {
        $data = $this->fetchResponseData($this->client->post("user-tasks/{$id}/escalate"));

        return new UserTaskType($data['reference'], $data['subject']);
    }

    public function errorizeUserTask(string $id)
    {
        $data = $this->fetchResponseData($this->client->post("user-tasks/{$id}/errorize"));

        return new UserTaskType($data['reference'], $data['subject']);
    }

    public function lockServiceTasks(string $lock, string $topic, int $duration = 300, int $limit = 100): Collection
    {
        $collection = collect($this->fetchResponseData(
            $this->client->post(
                "service-tasks",
                [
                    'json' => [
                        'lock' => $lock,
                        'topic' => $topic,
                        'duration' => $duration,
                        'limit' => $limit,
                    ]
                ]
            )
        ));

        return $collection->map(function ($data) {
            return new ServiceTaskType($data['reference'], $data['topic'], null);
        });
    }

    public function serveServiceTask(
        string $lock,
        string $reference,
        ServiceTaskOutputInterface $output
    ): ServiceTaskType {
        $data = $this->fetchResponseData(
            $this->client->post(
                "service-tasks/{$reference}/serve",
                [
                    'json' => [
                        'lock'      => $lock,
                        'variables' => $output->getVariables(),
                    ],
                ]
            )
        );

        return new ServiceTaskType($data['reference'], $data['topic'], null);
    }

    public function unlockServiceTask(string $reference): ServiceTaskType
    {
        $data = $this->fetchResponseData($this->client->post("service-tasks/{$reference}/unlock"));

        return new ServiceTaskType($data['reference'], $data['topic'], null);
    }
}
