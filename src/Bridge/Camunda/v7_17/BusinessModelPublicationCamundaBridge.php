<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\BusinessModelPublicationBridgeContract;
use App\Models\BusinessBaseModelPublication;
use Stackflows\Types\EnvironmentType;
use Stackflows\Clients\Camunda\v7_17\Api\DeploymentApi;
use Stackflows\Clients\Camunda\v7_17\ApiException;

class BusinessModelPublicationCamundaBridge extends AbstractBridge
    implements BusinessModelPublicationBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected DeploymentApi $deploymentApi
    ) {

    }

    public function delete(BusinessBaseModelPublication $publication): BusinessBaseModelPublication
    {
        try {
            $deployment = $this->deploymentApi->getDeployment(
                $publication->getAttribute('engine_deployment_reference')
            );
        } catch (ApiException $apiException) {
            // Publication does not exist, we do not have to do anything here, just return
            return $publication;
        }

        $this->deploymentApi->deleteDeployment($deployment->getId(), true);

        return $publication;
    }
}
