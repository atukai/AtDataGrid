<?php

namespace AtDataGrid\Column\Decorator;

use AtDataGrid\Column\Column;

class HyperLink extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $url = '#';

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function render($value, $row)
    {
        $params = array();
        
        foreach ($this->params as $key => $param) {
                $params[$key] = $param instanceof Column
                              ? $row[$param->getName()]
                              : $params[$key] = $param;
        }
        
        $url = vsprintf($this->url, $params);
        
        return '<a href="' . $url . '">' . $value . '</a>';
    }

    /**
     * @param $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }    
}