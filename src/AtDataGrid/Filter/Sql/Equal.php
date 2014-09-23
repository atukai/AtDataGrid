<?php

namespace AtDataGrid\Filter\Sql;

use AtDataGrid\Filter;

class Equal extends Filter\AbstractFilter
{
    /**
     * @param $select
     * @param $columnName
     * @param null $value
     * @return mixed
     */
    public function apply($select, $columnName, $value = null)
    {
        if ($value) {
            $value = $this->applyValueType($value);
        }

        // Not null or not empty string
        if ($value && (!is_string($value) || (is_string($value) && !empty($value)))) {
            $select->where(array($columnName => $value));
            //var_dump($select->getSqlString());exit;
        }

        return $select;
    }
}