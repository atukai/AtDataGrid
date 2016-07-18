<?php

namespace AtDataGrid\Column\Decorator;

class Callback extends AbstractDecorator
{
    /**
     * @var
     */
    protected $callback;

    /**
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @param $callback
     */
    public function __construct(callable $callback)
    {
        $this->setCallback($callback);
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback(callable $callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, $params = [])
    {
        /*if ($value === null || $value === '') {
            $value = $params;
        }*/

        return call_user_func_array($this->callback, [$value, $params]);
    }
}