<?php

namespace Daseraf\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class LayoutNodeRenderer implements RendererInterface
{
    public const TEMPLATE = 'Daseraf_Debug::renderer/layout/node.phtml';

    /**
     * @var \Daseraf\Debug\Model\ValueObject\LayoutNode
     */
    private $node;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\LayoutNodeRendererFactory
     */
    private $layoutNodeRendererFactory;

    /**
     * @var \Daseraf\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Daseraf\Debug\Model\ValueObject\LayoutNode $node,
        \Magento\Framework\View\LayoutInterface $layout,
        \Daseraf\Debug\Model\View\Renderer\LayoutNodeRendererFactory $layoutNodeRendererFactory,
        \Daseraf\Debug\Helper\Formatter $formatter
    ) {
        $this->node = $node;
        $this->layout = $layout;
        $this->layoutNodeRendererFactory = $layoutNodeRendererFactory;
        $this->formatter = $formatter;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setData([
                'node' => $this->node,
                'formatter' => $this->formatter,
                'layout_node_renderer' => $this->layoutNodeRendererFactory,
            ])
            ->toHtml();
    }
}
