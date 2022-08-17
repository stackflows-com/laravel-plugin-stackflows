<?php

namespace Stackflows;

use Illuminate\Support\Collection;
use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\ApiException;
use Stackflows\Clients\Stackflows\Model\GetEnvironmentUserTasksListRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksLockRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksServeRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksUnlockRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentTaggedBusinessModelsStartRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentUserTasksCompleteRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentUserTasksErrorizeRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentUserTasksEscalateRequest;
use Stackflows\Clients\Stackflows\Model\ServiceTaskType;
use Stackflows\Clients\Stackflows\Model\UserTaskType;
use Stackflows\Collections\PaginatedCollection;
use Stackflows\Types\SubmissionType;

class Stackflows
{
    private EnvironmentApi $environmentApi;

    public function __construct(EnvironmentApi $environmentApi)
    {
        $this->environmentApi = $environmentApi;
    }

    public function getEnvironmentApi(): EnvironmentApi
    {
        return $this->environmentApi;
    }

    /**
     * Trigger tagged business processes
     *
     * @param array $tags
     * @param SubmissionType $submission
     * @return mixed
     * @throws Clients\Stackflows\ApiException
     */
    public function startBusinessProcesses(array $tags, SubmissionType $submission): Collection
    {
        return new Collection(
            $this->environmentApi->postEnvironmentTaggedBusinessModelsStart(
                new PostEnvironmentTaggedBusinessModelsStartRequest([
                    'tags' => $tags,
                    'submission' => $submission->jsonSerialize(),
                ])
            )->getData()
        );
    }

    /**
     * @return Collection|UserTaskType[]
     * @throws Clients\Stackflows\ApiException
     */
    public function getUserTasks(array $crtiteria = []): PaginatedCollection
    {
        $response = $this->environmentApi->getEnvironmentUserTasksList(
            new GetEnvironmentUserTasksListRequest($crtiteria)
        );

        $tasks = new PaginatedCollection();
        foreach ($response->getData() as $task) {
            $tasks->add($task);
        }

        $tasks->setTotal($response->getMeta()['total']);

        return $tasks;
    }

    /**
     * @param string $reference
     * @param SubmissionType|null $submission
     * @return UserTaskType
     * @throws ApiException
     */
    public function completeUserTask(string $reference, SubmissionType $submission = null): UserTaskType
    {
        return $this->environmentApi->postEnvironmentUserTasksComplete(
            $reference,
            new PostEnvironmentUserTasksCompleteRequest([
                'submission' => $submission ? $submission->jsonSerialize() : null,
            ])
        )->getData();
    }

    /**
     * @param string $reference
     * @param string $code
     * @param SubmissionType|null $submission
     * @return UserTaskType
     * @throws ApiException
     */
    public function escalateUserTask(string $reference, string $code, SubmissionType $submission = null): UserTaskType
    {
        return $this->environmentApi->postEnvironmentUserTasksEscalate(
            $reference,
            new PostEnvironmentUserTasksEscalateRequest([
                'code' => $code,
                'submission' => $submission ? $submission->jsonSerialize() : null,
            ])
        )->getData();
    }

    /**
     * @param string $reference
     * @param string $code
     * @param string|null $message
     * @param SubmissionType|null $submission
     * @return UserTaskType
     * @throws ApiException
     */
    public function errorizeUserTask(
        string $reference,
        string $code,
        string $message = null,
        SubmissionType $submission = null
    ): UserTaskType {
        return $this->environmentApi->postEnvironmentUserTasksErrorize(
            $reference,
            new PostEnvironmentUserTasksErrorizeRequest([
                'code' => $code,
                'message' => $message,
                'submission' => $submission ? $submission->jsonSerialize() : null,
            ])
        )->getData();
    }

    /**
     * @param string $lock
     * @param string $topic
     * @param int $duration
     * @param int $limit
     * @return Collection|ServiceTaskType[]
     * @throws Clients\Stackflows\ApiException
     */
    public function lockServiceTasks(string $lock, string $topic, int $duration = 300, int $limit = 100): Collection
    {
        $response = $this->environmentApi->postEnvironmentServiceTasksLock(
            new PostEnvironmentServiceTasksLockRequest([
                'lock' => $lock,
                'topic' => $topic,
                'duration' => $duration,
                'limit' => $limit,
            ])
        );

        return new Collection($response->getData());
    }

    /**
     * @param string $reference
     * @param string $lock
     * @param SubmissionType|null $submission
     * @return ServiceTaskType
     * @throws Clients\Stackflows\ApiException
     */
    public function serveServiceTask(
        string $reference,
        string $lock,
        SubmissionType $submission = null
    ): ServiceTaskType {
        return $this->environmentApi->postEnvironmentServiceTasksServe(
            $reference,
            new PostEnvironmentServiceTasksServeRequest([
                'lock' => $lock,
                'submission' => $submission ? $submission->jsonSerialize() : null,
            ])
        )->getData();
    }

    /**
     * @param string $reference
     * @param string $lock
     * @return ServiceTaskType
     * @throws Clients\Stackflows\ApiException
     */
    public function unlockServiceTask(string $reference, string $lock): ServiceTaskType
    {
        return $this->environmentApi->postEnvironmentServiceTasksUnlock(
            $reference,
            new PostEnvironmentServiceTasksUnlockRequest([
                'lock' => $lock,
            ])
        )->getData();
    }
}
