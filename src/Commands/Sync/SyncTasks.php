<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
use Stackflows\Contracts\StackflowsTaskReflectionContract;
use Stackflows\Contracts\UserTaskSynchronizerContract;
use Stackflows\Exceptions\MissingInheritanceException;
use Stackflows\Stackflows;

class SyncTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stackflows:sync:tasks {--max-time=0 : The maximum number of seconds the service should run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stackflows tasks with local tasks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Stackflows $stackflows
     * @return void
     * @throws MissingInheritanceException
     */
    public function handle(Stackflows $stackflows)
    {
        $nextAfter = Carbon::now()->subDays(7);
        $startTime = Carbon::now()->timestamp;

        $size = 100;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');

        while (true) {
            $after = clone $nextAfter;
            $nextAfter = Carbon::now();

            foreach ($synchronizers as $synchronizer) {
                $index = 0;
                $created = 0;
                $updated = 0;

                $commandLock = Cache::lock(
                    sprintf(
                        'stackflows_locking_sync_tasks_%s_%s_to_%s',
                        strtolower($synchronizer::getActivityName()),
                        $after->format('YmdHi'),
                        $nextAfter->format('YmdHi'),
                    ),
                    2 * $size
                );
                if (! $commandLock->get()) {
                    $this->output->writeln(sprintf(
                        '[%s][%s][Locked]',
                        Carbon::now()->toIso8601String(),
                        $synchronizer::getActivityName()
                    ));

                    continue;
                }

                $synchronizer->setReferenceTimestamp($nextAfter);

                $taskReflectionsQuery = $synchronizer->getReflectionsQuery();
                $taskReflectionModel = $taskReflectionsQuery->getModel();
                if (! $taskReflectionModel instanceof StackflowsTaskReflectionContract) {
                    throw new MissingInheritanceException(
                        get_class($taskReflectionModel),
                        StackflowsTaskReflectionContract::class
                    );
                }

                $taskReflections = $taskReflectionsQuery
                    ->where($synchronizer::getActivityAttributeName(), $synchronizer::getActivityName())
                    ->get()
                    ->keyBy($synchronizer::getReferenceAttributeName());

                $synchronizer->setReflections($taskReflections);

                do {
                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Fetching]',
                            Carbon::now()->toIso8601String(),
                            $synchronizer::getActivityName(),
                        )
                    );

                    try {
                        $criteria = [
                            'createdAtFrom' => $after->format('Y-m-d\TH:i:s.vO'),
                            'activeOnly' => true,
                            'limit' => $size,
                            'offset' => $index * $size,
                            'activity' => $synchronizer::getActivityName(),
                        ];

                        $tasks = $stackflows->getUserTasks($criteria);
                    } catch (ApiException $e) {
                        $data = json_decode($e->getResponseBody(), true);
                        $context = [
                            'synchronizer' => get_class($synchronizer),
                            'criteria' => $criteria,
                        ];

                        Log::error($data['message'] ?? 'None', $context);

                        $commandLock->forceRelease();

                        continue;
                    }

                    $chunkSize = count($tasks);

                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Chunk][%s/%s]',
                            Carbon::now()->toIso8601String(),
                            $synchronizer::getActivityName(),
                            $chunkSize,
                            $tasks->getTotal()
                        )
                    );

                    foreach ($tasks as $task) {
                        if ($taskReflections->has($task->getReference())) {
                            $synchronizer->update($task, $taskReflections->get($task->getReference()));
                            $updated++;

                            continue;
                        }

                        $synchronizer->create($task);
                        $created++;
                    }

                    $index++;
                } while ($chunkSize === $size);

                $commandLock->forceRelease();

                if ($tasks->getTotal() > 0) {
                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Completed][Processed: %s][Created: %s][Updated: %s]',
                            Carbon::now()->toIso8601String(),
                            $synchronizer::getActivityName(),
                            $tasks->getTotal(),
                            $created,
                            $updated
                        )
                    );
                }
            }

            if ((int)$this->option('max-time') !== 0 && $this->option('max-time') <= (Carbon::now()->timestamp - $startTime)) {
                $this->output->writeln(
                    sprintf(
                        'Service is restarting after %s seconds.',
                        (Carbon::now()->timestamp - $startTime)
                    )
                );
                break;
            }


            sleep(1);
        }
    }
}
