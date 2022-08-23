<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessDecisionModelDiagramBridgeContract;
use App\Models\BusinessBaseModelDiagram;
use App\Models\BusinessProcessModelPublication;
use Stackflows\Types\EnvironmentType;
use App\Parsers\ModelParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Stackflows\Clients\Camunda\v7_17\Api\DecisionDefinitionApi;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;
use Webpatser\Uuid\Uuid;

class BusinessDecisionModelDiagramCamundaBridge extends AbstractBridge
    implements BusinessDecisionModelDiagramBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected DeploymentApi $deploymentApi,
        protected DecisionDefinitionApi $decisionDefinitionApi
    ) {

    }
}
