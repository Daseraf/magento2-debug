<?php

namespace Daseraf\Debug\Interception;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Interception\DefinitionInterface;
use Magento\Framework\Interception\PluginListInterface;

trait Interceptor
{
    /**
     * List of plugins
     *
     * @var PluginListInterface
     */
    private $pluginList;

    /**
     * Subject type name
     *
     * @var string
     */
    private $subjectType;

    /**
     * @var PluginDataCollector
     */
    private $pluginDataCollector;

    /**
     * Initialize the Interceptor
     *
     * @return void
     */
    public function ___init()
    {
        $this->pluginList = ObjectManager::getInstance()->get(PluginListInterface::class);
        $this->pluginDataCollector = ObjectManager::getInstance()->get(PluginDataCollector::class);
        $this->subjectType = get_parent_class($this);
        if (method_exists($this->subjectType, '___init')) {
            parent::___init();
        }
    }

    /**
     * Calls parent class method
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function ___callParent($method, array $arguments)
    {
        return parent::$method(...array_values($arguments));
    }

    /**
     * Calls parent class sleep if defined, otherwise provides own implementation
     *
     * @return array
     */
    public function __sleep()
    {
        if (method_exists(get_parent_class($this), '__sleep')) {
            $properties = parent::__sleep();
        } else {
            $properties = array_keys(get_object_vars($this));
        }
        $properties = array_diff($properties, ['pluginList', 'subjectType']);
        return $properties;
    }

    /**
     * Causes Interceptor to be initialized
     *
     * @return void
     */
    public function __wakeup()
    {
        if (method_exists(get_parent_class($this), '__wakeup')) {
            parent::__wakeup();
        }
        $this->___init();
    }

    /**
     * Calls plugins for a given method.
     *
     * @param string $method
     * @param array $arguments
     * @param array $pluginInfo
     * @return mixed|null
     */
    protected function ___callPlugins($method, array $arguments, array $pluginInfo)
    {
        $subject = $this;
        $type = $this->subjectType;
        $pluginList = $this->pluginList;

        $next = function (...$arguments) use (
            $method,
            &$pluginInfo,
            $subject,
            $type,
            $pluginList,
            &$next
        ) {
            $capMethod = ucfirst($method);
            $currentPluginInfo = $pluginInfo;
            $result = null;

            if (isset($currentPluginInfo[DefinitionInterface::LISTENER_BEFORE])) {
                // Call 'before' listeners
                foreach ($currentPluginInfo[DefinitionInterface::LISTENER_BEFORE] as $code) {
                    $pluginInstance = $pluginList->getPlugin($type, $code);
                    $timeStart = microtime(true);
                    $pluginMethod = 'before' . $capMethod;
                    $beforeResult = $pluginInstance->$pluginMethod($this, ...array_values($arguments));
                    $timeEnd = microtime(true);
                    $pluginClass = get_class($pluginInstance);

                    $pluginExecutionTime[DefinitionInterface::LISTENER_BEFORE][$pluginClass] = [
                        $method => [$code => [$timeEnd - $timeStart]]
                    ];
                    $this->pluginDataCollector->setPluginExecutionTime($pluginExecutionTime);
                    $this->pluginDataCollector->addInExecutedTypes($type);
                    if ($beforeResult !== null) {
                        $arguments = (array)$beforeResult;
                    }
                }
            }

            if (isset($currentPluginInfo[DefinitionInterface::LISTENER_AROUND])) {
                // Call 'around' listener
                $code = $currentPluginInfo[DefinitionInterface::LISTENER_AROUND];
                $pluginInfo = $pluginList->getNext($type, $method, $code);
                $pluginInstance = $pluginList->getPlugin($type, $code);
                $pluginMethod = 'around' . $capMethod;
                $timeStart = microtime(true);
                $result = $pluginInstance->$pluginMethod($subject, $next, ...array_values($arguments));
                $timeEnd = microtime(true);
                $pluginClass = get_class($pluginInstance);
                $pluginExecutionTime[DefinitionInterface::LISTENER_AROUND][$pluginClass] = [
                    $method => [$code => [$timeEnd - $timeStart]]
                ];
                $this->pluginDataCollector->setPluginExecutionTime($pluginExecutionTime);
                $this->pluginDataCollector->addInExecutedTypes($type);
            } else {
                // Call original method
                $result = $subject->___callParent($method, $arguments);
            }

            if (isset($currentPluginInfo[DefinitionInterface::LISTENER_AFTER])) {
                // Call 'after' listeners
                foreach ($currentPluginInfo[DefinitionInterface::LISTENER_AFTER] as $code) {
                    $pluginInstance = $pluginList->getPlugin($type, $code);
                    $pluginMethod = 'after' . $capMethod;
                    $timeStart = microtime(true);
                    $result = $pluginInstance->$pluginMethod($subject, $result, ...array_values($arguments));
                    $timeEnd = microtime(true);
                    $pluginClass = get_class($pluginInstance);
                    $pluginExecutionTime[DefinitionInterface::LISTENER_AFTER][$pluginClass] = [
                        $method => [$code => [$timeEnd - $timeStart]]
                    ];
                    $this->pluginDataCollector->setPluginExecutionTime($pluginExecutionTime);
                    $this->pluginDataCollector->addInExecutedTypes($type);
                }
            }

            return $result;
        };

        $result = $next(...array_values($arguments));
        $next = null;

        return $result;
    }
}
