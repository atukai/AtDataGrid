<?php

namespace AtDataGrid\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use AtDataGrid\Manager;

abstract class AbstractCrudController extends AbstractActionController
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

        if (isset($_POST['cmd'])) {
            $this->_forward($_POST['cmd']);    // @todo refactor this
        }

        $gridManager = $this->getGridManager();

        $filtersForm = $gridManager->getFiltersForm();
        if ($filtersForm->isValid()) {
            $gridManager->getGrid()->setFiltersData($filtersForm->getData());
        }

        return $gridManager->render();
    }

    // CRUD

    /**
     * @throws \Exception
     */
    public function createAction()
    {
        $gridManager = $this->getGridManager();
        $grid = $gridManager->getGrid();

        if (! $gridManager->isAllowCreate()) {
            throw new \Exception('You are not allowed to do this.');
        }

        $form = $gridManager->getForm();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $grid->save($form->getData());
                $this->backTo()->previous('Record created.');
            }
        }

        $viewModel = new ViewModel(array(
            'form' => $form,
            'backUrl' => $this->backTo()->getBackUrl(false)
        ));
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

        $form = $gridManager->getForm();

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $grid->save($form->getData(), $itemId);
                $this->backTo()->previous('Record updated.');
            }
        }

        $item = $grid->getRow($itemId);
        $form->setData($item);

        $viewModel = new ViewModel(array(
            'gridManager' => $gridManager,
            'item'        => $item,
            'form'        => $form,
            'backUrl'     => $this->backTo()->getBackUrl(false)
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
        $this->backTo()->previous('Record deleted.');
    }

    /**
     * @return Manager
     */
    abstract public function getGridManager();
}