<?php

namespace AtDataGrid\DataSource;

use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\AdapterInterface;

abstract class AbstractDataSource implements DataSourceInterface
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
}