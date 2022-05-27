<?php

namespace Daseraf\Debug\Model\View\Renderer;

use Magento\Framework\View\Element\Template;

class RedirectRenderer implements RendererInterface
{
    public const TEMPLATE = 'Daseraf_Debug::renderer/redirect.phtml';

    /**
     * @var \Daseraf\Debug\Model\ValueObject\Redirect
     */
    private $redirect;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var \Daseraf\Debug\Helper\Url
     */
    private $url;

    public function __construct(
        \Daseraf\Debug\Model\ValueObject\Redirect $redirect,
        \Magento\Framework\View\LayoutInterface $layout,
        \Daseraf\Debug\Helper\Url $url
    ) {
        $this->redirect = $redirect;
        $this->layout = $layout;
        $this->url = $url;
    }

    public function render(): string
    {
        return $this->layout->createBlock(Template::class)
            ->setTemplate(self::TEMPLATE)
            ->setProfilerUrl($this->url->getProfilerUrl($this->redirect->getToken()))
            ->setRedirect($this->redirect)
            ->toHtml();
    }
}
