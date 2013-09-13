<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class DateTime extends Column
{
    public function init()
    {
        parent::init();
        
        $this->setFormElement(new \Zend\Form\Element\DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat());
    }
}