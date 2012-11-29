<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class DbReference extends AbstractDecorator
{
    /**
     * @var null|\Zend\Db\TableGateway\TableGateway
     */
    protected $table = null;

    /**
     * @var string
     */
    protected $referenceField = '';

    /**
     * @var string
     */
    protected $resultFieldName = '';

    /**
     * @param \Zend\Db\TableGateway\TableGateway $table
     * @param $referenceField
     * @param $resultFieldName
     */
    public function __construct(\Zend\Db\TableGateway\TableGateway $table, $referenceField, $resultFieldName)
    {
        $this->table           = $table;
        $this->referenceField  = $referenceField;
        $this->resultFieldName = $resultFieldName;
    }

    /**
     * @param $value
     * @param $row
     * @return
     */
    public function render($value, $row)
    {
        if (!$value) {
            return '';
        }
        
        $select = $this->table->select()
                                  ->from($this->table->getName(), array($this->resultFieldName))
                                  ->where($this->referenceField . ' = ?', $value);

        return $this->table->getAdapter()->fetchOne($select);
    }
}