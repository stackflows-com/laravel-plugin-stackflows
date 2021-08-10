<?php

namespace Stackflows\StackflowsPlugin\Tests\Services\UserTask;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Stackflows\StackflowsPlugin\Channels\UserTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\LoopLogger;
use Stackflows\StackflowsPlugin\Services\UserTask\UserTaskSync;
use Stackflows\StackflowsPlugin\Services\UserTask\UserTaskSyncInterface;
use Stackflows\StackflowsPlugin\Tests\Factories\UserTaskFactory;
use Symfony\Component\Console\Output\OutputInterface;

class UserTaskSyncTest extends TestCase
{
    /** @test */
    public function handle()
    {
        $task = UserTaskFactory::new()->create();
        $sync = $this->createMock(UserTaskSyncInterface::class);
        $sync->expects($this->once())
            ->method('sync')
            ->with(
                $this->equalTo([$task]),
                $this->equalTo(['createdAt' => null])
            );

        $api = $this->createMock(UserTaskChannel::class);
        $api->expects($this->once())
            ->method('getList')
            ->willReturn([$task]);

        $logger = $this->createMock(LoopLogger::class);

        $syncHandler = new UserTaskSync($api, $logger, [$sync]);

        $syncHandler->handle();
    }

    /** @test */
    public function handleWithLogError()
    {
        $task = UserTaskFactory::new()->create();
        $sync = $this->createMock(UserTaskSyncInterface::class);
        $sync->expects($this->once())
            ->method('sync')
            ->willThrowException(new \Exception("oops"));

        $api = $this->createMock(UserTaskChannel::class);
        $api->expects($this->once())
            ->method('getList')
            ->willReturn([$task]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error');

        $loopLogger = new LoopLogger($logger, $this->createMock(OutputInterface::class));

        $syncHandler = new UserTaskSync($api, $loopLogger, [$sync]);

        $syncHandler->handle();
    }

    /** @test */
    public function handleWithTooManyError()
    {
        $task = UserTaskFactory::new()->create();
        $sync = $this->createMock(UserTaskSyncInterface::class);
        $sync->method('sync')
            ->willThrowException(new \Exception("oops"));

        $api = $this->createMock(UserTaskChannel::class);
        $api->method('getList')
            ->willReturn([$task]);

        $logger = $this->createMock(LoopLogger::class);

        $syncHandler = new UserTaskSync($api, $logger, [$sync]);

        $this->expectException(TooManyErrors::class);

        for ($i = 0; $i < 7; $i++) {
            $syncHandler->handle();
        }
    }

    /** @test */
    public function handleWithCreatedAfterAssert()
    {
        $task = UserTaskFactory::new()->create();
        $sync = $this->createMock(UserTaskSyncInterface::class);

        $api = $this->createMock(UserTaskChannel::class);
        $api->method('getList')
            ->willReturn([$task]);

        $logger = $this->createMock(LoopLogger::class);

        $syncHandler = new UserTaskSync($api, $logger, [$sync]);

        $syncHandler->handle();

        $api->expects($this->once())
            ->method('getList')
            ->with($this->equalToWithDelta(new \DateTime(), 2));

        $syncHandler->handle();
    }
}
