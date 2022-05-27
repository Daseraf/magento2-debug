<?php

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Model\Config\Source\XhprofFlags;
use ClawRock\Debug\Model\Info\CallmapInfo;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CallmapCollectorPlugin
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

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param \Magento\Framework\App\Http $subject
     */
    public function beforeLaunch(\Magento\Framework\App\Http $subject)
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

    /**
     * @param \Magento\Framework\App\Http $subject
     * @param ResponseInterface $result
     * @return ResponseInterface
     */
    public function afterLaunch(\Magento\Framework\App\Http $subject, ResponseInterface $result)
    {
        if (!$this->isEnable()) {
            return $result;
        }

        $xhprofData = xhprof_disable();
        $this->callmapInfo->setRunData($xhprofData);

        return $result;
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
        $this->config = $objectManager->create(\ClawRock\Debug\Helper\Config::class);

        return $this->config;
    }
}
