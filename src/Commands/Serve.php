<?php

namespace Stackflows\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
use Stackflows\Contracts\ServiceTaskExecutorInterface;
use Stackflows\Exceptions\ExecutorException;
use Stackflows\Stackflows;

class Serve extends Command
{
    public $signature = 'stackflows:serve {chunk=10 : Single chunk size} {topic? : Serve a specific topic} {--once : Run only once}';

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

        $lock = uniqid();

        while (true) {
            foreach ($executors as $executor) {
                // Skip if topic does not match the one that was provider via argument
                if ($topic && $executor::getTopic() !== $topic) {
                    continue;
                }

                $this->output->writeln(
                    sprintf(
                        '[%s][%s][Fetching]',
                        Carbon::now()->toIso8601String(),
                        $executor::getTopic()
                    )
                );

                $commandLock = Cache::lock(
                    sprintf('stackflows_locking_service_task_%s', strtolower($executor::getTopic())),
                    60
                );
                if (! $commandLock->get()) {
                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Locked]',
                            Carbon::now()->toIso8601String(),
                            $executor::getTopic()
                        )
                    );

                    continue;
                }

                try {
                    $tasks = $stackflows->lockServiceTasks(
                        $lock,
                        $executor::getTopic(),
                        $executor::getLockDuration(),
                        $this->input->getArgument('chunk')
                    );

                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Serving][%s]',
                            Carbon::now()->toIso8601String(),
                            $executor::getTopic(),
                            count($tasks)
                        )
                    );
                } catch (\Exception $e) {
                    if ($this->output->isDebug()) {
                        $this->output->error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
                    }

                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Failed][%s]',
                            Carbon::now()->toIso8601String(),
                            $executor::getTopic(),
                            count($tasks)
                        )
                    );

                    continue;
                } finally {
                    $commandLock->forceRelease();
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
                    } catch (ExecutorException | ApiException $e) {
                        $message = $e->getMessage();
                        $context = [
                            'service_task' => $task,
                            'executor' => get_class($executor),
                            'submission' => $submission ?? null,
                        ];
                        if ($e instanceof ExecutorException) {
                            $context += $e->getContext();
                        } elseif ($e instanceof ApiException) {
                            $message = 'Stackflows responded with an error';
                            $context['error'] = json_decode($e->getResponseBody(), true);
                        }

                        Log::error($message, $context);

                        if ($this->output->isDebug()) {
                            $this->output->error(sprintf(
                                "%s%s%s",
                                $message,
                                PHP_EOL,
                                json_encode($context, JSON_PRETTY_PRINT)
                            ));
                        }
                    }
                }

                $this->output->writeln(
                    sprintf(
                        '[%s][%s][Served][%s successful out of %s]',
                        Carbon::now()->toIso8601String(),
                        $executor::getTopic(),
                        $served,
                        count($tasks)
                    )
                );
            }

            if ($this->option('once')) {
                break;
            }

            sleep(1);
        }
    }
}
