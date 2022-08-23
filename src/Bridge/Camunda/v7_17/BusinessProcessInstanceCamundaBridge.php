<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessProcessInstanceBridgeContract;
use Stackflows\Bridge\LoggableBridgeContract;
use Stackflows\DataTransfer\Types\BusinessProcessInstanceType;
use App\Models\BusinessProcessModelPublication;
use Stackflows\Types\EnvironmentType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Stackflows\Clients\Camunda\v7_17\Api\HistoricProcessInstanceApi;
use Stackflows\Clients\Camunda\v7_17\Api\ProcessInstanceApi;

class BusinessProcessInstanceCamundaBridge extends AbstractBridge implements BusinessProcessInstanceBridgeContract,
                                                                             LoggableBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected ProcessInstanceApi $processInstanceApi,
        protected HistoricProcessInstanceApi $historicProcessInstanceApi
    ) {
    }

    protected function transform(array $datum): BusinessProcessInstanceType
    {
        return new BusinessProcessInstanceType([
            'reference'   => $datum['reference'],
            'publication' => BusinessProcessModelPublication::query()
                ->where('engine_diagram_reference', $datum['publication'])
                ->first(),
            'context'     => $datum['context'],
        ]);
    }

    public function get(string $reference): BusinessProcessInstanceType
    {
        $cacheKey = sprintf('bridge.process-instance.%s', $reference);

        $data = Cache::rememberForever($cacheKey, function () use ($reference) {
            $data = $this->processInstanceApi->getProcessInstance($reference);

            return [
                'reference'   => $reference,
                'publication' => $data['definitionId'],
                'context'     => $data['businessKey'],
            ];
        });

        return $this->transform($data);
    }

    public function terminate(string $reference): BusinessProcessInstanceType
    {
        $this->processInstanceApi->deleteProcessInstance($reference);
    }

    public function logs(int $offset = 0, int $limit = 100): Collection
    {
        return new Collection($this->historicProcessInstanceApi->getHistoricProcessInstances(...[
            'sortBy' => 'startTime',
            'sortOrder' => 'desc',
            'firstResult' => $offset,
            'maxResults' => $limit,
        ]));
    }
}
