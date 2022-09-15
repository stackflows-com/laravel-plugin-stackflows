<?php

namespace Stackflows\Commands\Sync;

use App\Exceptions\MissingInheritanceException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
use Stackflows\Clients\Stackflows\Model\UserTaskType;
use Stackflows\Contracts\StackflowsTaskReflectionContract;
use Stackflows\Contracts\UserTaskSynchronizerContract;
use Stackflows\Stackflows;

class SyncTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stackflows:sync:tasks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Stackflows tasks with local tasks';

    /**
     * @var Stackflows
     */
    protected Stackflows $stackflows;

    /**
     * @var UserTaskSynchronizerContract|null
     */
    protected ?UserTaskSynchronizerContract $synchronizer;

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
        $this->stackflows = $stackflows;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');
        foreach ($synchronizers as $synchronizer) {
            $this->synchronizer = $synchronizer;

            $chunk = $this->getAvailableChunk();
        }
    }

                $commandLock = Cache::lock(
                    sprintf(
                        'stackflows_locking_sync_tasks_%s_%s_to_%s',
                        strtolower($synchronizer::getActivityName()),
                        $after->format('YmdHi'),
                        $nextAfter->format('YmdHi'),
                    ),
                    10 * $size
                );
                if (! $commandLock->get()) {
                    $this->output->writeln(sprintf(
                        '[%s][%s][Locked]',
                        Carbon::now()->toIso8601String(),
                        $synchronizer::getActivityName()
                    ));

                    continue;
                }

            $startOfDay = Carbon::parse($date)->startOfDay();
            $endOfDay = Carbon::parse($date)->endOfDay();

                $taskReflections = $taskReflectionsQuery
                    ->where($synchronizer::getActivityAttributeName(), $synchronizer::getActivityName())
                    ->where($synchronizer::getCreatedAtAttributeName(), '>=', $after)
                    ->get()
                    ->keyBy($synchronizer::getReferenceAttributeName());

            $size = count($tasks) + count($reflections);

                // All task reflections that has not met its task will be removed
                $taskReflectionsToBeRemoved = clone $taskReflections;

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

            return [
                'tasks' => $tasks,
                'reflections' => $reflections,
            ]; //TODO: Retrieve everything for this period
        }

        return [];
    }

                    foreach ($tasks as $task) {
                        if ($taskReflections->has($task->getReference())) {
                            $synchronizer->update($task, $taskReflections->get($task->getReference()));
                            $updated++;

                            // Task is still present, so lets remove it from removal list
                            $taskReflectionsToBeRemoved->pull($task->getReference());

                            continue;
                        }

        Cache::forget(sprintf('%s_period_start', $this->synchronizer::getCachePrefix()));
        Cache::forget(sprintf('%s_period_end', $this->synchronizer::getCachePrefix()));
    }

    /**
     * @return \DatePeriod
     * @throws ApiException
     */
    private function getPeriod(): \DatePeriod
    {
        /** @var UserTaskType $oldestTask */
        $oldestTask = $this->stackflows
            ->getUserTasks(['sort_by' => 'created', 'sort_order' => 'asc', 'limit' => 1])
            ->first();

                $removed = $synchronizer->remove($taskReflectionsToBeRemoved)->count();

                $commandLock->forceRelease();

                if ($tasks->getTotal() > 0) {
                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Completed][Processed: %s][Created: %s][Updated: %s][Removed: %s of %s]',
                            Carbon::now()->toIso8601String(),
                            $synchronizer::getActivityName(),
                            $tasks->getTotal(),
                            $created,
                            $updated,
                            $removed,
                            $taskReflectionsToBeRemoved->count()
                        )
                    );
                }
            }

        $end = Cache::get(
            sprintf('%s_period_end', $this->synchronizer::getCachePrefix()),
            function () use ($newestTask) {
                return max([
                    $this->synchronizer
                        ->getReflectionsQuery()
                        ->orderBy($this->synchronizer::getCreatedAtAttributeName(), 'desc')
                        ->first(),
                    Carbon::parse($newestTask->getCreatedAt()), // Destination end date
                ]);
            }
        );

        return new \DatePeriod($start, new \DateInterval('P1D'), $end);
    }
}
