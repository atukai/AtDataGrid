<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class DateTime extends Column
{
    public function __construct($name)
    {
        parent::__construct($name);

        $dateTimeElement = new \Zend\Form\Element\DateTime($this->getName());
        $dateTimeElement->setOptions(array(
            'format' => 'Y-m-d H:i:s'
        ));

        $this->setFormElement($dateTimeElement)
             ->addDecorator(new Decorator\DateFormat());
    }
}