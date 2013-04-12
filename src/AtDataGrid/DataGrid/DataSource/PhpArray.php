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
     * Return row by key
     */
    public function find($key)
    {
    }

    /**
     * @param $listType
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed|void
     */
    public function fetch($listType, $order, $currentPage, $itemsPerPage, $pageRange)
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