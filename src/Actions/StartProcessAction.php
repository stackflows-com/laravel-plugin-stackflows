<?php

namespace Stackflows\StackflowsPlugin\Actions;

use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
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
        $this->authenticate($this->stackflows->getAuth());

        $processApi = $this->stackflows->getProcessChannel();
        $processApi->start($name, $variables);
    }

    /**
     * @throws InvalidCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authenticate(BackofficeAuth $auth)
    {
        if ($auth->check()) {
            return;
        }

        if ($auth->attempt(config('stackflows.email'), config('stackflows.password'))) {
            return;
        }

        throw InvalidCredentials::emailOrPassword();
    }
}
