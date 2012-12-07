<?php

namespace AtDataGrid\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AtDataGrid\DataGrid;

class DataGridController extends AbstractActionController
{
    /**
     * @var \AtDataGrid\DataGrid\DataGrid
     */
    protected $grid;

    /**
     * @return array|\Zend\View\Model\ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }

    /**
     * @return void
     */
    public function listAction()
    {
        $this->backTo()->setBackUrl();

        // Get grid object
    	$grid = $this->getGrid();

        if (!isset($_POST['cmd'])) {
            $requestParams = $this->getRequest()->getQuery();

            $filtersForm = $grid->getFiltersForm();
            $filtersForm->setData($requestParams);

            if ($filtersForm->isValid()) {
                $grid->applyFilters($filtersForm->getData());
            }

            $viewModel = new ViewModel(array('grid' => $grid));
	        $viewModel->setTemplate('at-datagrid/grid');

            return $viewModel;
        } else {
            $this->_forward($_POST['cmd']);
        }
    }

    // CRUD

    /**
     * @throws \Exception
     */
    public function createAction()
    {
        $grid = $this->getGrid();

        if (!$grid->isAllowCreate()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $requestParams = $this->getRequest()->getPost();

        $form = $grid->getForm();
        $form->setData($requestParams);

        if ($form->isValid()) {
            $formData = $this->preSave($form);
            $itemId = $grid->save($formData);
            $this->postSave($grid, $itemId);

            $this->backTo()->goBack('Record created.');
        }

        $viewModel = new ViewModel(array('grid' => $grid));
        $viewModel->setTemplate('at-datagrid/create');

        return $viewModel;
    }

    /**
     * @return \Zend\View\Model\ViewModel
     * @throws \Exception
     */
    public function editAction()
    {
        //$this->view->backUrl = $this->_helper->backToUrl->getBackUrl(false);

        $grid = $this->getGrid();

        if (!$grid->isAllowEdit()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $itemId = $this->params('id');

        if (!$itemId) {
            throw new \Exception('No record found.');
        }

        $requestParams = $this->getRequest()->getPost();

        $form = $grid->getForm();
        $form->setData($requestParams);

        if ($this->getRequest()->isPost() && $form->isValid()) {
            $data = $this->preSave($form);
            $grid->save($data, $itemId);
            $this->postSave($grid, $itemId);

            $this->backTo()->goBack('Record updated.');
        }

        $item = $grid->getRow($itemId);
        $form->setData($item);

        //$currentPanel = $this->getRequest()->getParam('panel');
        //$this->view->panel = $currentPanel;

        $viewModel = new ViewModel(array(
            'grid' => $grid,
            'item' => $item
        ));
        $viewModel->setTemplate('at-datagrid/edit');

        return $viewModel;
    }

    /**
     * @throws \Exception
     */
    public function deleteAction()
    {
        $grid = $this->getGrid();

        if (!$grid->isAllowDelete()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $itemId = $this->params('id');

        if (!$itemId) {
            throw new \Exception('No record found.');
        }

        $grid->delete($itemId);

        $this->backTo()->goBack('Record deleted.');    }

    /**
     * Hook before save row
     * @todo: Use event here. See ZfcBase EventAwareForm
     *
     * @param $form
     * @return mixed
     */
    public function preSave($form)
    {
        $data = $form->getData();
        return $data;
    }

    /**
     * Hook after save row
     * @todo Use event here
     *
     * @param $grid
     * @param $primary
     */
    public function postSave($grid, $primary)
    {
        return;
    }

    /**
     * @return \AtAdmin\DataGrid\DataGrid
     */
    public function getGrid()
    {
        return $this->grid;
    }
}