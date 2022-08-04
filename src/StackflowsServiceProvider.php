<?php

namespace Stackflows;

use GuzzleHttp\Client;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\Clients\Stackflows\Api\EnvironmentApi;
use Stackflows\Clients\Stackflows\Configuration;
use Stackflows\Commands\Make\MakeServiceTaskExecutor;
use Stackflows\Commands\Serve;
use Stackflows\Commands\Start;
use Stackflows\Commands\Sync\SyncTasks;

class StackflowsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('stackflows')
            ->hasConfigFile('stackflows')
            ->hasCommands([
                MakeServiceTaskExecutor::class,
                SyncTasks::class,
                Serve::class,
                Start::class,
            ]);
    }

    public function packageRegistered()
    {
        $this->app->bind(EnvironmentApi::class, function () {
            $cfg = new Configuration();
            $cfg
                ->setHost(sprintf('%s://%s', config('stackflows.protocol'), config('stackflows.host')))
                ->setAccessToken(config('stackflows.token'));

            return new EnvironmentApi(new Client(), $cfg);
        });
    }
}
