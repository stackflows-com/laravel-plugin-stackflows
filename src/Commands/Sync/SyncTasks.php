<?php

namespace Stackflows\Commands\Sync;

use App\Exceptions\MissingInheritanceException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
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

        $size = 100;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');

        while (true) {
            $after = clone $nextAfter;
            $nextAfter = Carbon::now();

            foreach ($synchronizers as $synchronizer) {
                $index = 0;
                $successful = 0;

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
                    ->where($taskReflectionModel::getStackflowsActivityKeyName(), static::getActivityName())
                    ->get()
                    ->keyBy($taskReflectionModel::getStackflowsReferenceKeyName());

                $synchronizer->setReflections($taskReflections);

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

                            // Task is still present, so lets remove it from removal list
                            $taskReflectionsToBeRemoved->pull($task->getReference());

                            continue;
                        }

                        $synchronizer->create($task);
                    }

                    $successful += $chunkSize;

                    $index++;
                } while ($chunkSize === $size);

                $synchronizer->remove($taskReflectionsToBeRemoved);

                if ($tasks->getTotal() > 0) {
                    $this->output->writeln(
                        sprintf(
                            '[%s][%s][Completed][%s successful out of %s]',
                            Carbon::now()->toIso8601String(),
                            $synchronizer::getActivityName(),
                            $successful,
                            $tasks->getTotal(),
                        )
                    );
                }
            }

            sleep(1);
        }
    }
}
