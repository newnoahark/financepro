<?php

/**
 * 定义 Control_Ajaxtree 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: checkbox.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个多选框
 *
 * @author zhaoyu
 * @version v1.0 2015年10月31日 12:06:20
 * @package webcontrols
 */
class Control_Ajaxtree extends QUI_Control_Abstract
{
	function render()
	{
		$this->_view['items'] = $this->_extract('items');
		$this->_view['id'] = $this->id;
		$this->_view['style'] = $this->style;
		$this->_view['upfield'] = isset($this->upfield) ? $this->upfield : 'parent_id';
		$this->_view['type'] = isset($this->type) ? $this->type : 'checkbox';
		$this->_view['url'] = $this->url;
		$this->_view['fillfield'] = !is_null($this->fillfield) ? $this->fillfield : '';
		$this->_view['currentName'] = isset($this->currentName) ? $this->currentName : '';
		$this->_view['values'] = isset($this->values) ? $this->values : '';
		$this->_view['value'] = isset($this->value) ? $this->value : '';
		return $this->_fetchView(dirname(__FILE__) . '/ajaxtree_view');
	}
}

