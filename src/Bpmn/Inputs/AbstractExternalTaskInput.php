<?php

namespace Stackflows\StackflowsPlugin\Bpmn\Inputs;

abstract class AbstractExternalTaskInput implements ExternalTaskInputInterface
{
    private ?string $id;
    private ?string $activityId;
    private ?string $activityInstanceId;
    private ?string $executionId;
    private \DateTime $lockExpirationTime;
    private ?string $processDefinitionId;
    private ?string $processDefinitionKey;
    private ?string $processDefinitionVersionTag;
    private ?string $processInstanceId;
    private ?string $retries;
    private ?string $workerId;
    private ?string $topicName;
    private ?string $tenantId;
    private ?string $priority;
    private ?string $errorMessage;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getActivityId(): ?string
    {
        return $this->activityId;
    }

    public function setActivityId(?string $activityId): self
    {
        $this->activityId = $activityId;

        return $this;
    }

    public function getActivityInstanceId(): ?string
    {
        return $this->activityInstanceId;
    }

    public function setActivityInstanceId(?string $activityInstanceId): self
    {
        $this->activityInstanceId = $activityInstanceId;

        return $this;
    }

    public function getExecutionId(): ?string
    {
        return $this->executionId;
    }

    public function setExecutionId(?string $executionId): self
    {
        $this->executionId = $executionId;

        return $this;
    }

    public function getLockExpirationTime(): \DateTime
    {
        return $this->lockExpirationTime;
    }

    public function setLockExpirationTime(\DateTime $lockExpirationTime): self
    {
        $this->lockExpirationTime = $lockExpirationTime;

        return $this;
    }

    public function getProcessDefinitionId(): ?string
    {
        return $this->processDefinitionId;
    }

    public function setProcessDefinitionId(?string $processDefinitionId): self
    {
        $this->processDefinitionId = $processDefinitionId;

        return $this;
    }

    public function getProcessDefinitionKey(): ?string
    {
        return $this->processDefinitionKey;
    }

    public function setProcessDefinitionKey(?string $processDefinitionKey): self
    {
        $this->processDefinitionKey = $processDefinitionKey;

        return $this;
    }

    public function getProcessDefinitionVersionTag(): ?string
    {
        return $this->processDefinitionVersionTag;
    }

    public function setProcessDefinitionVersionTag(?string $processDefinitionVersionTag): self
    {
        $this->processDefinitionVersionTag = $processDefinitionVersionTag;

        return $this;
    }

    public function getProcessInstanceId(): ?string
    {
        return $this->processInstanceId;
    }

    public function setProcessInstanceId(?string $processInstanceId): self
    {
        $this->processInstanceId = $processInstanceId;

        return $this;
    }

    public function getRetries(): ?string
    {
        return $this->retries;
    }

    public function setRetries(?string $retries): self
    {
        $this->retries = $retries;

        return $this;
    }

    public function getWorkerId(): ?string
    {
        return $this->workerId;
    }

    public function setWorkerId(?string $workerId): self
    {
        $this->workerId = $workerId;

        return $this;
    }

    public function getTopicName(): ?string
    {
        return $this->topicName;
    }

    public function setTopicName(?string $topicName): self
    {
        $this->topicName = $topicName;

        return $this;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function setTenantId(?string $tenantId): self
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(?string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param ?string $errorMessage
     * @return AbstractExternalTaskRequest
     */
    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
