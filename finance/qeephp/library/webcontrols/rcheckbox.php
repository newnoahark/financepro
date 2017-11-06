<?php
/**
 * 级联下拉列表控件
 * 参数：
 * upid：最高ID号(没有设为0)
 * currentId：当前ID号(可选)
 * model：模型(必选)
 * caption：标题(可选)
 * obj：下拉框名(必选)
 */
class Control_Rcheckbox extends QUI_Control_Abstract {
	function render() {
		$this->_view['id'] = $this->id();
		$this->_view['caption'] = $this->caption;
		$value = $this->value ? $this->value : array();
		$this->_view['value'] = $value;
		$this->_view['selected'] = Helper_Array::toHashmap($value, 'id', 'id');
		$this->_view['model'] = $this->model;
		$this->_view['root_id'] = $this->root_id;
		
		$model = new $this->model();
		$items = $model->cache();
		$currentItems = array();
		foreach ($items as $item) {
			if ($item['upid'] == $this->root_id) {
				$currentItems[$item['id']] = $item;
			}
		}
		$this->_view['items'] = $currentItems;
		
		return $this->_fetchView(dirname(__FILE__) . '/rcheckbox_view');
	}
}


