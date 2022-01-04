<?php

namespace Stackflows\Commands\BusinessProcesses;

use Illuminate\Console\Command;
use Stackflows\Stackflows;

class MakeServiceTaskExecutor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stackflows:business-processes:make:service-task-executor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will start all business processes that are tagged with specified tag';

    /**
     * @var Stackflows
     */
    private Stackflows $stackflows;

    /**
     * @param Stackflows $stackflows
     */
    public function __construct(Stackflows $stackflows)
    {
        $this->stackflows = $stackflows;

        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (! class_exists(Nette\PhpGenerator\ClassType::class)) {
            $this->output->error(sprintf('Package "nette/php-generator" is not installed.'));
        }

        $namespace = $this->ask(
            'Provide a namespace (default: App\Stackflows\ServiceTasks)',
            'App\Stackflows\ServiceTasks'
        );
        $topic = $this->ask('Provide a topic');
        $lockDuration = $this->ask('Provide a lock duration (default: 60000)', 60000);

        $this->output->info('Use "int:someVariableName" format. Following types will be accepted: int, float, array, string, bool');
        $inputVariable = $this->ask('Provide first input variable (leave blank to continue)');
        $inputVariables = [];
        while (! empty($inputVariable)) {
            $inputVariables[] = $inputVariable;
            $inputVariable = $this->ask('Provide another input variable (leave blank to continue)');
        }

        $outputVariable = $this->ask('Provide first output variable (leave blank to continue)');
        $outputVariables = [];
        while (! empty($outputVariable)) {
            $outputVariables[] = $outputVariable;
            $outputVariable = $this->ask('Provide another output variable (leave blank to continue)');
        }
    }
}
