<?php

namespace AtDataGrid\Column;

class Textarea extends Column
{
    public function __construct($name)
    {
        parent::__construct($name);

        $this->setFormElement(new \Zend\Form\Element\Textarea($this->getName()));
    }
}