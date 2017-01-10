# AtDataGrid

A data grid builder for yours awesome admin panels.

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/atukai/AtDataGrid/badges/quality-score.png?s=f9e828e623137b09a68dbf29612351d610724282)](https://scrutinizer-ci.com/g/atukai/AtDataGrid/)

## Requirements

* [PHP 5.6+](http://php.net)
* [Zend\Paginator](https://github.com/zendframework/zend-paginator)
* [Zend\Form](https://github.com/zendframework/zend-form)
* [Zend\Stdlib](https://github.com/zendframework/zend-stdlib)

## Installation

 1. Run `$ composer require zendframework/at-datagrid`;
 2. Add `AtDataGrid` to your `config/application.config.php` file under the `modules` key;
 3. Copy `public/css` and `public/js` to your website root directory or use any asset manager.

## Available Data Sources
* ZendDb/TableGateway