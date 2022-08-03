<?php

namespace Stackflows\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
use Stackflows\Contracts\ServiceTaskExecutorInterface;
use Stackflows\Exceptions\ExecutorException;
use Stackflows\Stackflows;

class Serve extends Command
{
    public $signature = 'stackflows:serve {topic? : Serve a specific topic}';

    public $description = 'This command will start executing business processes service tasks endlessly';

    /**
     * @param Stackflows $stackflows
     * @return void
     * @throws \Exception
     */
    public function handle(Stackflows $stackflows): void
    {
        $topic = $this->input->getArgument('topic');

        /** @var ServiceTaskExecutorInterface[] $executors */
        $executors = app()->tagged('stackflows:executor');

        if (empty($executors)) {
            $this->error('There are no executors registered at this moment');

            return;
        }

        $lock = config('app.key');

        while (true) {
            foreach ($executors as $executor) {
                // Skip if topic does not match the one that was provider via argument
                if ($topic && $executor::getTopic() !== $topic) {
                    continue;
                }

                try {
                    $tasks = $stackflows->lockServiceTasks($lock, $executor::getTopic(), $executor::getLockDuration());
                } catch (ApiException $e) {
                    $this->output->error($e->getMessage());
                } catch (\Exception $e) {
                    $this->output->error($e->getMessage());
                }

                $served = 0;
                foreach ($tasks as $task) {
                    try {
                        $submission = $executor->execute($task);
                        if ($submission === null) {
                            continue;
                        }

                        $stackflows->serveServiceTask($task->getReference(), $lock, $submission);
                        $served++;
                    } catch (ExecutorException $e) {
                        $stackflows->unlockServiceTask($task->getReference(), $lock);

                        Log::warning(
                            $e->getMessage(),
                            ['service_task' => $task, 'executor' => $executor] + $e->getContext()
                        );

                        $this->output->warning(sprintf(
                            "%s%s%s",
                            $e->getMessage(),
                            PHP_EOL,
                            json_encode($e->getContext(), JSON_PRETTY_PRINT)
                        ));
                    }
                }

                $this->output->success(sprintf(
                    'Successfully executed %s out of %s service tasks',
                    $served,
                    count($tasks)
                ));
            }

            sleep(1);
        }
    }
}
