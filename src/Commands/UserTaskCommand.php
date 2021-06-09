<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Stackflows\StackflowsPlugin\Stackflows;

class UserTaskCommand extends Command
{
    public $signature = 'stackflows:user-tasks:list';

    public $description = 'Get a list of user task';

    public function handle(Stackflows $client)
    {
        $taskCh = $client->getUserTaskChannel();

        $this->info("Sending...");
        try {
            $tasks = $taskCh->getList();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return;
        }

        $this->info("Successful.");
        dd($tasks);
    }
}
