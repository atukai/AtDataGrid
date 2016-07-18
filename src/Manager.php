<?php

namespace AtDataGrid;

use AtDataGrid\Filter\FilterInterface;
use AtDataGrid\Form\FormBuilder;
use AtDataGrid\Renderer\RendererInterface;
use AtDataGrid\Row\Action;
use Zend\Cache\Storage\StorageInterface;
use Zend\Form\Element;
use Zend\Form\Form;

class Manager
{
    /**
     * @var DataGrid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $rawData;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var Form
     */
    protected $filtersForm;

    /**
     * @var StorageInterface
     */
    protected $cache;

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
    protected $actions = [];

    /**
     * @param DataGrid $grid
     */
    public function __construct(DataGrid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * @return DataGrid
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param RendererInterface $renderer
     * @return $this
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param StorageInterface $cache
     */
    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return StorageInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param bool $flag
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return FormBuilder
     */
    public function getFormBuilder()
    {
        if (! $this->formBuilder) {
            $this->formBuilder  = new FormBuilder();
        }

        return $this->formBuilder;
    }

    /**
     * @param FormBuilder $formBuilder
     */
    public function setFormBuilder($formBuilder)
    {
        $this->formBuilder = $formBuilder;
    }

    /**
     * @return Form
     */
    protected function buildFiltersForm()
    {
        $grid = $this->getGrid();
        $form = new Form('at-datagrid-filters-form');

        /** @var FilterInterface $filter */
        foreach ($grid->getFilters() as $filter) {
            $element = $filter->getFormElement();
            if (! $element) {
                $column = $grid->getColumn($filter->getName());
                $element = clone $column->getFormElement();

                if ($element instanceof Element\Select) {
                    $element->setEmptyOption('');
                }

                $element->setName($filter->getName());
                $filter->setFormElement($element);
            }

            $form->add($element);
        }

        // Apply button
        $form->add(new Element\Submit('apply', ['label' => 'Search']));

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
    public function addActions($actions = [])
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
    public function getBulkActions()
    {
        $bulkActions = [];
        foreach ($this->getActions() as $action) {
            if ($action->isBulk()) {
                $bulkActions[] = $action;
            }
        }

        return $bulkActions;
    }


    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getData()
    {
        $grid = $this->getGrid();
        $data = $grid->getData();

        $this->rawData = $data;

        // Add all columns from grid (not only from source)
        foreach (array_keys($grid->getColumns()) as $name) {
            foreach ($data as &$row) {
                if (!array_key_exists($name, $row)) {
                    $row[$name] = '';
                }
            }
        }
        unset($row);

        // Apply decorators only for visible columns
        $decoratedData = [];
        foreach ($data as $row) {
            $decoratedRow = [];
            foreach ($row as $colName => $value) {
                $column = $grid->getColumn($colName);
                $decoratedRow[$colName] = $value;
                if ($column->isVisible()) {
                    $decoratedRow[$colName] = $column->render($value, $row);
                }
            }
            $decoratedData[] = $decoratedRow;
        }

        return $decoratedData;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return $this->getRenderer()->render([
            'gridManager' => $this
        ]);
    }
}