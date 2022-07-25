<?php

namespace Stackflows\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stackflows\BusinessProcesses\ServiceTasks\ServiceTaskExecutorInterface;
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
        $executors = app()->tagged('stackflows:business-process:service-task');

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
                    if (! $submission) {
                        continue;
                    }

                    $stackflows->serveServiceTask($task->reference, $lock, $submission);
                } catch (\Exception $e) {
                    $stackflows->unlockServiceTask($task->reference, $lock);

                    Log::warning($e->getMessage(), ['executor' => $executor]);
                }
            }
        }
    }
}
