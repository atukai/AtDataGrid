<?php

namespace AtDataGrid\Column\Decorator;

class HtmlTag extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $tag = 'span';

    /**
     * @param $tag
     */
    public function __construct($tag)
    {
        $this->tag = (string)$tag;
    }

    /**
     * @param $value
     * @param array $params
     * @return string
     */
    public function decorate($value, array $params = [])
    {
        return '<' . $this->tag . '>' . $value . '</' . $this->tag . '>';
    }
}