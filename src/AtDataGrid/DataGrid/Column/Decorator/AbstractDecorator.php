<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

abstract class AbstractDecorator implements DecoratorInterface
{
    /**
     * Placement constants
     */
    const APPEND  = 'append';
    const PREPEND = 'prepend';
    const REPLACE = 'replace';

    /**
     * Default placement: append
     * @var string
     */
    protected $placement;

    /**
     * Default separator: ' '
     * @var string
     */
    protected $separator = ' ';

    /**
     * @param string $placement
     */
    public function __construct($placement = self::APPEND)
    {
        $this->setPlacement($placement);
    }

    /**
     * @param $placement
     * @return AbstractDecorator
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param $separator
     * @return AbstractDecorator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }
}