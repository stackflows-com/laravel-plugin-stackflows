<?php

namespace Stackflows\Commands\BusinessProcesses;

use function config;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\BusinessProcesses\ServiceTasks\Inputs\ServiceTaskInputInterface;
use Stackflows\BusinessProcesses\ServiceTasks\ServiceTaskExecutorInterface;
use Stackflows\BusinessProcesses\Types\ServiceTaskType;
use Stackflows\Http\Client\StackflowsDirectCamundaClient;

class ExecuteServiceTasksCommand extends Command
{
    public $signature = 'stackflows:business-processes:execute-service-tasks';

    public $description = 'This command will start executing business processes service tasks endlessly';

    public function handle(
        Application $app,
        StackflowsDirectCamundaClient $client
    ): void {
        /** @var ServiceTaskExecutorInterface[] $executors */
        $executors = $app->tagged('stackflows:business-process:service-task');

        if (empty($executors)) {
            $this->error('There are no executors registered at this moment');

            return;
        }

        $workerId = config('app.key');

        foreach ($executors as $executor) {
            $tasks = $client->fetchAndLock($executor->getTopic(), $executor->getLockDuration(), $workerId);
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

                    $client->completeExternalTask($serviceTask->getReference(), $workerId, $output);
                } catch (\Exception $e) {
                    $client->unlock($serviceTask->getReference());

                    // TODO: Fix this, execution process should not be halted because of one faulty node
                    throw new \Exception($e);
                }
            }
        }
    }
}
