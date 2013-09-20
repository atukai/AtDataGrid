<?php

namespace AtDataGrid\Filter\Sql;

use AtDataGrid\Filter;
use Zend\Db\Sql\Where;

class Like extends Filter\AbstractFilter
{
    /**
     * @param $select
     * @param $column
     * @param null $value
     * @return mixed
     */
    public function apply($select, $columnName, $value = null)
    {
        $value = $this->applyValueType($value);

        if (isset($value) && !empty($value)) {
            
            //$columnName = $this->findTableColumnName($select, $column->getName());
            // @todo Add param for like template
            $spec = function (Where $where) use ($columnName, $value) {
                $where->like($columnName, '%' . $value . '%');
            };

            $select->where($spec);
        }

        return $select;
    }
}