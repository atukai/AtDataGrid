<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

// @todo use ZF2 I18n component
class Money extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $currency = '$';

    /**
     * @var string
     */
    protected $format = '%i';

    /**
     * @param $value
     * @return string
     */
    public function render($value)
    {
        if ($value) {
            return money_format($this->getFormat(), $value);
        }
    }

    /**
     * @param $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }
}