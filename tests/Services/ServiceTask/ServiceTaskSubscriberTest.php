<?php

namespace Stackflows\StackflowsPlugin\Tests\Services\ServiceTask;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Stackflows\StackflowsPlugin\Channels\ServiceTaskChannel;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\LoopLogger;
use Stackflows\StackflowsPlugin\Services\ServiceTask\ServiceTaskSubscriber;
use Stackflows\StackflowsPlugin\Tests\Factories\ServiceTaskFactory;
use Stackflows\StackflowsPlugin\Tests\Factories\VariableFactory;
use Stackflows\StackflowsPlugin\Tests\Services\ServiceTask\Fixture\ChangeStatusExecutor;
use Stackflows\StackflowsPlugin\Tests\Services\ServiceTask\Fixture\ExecutorWithException;
use Symfony\Component\Console\Output\OutputInterface;

class ServiceTaskSubscriberTest extends TestCase
{
    /** @test */
    public function handle()
    {
        $statusVar = VariableFactory::new()->make(['value' => 'not reviewed']);
        $approved = VariableFactory::new()->make(['value' => 'approved']);
        $task = ServiceTaskFactory::new()
            ->addVariable($statusVar)
            ->create();

        $executor = new ChangeStatusExecutor();

        $api = $this->createMock(ServiceTaskChannel::class);
        $api->method('getPending')
            ->willReturn([$task]);

        $api->expects($this->once())
            ->method('getPending')
            ->with(
                $this->equalTo($executor->getReference()),
                $this->equalTo($executor->getLockDuration())
            );

        $api->expects($this->once())
            ->method('complete')
            ->with(
                $this->equalTo($task->getId()),
                $this->equalTo([$approved])
            );

        $subscriber = new ServiceTaskSubscriber(
            $api,
            $this->createMock(LoopLogger::class),
            [$executor]
        );

        $subscriber->handle();
    }

    /** @test */
    public function handleWithLogError()
    {
        $task = ServiceTaskFactory::new()->create();
        $executor = new ExecutorWithException();

        $api = $this->createMock(ServiceTaskChannel::class);
        $api->method('getPending')
            ->willReturn([$task]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error');
        $loopLogger = new LoopLogger(
            $logger,
            $this->createMock(OutputInterface::class),
        );

        $subscriber = new ServiceTaskSubscriber($api, $loopLogger, [$executor]);

        $subscriber->handle();
    }

    /** @test */
    public function handleWithTooManyErrors()
    {
        $task = ServiceTaskFactory::new()->create();
        $executor = new ExecutorWithException();

        $api = $this->createMock(ServiceTaskChannel::class);
        $api->method('getPending')
            ->willReturn([$task, $task, $task, $task, $task, $task, $task]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(7))
            ->method('error');
        $loopLogger = new LoopLogger(
            $logger,
            $this->createMock(OutputInterface::class),
        );

        $subscriber = new ServiceTaskSubscriber($api, $loopLogger, [$executor]);

        $this->expectException(TooManyErrors::class);

        $subscriber->handle();
    }
}
