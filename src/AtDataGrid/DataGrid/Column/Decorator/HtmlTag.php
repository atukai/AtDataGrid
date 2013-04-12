<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class HtmlTag extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $tag = 'span';

    /**
     * @var string
     */
    protected $placement = self::REPLACE;

    /**
     * @param string $tag
     */
    public function __construct($tag)
    {
        $this->tag = (string) $tag;
    }

    /**
     * @param $tag
     * @return HtmlTag
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
     * Wrap valueinto tag
     *
     * @param $value
     * @return string
     */
    public function render($value)
    {
        $tag = $this->getTag();
        $content = '<' . $tag . '>' . $value . '</' . $tag . '>';

        return $content;
    }
}