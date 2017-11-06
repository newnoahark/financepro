<?php

/**
 * 相关主题控件
 *
 * @author sqlhost
 * @version 1.0.0
 * @param string $modelName
 *        相关的模型
 * @param string|array $filters
 *        选择列表的过滤条件
 * @param string $order
 *        排序方式
 * @param int $page
 *        当前显示第几页
 * @param array $filterItems
 *        参与过滤条件下的字段
 * @param object $form
 *        使用的表单对象
 *        
 */
class Control_RPost extends QUI_Control_Abstract {

	function render() {
		$this->_view['id'] = $this->id();
		$this->_view['value'] = $this->value ? $this->value : array();
		$this->_view['url'] = $this->url;
		$this->_view['filterItems'] = $this->filterItems ? $this->filterItems : array();
		$this->_view['idField'] = $this->idField;
		$this->_view['caption'] = $this->caption;
		
		// 获得列表字段{field: 'id', title: 'ID', width: 50, checkbox: true}
		$columns = $this->columns;
		$this->_view['columns'] = $columns;
		
		return $this->_fetchView(dirname(__FILE__) . '/rpost_view');
	}
}
