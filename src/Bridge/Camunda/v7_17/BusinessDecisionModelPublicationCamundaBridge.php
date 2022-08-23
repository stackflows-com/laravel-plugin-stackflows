<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use App\Models\BusinessDecisionModelPublication;
use Stackflows\Bridge\BusinessDecisionModelPublicationBridgeContract;
use Stackflows\Clients\Camunda\v7_17\Api\DecisionDefinitionApi;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;
use Stackflows\Clients\Camunda\v7_17\Model\EvaluateDecisionDto;
use Stackflows\Clients\Camunda\v7_17\Model\VariableValueDto;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\BusinessDecisionScoreType;
use Stackflows\DataTransfer\Types\DataPointType;

class BusinessDecisionModelPublicationCamundaBridge extends BusinessModelPublicationCamundaBridge implements BusinessDecisionModelPublicationBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected DeploymentApi $deploymentApi,
        protected DecisionDefinitionApi $decisionDefinitionApi
    ) {
    }

    public function evaluate(
        BusinessDecisionModelPublication $publication,
        DataPointCollection $submission = null
    ): BusinessDecisionScoreType {
        $variables = null;
        if ($submission) {
            $variables = $submission->getPoints()->map(function (DataPointType $dataPoint) {
                return new VariableValueDto([
                    'type' => $dataPoint->attribute,
                    'value' => $dataPoint->value,
                ]);
            });
        }

        $score = $this->decisionDefinitionApi->evaluateDecisionByKeyAndTenant(
            $publication->getAttribute('engine_model_reference'),
            $publication->environment->getAttribute('engine_reference'),
            new EvaluateDecisionDto(['variables' => $variables])
        );

        return new BusinessDecisionScoreType([
            'publication' => $publication,
            'score' => $score,
        ]);
    }
}
