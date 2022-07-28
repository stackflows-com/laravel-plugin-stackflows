<?php

namespace Stackflows\BusinessProcesses\ServiceTasks;

use Stackflows\Clients\Stackflows\Model\ServiceTaskType;

interface BuildableFromServiceTaskContract
{
    public function buildFromServiceTask(ServiceTaskType $serviceTask): void;
}
