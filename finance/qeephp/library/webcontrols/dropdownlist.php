<?php
// $Id: dropdownlist.php 2014 2009-01-08 19:01:29Z dualface $


/**
 * 定义 Control_DropdownList 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 *            http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: dropdownlist.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * Control_DropdownList 构造一个下拉列表框
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: dropdownlist.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */
class Control_DropdownList extends QUI_Control_Abstract {

	function render() {

		$items = $this->_extract('items');
		
		$selected = $this->_extract('value');
		// 处理中间表
		if (is_object($selected)) {
			$selected = Helper_Array::toHashmap($selected, 'id', 'id');
		}
		elseif (is_array($selected)) {

		}
		// 处理set类型的字段
		else {
			$selected = array_filter(explode(',', $selected), 'trim');
		}
		
		$out = '<select ';
		$out .= $this->_printIdAndName();
		$out .= $this->_printDisabled();
		$out .= $this->_printAttrs();
		$out .= ">\n<option value=\"\">－请选择－</option>\n";
		
		foreach ((array)$items as $value => $caption) {$checked = false;
			if (is_array($selected)) {
				if (in_array($value, $selected))
					$checked = true;
			}
			else {
				if ($value == $selected && strlen($value) == strlen($selected) && strlen($selected) > 0) {
					$checked = true;
				}
			}
			$out .= '<option value="' . htmlspecialchars($value) . '" ';
			if ($checked) {
				$out .= 'selected="selected" ';
			}
			$out .= '>';
			$out .= htmlspecialchars($caption);
			$out .= "</option>\n";
		}
		$out .= "</select>\n";
		
		return $out;
	}
}

