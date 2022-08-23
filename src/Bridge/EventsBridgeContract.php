<?php

namespace Stackflows\Bridge;

use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\EventType;

interface EventsBridgeContract
{
    public function sendMessage(string $reference, string $context = null, DataPointCollection $submission = null): EventType;

    public function sendSignal(string $reference, DataPointCollection $submission = null): EventType;
}
