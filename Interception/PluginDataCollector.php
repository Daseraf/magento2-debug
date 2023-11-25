<?php

namespace Daseraf\Debug\Interception;

use Magento\Framework\Interception\DefinitionInterface;

class PluginDataCollector
{
    private $execution = [
        DefinitionInterface::LISTENER_BEFORE => [],
        DefinitionInterface::LISTENER_AROUND => [],
        DefinitionInterface::LISTENER_AFTER => []
    ];

    private $executedTypes = [];

    /**
     * @param array $data
     * @return void
     */
    public function setPluginExecutionTime(array $data)
    {
        $definition = array_key_first($data);
        $this->execution[$definition] = array_merge_recursive($this->execution[$definition], $data[$definition]);
    }

    public function getPluginExecutionTime()
    {
        return $this->execution;
    }

    public function addInExecutedTypes($typeClass)
    {
        $this->executedTypes[] = $typeClass;
    }

    public function getExecutedTypes()
    {
        return $this->executedTypes;
    }
}