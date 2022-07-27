<?php

namespace Stackflows\BusinessProcesses\ServiceTasks;

use Stackflows\Clients\Stackflows\Model\ServiceTaskType;
use Stackflows\Exceptions\ExecutorException;
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
     * @param ServiceTaskType $serviceTask
     * @return SubmissionType|null
     * @throws ExecutorException
     */
    public function execute(ServiceTaskType $serviceTask): ?SubmissionType;
}
