<?php
namespace Daseraf\Debug\App;

use Magento\Framework\App\Action\Context;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

}
