<?php

namespace Stackflows;

use Illuminate\Support\Collection;
use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksLockRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksServeRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksUnlockRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentTaggedBusinessModelsStartRequest;
use Stackflows\Clients\Stackflows\Model\ServiceTaskTypeResource;
use Stackflows\Clients\Stackflows\Model\UserTaskTypeResource;
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
                    'tags'       => $tags,
                    'submission' => $submission,
                ])
            )
        );
    }

    /**
     * @return Collection|UserTaskTypeResource[]
     * @throws Clients\Stackflows\ApiException
     */
    public function getUserTasks(): Collection
    {
        $tasks = new Collection();
        foreach ($this->environmentApi->getEnvironmentUserTasksList() as $task) {
            $tasks->add($task);
        }

        return $tasks;
    }

    public function completeUserTask(string $reference): UserTaskTypeResource
    {

    }

    public function escalateUserTask(string $reference, SubmissionType $submission = null): UserTaskTypeResource
    {

    }

    /**
     * @param string $lock
     * @param string $topic
     * @param int $duration
     * @param int $limit
     * @return Collection|ServiceTaskTypeResource[]
     * @throws Clients\Stackflows\ApiException
     */
    public function lockServiceTasks(string $lock, string $topic, int $duration = 300, int $limit = 100): Collection
    {
        $data = $this->environmentApi->postEnvironmentServiceTasksLock(
            new PostEnvironmentServiceTasksLockRequest([
                'lock'     => $lock,
                'topic'    => $topic,
                'duration' => $duration,
                'limit'    => $limit,
            ])
        );

        return new Collection($data);
    }

    /**
     * @param string $reference
     * @param string $lock
     * @param SubmissionType|null $submission
     * @return ServiceTaskTypeResource
     * @throws Clients\Stackflows\ApiException
     */
    public function serveServiceTask(
        string $reference,
        string $lock,
        SubmissionType $submission = null
    ): ServiceTaskTypeResource {
        return $this->environmentApi->postEnvironmentServiceTasksServe(
            $reference,
            new PostEnvironmentServiceTasksServeRequest([
                'lock'       => $lock,
                'submission' => $submission,
            ])
        );
    }

    /**
     * @param string $reference
     * @param string $lock
     * @return ServiceTaskTypeResource
     * @throws Clients\Stackflows\ApiException
     */
    public function unlockServiceTask(string $reference, string $lock): ServiceTaskTypeResource
    {
        return $this->environmentApi->postEnvironmentServiceTasksUnlock(
            $reference,
            new PostEnvironmentServiceTasksUnlockRequest([
                'lock' => $lock,
            ])
        );
    }
}
