<?php

namespace AtDataGrid\DataSource;

interface DataSourceInterface
{
    /**
     * @return mixed
     */
    public function loadColumns();

    /**
     * @param array $order
     * @param array $filters
     * @return mixed
     */
    public function prepare($order = [], $filters = []);

    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $data
     * @return mixed
     */
    public function insert($data);

    /**
     * @param $data
     * @param $key
     * @return mixed
     */
    public function update($data, $key);

    /**
     * @param $key
     * @return mixed
     */
    public function delete($key);
}