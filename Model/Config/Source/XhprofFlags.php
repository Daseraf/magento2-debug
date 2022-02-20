<?php

namespace ClawRock\Debug\Model\Config\Source;

class XhprofFlags implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @link https://php.net/manual/en/xhprof.constants.php#constant.xhprof-flags-no-builtins
     */
    public const FLAG_NO_BUILTINS = 1;
    /**
     * @link https://php.net/manual/en/xhprof.constants.php#constant.xhprof-flags-cpu
     */
    public const FLAG_CPU = 2;
    /**
     * @link https://php.net/manual/en/xhprof.constants.php##constant.xhprof-flags-memory
     */
    public const FLAG_MEMORY = 4;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FLAG_NO_BUILTINS, 'label' => __('No xhprof builtins')],
            ['value' => self::FLAG_MEMORY, 'label' => __('Profile Memory')],
            ['value' => self::FLAG_CPU, 'label' => __('Profile Cpu')],
        ];
    }
}
