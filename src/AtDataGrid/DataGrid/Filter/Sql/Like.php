<?php

namespace AtDataGrid\DataGrid\Filter\Sql;

use AtDataGrid\DataGrid\Filter;

class Like extends Filter\AbstractFilter
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

        if (isset($value) && !empty($value)) {
            
            //$columnName = $this->findTableColumnName($select, $column->getName());
            $columnName = $column->getName();
            
            // @todo Add param for like template
            $spec = function (\Zend\Db\Sql\Where $where) use ($columnName,$value) {
                $where->like($columnName, '%' . $value . '%');
            };

            $select->where($spec);
        }

        return $select;
    }
}