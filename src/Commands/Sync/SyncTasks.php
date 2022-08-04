<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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
        $after = Carbon::now()->startOfWeek()->format('Y-m-d\TH:i:s.vO');

        $size = 10;
        $criteria = [
            'createdAtFrom' => $after,
            'activeOnly' => true,
            'limit' => $size,
        ];
        $index = 0;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');
        while (true) {
            foreach ($synchronizers as $synchronizer) {
                $after = Carbon::now()->format('Y-m-d\TH:i:s.vO');

                $tasks = $stackflows->getUserTasks(
                    $criteria + [
                        'offset' => $index * $size,
                        'activity' => $synchronizer::getActivityName(),
                    ]
                );
                $chunkSize = count($tasks);

                $synchronizer->sync($tasks);

                if ($chunkSize === $size) {
                    $index++;

                    continue;
                }

                $this->output->writeln(
                    sprintf(
                        'Total synchronized tasks: %s. Waiting for new ones...',
                        $tasks->getTotal()
                    )
                );

                $criteria['createdAtFrom'] = $after;
                $index = 0;

                sleep(1);
            }
        }
    }
}
