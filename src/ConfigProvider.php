<?php

namespace AtDataGrid;

use AtDataGrid\View\Helper\QueryParams;
use AtDataGrid\View\Helper\RowAction;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => [
                'factories' => [
                    \AtDataGrid\Form\FormBuilder::class => \AtDataGrid\Form\FormBuilderFactory::class
                ],
            ],

            'templates' => [
                'paths' => [
                    __DIR__ . '/../view',
                ],
            ],

            'view_helpers' => [
                'invokables' => [
                    'rowAction' => RowAction::class,
                    'queryParams' => QueryParams::class,
                ],
            ],

            'translator' => [
                'locale' => 'ru',
                'translation_file_patterns' => [
                    [
                        'type'     => 'gettext',
                        'base_dir' => __DIR__ . '/../language',
                        'pattern'  => '%s.mo',
                    ],
                ],
            ],
        ];
    }
}
