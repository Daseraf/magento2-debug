<?php

namespace Daseraf\Debug\Model\View\Renderer;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Model\Collector\CacheCollector;
use Daseraf\Debug\Model\ValueObject\Block;
use Daseraf\Debug\Model\ValueObject\CacheAction;
use Magento\Framework\View\Element\Template;

class LayoutGraphRenderer implements RendererInterface
{
    public const TEMPLATE = 'Daseraf_Debug::renderer/layout/graph.phtml';

    /**
     * @var \Daseraf\Debug\Model\ValueObject\Block[]
     */
    private $blocks;

    /**
     * @var float
     */
    private $totalRenderTime;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Daseraf\Debug\Model\ValueObject\LayoutNodeFactory
     */
    private $layoutNodeFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\LayoutNodeRendererFactory
     */
    private $layoutNodeRendererFactory;

    /**
     * @var \Daseraf\Debug\Helper\Formatter
     */
    private $formatter;

    /**
     * @var ProfileInterface $profile
     */
    private  $profile;

    private $cacheList;

    public function __construct(
        array $blocks,
        float $totalRenderTime,
        ProfileInterface $profile,
        \Magento\Framework\View\LayoutInterface $layout,
        \Daseraf\Debug\Model\ValueObject\LayoutNodeFactory $layoutNodeFactory,
        \Daseraf\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \Daseraf\Debug\Helper\Formatter $formatter
    ) {
        $this->blocks = $blocks;
        $this->totalRenderTime = $totalRenderTime;
        $this->profile = $profile;
        $this->layout = $layout;
        $this->layoutNodeFactory = $layoutNodeFactory;
        $this->layoutNodeRendererFactory = $layoutNodeRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        // Microtime formatting revert for calculations
        $this->totalRenderTime = $this->formatter->revertMicrotime($this->totalRenderTime);

        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'nodes' => $this->createNodes(),
                'layout_node_renderer' => $this->layoutNodeRendererFactory,
            ])
            ->toHtml();
    }

    private function createNodes(): array
    {
        $nodes = [];

        foreach ($this->blocks as $block) {
            if (!$block->getParentId()) {
                $children = $this->resolveChildren($block);
                $nodes[] = $this->layoutNodeFactory->create([
                    'block' => $block,
                    'layoutRenderTime' => $this->totalRenderTime,
                    'children' => $children,
                    'prefix' => '',
                    'cacheStatus' => $this->getCacheStatusByBlock($block->getCacheKey())
                ]);
            }
        }

        return $nodes;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param \Daseraf\Debug\Model\ValueObject\Block $block
     * @param string $prefix
     * @param bool $sibling
     * @return array
     */
    private function resolveChildren(Block $block, string $prefix = '', bool $sibling = false)
    {
        $children = [];
        $childrenCount = count($block->getChildren());
        $i = 1;
        $prefix .= $sibling ? '│&nbsp;' : '&nbsp;';
        foreach ($block->getChildren() as $childId) {
            $child = array_filter($this->blocks, function ($block) use ($childId) {
                /** @var \Daseraf\Debug\Model\ValueObject\Block $block */
                return $block->getName() === $childId;
            });
            if (($child = array_shift($child)) === null) {
                continue;
            }
            $childChildren = $this->resolveChildren($child, $prefix, $i++ !== $childrenCount);
            $children[$childId] = $this->layoutNodeFactory->create([
                'block' => $child,
                'layoutRenderTime' => $this->totalRenderTime,
                'prefix' => $prefix,
                'children' => $childChildren,
                'cacheStatus' => $this->getCacheStatusByBlock($child->getCacheKey())
            ]);
        }

        return $children;
    }

    public function getCacheStatusByBlock($cacheKey)
    {
        $cacheCollector = $this->profile->getCollector('cache');
        if (!$cacheCollector) {
            return __('cache collector disabled');
        }

        if (!$this->cacheList) {
            $this->cacheList = $cacheCollector->getCacheLog();
        }

        if (isset($this->cacheList[$cacheKey])) {
            /** @var CacheAction $cacheStatus */
            $cacheStatus = $this->cacheList[$cacheKey];
            return $cacheStatus->getName();
        }

        return __('not cached');
    }

    protected function sortByTime($a, $b)
    {
        $a = $a->getRenderTime();
        $b = $b->getRenderTime();
        if ($a == $b) {
            return 0;
        }
        return ($a > $b) ? -1 : 1;
    }
}
