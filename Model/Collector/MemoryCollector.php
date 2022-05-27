<?php

namespace Daseraf\Debug\Model\Collector;

class MemoryCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'memory';

    public const REAL_MEMORY_USAGE = 'memory_usage';
    public const TOTAL_MEMORY_USAGE = 'total_memory_usage';
    public const MEMORY_LIMIT = 'memory_limit';

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Daseraf\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Daseraf\Debug\Model\Info\MemoryInfo
     */
    private $memoryInfo;

    /**
     * @var \Daseraf\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Daseraf\Debug\Helper\Config $config,
        \Daseraf\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Daseraf\Debug\Model\Info\MemoryInfo $memoryInfo,
        \Daseraf\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->memoryInfo = $memoryInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        $this->dataCollector->setData([
            self::REAL_MEMORY_USAGE => $this->memoryInfo->getRealMemoryUsage(),
            self::TOTAL_MEMORY_USAGE => $this->memoryInfo->getMemoryUsage(),
            self::MEMORY_LIMIT => $this->memoryInfo->getCurrentMemoryLimit(),
        ]);

        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->addData(self::REAL_MEMORY_USAGE, $this->memoryInfo->getRealMemoryUsage());
        $this->dataCollector->addData(self::TOTAL_MEMORY_USAGE, $this->memoryInfo->getMemoryUsage());

        return $this;
    }

    public function getRealMemoryUsage()
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::REAL_MEMORY_USAGE), 1);
    }

    public function getTotalMemoryUsage()
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::TOTAL_MEMORY_USAGE), 1);
    }

    public function getMemoryLimit()
    {
        return $this->formatter->toMegaBytes($this->dataCollector->getData(self::MEMORY_LIMIT));
    }

    public function hasMemoryLimit()
    {
        return $this->dataCollector->getData(self::MEMORY_LIMIT) !== -1;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isMemoryCollectorEnabled();
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
}
