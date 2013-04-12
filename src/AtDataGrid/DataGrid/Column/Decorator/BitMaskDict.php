<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class BitMaskDict extends AbstractDecorator
{
    protected $choises = array();
    protected $delimiter = '<br/>';

    public function __construct($options = array())
    {
        if (array_key_exists('choises', $options)) {
            $this->setChoises($options['choises']);
        }

        if (array_key_exists('delimiter', $options)) {
            $this->setDelimiter($options['delimiter']);
        }
    }

    public function setChoises($choises = array())
    {
        $this->choises = $choises;
    }

    public function setDelimiter($delimiter = '<br/>')
    {
        $this->delimiter = $delimiter;
    }
    
    public function render($value)
    {
        $rs = array();
        foreach ($this->choises as $k => $v) {
            if (($value & $k) == $k) {
                $rs[] = $v;
            }
        }
        return implode($this->delimiter, $rs);
    }
}