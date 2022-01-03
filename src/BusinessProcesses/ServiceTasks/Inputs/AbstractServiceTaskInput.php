<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\BusinessProcesses\Types\ServiceTaskType;

abstract class AbstractServiceTaskInput implements ServiceTaskInputInterface
{
    private ServiceTaskType $serviceTask;

    public function __construct(ServiceTaskType $serviceTask)
    {
        $this->serviceTask = $serviceTask;
    }

    public function getServiceTask(): ServiceTaskType
    {
        return $this->serviceTask;
    }
}