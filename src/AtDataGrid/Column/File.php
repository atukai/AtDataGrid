<?php

namespace AtDataGrid\Column;

class File extends Column
{
    public function __construct($name)
	{
		parent::__construct($name);
		$this->setFormElement(new \Zend\Form\Element\File($this->getName()));
	}
}