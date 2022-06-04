<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\Types\DataTransfer\ServiceTaskType;

interface ServiceTaskInputInterface
{
    public function getServiceTask(): ServiceTaskType;
}
