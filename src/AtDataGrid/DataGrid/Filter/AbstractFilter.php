<?php

namespace AtDataGrid\DataGrid\Filter;

/**
 *
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * Value types
     */
    const FILTER_VALUE_TYPE_STRING = 'string';
    const FILTER_VALUE_TYPE_INTEGER = 'integer';
    const FILTER_VALUE_TYPE_DATETIME = 'datetime';
    
    /**
     * 
     */
    protected $name;

    /**
     * 
     */
    protected $label;
    
    /**
     * 
     */
    protected $valueType;
    
    /**
     * 
     * @param $name
     */
    public function __construct($name = null, $valueType = self::FILTER_VALUE_TYPE_STRING)
    {
    	if (isset($name)) {
    		$this->setName($name);
    	}
    }
    
    /**
     * 
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * 
     */
    public function getName()
    {
        return $this->name;
    }    
    
    /**
     * 
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }
    
    /**
     * 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * 
     */
    public function setValueType($type)
    {
        $this->valueType = $type;
        return $this;
    }
    
    /**
     * 
     */
    public function getValueType()
    {
        return $this->valueType;
    }        
    
    /**
     * 
     */
    protected function applyValueType($value)
    {
        $value = trim($value);

        if (empty($value)) {
            return $value;
        }

        switch ($this->valueType) {
            case self::FILTER_VALUE_TYPE_INTEGER:
                $value = (integer) $value;
                break;

            case self::FILTER_VALUE_TYPE_STRING:
                $value = (string) $value;
                break;
                
            case self::FILTER_VALUE_TYPE_DATETIME:
                $value = date('Y-m-d H:i:s', strtotime($value));
                break;
                
            default:
                break;
        }
        
        return $value;
    } 

    /**
     * Формирует полное название поля для селекта. Учитывается название таблицы.
     * Сделано, так как в mySQL нельзя использовать псевдонимы полей в условии
     * where.
     * 
     * @author atukai
     * @todo Очень жестко зарефакторить этот говнокод
     */
    protected function findTableColumnName($select, $columnName)
    {
        $parsedName = explode('__', $columnName);
        
        if (count($parsedName) == 2) {
            $fullColumnName = $parsedName[0] . '.' . $parsedName[1];
        } else {        
        
            $selectColumns = $select->getPart(Zend_Db_Select::COLUMNS);
    
            foreach ($selectColumns as $column) {
                if (!$i = array_search($columnName, $column)) {
                    continue;
                }
            }
            
            if (false != $i) {
                $fullColumnName = $selectColumns[$i][0] . '.' . $selectColumns[$i][1];
            } else {
            	$fullColumnName = $selectColumns[0][0] . '.' . $columnName;
            }       
        }

        return $fullColumnName;
    }    
}