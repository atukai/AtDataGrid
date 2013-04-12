<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

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
                $params[$key] = $param instanceof \AtAdmin\DataGrid\Column
                              ? $row[$param->getName()]
                              : $params[$key] = $param;
        }
        
        $url = vsprintf($this->url, $params);
        
        return '<a href="' . $url . '">' . $value . '</a>';
    }

    /**
     * Set url to display hyperlink
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Set params for url
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }    
}