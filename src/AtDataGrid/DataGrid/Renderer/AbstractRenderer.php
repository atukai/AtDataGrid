<?php

namespace AtDataGrid\DataGrid\Renderer;

abstract class AbstractRenderer
{
    /**
     * @abstract
     */
    abstract public function render($variables = array());
}