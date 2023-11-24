<?php

namespace Daseraf\Debug\Model\View;

use Daseraf\Debug\Api\Data\ProfileInterface;
use Daseraf\Debug\Helper\Formatter;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Zend\Stdlib\ParametersInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class Profiler implements ArgumentInterface
{
    /**
     * @var \Daseraf\Debug\Model\View\Renderer\TraceRendererFactory
     */
    private $traceRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\LayoutGraphRendererFactory
     */
    private $layoutGraphRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\ParametersRendererFactory
     */
    private $parametersRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\QueryParametersRendererFactory
     */
    private $queryParametersRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\QueryRendererFactory
     */
    private $queryRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\QueryListFactory
     */
    private $queryListRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\TableRendererFactory
     */
    private $tableRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\View\Renderer\VarRendererFactory
     */
    private $varRendererFactory;

    /**
     * @var \Daseraf\Debug\Model\Storage\ProfileMemoryStorage
     */
    private $profileMemoryStorage;

    /**
     * @var \Daseraf\Debug\Helper\Formatter
     */
    private $formatter;

    public function __construct(
        \Daseraf\Debug\Model\View\Renderer\TraceRendererFactory $traceRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\LayoutGraphRendererFactory $layoutGraphRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\ParametersRendererFactory $parametersRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\QueryParametersRendererFactory $queryParametersRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\QueryRendererFactory $queryRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\QueryListRendererFactory $queryListRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\TableRendererFactory $tableRendererFactory,
        \Daseraf\Debug\Model\View\Renderer\VarRendererFactory $varRendererFactory,
        \Daseraf\Debug\Model\Storage\ProfileMemoryStorage $profileMemoryStorage,
        \Daseraf\Debug\Helper\Formatter $formatter
    ) {
        $this->traceRendererFactory = $traceRendererFactory;
        $this->layoutGraphRendererFactory = $layoutGraphRendererFactory;
        $this->parametersRendererFactory = $parametersRendererFactory;
        $this->queryParametersRendererFactory = $queryParametersRendererFactory;
        $this->queryRendererFactory = $queryRendererFactory;
        $this->queryListRendererFactory = $queryListRendererFactory;
        $this->tableRendererFactory = $tableRendererFactory;
        $this->varRendererFactory = $varRendererFactory;
        $this->profileMemoryStorage = $profileMemoryStorage;
        $this->formatter = $formatter;
    }

    public function renderLayoutGraph(array $blocks, float $totalTime, $profile): string
    {
        return $this->layoutGraphRendererFactory->create([
            'blocks' => $blocks,
            'totalRenderTime' => $totalTime,
            'profile' => $profile
        ])->render();
    }

    public function renderTrace(array $trace): string
    {
        return $this->traceRendererFactory->create(['trace' => $trace])->render();
    }

    public function renderParameters(ParametersInterface $parameters): string
    {
        return $this->parametersRendererFactory->create(['parameters' => $parameters])->render();
    }

    public function renderQueryParameters(string $query, array $parameters): string
    {
        return $this->queryParametersRendererFactory->create([
            'query' => $query,
            'parameters' => $parameters,
        ])->render();
    }

    public function renderQuery(string $query): string
    {
        return $this->queryRendererFactory->create(['query' => $query])->render();
    }

    public function renderQueryList(array $queries): string
    {
        return $this->queryListRendererFactory->create(['queries' => $queries])->render();
    }

    public function renderTable(array $items, array $labels = []): string
    {
        return $this->tableRendererFactory->create(['items' => $items, 'labels' => $labels])->render();
    }

    public function dump($variable): string
    {
        return $this->varRendererFactory->create(['variable' => $variable])->render();
    }

    public function getProfile(): ProfileInterface
    {
        return $this->profileMemoryStorage->read();
    }

    public function getFormatter(): Formatter
    {
        return $this->formatter;
    }
}
