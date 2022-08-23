<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessDecisionInstanceBridgeContract;
use Stackflows\Bridge\LoggableBridgeContract;
use Stackflows\Types\EnvironmentType;
use Illuminate\Support\Collection;
use Stackflows\Clients\Camunda\v7_17\Api\HistoricDecisionInstanceApi;

class BusinessDecisionInstanceCamundaBridge extends AbstractBridge implements BusinessDecisionInstanceBridgeContract,
                                                                              LoggableBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected HistoricDecisionInstanceApi $historicDecisionInstanceApi,
    ) {
    }

    public function logs(int $offset = 0, int $limit = 100): Collection
    {
        return new Collection($this->historicDecisionInstanceApi->getHistoricDecisionInstances(...[
            'sortBy' => 'evaluationTime',
            'sortOrder' => 'desc',
            'firstResult' => $offset,
            'maxResults' => $limit,
        ]));
    }
}
