<?php

namespace Stackflows\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Stackflows\Clients\Stackflows\Model\UserTaskType;

interface UserTaskSynchronizerContract
{
    /**
     * @return string
     */
    public static function getActivityName(): string;

    public static function getCachePrefix(): string;

    public static function getReferenceAttributeName(): string;

    public static function getActivityAttributeName(): string;

    public static function getCreatedAtAttributeName(): string;

    /**
     * @param \DateTime $referenceTime
     * @return void
     */
    public function setReferenceTimestamp(\DateTime $referenceTime): void;

    /**
     * Must be keyed by external task reference
     *
     * @return Builder
     */
    public function getReflectionsQuery(): Builder;

    /**
     * @param Collection|StackflowsTaskReflectionContract[] $reflections
     * @return void
     */
    public function setReflections(Collection $reflections): void;

    /**
     * @param UserTaskType $userTask
     * @return void
     */
    public function create(UserTaskType $userTask): void;

    /**
     * @param UserTaskType $task
     * @param StackflowsTaskReflectionContract $reflection
     * @return void
     */
    public function update(UserTaskType $task, StackflowsTaskReflectionContract $reflection): void;

    /**
     * @param Collection|StackflowsTaskReflectionContract[] $reflections
     * @return Collection|StackflowsTaskReflectionContract[]
     */
    public function remove(Collection $reflections): Collection;
}
