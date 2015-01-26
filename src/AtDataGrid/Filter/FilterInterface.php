<?php

namespace AtDataGrid\Filter;

interface FilterInterface
{
    /**
     * @param $source
     * @param $columnName
     * @param null $value
     * @return mixed
     */
    public function apply($source, $columnName, $value = null);

    /**
     * @param $name
     * @return mixed
     */
    public function setName($name);

    /**
     * @return mixed
     */
    public function getName();

    /**
     * @param $value
     * @return mixed
     */
    public function setValue($value);
}