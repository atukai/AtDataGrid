<?php

namespace AtDataGrid;

use AtDataGrid\Renderer\AbstractRenderer;
use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use ZfcBase\EventManager\EventProvider;

class Manager extends EventProvider
{
    /**
     * @var HttpRequest
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
     * Actions for row
     *
     * @var array
     */
    protected $actions = array(
        'edit'   => array('action' => 'edit', 'label' => 'View & Edit', 'bulk' => false, 'in_row' => true, 'class' => 'glyphicon glyphicon-pencil'),
        'delete' => array('action' => 'delete', 'label' => 'Delete', 'confirm-message' => 'Are you sure?', 'bulk' => true, 'in_row' => false)
    );

    /**
     * @param DataGrid $grid
     * @param HttpRequest $request
     */
    public function __construct(DataGrid $grid, HttpRequest $request)
    {
        $this->grid = $grid;
        $this->request = $request;

        $this->grid->setOrder($this->request->getQuery('order', $this->grid->getIdentifierColumnName().'~desc'));
        $this->grid->setCurrentPage($this->request->getQuery('page'));
        $this->grid->setItemsPerPage($this->request->getQuery('show_items'));
    }

    /**
     * @return DataGrid
     */
    public function getGrid()
    {
        return $this->grid;
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
     * Generate form for create/edit row
     *
     * @param array $options
     * @return mixed|Form
     */
    public function buildForm($options = array())
    {
        if ($this->form == null) {
            $form = new Form('at-datagrid-form-create', $options);

            // Collect elements
            foreach ($this->getGrid()->getColumns() as $column) {
                if (!$column->isVisibleInForm()) {
                    continue;
                }

                /* @var \Zend\Form\Element */
                $element = $column->getFormElement();
                $element->setLabel($column->getLabel());
                $form->add($element);
            }

            // Hash element to prevent CSRF attack
            $csrf = new Csrf('hash');
            $form->add($csrf);

            // Submit button
            $submit = new Submit('submit');
            $submit->setValue('Save');
            $form->add($submit);

            // Use this method to add additional element to form
            // @todo Use Event instead
            $this->form = $form;
        }

        return $this->form;
    }

    /**
     * @param array $options
     * @return Form
     */
    public function buildFiltersForm($options = array())
    {
        if ($this->filtersForm == null) {
            $form = new Form('at-datagrid-filters-form', $options);

            foreach ($this->getGrid()->getColumns() as $column) {
                if ($column->hasFilters()) {
                    $filters = $column->getFilters();
                    foreach ($filters as $filter) {
                        $form->add($column->getFilterFormElement($filter));
                    }
                }
            }

            // Apply button
            $apply = new Submit('apply');
            $apply->setLabel('Search');
            $form->add($apply);

            $this->filtersForm = $form;
        }

        $this->filtersForm->setData($this->request->getQuery());
        return $this->filtersForm;    }


    /**
     * @param Renderer\AbstractRenderer $renderer
     * @return $this
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return Renderer\AbstractRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Render grid with current renderer
     *
     * @return mixed
     */
    public function render()
    {
        $grid = $this->getGrid();

        $variables           = array();
        $variables['gridManager'] = $this;
        $variables['grid']        = $this->getGrid();
        $variables['columns']     = $grid->getColumns();
        $variables['data']        = $grid->getData();
        $variables['paginator']   = $grid->getPaginator();

        return $this->getRenderer()->render($variables);
    }

    /**
     * @param $name
     * @param array $action
     * @return DataGrid
     * @throws \Exception
     */
    public function addAction($name, $action = array())
    {
        if (! is_array($action)) {
            throw new \Exception('Row action must be an array with `action`, `label` and `confirm-message` keys');
        }

        if (! array_key_exists('action', $action)) {
            throw new \Exception('Row action must be an array with `action`, `label` and `confirm-message` keys');
        }

        if (! array_key_exists('label', $action)) {
            throw new \Exception('Row action must be an array with `action`, `label` and `confirm-message` keys');
        }

        if (! array_key_exists('bulk', $action)) {
            $action['bulk'] = true;
        }

        if (! array_key_exists('in_row', $action)) {
            $action['in_row'] = false;
        }

        $this->actions[$name] = $action;
        return $this;
    }

    /**
     * @param array $actions
     * @return DataGrid
     */
    public function addActions($actions = array())
    {
        foreach ($actions as $name => $action) {
            $this->addAction($name, $action);
        }

        return $this;
    }

    /**
     * @param $name
     * @return DataGrid
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
    public function getInRowActions()
    {
        $actions = array();

        foreach ($this->actions as $action) {
            if ($action['in_row'] == true) {
                $actions[] = $action;
            }
        }

        return $actions;
    }
}