<?php

namespace AtDataGrid\Column\Decorator;

class Alias extends AbstractDecorator
{
    /**
     * @var array
     */
    protected $renameTo;

    /**
     * @param array $renameTo
     */
    public function __construct($renameTo = [])
    {
        $this->setRenameTo($renameTo);
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, $params = [])
    {
        if (!isset($this->renameTo)) {
            return $value;
        }
        
        if (isset($this->renameTo[$value])) {
            return $this->renameTo[$value];
        }
        
        return $value;
    }

    /**
     * @param array $renameTo
     */
    public function setRenameTo($renameTo = [])
    {
        $this->renameTo = $renameTo;
    }
}