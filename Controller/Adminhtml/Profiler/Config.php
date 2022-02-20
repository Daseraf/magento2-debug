<?php

namespace ClawRock\Debug\Controller\Adminhtml\Profiler;

use Magento\Backend\App\Action;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Config extends Action
{
    /**
     * @var array
     */
    protected $_publicActions = ['config']; // phpcs:ignore

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('admin/system_config/edit', [
            'section' => 'clawrock_debug',
            'key' => $this->_url->getSecretKey('adminhtml', 'system_config', 'edit'),
        ]);
    }
}
