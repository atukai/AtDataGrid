<?php

namespace AtDataGrid\DataSource;

use Zend\Paginator\Adapter\AdapterInterface;

abstract class AbstractDataSource
{
    /**
     * @var string
     */
    protected $identifierFieldName = 'id';

    /**
     * @var AdapterInterface
     */
    protected $paginatorAdapter;

    /**
     * @param $name
     * @return $this
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
     * @param AdapterInterface $adapter
     * @return $this
     */
    public function setPaginatorAdapter(AdapterInterface $adapter)
    {
        $this->paginatorAdapter = $adapter;
        return $this;
    }

    /**
     * @return AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->paginatorAdapter;
    }

    /**
     * Load columns from source
     *
     * @abstract
     * @return mixed
     */
    abstract public function loadColumns();

    /**
     * @param $order
     * @return mixed
     */
    abstract public function prepare($order);

    /**
     * @param $id
     * @return mixed
     */
    abstract public function find($id);

    /**
     * @param $data
     * @return mixed
     */
    abstract public function insert($data);

    /**
     * @param $data
     * @param $key
     * @return mixed
     */
    abstract public function update($data, $key);

    /**
     * @param $key
     * @return mixed
     */
    abstract public function delete($key);
}