<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\ActivityBridgeContract;
use Stackflows\Bridge\LoggableBridgeContract;
use Stackflows\Types\EnvironmentType;
use Illuminate\Support\Collection;
use Stackflows\Clients\Camunda\v7_17\Api\HistoricActivityInstanceApi;

class ActivityCamundaBridge extends AbstractBridge implements ActivityBridgeContract, LoggableBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected HistoricActivityInstanceApi $historicActivityInstanceApi
    ) {
    }

    public function logs(int $offset = 0, int $limit = 100): Collection
    {
        return new Collection($this->historicActivityInstanceApi->getHistoricActivityInstances(...[
            'sortBy' => 'startTime',
            'sortOrder' => 'desc',
            'firstResult' => $offset,
            'maxResults' => $limit,
        ]));
    }
}
