<?php

namespace Stackflows\StackflowsPlugin\Tasks;

use Stackflows\StackflowsPlugin\Bpmn\Requests\AbstractExternalTaskRequest;
use Stackflows\StackflowsPlugin\Bpmn\Requests\ExternalTaskRequestInterface;

class TaskService
{
    public function convertToExternalTaskRequest(AbstractExternalTaskRequest $externalTask, array $taskArray): ExternalTaskRequestInterface
    {
        $externalTask->setActivityId($taskArray['activityId'] ?? null);
        $externalTask->setActivityInstanceId($taskArray['activityInstanceId'] ?? null);
        $externalTask->setErrorMessage($taskArray['errorMessage'] ?? null);
        $externalTask->setExecutionId($taskArray['executionId'] ?? null);
        $externalTask->setLockExpirationTime($taskArray['lockExpirationTime'] ?? null);
        $externalTask->setPriority($taskArray['priority'] ?? null);
        $externalTask->setProcessDefinitionId($taskArray['processDefinitionId'] ?? null);
        $externalTask->setProcessDefinitionKey($taskArray['processDefinitionKey'] ?? null);
        $externalTask->setProcessDefinitionVersionTag($taskArray['processDefinitionVersionTag'] ?? null);
        $externalTask->setProcessInstanceId($taskArray['processInstanceId'] ?? null);
        $externalTask->setRetries($taskArray['retries'] ?? null);
        $externalTask->setTenantId($taskArray['tenantId'] ?? null);
        $externalTask->setTopicName($taskArray['topicName'] ?? null);
        $externalTask->setWorkerId($taskArray['workerId'] ?? null);

        return $externalTask;
    }
}