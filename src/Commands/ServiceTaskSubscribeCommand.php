<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Bpmn\Requests\ExternalTaskRequestInterface;
use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;
use Stackflows\StackflowsPlugin\Tasks\TaskExecutorInterface;

class ServiceTaskSubscribeCommand extends Command
{
    public $signature = 'stackflows:subscribe:service-tasks';

    public $description = 'Subscribe to service tasks';

    public function handle(Application $app, GatewayClient $client): void
    {
        $executors = $app->tagged('stackflows-external-task');

        if (empty($executors)) {
            $this->error(
                'Stackflows service task executors are not registered. Check the configuration file stackflows.php'
            );

            return;
        }

        // TODO Auth
        // Here should be a request to the gateway by the configured auth token, and get tenant id for camunda
        $tenantId = 'bt'; //temporary set for everything bt tenant id

        /** @var TaskExecutorInterface $executor */
        foreach ($executors as $executor) {
            $tasks = $client->fetchAndLock($tenantId, $executor->getTopic(), $executor->getLockDuration());
            foreach ($tasks as $task) {
                try {
                    $result = $executor->execute($task);
                    print_r($result);
                } catch (\Exception $e) {
                    $client->unlock($task);
                }
            }
        }
    }

    public function castTaskToObject($task): ExternalTaskRequestInterface
    {

    }
}
