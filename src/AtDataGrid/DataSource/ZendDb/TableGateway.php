<?php

namespace AtDataGrid\DataSource\ZendDb;

use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use Zend\Db\TableGateway\Feature;
use AtDataGrid\Column;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class TableGateway extends Select
{
    /**
     * @var ZendTableGateway
     */
    protected $tableGateway;

    /**
     * @param ZendTableGateway $table
     */
    public function __construct(ZendTableGateway $tableGateway)
	{
        $this->tableGateway = $tableGateway;
        $this->dbAdapter = $tableGateway->getAdapter();
        $this->select = $this->getTableGateway()->getSql()->select();
        $this->paginator = new Paginator(
            new DbSelect($this->select, $this->dbAdapter)
        );

        $this->columns = $this->loadColumns();
	}

    /**
     * @return ZendTableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }
}