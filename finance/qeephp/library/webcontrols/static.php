<?php
// $Id: static.php 2014 2009-01-08 19:01:29Z dualface $

/**
 * 定义 Control_Static 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 * http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: static.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 */

/**
 * 构造一个静态控件
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: static.php 2014 2009-01-08 19:01:29Z dualface $
 * @package webcontrols
 *
 * @author sqlhost
 * @version 1.0.0 (2012-4-14)
 * 增加日期时间的显示功能
 */
class Control_Static extends QUI_Control_Abstract {
	function render() {
		$datetime = $this->_extract('datetime');
		$html = $this->_extract('html');
		$out = '<span ';
		$out .= $this->_printIdAndName();
		$out .= $this->_printAttrs();
		$out .= '>';
		$out .= $datetime ? date($datetime, $this->value) : ($html ? $this->value: htmlspecialchars($this->value));
		$out .= '</span>';
		
		return $out;
	}
}
