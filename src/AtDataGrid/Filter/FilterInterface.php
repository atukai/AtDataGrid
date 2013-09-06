<?php

namespace AtDataGrid\Filter;

interface FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply($select, $column, $value);
}