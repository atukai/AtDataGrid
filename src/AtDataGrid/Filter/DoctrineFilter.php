<?php

namespace AtDataGrid\Filter;

use Doctrine\ORM\QueryBuilder;

class DoctrineFilter extends AbstractFilter
{
    /**
     * @param $qb QueryBuilder
     * @param $columnName
     * @param $value
     * @return mixed
     * @throws \Exception
     */
    public function apply($qb, $columnName, $value)
    {
        if ($value !== null && $value !== '') {
            switch ($this->getOperator()) {
                case self::OP_EQUAL:
                    $qb->where([$columnName => $value]);
                    break;

                case self::OP_LIKE:
                    $spec = function (Where $where) use ($columnName, $value) {
                        $where->like($columnName, '%' . $value . '%');
                    };

                    $qb->where($spec);
                    break;

                case self::OP_GREATER:
                    $qb->where(
                        new Operator($columnName, Operator::OP_GT, $value)
                    );
                    break;

                case self::OP_GREATER_OR_EQUAL:
                    $qb->where(
                        new Operator($columnName, Operator::OP_GTE, $value)
                    );
                    break;

                case self::OP_LESS:
                    $qb->where(
                        new Operator($columnName, Operator::OP_LT, $value)
                    );
                    break;

                case self::OP_LESS_OR_EQUAL:
                    $qb->where(
                        new Operator($columnName, Operator::OP_LTE, $value)
                    );
                    break;

                default:
                    throw new \Exception('Operator '. $this->getOperator() . ' not supported in this data source');
            }
        }

        return $qb;
    }
}