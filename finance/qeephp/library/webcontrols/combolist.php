<?php
// $Id: combolist.php 2014 2013-09-12 sqlhost $


/**
 * 定义 Control_ComboList 类，构造一个固定数量的级联菜单
 *
 * 注：实际上只是定义了下拉列表框，级联效果交由前端实现，适用于不同的模型间的级联，同一模型的级联请使用combotree
 *
 * @link http://labphp.com/
 * @copyright Copyright (c) 2006-2009 LabPHP Inc.
 * @link http://www.labphp.com
 * @license New BSD License {@link http://labphp.com/license/}
 * @version $Id: combolist.php 2014 2013-09-12 sqlhost $
 * @package webcontrols
 */
class Control_ComboList extends QUI_Control_Abstract {

	function render() {

		$value = '';
		if (is_array($this->value)) {
			foreach ( $this->value as $val ) {
				$value = $value ? $val . "," : $val;
			}
		} elseif (is_object($this->value)) {
			$value = isset($this->value->id) ? $this->value->id : '';
		} else {
			$value = $this->value;
		}
		
		if ($this->param) {
			$size = intval($this->size) ? intval($this->size) : 220;
			$out = "<input id=\"" . $this->id . "\" name=\"" . $this->id . ($this->multiple ? "[]" : "") . "\" class=\"easyui-combobox\" data-options=\"valueField:'id',textField:'text', disabled: " . ($this->disabled ? 'true' : 'false') . ", readonly: " . ($this->readonly ? 'true' : 'false') . ", url:'" . url($this->url, ($this->multiple ? array(
					'noDefault' => 1,
					'param' => $this->param 
			) : array())) . "',multiple:" . ($this->multiple ? "true" : "false") . ",required:" . ($this->require ? "true" : "false") . "\" value=\"" . $value . "\" style=\"width: {$size}px;\">";
		} else {
			$size = intval($this->size) ? intval($this->size) : 220;
			$out = "<input id=\"" . $this->id . "\" name=\"" . $this->id . ($this->multiple ? "[]" : "") . "\" class=\"easyui-combobox\" data-options=\"valueField:'id',textField:'text', disabled: " . ($this->disabled ? 'true' : 'false') . ", readonly: " . ($this->readonly ? 'true' : 'false') . ", url:'" . url($this->url, ($this->multiple ? array(
					'noDefault' => 1 
			) : array())) . "',multiple:" . ($this->multiple ? "true" : "false") . ",required:" . ($this->require ? "true" : "false") . "\" value=\"" . $value . "\" style=\"width: {$size}px;\">";
		}
		
		return $out;
	}
}

