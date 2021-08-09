<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Auth\BackofficeAuth;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\Loop\Loop;
use Stackflows\StackflowsPlugin\Services\ServiceTask\ServiceTaskSubscriber;
use Stackflows\StackflowsPlugin\Stackflows;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class ServiceTaskSubscribeCommand extends Command implements SignalableCommandInterface
{
    public $signature = 'stackflows:subscribe:service-tasks';

    public $description = 'Subscribe to service tasks';

    private Loop $subscriber;

    public function handle(Application $app, Stackflows $client)
    {
        $executors = $app->tagged('stackflows-service-task');
        if (empty($executors)) {
            $this->error(
                'Stackflows service task executors are not registered. Check the configuration file stackflows.php'
            );

            return;
        }

        $this->authenticate($client->getAuth());

        $logger = $app->make('log');
        $taskCh = $client->getServiceTaskChannel();
        $handler = new ServiceTaskSubscriber($taskCh, $logger, $executors);
        $this->subscriber = new Loop($handler);

        try {
            $this->info('Listening to a pending service task');
            // Infinity Loop
            $this->subscriber->run();
        } catch (TooManyErrors | Exception $e) {
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
            $this->info('Stopping service task subscriber...');
            $this->subscriber->stop();
        }
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
