# AtDataGrid

A missing data grid component for Zend Framework 2.

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/atukai/AtDataGrid/badges/quality-score.png?s=f9e828e623137b09a68dbf29612351d610724282)](https://scrutinizer-ci.com/g/atukai/AtDataGrid/)

>NOTE: This module is still under heavy development. Do not use it in production

## Requirements

* [PHP 5.5+](http://php.net)
* [Zend Mvc](https://github.com/zendframework/zend-mvc)
* [Zend Paginator](https://github.com/zendframework/zend-paginator)
* [Zend Form](https://github.com/zendframework/zend-form)

## Installation

 1. Add `"atukai/at-datagrid": "dev-master"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtDataGrid` to your `config/application.config.php` file under the `modules` key.
 3. Copy `public/css` and `public/js` to your website root directory or use any asset manager