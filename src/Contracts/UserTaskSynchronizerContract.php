<?php

namespace Stackflows\Contracts;

use Illuminate\Support\Collection;
use Stackflows\Clients\Stackflows\Model\UserTaskType;

interface UserTaskSynchronizerContract
{
    /**
     * @return string
     */
    public static function getActivityName(): string;

    /**
     * @param Collection|UserTaskType[] $userTasks
     * @return void
     */
    public function sync(Collection $userTasks): void;
}
