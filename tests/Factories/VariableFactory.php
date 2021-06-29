<?php

namespace Stackflows\StackflowsPlugin\Tests\Factories;

use Stackflows\GatewayApi\Model\Variable;

class VariableFactory
{
    public static function new(): self
    {
        return new self();
    }

    public function make(array $extra): Variable
    {
        $data = array_merge(
            [
                'name' => 'Status',
                'value' => 'not reviewed',
                'type' => 'String',
            ],
            $extra
        );

        $var = new Variable(['name' => $data['name'], 'type' => $data['type']]);

        if (! is_null($data['value'])) {
            $var->setValue((object)[$data['value']]);
        }

        return $var;
    }
}
