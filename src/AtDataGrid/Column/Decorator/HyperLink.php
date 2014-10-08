<?php

namespace AtDataGrid\Column\Decorator;

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
     * @param $url
     */
    public function __construct($url)
    {
        $this->setUrl($url);
    }

    /**
     * @param $value
     * @return string
     */
    public function decorate($value)
    {
        $params = array($value);

        var_dump($params);exit;

        foreach ($this->params as $key => $param) {
            /*$params[$key] = $param instanceof Column
                          ? $row[$param->getName()]
                          : $params[$key] = $param;*/
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