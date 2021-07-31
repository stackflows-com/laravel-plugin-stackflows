<?php

namespace Stackflows\StackflowsPlugin\Tests\Factories;

use Illuminate\Support\Str;
use Stackflows\GatewayApi\Model\UserTask;
use Stackflows\GatewayApi\Model\Variable;

class UserTaskFactory
{
    /** @var Variable[] */
    private array $variables = [];

    public static function new(): self
    {
        return new self();
    }

    public function create(array $extra = []): UserTask
    {
        $data = array_merge(
            [
                'id' => Str::uuid()->toString(),
                'name' => 'Approve Invoice',
                'assignee' => 'demo',
                'owner' => null,
                'taskDefinitionKey' => 'approveInvoice',
                'createdAt' => new \DateTime(),
            ],
            $extra
        );
        $task = new UserTask($data);

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
