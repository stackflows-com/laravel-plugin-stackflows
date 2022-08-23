<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Stackflows\Clients\Stackflows\ApiException;
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
     * @return mixed
     */
    public function handle(Stackflows $stackflows)
    {
        $nextAfter = Carbon::now()->subDays(7)->format('Y-m-d\TH:i:s.vO');

        $size = 100;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');

        while (true) {
            $after = $nextAfter;
            $nextAfter = Carbon::now()->format('Y-m-d\TH:i:s.vO');

            foreach ($synchronizers as $synchronizer) {
                $index = 0;
                $successful = 0;

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
                            'createdAtFrom' => $after,
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

                    $synchronizer->sync($tasks);

                    $successful += $chunkSize;

                    $index++;
                } while ($chunkSize === $size);

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

                $index = 0;
            }

            sleep(1);
        }
    }
}
