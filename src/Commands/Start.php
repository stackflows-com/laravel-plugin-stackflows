<?php

namespace Stackflows\Commands;

use Illuminate\Console\Command;
use Stackflows\Stackflows;

class Start extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stackflows:start
        {tag : A tag by which processes will be selected for starting}
        {variables? : A JSON string of variables}
    ';

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
     * @throws \Stackflows\Clients\Stackflows\ApiException
     */
    public function handle()
    {
        $tag = $this->input->getArgument('tag');
        $variables = $this->input->getArgument('variables');

        if ($variables) {
            $variables = json_decode($variables);
        }

        $this->stackflows->startBusinessProcesses((array) $tag, (array)$variables);

        $this->output->success(sprintf('Business processes tagged as "%s" was started successfully', $tag));
    }
}
