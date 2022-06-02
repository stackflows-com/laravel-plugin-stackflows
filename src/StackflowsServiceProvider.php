<?php

namespace Stackflows;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\Commands\BusinessProcesses\ExecuteServiceTasks;
use Stackflows\Commands\BusinessProcesses\MakeServiceTaskExecutor;
use Stackflows\Commands\BusinessProcesses\Start;
use Stackflows\Exceptions\InvalidConfiguration;
use Stackflows\Http\Client\AbstractStackflowsClient;
use Stackflows\Http\Client\StackflowsClient;

class StackflowsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('stackflows')
            ->hasConfigFile('stackflows')
            ->hasCommands([
                ExecuteServiceTasks::class,
                MakeServiceTaskExecutor::class,
                Start::class,
            ]);
    }

    public function packageRegistered()
    {
        $this->app->bind(
            StackflowsClient::class,
            function () {
                return $this->buildClient(StackflowsClient::class);
            }
        );
    }

    private function buildClient($clientClassName): AbstractStackflowsClient
    {
        $this->guardAgainstInvalidConfiguration(config('stackflows'));

        $version = config('stackflows.version');
        switch ($version) {
            case '2':
                return new $clientClassName(
                    config('stackflows.token'),
                    sprintf(
                        '%s://%s/api/v2/auth/environment/',
                        config('stackflows.secure') ? 'https' : 'http',
                        config('stackflows.host')
                    )
                );
            default:
                throw new \Exception(sprintf(
                    'Stackflows plugin does not support "%s" version. Try using latest version of a plugin.',
                    $version
                ));
        }
    }

    /**
     * @throws InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['host'])) {
            throw InvalidConfiguration::hostNotSpecified();
        }

        if (empty($config['version'])) {
            throw InvalidConfiguration::versionNotSpecified();
        }

        if (empty($config['token'])) {
            throw InvalidConfiguration::tokenNotSpecified();
        }
    }
}
