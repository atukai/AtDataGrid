<?php

namespace AtDataGrid\DataGrid\DataSource;

abstract class AbstractDataSource
{
    /**
     * Rows list types
     */
    const LIST_TYPE_PLAIN = 'plain';
    const LIST_TYPE_TREE  = 'tree';

    /**
     * @var string
     */
    protected $identifierFieldName = 'id';

    /**
     * All columns
     *
     * @var array
     */
    protected $columns = array();

    /**
     * @var
     */
    protected $paginator;

    /**
     * Constructor
     */
	public function __construct($options)
	{
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
            $this->setOptions($options);
        } else {
            throw new \Exception('Data source parameters must be in an array or a \Zend\Config\Config object');
        }
	}

    /**
     * @param array $options
     * @return AbstractDataSource
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

    /**
     * @param $name
     * @return AbstractDataSource
     */
    public function setIdentifierFieldName($name)
    {
        $this->identifierFieldName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentifierFieldName()
    {
        return $this->identifierFieldName;
    }

    /**
     * @param $paginator
     */
    public function setPaginator($paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Load columns from source
     *
     * @abstract
     * @return mixed
     */
    abstract public function loadColumns();

    /**
     * @abstract
     * @param $id
     * @return mixed
     */
    abstract public function find($id);
    
    /**
     * @abstract
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed
     */
    abstract public function fetch($listType, $order, $currentPage, $itemsPerPage, $pageRange);
    
    /**
     * @abstract
     * @param $data
     * @return mixed
     */
    abstract public function insert($data);
    
    /**
     * @abstract
     * @param $data
     * @param $key
     * @return mixed
     */
    abstract public function update($data, $key);
    
    /**
     * @abstract
     * @param $key
     * @return mixed
     */
    abstract public function delete($key);
}
