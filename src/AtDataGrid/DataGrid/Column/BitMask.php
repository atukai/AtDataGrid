<?php

class ATF_DataGrid_Column_BitMask extends ATF_DataGrid_Column 
{
    /**
     * Column type
     * 
     * @var string
     */
    protected $_type = self::TYPE_BITMASK;
    
    /**
     * Available statuses
     * 
     * <format>
     * 
     * array(
     *     'is_active'   => array(1, 'Активен'),
     *     'is_locked'   => array(2, 'Заблокирован'),
     *     'is_deleted'  => array(4, 'Удален'),
     *     'is_approved' => array(8, 'Одобрен')
     * )
     * 
     * </format>
     * 
     * @var array
     */
    protected $_statuses = array();
    
    
    /**
     * @param array $statuses
     * @return ATF_DataGrid_Column_BitMask
     */
    public function setStatuses($statuses = array())
    {
        $this->_statuses = $statuses;

        // Add default decorator
        $decoratorStatuses = array();
        foreach ($statuses as $status) {
            list($value, $label) = $status;
            $decoratorStatuses[$label] = $value;            
        }
        
        $decorator = new ATF_DataGrid_Column_Decorator_BitMask();
        $decorator->setStatuses($decoratorStatuses);
        
        $this->addDecorator($decorator);
        return $this;
    }

    /**
     * @return array
     */
    public function getStatuses()
    {
        return $this->_statuses;
    }    

    /**
     * @param $status
     */
    public function addStatus($status)
    {
        $this->_statuses[] = $status;
    }

    /**
     * @param $filter
     * @return Zend_Form_Element_Select
     */
    public function getFilterFormElement($filter)
    {
        $statuses = $this->getStatuses();
        $status = $statuses[$filter->getName()];
        list($value, $label) = $status;
                    
        $formElement = new Zend_Form_Element_Select($filter->getName());
        $options = array("" => " ", $value => "Да", -$value => "Нет");
        $formElement->setLabel($label)
                    ->setMultiOptions($options)
                    ->addDecorator(array('div' => 'htmlTag'), array('tag' => 'div'));

        $this->_filterFormElements[$filter->getName()] = $formElement;                    
                    
        return $formElement;                                
    }

    /**
     * @return null|Zend_Form_Element
     */
    public function getFormElement()
    {

        $statuses = $this->getStatuses();

        $element = new ATF_Form_Element_BitMask($this->getName());
        
        $_statuses = array();
        foreach ($statuses as $status) {
            list($value, $label) = $status;                    
	        $_statuses[$value] = $label;            
        }
        
        $element->setMultiOptions($_statuses);
        
        $this->_formElement = $element;
        
        return $this->_formElement;
        
    }
    
}