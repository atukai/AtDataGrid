# AtDataGrid

A data grid component for Zend Framework 2.

[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/atukai/AtDataGrid/badges/quality-score.png?s=f9e828e623137b09a68dbf29612351d610724282)](https://scrutinizer-ci.com/g/atukai/AtDataGrid/)

>NOTE: This module is still under heavy development. Do not use it in production

## Requirements

* [PHP 5.4+](http://php.net)
* [Zend Framework 2](https://github.com/zendframework/zf2)

## Installation

 1. Add `"atukai/at-datagrid": "dev-master"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtDataGrid` to your `config/application.config.php` file under the `modules` key.
 3. Copy public/css and public/js to your website root directory

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
			'action'     => 'index'
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
			'user_grid' => function ($sm) {
            	$dataSource = new TableGateway(new ZendTableGateway('user', $sm->get('Zend\Db\Adapter\Adapter')));
				$grid = new Grid\User($dataSource);
				return $grid;
			},

			'user_grid_manager' => function ($sm) {
				$manager = new Manager($sm->get('user_grid'), $sm->get('Request'));
				$manager->setRenderer(new Html());
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

use AtDatagrid\Manager;

class IndexController extends AbstractCrudController
{
    public function listAction()
    {
        $gridManager = $this->getServiceLocator()->get('user_grid_manager');

        $filtersForm = $gridManager->getFiltersForm();
        $filtersForm->setData($this->request->getQuery());

        $grid = $gridManager->getGrid();
        $grid->setFiltersData($filtersForm->getData());

        return $gridManager->render();
    }
}
```

Grid.php

```PHP
<?php

namespace Application\Grid;

use AtDataGrid\DataGrid;
use AtDataGrid\Filter\Sql as SqlFilter;
use AtDataGrid\Column;
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

        $password = new Column\Password('password');
        $password->setLabel('Password');
        $this->setColumn($password);

        // Filters
        $this->addFilter(new SqlFilter\Equal(), $userId);
        $this->addFilter(new SqlFilter\Like(), $email);

        $this->hideColumns(array('password', 'state'));
        $this->hideColumnsInForm(array('user_id', 'state'));
    }
}
```

Check /users/list  route.