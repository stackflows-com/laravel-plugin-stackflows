<?php

namespace Stackflows\Http\Client;

use Stackflows\BusinessProcesses\ServiceTasks\Outputs\ServiceTaskOutputInterface;
use Stackflows\Types\DataTransfer\ServiceTaskCollectionType;
use Stackflows\Types\DataTransfer\ServiceTaskType;
use Stackflows\Types\DataTransfer\UserTaskCollectionType;
use Stackflows\Types\DataTransfer\UserTaskType;

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

    public function getUserTasks(): UserTaskCollectionType
    {
        return new UserTaskCollectionType($this->fetchResponseData($this->client->get("user-tasks")));
    }

    public function completeUserTask(string $id)
    {
        return new UserTaskType($this->fetchResponseData($this->client->post("user-tasks/{$id}/complete")));
    }

    public function escalateUserTask(string $id): UserTaskType
    {
        return new UserTaskType($this->fetchResponseData($this->client->post("user-tasks/{$id}/escalate")));
    }

    public function errorizeUserTask(string $id): UserTaskType
    {
        return new UserTaskType($this->fetchResponseData($this->client->post("user-tasks/{$id}/errorize")));
    }

    public function lockServiceTasks(
        string $lock,
        string $topic,
        int $duration = 300,
        int $limit = 100
    ): ServiceTaskCollectionType {
        return new ServiceTaskCollectionType(
            $this->fetchResponseData(
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
            )
        );
    }

    public function serveServiceTask(
        string $lock,
        string $reference,
        ServiceTaskOutputInterface $output
    ): ServiceTaskType {
        return new ServiceTaskType(
            $this->fetchResponseData(
                $this->client->post(
                    "service-tasks/{$reference}/serve",
                    [
                        'json' => [
                            'lock'      => $lock,
                            'variables' => $output->getVariables(),
                        ],
                    ]
                )
            )
        );
    }

    public function unlockServiceTask(string $reference): ServiceTaskType
    {
        return new ServiceTaskType($this->fetchResponseData($this->client->post("service-tasks/{$reference}/unlock")));
    }
}
