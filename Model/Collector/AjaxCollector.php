<?php

namespace Daseraf\Debug\Model\Collector;

class AjaxCollector implements CollectorInterface
{
    public const NAME = 'ajax';

    /**
     * @var \Daseraf\Debug\Helper\Config
     */
    private $config;

    public function __construct(
        \Daseraf\Debug\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function collect(): CollectorInterface
    {
        // Nothing to collect here
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->config->isAjaxCollectorEnabled();
    }

    public function getData(): array
    {
        return [];
    }

    public function setData(array $data): CollectorInterface
    {
        return $this;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getStatus(): string
    {
        return self::STATUS_DEFAULT;
    }
}
