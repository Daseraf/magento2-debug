<?php

namespace Daseraf\Debug\Controller\Debug\Xhprof;

use Daseraf\Debug\Model\Collector\CallmapCollector;
use Daseraf\Debug\Model\Profiler;
use Daseraf\Debug\App\AbstractAction;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class Detail extends AbstractAction
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    public function __construct(
        \Daseraf\Debug\App\Action\Context $context,
        \Magento\Framework\View\LayoutInterface $layout,
        \Daseraf\Debug\Api\ProfileRepositoryInterface $profileRepository,
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage
    ) {
        parent::__construct($context);

        $this->layout = $layout;
        $this->profileRepository = $profileRepository;
        $this->profileMemoryStorage = $profileMemoryStorage;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $token = $request->getParam(Profiler::URL_TOKEN_PARAMETER, '');
        $profile = ($token === 'latest'
            ? $this->profileRepository->findLatest()
            : $this->profileRepository->getById($token));

        $panel = CallmapCollector::NAME;

        if (!$profile->hasCollector($panel)) {
            throw new LocalizedException(__('Panel "%s" is not available for token "%s".', $panel, $token));
        }

        /** @var \Magento\Framework\View\Result\Page $page */
        $page = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $page->addPageLayoutHandles([
            'panel' => 'xhprof',
            'profiler' => 'detail',
        ], 'debug');

        $this->profileMemoryStorage->write($profile);
        $collector = $profile->getCollector($panel);
        $panelBlock = $this->layout->getBlock('debug.profiler.panel.content');

        if (!$panelBlock) {
            throw new LocalizedException(__('Panel Block for "%1" is not available for token "%2".', $panel, $token));
        }

        $panelBlock->setCollector($collector);
        $resultRaw = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        return $resultRaw->setContents($panelBlock->toHtml());
    }
}
