<?php

namespace AtDataGrid\Renderer;

interface RendererInterface
{
    /**
     * @param array $variables
     * @return mixed
     */
    public function render(array $variables = []);
}