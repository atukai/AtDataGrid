<?php

namespace AtDataGrid\Column;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Form\Element\Select;

class DbReference extends Column
{
    /**
     * @var Adapter
     */
    protected $dbAdapter;

    /**
     * @var string
     */
    protected $refTable;

    /**
     * @var string
     */
    protected $refField;

    /**
     * @var string
     */
    protected $resultField;

    /**
     * @param $name
     * @param Adapter $dbAdapter
     * @param $refTable
     * @param $refField
     * @param $resultField
     */
    public function __construct($name, Adapter $dbAdapter, $refTable, $refField, $resultField)
    {
        parent::__construct($name);

        $this->dbAdapter = $dbAdapter;
        $this->refTable = $refTable;
        $this->refField = $refField;
        $this->resultField = $resultField;

        $sql = new Sql($this->dbAdapter, $this->refTable);

        // Decorator
        $decorator = new Decorator\DbReference($sql, $this->refField, $this->resultField);
        $this->addDecorator($decorator);

        // Form element
        $this->setFormElement($this->buildFormElement());
    }

    /**
     * @param string $identityColumn
     * @param bool $addEmptyOption
     * @return Select
     */
    protected function buildFormElement($identityColumn = 'id', $addEmptyOption = true)
    {
        $sql = new Sql($this->dbAdapter, $this->refTable);
        $select = $sql->select();
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();

        $options = [];
        foreach ($rowset as $row) {
            $options[$row[$identityColumn]] = $row[$this->resultField];
        }

        $formElement = new Select($this->getName());
        $formElement->setValueOptions($options);

        if ($addEmptyOption) {
            $formElement->setEmptyOption('');
        }

        return $formElement;
    }
}