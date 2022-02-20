<?php

namespace ClawRock\Debug\Plugin\Collector;

use ClawRock\Debug\Helper\Config;
use ClawRock\Debug\Model\Info\CallmapInfo;
use Magento\Framework\App\Http;
use Magento\Framework\App\ResponseInterface;
use ClawRock\Debug\Model\Config\Source\XhprofFlags;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CallmapCollectorPlugin
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var CallmapInfo
     */
    private $callmapInfo;

    /**
     * @param Config $config
     * @param CallmapInfo $callmapInfo
     */
    public function __construct(
        Config $config,
        CallmapInfo $callmapInfo
    ) {
        $this->config = $config;
        $this->callmapInfo = $callmapInfo;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param Http $subject
     */
    public function beforeLaunch(Http $subject)
    {
        if(!$this->config->isCallmapCollectorEnabled()) {
            return;
        }

        $flags = $this->config->getXhprofFlags();
        $excludeBuiltins = in_array(XhprofFlags::FLAG_NO_BUILTINS, $flags) ? XhprofFlags::FLAG_NO_BUILTINS : 0;
        $profileCpu = in_array(XhprofFlags::FLAG_CPU, $flags) ? XhprofFlags::FLAG_CPU : 0;
        $profileMemory = in_array(XhprofFlags::FLAG_MEMORY, $flags) ? XhprofFlags::FLAG_MEMORY : 0;

        xhprof_enable($excludeBuiltins | $profileMemory | $profileCpu);
    }

    /**
     * @param Http $subject
     * @param ResponseInterface $result
     * @return ResponseInterface
     */
    public function afterLaunch(Http $subject, ResponseInterface $result)
    {
        if(!$this->config->isCallmapCollectorEnabled()) {
            return $result;
        }

        $xhprofData = xhprof_disable();
        $this->callmapInfo->setRunData($xhprofData);

        return $result;
    }
}
