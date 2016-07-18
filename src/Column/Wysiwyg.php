<?php

namespace AtDataGrid\Column;

class Wysiwyg extends Textarea
{
    public function __construct($name)
    {
        parent::__construct($name);

        $formElement = new \Zend\Form\Element\Textarea($this->getName());
        $formElement->setAttribute('class', 'wysiwyg');

        $this->setFormElement($formElement);
    }
}