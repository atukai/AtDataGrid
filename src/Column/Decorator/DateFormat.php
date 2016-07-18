<?php

namespace AtDataGrid\Column\Decorator;

// @todo Use zf2 i18n component
class DateFormat extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $format = 'd.m.Y H:i';

    /**
     * @param string $format
     */
    public function __construct($format = null)
    {
    	if ($format) {
    		$this->setFormat($format);
    	}
    }

    /**
     * @param  $format
     * @return void
     */
    public function setFormat($format)
    {
    	$this->format = $format;
    }

    /**
     * @param $value
     * @param array $params
     * @return bool|string
     */
    public function decorate($value, $params = [])
    {
        if ($value) {
            return date($this->format, strtotime($value));
        }

        return $value;
    }
}