<?php

namespace AtDataGrid\DataSource;

abstract class AbstractDataSource
{
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
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed
     */
    abstract public function fetch($order, $currentPage, $itemsPerPage, $pageRange);
    
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
