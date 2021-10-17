<?php

namespace Stackflows\StackflowsPlugin;

use Stackflows\StackflowsPlugin\Http\Client\StackflowsClient;

class Stackflows
{
    private StackflowsClient $stackFlowsClient;

    public function __construct(StackflowsClient $stackFlowsClient)
    {
        $this->stackFlowsClient = $stackFlowsClient;
    }

    public function getStackFlowsClient(): StackflowsClient
    {
        return $this->stackFlowsClient;
    }
}
