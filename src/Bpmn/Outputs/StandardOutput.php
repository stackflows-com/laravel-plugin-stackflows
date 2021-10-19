<?php
namespace Stackflows\StackflowsPlugin\Bpmn\Outputs;

class StandardOutput extends AbstractExternalTaskOutput
{
    public function getNamesForRequiredProperties(): array
    {
        return array_keys($this->getVariables());
    }
}
