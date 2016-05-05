<?php

namespace AtDataGrid\Column\Decorator;

class BitMask extends AbstractDecorator
{
    protected $statuses;

    /**
     * @param array $statuses
     */
    public function __construct($statuses = array())
    {
        $this->setStatuses($statuses);
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
     * @param array $params
     * @return string
     */
    public function decorate($value, $params = array())
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