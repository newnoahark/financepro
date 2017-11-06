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
class Control_Date extends QUI_Control_Abstract {

	function render() {

		$required = isset($this->required) ? $this->required : 'false';
		$value = $this->value;
		if(is_numeric($value) && date('Y-m-d', $value)){
			$value = date('Y-m-d', $value);
		}
		$name = isset($this->id) ? $this->id : '';
		$out = "<input class=\"easyui-datebox\" name=\"{$name}\" data-options=\"required: {$required}, formatter:myformatter,parser:myparser\" value=\"" . $value . "\">";
		
		$out .= <<<EOD
<script type="text/javascript">
	function myformatter(date) {
		var y = date.getFullYear();
		var m = date.getMonth()+1;
		var d = date.getDate();
		return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
	}
	function myparser(s) {
		if (!s) return new Date();
		var ss = (s.split('-'));
		var y = parseInt(ss[0],10);
		var m = parseInt(ss[1],10);
		var d = parseInt(ss[2],10);
		if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
			return new Date(y,m-1,d);
		} else {
			return new Date();
		}
	}
</script>
EOD;
		return $out;
	}
}

