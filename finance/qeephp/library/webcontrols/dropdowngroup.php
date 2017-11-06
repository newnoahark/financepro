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
class Control_DropdownGroup extends QUI_Control_Abstract {

	function render() {
		
		$parent_items = $this->_extract('parent_items');
		$items = $this->_extract('items');
		$upField = $this->_extract('upField');
		$upField = isset($upField) ? $upField : 'submodule_id';
		
		$selected = $this->_extract('value');

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
		
		$out = "";
		$out = '<select ';
		$out .= $this->_printIdAndName();
		$out .= $this->_printDisabled();
		$out .= $this->_printAttrs();
		$out .= ">\n";
		foreach ($parent_items as $key => $val) {
			$out .= "<optgroup label=\"{$val}\">\n";
			foreach ($items as $k => $v) {
				if ($v[$upField] != $key) {
					continue;
				}
				$out .= "<option value=\"{$v['id']}\">{$v['name']}</option>\n";
			}
			$out .= "</optgroup>\n";
		}
		$out .= "</select>\n";
		
		return $out;
	}
}

