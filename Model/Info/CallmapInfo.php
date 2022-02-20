<?php

namespace ClawRock\Debug\Model\Info;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Model\Customer;

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
