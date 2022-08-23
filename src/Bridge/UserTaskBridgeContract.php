<?php

namespace Stackflows\Bridge;

use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\DataAttributeType;
use Stackflows\DataTransfer\Types\DataPointType;
use Stackflows\DataTransfer\Types\UserTaskType;
use Illuminate\Support\Collection;

interface UserTaskBridgeContract
{
    public function getCount(array $criteria = []): int;

    /**
     * @return Collection|UserTaskType[]
     */
    public function getAll(array $criteria = []): Collection;

    public function get(string $id): UserTaskType;

    /**
     * @param string $taskId
     * @return Collection|DataPointType[]
     */
    public function getAttributes(string $id): Collection;

    /**
     * @param string $taskId
     * @return Collection|DataAttributeType[]
     */
    public function getFields(string $id): Collection;

    public function errorize(
        string $id,
        string $code,
        string $message,
        DataPointCollection $submission = null
    ): UserTaskType;

    public function submit(string $id, DataPointCollection $submission = null): UserTaskType;

    public function complete(string $id, DataPointCollection $submission = null): UserTaskType;

    public function escalate(string $id, string $code, DataPointCollection $submission = null): UserTaskType;
}
