# AtDataGrid

Version 0.3.0-dev

A data grid component for Zend Framework 2.

>NOTE: This module is still under heavy development. Do not use it in production

## Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [ZfcBase](https://github.com/zf-commons/ZfcBase)
* [AtBase](https://github.com/atukai/AtBase)

## Installation

 1. Add `"atukai/at-datagrid": "0.*"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `ZfcBase`, `AtBase` and `AtDataGrid` to your `config/application.config.php` file under the `modules` key.
 3. Copy or create a symlink of public/css and public/js to your website root directory

## How To Use

Example for ZfcUser

module.config.php

```PHP
'users' => array(
	'type' => 'Segment',
	'options' => array(
		'route' => 'users[/:action][/:id]',
		'defaults' => array(
			'controller' => 'Application\Controller\Index',
			'action'     => 'index',
			'page'       => 1,
			'show_items' => 20,
		),
	)
)
```

Module.php

```PHP
use Zend\Db\TableGateway\TableGateway as ZendTableGateway;
use AtDataGrid\DataSource\ZendDb\TableGateway;
use AtDataGrid\Manager;
use AtDataGrid\Renderer\Html;

return array(
	'factories' => array(
		'user_grid_datasource' => function ($sm) {
			$tableGateway = new ZendTableGateway('user', $sm->get('Zend\Db\Adapter\Adapter'));
			$dataSource = new TableGateway($tableGateway);
			return $dataSource;
		},

		'user_grid_renderer' => function ($sm) {
			$renderer = new Html();
			$renderer->setEngine($sm->get('ViewRenderer'));
			return $renderer;
		},

		'user_grid' => function ($sm) {
			$grid = new Grid\User($sm->get('user_grid_datasource'));
			$grid->setIdentifierColumnName('user_id');
			$grid->setCaption('Users');
			return $grid;
		},

		'user_grid_manager' => function ($sm) {
			$manager = new Manager($sm->get('user_grid'));
			$manager->setRenderer($sm->get('user_grid_renderer'));
			return $manager;
		},
	),
);
```

IndexController.php

```PHP
use AtDataGrid\Controller\AbstractCrudController;

class IndexController extends AbstractCrudController
{
    public function getGridManager()
    {
        return $this->getServiceLocator()->get('user_grid_manager');
    }
}
```

Grid.php

```PHP
use AtDataGrid\DataGrid;

class Grid extends DataGrid
{
}
```

Check .../users/list  route.