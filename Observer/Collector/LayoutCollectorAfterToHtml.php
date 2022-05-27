<?php

namespace Daseraf\Debug\Observer\Collector;

use Daseraf\Debug\Model\Collector\LayoutCollector;
use Daseraf\Debug\Model\ValueObject\Block;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LayoutCollectorAfterToHtml implements ObserverInterface
{
    /**
     * @var \Daseraf\Debug\Model\Collector\LayoutCollector
     */
    private $layoutCollector;

    public function __construct(
        \Daseraf\Debug\Model\Collector\LayoutCollector $layoutCollector
    ) {
        $this->layoutCollector = $layoutCollector;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Element\AbstractBlock $block */
        $block = $observer->getBlock();

        $renderedTimestamp = microtime(true);
        $renderTime = $renderedTimestamp - $block->getData(LayoutCollector::BLOCK_START_RENDER_KEY);

        $block->addData([
            LayoutCollector::BLOCK_STOP_RENDER_KEY => $renderedTimestamp,
            LayoutCollector::RENDER_TIME => $renderTime,
        ]);

        $this->layoutCollector->log(new Block($block));
    }
}
