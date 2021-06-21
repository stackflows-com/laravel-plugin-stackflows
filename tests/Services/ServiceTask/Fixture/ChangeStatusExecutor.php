<?php

namespace Stackflows\StackflowsPlugin\Tests\Services\ServiceTask\Fixture;

use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\Services\ServiceTask\ServiceTaskExecutorInterface;

class ChangeStatusExecutor implements ServiceTaskExecutorInterface
{
    public function getReference(): array
    {
        return ['demo'];
    }

    public function getLockDuration(): int
    {
        return 5000;
    }

    public function execute(ServiceTask $task): ServiceTask
    {
        $variables = $this->changeStatus($task->getVariables());
        $task->setVariables($variables);
        return $task;
    }

    private function changeStatus(array $variables): array
    {
        return array_map(
            function (Variable $var) {
                if ($var->getName() === 'Status') {
                    $var->setValue((object)['approved']);
                }
                return $var;
            },
            $variables
        );
    }
}
