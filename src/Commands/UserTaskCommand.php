<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Stackflows;

class UserTaskCommand extends Command
{
    public $signature = 'stackflows:user-tasks:list';

    public $description = 'Get a list of user task';

    public function handle(Stackflows $client)
    {
        $this->authenticate($client->getAuth());
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

    private function authenticate(BackofficeAuth $auth)
    {
        if ($auth->check()) {
            return;
        }

        $this->info('Attempt to authenticate in the Backoffice...');
        if ($auth->attempt(config('stackflows.email'), config('stackflows.password'))) {
            $this->info("Successful authentication.");
            return;
        }

        $this->error('The authentication is failed. Please check credentials.');
        exit(1);
    }
}
