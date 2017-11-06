<?php

/**
 * 定义 Control_Bmap 类
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
 * @version v1.0 2015年10月27日 10:11:25
 * @package webcontrols
 */
class Control_Bmap extends QUI_Control_Abstract
{
	function render()
	{
		$this->_view['id'] = $this->id;
		$this->_view['value'] = $this->value;
		$this->_view['style'] = $this->style;
		return $this->_fetchView(dirname(__FILE__) . '/bmap_view');
	}
}

