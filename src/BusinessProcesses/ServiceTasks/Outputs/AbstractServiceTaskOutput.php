<?php

namespace Stackflows\BusinessProcesses\ServiceTasks\Outputs;

use Stackflows\Types\VariableType;

abstract class AbstractServiceTaskOutput implements ServiceTaskOutputInterface
{
    private array $variables;

    public function __construct(array $variables = [])
    {
        $this->variables = $variables;
    }

    public function addVariable(string $key, VariableType $variable): self
    {
        $this->variables[$key] = $variable;

        return $this;
    }

    public function reset()
    {
        $this->variables = [];
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function setVariables(array $variables): self
    {
        $this->variables = $variables;

        return $this;
    }
}
