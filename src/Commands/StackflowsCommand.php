<?php

namespace Stackflows\StackflowsPlugin\Commands;

use Illuminate\Console\Command;

class StackflowsCommand extends Command
{
    public $signature = 'stackflows:inspire';

    public $description = 'Stackflows command';

    public function handle()
    {
        $this->info('Simplicity is the essence of happiness. - Cedric Bledsoe');
    }
}
