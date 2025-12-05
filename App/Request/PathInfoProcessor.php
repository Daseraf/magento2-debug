<?php
/**
 * Designed by Stanislav Matiavin
 */

declare(strict_types=1);
namespace Daseraf\Debug\App\Request;

use Daseraf\Debug\App\Area\FrontNameResolver;
use Magento\Backend\Helper\Data;
use Magento\Framework\App\Request\PathInfoProcessorInterface;
use Magento\Framework\App\RequestInterface;

class PathInfoProcessor implements PathInfoProcessorInterface
{
    /**
     * @var Data
     */
    private $frontname;

    /**
     * @var \Magento\Store\App\Request\PathInfoProcessor
     */
    private $subject;

    /**
     * @param \Magento\Store\App\Request\PathInfoProcessor $subject
     * @param FrontNameResolver $frontNameResolver
     */
    public function __construct(
        \Magento\Store\App\Request\PathInfoProcessor $subject,
        FrontNameResolver $frontNameResolver
    ) {
        $this->subject = $subject;
        $this->frontname = $frontNameResolver;
    }

    /**
     * Process path info
     *
     * @param RequestInterface $request
     * @param string $pathInfo
     * @return string
     */
    public function process(RequestInterface $request, $pathInfo)
    {
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $firstPart = $pathParts[0];

        if ($firstPart != $this->frontname->getFrontName()) {
            return $this->subject->process($request, $pathInfo);
        }

        return $pathInfo;
    }
}
