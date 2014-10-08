<?php

namespace AtDataGrid\Column\Decorator;

class Long2Ip extends AbstractDecorator
{
    /**
     * @param $value
     * @param $row
     * @return string
     */
    public function decorate($value)
    {
        if ($value) {
            return long2ip($value);
        }
    }
}