<?php

namespace AtDataGrid\Renderer;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @abstract
     */
    abstract public function render($variables = array());
}