<?php

namespace Stackflows;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Str;
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
    private array $bridges = [
        'activity',
        'business_decision_instance',
        'business_decision_model_diagram',
        'business_decision_model_publication',
        'business_model_publication',
        'business_process_instance',
        'business_process_model_diagram',
        'business_process_model_publication',
        'environment',
        'events',
        'service_task',
        'user_task',
    ];

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

        foreach ($this->bridges as $bridge) {
            $bridgeContractClass = sprintf('App\\Bridge\\%sBridgeContract', Str::ucfirst(Str::camel($bridge)));

            $this->app->bind(
                $bridgeContractClass,
                function (Application $app, array $arguments = []) use ($bridge) {
                    $bridgeClass = sprintf(
                        'App\\Bridge\\%1$s\\v%2$s\\%3$s%1$sBridge',
                        Str::ucfirst(Str::camel('Camunda')),
                        Str::replace('.', '_', '7.17'), //TODO: This should come from an engine
                        Str::ucfirst(Str::camel($bridge))
                    );

                    $parameters = [
                        'environment' => $environment,
                    ];

                    // Ensure clients to be injected
                    $bridgeReflection = new \ReflectionClass($bridgeClass);
                    foreach ($bridgeReflection->getConstructor()->getParameters() as $parameter) {
                        if (Str::startsWith($parameter->getType()->getName(), 'Stackflows\\Clients\\Camunda\\')) {
                            $parameters[$parameter->getName()] = $this->makeCamundaApi(
                                $environment,
                                $parameter->getType()
                            );
                        }
                    }

                    return $app->makeWith($bridgeClass, $parameters);
                }
            );
        }
    }

    /**
     * @param Environment $environment
     * @param string $engineApiClass
     * @return mixed
     */
    private function makeCamundaApi(Environment $environment, string $apiClass): mixed
    {
        $cfg = new \Stackflows\Clients\Camunda\v7_17\Configuration();
        $cfg->setHost($environment->engine->getAttribute('path'));
        if ($environment->engine->getAttribute('auth')) {
            list($username, $password) = explode(':', $environment->engine->getAttribute('auth'));
            $cfg
                ->setUsername($username)
                ->setPassword($password);
        }

        return $this->app->make(
            $apiClass,
            [
                'client' => new Client(['verify' => false, 'auth' => [$cfg->getUsername(), $cfg->getPassword()]]),
                'config' => $cfg
            ]
        );
    }
}
