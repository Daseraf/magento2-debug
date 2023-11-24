<?php
namespace Daseraf\Debug\App\Request;

class PathInfoProcessor implements \Magento\Framework\App\Request\PathInfoProcessorInterface
{

    /**
     * @var \Magento\Backend\Helper\Data
     */
    private $frontname;

    /**
     * @var \Magento\Store\App\Request\PathInfoProcessor
     */
    private $subject;

    /**
     * @param \Magento\Store\App\Request\PathInfoProcessor $subject
     * @param \Magento\Backend\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\App\Request\PathInfoProcessor $subject,
        \Daseraf\Debug\App\Area\FrontNameResolver $frontNameResolver
    ) {
        $this->subject = $subject;
        $this->frontname = $frontNameResolver;
    }

    /**
     * Process path info
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(\Magento\Framework\App\RequestInterface $request, $pathInfo)
    {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $firstPart = $pathParts[0];

        if ($firstPart != $this->frontNameResolver->getFrontName()) {
            return $this->subject->process($request, $pathInfo);
        }
        return $pathInfo;
    }
}
