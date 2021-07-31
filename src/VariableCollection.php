<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\GatewayApi\Model\Variable;

class VariableCollection
{
    /** @var Variable[] */
    private array $variables;

    public function __construct(array $variables = null)
    {
        $this->variables = $variables ?? [];
    }

    /**
     * @return mixed
     */
    public function getVariableValue(string $name)
    {
        $var = $this->getVariable($name);
        if ($var === null) {
            return null;
        }

        $value = $var->getValue();
        if (is_array($value) && array_key_exists(0, $value)) {
            return $value[0];
        }

        return $value;
    }

    public function getVariable(string $name): ?Variable
    {
        foreach ($this->variables as $var) {
            if ($var->getName() === $name) {
                return $var;
            }
        }

        return null;
    }

    public function changeVariableValue(string $name, $value): bool
    {
        $var = $this->getVariable($name);
        if ($var === null) {
            return false;
        }

        $var->setValue($value);

        return true;
    }

    public function changeOrCreateVariableValue(string $name, $value)
    {
        if ($this->changeVariableValue($name, $value)) {
            return;
        }

        $this->addVariableValue($name, $value);
    }

    public function addVariable(Variable $variable)
    {
        $this->variables[] = $variable;
    }

    public function addVariableValue(string $name, $value)
    {
        $var = new Variable(['name' => $name]);
        $var->setValue($value);
        $this->variables[] = $var;
    }

    public function exists(string $name): bool
    {
        foreach ($this->variables as $var) {
            if ($var->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function all(): ?array
    {
        if (empty($this->variables)) {
            return null;
        }

        return $this->variables;
    }

    public function toArray(): array
    {
        return $this->variables;
    }
}
