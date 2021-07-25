<?php

namespace Stackflows\StackflowsPlugin\Services\UserTask;

use DateTime;
use Psr\Log\LoggerInterface;
use Stackflows\GatewayApi\ApiException;
use Stackflows\GatewayApi\Model\UserTask;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\LoopHandlerInterface;

class UserTaskSync implements LoopHandlerInterface
{
    private UserTaskChannel $api;
    private LoggerInterface $logger;

    private iterable $synchronizers;

    /** @var array<string, int> */
    private array $errors;

    private ?DateTime $createdAfter = null;

    public function __construct(UserTaskChannel $api, LoggerInterface $logger, iterable $synchronizers)
    {
        $this->api = $api;
        $this->logger = $logger;
        $this->synchronizers = $synchronizers;
        $this->errors = $this->getErrorMap($synchronizers);
    }

    public function handle(): void
    {
        $tasks = $this->fetch();
        $this->execute($tasks);
        $this->setCreatedAfter($tasks);
    }

    /**
     * @throws TooManyErrors|ApiException
     */
    private function fetch(): array
    {
        return $this->api->getList($this->createdAfter);
    }

    /**
     * @throws TooManyErrors
     */
    private function execute(array $tasks): void
    {
        foreach ($this->synchronizers as $sync) {
            try {
                $sync->sync($tasks, ['createdAt' => $this->createdAfter]);
                $this->errors[get_class($sync)] = 0;
            } catch (\Exception $e) {
                $this->logger->error(sprintf("%s %s(%s)", $e->getMessage(), $e->getFile(), $e->getLine()));
                $this->errors[get_class($sync)] += 1;
            }

            if ($this->errors[get_class($sync)] >= 7) {
                throw TooManyErrors::synchronizerHasTooManyErrors(get_class($sync));
            }
        }
    }

    /**
     * @return array<string, int>
     */
    private function getErrorMap(iterable $synchronizers): array
    {
        $errorMap = [];
        foreach ($synchronizers as $obj) {
            $errorMap[get_class($obj)] = 0;
        }

        return $errorMap;
    }

    /**
     * @param UserTask[] $tasks
     */
    private function setCreatedAfter(array $tasks): void
    {
        if (empty($tasks)) {
            return;
        }
        $last = $this->createdAfter;
        foreach ($tasks as $task) {
            if ($task->getCreatedAt() > $last) {
                $last = $task->getCreatedAt();
            }
        }

        $this->createdAfter = is_null($last) ? null : $last->add(\DateInterval::createFromDateString('1 second'));
    }
}
