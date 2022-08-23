<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\LoggableBridgeContract;
use Stackflows\Bridge\ServiceTaskBridgeContract;
use Stackflows\DataTransfer\Types\ActivityType;
use Stackflows\DataTransfer\Types\BusinessProcessInstanceType;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\ServiceTaskType;
use App\Models\BusinessProcessModelPublication;
use Stackflows\Types\EnvironmentType;
use Stackflows\Transformers\Bridge\Camunda\DataPointCollectionToVariablesTransformer;
use Stackflows\Transformers\Bridge\Camunda\VariableInstanceToDataPointCollectionTransformer;
use Illuminate\Support\Collection;
use Stackflows\Clients\Camunda\v7_17\Api\ExternalTaskApi;
use Stackflows\Clients\Camunda\v7_17\Api\HistoricExternalTaskLogApi;
use Stackflows\Clients\Camunda\v7_17\Api\VariableInstanceApi;
use Stackflows\Clients\Camunda\v7_17\Model\CompleteExternalTaskDto;
use Stackflows\Clients\Camunda\v7_17\Model\ExternalTaskDto;
use Stackflows\Clients\Camunda\v7_17\Model\FetchExternalTasksDto;
use Stackflows\Clients\Camunda\v7_17\Model\FetchExternalTaskTopicDto;
use Stackflows\Clients\Camunda\v7_17\Model\LockedExternalTaskDto;
use Stackflows\Clients\Camunda\v7_17\Model\LockExternalTaskDto;

class ServiceTaskCamundaBridge extends AbstractBridge implements ServiceTaskBridgeContract, LoggableBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected VariableInstanceApi $variableInstanceApi,
        protected ExternalTaskApi $externalTaskApi,
        protected HistoricExternalTaskLogApi $historicExternalTaskLogApi,
        protected VariableInstanceToDataPointCollectionTransformer $variableInstanceToDataObjectTransformer,
        protected DataPointCollectionToVariablesTransformer $dataObjectToVariablesTransformer,
    ) {
    }

    /**
     * @param mixed|ExternalTaskDto|LockedExternalTaskDto $datum
     * @return ServiceTaskType
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    protected function transform(mixed $datum): ServiceTaskType
    {
        $publication = BusinessProcessModelPublication::query()
            ->where('engine_diagram_reference', $datum->getProcessDefinitionId())
            ->first();

        return new ServiceTaskType([
            'reference'   => $datum->getId(),
            'topic'       => $datum->getTopicName(),
            'suspended'   => $datum->getSuspended(),
            'priority'    => $datum->getPriority(),
            'activity'    => new ActivityType([
                'name'      => $datum->getActivityId(),
                'reference' => $datum->getActivityInstanceId(),
            ]),
            'instance'    => new BusinessProcessInstanceType([
                'reference'   => $datum->getProcessInstanceId(),
                'publication' => $publication,
            ]),
            'attributes' => $this->variableInstanceToDataObjectTransformer->convert(
                $this->variableInstanceApi->getVariableInstances(...[
                    'processInstanceIdIn' => [
                        $datum->getProcessInstanceId()
                    ],
                ])
            ),
        ]);
    }

    public function getAll(): Collection
    {
        return new Collection(
            array_map(
                fn($datum) => $this->transform($datum),
                $this->externalTaskApi->getExternalTasks()
            )
        );
    }

    public function get(string $reference): ServiceTaskType
    {
        return $this->transform($this->externalTaskApi->getExternalTask($reference));
    }

    public function lockTopic(string $topic, string $lock, int $duration = 300, int $limit = 100): Collection
    {
        $data = $this->externalTaskApi->fetchAndLock(
            new FetchExternalTasksDto([
                'topics'               => [
                    new FetchExternalTaskTopicDto([
                        'topicName' => $topic,
                        'lockDuration' => $duration,
                        'tenantIdIn' => [$this->environment->getAttribute('engine_reference')]
                    ])
                ],
                'workerId'             => $lock,
                'maxTasks'             => $limit
            ])
        );

        return (new Collection($data))->map(fn($datum) => $this->transform($datum));
    }

    public function lock(string $reference, string $lock, int $duration = 300): ServiceTaskType
    {
        $this->externalTaskApi->lock(
            $reference,
            new LockExternalTaskDto([
                'workerId' => $lock,
                'lockDuration' => $duration,
            ])
        );

        return $this->get($reference);
    }

    public function unlock(string $reference): ServiceTaskType
    {
        $this->externalTaskApi->unlock($reference);

        return $this->get($reference);
    }

    public function serve(?string $lock, string $reference, DataPointCollection $dataObject = null): ServiceTaskType
    {
        $serviceTask = $this->get($reference);

        $this->externalTaskApi->completeExternalTaskResource(
            $reference,
            new CompleteExternalTaskDto([
                'variables' => $this->dataObjectToVariablesTransformer->convert($dataObject),
                'workerId'  => $lock,
            ])
        );

        return $serviceTask;
    }

    public function logs(int $offset = 0, int $limit = 100): Collection
    {
        return new Collection($this->historicExternalTaskLogApi->getHistoricExternalTaskLogs(...[
            'sortBy' => 'timestamp',
            'sortOrder' => 'desc',
            'firstResult' => $offset,
            'maxResults' => $limit,
        ]));
    }
}
