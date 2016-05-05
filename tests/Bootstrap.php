<?php

ini_set('error_reporting', E_ALL);
date_default_timezone_set('UTC');

$files = [
    realpath(__DIR__.'/../../../autoload.php'),
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $loader = require $file;
        break;
    }
}

if (! isset($loader)) {
    throw new RuntimeException('vendor/autoload.php could not be found. Did you run `php composer.phar install`?');
}

/** \Composer\Autoload\ClassLoader */
$loader->add('AtDataGrid\\', './../src/AtDataGrid');