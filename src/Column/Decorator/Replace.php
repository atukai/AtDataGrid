<?php

namespace AtDataGrid\Column\Decorator;

class Replace extends AbstractDecorator
{
    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, array $params = [])
    {
        $p = [];
        $v = [];

        foreach ($params as $key => $val) {
            $p[] = '%'. $key .'%';
            $v[] = $val;
        }

        return str_replace($p, $v, $value);
    }
}