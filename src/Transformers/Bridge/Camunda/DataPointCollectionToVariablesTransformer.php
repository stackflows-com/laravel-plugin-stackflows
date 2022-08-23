<?php

namespace Stackflows\Transformers\Bridge\Camunda;

use Stackflows\Clients\Camunda\v7_17\Model\VariableValueDto;
use Stackflows\DataTransfer\Collections\DataPointCollection;
use Stackflows\DataTransfer\Types\DataAttributeType;
use Stackflows\DataTransfer\Types\DataPointType;

class DataPointCollectionToVariablesTransformer
{
    public const MAP_ATTRIBUTE_TYPES = [
        DataAttributeType::TYPE_STRING => 'String',
        DataAttributeType::TYPE_INTEGER => 'Long',
        DataAttributeType::TYPE_FLOAT => 'Double',
        DataAttributeType::TYPE_BOOLEAN => 'Boolean',
        DataAttributeType::TYPE_OBJECT => 'Object',
    ];

    public function convert($data, bool $form = false): mixed
    {
        $mapTypes = static::MAP_ATTRIBUTE_TYPES;
        if ($form) {
            $mapTypes[DataAttributeType::TYPE_FLOAT] = 'String';
        }

        $variables = null;
        if ($data instanceof DataPointCollection && ! $data->isEmpty()) {
            /** @var DataPointType $dataPoint */
            foreach ($data->all() as $dataPoint) {
                $variables[$dataPoint->attribute->getReference()] = new VariableValueDto([
                    'value' => $dataPoint->value,
                ]);

                if (isset($mapTypes[$dataPoint->attribute->getType()])) {
                    $variables[$dataPoint->attribute->getReference()]->setType(
                        $mapTypes[$dataPoint->attribute->getType()]
                    );
                }
            }
        }

        return $variables;
    }
}
