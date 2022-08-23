<?php

namespace Stackflows\Bridge\Camunda\v7_17;

use Stackflows\Bridge\AbstractBridge;
use Stackflows\Bridge\Camunda\Apis\TaskApiContract;
use Stackflows\Bridge\EventsBridgeContract;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\EventType;
use Stackflows\Types\EnvironmentType;
use Stackflows\Transformers\Bridge\Camunda\DataPointCollectionToVariablesTransformer;
use Stackflows\Clients\Camunda\v7_17\Api\MessageApi;
use Stackflows\Clients\Camunda\v7_17\Api\SignalApi;
use Stackflows\Clients\Camunda\v7_17\Model\CorrelationMessageDto;
use Stackflows\Clients\Camunda\v7_17\Model\SignalDto;

class EventsCamundaBridge extends AbstractBridge implements EventsBridgeContract
{
    public function __construct(
        protected Environment $environment,
        protected MessageApi $messageApi,
        protected SignalApi $signalApi,
        protected DataPointCollectionToVariablesTransformer $dataObjectToVariablesTransformer
    ) {

    }

    public function sendMessage(string $reference, string $context = null, DataPointCollection $submission = null): EventType
    {
        $this->messageApi->deliverMessage(new CorrelationMessageDto([
            'messageName' => $reference,
            'businessKey' => $context,
            'tenantId' => $this->environment->getAttribute('engine_reference'),
            'processVariables' => $this->dataObjectToVariablesTransformer->convert($submission),
        ]));

        return new EventType(['name' => $reference, 'type' => 'message']);
    }

    public function sendSignal(string $reference, DataPointCollection $submission = null): EventType
    {
        $this->signalApi->throwSignal(new SignalDto([
            'name' => $reference,
            'tenantId' => $this->environment->getAttribute('engine_reference'),
            'variables' => $this->dataObjectToVariablesTransformer->convert($submission),
        ]));

        return new EventType(['name' => $reference, 'type' => 'signal']);
    }
}
