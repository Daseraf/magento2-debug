<?php

namespace Daseraf\Debug\Helper;

class Formatter
{
    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \Daseraf\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function microtime(float $value, int $precision = null)
    {
        if ($precision === null) {
            $precision = $this->config->getTimePrecision();
        }

        return sprintf('%0.' . $precision . 'f', $value * 1000);
    }

    public function revertMicrotime(string $value): float
    {
        return (float) $value / 1000;
    }

    public function toMegaBytes(int $value, int $precision = 0)
    {
        return sprintf('%0.' . $precision . 'f', $value / 1024 / 1024);
    }

    public function formatBytes($bytes, $to, $decimal_places = 3)
    {
        $formulas = [
            'K' => number_format($bytes / 1024, $decimal_places),
            'M' => number_format($bytes / 1048576, $decimal_places),
            'G' => number_format($bytes / 1073741824, $decimal_places),
        ];

        return $formulas[$to] ?? 0;
    }

    public function percentage(float $value, int $precision = 5)
    {
        return sprintf('%.' . $precision . 'f%%', $value * 100);
    }
}
