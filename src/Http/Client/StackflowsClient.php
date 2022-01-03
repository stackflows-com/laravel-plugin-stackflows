<?php

namespace Stackflows\Http\Client;

class StackflowsClient extends AbstractStackflowsClient
{
    public function startTaggedProcessModels(array $tags, array $variables = [])
    {
        return $this->makePostRequest("tagged/process-models/start", [
            'json' => [
                'tags' => $tags,
                'variables' => $variables,
            ],
        ]);
    }

    public function getProcessesByTag(string $tag)
    {
        return $this->makeGetRequest("direct/camunda/process-definition/get-by-tag/{$tag}");
    }
}
