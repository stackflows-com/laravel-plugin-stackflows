<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessProcessModelDiagramBridgeContract;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;

class BusinessProcessModelDiagramCamundaBridge extends AbstractBridge implements BusinessProcessModelDiagramBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected DeploymentApi $deploymentApi
    ) {
    }
}
