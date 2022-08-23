<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\BusinessProcessModelPublicationBridgeContract;
use Stackflows\DataTransfer\Types\BusinessProcessInstanceType;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\DataPointType;
use App\Models\BusinessProcessModelPublication;
use Stackflows\Types\EnvironmentType;
use App\Statistics\BusinessProcessActivityStatisticalUnit;
use Stackflows\Transformers\Bridge\Camunda\DataPointCollectionToVariablesTransformer;
use Illuminate\Support\Collection;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;
use Stackflows\Clients\Camunda\v7_17\Api\ProcessDefinitionApi;
use Stackflows\Clients\Camunda\v7_17\Model\StartProcessInstanceFormDto;
use Stackflows\Clients\Camunda\v7_17\Model\VariableValueDto;

class BusinessProcessModelPublicationCamundaBridge
    extends BusinessModelPublicationCamundaBridge
    implements BusinessProcessModelPublicationBridgeContract
{
    public function __construct(
        Environment $environment,
        protected DeploymentApi $deploymentApi,
        protected ProcessDefinitionApi $processDefinitionApi,
        protected DataPointCollectionToVariablesTransformer $dataPointCollectionToVariablesTransformer
    ) {

    }

    protected function transform(array $datum): array
    {
        return [];
    }

    public function start(BusinessProcessModelPublication $publication, DataPointCollection $submission = null)
    {
        $variables = null;
        if ($submission) {
            $variables = $this->dataPointCollectionToVariablesTransformer->convert($submission, true);
        }

        $processInstanceDto = $this->processDefinitionApi->submitFormByKeyAndTenantId(
            $publication->getAttribute('engine_model_reference'),
            $publication->environment->getAttribute('engine_reference'),
            new StartProcessInstanceFormDto(['variables' => $variables])
        );

        return new BusinessProcessInstanceType([
            'reference' => $processInstanceDto->getId(),
            'publication' => BusinessProcessModelPublication::query()
                ->where('engine_diagram_reference', $processInstanceDto->getDefinitionId())
                ->first(),
            'context' => $processInstanceDto->getBusinessKey(),
        ]);
    }

    public function getActivityStatistics(BusinessProcessModelPublication $publication): Collection
    {
        $statistics = new Collection();

        if ($publication->getAttribute('engine_model_reference') === null) {
            return $statistics;
        }

        $rawStatistics = $this->processDefinitionApi->getActivityStatisticsByProcessDefinitionKeyAndTenantId(
            $publication->getAttribute('engine_model_reference'),
            $publication->environment->getAttribute('engine_reference')
        );
        foreach ($rawStatistics as $rawStatisticalUnit) {
            $statistics->add(new BusinessProcessActivityStatisticalUnit(
                $rawStatisticalUnit['id'],
                $rawStatisticalUnit['instances'],
                count($rawStatisticalUnit['incidents']),
                $rawStatisticalUnit['failedJobs'],
            ));
        }

        return $statistics;
    }
}
