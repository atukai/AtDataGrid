<?php

namespace AtDataGrid\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FormBuilderFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $sm)
    {
        return new FormBuilder();
    }
}