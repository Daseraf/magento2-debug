<?php

namespace Daseraf\Debug\Interception;

use Daseraf\Debug\Model\Profiler;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CliProfiler extends \Magento\Framework\Console\Cli
{
    /**
     * Initialization exception.
     *
     * @var \Exception
     */
    private $initException;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Profiler
     */
    private $profiler;
    
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);
        $this->logger = ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->profiler = ObjectManager::getInstance()->get(Profiler::class);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception The exception in case of unexpected error
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $exitCode = null;
        try {
            $exitCode = parent::doRun($input, $output);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage() . PHP_EOL . $e->getTraceAsString();
            $this->logger->error($errorMessage);
            $this->initException = $e;
        }

        if ($this->initException) {
            throw $this->initException;
        }

        $name = $this->getCommandName($input);
        $this->profiler->collectCli($exitCode, $name);

        return $exitCode;
    }
}