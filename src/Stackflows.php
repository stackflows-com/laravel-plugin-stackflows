<?php

namespace Stackflows;

use Illuminate\Support\Collection;
use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentTaggedBusinessModelsStartRequest;
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
        return new Collection($this->getEnvironmentApi()->postEnvironmentTaggedBusinessModelsStart(
            new PostEnvironmentTaggedBusinessModelsStartRequest([
                'tags' => $tags,
                'submission' => $submission,
            ])
        ));
    }

    /**
     * @return Collection
     * @throws Clients\Stackflows\ApiException
     */
    public function getUserTasks(): Collection
    {
        $tasks = new Collection();
        foreach ($this->getEnvironmentApi()->getEnvironmentUserTasksList() as $task) {
            $tasks->add($task);
        }

        return $tasks;
    }
}
