<?php

namespace AtDataGrid\Column\Decorator;

interface DecoratorInterface
{
    public function decorate($value, array $params = []);
}