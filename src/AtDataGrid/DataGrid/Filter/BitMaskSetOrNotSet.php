<?php

/**
 * @category   ATF
 * @package    ATF_DataGrid
 * @subpackage ATF_DataGrid_Filter
 */
class ATF_DataGrid_Filter_BitMaskSetOrNotSet extends ATF_DataGrid_Filter_Abstract
{
    /**
     * Returns the result of applying $value
     *
     * @param  mixed $value
     * @return mixed
     */
    public function apply($select, $column, $value)
    {
        $value = $this->_applyValueType($value);

        if (strlen($value) > 0) {
            $columnName = $this->_findTableColumnName($select, $column->getName());
            
            if ($value > 0) {
                $select->where($columnName . ' & ? = ' . $value, $value);    
            } else {
                $select->where($columnName . ' & ? = 0', -$value);    
            }
        }
    }
}