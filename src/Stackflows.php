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
     * @param array $tags
     * @param array $variables
     * @return mixed
     */
    public function startBusinessProcesses(array $tags, array $variables = [])
    {
        return $this->getClient()->startTaggedProcessModels($tags, $variables);
    }
}
