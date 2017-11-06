<?php
/**
 * 不同的有关联的两个模型之间的两级关联
 *
 * @author sqlhost
 * @version v1.0.1
 * 
 * @param parent_items: 上层选项
 * @param currentId: 当前
 * @param child: 子项名称
 * @param caption：标题(可选)
 * @param obj：下拉框名(必选)
 */
class Control_Rradio extends QUI_Control_Abstract {
	function render() {
		if (! is_array($this->items) || ! is_array($this->parent_items)) {
			return "";
		}
		
		$out = '';
		
		foreach ($this->parent_items as $key => $val) {
			$out .= "<div id=\"" . $this->assoc_class . "_".$key."\">\n";
			$out .= "<span>".$val . ": </span>\n";
			foreach ($this->items as $k => $v) {
				$checked = '';
				if ($v[$this->assoc_id] != $key) {
					continue;
				}
				if (array_key_exists($k, $this->current_id)) {
					$checked = 'checked="checked"';
				}
				$out .= "<input type=\"radio\" id=\"" . $this->id() . "_" . $k . "\" name=\"" . $this->id() . "[".$key."]\" value=\"".$k."\"" . $checked . " />&nbsp;<label for=\"" . $this->id() . "_" . $k . "\">" . $v['name'] . "</label>";
			}
			$out .= "</div>\n";
		}
		return $out;
	}

}


