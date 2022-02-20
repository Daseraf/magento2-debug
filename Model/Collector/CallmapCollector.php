<?php

namespace ClawRock\Debug\Model\Collector;

use ClawRock\Debug\Model\Info\CallmapInfo;

class CallmapCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'callmap';
    public const PROFILE_DATA = 'profile';

    /**
     * @var \ClawRock\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \ClawRock\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \ClawRock\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var CallmapInfo
     */
    private $callmapInfo;

    public function __construct(
        \ClawRock\Debug\Helper\Config $config,
        \ClawRock\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \ClawRock\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
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
