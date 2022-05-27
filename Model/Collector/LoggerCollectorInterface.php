<?php

namespace Daseraf\Debug\Model\Collector;

use Daseraf\Debug\Logger\LoggableInterface;

interface LoggerCollectorInterface
{
    public function log(LoggableInterface $value): LoggerCollectorInterface;
}
