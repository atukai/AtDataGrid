<?php

namespace AtDataGrid\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AtDataGrid\DataGrid\Manager;

abstract class AbstractDataGridController extends AbstractActionController
{
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
        // Save back url to redirect after actions
        $this->backTo()->setBackUrl();

        // Configure grid
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        $grid->setOrder($this->params()->fromQuery('order', $grid->getIdentifierColumnName().'~desc'));
        $grid->setCurrentPage($this->params()->fromQuery('page'));
        $grid->setItemsPerPage($this->params()->fromQuery('show_items'));

        if (!isset($_POST['cmd'])) {
            $requestParams = $this->getRequest()->getQuery();

            $filtersForm = $grid->getFiltersForm();
            $filtersForm->setData($requestParams);

            if ($filtersForm->isValid()) {
                $grid->applyFilters($filtersForm->getData());
            }

            $viewModel = new ViewModel(array('gridManager' => $gridManager));
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
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowCreate()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $requestParams = $this->getRequest()->getPost();

        $form = $gridManager->getForm();
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
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowEdit()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $itemId = $this->params('id');
        if (!$itemId) {
            throw new \Exception('No record found.');
        }

        $requestParams = $this->getRequest()->getPost();

        $form = $gridManager->getForm();
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
            'grid'    => $grid,
            'item'    => $item,
            'backUrl' => $this->backTo()->getBackUrl(false)
        ));
        $viewModel->setTemplate('at-datagrid/edit');

        return $viewModel;
    }

    /**
     * @throws \Exception
     */
    public function deleteAction()
    {
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (!$gridManager->isAllowDelete()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $itemId = $this->params('id');
        if (!$itemId) {
            throw new \Exception('No record found.');
        }

        $grid->delete($itemId);
        $this->backTo()->goBack('Record deleted.');
    }

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
     * @return mixed
     */
    abstract public function getGridManager();
}