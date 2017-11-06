<?php
/**
 * Control_Checkbox_Abstract 是所有多选框的基础类类
 *
 * @author zhao yu
 * @package webcontrols
 */
class Control_Morehidden extends QUI_Control_Abstract{
	function render(){
        $out =  '';
        $value = $this->_extract('value');
        $valueName = strlen($this->value_name) > 0 ? $this->value_name : "id";
        if(count($value) > 0){
        	foreach($value as $val){
        		$out .= '<input type="hidden" ' . $this->_printIdAndName() . ' value="' . $val->$valueName . '" >';
        	}
        } else {
        	$out .= '<input type="hidden" ' . $this->_printIdAndName() . ' value="" >';
        }
    	return $out;
	}
}

