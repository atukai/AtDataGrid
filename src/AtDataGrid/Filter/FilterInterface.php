<?php

namespace AtDataGrid\Filter;

interface FilterInterface
{
    /**
     * @param $source
     * @param $column
     * @param $value
     * @return mixed
     */
    public function apply($source, $column, $value);
}