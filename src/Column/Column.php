<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator\DecoratorInterface;
use Zend\Form\Element;
use Zend\InputFilter\InputFilterProviderInterface;

class Column implements InputFilterProviderInterface
{
    protected $name;
    protected $label;
    protected $visible = true;
    protected $visibleInForm = true;
    protected $sortable = false;
    protected $formElement;
    protected $section;
    protected $inputFilterSpecification;
    protected $decorators = [];

    /**
     * Column constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    // METADATA

    /**
     * @param $name
     * @return Column
     * @throws \Exception
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setLabel($name)
    {
    	$this->label = $name;
    	return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        if (!$this->label) {
            return $this->name;
        }
        return $this->label;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setVisible($value = true)
	{
		$this->visible = (bool)$value;
		return $this;
	}

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setVisibleInForm($value)
    {
        $this->visibleInForm = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleInForm()
    {
        return $this->visibleInForm;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setSortable($value = true)
    {
        $this->sortable = (bool)$value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    // RENDERING & DECORATORS

    /**
     * @param DecoratorInterface $decorator
     * @return $this
     */
    public function addDecorator(DecoratorInterface $decorator)
    {
        $this->decorators[get_class($decorator)] = $decorator;
        return $this;
    }

    /**
     * @param $decorators
     * @return $this
     */
    public function addDecorators($decorators)
    {
        foreach ($decorators as $decorator) {
            $this->addDecorator($decorator);  
        }

        return $this;
    }

    /**
     * @param $name
     * @return null
     */
    public function getDecorator($name)
    {
    	if (isset($this->decorators[$name])) {
    		return $this->decorators[$name];
    	}
    	
    	return null;
    }

    /**
     * @return $this
     */
    public function clearDecorators()
    {
        $this->decorators = [];
        return $this;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function render($value, $params = [])
    {
        /** @var DecoratorInterface $decorator */
        foreach ($this->decorators as $decorator) {
            $value = $decorator->decorate($value, $params);
        }

        return $value;
    }

    /**
     * @param Element $formElement
     * @return $this
     */
    public function setFormElement(Element $formElement)
    {
    	$this->formElement = $formElement;
    	return $this;
    }

    /**
     * @return mixed
     */
    public function getFormElement()
    {
        return $this->formElement;
    }

    // INPUT FILTERS

    /**
     * @param $spec
     * @return $this
     */
    public function setInputFilterSpecification($spec)
    {
        $this->inputFilterSpecification = $spec;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInputFilterSpecification()
    {
        return $this->inputFilterSpecification;
    }
}