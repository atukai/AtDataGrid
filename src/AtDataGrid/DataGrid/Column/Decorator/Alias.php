<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class Alias extends AbstractDecorator
{
    /**
     * @var null
     */
    protected $renameTo = null;
    
    /**
     * @param null $renameTo
     */
    public function __construct($renameTo = null)
    {
        if (null != $renameTo) {
            $this->setRenameTo($renameTo);
        }
    }
    
    /**
     * @param $value
     * @param $row
     * @return
     */
    public function render($value)
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
     * @return void
     */
    public function setRenameTo(Array $renameTo = array())
    {
        $this->renameTo = $renameTo;
    }       
}