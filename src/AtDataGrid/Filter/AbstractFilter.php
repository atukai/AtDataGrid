<?php

namespace AtDataGrid\Filter;

use Zend\Form\ElementInterface;

abstract class AbstractFilter implements FilterInterface
{
    const OP_EQUAL            = 'equal';
    const OP_NOT_EQUAL        = 'not_equal';
    const OP_GREATER          = 'greater';
    const OP_GREATER_OR_EQUAL = 'greater_or_equal';
    const OP_LESS             = 'less';
    const OP_LESS_OR_EQUAL    = 'less_or_equal';
    const OP_LIKE             = 'like';
    const OP_LIKE_LEFT        = 'like_left';
    const OP_LIKE_RIGHT       = 'like_right';
    const OP_NOT_LIKE         = 'not_like';
    const OP_NOT_LIKE_LEFT    = 'not_like_left';
    const OP_NOT_LIKE_RIGHT   = 'not_like_right';
    const OP_IN               = 'in';
    const OP_NOT_IN           = 'not_in';
    const OP_BETWEEN          = 'between';

    /**
     * @var string
     */
    private $operator;

    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $label;

    /**
     * @var
     */
    private $value;

    /**
     * @var ElementInterface
     */
    protected $formElement;

    /**
     * @param string $operator
     * @param null $name
     */
    public function __construct($operator, $name = null)
    {
        $this->operator = $operator;

        if ($name) {
            $this->setName($name);
        }
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param ElementInterface $element
     * @return $this
     */
    public function setFormElement(ElementInterface $element)
    {
        $this->formElement = $element;
        return $this;
    }

    /**
     * @return ElementInterface
     */
    public function getFormElement()
    {
        return $this->formElement;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }
}