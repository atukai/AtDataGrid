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
     * @var null
     */
    protected $referenceTable;

    /**
     * @var string
     */
    protected $referenceField;

    /**
     * @var string
     */
    protected $resultFieldName;

    /**
     * @param $name
     * @param Adapter $dbAdapter
     * @param $refTable
     * @param $refField
     * @param $resultFieldName
     * @throws \Exception
     */
    public function __construct($name, Adapter $dbAdapter, $refTable, $refField, $resultFieldName)
    {
        parent::__construct($name);

        $this->dbAdapter = $dbAdapter;
        $this->referenceTable = $refTable;
        $this->referenceField = $refField;
        $this->resultFieldName = $resultFieldName;

        $sql = new Sql($this->dbAdapter, $this->referenceTable);

        // Decorator
        $decorator = new \AtDataGrid\Column\Decorator\DbReference(
            $sql,
            $this->referenceField,
            $this->resultFieldName
        );
        $this->addDecorator($decorator);

        // Form element
        $select = $sql->select();
        $statement = $sql->prepareStatementForSqlObject($select);
        $rowset = $statement->execute();

        $options = array('' => '');
        foreach ($rowset as $row) {
            $options[$row['id']] = $row[$this->resultFieldName];
        }

        $formElement = new Select($this->getName());
        $formElement->setValueOptions($options);
        $this->setFormElement($formElement);
    }
}