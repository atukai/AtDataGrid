<?php

namespace AtDataGrid;

class Module
{
    public function getControllerPluginConfig()
    {
        return array(
            'invokables' => array(
                'backTo' => 'AtBase\Mvc\Controller\Plugin\BackTo'
            ),
        );
    }

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
}
