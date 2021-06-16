<?php

namespace Stackflows\StackflowsPlugin\Channels;

use DateTime;
use Stackflows\GatewayApi\Api\UserTaskApi;
use Stackflows\GatewayApi\ApiException;
use Stackflows\GatewayApi\Model\CompleteUserTaskRequest;
use Stackflows\GatewayApi\Model\UserTask;
use Stackflows\StackflowsPlugin\Configuration;

class UserTaskChannel
{
    private UserTaskApi $api;
    private Configuration $conf;

    public function __construct(UserTaskApi $api, Configuration $conf)
    {
        $this->api = $api;
        $this->conf = $conf;
    }

    /**
     * Get the user's task list.
     *
     * @param DateTime|null $createdAfter
     * @return UserTask[]
     * @throws ApiException
     */
    public function getList(DateTime $createdAfter = null): array
    {
        $after = $createdAfter?->format(\DateTimeInterface::ISO8601);
        return $this->api->getList($this->conf->getEngine(), $after);
    }

    /**
     * Complete the user's task.
     *
     * @param string $id Task ID.
     * @throws ApiException
     */
    public function complete(string $id)
    {
        $request = new CompleteUserTaskRequest(['engine' => $this->conf->getEngine()]);
        $this->api->completeTask($id, $request);
    }

    /**
     * @return UserTaskApi
     */
    public function getApi(): UserTaskApi
    {
        return $this->api;
    }
}
