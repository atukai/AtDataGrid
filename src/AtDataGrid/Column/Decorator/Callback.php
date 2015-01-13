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
    public function __construct($callback)
    {
        $this->setCallback($callback);
    }

    /**
     * @param $callback
     * @return $this
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callback given');
        }

        $this->callback = $callback;
        return $this;
    }

    /**
     * @param $value
     * @param array $params
     * @return mixed
     */
    public function decorate($value, $params = array())
    {
        if (!$value || $value == '') {
            $value = $params;
        }

        return call_user_func_array($this->callback, array($value));
    }
}