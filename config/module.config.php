<?php

return [
    'service_manager' => [
        'factories' => [
            \AtDataGrid\Form\FormBuilder::class => \AtDataGrid\Form\FormBuilderFactory::class
        ]
    ],

    'translator' => [
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

    'view_helpers' => [
        'invokables' => [
            'rowAction' => 'AtDataGrid\View\Helper\RowAction',
            'queryParams' => \AtDataGrid\View\Helper\QueryParams::class,
        ],
    ]
];
