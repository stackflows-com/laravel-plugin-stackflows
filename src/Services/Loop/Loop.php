<?php

namespace Stackflows\StackflowsPlugin\Services\Loop;

use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;

final class Loop
{
    private bool $stopped = false;
    private LoopHandlerInterface $handler;

    public function __construct(LoopHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @throws TooManyErrors
     */
    public function run(int $interval = 10): void
    {
        while (! $this->stopped) {
            $this->handler->handle();
            // Todo: make more gracefully
            sleep($interval);
        }
    }

    public function stop(): void
    {
        $this->stopped = true;
    }
}
