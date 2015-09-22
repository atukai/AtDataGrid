<?php

namespace AtDataGrid\View\Helper;

use AtDataGrid\Row\Action;
use Zend\View\Helper\AbstractHelper;

class RowAction extends AbstractHelper
{
    /**
     * @param Action $action
     * @param $row
     * @param string $identifierColumnName
     * @return string
     */
    public function __invoke(Action $action, $row, $identifierColumnName = 'id')
    {
        $view = $this->getView();

        $routeParams = array_merge($action->getRouteParams(), ['id' => $row[$identifierColumnName]]);
        $actionUrl = $view->url($action->getRouteName(), $routeParams);

        $onClick = '';
        if ($action->isConfirm()) {
            $onClick = 'onclick="DataGrid.confirmAction(this, \''. $view->translate($action->getConfirmMessage()) . '\')"';
        }

        $html = '<a class="action-' .$action->getName() .' btn btn-default" href="'. $actionUrl .'"'. $onClick .'>
                     <span title="'. $this->getView()->translate($action->getLabel()) .'"
                           class="'. $action->getClass().'">
                     </span>
                 </a>';

        return $html;
    }
}