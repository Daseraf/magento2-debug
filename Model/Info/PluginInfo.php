<?php

namespace ClawRock\Debug\Model\Info;

use ClawRock\Debug\Model\Collector\PluginCollector;
use ClawRock\Debug\Model\ValueObject\Plugin;
use Magento\Framework\Interception\DefinitionInterface;

class PluginInfo
{
    /**
     * @var array
     */
    private $plugins;

    /**
     * @var array
     */
    private $typeExecutionList = [];

    /**
     * @var array
     */
    private $totalExecutionTimeList;

    /**
     * @var bool
     */
    private $alreadySorted = false;

    /**
     * @var \Magento\Framework\Interception\PluginList\PluginList
     */
    private $pluginList;

    /**
     * @var \ClawRock\Debug\Helper\Debug
     */
    private $debug;

    public function __construct(
        \Magento\Framework\Interception\PluginList\PluginList $pluginList,
        \ClawRock\Debug\Helper\Debug $debug
    ) {
        $this->pluginList = $pluginList;
        $this->debug = $debug;
    }

    public function getBeforePlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::BEFORE];
    }

    public function getAroundPlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::AROUND];
    }

    public function getAfterPlugins(): array
    {
        $this->resolvePlugins();

        return $this->plugins[PluginCollector::AFTER];
    }

    public function getTypesExecutionList(): array
    {
        $this->resolvePlugins();

        return $this->typeExecutionList;
    }

    public function getPluginsExecutionTime($definition = null): float
    {
        $this->resolvePlugins();

        if (isset($this->totalExecutionTimeList)) {
            if ($definition && isset($this->totalExecutionTimeList[$definition])) {
                return $this->totalExecutionTimeList[$definition];
            }

            return $this->totalExecutionTimeList['total'];
        }

        $typesExecutionList = $this->getTypesExecutionList();

        foreach ($typesExecutionList as $definition => $classArray) {
            foreach ($classArray as $time) {
                if (!isset($this->totalExecutionTimeList[$definition])) {
                    $this->totalExecutionTimeList[$definition] = 0;
                }

                $this->totalExecutionTimeList[$definition] = $this->totalExecutionTimeList[$definition] + $time;
            }
        }

        foreach ($this->totalExecutionTimeList as $total) {
            if (!isset($this->totalExecutionTimeList['total'])) {
                $this->totalExecutionTimeList['total'] = 0;
            }

            $this->totalExecutionTimeList['total'] = $this->totalExecutionTimeList['total'] + $total;
        }

        if ($definition) {
            return $this->totalExecutionTimeList[$definition];
        }

        return $this->totalExecutionTimeList['total'];
    }

    private function resolvePlugins(): void
    {
        if ($this->plugins !== null) {
            $this->sortPluginsByExecutionTime();

            return;
        }

        $reflection = new \ReflectionClass($this->pluginList);
        
        $processed = $reflection->getProperty('_processed');
        $processed->setAccessible(true);
        $processed = $processed->getValue($this->pluginList);
        
        $inherited = $reflection->getProperty('_inherited');
        $inherited->setAccessible(true);
        $inherited = $inherited->getValue($this->pluginList);
        
        $execution = $reflection->getProperty('execution');
        $execution->setAccessible(true);
        $executionTime = $execution->getValue($this->pluginList);
        
        $executedTypes = $reflection->getProperty('executedTypes');
        $executedTypes->setAccessible(true);
        $executedTypesList = $executedTypes->getValue($this->pluginList);
        
        $definitionTypes = [
            DefinitionInterface::LISTENER_BEFORE => PluginCollector::BEFORE,
            DefinitionInterface::LISTENER_AROUND => PluginCollector::AROUND,
            DefinitionInterface::LISTENER_AFTER => PluginCollector::AFTER,
        ];

        foreach ($processed as $plugin => $definition) {
            if (!preg_match('/^(.*?)_(.*?)_(.*)$/', $plugin, $matches)) {
                continue;
            }
            $type = $matches[1];
            $method = $matches[2];

            if (!in_array($type, $executedTypesList)) {
                continue;
            }

            if ($this->debug->isDebugClass($type)) {
                continue;
            }

            foreach ($definition as $definitionType => $plugins) {
                if (!isset($this->typeExecutionList[$definitionType][$type])) {
                    $this->typeExecutionList[$definitionType][$type] = 0;
                }
                $executionTimeByDefinition = $executionTime[$definitionType];
                $pluginList = [];

                foreach ((array) $plugins as $name) {
                    if (!isset($inherited[$type][$name])) {
                        continue;
                    }

                    if ($this->debug->isDebugClass($inherited[$type][$name]['instance'])) {
                        continue;
                    }

                    $pluginExecutionTime = 0;
                    $executionCount = 0;
                    if (isset($executionTimeByDefinition[$inherited[$type][$name]['instance']])) {
                        try {
                            $executedPlugin = $executionTimeByDefinition[$inherited[$type][$name]['instance']];
                            $executionCount = count($executedPlugin[$method][$name]);
                        } catch (\Exception $e) {
                            continue;
                        }
                        if ($executionCount > 1) {
                            foreach ($executedPlugin[$method][$name] as $time) {
                                $pluginExecutionTime = $pluginExecutionTime + $time;
                            }
                        } else {
                            $pluginExecutionTime = $executedPlugin[$method][$name][0];
                        }
                    }

                    if (!$executionCount) {
                        continue;
                    }

                    $this->typeExecutionList[$definitionType][$type] = $this->typeExecutionList[$definitionType][$type] + $pluginExecutionTime;

                    $this->plugins[$definitionTypes[$definitionType]][$type][] = new Plugin(
                        $inherited[$type][$name]['instance'],
                        $name,
                        $inherited[$type][$name]['sortOrder'],
                        $definitionTypes[$definitionType] . ucfirst($method),
                        $type,
                        $pluginExecutionTime,
                        $executionCount
                    );
                }
            }
        }
        $this->sortPluginsByExecutionTime();
    }

    private function sortPluginsByExecutionTime()
    {
        if ($this->alreadySorted) {
            return;
        }

        foreach ($this->typeExecutionList as $definition => $list) {
            arsort($list);

            $this->typeExecutionList[$definition] = $list;
        }

        foreach ($this->plugins as $key => $definitionArray) {
            $sortedArray = [];

            switch ($key) {
                case PluginCollector::BEFORE:
                    $definition = DefinitionInterface::LISTENER_BEFORE;
                    break;
                case PluginCollector::AFTER:
                    $definition = DefinitionInterface::LISTENER_AFTER;
                    break;
                case PluginCollector::AROUND:
                    $definition = DefinitionInterface::LISTENER_AROUND;
                    break;
            }

            if (!isset($this->typeExecutionList[$definition])) {
                continue;
            }

            foreach ($this->typeExecutionList[$definition] as $class => $time) {
                if (!isset($definitionArray[$class])) {
                    continue;
                }

                $sortedArray[$class] = $definitionArray[$class];
            }

            $this->plugins[$key] = $sortedArray;
        }

        $this->alreadySorted = true;
    }
}
