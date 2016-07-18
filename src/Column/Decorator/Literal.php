<?php

namespace AtDataGrid\Column\Decorator;

// @todo Use zf2 escaper component
class Literal extends AbstractDecorator
{
    /**
     * Callback for escaping.
     *
     * @var string
     */
    protected $escape = 'htmlspecialchars';

    /**
     * Encoding to use in escaping mechanisms; defaults to utf-8 (UTF-8)
     * @var string
     */
    protected $encoding = 'UTF-8';

    /**
     * Sets the _escape() callback.
     *
     * @param mixed $spec The callback for _escape() to use.
     * @return Literal
     */
    public function setEscape($spec)
    {
        $this->escape = $spec;
        return $this;
    }    

    /**
     * Set encoding to use with htmlentities() and htmlspecialchars()
     *
     * @param string $encoding
     * @return Literal
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Return current escape encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, $params = array())
    {
        if (in_array($this->escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->escape, $value, ENT_COMPAT, $this->encoding);
        }

        return call_user_func($this->escape, $value);
    }
}