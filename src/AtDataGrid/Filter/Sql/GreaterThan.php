<?php

namespace AtDataGrid\Filter\Sql;

use AtDataGrid\Filter;
use Zend\Db\Sql\Predicate\Operator;

class GreaterThan extends Filter\AbstractFilter
{
    /**
     * Returns the result of applying $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply($select, $columnName, $value = null)
    {
        $value = $this->applyValueType($value);
        
        if (strlen($value) > 0) {
            $select->where(
                new Operator($columnName, Operator::OP_GT, $value)
            );
        }

        return $select;
    }
}