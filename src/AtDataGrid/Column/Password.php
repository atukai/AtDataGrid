<?php

namespace AtDataGrid\Column;

class Password extends Column
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setFormElement(new \Zend\Form\Element\Password($this->getName()));
    }
}