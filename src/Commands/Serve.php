<?php

namespace Stackflows\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stackflows\BusinessProcesses\ServiceTasks\ServiceTaskExecutorInterface;
use Stackflows\Exceptions\ExecutorException;
use Stackflows\Stackflows;

class Serve extends Command
{
    public $signature = 'stackflows:serve';

    public $description = 'This command will start executing business processes service tasks endlessly';

    /**
     * @param Stackflows $stackflows
     * @return void
     * @throws \Exception
     */
    public function handle(Stackflows $stackflows): void
    {
        /** @var ServiceTaskExecutorInterface[] $executors */
        $executors = app()->tagged('stackflows:executor');

        if (empty($executors)) {
            $this->error('There are no executors registered at this moment');

            return;
        }

        $lock = config('app.key');
        foreach ($executors as $executor) {
            $tasks = $stackflows->lockServiceTasks($lock, $executor::getTopic(), $executor::getLockDuration());
            foreach ($tasks as $task) {
                try {
                    $submission = $executor->execute($task);
                    if ($submission === null) {
                        continue;
                    }

                    $stackflows->serveServiceTask($task->reference, $lock, $submission);
                } catch (ExecutorException $e) {
                    $stackflows->unlockServiceTask($task->reference, $lock);

                    Log::warning(
                        $e->getMessage(),
                        ['service_task' => $task, 'executor' => $executor] + $e->getContext()
                    );
                }
            }
        }
    }
}
