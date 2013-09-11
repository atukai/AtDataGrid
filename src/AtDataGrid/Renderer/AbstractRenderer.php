<?php

namespace AtDataGrid\Renderer;

abstract class AbstractRenderer implements RendererInterface
{
    /**
     * @param array $variables
     * @return mixed
     */
    abstract public function render($variables = array());
}