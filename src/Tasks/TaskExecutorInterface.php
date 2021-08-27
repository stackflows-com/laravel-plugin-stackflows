<?php

namespace Stackflows\StackflowsPlugin\Tasks;

interface TaskExecutorInterface {
    public static function getTopic(): string;
    public static function getLockDuration(): int;
    public function execute(): ?array;
}
