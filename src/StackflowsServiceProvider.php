<?php

namespace Stackflows;

use GuzzleHttp\Client;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\Configuration;
use Stackflows\Commands\BusinessProcesses\ExecuteServiceTasks;
use Stackflows\Commands\BusinessProcesses\MakeServiceTaskExecutor;
use Stackflows\Commands\BusinessProcesses\Start;

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
        $this->app->bind(EnvironmentApi::class, function () {
            $cfg = new Configuration();
            $cfg
                ->setHost(sprintf('%s://%s/api/v2', config('stackflows.protocol'), config('stackflows.host')))
                ->setAccessToken(config('stackflows.token'));

            return new EnvironmentApi(new Client(), $cfg);
        });
    }
}
