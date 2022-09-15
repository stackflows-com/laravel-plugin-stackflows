<?php

namespace Stackflows\Commands\Sync;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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

    private function getAvailableChunk(): array
    {
        if ($this->synchronizer === null) {
            return [];
        }

        foreach ($this->getPeriod() as $date) {
            $lock = Cache::lock(
                sprintf('%s_for_period_%s', $this->synchronizer::getCachePrefix(), $date->format('Ymd')),
                360
            );
            if ($lock->get()) {
                continue;
            }

            $startOfDay = Carbon::parse($date)->startOfDay();
            $endOfDay = Carbon::parse($date)->endOfDay();

            $tasks = $this->stackflows->getUserTasks();
            $reflections = [];

            $size = count($tasks) + count($reflections);

            if ($size === 0) {
                $this->forgetPeriod();

                continue;
            }

            return [
                'tasks' => $tasks,
                'reflections' => $reflections,
            ]; //TODO: Retrieve everything for this period
        }

        return [];
    }

    private function forgetPeriod(): void
    {
        if ($this->synchronizer === null) {
            return;
        }

        Cache::forget(sprintf('%s_period_start', $this->synchronizer::getCachePrefix()));
        Cache::forget(sprintf('%s_period_end', $this->synchronizer::getCachePrefix()));
    }

    /**
     * @return \DatePeriod|\DateTime[]
     */
    private function getPeriod(): \DatePeriod
    {
        $start = Cache::get(sprintf('%s_period_start', $this->synchronizer::getCachePrefix()), function () {
            return min([
                $this->synchronizer
                    ->getReflectionsQuery()
                    ->orderBy($this->synchronizer::getCreatedAtAttributeName(), 'asc')
                    ->first(),
                $this->stackflows->getUserTasks(), // Destination start date
            ]);
        });

        $end = Cache::get(sprintf('%s_period_end', $this->synchronizer::getCachePrefix()), function () {
            return max([
                $this->synchronizer
                    ->getReflectionsQuery()
                    ->orderBy($this->synchronizer::getCreatedAtAttributeName(), 'desc')
                    ->first(),
                $this->stackflows, // Destination end date
            ]);
        });

        return new \DatePeriod($start, new \DateInterval('P1D'), $end);
    }
}
