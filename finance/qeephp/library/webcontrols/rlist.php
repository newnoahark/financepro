<?php
/**
 * 不同的有关联的两个模型（或给定的两个相关的数组）之间的两级关联下拉列表框
 *
 * @author sqlhost
 * @version v1.0.1
 *
 * @param
 * upid: 上层ID(可选)
 * @param
 * currentId: 当前ID号(可选)
 * @param
 * child: 子项名称
 * @param
 * caption：标题(可选)
 * @param
 * obj：下拉框名(必选)
 */
class Control_Rlist extends QUI_Control_Abstract {
	function render() {
		if (! is_array($this->items)) {
			return "";
		}
		
		$out = "<select name=\"rlistParent\" onChange=\"setChild(this.options[this.selectedIndex].value);\" id=\"rlistParent\">\n\r<option value=\"0\">" . $this->caption . "</option>\n";
		
		foreach ($this->parent_items as $key => $val) {
			$out .= "<option value=\"" . $key . "\">" . $val . "</option>\n";
		}
		$out .= "</select>\n<select name=\"" . $this->id() . "\" id=\"" . $this->id() . "\"><option value=\"0\">" . $this->child_caption . "</option></select>";
		$out .= "<script type=\"text/javascript\" language=\"javascript\">\n
			var rlist = new Array();\nrlist[0] = new Array();\nrlist[0][0] = new Array(\"" . $this->child_caption . "\");\n";
		
		foreach ($this->parent_items as $key => $val) {
			$out .= "rlist[" . $key . "] = new Array();\n";
			$out .= "rlist[" . $key . "][0] = new Array(\"" . $this->child_caption . "\");";
			foreach ($this->items as $k => $v) {
				if ($v[$this->associate] == $key) {
					$out .= "rlist[" . $key . "][" . $k . "] = \"" . $v['name'] . "\";\n";
				}
			}
		}
		
		if ($this->current_id > 0) {
			$parent_id = $this->items[$this->current_id][$this->associate];
			
			$out .= "$(\"select[name=rlistParent]\").val(" . $parent_id . ");\n";
			$out .= "setChild(" . $parent_id . ");\n";
			$out .= "$(\"select[name=" . $this->id() . "]\").val(" . $this->current_id . ");\n";
		}
		elseif ($this->parent_id > 0) {
			$out .= "$(\"select[name=rlistParent]\").val(" . $this->parent_id . ");\n";
			$out .= "setChild(" . $this->parent_id . ");\n";
		}
		
		// if (intval($this->current_id) > 0)
		// {
		// $out .= "var is_found = false\n";
		// $out .= "for (key in rlist)\n";
		// $out .= "{\n";
		// $out .= "for (k in rlist[key])";
		// $out .= "{\n";
		// $out .= "if (k == ".$this->current_id.")\n";
		// $out .= "{\n";
		// $out .= "$(\"select[name=rlistParent]\").val(key);\n";
		// $out .= "setChild(key)\n";
		// $out .= "$(\"select[name=".$this->id()."]\").val(k);\n";
		// $out .= "var is_found = true\n";
		// $out .= "break";
		// $out .= "}\n";
		// $out .= "}\n";
		// $out .= "if (is_found) break;\n";
		// $out .= "}\n";
		// }
		
		$out .= "function setChild(upid)\n";
		$out .= "{\n";
		$out .= "$(\"select[name=" . $this->id() . "]\").empty();\n";
		$out .= "len = rlist[upid].length;\n;";
		$out .= "if(len == 0) return;\n";
		$out .= "for(key in rlist[upid])\n";
		$out .= "{\n";
		$out .= "$(\"select[name=" . $this->id() . "]\").append(\"<option value='\" + key + \"'>\" + rlist[upid][key] + \"</option>\");\n";
		$out .= "}\n";
		$out .= "}\n";
		$out .= "</script>";
		
		return $out;
	}

}


