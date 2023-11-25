<?php

namespace Daseraf\Debug\Interception\Code\Generator;

class Interceptor extends \Magento\Framework\Interception\Code\Generator\Interceptor
{

    protected function _generateCode()
    {
        $typeName = $this->getSourceClassName();
        $reflection = new \ReflectionClass($typeName);

        $interfaces = [];
        if ($reflection->isInterface()) {
            $interfaces[] = $typeName;
        } else {
            $this->_classGenerator->setExtendedClass($typeName);
        }
        $this->_classGenerator->addTrait('\\' . \Daseraf\Debug\Interception\Interceptor::class);
        $interfaces[] = '\\' . \Magento\Framework\Interception\InterceptorInterface::class;
        $this->_classGenerator->setImplementedInterfaces($interfaces);

        return \Magento\Framework\Code\Generator\EntityAbstract::_generateCode();
    }
}
