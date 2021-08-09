<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Stackflows\GatewayApi\Model\Variable;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Stackflows;

class SignalThrowCommand extends Command
{
    public $signature = 'stackflows:signals:throw
        {name : The signal name}
        {--var=* : Variable in the format "name:value"}';

    public $description = 'Throw a Signal';

    public function handle(Stackflows $client)
    {
        $name = $this->argument('name');
        $vars = $this->option('var');
        $signal = $client->getSignalChannel();

        $this->authenticate($client->getAuth());

        try {
            $variables = $this->convertVariables($vars);
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->info("Sending...");

        try {
            $signal->throw($name, $variables);
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->info("Successful.");
    }

    /**
     * @param array $variables
     * @return Variable[]
     */
    private function convertVariables(array $variables): array
    {
        $result = [];
        foreach ($variables as $var) {
            $data = explode(':', $var);
            if (count($data) !== 2) {
                throw new \InvalidArgumentException('The variable must be in the format "name:value"');
            }
            $v = new Variable();
            $v->setName($data[0]);
            $v->setValue((object)[$data[1]]);
            $v->setType('String');
            $result[] = $v;
        }

        return $result;
    }

    private function authenticate(BackofficeAuth $auth)
    {
        if ($auth->check()) {
            return;
        }

        $this->info('Attempt to authenticate in the Backoffice...');
        if ($auth->attempt(config('stackflows.email'), config('stackflows.password'))) {
            $this->info("Successful authentication.");
            return;
        }

        $this->error('The authentication is failed. Please check credentials.');
        exit(1);
    }
}
