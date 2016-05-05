<?php

namespace AtDataGrid\Column\Decorator;

class HyperLink extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $url = '#';

    /**
     * @param $url
     */
    public function __construct($url)
    {
        $this->setUrl($url);
    }

    /**
     * @param $value
     * @param array $params
     * @return string
     */
    public function decorate($value, $params = [])
    {
        $p = [];
        $v = [];

        foreach ($params as $key => $val) {
            $p[] = '%'. $key .'%';
            $v[] = $val;
        }

        $url = str_replace($p, $v, $this->url);

        
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
}