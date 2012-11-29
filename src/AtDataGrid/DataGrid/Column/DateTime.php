<?php

namespace AtDataGrid\DataGrid\Column;

use AtDataGrid\DataGrid\Column\Decorator;

class DateTime extends Column
{
    /**
     * Extensions
     */
    public function init()
    {
        parent::init();
        
        $this->setFormElement(new \Zend\Form\Element\DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat());
    }
}