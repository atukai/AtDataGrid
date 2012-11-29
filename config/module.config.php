<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'AtDataGrid\Controller\DataGrid'  => 'AtDataGrid\Controller\DataGridController'
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
