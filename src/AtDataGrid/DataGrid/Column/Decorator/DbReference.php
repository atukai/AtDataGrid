<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class DbReference extends AbstractDecorator
{
    /**
     * @var null|\Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway = null;

    /**
     * @var string
     */
    protected $referenceField = '';

    /**
     * @var string
     */
    protected $resultFieldName = '';

    /**
     * @param \Zend\Db\TableGateway\TableGateway $tableGateway
     * @param $referenceField
     * @param $resultFieldName
     */
    public function __construct(\Zend\Db\TableGateway\TableGateway $tableGateway, $referenceField, $resultFieldName)
    {
        $this->tableGateway    = $tableGateway;
        $this->referenceField  = $referenceField;
        $this->resultFieldName = $resultFieldName;
    }

    /**
     * @param $value
     * @param $row
     * @return
     */
    public function render($value)
    {
        if (!$value) {
            return '';
        }
        
        $select = $this->tableGateway->select()
                                         ->from($this->tableGateway->getName(), array($this->resultFieldName))
                                         ->where($this->referenceField . ' = ?', $value);

        return $this->tableGateway->getAdapter()->fetchOne($select);
    }
}