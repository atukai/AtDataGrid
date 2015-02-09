<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Date extends Column
{
    public function __construct($name)
    {
    	parent::__construct($name);
    	
        $this->setFormElement(new \Zend\Form\Element\DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat('d.m.Y'));
    }
}