<?php

namespace AtDataGrid;

use AtDataGrid\Renderer\AbstractRenderer;
use AtDataGrid\Row\Action;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Http\PhpEnvironment\Request;

class Manager
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var AbstractRenderer
     */
    protected $renderer;

    /**
     * @var Form
     */
    protected $filtersForm;

    /**
     * @var bool
     */
    protected $allowCreate = true;

    /**
     * @var bool
     */
    protected $allowDelete = true;

    /**
     * @var bool
     */
    protected $allowEdit = true;

    /**
     * Row actions
     *
     * @var array
     */
    protected $actions = array();

    /**
     * @var array
     */
    protected $linkedRecords = array();

    /**
     * @param DataGrid $grid
     * @param Request $request
     */
    public function __construct(DataGrid $grid, Request $request)
    {
        $this->grid = $grid;
        $this->request = $request;

        // Row actions
        $editAction = new Action('edit');
        $editAction->setAction('edit');
        $editAction->setBulk(false);
        $editAction->setLabel('View & Edit');
        $editAction->setClass('glyphicon glyphicon-pencil');
        $this->addAction($editAction);

        $deleteAction = new Action('delete');
        $deleteAction->setAction('delete');
        $deleteAction->setLabel('Delete');
        $deleteAction->setConfirm(true);
        $deleteAction->setClass('glyphicon glyphicon-trash');
        $this->addAction($deleteAction);

        // @todo Use event?
        $this->grid->setOrder($this->request->getQuery('order', $this->grid->getIdentifierColumnName().'~desc'));

        if ($this->request->getQuery('page')) {
            $this->grid->setCurrentPage($this->request->getQuery('page'));
        }

        if ($this->request->getQuery('show_items')) {
            $this->grid->setItemsPerPage($this->request->getQuery('show_items'));
        }
    }

    /**
     * @param DataGrid $grid
     * @return $this
     */
    public function setGrid(DataGrid $grid)
    {
        $this->grid = $grid;
        return $this;
    }

    /**
     * @return DataGrid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param AbstractRenderer $renderer
     * @return $this
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return AbstractRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param bool $flag
     * @return DataGrid
     */
    public function setAllowCreate($flag = true)
    {
        $this->allowCreate = $flag;
        return $this;
    }

    /**
     * Alias for setAllowCreate
     *
     * @param bool $flag
     * @return DataGrid
     */
    public function allowCreate($flag = true)
    {
        $this->setAllowCreate($flag);
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowCreate()
    {
        return $this->allowCreate;
    }

    /**
     * @param bool $flag
     * @return DataGrid
     */
    public function setAllowDelete($flag = true)
    {
        $this->allowDelete = $flag;
        return $this;
    }

    /**
     * Alias for setAllowDelete
     *
     * @param bool $flag
     * @return DataGrid
     */
    public function allowDelete($flag = true)
    {
        $this->setAllowDelete($flag);
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowDelete()
    {
        return $this->allowDelete;
    }

    /**
     * @param bool $flag
     * @return DataGrid
     */
    public function setAllowEdit($flag = true)
    {
        $this->allowEdit = $flag;
        return $this;
    }

    /**
     * Alias for setAllowEdit
     *
     * @param bool $flag
     * @return DataGrid
     */
    public function allowEdit($flag = true)
    {
        $this->setAllowEdit($flag);
        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowEdit()
    {
        return $this->allowEdit;
    }

    /**
     * @return Form
     */
    public function getFiltersForm()
    {
        if (!$this->filtersForm) {
            $this->buildFiltersForm();
        }

        return $this->filtersForm;
    }

    /**
     * @return Form
     */
    protected function buildFiltersForm()
    {
        $grid = $this->getGrid();
        $form = new Form('at-datagrid-filters-form');

        foreach ($grid->getFilters() as $filter) {
            $element = $filter->getFormElement();
            $form->add($element);
        }

        // Apply button
        $form->add(new Element\Submit('apply', array('label' => 'Search')));

        // Set data from request
        // Use event instead? build.form.post
        $form->setData($this->request->getQuery());

        $this->filtersForm = $form;

        return $this->filtersForm;
    }

    /**
     * @param Action $action
     * @return $this
     * @throws \Exception
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
        return $this;
    }

    /**
     * @param array $actions
     * @return $this
     */
    public function addActions($actions = array())
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function removeAction($name)
    {
        if (array_key_exists($name, $this->actions)) {
            unset($this->actions[$name]);
        }

        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function getAction($name)
    {
        if (array_key_exists($name, $this->actions)) {
            return $this->actions[$name];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return array
     */
    public function getLinkedRecords()
    {
        return $this->linkedRecords;
    }

    /**
     * @param $name
     * @param $record
     * @return $this
     */
    public function addLinkedRecord($name, $record)
    {
        $this->linkedRecords[$name] = $record;
        return $this;
    }

    /**
     *
     */
    public function render()
    {
        $grid = $this->getGrid();

        return $this->getRenderer()->render(array(
            'gridManager' => $this,
            'grid'        => $grid,
            'columns'     => $grid->getColumns(),
            'data'        => $grid->getData(),
            'paginator'   => $grid->getPaginator(),
            'filtersForm' => $this->getFiltersForm(),
        ));
    }
}