<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
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
        $nextAfter = Carbon::now()->startOfWeek()->format('Y-m-d\TH:i:s.vO');

        $size = 10;
        $index = 0;

        /** @var UserTaskSynchronizerContract[] $synchronizers */
        $synchronizers = app()->tagged('stackflows:user-tasks-synchronizer');

        $this->output->writeln('Starting synchronization process');

        while (true) {
            $after = $nextAfter;
            $nextAfter = Carbon::now()->format('Y-m-d\TH:i:s.vO');

            foreach ($synchronizers as $synchronizer) {
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

                    $this->output->error(sprintf(
                        "%s%s%s",
                        $data['message'] ?? 'None',
                        PHP_EOL,
                        json_encode($context, JSON_PRETTY_PRINT)
                    ));

                    continue;
                }

                $chunkSize = count($tasks);

                $synchronizer->sync($tasks);

                if ($chunkSize === $size) {
                    $index++;

                    continue;
                }

                if ($tasks->getTotal() > 0) {
                    $this->output->writeln(
                        sprintf(
                            'Total tasks processes: %s. Waiting for new ones...',
                            $tasks->getTotal()
                        )
                    );
                }

                $index = 0;
            }

            sleep(1);
        }
    }
}
