<?php

namespace AtDataGrid\DataGrid;

use AtDataGrid\DataGrid\DataSource;
use AtDataGrid\DataGrid\Column\Column;

/**
 * Class DataGrid
 * @package AtDataGrid\DataGrid
 */
class DataGrid implements \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * Grid caption
     *
     * @var string
     */
    protected $caption = '';
    
    /**
     * Data grid columns
     *
     * @var array
     */
    protected $columns = array();

    /**
     * @var string
     */
    protected $identifierColumnName = 'id';

    /**
     * @var null
     */
    protected $currentOrderColumnName = null;

    /**
     * @var string
     */
    protected $currentOrderDirection = 'asc';

    /**
     * Current page
     *
     * @var integer
     */
    protected $currentPage = 1;

    /**
     * Items per page
     *
     * @var integer
     */
    protected $itemsPerPage = 20;

    /**
     * Page range
     *
     * @var integer
     */
    protected $pageRange = 10;

    /**
     * Array of rows (items)
     *
     * @var array
     */
    protected $data = array();

    /**
     * Data source
     *
     * @var
     */
    protected $dataSource;

    /**
     * Data panels
     *
     * @var array
     */
    protected $dataPanels = array();

    /**
     * @param $dataSource
     * @param array $options
     */
    public function __construct($dataSource, $options = array())
    {
        $this->setDataSource($dataSource);

        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        $this->setOptions($options);

        /** @todo use event instead */
        $this->init();
    }
    
    /**
     * Initialize data grid (used by extending classes)
     *
     * 
     * @return void
     */
    public function init()
    {
    }
    
    // OPTIONS

    /**
     * Set data grid options
     *
     * @param array $options
     * @return DataGrid
     */
    public function setOptions(array $options)
    {
        unset($options['options']);
        unset($options['config']);

        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        
        return $this;
    }
    
    // METADATA

    /**
     * @param $caption
     * @return DataGrid
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    // COLUMNS

    /**
     * @param $name
     * @return DataGrid
     */
    public function setIdentifierColumnName($name)
    {
        $this->identifierColumnName = (string) $name;
        $this->getDataSource()->setIdentifierFieldName($this->identifierColumnName);

        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifierColumnName()
    {
        return $this->identifierColumnName;
    }

    /**
     * Check if is column present in column list
     *
     * @param $name
     * @return bool
     */
    protected function isColumn($name)
    {
        return array_key_exists($name, $this->columns);
    }

    /**
     * Add a column to data grid
     *
     * @param Column $column
     * @param bool $overwrite
     * @return DataGrid
     * @throws \Exception
     */
    public function addColumn(Column $column, $overwrite = false)
    {
        if ( (false == $overwrite) && ($this->isColumn($column)) ) {
            throw new \Exception('Column `' . $column->getName() . '` already in a column list. Use other name.');
        }    
        
        $this->columns[$column->getName()] = $column;
    	
    	// If label is not set, set column name as label
    	if (null == $column->getLabel()) {
    		$column->setLabel($column->getName());
    	}
    	
    	return $this;
    }

    /**
     * Set column by given name with overwriting.
     * Alias for addColumn($column, true)
     *
     * @param Column $column
     * @return DataGrid
     */
    public function setColumn(Column $column)
    {
        $this->addColumn($column, true);
        return $this;
    }

    /**
     * Add columns to grid
     *
     * @param array $columns
     * @param bool $overwrite
     * @return DataGrid
     */
    public function addColumns(array $columns, $overwrite = false)
    {
        foreach ($columns as $column) {
        	$this->addColumn($column, $overwrite);        
        }

        return $this;
    }

    /**
     * Return column object specified by it name
     *
     * @param $name
     * @return Column
     * @throws \Exception
     */
    public function getColumn($name)
    {
        if ($this->isColumn($name)) {
            return $this->columns[$name];
        }
        
        throw new \Exception("Column '" . $name . "' doesn't exist in column list.");
    }

    /**
     * Return all column objects
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Remove column specified by it name
     *
     * @param $name
     * @return DataGrid
     */
    public function removeColumn($name)
    {
        if ($this->isColumn($name)) {
            unset($this->columns[$name]);
        }

    	return $this;	
    }

    /**
     * Remove columns specified by its names
     *
     * @param array $names
     * @return DataGrid
     */
    public function removeColumns(array $names)
    {
        foreach ($names as $name) {
	        $this->removeColumn($name);
        }
        
        return $this;     	
    }

    /**
     * Set column invisible in grid
     *
     * @param $name
     * @return DataGrid
     */
    public function hideColumn($name)
    {
        $this->getColumn($name)
             ->setVisible(false);
        
        return $this;   
    }

    /**
     * Set columns invisible in grid
     *
     * @param array $names
     * @return DataGrid
     */
    public function hideColumns(array $names)
    {
        foreach ($names as $name) {
            $this->hideColumn($name);
        }

        return $this;                   
    }

    /**
     * Set column invisible in add/edit form
     *
     * @param $name
     * @return DataGrid
     */
    public function hideColumnInForm($name)
    {
        $this->getColumn($name)
    	     ->setVisibleInForm(false);
        
        return $this;    
    }

    /**
     * Set columns invisible in form
     *
     * @param $names
     * @return DataGrid
     */
    public function hideColumnsInForm($names)
    {
        foreach ($names as $name) {
            $this->hideColumnInForm($name);
        }

        return $this;
    }


    // SORTING

    /**
     * @param string $order  columnName~orderDirection
     */
    public function setOrder($order)
    {
        $order = explode('~', $order);

        if (isset($order[1])) {
            list($columnName, $orderDirection) = $order;
            $this->setCurrentOrderColumn($columnName, $orderDirection);
        }
    }

    /**
     * @param $columnName
     * @param $orderDirection
     */
    public function setCurrentOrderColumn($columnName, $orderDirection = 'asc')
    {
        try {
            $column = $this->getColumn($columnName);

            $this->currentOrderColumnName = $column->getName();
            $this->currentOrderDirection  = $orderDirection;

            $column->setOrderDirection($orderDirection);
            $column->revertOrderDirection();
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @return null
     */
    public function getCurrentOrderColumnName()
    {
        return $this->currentOrderColumnName;
    }

    /**
     * @return string
     */
    public function getCurrentOrderDirection()
    {
        return $this->currentOrderDirection;
    }

    // DATA SOURCE

    /**
     * Set data source and load columns defined in it
     *
     * @param DataSource\AbstractDataSource $dataSource
     * @return DataGrid
     */
    public function setDataSource(DataSource\AbstractDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
        $this->columns = $this->getDataSource()->getColumns();
        
    	return $this;	
    }

    /**
     * Get data source object
     *
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }
    
    /**
     * Find row by primary key
     *
     * @param $key
     * @return mixed
     */
    public function getRow($key)
    {
        return $this->getDataSource()->find($key);
    }

    /**
     * Returns rows demands on list type. It may be list or tree
     *
     * @param string $listType
     * @return mixed
     */
    public function getData($listType = DataSource\AbstractDataSource::LIST_TYPE_PLAIN)
    {
        $order = null;

        if ($this->getCurrentOrderColumnName()) {
            $order = $this->getCurrentOrderColumnName() . ' ' . $this->getCurrentOrderDirection();
        }

    	$this->data = $this->getDataSource()->fetch(
            $listType,
            $order,
            $this->currentPage,
            $this->itemsPerPage,
            $this->pageRange
        );

        return $this->data;
    }

    // CRUD

    /**
     * Insert new row to grid
     */
    public function insert($data)
    {
        return $this->getDataSource()->insert($data);
    }

    /**
     * Update row in a grid
     *
     * @param $data
     * @param $primary
     */
    public function update($data, $primary)
    {
        return $this->getDataSource()->update($data, $primary);
    }

    /**
     * @param $data
     * @param null $identifier
     */
    public function save($data, $identifier = null)
    {
        if ($identifier) {
            $id = $this->update($data, $identifier);
        } else {
            $id = $this->insert($data);
        }

        return $id;
    }

    /**
     * @param $identifier
     */
    public function delete($identifier)
    {
        $this->getDataSource()->delete($identifier);
    }


    // FILTERS

    /**
     * Add filter to column
     *
     * @param $column
     * @param $filter
     * @return DataGrid
     */
    public function addFilter($column, $filter)
    {
        $this->getColumn($column)->addFilter($filter);
        return $this;    
    }

    /**
     * Apply filters. Modify select object.
     *
     * @param $values
     */
    public function applyFilters($values)
    {
        $columns = $this->getColumns();

        /** @var \Zend\Db\Sql\Select $select  */
        $select = $this->getDataSource()->getSelect();

        foreach ($columns as $column) {
            $filters = $column->getFilters();

            foreach ($filters as $filter) {
                $filter->apply($select, $column, $values[$filter->getName()]);
            }
        }

        //var_dump($select->getSqlString());exit;

        //exit;
    }

    /**
     * @param array $options
     * @return \Zend\Form\Form
     */
    public function getFiltersForm($options = array())
    {
        $form = new \Zend\Form\Form('filters-form', $options);

        foreach ($this->getColumns() as $column) {
            if ($column->hasFilters()) {
	            $filters = $column->getFilters();
	            foreach ($filters as $filter) {
	                $form->add($column->getFilterFormElement($filter));
	            }	
            }
        }

        // Apply button
        $apply = new \Zend\Form\Element\Submit('apply');
        $apply->setLabel('Поиск');
        $form->add($apply);
        
        return $form;
    }

    /**
     * @param $number
     * @return DataGrid
     */
    public function setCurrentPage($number)
    {
        if (!is_null($number)) {
            $this->currentPage = (int) $number;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }


    /**
     * @param integer $count
     */
    public function setItemsPerPage($count)
    {
        if (!is_null($count)) {
            $this->itemsPerPage = (int) $count;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param $count
     * @return DataGrid
     */
    public function setPageRange($count)
    {
        if (!is_null($count)) {
            $this->pageRange = (int) $count;
        }

        return $this;
    }

    // DATA PANELS

    /**
     * @param $key
     * @param $name
     * @param bool $isAjax
     * @return DataGrid
     */
    public function addDataPanel($key, $name, $isAjax = true)
    {
        $this->dataPanels[$key] = array('name' => $name, 'is_ajax' => $isAjax);
        return $this;
    }

    /**
     * @return array
     */
    public function getDataPanels()
    {
        return $this->dataPanels;
    }

    // Interfaces implementation

    /**
     * @return mixed
     */
    public function current()
    {
        return current($this->columns);
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return ($this->current() !== false);
    }

    /**
     * @return mixed
     */
    public function next()
    {
        return next($this->columns);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        return key($this->columns);
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        return reset($this->columns);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->columns);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->columns[$offset]);
    }

    /**
     * @param mixed $offset
     * @return bool|mixed
     */
    public function offsetGet($offset)
    {
        if(isset($this->columns[$offset])) {
            return $this->columns[$offset];
        }

        return false;
    }

    /**
     * @param mixed $offset
     * @param mixed $column
     */
    public function offsetSet($offset, $column)
    {
        if ($offset !== null) {
            $this->columns[$offset] = $column;
        } else {
            $this->columns[] = $column;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        if (isset($this->columns[$offset])) {
            unset($this->columns[$offset]);
        }
    }

    /**
     * Get an iterator for iterating over the elements in the collection.
     *
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->columns);
    }
}