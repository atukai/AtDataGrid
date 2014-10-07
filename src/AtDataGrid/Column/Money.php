<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Money extends Column
{
    public function __construct($name)
    {
        parent::__construct($name);

        $this->addDecorator(new Decorator\Money())
             ->setFormElement(new \Zend\Form\Element\Text($this->getName()));
    }
}