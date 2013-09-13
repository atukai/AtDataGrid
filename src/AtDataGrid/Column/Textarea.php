<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Textarea extends Column
{
    public function init()
    {
        parent::init();
        $this->setFormElement(new \Zend\Form\Element\Textarea($this->getName()));
    }
}