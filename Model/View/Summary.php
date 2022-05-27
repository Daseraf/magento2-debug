<?php

namespace Daseraf\Debug\Model\View;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Model\ValueObject\Redirect;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Summary implements ArgumentInterface
{
    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Daseraf\Debug\Helper\Url
     */
    private $url;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\RedirectRendererFactory
     */
    private $redirectRendererFactory;

    public function __construct(
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Daseraf\Debug\Helper\Url $url,
        \Daseraf\Debug\Model\View\Renderer\RedirectRendererFactory $redirectRendererFactory
    ) {
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->url = $url;
        $this->redirectRendererFactory = $redirectRendererFactory;
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getProfilerUrl($token): string
    {
        return $this->url->getProfilerUrl($token);
    }

    public function renderRedirect(Redirect $redirect): string
    {
        return $this->redirectRendererFactory->create(['redirect' => $redirect])->render();
    }
}
