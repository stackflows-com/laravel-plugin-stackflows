<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\Loop;
use Stackflows\StackflowsPlugin\Services\UserTask\UserTaskSync;
use Stackflows\StackflowsPlugin\Stackflows;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class UserTaskSyncCommand extends Command implements SignalableCommandInterface
{
    public $signature = 'stackflows:sync:user-tasks';

    public $description = 'Synchronize user tasks';

    private Loop $subscriber;

    public function handle(Application $app, Stackflows $client)
    {
        $syncs = $app->tagged('stackflows-user-task');
        if (empty($syncs)) {
            $this->error(
                'Stackflows service task executors are not registered. Check the configuration file stackflows.php'
            );

            return;
        }

        $this->authenticate($client->getAuth());

        $logger = $app->make('log');

        $taskCh = $client->getUserTaskChannel();
        $handler = new UserTaskSync($taskCh, $logger, $syncs);
        $this->subscriber = new Loop($handler);

        try {
            $this->info('Listening to user tasks');
            // Infinity Loop
            $this->subscriber->run();
        } catch (TooManyErrors $e) {
            $this->error($e->getMessage());
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the list of signals handled by the command.
     *
     * @return array
     */
    public function getSubscribedSignals(): array
    {
        return [SIGINT, SIGTERM];
    }

    /**
     * Handle an incoming signal.
     *
     * @param int $signal
     * @return void
     */
    public function handleSignal(int $signal): void
    {
        if ($signal === SIGINT || $signal === SIGTERM) {
            $this->info('Stopping user task subscriber...');
            $this->subscriber->stop();

            return;
        }

        return;
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
