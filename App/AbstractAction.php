<?php
/**
 * Designed by Stanislav Matiavin
 */

declare(strict_types=1);
namespace Daseraf\Debug\App;

use Magento\Framework\App\Action\Context;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
}
