<?php

namespace AtDataGrid\Column;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class DbReference extends Column
{
    protected $dbAdapter = null;

    /**
     * @var null
     */
    protected $referenceTable = null;

    /**
     * @var string
     */
    protected $referenceField = '';

    /**
     * @var string
     */
    protected $resultFieldName = '';

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
        //$select = $sql->select();
        //$select->columns(array($this->referenceField, $this->resultFieldName));
        //$allRecords = $setableGateWay->select($select);

        /*$formElement = new Select($this->getName());
        $formElement->addMultiOption('', '--')
            ->addMultiOptions($allRecords);
        $this->setFormElement($formElement);*/
    }

    /**
     * @return null|Adapter
     */
    public function getDbAdapter()
    {
        return $this->dbAdapter;
    }
}