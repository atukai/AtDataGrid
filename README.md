# AtDataGrid

Version 0.3.1-dev

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

public function getServiceConfig()
{
	return array(
		'factories' => array(
			'user_grid_datasource' => function ($sm) {
				$tableGateway = new ZendTableGateway('user', $sm->get('Zend\Db\Adapter\Adapter'));
				$dataSource = new TableGateway($tableGateway);
				return $dataSource;
			},

			'user_grid_renderer' => function () {
				$renderer = new Html();
				return $renderer;
			},

			'user_grid' => function ($sm) {
				$grid = new Grid\User($sm->get('user_grid_datasource'));
				return $grid;
			},

			'user_grid_manager' => function ($sm) {
				$manager = new Manager($sm->get('user_grid'), $sm->get('Request'));
				$manager->setRenderer($sm->get('user_grid_renderer'));
				return $manager;
			}
		),
	);
}
```

IndexController.php

```PHP
<?php

namespace Application\Controller;

use AtDataGrid\Controller\AbstractCrudController;

class IndexController extends AbstractCrudController
{
    /**
     * @return array|mixed|object
     */
    public function getGridManager()
    {
        return $this->getServiceLocator()->get('user_grid_manager');
    }
}
```

Grid.php

```PHP
<?php

namespace Application\Grid;

use AtDataGrid\DataGrid;
use AtDataGrid\Filter\Sql as SqlFilter;
use AtDataGrid\Column\Decorator;

class User extends DataGrid
{
    public function init()
    {
        parent::init();

        $this->setIdentifierColumnName('user_id');
        $this->setCaption('Users');

        $userId = $this->getColumn('user_id');
        $userId->setSortable()
               ->setLabel('#');

        $this->getColumn('username')
            ->setLabel('Username');

        $this->getColumn('display_name')
            ->setLabel('Display As');

        $email = $this->getColumn('email');
        $email->setSortable()
              ->setLabel('Email');

        $password = new Password('password');
        $password->setLabel('Password');
        $this->addColumn($password, true);

        // Filters
        $this->addFilter(new SqlFilter\Equal(), $userId);
        $this->addFilter(new SqlFilter\Like(), $email);

        $this->hideColumns(array('password', 'state'));
        $this->hideColumnsInForm(array('user_id', 'state'));
    }
}
```

Check /users/list  route.