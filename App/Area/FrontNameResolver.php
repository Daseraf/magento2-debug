<?php

namespace Daseraf\Debug\App\Area;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class FrontNameResolver implements \Magento\Framework\App\Area\FrontNameResolverInterface
{
    const XML_PATH_USE_CUSTOM_DEBUG_PATH = 'debug/url/use_custom_path';

    const XML_PATH_CUSTOM_DEBUG_PATH = 'debug/url/custom_path';

    const XML_PATH_USE_CUSTOM_DEBUG_URL = 'debug/url/use_custom';

    const XML_PATH_CUSTOM_DEBUG_URL = 'debug/url/custom';

    /**
     * debug area code
     */
    const AREA_CODE = 'debug';

    /**
     * @var array
     */
    protected $standardPorts = ['http' => '80', 'https' => '443'];

    /**
     * @var string
     */
    protected $defaultFrontName = 'debug';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $config;


    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param \Magento\Backend\App\Config $config
     * @param DeploymentConfig $deploymentConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Config $config,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve area front name
     *
     * @param bool $checkHost If true, verify front name is valid for this url (hostname is correct)
     * @return string|bool
     */
    public function getFrontName($checkHost = false)
    {
        if ($checkHost && !$this->isHostDebugBackend()) {
            return false;
        }
        $isCustomPathUsed = (bool)(string)$this->config->getValue(self::XML_PATH_USE_CUSTOM_DEBUG_PATH);
        if ($isCustomPathUsed) {
            return (string)$this->config->getValue(self::XML_PATH_CUSTOM_DEBUG_PATH);
        }
        return $this->defaultFrontName;
    }

    /**
     * Return whether the host from request is the debug host
     *
     * @return bool
     */
    public function isHostDebugBackend()
    {
        if ($this->scopeConfig->getValue(self::XML_PATH_USE_CUSTOM_DEBUG_URL, ScopeInterface::SCOPE_STORE)) {
            $debugUrl = $this->scopeConfig->getValue(self::XML_PATH_CUSTOM_DEBUG_URL, ScopeInterface::SCOPE_STORE);
        } else {
            $debugUrl = $this->scopeConfig->getValue(Store::XML_PATH_UNSECURE_BASE_URL, ScopeInterface::SCOPE_STORE);
        }

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        return stripos($this->getHostNameWithPort($debugUrl), $host) !== false;
    }

    /**
     * Get host with port
     *
     * @param string $url
     * @return mixed|string
     */
    private function getHostNameWithPort($debugUrl)
    {
        $schemeVar = parse_url(trim($debugUrl), PHP_URL_SCHEME);
        $hostVar = parse_url(trim($debugUrl), PHP_URL_HOST);
        $portVar = parse_url(trim($debugUrl), PHP_URL_PORT);
        if (!$portVar) {
            $portVar = isset($this->standardPorts[$schemeVar]) ? $this->standardPorts[$schemeVar] : null;
        }
        return isset($portVar) ? $hostVar . ':' . $portVar : $hostVar;
    }
}
