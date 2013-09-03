<?php

namespace AtDataGrid\DataSource;

use AtDataGrid\Column;

class PhpArray extends AbstractDataSource
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param $options
     */
    public function __construct($data)
    {
        $this->data = $data;
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
     * @param $order
     * @param $currentPage
     * @param $itemsPerPage
     * @param $pageRange
     * @return mixed|void
     */
    public function fetch($order, $currentPage, $itemsPerPage, $pageRange)
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