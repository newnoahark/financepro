<?php
// $Id: dropdownlist.php 2014 2009-01-08 19:01:29Z dualface $


/**
 * 定义 Control_LabSelect 类
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
class Control_LabSelect extends QUI_Control_Abstract {

// 	function render() {


// 		$items = $this->_extract('items');


// 		$selected = $this->_extract('value');
// 		// 处理中间表
// 		if (is_object($selected)) {
// 			$selected = Helper_Array::toHashmap($selected, 'id', 'id');
// 		}
// 		elseif (is_array($selected)) {


// 		}
// 		// 处理set类型的字段
// 		else {
// 			$selected = array_filter(explode(',', $selected), 'trim');
// 		}


// 		$out = '<select ';
// 		$out .= $this->_printIdAndName();
// 		$out .= $this->_printDisabled();
// 		$out .= $this->_printAttrs();
// 		$out .= ">\n<option value=\"0\">－请选择－</option>\n";


// 		foreach ((array)$items as $value => $caption) {$checked = false;
// 			if (is_array($selected)) {
// 				if (in_array($value, $selected))
// 					$checked = true;
// 			}
// 			else {
// 				if ($value == $selected && strlen($value) == strlen($selected) && strlen($selected) > 0) {
// 					$checked = true;
// 				}
// 			}
// 			$out .= '<option value="' . htmlspecialchars($value) . '" ';
// 			if ($checked) {
// 				$out .= 'selected="selected" ';
// 			}
// 			$out .= '>';
// 			$out .= htmlspecialchars($caption);
// 			$out .= "</option>\n";
// 		}
// 		$out .= "</select>\n";


// 		return $out;
// 	}
	function render() {

		$items = $this->_extract('items');
		
		$selected = $this->_extract('value');
		
		$size = $this->_extract('size');
		
		$multi = $this->_extract('multi');
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
		
		$echo_multi = ($multi)?'multi':'';
		
		$echo_size  = ($size)?',"size":"' . $size . '"':'';
		
		$out = "<div class='lab-select " . $echo_multi . " dropdown' select-data='{\"inputName\":\"" . $this->id() . "\"" . $echo_size . "}' ";
		$out .= $this->_printIdAndName();
		$out .= $this->_printDisabled();
		$out .= $this->_printAttrs();
		$out .= ">";
		$out .= '<div class="input">';
		$out .= '<ul>';
		foreach ((array)$items as $value => $caption) {
			if (is_array($selected)) {
				
				if (in_array($value, $selected)){
					$out .= '<li>';
					$out .= $caption;
					$out .= '<input type="hidden" value="' . $value . '" name="' . $this->id() . '[]">';
					$out .= '</li>';
				}
			}
			else{
				if ($value == $selected && strlen($value) == strlen($selected) && strlen($selected) > 0) {
					$out .= $caption;
					$out .= '<input type="hidden" value="' . $value . '" name="' . $this->id() . '">';
				}
			}
		}
		$out .= '</ul>';
		$out .= '<span class="switcher switcher-down"></span>';
		$out .= '</div>';
		$out .= '<ul class="sub border-solid border-gray border white-bg">';
		
		foreach ((array)$items as $value => $caption) {
			$out .= '<li class="submenu option" ref="' . htmlspecialchars($value) . '"><a href="javascript;" title="" class="ref">' . htmlspecialchars($caption) . '</a></li>';
		}
		$out .= '</ul>';
		$out .= '</div>';
		
		return $out;
	}
}

