<?php

namespace Daseraf\Debug\Controller\Debug\Profiler;

use Daseraf\Debug\Model\Profiler;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Toolbar extends Action
{
    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Daseraf\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
        parent::__construct($context);
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->profileRepository = $profileRepository;
    }

    public function execute()
    {
        $token = $this->getRequest()->getParam(Profiler::URL_TOKEN_PARAMETER);
        $profile = $this->profileRepository->getById($token);
        $this->profileMemoryStorage->write($profile);

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
