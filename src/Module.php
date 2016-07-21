<?php

namespace AtDataGrid;

use AtDataGrid\View\Helper\QueryParams;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getViewHelperConfig()
    {
        return [
            'invokables' => [
                'queryParams' => QueryParams::class,
            ],
        ];
    }
}