<?php

namespace Daseraf\Debug\Model\Collector;

use Daseraf\Debug\Model\Info\CallmapInfo;

class CallmapCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'callmap';
    public const PROFILE_DATA = 'profile';

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Daseraf\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var CallmapInfo
     */
    private $callmapInfo;

    public function __construct(
        \Daseraf\Debug\Helper\Config $config,
        \Daseraf\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        CallmapInfo $callmapInfo
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->callmapInfo = $callmapInfo;
    }

    public function collect(): CollectorInterface
    {
        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->setData([
            self::PROFILE_DATA => $this->callmapInfo->getRunData(),
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(): bool
    {
        return extension_loaded('xhprof') && $this->config->isCallmapCollectorEnabled();
    }

    public function getData(): array
    {
        return $this->dataCollector->getData();
    }

    public function setData(array $data): CollectorInterface
    {
        $this->dataCollector->setData($data);

        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }

    public function getToken(): string
    {
        return $this->profileMemoryStorage->read()->getToken();
    }

    public function getRunData(): array
    {
        return $this->getData(self::PROFILE_DATA);
    }
}
