<?php

namespace Stackflows\StackflowsPlugin;

use GuzzleHttp\Client;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Auth\BackofficeClient;
use Stackflows\StackflowsPlugin\Auth\TokenProviderInterface;
use Stackflows\StackflowsPlugin\Commands\ServiceTaskSubscribeCommand;
use Stackflows\StackflowsPlugin\Commands\SignalThrowCommand;
use Stackflows\StackflowsPlugin\Commands\UserTaskSyncCommand;
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
            ->hasCommands([SignalThrowCommand::class, ServiceTaskSubscribeCommand::class, UserTaskSyncCommand::class]);
    }

    public function packageRegistered()
    {
        $this->app->singleton(
            Configuration::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new Configuration(
                    config('stackflows.host'),
                    config('stackflows.instance'),
                    config('stackflows.backofficeHost'),
                    config('app.debug')
                );
            }
        );

        $this->app->bind(
            BackofficeClient::class,
            function () {
                $this->guardAgainstInvalidConfiguration(config('stackflows'));

                return new BackofficeClient(new Client(['base_uri' => config('stackflows.backofficeHost')]));
            }
        );

        $this->app->bind(TokenProviderInterface::class, function ($app) {
            $providerClass = config('stackflows.token_provider');
            $provider = $app->make($providerClass);

            if (! $provider instanceof TokenProviderInterface) {
                throw InvalidConfiguration::invalidTokenProvider($providerClass);
            }

            return $provider;
        });

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

        if (empty($config['backofficeHost'])) {
            throw InvalidConfiguration::backofficeHostNotSpecified();
        }

        if (empty($config['instance'])) {
            throw InvalidConfiguration::instanceNotSpecified();
        }
    }
}
