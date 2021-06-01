<?php

namespace Stackflows\StackflowsPlugin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Stackflows\StackflowsPlugin\Commands\StackflowsCommand;

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
            ->hasCommand(StackflowsCommand::class);
    }
}
