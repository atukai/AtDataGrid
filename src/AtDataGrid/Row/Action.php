<?php

namespace AtDataGrid\Row;

class Action
{
    private $name;

    private $action;

    private $label;

    private $confirm = false;

    private $confirmMessage = 'Are you sure?';

    private $bulk = true;

    private $button = false;

    private $class;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return boolean
     */
    public function isBulk()
    {
        return $this->bulk;
    }

    /**
     * @param boolean $bulk
     */
    public function setBulk($bulk)
    {
        $this->bulk = $bulk;
    }

    /**
     * @return boolean
     */
    public function isButton()
    {
        return $this->button;
    }

    /**
     * @param boolean $button
     */
    public function setButton($button)
    {
        $this->button = $button;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return boolean
     */
    public function isConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param boolean $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
    }

    /**
     * @return string
     */
    public function getConfirmMessage()
    {
        return $this->confirmMessage;
    }

    /**
     * @param string $confirmMessage
     */
    public function setConfirmMessage($confirmMessage)
    {
        $this->confirmMessage = $confirmMessage;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}