<?php

namespace AtDataGrid\Column;

class File extends Column
{
    public function init()
	{
		parent::init();
		$this->setFormElement(new \Zend\Form\Element\File($this->getName()));
	}
}