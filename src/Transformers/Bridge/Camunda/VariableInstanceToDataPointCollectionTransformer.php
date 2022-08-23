<?php

namespace Stackflows\Transformers\Bridge\Camunda;

use Stackflows\DataTransfer\Types\DataAttributeType;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\DataPointType;
use Stackflows\Transformers\BasicTransformerContract;
use Illuminate\Support\Str;
use Stackflows\Clients\Camunda\v7_17\Model\VariableInstanceDto;

class VariableInstanceToDataPointCollectionTransformer implements BasicTransformerContract
{
    public const MAP_ATTRIBUTE_TYPES = [
        'String' => DataAttributeType::TYPE_STRING,
        'Long' => DataAttributeType::TYPE_INTEGER,
        'Double' => DataAttributeType::TYPE_FLOAT,
        'Boolean' => DataAttributeType::TYPE_BOOLEAN,
    ];

    public function convert($data): mixed
    {
        $dataObject = new DataPointCollection();
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $variableInstance) {
                if ($variableInstance instanceof VariableInstanceDto) {
                    $dataObject->put(
                        $variableInstance->getName(),
                        new DataPointType(
                            new DataAttributeType([
                                DataAttributeType::KEY_REFERENCE => $variableInstance->getName(),
                                DataAttributeType::KEY_TYPE => static::MAP_ATTRIBUTE_TYPES[$variableInstance->getType()]
                                    ?? DataAttributeType::TYPE_STRING,
                                DataAttributeType::KEY_LABEL => Str::title($variableInstance->getName()),
                            ]),
                            $variableInstance->getValue()
                        )
                    );
                }
            }
        }

        return $dataObject;
    }
}
