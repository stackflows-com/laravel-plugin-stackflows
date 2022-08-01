<?php

namespace Stackflows\Contracts;

use Stackflows\Clients\Stackflows\Model\ServiceTaskType;

interface BuildableFromServiceTaskContract
{
    public function buildFromServiceTask(ServiceTaskType $serviceTask): void;
}
