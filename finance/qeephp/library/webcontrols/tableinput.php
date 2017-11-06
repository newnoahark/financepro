<?php
/**
 * 表格型输入框
 * 
 * @author zhaoyu
 * @version 1.0.0
 */
class Control_TableInput extends QUI_Control_Abstract {
	function render() {
		$this->_view['id'] = $this->id();
		$this->_view['value'] = $this->value;
		$this->_view['size'] = (int)$this->size;
		$this->_view['color'] = $this->color;
		$this->_view['label'] = $this->_label;
		$this->_view['field'] = is_array($this->field) ? $this->field : explode(',', $this->field);
		
		return $this->_fetchView(dirname(__FILE__) . '/tableinput_view');
	}
}


