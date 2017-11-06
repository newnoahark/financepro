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
class Control_DateTime extends QUI_Control_Abstract {

	function render() {

		$required = isset($this->required) ? $this->required : 'false';
		$show_seconds = isset($this->show_seconds) ? $this->show_seconds : 'false';
		$name = isset($this->id) ? $this->id : '';
		$out = "<input name=\"{$name}\" class=\"easyui-datetimebox\" data-options=\"required: {$required}, showSeconds: {$show_seconds}\" value=\"" . (empty($this->value) ? '' : date("Y-m-d H:i", $this->value)) . "\">";		
		return $out;
	}
}

