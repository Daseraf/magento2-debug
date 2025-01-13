<?php

namespace Daseraf\Debug\Interception;

use Daseraf\Debug\Model\Config\Source\XhprofFlags;
use Daseraf\Debug\Model\Info\CallmapInfo;
use Daseraf\Debug\Model\Profiler;
use Magento\Framework\App\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CliCallmap
{
    /**
     * @var CallmapInfo
     */
    private $callmapInfo;

    private $isEnableStatus;

    private $config;

    /**
     * @param CallmapInfo $callmapInfo
     */
    public function __construct(CallmapInfo $callmapInfo) {
        $this->callmapInfo = $callmapInfo;
    }
    
    public function startTracking()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if (!$this->isEnable()) {
            return;
        }

        $flags = $this->config->getXhprofFlags();
        $excludeBuiltins = in_array(XhprofFlags::FLAG_NO_BUILTINS, $flags) ? XhprofFlags::FLAG_NO_BUILTINS : 0;
        $profileCpu = in_array(XhprofFlags::FLAG_CPU, $flags) ? XhprofFlags::FLAG_CPU : 0;
        $profileMemory = in_array(XhprofFlags::FLAG_MEMORY, $flags) ? XhprofFlags::FLAG_MEMORY : 0;

        xhprof_enable($excludeBuiltins | $profileMemory | $profileCpu);
    }
    
    public function finishTracking()
    {
        if (!$this->isEnable()) {
            return;
        }

        $xhprofData = xhprof_disable();
        $this->callmapInfo->setRunData($xhprofData);
    }

    private function isEnable()
    {
        if (isset($this->isEnableStatus)) {
            return $this->isEnableStatus;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->isEnableStatus = $this->getConfig()->isCallmapCollectorEnabled();

        return $this->isEnableStatus;
    }

    private function getXhprofFlags()
    {
        return $this->getConfig()->getXhprofFlags();
    }

    private function getConfig()
    {
        if (isset($this->config)) {
            return $this->config;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->config = $objectManager->create(\Daseraf\Debug\Helper\Config::class);

        return $this->config;
    }
}