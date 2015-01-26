<?php

namespace AtDataGrid\DataSource;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\AdapterInterface;

abstract class AbstractDataSource
{
    use EventManagerAwareTrait;

    const EVENT_DATASOURCE_PREPARE_POST = 'at-datagrid.datasource.prepare.post';

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
     */
    public function setIdentifierFieldName($name)
    {
        $this->identifierFieldName = $name;
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
     */
    public function setPaginatorAdapter(AdapterInterface $adapter)
    {
        $this->paginatorAdapter = $adapter;
    }

    /**
     * @return AdapterInterface
     */
    public function getPaginatorAdapter()
    {
        return $this->paginatorAdapter;
    }

    /**
     * @return mixed
     */
    abstract public function loadColumns();

    /**
     * @param $order
     * @param array $filters
     * @return mixed
     */
    abstract public function prepare($order, $filters = array());

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