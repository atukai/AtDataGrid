<?php

namespace AtDataGrid;

use AtDataGrid\Filter\FilterInterface;
use AtDataGrid\Renderer\AbstractRenderer;
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
     * @var AbstractRenderer
     */
    protected $renderer;

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

        /** @var FilterInterface $filter */
        foreach ($grid->getFilters() as $filter) {
            $element = $filter->getFormElement();
            if (! $element) {
                $column = $grid->getColumn($filter->getName());
                $element = clone $column->getFormElement();
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
     * @throws \Exception
     */
    protected function composeData()
    {
        $grid = $this->getGrid();
        $data = $grid->getData();

        /**
         * Add all columns from grid
         */
        foreach ($grid->getColumns() as $name => $column) {
            foreach ($data as &$row) {
                if (! array_key_exists($name, $row)) {
                    $row[$name] = '';
                }
            }
        }

        unset($row);

        /**
         * Apply decorators only for visible columns
         */
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
        $grid = $this->getGrid();

        return $this->getRenderer()->render([
            'gridManager' => $this,
            'grid'        => $grid,
            'columns'     => $grid->getColumns(),
            'data'        => $this->composeData(),
            'paginator'   => $grid->getPaginator(),
            'filtersForm' => $this->getFiltersForm()
        ]);
    }
}