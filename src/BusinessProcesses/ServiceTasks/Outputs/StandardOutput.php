<?php
namespace Stackflows\BusinessProcesses\ServiceTasks\Outputs;

class StandardOutput extends AbstractServiceTaskOutput
{
    public function getNamesForRequiredProperties(): array
    {
        return array_keys($this->getVariables()->items());
    }
}
