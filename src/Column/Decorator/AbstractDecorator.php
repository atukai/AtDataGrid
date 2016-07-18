<?php

namespace AtDataGrid\Column\Decorator;

abstract class AbstractDecorator implements DecoratorInterface
{
    /**
     * Placement constants
     */
    const APPEND  = 'append';
    const PREPEND = 'prepend';
    const REPLACE = 'replace';

    /**
     * Default placement: replace
     * @var string
     */
    protected $placement = self::REPLACE;

    /**
     * Default separator: ' '
     * @var string
     */
    protected $separator = ' ';

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
     * @return $this
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