<?php

namespace Daseraf\Debug\Controller\Debug\Profiler;

use Daseraf\Debug\App\AbstractAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\FileSystemException;

class Purge extends AbstractAction implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileFileStorage
     */
    private $profileFileStorage;

    /**
     * @var \Daseraf\Debug\Logger\Logger
     */
    private $logger;

    public function __construct(
        \Daseraf\Debug\App\Action\Context $context,
        \Daseraf\Debug\Model\Storage\ProfileFileStorage $profileFileStorage,
        \Daseraf\Debug\Logger\Logger $logger
    ) {
        parent::__construct($context);
        $this->profileFileStorage = $profileFileStorage;
        $this->logger = $logger;
    }

    public function execute()
    {
        try {
            $this->profileFileStorage->purge();
        } catch (FileSystemException $e) {
            $this->logger->critical($e);
        }

        /** @var $resultRedirect */
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
            ->setUrl($this->_redirect->getRefererUrl());
    }
}
