<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Money extends Column
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->addDecorator(new Decorator\Money())
             ->setFormElement(new \Zend\Form\Element\Text($this->getName()));
    }
}