<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class DateTime extends Column
{
    public function __construct($name)
    {
        parent::__construct($name);

        $formElement = new \Zend\Form\Element\DateTime($this->getName());
        $formElement->setOptions(['format' => 'Y-m-d H:i:s']);

        $this->setFormElement($formElement)
            ->addDecorator(new Decorator\DateFormat());
    }
}