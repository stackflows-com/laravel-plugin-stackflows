<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Outputs;

interface ServiceTaskOutputInterface
{
    /**
     * It is necessary to provide the conversion from the object to the array output to pass to the model.
     *
     * All keys have to match the model expected variables keys.
     *
     * @return mixed
     */
    public function getNamesForRequiredProperties(): array;
}
