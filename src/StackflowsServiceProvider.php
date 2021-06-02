<?php

namespace Stackflows\StackflowsPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Commands\SignalThrowCommand;
use Stackflows\StackflowsPlugin\Commands\StackflowsCommand;
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
            ->hasCommands([StackflowsCommand::class, SignalThrowCommand::class]);
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
