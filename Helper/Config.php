<?php

namespace Daseraf\Debug\Helper;

use Daseraf\Debug\Exception\CollectorNotFoundException;
use Daseraf\Debug\Model\Config\Source\ErrorHandler;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class Config
{
    public const CONFIG_ENABLED = 'daseraf_debug/general/active';
    public const CONFIG_ENABLED_ADMINHTML = 'daseraf_debug/general/active_adminhtml';
    public const CONFIG_ALLOWED_IPS = 'daseraf_debug/general/allowed_ips';
    public const CONFIG_ERROR_HANDLER = 'daseraf_debug/general/error_handler';
    public const CONFIG_TIME_PRECISION = 'daseraf_debug/time/precision';
    public const CONFIG_PERFORMANCE_COLOR = 'daseraf_debug/performance/%s_color';
    public const CONFIG_COLLECTOR_AJAX = 'daseraf_debug/collector/ajax';
    public const CONFIG_COLLECTOR_CACHE = 'daseraf_debug/collector/cache';
    public const CONFIG_COLLECTOR_CONFIG = 'daseraf_debug/collector/config';
    public const CONFIG_COLLECTOR_CUSTOMER = 'daseraf_debug/collector/customer';
    public const CONFIG_COLLECTOR_DATABASE = 'daseraf_debug/collector/database';
    public const CONFIG_COLLECTOR_EVENT = 'daseraf_debug/collector/event';
    public const CONFIG_COLLECTOR_PLUGIN = 'daseraf_debug/collector/plugin';
    public const CONFIG_COLLECTOR_LAYOUT = 'daseraf_debug/collector/layout';
    public const CONFIG_COLLECTOR_MEMORY = 'daseraf_debug/collector/memory';
    public const CONFIG_COLLECTOR_MODEL = 'daseraf_debug/collector/model';
    public const CONFIG_COLLECTOR_TIME = 'daseraf_debug/collector/time';
    public const CONFIG_COLLECTOR_TRANSLATION = 'daseraf_debug/collector/translation';
    public const CALLMAP_COLLECTOR_CONFIG = 'daseraf_debug/collector/callmap';
    public const XHPROF_FLAGS_CONFIG = 'daseraf_debug/collector/xhprof_flags';

    public const COLLECTORS = 'daseraf_debug/profiler/collectors';

    /**
     * @var \Magento\Framework\PhraseFactory
     */
    private $phraseFactory;

    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var \Daseraf\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    public function __construct(
        \Magento\Framework\PhraseFactory $phraseFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Daseraf\Debug\Model\Storage\HttpStorage $httpStorage
    ) {
        $this->phraseFactory = $phraseFactory;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->deploymentConfig = $deploymentConfig;
        $this->httpStorage = $httpStorage;
    }

    public function getErrorHandler(): string
    {
        if (!$this->isEnabled()) {
            return ErrorHandler::MAGENTO;
        }

        return $this->scopeConfig->getValue(self::CONFIG_ERROR_HANDLER, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isEnabled(): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        try {
            if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML && !$this->isAdminhtmlActive()) {
                return false;
            }
        } catch (LocalizedException $e) {
            return true;
        }

        return true;
    }

    public function isActive(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ENABLED, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    public function isAdminhtmlActive(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_ENABLED_ADMINHTML, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    public function isFrontend(): bool
    {
        return $this->appState->getAreaCode() === Area::AREA_FRONTEND;
    }

    public function getAllowedIPs(): array
    {
        return array_filter(array_map('trim', explode(',', $this->scopeConfig->getValue(
            self::CONFIG_ALLOWED_IPS,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ))));
    }

    /**
     * @SuppressWarnings(PHPMD.Superglobals)
     * @return bool
     */
    public function isAllowedIP(): bool
    {
        if (empty($this->getAllowedIPs())) {
            return true;
        }

        return in_array($_SERVER['REMOTE_ADDR'], $this->getAllowedIPs());
    }

    public function getTimePrecision(): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_TIME_PRECISION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getCollectors(): array
    {
        return $this->scopeConfig->getValue(self::COLLECTORS, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * @param string $name
     * @throws \Daseraf\Debug\Exception\CollectorNotFoundException
     * @return string
     */
    public function getCollectorClass(string $name): string
    {
        if (!isset($this->getCollectors()[$name])) {
            throw new CollectorNotFoundException(__('Collector "%1" not found', $name));
        }

        return $this->getCollectors()[$name];
    }

    public function isAjaxCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_AJAX,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCacheCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CACHE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isConfigCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CONFIG,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isCallmapCollectorEnabled(): bool
    {
        return ($this->scopeConfig->isSetFlag(
                self::CALLMAP_COLLECTOR_CONFIG,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT
            ) && extension_loaded('xhprof'));
    }

    public function getXhprofFlags(): array
    {
        $value = $this->scopeConfig->getValue(
            self::XHPROF_FLAGS_CONFIG,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );

        return explode(',', $value);
    }

    public function isCustomerCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_CUSTOMER,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isDatabaseCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_DATABASE,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && $this->deploymentConfig->get('db/connection/default/profiler/enabled');
    }

    public function isEventCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_EVENT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isPluginCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_PLUGIN,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isLayoutCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_LAYOUT,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function isMemoryCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_MEMORY,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isModelCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_MODEL,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTimeCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_TIME,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function isTranslationCollectorEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_COLLECTOR_TRANSLATION,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        ) && !$this->httpStorage->isFPCRequest();
    }

    public function getPerformanceColor(string $event): string
    {
        return $this->scopeConfig->getValue(
            sprintf(self::CONFIG_PERFORMANCE_COLOR, $event),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }
}
