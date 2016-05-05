<?php

namespace AtDataGrid\Filter;

interface FilterInterface
{
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

    /**
     * @todo: Remove from this
     * @return mixed
     */
    public function getFormElement();

    /**
     * @param $source
     * @param $columnName
     * @param $value
     * @return mixed
     */
    public function apply($source, $columnName, $value);
}