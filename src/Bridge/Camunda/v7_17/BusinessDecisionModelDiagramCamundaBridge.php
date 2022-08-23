<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessDecisionModelDiagramBridgeContract;
use Stackflows\Clients\Camunda\v7_17\Api\DecisionDefinitionApi;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;

class BusinessDecisionModelDiagramCamundaBridge extends AbstractBridge implements BusinessDecisionModelDiagramBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected DeploymentApi $deploymentApi,
        protected DecisionDefinitionApi $decisionDefinitionApi
    ) {
    }
}
