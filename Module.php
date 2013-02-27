<?php

namespace AtDataGrid;

/**
 * Class Module
 * @package AtDataGrid
 */
class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'invokables' => array(
                'AtDataGrid\Controller\DataGrid' => 'AtDataGrid\Controller\DataGridController'
            ),
        );
    }

    /**
     * @return array
     */
    public function getControllerPluginConfig()
    {
        return array(
            'invokables' => array(
                'backTo' => 'AtBase\Mvc\Controller\Plugin\BackTo'
            ),
        );
    }
}
