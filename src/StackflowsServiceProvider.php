<?php

namespace Stackflows\StackflowsPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Commands\ServiceTaskSubscribeCommand;
use Stackflows\StackflowsPlugin\Exceptions\InvalidConfiguration;
use Stackflows\StackflowsPlugin\Http\Client\StackflowsClient;

class StackflowsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('stackflows')
            ->hasConfigFile('stackflows')
            ->hasCommands([ServiceTaskSubscribeCommand::class]);
    }

    public function packageRegistered()
    {
        $this->app->bind(
            StackflowsClient::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new StackflowsClient(config('stackflows.authToken'), config('stackflows.apiHost'));
            }
        );


        $this->app->tag(config('stackflows.external_task_executors'), 'stackflows-external-task');
//        $this->app->tag(config('stackflows.user_task_sync'), 'stackflows-user-task');
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['apiHost'])) {
            throw InvalidConfiguration::apiHostNotSpecified();
        }

        if (empty($config['authToken'])) {
            throw InvalidConfiguration::authTokenNotSpecified();
        }
    }
}
