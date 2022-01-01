<?php

namespace Stackflows\Http\Client;

class StackflowsClient extends AbstractStackflowsClient
{
    public function getProcessesByTag(string $tag)
    {
        return $this->makeGetRequest("direct/camunda/process-definition/get-by-tag/{$tag}");
    }
}
