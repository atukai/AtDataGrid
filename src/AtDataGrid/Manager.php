<?php

namespace AtDataGrid;

use AtDataGrid\Renderer\AbstractRenderer;
use AtDataGrid\Row\Action;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\Http\Request;
use ZfcBase\EventManager\EventProvider;

class Manager extends EventProvider
{
    const EVENT_GRID_FORM_BUILD_POST = 'at-datagrid.grid.form.build.post';
    const EVENT_GRID_FILTERS_FORM_BUILD_POST = 'at-datagrid.grid.filters_form.build.post';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Form
     */
    protected $filtersForm;

    /**
     * @var AbstractRenderer
     */
    protected $renderer;

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
     * @param DataGrid $grid
     * @param Request $request
     */
    public function __construct(DataGrid $grid, Request $request)
    {
        $this->grid = $grid;
        $this->request = $request;

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
    public function getForm()
    {
        if ($this->form == null) {
            $this->buildForm();
        }

        return $this->form;
    }

    /**
     * @return Form
     */
    protected function buildForm()
    {
        $form = new Form('at-datagrid-form-create');

        // Collect elements
        foreach ($this->getGrid()->getColumns() as $column) {
            if (!$column->isVisibleInForm()) {
                continue;
            }

            /* @var \Zend\Form\Element */
            $element = $column->getFormElement();
            //$element->setName($column->getName());

            if (!$element->getLabel()) {
                $element->setLabel($column->getLabel());
            }

            $form->add($element);
        }

        // Hash element to prevent CSRF attack
        $csrf = new Element\Csrf('hash');
        $form->add($csrf);

        // Submit button
        $submit = new Element\Submit('submit');
        $submit->setValue('Save');
        $form->add($submit);

        $this->getEventManager()->trigger(self::EVENT_GRID_FORM_BUILD_POST, $form);

        $this->form = $form;
        return $this->form;
    }

    /**
     * @return Form
     */
    public function getFiltersForm()
    {
        if ($this->filtersForm == null) {
            $this->buildFiltersForm();
        }

        return $this->filtersForm;
    }

    /**
     * @return Form
     */
    protected function buildFiltersForm()
    {
        $form = new Form('at-datagrid-filters-form');

        foreach ($this->getGrid()->getFilters() as $filter) {
            $element = $filter->getFormElement();
            $form->add($element);
        }

        // Apply button
        $form->add(new Element\Submit('apply', array('label' => 'Search')));

        // Set data from request
        // Use event?
        $form->setData($this->request->getQuery());

        $this->getEventManager()->trigger(self::EVENT_GRID_FILTERS_FORM_BUILD_POST, $form);

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
     * @param array $variables
     * @return mixed
     * @throws \Exception
     */
    public function render($variables = array())
    {
        $grid = $this->getGrid();

        $variables['gridManager'] = $this;
        $variables['grid'] = $this->getGrid();
        $variables['columns'] = $grid->getColumns();
        $variables['data'] = $grid->getData();
        $variables['paginator']   = $grid->getPaginator();

        return $this->getRenderer()->render($variables);
    }
}