<?php

namespace Stackflows\StackflowsPlugin\Tasks;

use Stackflows\StackflowsPlugin\Bpmn\Inputs\AbstractExternalTaskInput;
use Stackflows\StackflowsPlugin\Bpmn\Inputs\ExternalTaskInputInterface;

class TaskService
{
    public function convertToExternalTaskRequest(AbstractExternalTaskInput $externalTask, array $taskArray): ExternalTaskInputInterface
    {
        $externalTask->setId($taskArray['id'] ?? null);
        $externalTask->setActivityId($taskArray['activityId'] ?? null);
        $externalTask->setActivityInstanceId($taskArray['activityInstanceId'] ?? null);
        $externalTask->setErrorMessage($taskArray['errorMessage'] ?? null);
        $externalTask->setExecutionId($taskArray['executionId'] ?? null);
        $externalTask->setLockExpirationTime(new \DateTime($taskArray['lockExpirationTime'] ?? null));
        $externalTask->setPriority($taskArray['priority'] ?? null);
        $externalTask->setProcessDefinitionId($taskArray['processDefinitionId'] ?? null);
        $externalTask->setProcessDefinitionKey($taskArray['processDefinitionKey'] ?? null);
        $externalTask->setProcessDefinitionVersionTag($taskArray['processDefinitionVersionTag'] ?? null);
        $externalTask->setProcessInstanceId($taskArray['processInstanceId'] ?? null);
        $externalTask->setRetries($taskArray['retries'] ?? null);
        $externalTask->setTenantId($taskArray['tenantId'] ?? null);
        $externalTask->setTopicName($taskArray['topicName'] ?? null);
        $externalTask->setWorkerId($taskArray['workerId'] ?? null);

        foreach ($taskArray['variables'] as $variableName => $variableOptions) {
            $method = 'set'.ucfirst($variableName);
            if (method_exists($externalTask, $method)) {
                $externalTask->$method($variableOptions['value']);
            }
        }

        return $externalTask;
    }
}
