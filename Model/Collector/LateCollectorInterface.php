<?php

namespace Daseraf\Debug\Model\Collector;

interface LateCollectorInterface
{
    public function lateCollect(): LateCollectorInterface;
}
