<?php

namespace Daseraf\Debug\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseProfilerDisableCommand extends Command
{
    /**
     * @var \Daseraf\Debug\Model\Config\Database\ProfilerWriter
     */
    private $profilerWriter;

    public function __construct(
        \Daseraf\Debug\Model\Config\Database\ProfilerWriter $profilerWriter
    ) {
        parent::__construct('debug:db-profiler:disable');

        $this->profilerWriter = $profilerWriter;
    }

    protected function configure()
    {
        parent::configure();

        $this->setDescription('Disable database profiler required for database collector.');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->profilerWriter->save(false);

        $output->writeLn('<info>Database profiler disabled!</info>');

        return 1;
    }
}
