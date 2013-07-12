<?php

namespace AtDataGrid\Filter\Sql;

use AtDataGrid\Filter;

class GreaterThan extends Filter\AbstractFilter
{
    /**
     * Returns the result of applying $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply($select, $column, $value)
    {
        $value = $this->applyValueType($value);
        
        if (strlen($value) > 0) {
            $select->where(
                new \Zend\Db\Sql\Predicate\Operator($column->getName(), \Zend\Db\Sql\Predicate\Operator::OP_GT, $value)
            );
        }

        return $select;
    }
}