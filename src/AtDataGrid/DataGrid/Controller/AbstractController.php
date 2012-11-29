<?php

/**
 *
 */
class ATF_DataGrid_Controller_AbstractController extends Zend_Controller_Action
{
    /**
     *
     */
    public function init()
    {
        parent::init();

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
        }

        $moduleName     = strtolower($this->getRequest()->getModuleName());
        $controllerName = str_ireplace('admin_', '', $this->getRequest()->getControllerName());

        $this->view->setScriptPath(APPLICATION_PATH . '/views/scripts');
        $this->view->addScriptPath(APPLICATION_PATH . '/modules/' . $moduleName . '/views/scripts/admin');
        $this->view->addScriptPath(APPLICATION_PATH . '/modules/' . $moduleName . '/views/scripts/admin/' . $controllerName);

        //var_dump($this->view->getScriptPaths());exit;
    }
}
