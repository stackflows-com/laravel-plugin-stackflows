<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Stackflows;
use Stackflows\StackflowsPlugin\Tasks\TaskExecutorInterface;

class ServiceTaskSubscribeCommand extends Command
{
    public $signature = 'stackflows:subscribe:service-tasks';

    public $description = 'Subscribe to service tasks';

    public function handle(Application $app, Stackflows $client): void
    {
        $executors = $app->tagged('stackflows-service-task');

        if (empty($executors)) {
            $this->error(
                'Stackflows service task executors are not registered. Check the configuration file stackflows.php'
            );

            return;
        }

        /** @var TaskExecutorInterface $executor */
        foreach ($executors as $executor) {
            $result = $executor->execute();
            print_r($result);
        }
    }
}
