<?php

namespace Stackflows\StackflowsPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Commands\ServiceTaskSubscribeCommand;
use Stackflows\StackflowsPlugin\Exceptions\InvalidConfiguration;
use Stackflows\StackflowsPlugin\Http\Client\GatewayClient;
use StackflowsPlugin\StackflowsConfiguration;

class StackflowsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('stackflows')
            ->hasConfigFile('stackflows')
            ->hasCommands([SignalThrowCommand::class, ServiceTaskSubscribeCommand::class, UserTaskSyncCommand::class]);
    }

    public function packageRegistered()
    {
        $this->app->singleton(
            StackflowsConfiguration::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new StackflowsConfiguration(
                    config('stackflows.gatewayHost'),
                    config('stackflows.authToken'),
                    config('app.debug')
                );
            }
        );

        $this->app->bind(
            GatewayClient::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new GatewayClient(config('stackflows.camundaHost'));
            }
        );


        $this->app->tag(config('stackflows.service_task_executors'), 'stackflows-service-task');
//        $this->app->tag(config('stackflows.user_task_sync'), 'stackflows-user-task');
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['host'])) {
            throw InvalidConfiguration::hostNotSpecified();
        }

        if (empty($config['backofficeHost'])) {
            throw InvalidConfiguration::backofficeHostNotSpecified();
        }

        if (empty($config['instance'])) {
            throw InvalidConfiguration::instanceNotSpecified();
        }
    }
}
