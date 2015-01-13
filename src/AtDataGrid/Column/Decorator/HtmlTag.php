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
        $this->tag = (string) $tag;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param $value
     * @return string
     */
    public function decorate($value, $params = array())
    {
        $content = '<' . $this->tag . '>' . $value . '</' . $this->tag . '>';

        return $content;
    }
}