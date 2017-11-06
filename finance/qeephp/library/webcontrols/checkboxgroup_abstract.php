<?php
// $Id: checkboxgroup_abstract.php 2283 2009-03-04 14:33:21Z lonestone $

/**
 * 定义 Control_CheckboxGroup_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 * http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: checkboxgroup_abstract.php 2283 2009-03-04 14:33:21Z lonestone
 * $
 * @package webcontrols
 */

/**
 * Control_CheckboxGroup_Abstract 是群组多选框的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: checkboxgroup_abstract.php 2283 2009-03-04 14:33:21Z lonestone
 * $
 * @package webcontrols
 */
abstract class Control_CheckboxGroup_Abstract extends QUI_Control_Abstract {
	protected function _make($type, $suffix) {
		static $id_index = 1;
		$items = (array)$this->_extract('items');
		$max = count($items);
		if ($max == 0)
			return '';
		
		$selected = $this->_extract('value');
		
		/**
		 * 处理set类型的字段，
		 * 因为set类型查询输出来是用,号隔开的字串，
		 * 所以必须要转换成数组
		 * 
		 * @author sqlhost
		 * @version 1.0.0
		 * 2012-4-13
		 *
		 * 增加处理中间表的情况
		 * 例如用户的角色，此时该字段的值是一个Role型数组
		 * @author sqlhost
		 * @version 1.0.1
		 * 2012-4-13
		 */
		if ($type == 'checkbox') {
			// 处理中间表
			if (is_object($selected)) {
				$selected = Helper_Array::toHashmap($selected, 'id', 'id');
			}
			elseif (is_array($selected)) {
				
			}
			else {
				// 处理set类型的字段
				$selected = array_filter(explode(',', $selected), 'trim');
			}
		}
		// -
		$caption_class = $this->_extract('caption_class');
		$smart_style = $this->_extract('smartstyle');
		
		$out = '';
		foreach ($items as $value => $caption) {
			$checked = false;
			if (is_array($selected)) {
				if (in_array($value, $selected))
					$checked = true;
			}
			else {
				if ($value == $selected && strlen($value) == strlen($selected) && strlen($selected) > 0) {
					$checked = true;
				}
			}
			
			if ($smart_style) {
				$name = $this->id() . $suffix;
				$id = $this->id() . "_{$id_index}";
				$id_index ++;
				$out .= "<label for=\"{$id}\" id=\"{$id}_label\" class=\"radio\">";
				$out .= "<input type=\"{$type}\" ";
				$out .= "name=\"{$name}\" ";
				$out .= "id=\"{$id}\" ";
				// 			if (strlen($value) == 0) {
				// 				$value = 1;
				// 			}
				$out .= 'value="' . htmlspecialchars($value) . '" ';
				$out .= $this->_printAttrs();
				$out .= $this->_printChecked();
				$out .= $this->_printDisabled();
				if ($checked) {
					$out .= 'checked="checked" ';
				}
					
				$out .= "><i></i>{$caption}</label>";
			}
			else {
				$out .= "<input type=\"{$type}\" ";
				$name = $this->id() . $suffix;
				$id = $this->id() . "_{$id_index}";
				$out .= "name=\"{$name}\" ";
				$id_index ++;
				$out .= "id=\"{$id}\" ";
				// 			if (strlen($value) == 0) {
				// 				$value = 1;
				// 			}
				$out .= 'value="' . htmlspecialchars($value) . '" ';
				$out .= $this->_printAttrs();
				$out .= $this->_printChecked();
				$out .= $this->_printDisabled();
				if ($checked) {
					$out .= 'checked="checked" ';
				}
					
				$out .= '>';
				if ($caption) {
					$out .= Q::control('label', "{$id}_label", array(
							'for' => $id,
							'caption' => $caption,
							'class' => $caption_class
					))->render();
				}
			}
		}
		
		return $out;
	}
}

