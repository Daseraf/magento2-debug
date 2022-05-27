<?php

namespace Daseraf\Debug\Model\Collector;

use Magento\Framework\Interception\DefinitionInterface;

class PluginCollector implements CollectorInterface, LateCollectorInterface
{
    public const NAME = 'plugin';

    public const BEFORE = 'before';
    public const AROUND = 'around';
    public const AFTER = 'after';
    public const TOTAL_EXECUTION_TIME = 'total_execution_time';
    public const BEFORE_EXECUTION_TIME = 'before_execution_time';
    public const AROUND_EXECUTION_TIME = 'around_execution_time';
    public const AFTER_EXECUTION_TIME = 'after_execution_time';
    public const EXECUTION_TIME_BY_TYPES = 'execution_time_by_types';

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Daseraf\Debug\Model\DataCollector
     */
    private $dataCollector;

    /**
     * @var \Daseraf\Debug\Model\Info\PluginInfo
     */
    private $pluginInfo;

    /**
     * @var \Daseraf\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Daseraf\Debug\Helper\Config $config,
        \Daseraf\Debug\Model\DataCollectorFactory $dataCollectorFactory,
        \Daseraf\Debug\Model\Info\PluginInfo $pluginInfo,
        \Daseraf\Debug\Helper\Formatter $formatter
    ) {
        $this->config = $config;
        $this->dataCollector = $dataCollectorFactory->create();
        $this->pluginInfo = $pluginInfo;
        $this->formatter = $formatter;
    }

    public function collect(): CollectorInterface
    {
        return $this;
    }

    public function lateCollect(): LateCollectorInterface
    {
        $this->dataCollector->setData([
            self::BEFORE => $this->pluginInfo->getBeforePlugins(),
            self::AROUND => $this->pluginInfo->getAroundPlugins(),
            self::AFTER => $this->pluginInfo->getAfterPlugins(),
            self::TOTAL_EXECUTION_TIME => $this->pluginInfo->getPluginsExecutionTime(),
            self::BEFORE_EXECUTION_TIME => $this->pluginInfo->getPluginsExecutionTime(DefinitionInterface::LISTENER_BEFORE),
            self::AROUND_EXECUTION_TIME => $this->pluginInfo->getPluginsExecutionTime(DefinitionInterface::LISTENER_AROUND),
            self::AFTER_EXECUTION_TIME => $this->pluginInfo->getPluginsExecutionTime(DefinitionInterface::LISTENER_AFTER),
            self::EXECUTION_TIME_BY_TYPES => $this->pluginInfo->getTypesExecutionList(),
        ]);

        return $this;
    }

    public function hasPlugins(): bool
    {
        return !empty($this->dataCollector->getData(self::BEFORE))
            || !empty($this->dataCollector->getData(self::AROUND))
            || !empty($this->dataCollector->getData(self::AFTER));
    }

    public function getBeforePlugins(): array
    {
        return $this->dataCollector->getData(self::BEFORE) ?? [];
    }

    public function getAroundPlugins(): array
    {
        return $this->dataCollector->getData(self::AROUND) ?? [];
    }

    public function getAfterPlugins(): array
    {
        return $this->dataCollector->getData(self::AFTER) ?? [];
    }

    public function getPluginsCount(): int
    {
        return $this->getBeforePluginsCount() + $this->getAroundPluginsCount() + $this->getBeforePluginsCount();
    }

    public function getBeforePluginsCount(): int
    {
        return array_sum(array_map('count', $this->getBeforePlugins()));
    }

    public function getAroundPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAroundPlugins()));
    }

    public function getAfterPluginsCount(): int
    {
        return array_sum(array_map('count', $this->getAfterPlugins()));
    }

    public function getPluginsExecutionTime(): string
    {
        return $this->formatter->microtime($this->pluginInfo->getPluginsExecutionTime());
    }

    public function getBeforePluginsExecutionTime(): string
    {
        $aroundPluginsExecutionTime = $this->dataCollector->getData(self::BEFORE_EXECUTION_TIME) ?? 0;

        return $this->formatter->microtime($aroundPluginsExecutionTime);
    }

    public function getAfterPluginsExecutionTime(): string
    {
        $aroundPluginsExecutionTime = $this->dataCollector->getData(self::AFTER_EXECUTION_TIME) ?? 0;

        return $this->formatter->microtime($aroundPluginsExecutionTime);
    }

    public function getAroundPluginsExecutionTime(): string
    {
        $aroundPluginsExecutionTime = $this->dataCollector->getData(self::AROUND_EXECUTION_TIME) ?? 0;

        return $this->formatter->microtime($aroundPluginsExecutionTime);
    }

    public function getTypePluginsExecutionTime($type, $definition): string
    {
        $typesExecutionList = $this->dataCollector->getData(self::EXECUTION_TIME_BY_TYPES) ?? [];
        if (isset($typesExecutionList[$definition][$type])) {
            return $this->formatter->microtime($typesExecutionList[$definition][$type]);
        }

        return '0';
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isPluginCollectorEnabled();
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

    public function formatTime($value): string
    {
        return $this->formatter->microtime($value);
    }
}
