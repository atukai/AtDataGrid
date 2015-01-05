<?php

namespace AtDataGrid;

use AtDataGrid\DataSource;
use AtDataGrid\Column\Column;
use AtDataGrid\Filter\AbstractFilter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Paginator;
use ZfcBase\EventManager\EventProvider;

class DataGrid extends EventProvider implements \Countable, \IteratorAggregate, \ArrayAccess
{
    const EVENT_GRID_INIT = 'at-datagrid.grid.init';
    const EVENT_GRID_PERSIST_PRE = 'at-datagrid.grid.persist.pre';
    const EVENT_GRID_PERSIST_POST = 'at-datagrid.grid.persist.post';
    const EVENT_GRID_DELETE_PRE = 'at-datagrid.grid.delete.pre';
    const EVENT_GRID_DELETE_POST = 'at-datagrid.grid.delete.post';

    /**
     * Grid title
     *
     * @var string
     */
    protected $title;

    /**
     * Data source
     *
     * @var
     */
    protected $dataSource;

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
     * @var Paginator
     */
    protected $paginator;

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
    protected $itemsPerPage = 10;

    /**
     * Page range
     *
     * @var integer
     */
    protected $pageRange = 10;

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * Array of rows from data source
     *
     * @var array
     */
    protected $data = array();

    /**
     * @param $dataSource
     */
    public function __construct($dataSource)
    {
        $this->setDataSource($dataSource);

        $this->columns = $this->getDataSource()->loadColumns();

        $this->getEventManager()->trigger(self::EVENT_GRID_INIT, $this);
    }
    
    // METADATA

    /**
     * @param $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    // COLUMNS

    /**
     * @param $name
     * @return $this
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
     * Check if grid has column by the given name
     *
     * @param $name
     * @return bool
     */
    protected function hasColumn($name)
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
        $columnName = $column->getName();

        if ( (false == $overwrite) && ($this->hasColumn($columnName)) ) {
            throw new \Exception('Column `' . $columnName . '` already in a column list. Use another name.');
        }    
        
        $this->columns[$columnName] = $column;
    	
    	// If label is not set, set column name as label
    	if (null == $column->getLabel()) {
    		$column->setLabel($columnName);
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
        if ($this->hasColumn($name)) {
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
        if ($this->hasColumn($name)) {
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
    public function hideColumnsInForm(array $names)
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
     * @param DataSource\AbstractDataSource $dataSource
     * @return $this
     */
    public function setDataSource(DataSource\AbstractDataSource $dataSource)
    {
        $this->dataSource = $dataSource;
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
     * @return mixed
     * @throws \Exception
     */
    public function getData()
    {
        /**
         * Sorting
         */
        $order = null;
        if ($this->getCurrentOrderColumnName()) {
            $order = $this->getCurrentOrderColumnName() . ' ' . $this->getCurrentOrderDirection();
        }

        /**
         * Prepare data source for fetching data
         */
    	$this->getDataSource()->prepare($order, $this->getFilters());

        /**
         * Load data using paginator
         */
        $this->paginator = new Paginator($this->getDataSource()->getPaginatorAdapter());
        $this->paginator->setCurrentPageNumber($this->currentPage);
        $this->paginator->setItemCountPerPage($this->itemsPerPage);
        $this->paginator->setPageRange($this->pageRange);

        $data = $this->paginator->getCurrentItems();

        /**
         * Convert data to array
         */
        if (! is_array($data)) {
            if ($data instanceof ResultSet) {
                $data = $data->toArray();
            } elseif ($data instanceof \ArrayIterator) {
                $data = $data->getArrayCopy();
            } else {
                if (is_object($data)) {
                    $type = get_class($data);
                } else {
                    $type = gettype($data);
                }

                throw new \Exception('The paginator returned a result of unsupported type: ' . $type . '. Should be \ArrayIterator or an Array)');
            }
        }

        /**
         * Add all columns from grid
         */
        foreach ($this->getColumns() as $name => $column) {
            foreach ($data as &$row) {
                if (! array_key_exists($name, $row)) {
                    $row[$name] = '';
                }
            }
        }

        /**
         * Apply decorators only for visible columns
         */
        foreach ($data as &$row) {
            foreach ($row as $colName => $value) {
                $column = $this->getColumn($colName);
                if ($column->isVisible()) {
                    $row[$colName] = $column->render($value);
                } else {
                    // Remove invisible column data
                    unset($row[$colName]);
                }
            }
        }

        $this->data = $data;
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    // CRUD

    /**
     * @param $data
     * @param null $identifier
     * @return mixed
     */
    public function save($data, $identifier = null)
    {
        $eventResult = $this->getEventManager()->trigger(self::EVENT_GRID_PERSIST_PRE, $this, $data)->last();

        if ($eventResult) {
            $data = $eventResult;
        }

        if ($identifier) {
            $id = $this->update($data, $identifier);
        } else {
            $id = $this->insert($data);
            $data[$this->getIdentifierColumnName()] = $id;
        }

        $this->getEventManager()->trigger(self::EVENT_GRID_PERSIST_POST, $this, $data);

        return $id;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
        return $this->getDataSource()->insert($data);
    }

    /**
     * @param $data
     * @param $primary
     * @return mixed
     */
    public function update($data, $primary)
    {
        return $this->getDataSource()->update($data, $primary);
    }

    /**
     * @param $identifier
     */
    public function delete($identifier)
    {
        $this->getEventManager()->trigger(self::EVENT_GRID_DELETE_PRE, $this, array('id' => $identifier));
        $this->getDataSource()->delete($identifier);
        $this->getEventManager()->trigger(self::EVENT_GRID_DELETE_POST, $this, array('id' => $identifier));
    }

    // FILTERS

    /**
     * @param AbstractFilter $filter
     * @param Column $column
     * @return $this
     */
    public function addFilter(AbstractFilter $filter, Column $column)
    {
        if (! $filter->getName()) {
            $filter->setName($column->getName());
        }

        if (! $filter->getLabel()) {
            $filter->setLabel($column->getLabel());
        }

        if (! $filter->getFormElement()) {
            $filterFormElement = clone $column->getFormElement();
            $filterFormElement->setName($filter->getName());
            $filter->setFormElement($filterFormElement);
        }

        $this->filters[$filter->getName()] = $filter;

        return $this;
    }

    /**
     * @param $columnName
     * @return bool
     */
    public function hasFilter($columnName)
    {
        return array_key_exists($columnName, $this->filters);
    }

    /**
     * @param $columnName
     * @return null
     */
    public function getFilter($columnName)
    {
        if ($this->hasFilter($columnName)) {
            return $this->filters[$columnName];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return bool
     */
    public function hasFilters()
    {
        if (count($this->getFilters()) > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param $values
     */
    public function setFiltersData($values)
    {
        foreach ($this->getFilters() as $filter) {
            $filter->setValue($values[$filter->getName()]);
        }
    }

    // PAGINATOR

    /**
     * @param $number
     * @return $this
     */
    public function setCurrentPage($number)
    {
        $this->currentPage = (int) $number;
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
     * @param $count
     * @return $this
     */
    public function setItemsPerPage($count)
    {
        $this->itemsPerPage = (int) $count;
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
     * @return $this
     */
    public function setPageRange($count)
    {
        $this->pageRange = (int) $count;
        return $this;
    }

    // INTERFACES IMPLEMENTATION

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