<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

use Stackflows\GatewayApi\ApiException;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\LoopHandlerInterface;

class UserTaskSync implements LoopHandlerInterface
{
    private UserTaskChannel $api;

    /** @var UserTaskSyncInterface[] */
    private array $synchronizers;

    public function __construct(UserTaskChannel $api, array $synchronizers)
    {
        $this->api = $api;
        $this->synchronizers = $synchronizers;
    }

    public function handle(): void
    {
        $tasks = $this->fetch();
        $this->execute($tasks);
    }

    /**
     * @throws TooManyErrors|ApiException
     */
    private function fetch(): array
    {
        return $this->api->getList();
    }

    /**
     * @throws TooManyErrors
     */
    private function execute(array $tasks): void
    {
        foreach ($this->synchronizers as $sync) {
            $sync->sync($tasks);
        }
    }

    /**
     * @return array<string, int>
     */
    private function getErrorMap(iterable $synchronizers): array
    {
        $errorMap = [];
        foreach ($synchronizers as $obj) {
            $errorMap[$obj::class] = 0;
        }

        return $errorMap;
    }
}
