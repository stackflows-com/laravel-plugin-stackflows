<?php

namespace Stackflows\StackflowsPlugin\Actions;

use Stackflows\StackflowsPlugin\Exceptions\InvalidCredentials;
use Stackflows\StackflowsPlugin\Stackflows;

class StartProcessAction
{
    private Stackflows $stackflows;

    public function __construct(Stackflows $stackflows)
    {
        $this->stackflows = $stackflows;
    }

    /**
     * @param string $name
     * @param Variable[]|null $variables
     * @throws InvalidCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Stackflows\GatewayApi\ApiException
     */
    public function execute(string $name, array $variables = null): void
    {
//        $processApi = $this->stackflows->getProcessChannel();
//        $processApi->start($name, $variables);
    }
}
