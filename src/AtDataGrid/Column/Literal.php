<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Literal extends Column
{
    /**
     * 
     */
	public function init()
	{
		parent::init();
		
		$this->addDecorator(new Decorator\Literal())
             ->setFormElement(new \Zend\Form\Element\Text($this->getName()));
	}
}