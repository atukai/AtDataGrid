<?php

namespace AtDataGrid\Column\Decorator;

class Alias extends AbstractDecorator
{
    /**
     * @var array
     */
    protected $mapping;

    /**
     * Alias constructor.
     * @param array $mapping
     */
    public function __construct(array $mapping)
    {
        $this->setMapping($mapping);
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, array $params = [])
    {
        if (isset($this->mapping[$value])) {
            return $this->mapping[$value];
        }
        
        return $value;
    }

    /**
     * @param array $mapping
     */
    public function setMapping(array $mapping = [])
    {
        $this->mapping = $mapping;
    }
}