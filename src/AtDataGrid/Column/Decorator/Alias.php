<?php

namespace AtDataGrid\Column\Decorator;

class Alias extends AbstractDecorator
{
    /**
     * @var null
     */
    protected $renameTo;

    /**
     * @param null $renameTo
     */
    public function __construct($renameTo = null)
    {
        if ($renameTo) {
            $this->setRenameTo($renameTo);
        }
    }

    /**
     * @param $value
     * @return mixed
     */
    public function decorate($value, $params = array())
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
    public function setRenameTo($renameTo = array())
    {
        $this->renameTo = $renameTo;
    }
}