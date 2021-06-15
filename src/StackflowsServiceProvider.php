<?php

namespace Stackflows\StackflowsPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Commands\ServiceTaskSubscribeCommand;
use Stackflows\StackflowsPlugin\Commands\SignalThrowCommand;
use Stackflows\StackflowsPlugin\Exceptions\InvalidConfiguration;

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
            ->hasCommands([SignalThrowCommand::class, ServiceTaskSubscribeCommand::class]);
    }

    public function packageRegistered()
    {
        $this->app->singleton(
            Configuration::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new Configuration(config('stackflows.host'), config('stackflows.instance'));
            }
        );

        $this->app->tag(config('stackflows.service_task_executors'), 'stackflows-service-task');
        $this->app->tag(config('stackflows.user_task_sync'), 'stackflows-user-task');
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['host'])) {
            throw InvalidConfiguration::hostNotSpecified();
        }

        if (empty($config['instance'])) {
            throw InvalidConfiguration::instanceNotSpecified();
        }
    }
}
