<?php

namespace AtDataGrid\View\Helper;

use AtDataGrid\Row\Action;
use Zend\Db\ResultSet\ResultSet;
use Zend\View\Helper\AbstractHelper;
use Zend\View\View;

class RowAction extends AbstractHelper
{
    /**
     * @param Action $action
     * @param $row
     * @return string
     */
    public function __invoke(Action $action, $row)
    {
        // Convert data to array
        if (! is_array($row)) {
            if ($row instanceof ResultSet) {
                $row = $row->toArray();
            } elseif ($row instanceof \ArrayIterator || $row instanceof \ArrayObject) {
                $row = $row->getArrayCopy();
            } else {
                throw new \RuntimeException('Row data couldn\'t be converted to array');
            }
        }

        /** @var View $view */
        $view = $this->getView();

        if ($url = $action->getUrl()) {
            $p = [];
            $v = [];

            foreach ($row as $key => $val) {
                $p[] = '%'. $key .'%';
                $v[] = $val;
            }

            $actionUrl = str_replace($p, $v, $url);
        } else {
            $routeParams = array_merge($action->getRouteParams(), $row);
            $actionUrl = $view->url($action->getRouteName(), $routeParams);
        }

        $onClick = '';
        if ($action->isConfirm()) {
            $onClick = 'onclick="DataGrid.confirmAction(this, \''. $view->translate($action->getConfirmMessage()) . '\')"';
        }

        $disabledClass = '';
        if ($action->isDisablable() && $callback = $action->getDisableCallback()) {
            $result = call_user_func($callback, $row);
            if ($result) {
                $disabledClass = 'disabled';
            }
        }

        $html = '<a class="action-' .$action->getName() .' btn btn-default '.$disabledClass.'" href="'. $actionUrl .'"'. $onClick .'>
                     <span title="'. $view->translate($action->getLabel()) .'"
                           class="'. $action->getClass().'">
                     </span>
                 </a>';

        return $html;
    }
}