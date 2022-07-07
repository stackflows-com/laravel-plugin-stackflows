<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\Clients\Stackflows\Model\ServiceTaskTypeResource;

interface ServiceTaskInputInterface
{
    public function getServiceTask(): ServiceTaskTypeResource;
}
