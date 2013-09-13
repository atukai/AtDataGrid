<?php

namespace AtDataGrid\Column;

class Password extends Column
{
    public function init()
    {
        parent::init();
        $this->setFormElement(new \Zend\Form\Element\Password($this->getName()));
    }
}