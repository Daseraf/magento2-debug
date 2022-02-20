<?php

namespace ClawRock\Debug\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ErrorHandler implements ArrayInterface
{
    public const MAGENTO = '0';
    public const WHOOPS = 'whoops';

    public function toOptionArray()
    {
        return [
            ['value' => self::MAGENTO, 'label' => __('Default')],
            ['value' => self::WHOOPS, 'label' => __('Whoops')],
        ];
    }

    public function toArray()
    {
        return [
            self::MAGENTO => __('Default'),
            self::WHOOPS => __('Whoops'),
        ];
    }
}
