<?php

namespace Daseraf\Debug\Model;

use Daseraf\Debug\Model\Collector\CollectorInterface;
use Daseraf\Debug\Model\Collector\LateCollectorInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\HTTP\PhpEnvironment\Response;
use Magento\Framework\Profiler as MagentoProfiler;

class Profiler
{
    public const URL_TOKEN_PARAMETER = 'token';
    public const URL_PANEL_PARAMETER = 'panel';
    public const TOOLBAR_FULL_ACTION_NAME = 'debug_profiler_toolbar';

    /**
     * @var CollectorInterface[]|null
     */
    private $dataCollectors = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    /**
     * @var \Daseraf\Debug\Model\ProfileFactory
     */
    private $profileFactory;

    /**
     * @var \Daseraf\Debug\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Daseraf\Debug\Helper\Injector
     */
    private $injector;

    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Daseraf\Debug\Api\ProfileRepositoryInterface
     */
    private $profileRepository;

    /**
     * @var \Daseraf\Debug\Model\Storage\HttpStorage
     */
    private $httpStorage;

    /**
     * @var \Daseraf\Debug\Logger\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Daseraf\Debug\Helper\Config $config,
        \Daseraf\Debug\Model\ProfileFactory $profileFactory,
        \Daseraf\Debug\Helper\Url $urlHelper,
        \Daseraf\Debug\Helper\Injector $injector,
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Daseraf\Debug\Api\ProfileRepositoryInterface $profileRepository,
        \Daseraf\Debug\Model\Storage\HttpStorage $httpStorage,
        \Daseraf\Debug\Logger\Logger $logger
    ) {
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->profileFactory = $profileFactory;
        $this->urlHelper = $urlHelper;
        $this->injector = $injector;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->profileRepository = $profileRepository;
        $this->httpStorage = $httpStorage;
        $this->logger = $logger;
    }

    public function run(Request $request, Response $response)
    {
        if (!$this->config->isAllowedIP()) {
            return;
        }

        try {
            $profile = $this->collect($request, $response);
            $this->profileMemoryStorage->write($profile);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            return;
        }

        $token = null;
        /** @var \Zend\Http\Header\HeaderInterface $header */
        foreach ($response->getHeaders() as $header) {
            if ($header->getFieldName() === 'X-Debug-Token') {
                $token = $header->getFieldValue();
                break;
            }
        }

        if ($token) {
            $response->setHeader('X-Debug-Token-Link', $this->urlHelper->getProfilerUrl($token));
        }

        $this->injector->inject($request, $response, $token);

        register_shutdown_function([$this, 'onTerminate']);
    }

    public function getDataCollector($name)
    {
        $collectors = $this->getDataCollectors();

        return $collectors[$name] ?? false;
    }

    public function getDataCollectors()
    {
        if ($this->dataCollectors === null) {
            $this->dataCollectors = [];

            $collectors = $this->config->getCollectors();
            foreach ($collectors as $class) {
                $collector = $this->objectManager->get($class);
                if (!$collector instanceof CollectorInterface) {
                    throw new \InvalidArgumentException('Collector must implement "CollectorInterface"');
                }

                if ($collector->isEnabled()) {
                    $this->dataCollectors[$collector->getName()] = $collector;
                }
            }
        }

        return $this->dataCollectors;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     * @param \Magento\Framework\HTTP\PhpEnvironment\Response $response
     * @return \Daseraf\Debug\Model\Profile
     */
    public function collect(Request $request, Response $response)
    {
        $start = microtime(true);
        /** @var \Daseraf\Debug\Model\Profile $profile */
        $profile = $this->profileFactory->create(['token' => substr(hash('sha256', uniqid(mt_rand(), true)), 0, 6)]);
        $profile->setUrl($request->getRequestString() ? $request->getRequestString() : '/');
        $profile->setMethod($request->getMethod());
        $profile->setRoute($this->urlHelper->getRequestFullActionName($request));
        $profile->setStatusCode($response->getHttpResponseCode());
        $profile->setIp($request->getClientIp());

        $response->setHeader('X-Debug-Token', $profile->getToken());

        $this->httpStorage->setRequest($request);
        $this->httpStorage->setResponse($response);

        $profileKey = 'DEBUG::profiler::collect';
        MagentoProfiler::start($profileKey);
        foreach ($this->getDataCollectors() as $collector) {
            $profileCollectorKey = $profileKey . '::' . $collector->getName();
            /** @var CollectorInterface $collector */
            MagentoProfiler::start($profileCollectorKey);
            $collector->collect();
            MagentoProfiler::stop($profileCollectorKey);
            $profile->addCollector($collector);
        }
        MagentoProfiler::stop($profileKey);
        $profile->setTime(time());
        $collectTime = microtime(true) - $start;
        $profile->setCollectTime($collectTime);

        return $profile;
    }

    public function onTerminate()
    {
        try {
            $profile = $this->profileMemoryStorage->read();
            foreach ($profile->getCollectors() as $collector) {
                if ($collector instanceof LateCollectorInterface) {
                    $collector->lateCollect();
                }
            }

            $this->profileRepository->save($profile);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }
}
