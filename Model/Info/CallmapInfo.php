<?php

namespace Daseraf\Debug\Model\Info;

class CallmapInfo
{
    /**
     * @var array
     */
    private $runData = [];

    public function setRunData($data): void
    {
        $this->runData = $data;
    }

    public function getRunData(): array
    {
        return $this->runData;
    }
}
