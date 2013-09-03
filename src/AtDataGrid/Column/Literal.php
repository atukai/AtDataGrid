<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;
use Zend\Form\Element\Text;

class Literal extends Column
{
    /**
     *
     */
    public function init()
	{
		parent::init();
		
		$this->addDecorator(new Decorator\Literal())
             ->setFormElement(new Text($this->getName()));
	}
}