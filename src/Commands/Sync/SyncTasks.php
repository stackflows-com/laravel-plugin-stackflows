<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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

        $tasks = $stackflows->getUserTasks($criteria);
        $totalCount = $tasks->getTotal();
        $bar = $this->output->createProgressBar($tasks->getTotal());
        while (true) {
            $after = Carbon::now()->format('Y-m-d\TH:i:s.vO');

            $tasks = $stackflows->getUserTasks(
                $criteria + [
                    'offset' => $index * $size,
                ]
            );
            $chunkSize = count($tasks);

            if ($totalCount === 0 && $chunkSize > 0) {
                $totalCount = $tasks->getTotal();
                $bar->start($totalCount);
            }

            $synchronizers = app()->tagged('stackflows:tasks-synchronizer');
            foreach ($synchronizers as $synchronizer) {
                $synchronizer::sync($tasks, $criteria);
            }

            $bar->advance($chunkSize);

            if ($chunkSize === $size) {
                $index++;

                continue;
            }

            $this->output->writeln(
                sprintf(
                    'Total synchronized tasks: %s. Waiting for new ones...',
                    $totalCount
                )
            );

            $criteria['createdAtFrom'] = $after;
            $index = 0;
            $totalCount = 0;

            $bar->finish();
            $bar->clear();

            sleep(1);
        }
    }
}
