<?php

namespace ClawRock\Debug\Model\ValueObject;

class Plugin
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $executionTime;

    /**
     * @var int
     */
    private $executionCount;

    public function __construct(
        string $class,
        string $name,
        int $sortOrder,
        string $method,
        string $type,
        float $executionTime = 0,
        int $executionCount = 0
    ) {
        $this->class = $class;
        $this->name = $name;
        $this->sortOrder = $sortOrder;
        $this->method = $method;
        $this->type = $type;
        $this->executionTime = $executionTime;
        $this->executionCount = $executionCount;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    /**
     * @return int
     */
    public function getExecutionCount(): int
    {
        return $this->executionCount;
    }
}
