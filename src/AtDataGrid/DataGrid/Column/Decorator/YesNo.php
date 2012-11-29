<?php

namespace AtDataGrid\DataGrid\Column\Decorator;

class YesNo extends Alias
{
    /**
     * @var array
     */
    protected $renameTo = array(
        '1'       => 'Yes', '0'        => 'No',
        'yes'     => 'Yes', 'no'       => 'No',
        'true'    => 'Yes', 'false'    => 'No',
        'enable'  => 'Yes', 'disable'  => 'No',
        'enabled' => 'Yes', 'disabled' => 'No',
        'on'      => 'Yes', 'off'      => 'No',
    );
}