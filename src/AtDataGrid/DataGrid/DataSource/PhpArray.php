<?php

namespace AtDataGrid\DataGrid\DataSource;

use AtDataGrid\DataGrid\Column;

class PhpArray extends AbstractDataSource
{
    /**
     * @param $options
     */
    public function __construct($options)
    {
        parent::__construct($options);
        $this->columns = $this->loadColumns();
    }

    /**
     * @return array
     */
    public function loadColumns()
    {
        return array();
    }

    /**
     * Return row by primary key
     */
    public function getRow($key)
    {
    }

    /**
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return array|Traversable
     */
    public function getRows($listType, $order, $currentPage, $itemsPerPage, $pageRange)
    {
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data)
    {
    }

    /**
     * @param $data
     * @param $key
     * @return mixed|void
     */
    public function update($data, $key)
    {
    }

    /**
     * @param $key
     * @return mixed|void
     */
    public function delete($key)
    {
    }
}