<?php

namespace Stackflows\Commands\BusinessProcesses;

use Stackflows\Http\Client\StackflowsClient;
use function config;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\BusinessProcesses\ServiceTasks\Inputs\ServiceTaskInputInterface;
use Stackflows\BusinessProcesses\ServiceTasks\ServiceTaskExecutorInterface;
use Stackflows\BusinessProcesses\Types\ServiceTaskType;

class ExecuteServiceTasks extends Command
{
    public $signature = 'stackflows:business-processes:execute-service-tasks';

    public $description = 'This command will start executing business processes service tasks endlessly';

    public function handle(
        Application $app,
        StackflowsClient $client
    ): void {
        /** @var ServiceTaskExecutorInterface[] $executors */
        $executors = $app->tagged('stackflows:business-process:service-task');

        if (empty($executors)) {
            $this->error('There are no executors registered at this moment');

            return;
        }

        $lock = config('app.key');
        foreach ($executors as $executor) {
            $tasks = $client->lockServiceTasks($lock, $executor->getTopic(), $executor->getLockDuration());
            foreach ($tasks as $task) {
                $serviceTask = new ServiceTaskType($task['id'], $task['topicName'], $task['priority']);

                try {
                    $inputClass = $executor->getInputClass();
                    /** @var ServiceTaskInputInterface $input */
                    $input = new $inputClass($serviceTask);

                    $output = $executor->execute($input);
                    if (! $output) {
                        continue;
                    }

                    $client->serveServiceTask($lock, $serviceTask->getReference(), $output);
                } catch (\Exception $e) {
                    $client->unlockServiceTask($serviceTask->getReference());

                    // TODO: Fix this, execution process should not be halted because of one faulty node
                    throw new \Exception($e);
                }
            }
        }
    }
}
