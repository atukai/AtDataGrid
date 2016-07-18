<?php

namespace AtDataGrid\Filter;

use Zend\Form\ElementInterface;

abstract class AbstractFilterOld implements FilterInterface
{
    /**
     * Value types
     */
    const VALUE_TYPE_STRING = 'string';
    const VALUE_TYPE_INTEGER = 'integer';
    const VALUE_TYPE_DATETIME = 'datetime';

    /**
     * @var
     */
    protected $name;

    /**
     * @var
     */
    protected $label;

    /**
     * @var
     */
    protected $value;

    /**
     * @var
     */
    protected $valueType = self::VALUE_TYPE_STRING;

    /**
     * @var ElementInterface
     */
    protected $formElement;

    /**
     * @param null $name
     * @param null $type
     */
    public function __construct($name = null, $type = null)
    {
    	if ($name) {
    		$this->setName($name);
    	}

        if ($type) {
            $this->setValueType($type);
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
     * @param $type
     * @return $this
     */
    public function setValueType($type)
    {
        $this->valueType = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValueType()
    {
        return $this->valueType;
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
     * @param $value
     * @return bool|int|string
     */
    protected function applyValueType($value)
    {
        $value = trim($value);

        switch ($this->valueType) {
            case self::VALUE_TYPE_INTEGER:
                $value = (integer) $value;
                break;

            case self::VALUE_TYPE_STRING:
                $value = (string) $value;
                break;
                
            case self::VALUE_TYPE_DATETIME:
                $value = date('Y-m-d H:i:s', strtotime($value));
                break;
                
            default:
                break;
        }
        
        return $value;
    }

    /**
     * @param $string
     * @return bool
     */
    public function isNotEmptyString($string)
    {
        return is_string($string) && !empty($string);
    }
}