<?php

class ATF_DataGrid_Column_DbReference extends ATF_DataGrid_Column
{
    /**
     * @var \ATF_Db_Table_Abstract|null
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
     * @param ATF_Db_Table_Abstract $table
     * @param $referenceField
     * @param $resultFieldName
     */
    public function __construct($name, ATF_Db_Table_Abstract $refTable, $refField, $resultFieldName)
    {
        $this->referenceTable = $refTable;
        $this->referenceField = $refField;
        $this->resultFieldName = $resultFieldName;

        parent::__construct($name);
    }

    /**
     *
     */
    public function init()
    {
        parent::init();

        // Decorator
        $decorator = new ATF_DataGrid_Column_Decorator_DbReference(
            $this->referenceTable,
            $this->referenceField,
            $this->resultFieldName
        );
        $this->addDecorator($decorator);

        // Form element
        $select = $this->referenceTable->select()
                                           ->from($this->referenceTable->getName(), array($this->referenceField, $this->resultFieldName));
        $allRecords = $this->referenceTable->getAdapter()->fetchPairs($select);

        $formElement = new Zend_Form_Element_Select($this->getName());
        $formElement->addMultiOption('', '--')
                    ->addMultiOptions($allRecords);
        $this->setFormElement($formElement);
    }
}