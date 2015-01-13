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
     * Filter a name to only allow valid variable characters
     * @link http://php.net/manual/en/language.variables.basics.php
     *
     * @param  string $value
     * @return string
     */
    protected function filterName($value)
    {
        $charset = '^a-zA-Z0-9_\x7f-\xff';
        return preg_replace('/[' . $charset . ']/', '', (string) $value);
    }

    /**
     * @param $name
     * @return Column
     * @throws \Exception
     */
    public function setName($name)
    {
        $name = $this->filterName($name);
        if (! $name) {
            throw new \Exception('Invalid name provided; must contain only valid
                variable characters and be non-empty');
        }
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
        //$this->getOrderDirection() == 'asc' ? $this->orderDirection = 'desc' : $this->orderDirection = 'asc';
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
        foreach ($this->decorators as $decorator) {
            $value = $decorator->decorate($value, $params);
        }

        return $value;
    }
    
    // FORMS

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