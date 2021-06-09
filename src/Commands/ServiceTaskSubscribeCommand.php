<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Application;
use Stackflows\StackflowsPlugin\Exceptions\TooManyErrors;
use Stackflows\StackflowsPlugin\Services\ServiceTaskSubscriber;
use Stackflows\StackflowsPlugin\Stackflows;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class ServiceTaskSubscribeCommand extends Command implements SignalableCommandInterface
{
    public $signature = 'stackflows:subscribe:service-tasks';

    public $description = 'Subscribe to service tasks';

    private ServiceTaskSubscriber $subscriber;

    public function handle(Application $app, Stackflows $client)
    {
        $executors = $app->tagged('stackflows-service-task');
        if ($executors->count() === 0) {
            $this->error('Stackflows service task executors are not registered. Check the configuration file stackflows.php');
            return;
        }

        $logger = $app->make('log');

        $taskCh = $client->getServiceTaskChannel();
        $this->subscriber = (new ServiceTaskSubscriber($taskCh, $logger))->setExecutors($executors);

        try {
            $this->info('Listening to a pending service task');
            // Infinity Loop
            $this->subscriber->listen();
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
            $this->info('Stopping service task subscriber...');
            $this->subscriber->stop();

            return;
        }
        return;
    }
}
