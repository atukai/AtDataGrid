<?php

namespace AtDataGrid\DataGrid\Column;

use AtDataGrid\DataGrid\Column\Decorator;

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