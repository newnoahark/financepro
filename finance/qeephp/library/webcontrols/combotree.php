<?php

/**
 * Control_Combotree
 * 
 * @author sqlhost
 * @version v1.0.1
 *         
 */
class Control_Combotree extends QUI_Control_Abstract {

	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see QUI_Control_Abstract::render()
	 */
	function render() {

		$value = '';
		if (is_array ( $this->value )) {
			foreach ( $this->value as $val ) {
				$value = $value ? $val . "," : $val;
			}
		} elseif ($this->value instanceof QColl) {
			$value = Helper_Array::implode ( $this->value, ',', 'id', 'id' );
		} elseif (is_object ( $this->value )) {
			$value = isset ( $this->value->id ) ? $this->value->id : '';
		} else {
			$value = $this->value;
		}
		
		if ($this->param) {
			$size = isset ( $this->size ) ? $this->size : 100 . '%';
			$out = "<input id=\"" . $this->id . "\" name=\"" . $this->id . ($this->multiple ? "[]" : "") . "\" class=\"easyui-combotree\" data-options=\"queryParams:" . ($this->queryParams ? $this->queryParams : '{}') . ",editable: " . ($this->editable ? 'true' : 'false') . ",onlyLeafCheck: " . ($this->onlyLeafCheck ? 'true' : 'false') . ", disabled: " . ($this->disabled ? 'true' : 'false') . ", readonly: " . ($this->readonly ? 'true' : 'false') . ", url:'" . url ( $this->url, ($this->multiple ? array (
					'noDefault' => 1,
					'param' => $this->param 
			) : array ()) ) . "',cascadeCheck:" . ($this->cascadeCheck ? "true" : "false") . ",multiple:" . ($this->multiple ? "true" : "false") . ",required:" . ($this->require ? "true" : "false") . "\" value=\"" . $value . "\" style=\"width: {$size};\">";
		} else {
			$size = isset ( $this->size ) ? $this->size : 100 . '%';
			$out = "<input id=\"" . $this->id . "\" name=\"" . $this->id . ($this->multiple ? "[]" : "") . "\" class=\"easyui-combotree\" data-options=\"queryParams:" . ($this->queryParams ? $this->queryParams : '{}') . ", editable: " . ($this->editable ? 'true' : 'false') . ",onlyLeafCheck: " . ($this->onlyLeafCheck ? 'true' : 'false') . ", disabled: " . ($this->disabled ? 'true' : 'false') . ", readonly: " . ($this->readonly ? 'true' : 'false') . ", url:'" . url ( $this->url ) . "',cascadeCheck:" . ($this->cascadeCheck ? "true" : "false") . ",multiple:" . ($this->multiple ? "true" : "false") . ",required:" . ($this->require ? "true" : "false") . "\" value=\"" . $value . "\" style=\"width: {$size};\">";
		}
		
		return $out;
	}
}


