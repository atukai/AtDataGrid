<?php

namespace AtDataGrid\Filter;

interface FilterInterface
{
    /**
     * @param $source
     * @param $columnName
     * @param null $value
     * @return mixed
     */
    public function apply($source, $columnName, $value = null);
}