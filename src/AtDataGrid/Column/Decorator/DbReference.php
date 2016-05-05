<?php

namespace AtDataGrid\Column\Decorator;

use Zend\Db\Sql\Sql;

class DbReference extends AbstractDecorator
{
    /**
     * @var string
     */
    protected $refField;

    /**
     * @var string
     */
    protected $resultField;

    /**
     * @param Sql $sql
     * @param $refField
     * @param $resultField
     */
    public function __construct(Sql $sql, $refField, $resultField)
    {
        $this->sql = $sql;
        $this->refField = $refField;
        $this->resultField = $resultField;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, $params = [])
    {
        $select = $this->sql->select();
        $select->columns([$this->resultField])
            ->where([$this->refField => $value]);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $row = $statement->execute()->current();

        return $row[$this->resultField];
    }
}