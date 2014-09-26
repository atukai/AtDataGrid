<?php

namespace AtDataGrid\Column;

use AtDataGrid\Column\Decorator;

class Column
{
    /**
     * @var
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * Visibility in grid
     *
     * @var boolean
     */
    protected $visible = true;

    /**
     * Visibility in add/edit form
     *
     * @var boolean
     */
    protected $visibleInForm = true;

    /**
     * Is column sortable?
     */
    protected $sortable = false;
    
    /**
     * Current order direction
     */
    protected $orderDirection = 'desc';
    
    /**  
 	 * The form element  
 	 *  
	 * @var \Zend\Form\Element
	 */  
    protected $formElement = null;
    
    /**
     * Column validators
     * 
     * @var array
     */
    protected $validators = array();
    
    /**
     * The column decorator
     *
     * @var array
     */
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
        
        // Extensions...
        $this->init();
    }

    /**
     * Init extensions 
     */
    public function init()
    {
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
     * @return string
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
     * Set order direction
     *
     * @param $value
     * @return Column
     */
    public function setOrderDirection($value)
    {
        $this->orderDirection = strtolower($value);
        return $this;
    }   
  
    /**  
     * Retrieve element order direction  
     *  
     * @return boolean  
     */  
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**  
     * Retrieve element visibility  
     *  
     * @return boolean  
     */  
    public function revertOrderDirection()
    {
        $this->getOrderDirection() == 'asc' ? $this->orderDirection = 'desc' : $this->orderDirection = 'asc';
        return $this;
    }

    /**
     * @param $value
     * @return Column
     */
    public function setVisibleInForm($value)
    {
        $this->visibleInForm = $value;
        return $this;
    }

    /**
     * Alias for getVisibleInForm()  
     *  
     * @return boolean  
     */  
    public function isVisibleInForm()
    {
        return $this->visibleInForm;
    }	

    // RENDERING & DECORATORS

    /**
     * @param $decorator
     * @return Column
     * @throws \Exception
     */
    public function addDecorator($decorator)
    {
        if (is_string($decorator)) {
        	$name = ucfirst($decorator);
            $className = '\AtAdmin\DataGrid\Column\Decorator\\' . $name;
            $decorator = new $className;
        } elseif ($decorator instanceof Decorator\AbstractDecorator) {
        	$name = get_class($decorator);
        } else {
            throw new \Exception('Wrong decorator given.');
        }
        $this->decorators[$name] = $decorator;

        return $this;
    }

    /**
     * @param $decorators
     * @return Column
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
     * @return bool
     */
    public function getDecorator($name)
    {
    	if (isset($this->decorators[$name])) {
    		return $this->decorators[$name];
    	}
    	
    	return false;
    }

    /**
     * @param $value
     * @param null $row
     * @return mixed
     */
    public function render($value, $row = null)
    {
        foreach ($this->decorators as $decorator) {
            $value = $decorator->render($value, $row);    
        }
        return $value;
    }
    
    // FORMS

    /**
     * @param $formElement
     * @return Column
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

    // VALIDATORS

    /**
     * @param $validator
     * @return $this
     * @throws \Exception
     */
    public function addValidator($validator)
    {
    	if (!$this->formElement) {
    		throw new \Exception('Form element for column "' . $this->getName() . '" is not
    		    specified. Before set validators you must set form element.');
    	}
    	
    	$this->formElement->addValidator($validator);

        return $this;
    }
}