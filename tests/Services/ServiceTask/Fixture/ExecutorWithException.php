<?php

namespace Stackflows\StackflowsPlugin\Tests\Services\ServiceTask\Fixture;

use Exception;
use Stackflows\GatewayApi\Model\ServiceTask;
use Stackflows\StackflowsPlugin\Services\ServiceTask\ServiceTaskExecutorInterface;

class ExecutorWithException implements ServiceTaskExecutorInterface
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
        throw new Exception("oops");
    }
}
