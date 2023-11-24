<?php

namespace Daseraf\Debug\Controller\Debug\Profiler;

use Daseraf\Debug\Model\Profile\Criteria;
use Daseraf\Debug\Model\Profiler;
use Daseraf\Debug\App\AbstractAction;
use Magento\Framework\Controller\ResultFactory;

class Search extends AbstractAction implements \Magento\Framework\App\ActionInterface
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(
        \Daseraf\Debug\App\Action\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Daseraf\Debug\Api\ProfileRepositoryInterface $profileRepository
    ) {
        parent::__construct($context);

        $this->layout = $layout;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest();

        if (!empty($token = $request->getParam('_token'))) {
            return $this->_redirect('debug/profiler/info', [Profiler::URL_TOKEN_PARAMETER => $token]);
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $page->addPageLayoutHandles([
            'profiler' => 'info',
        ], 'debug');

        $criteria = Criteria::createFromRequest($request);

        $this->layout->getBlock('debug.profiler.panel.content')->addData([
            'results' => $this->profileRepository->find($criteria),
            'criteria' => $criteria,
        ]);

        return $page;
    }
}
