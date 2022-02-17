<?php

namespace Stackflows;

use Illuminate\Support\Collection;
use Stackflows\Enum\VariableValueFormats;
use Stackflows\Http\Client\StackflowsClient;
use Stackflows\Http\Client\StackflowsDirectCamundaClient;
use Stackflows\Types\UserTaskType;
use Stackflows\Types\VariableType;

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

    /**
     * @return Collection
     */
    public function getUserTasks(): Collection
    {
        $tasks = new Collection();
        foreach ($this->getClient()->getUserTasks() as $task) {
            $tasks->add(new UserTaskType($task));
        }

        return $tasks;
    }

    /**
     * @return Collection
     */
    public function getVariables():Collection
    {
        $variables = new Collection();

        foreach ($this->getClient()->getVariables() as $variable) {
            $variables->add(new VariableType($variable));
        }

        return $variables;
    }

    /**
     * @param $id
     * @return VariableType
     */
    public function getVariableById($id):VariableType
    {
        $variable = $this->getClient()->getVariableById($id);

        return new VariableType($variable);
    }

    /**
     * @param string $name
     * @param string $type
     * @param        $values
     * @param        $options
     * @return bool
     */
    public function createVariable(string $name, string $type, $values, $options):bool
    {
        // Allow save arrays format
        if (VariableValueFormats::FORMAT_ARRAY === $type) {
            $values = json_decode($values, true);
        }

        if (is_null($options)) {
            $options = [];
        }

        $response = $this->getClient()->createVariable($name, $type, $values, $options);

        return !array_key_exists('error', $response) && array_key_exists('data', $response);
    }

    public function updateVariable(string $id, string $name, string $type, $values, $options)
    {
        // Allow save arrays format
        if (VariableValueFormats::FORMAT_ARRAY === $type) {
            $values = json_decode($values, true);
        }

        if (is_null($options)) {
            $options = [];
        }

        $response = $this->getClient()->updateVariable($id, $name, $type, $values, $options);

        return !array_key_exists('error', $response) && array_key_exists('data', $response);
    }
}
