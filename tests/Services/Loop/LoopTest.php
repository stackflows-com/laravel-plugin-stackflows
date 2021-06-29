<?php

namespace Stackflows\StackflowsPlugin\Tests\Services\Loop;

use PHPUnit\Framework\TestCase;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\Loop;
use Stackflows\StackflowsPlugin\Services\Loop\LoopHandlerInterface;

class LoopTest extends TestCase
{
    /** @test */
    public function runWithTooManyError()
    {
        $handler = $this->createMock(LoopHandlerInterface::class);
        $handler->method('handle')
            ->willThrowException(TooManyErrors::executorHasTooManyErrors($handler::class));

        $loop = new Loop($handler);

        $this->expectException(TooManyErrors::class);

        $loop->run();
    }
}
