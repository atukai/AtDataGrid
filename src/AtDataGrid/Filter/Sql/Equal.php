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
        $value = $this->applyValueType($value);

        if (isset($value) && !empty($value)) {
        	//$columnName = $this->findTableColumnName($select, $column->getName());
            $select->where(array($columnName => $value));
        }

        return $select;
    }
}