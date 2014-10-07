<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;
use Zend\Form\Element\DateTime;

class Date extends Column
{
    public function __construct($name)
    {
    	parent::__construct($name);
    	
        $this->setFormElement(new DateTime($this->getName()))
             ->addDecorator(new Decorator\DateFormat('d.m.Y'));
    }
}