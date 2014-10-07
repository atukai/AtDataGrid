<?php

namespace AtDataGrid\Column\Decorator;

use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class DbReference extends AbstractDecorator
{
    /**
     * @var null|Select
     */
    protected $select = null;

    /**
     * @var string
     */
    protected $referenceField = '';

    /**
     * @var string
     */
    protected $resultFieldName = '';

    /**
     * @param Sql $sql
     * @param $referenceField
     * @param $resultFieldName
     */
    public function __construct(Sql $sql, $referenceField, $resultFieldName)
    {
        $this->sql             = $sql;
        $this->referenceField  = $referenceField;
        $this->resultFieldName = $resultFieldName;
    }

    /**
     * @param $value
     * @return string
     */
    public function render($value)
    {
        $select = $this->sql->select();
        $select->columns(array($this->resultFieldName))
            ->where(array($this->referenceField => $value))
            ->limit(1);

        $statement = $this->sql->prepareStatementForSqlObject($select);
        $row = $statement->execute()->current();

        return $row[$this->resultFieldName];
    }
}