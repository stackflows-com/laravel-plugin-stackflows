<?php

namespace Stackflows\BusinessProcesses\ServiceTasks;

use Stackflows\Clients\Stackflows\Model\ServiceTaskTypeResource;
use Stackflows\Types\SubmissionType;

interface ServiceTaskExecutorInterface
{
    /**
     * @return string
     */
    public static function getTopic(): string;

    /**
     * Get the duration of blocking service tasks in milliseconds.
     *
     * @return int
     */
    public static function getLockDuration(): int;

    /**
     * @param ServiceTaskTypeResource $serviceTaskType
     * @return SubmissionType
     */
    public function execute(ServiceTaskTypeResource $serviceTaskType): ?SubmissionType;
}
