<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator\DecoratorInterface;
use Zend\Form\Element;

class Column
{
    protected $name;

    protected $label;

    protected $visible = true;

    protected $visibleInForm = true;

    protected $sortable = false;
    
    protected $orderDirection = 'desc';

    protected $formElement;
    
    protected $decorators = array();

    /**
     * @param $name
     * @throws \Exception
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

    /**
     * @param $value
     * @return $this
     */
    public function setOrderDirection($value)
    {
        $this->orderDirection = strtolower($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @return $this
     */
    public function revertOrderDirection()
    {
        $this->orderDirection = ($this->getOrderDirection() == 'asc') ? 'desc' : 'asc';
        return $this;
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

    public function clearDecorators()
    {
        $this->decorators = array();
        return $this;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function render($value, $params = array())
    {
        /** @var DecoratorInterface $decorator */
        foreach ($this->decorators as $decorator) {
            $value = $decorator->decorate($value, $params);
        }

        return $value;
    }
    
    // FORM ELEMENTS

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
}