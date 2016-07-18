<?php

namespace AtDataGrid\Filter;

use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Where;

class ZendSqlFilter extends AbstractFilter
{
    public function apply($select, $columnName, $value)
    {
        if ($value !== null && $value !== '') {
            switch ($this->getOperator()) {
                case self::OP_EQUAL:
                    $select->where([$columnName => $value]);
                    break;

                case self::OP_LIKE:
                    $spec = function (Where $where) use ($columnName, $value) {
                        $where->like($columnName, '%' . $value . '%');
                    };

                    $select->where($spec);
                    break;

                case self::OP_GREATER:
                    $select->where(
                        new Operator($columnName, Operator::OP_GT, $value)
                    );
                    break;

                case self::OP_GREATER_OR_EQUAL:
                    $select->where(
                        new Operator($columnName, Operator::OP_GTE, $value)
                    );
                    break;

                case self::OP_LESS:
                    $select->where(
                        new Operator($columnName, Operator::OP_LT, $value)
                    );
                    break;

                case self::OP_LESS_OR_EQUAL:
                    $select->where(
                        new Operator($columnName, Operator::OP_LTE, $value)
                    );
                    break;

                default:
                    throw new \Exception('Operator '. $this->getOperator() . ' not supported in this data source');
            }
        }

        return $select;
    }
}