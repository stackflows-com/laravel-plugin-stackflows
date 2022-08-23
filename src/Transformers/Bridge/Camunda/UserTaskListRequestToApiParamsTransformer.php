<?php

namespace Stackflows\Transformers\Bridge\Camunda;

use Stackflows\Transformers\BasicTransformerContract;

class UserTaskListRequestToApiParamsTransformer implements BasicTransformerContract
{
    public function convert($data): mixed
    {
        $result = [];

        if (isset($data['activity'])) {
            $result['taskDefinitionKey'] = $data['activity'];
        }

        if (isset($data['created_at_from'])) {
            $result['createdAfter'] = $data['created_at_from']->format('Y-m-d\TH:i:s.vO');
        }

        if (isset($data['created_at_till'])) {
            $result['createdBefore'] = $data['created_at_till']->format('Y-m-d\TH:i:s.vO');
        }

        if (isset($data['active_only'])) {
            $result['active'] = $data['active_only'];
        }

        if (isset($data['offset'])) {
            $result['firstResult'] = $data['offset'];
        }

        if (isset($data['limit'])) {
            $result['maxResults'] = $data['limit'];
        }

        return $result;
    }
}
