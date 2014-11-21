<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator\DecoratorInterface;

class Column
{
    protected $name;

    protected $label;

    protected $visible = true;

    protected $visibleInForm = true;

    protected $sortable = false;
    
    protected $orderDirection = 'desc';
    
    protected $formElement = null;
    
    protected $decorators = array();

    /**
     * @param $name
     * @throws \Exception
     */
    public function __construct($name)
    {
        $this->setName($name);

        if (null === $this->getName()) {
            throw new \Exception('Please specify a column name');
        };
    }

    // METADATA

    /**
     * Filter a name to only allow valid variable characters
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
        if ('' === $name) {
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
		$this->visible = $value;
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
        $this->sortable = (bool) $value;
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
        $this->getOrderDirection() == 'asc' ? $this->orderDirection = 'desc' : $this->orderDirection = 'asc';
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
     * @return DecoratorInterface|null
     */
    public function getDecorator($name)
    {
    	if (isset($this->decorators[$name])) {
    		return $this->decorators[$name];
    	}
    	
    	return null;
    }

    /**
     * @param $value
     * @param null $row
     * @return mixed
     */
    public function render($value, $row = null)
    {
        foreach ($this->decorators as $decorator) {
            $value = $decorator->decorate($value, $row);
        }
        return $value;
    }
    
    // FORMS

    /**
     * @param $formElement
     * @return $this
     */
    public function setFormElement($formElement)
    {
    	$this->formElement = $formElement;
    	return $this;
    }

    /**
     * @return \Zend\Form\Element
     */
    public function getFormElement()
    {
        return $this->formElement;
    }
}