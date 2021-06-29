<?php

namespace Stackflows\StackflowsPlugin\Tests\Factories;

use Illuminate\Support\Str;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\GatewayApi\Model\Variable;

final class ServiceTaskFactory
{
    /** @var Variable[] */
    private array $variables = [];

    public static function new(): self
    {
        return new self();
    }

    public function create(array $extra = []): ServiceTask
    {
        $task = new ServiceTask(
            array_merge(
                [
                    'id' => Str::uuid()->toString(),
                    'processDefinitionKey' => 'demo-task',
                ],
                $extra
            )
        );

        if (! empty($this->variables)) {
            $task->setVariables($this->variables);
        }

        return $task;
    }

    public function addVariable(Variable $var): self
    {
        $factory = clone $this;
        $factory->variables[] = $var;

        return $factory;
    }
}
