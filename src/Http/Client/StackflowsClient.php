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

    public function getUserTasks()
    {
        return $this->makeGetRequest("user-tasks")['data'];
    }

    public function getVariables()
    {
        return $this->makeGetRequest("variables")['data'];
    }

    public function getVariableById($id)
    {
        return $this->makeGetRequest("variables/{$id}")['data'];
    }

    public function createVariable(string $name, string $type, $values, array $options)
    {
        return $this->makePostRequest("variables", [
            'json' => [
                'name' => $name,
                'type' => $type,
                'value' => $values,
                'option' => $options,
            ],
        ]);
    }

    public function updateVariable(string $id, string $name, string $type, $values, array $options)
    {
        return $this->makePutRequest("variables/{$id}", [
            'json' => [
                'name' => $name,
                'type' => $type,
                'value' => $values,
                'option' => $options,
            ],
        ]);
    }
}
