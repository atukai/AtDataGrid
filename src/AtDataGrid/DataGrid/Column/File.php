<?php

class ATF_DataGrid_Column_File extends ATF_DataGrid_Column
{
	protected $_type = self::TYPE_FILE;

	public function init()
	{
		parent::init();

		$this->setFormElement(new Zend_Form_Element_File($this->getName()));
	}
}