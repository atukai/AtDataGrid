<?php

namespace AtDataGrid\DataGrid\Column;

use AtDataGrid\DataGrid\Column\Decorator;

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
     * Array of column filters
     * 
     * @var array
     */
    protected $filters = array();

    /**
     * The filters form elements
     *
     * @var array
     */
    protected $filterFormElements = array();
    
    /**
     * The column decorator
     *
     * @var array
     */
    protected $decorators = array();

    /**
     * @param $name
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
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return Column
     */
    public function setLabel($name)
    {
    	$this->label = $name;
    	return $this;
    }

    /**
     * @return null
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * @param bool $value
     * @return Column
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
     * @return Column
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
        $this->orderDirection = $value;
        return $this;
    }   
  
    /**  
     * Retrieve element order direction  
     *  
     * @return boolean  
     */  
    public function getOrderDirection()
    {
        return strtolower($this->orderDirection);
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
     * @return bool
     */
    public function getVisibleInForm()
    {
        return $this->visibleInForm;
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
     * @return null|\Zend\Form\Element
     */
    public function getFormElement()
    {
        return $this->formElement;
    }

    // VALIDATORS
    
    /**
     * Add validator to form element
     */
    public function addValidator($validator)
    {
    	if (null == $this->formElement) {
    		throw new \Exception('Form element for column "' . $this->getName() . '" is not
    		    specified. Before set validators you must set form element.');
    	}
    	
    	if ($validator instanceof Zend_Validate_NotEmpty) {
    	    $this->formElement->setRequired(true);
    	}
    	
    	$this->formElement->addValidator($validator);

        return $this;
    }

    // FILTERS
    
    /**
     * Add filter for grid column
     */
    public function addFilter($filter)
    {
        if (!$filter->getName()) {
            $filter->setName($this->getName());    
        }
        
        if (!$filter->getLabel()) {
            $filter->setLabel($this->getLabel());    
        }
        
        $this->filters[] = $filter;
        
        return $this;
    }

    /**
     * Add filters for grid column
     */
    public function addFilters($filters)
    {
    	foreach ($filters as $filter) {
    	    $this->addFilter($filter);	
    	}

        return $this;
    }

    /**
     * Return column filters
     * 
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
    
    /**
     * Is column has filters?
     * 
     * @return boolean
     */
    public function hasFilters()
    {
        if (count($this->getFilters()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $filter
     */
    public function getFilterFormElement($filter)
    {
        $filterName = $filter->getName();

        if (!isset($this->filterFormElements[$filterName])) {
            $formElement = $this->getFormElement();
	        $filterFormElement = clone $formElement;
	        $filterFormElement->setName($filterName);
            $this->filterFormElements[$filterName] = $filterFormElement;
        }
        
        return $this->filterFormElements[$filterName];
    }

    /**
     * @param $element
     * @param null $filterName
     * @return Column
     */
    public function setFilterFormElement($formElement, $filterName = null)
    {
        if (!$filterName) {
            $filterName = $formElement->getName();
        }
        $this->filterFormElements[$filterName] = $formElement;

        return $this;            
    }    
}