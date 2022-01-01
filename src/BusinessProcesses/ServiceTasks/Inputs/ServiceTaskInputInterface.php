<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\BusinessProcesses\Types\ServiceTaskType;

interface ServiceTaskInputInterface
{
    public function getServiceTask(): ServiceTaskType;
}
