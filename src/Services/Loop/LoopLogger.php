<?php

namespace Stackflows\StackflowsPlugin\Services\Loop;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LoopLogger
{
    private LoggerInterface $logger;
    private OutputInterface $output;
    private bool $debug;

    public function __construct(LoggerInterface $logger, OutputInterface $output, bool $debug = false)
    {
        $this->logger = $logger;
        $this->output = $output;
        $this->debug = $debug;
    }

    public function info($message, array $context = [])
    {
        if (! $this->debug) {
            return;
        }

        $this->logger->info($message, $context);
        $this->output->writeln($message);
    }

    public function error($message, array $context = [])
    {
        $this->logger->error($message, $context);

        if ($this->debug) {
            $this->output->writeln($message);
        }
    }
}
