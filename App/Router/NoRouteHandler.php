<?php
namespace Daseraf\Debug\App\Router;

class NoRouteHandler implements \Magento\Framework\App\Router\NoRouteHandlerInterface
{
    /**
     * @var \Daseraf\Debug\App\Area\FrontNameResolver
     */
    protected $frontNameResolver;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface
     */
    protected $routeConfig;

    /**
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     */
    public function __construct(
        \Daseraf\Debug\App\Area\FrontNameResolver $frontNameResolver,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig
    ) {
        $this->frontNameResolver = $frontNameResolver;
        $this->routeConfig = $routeConfig;
    }

    /**
     * Check and process no route request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function process(\Magento\Framework\App\RequestInterface $request)
    {
        $requestPathParams = explode('/', trim($request->getPathInfo(), '/'));
        $areaFrontName = array_shift($requestPathParams);

        if ($areaFrontName === $this->frontNameResolver->getFrontName(true)) {
            $moduleName = $this->routeConfig->getRouteFrontName('debug');
            $actionNamespace = 'noroute';
            $actionName = 'index';
            $request->setModuleName($moduleName)->setControllerName($actionNamespace)->setActionName($actionName);
            return true;
        }
        return false;
    }
}
