<?php

namespace AtDataGrid\Column\Decorator;

class YesNo extends Alias
{
    /**
     * @var array
     */
    protected $renameTo = [
        '1'       => 'Yes', '0'        => 'No',
        'yes'     => 'Yes', 'no'       => 'No',
        'true'    => 'Yes', 'false'    => 'No',
        'enable'  => 'Yes', 'disable'  => 'No',
        'enabled' => 'Yes', 'disabled' => 'No',
        'on'      => 'Yes', 'off'      => 'No',
    ];
}