<?php

namespace Stackflows\Commands\BusinessProcesses;

use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksLockRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksServeRequest;
use Stackflows\Clients\Stackflows\Model\PostEnvironmentServiceTasksUnlockRequest;
use function config;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\BusinessProcesses\ServiceTasks\Inputs\ServiceTaskInputInterface;
use Stackflows\BusinessProcesses\ServiceTasks\ServiceTaskExecutorInterface;

class ExecuteServiceTasks extends Command
{
    public $signature = 'stackflows:business-processes:execute-service-tasks';

    public $description = 'This command will start executing business processes service tasks endlessly';

    public function handle(
        Application $app,
        EnvironmentApi $environmentApi
    ): void {
        /** @var ServiceTaskExecutorInterface[] $executors */
        $executors = $app->tagged('stackflows:business-process:service-task');

        if (empty($executors)) {
            $this->error('There are no executors registered at this moment');

            return;
        }

        $lock = config('app.key');
        foreach ($executors as $executor) {
            $tasks = $environmentApi->postEnvironmentServiceTasksLock(new PostEnvironmentServiceTasksLockRequest([
                'lock' => $lock,
                'topic' => $executor->getTopic(),
                'duration' => $executor->getLockDuration(),
            ]));
            foreach ($tasks as $task) {
                try {
                    $inputClass = $executor->getInputClass();
                    /** @var ServiceTaskInputInterface $input */
                    $input = new $inputClass($task);

                    $output = $executor->execute($input);
                    if (! $output) {
                        continue;
                    }

                    $environmentApi->postEnvironmentServiceTasksServe(
                        $task->reference,
                        new PostEnvironmentServiceTasksServeRequest([
                            'workerId' => $lock,
                            'variables' => $output,
                        ])
                    );
                } catch (\Exception $e) {
                    $environmentApi->postEnvironmentServiceTasksUnlock(
                        $task->reference,
                        new PostEnvironmentServiceTasksUnlockRequest()
                    );

                    // TODO: Fix this, execution process should not be halted because of one faulty node
                    throw new \Exception($e);
                }
            }
        }
    }
}
