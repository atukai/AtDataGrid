<?php

namespace AtDataGrid\Row;

trait DisablableTrait
{
    /**
     * @var bool
     */
    protected $disablable = false;

    /**
     * @var callable
     */
    protected $disableCallback;

    /**
     * @return bool
     */
    public function isDisablable()
    {
        return $this->disablable;
    }

    /**
     * @param callable $callback
     */
    public function setDisableCallback(callable $callback)
    {
        $this->disableCallback = $callback;
        $this->disablable = true;
    }

    /**
     * @return callable
     */
    public function getDisableCallback()
    {
        return $this->disableCallback;
    }
}