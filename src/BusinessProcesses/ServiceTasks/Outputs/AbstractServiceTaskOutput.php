<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Outputs;

use Stackflows\Types\DataTransfer\VariableCollectionType;
use Stackflows\Types\DataTransfer\VariableType;

abstract class AbstractServiceTaskOutput implements ServiceTaskOutputInterface
{
    private VariableCollectionType $variables;

    public function __construct(array $variables = [])
    {
        $this->variables = new VariableCollectionType($variables);
    }

    public function addVariable(string $key, VariableType $variable): self
    {
        $this->variables[$key] = $variable;

        return $this;
    }

    public function reset()
    {
        $this->variables = new VariableCollectionType();
    }

    public function getVariables(): VariableCollectionType
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = new VariableCollectionType($variables);

        return $this;
    }
}
