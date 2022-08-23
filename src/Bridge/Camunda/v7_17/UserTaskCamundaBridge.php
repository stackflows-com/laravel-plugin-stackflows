<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\Camunda\Apis\TaskApiContract;
use Stackflows\Bridge\UserTaskBridgeContract;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\DataAttributeType;
use Stackflows\DataTransfer\Types\DataPointType;
use Stackflows\DataTransfer\Types\UserTaskType;
use Stackflows\Types\EnvironmentType;
use Stackflows\Transformers\Bridge\Camunda\UserTaskListRequestToApiParamsTransformer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stackflows\Clients\Camunda\v7_17\Api\TaskApi;
use Stackflows\Clients\Camunda\v7_17\Api\TaskVariableApi;
use Stackflows\Clients\Camunda\v7_17\Model\CompleteTaskDto;
use Stackflows\Clients\Camunda\v7_17\Model\TaskBpmnErrorDto;
use Stackflows\Clients\Camunda\v7_17\Model\TaskDto;
use Stackflows\Clients\Camunda\v7_17\Model\TaskEscalationDto;
use Stackflows\Clients\Camunda\v7_17\Model\VariableValueDto;

class UserTaskCamundaBridge extends AbstractBridge implements UserTaskBridgeContract
{
    protected Environment $environment;
    protected TaskApi $taskApi;
    protected TaskVariableApi $taskVariableApi;
    protected UserTaskListRequestToApiParamsTransformer $userTaskListRequestToApiParamsTransformer;

    private array $fieldTypesMap = [
        'String' => DataAttributeType::TYPE_STRING,
        'Long' => DataAttributeType::TYPE_INTEGER,
        'Integer' => DataAttributeType::TYPE_INTEGER,
        'Double' => DataAttributeType::TYPE_FLOAT,
        'Boolean' => DataAttributeType::TYPE_BOOLEAN,
        'Null' => DataAttributeType::TYPE_STRING,
    ];


    protected function transform(TaskDto $datum): UserTaskType
    {
        return new UserTaskType([
            'processInstanceReference' => $datum->getProcessInstanceId(),
            'reference' => $datum->getId(),
            'subject' => $datum->getName(),
            'description' => $datum->getDescription(),
            'followUpAt' => Carbon::parse($datum->getFollowUp()),
            'dueAt' => Carbon::parse($datum->getDue()),
            'attributes' => $this->getAttributes($datum->getId()),
            'fields' => $this->getFields($datum->getId()),
        ]);
    }

    public function getCount(array $criteria = []): int
    {
        unset($criteria['offset']);
        unset($criteria['limit']);

        return $this->taskApi->getTasksCount(
            ...$this->userTaskListRequestToApiParamsTransformer->convert($criteria)
        )->getCount();
    }

    public function getAll(array $criteria = []): Collection
    {
        return new Collection(array_map(
            fn ($datum) => $this->transform($datum),
            $this->taskApi->getTasks(...$this->userTaskListRequestToApiParamsTransformer->convert($criteria))
        ));
    }

    public function get(string $id): UserTaskType
    {
        return $this->transform($this->taskApi->getTask($id));
    }

    public function errorize(
        string $id,
        string $code,
        string $message = null,
        DataPointCollection $submission = null
    ): UserTaskType {
        $task = $this->get($id);

        $this->taskApi->handleBpmnError($task->reference, new TaskBpmnErrorDto([
            'errorCode' => $code,
            'errorMessage' => $message,
            'variables' => $submission ? $submission->toArray() : null,
        ]));

        return $task;
    }

    public function complete(string $id, DataPointCollection $submission = null): UserTaskType
    {
        $task = $this->get($id);

        $this->taskApi->complete($task->reference, new CompleteTaskDto([
            'variables' => $submission ? $submission->toArray() : null,
        ]));

        return $task;
    }

    public function submit(string $id, DataPointCollection $submission = null): UserTaskType
    {
        $task = $this->get($id);

        $this->taskApi->submit($task->reference, new CompleteTaskDto([
            'variables' => $submission ? $submission->toArray() : null,
        ]));

        return $task;
    }

    public function escalate(string $id, string $code, DataPointCollection $submission = null): UserTaskType
    {
        $task = $this->get($id);

        $this->taskApi->handleEscalation($task->reference, new TaskEscalationDto([
            'escalationCode' => $code,
            'variables' => $submission ? $submission->toArray() : null,
        ]));

        return $task;
    }

    /**
     * @param string $taskId
     * @return Collection|DataPointCollection
     */
    public function getAttributes(string $taskId): Collection
    {
        $variableTypesToSkip = ['Object', 'File'];

        return (new DataPointCollection($this->taskVariableApi->getTaskVariables($taskId)))
            ->filter(function (VariableValueDto $value) use ($variableTypesToSkip) {
                if (in_array($value->getType(), $variableTypesToSkip)) {
                    return false;
                }

                return $value;
            })
            ->transform(function (VariableValueDto $value, $name) {
                return new DataPointType(
                    new DataAttributeType([
                        DataAttributeType::KEY_REFERENCE        => Str::snake($name),
                        DataAttributeType::KEY_ENGINE_REFERENCE => $name,
                        DataAttributeType::KEY_TYPE             => $this->fieldTypesMap[$value->getType()],
                        DataAttributeType::KEY_LABEL            => ucwords(Str::replace('_', ' ', Str::snake($name)))
                    ]),
                    $value->getValue()
                );
            });
    }

    /**
     * @param string $taskId
     * @return Collection|DataPointCollection
     */
    public function getFields(string $taskId): DataPointCollection
    {
        $variableTypesToSkip = ['Object', 'File'];

        return (new DataPointCollection($this->taskApi->getFormVariables($taskId)))
            ->filter(function (VariableValueDto $value) use ($variableTypesToSkip) {
                if (in_array($value->getType(), $variableTypesToSkip)) {
                    return false;
                }

                return $value;
            })
            ->transform(function (VariableValueDto $value, $name) {
                return new DataAttributeType([
                    DataAttributeType::KEY_REFERENCE        => Str::snake($name),
                    DataAttributeType::KEY_ENGINE_REFERENCE => $name,
                    DataAttributeType::KEY_TYPE             => $this->fieldTypesMap[$value->getType()],
                    DataAttributeType::KEY_LABEL            => ucwords(Str::replace('_', ' ', Str::snake($name)))
                ]);
            });
    }
}
