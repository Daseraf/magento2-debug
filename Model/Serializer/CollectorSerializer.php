<?php

namespace Daseraf\Debug\Model\Serializer;

use Daseraf\Debug\Exception\CollectorNotFoundException;
use Daseraf\Debug\Model\Collector\CollectorInterface;

class CollectorSerializer
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Daseraf\Debug\Logger\Logger
     */
    private $logger;

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Daseraf\Debug\Logger\Logger $logger,
        \Daseraf\Debug\Helper\Config $config
    ) {
        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param CollectorInterface[] $collectors
     * @return array
     */
    public function serialize(array $collectors): array
    {
        foreach ($collectors as &$collector) {
            $collector = $collector->getData();
        }

        return $collectors;
    }

    public function unserialize(array $data): array
    {
        $collectors = [];
        foreach ($data as $name => $collector) {
            try {
                $collectorClass = $this->config->getCollectorClass($name);
                $collectors[$name] = $this->objectManager->create($collectorClass)->setData($collector);
            } catch (CollectorNotFoundException $e) {
                $this->logger->critical($e);
                continue;
            }
        }

        return $collectors;
    }
}
