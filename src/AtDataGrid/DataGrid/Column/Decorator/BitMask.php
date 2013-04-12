<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class BitMask extends AbstractDecorator
{
    /**
     * @var array
     */
    protected $statuses = array();

    /**
     * @param array $statuses
     */
    public function __construct($statuses = array())
    {
        if ($statuses) {
            $this->setStatuses($statuses);            
        }
    }

    /**
     * @param array $statuses
     */
    public function setStatuses($statuses = array())
    {
        $this->statuses = $statuses;
    }
    
    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function render($value)
    {
        $str = '';
        foreach ($this->statuses as $name => $status) {
            if ($this->checkStatus($status, $value)) {
                $str .= '<div>' . $name . ': <b>Yes</b></div>';
            } else {
                $str .= '<div>' . $name . ': No</div>';
            }    
        }
        
        return $str;
    }
    
    /**
     * @param $status
     * @param $value
     * @return int
     */
    protected function checkStatus($status, $value)
    {
        return $value & $status;
    }    
}