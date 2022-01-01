<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\BusinessProcesses\Types\ServiceTaskType;

abstract class AbstractServiceTaskInput implements ServiceTaskInputInterface
{
    private ServiceTaskType $serviceTaskType;

    public function __construct(ServiceTaskType $serviceTaskType)
    {
        $this->serviceTaskType = $serviceTaskType;
    }

    public function getServiceTaskType(): ServiceTaskType
    {
        return $this->serviceTaskType;
    }
}
