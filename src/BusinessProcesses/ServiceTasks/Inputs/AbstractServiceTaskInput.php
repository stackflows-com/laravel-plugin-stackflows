<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Inputs;

use Stackflows\Clients\Stackflows\Model\ServiceTaskTypeResource;

abstract class AbstractServiceTaskInput implements ServiceTaskInputInterface
{
    private ServiceTaskTypeResource $serviceTask;

    public function __construct(ServiceTaskTypeResource $serviceTask)
    {
        $this->serviceTask = $serviceTask;
    }

    public function getServiceTask(): ServiceTaskTypeResource
    {
        return $this->serviceTask;
    }
}
