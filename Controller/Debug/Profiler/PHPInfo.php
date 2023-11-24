<?php

namespace Daseraf\Debug\Controller\Debug\Profiler;

use Daseraf\Debug\App\AbstractAction;
use Magento\Framework\Controller\ResultFactory;

class PHPInfo extends AbstractAction implements \Magento\Framework\App\ActionInterface
{
    public function execute()
    {
        phpinfo();

        return $this->resultFactory->create(ResultFactory::TYPE_RAW);
    }
}
