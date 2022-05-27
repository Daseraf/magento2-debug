<?php

namespace Daseraf\Debug\Plugin\PageCache;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class KernelPlugin
{
    /**
     * @var \Daseraf\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    public function __construct(
        \Daseraf\Debug\Model\Storage\HttpStorage $httpStorage
    ) {
        $this->httpStorage = $httpStorage;
    }

    /**
     * @param \Magento\Framework\App\PageCache\Kernel $subject
     * @param false|\Magento\Framework\App\Response\Http $result
     * @return false|\Magento\Framework\App\Response\Http
     */
    public function afterLoad(\Magento\Framework\App\PageCache\Kernel $subject, $result)
    {
        if ($result !== false) {
            $this->httpStorage->markAsFPCRequest();
        }

        return $result;
    }
}
