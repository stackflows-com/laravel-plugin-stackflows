<?php

namespace Stackflows;

use Stackflows\Http\Client\StackflowsClient;
use Stackflows\Http\Client\StackflowsDirectCamundaClient;

class Stackflows
{
    private StackflowsClient $client;
    private StackflowsDirectCamundaClient $directCamundaClient;

    public function __construct(StackflowsClient $client, StackflowsDirectCamundaClient $directCamundaClient)
    {
        $this->client = $client;
        $this->directCamundaClient = $directCamundaClient;
    }

    public function getClient(): StackflowsClient
    {
        return $this->client;
    }

    public function getDirectCamundaClient(): StackflowsDirectCamundaClient
    {
        return $this->directCamundaClient;
    }

    /**
     * Trigger tagged business processes
     *
     * @param string $tag
     * @param array $variables
     * @param int|null $version
     * @return void
     */
    public function startBusinessProcesses(string $tag, array $variables = [], int $version = null): void
    {
        //TODO: Trigger business processes based on specified tag and version
    }
}
