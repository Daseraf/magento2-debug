<?php
/**
 * Designed by Stanislav Matiavin
 */

declare(strict_types=1);

namespace Daseraf\Debug\App;

use Daseraf\Debug\App\Area\FrontNameResolver;

class Router extends \Magento\Framework\App\Router\Base
{
    /**
     * @var \Magento\Framework\UrlInterface $url
     */
    protected $_url;

    /**
     * List of required request parameters
     * Order sensitive
     *
     * @var string[]
     */
    protected $_requiredParams = ['areaFrontName', 'moduleFrontName', 'actionPath', 'actionName'];

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @var bool
     */
    protected $applyNoRoute = true;

    /**
     * @var string
     */
    protected $pathPrefix = 'debug';

    /**
     * Check whether redirect should be used for secure routes
     *
     * @return bool
     */
    protected function _shouldRedirectToSecure()
    {
        return false;
    }

    /**
     * Parse request URL params
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return array
     */
    protected function parseRequest(\Magento\Framework\App\RequestInterface $request)
    {
        $output = [];
        /** @var \Magento\Framework\App\Request\Http $request */
        $path = trim($request->getPathInfo(), '/');
        $path = FrontNameResolver::AREA_CODE . '/' . $path;

        $params = explode('/', $path ? $path : $this->pathConfig->getDefaultPath());
        foreach ($this->_requiredParams as $paramName) {
            $output[$paramName] = array_shift($params);
        }
        //$output['moduleFrontName'] = 'debug';
        for ($i = 0, $l = sizeof($params); $i < $l; $i += 2) {
            $output['variables'][$params[$i]] = isset($params[$i + 1]) ? urldecode($params[$i + 1]) : '';
        }

        return $output;
    }
}
