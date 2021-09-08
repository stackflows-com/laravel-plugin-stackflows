<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;
use Stackflows\StackflowsPlugin\Tasks\TaskExecutorInterface;
use Stackflows\StackflowsPlugin\Tasks\TaskService;

class ServiceTaskSubscribeCommand extends Command
{
    public $signature = 'stackflows:subscribe:service-tasks';

    public $description = 'Subscribe to service tasks';

    public function handle(Application $app, GatewayClient $client, TaskService $taskService): void
    {
        $executors = $app->tagged('stackflows-external-task');

        if (empty($executors)) {
            $this->error(
                'Stackflows service task executors are not registered. Check the configuration file stackflows.php'
            );

            return;
        }

        $response = $client->authenticateToken(config('stackflows.authToken'));
        if (! isset($response['tenantId'])) {
            $this->error(
                'Stackflows auth token invalid or not set. Check the configuration file stackflows.php'
            );

            return;
        }
        $tenantId = $response['tenantId'];

        /** @var TaskExecutorInterface $executor */
        foreach ($executors as $executor) {
            $tasks = $client->fetchAndLock($tenantId, $executor->getTopic(), $executor->getLockDuration());
            foreach ($tasks as $task) {
                try {
                    $requestObjectClass = $executor->getRequestObjectClass();
                    $requestObject = $taskService->convertToExternalTaskRequest(new $requestObjectClass(), $task);
                    $externalTaskResponse = $executor->execute($requestObject);

                    $client->complete($externalTaskResponse);
                } catch (\Exception $e) {
                    $client->unlock($task['id']);
                }
            }
        }
    }
}
