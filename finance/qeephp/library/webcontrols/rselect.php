<?php

/**
 * 级联下拉列表控件
 *
 * @author sqlhost
 * @version 1.1.0
 *         
 * @param
 *        root_id: 起始ID(没有设为0)，用于起始分类
 *        parent_id：父ID，用于确定上层分类
 *        current_id：当前ID号(可选)，用于移动分类
 *        model：模型(必选)
 *        caption：标题(可选)
 */
class Control_RSelect extends QUI_Control_Abstract {

	function render() {
		// 如果记录为空
		if (count($this->items) == 0) {
			return "<select name=\"{$this->id()}\"><option value=\"0\"></option></select>";
		}
		$nodes = array();
		// 如果没有设置起始ID
		if (!isset($this->root_id)) {
			$this->root_id = '0';
		}
		/**
		 * 设置当前节点
		 */
		// 如果设置了当前ID
		if ($this->current_id > 0) {
			// 如果设置了上级ID，也就是修改了上级ID
			if (isset($this->parent_id)) {
				if ($this->parent_id > 0) {
					// 获得父ID的所有结点
					$parent = $this->items[$this->parent_id];
					$nodes = array_filter(explode('_', $parent->node), 'trim');
					array_push($nodes, $this->parent_id);
					// 在节点的开头加上0，即从0开始
					array_unshift($nodes, '0');
				}
				else {
					$nodes[] = 0;
				}
			}
			// 如果没有设置上级ID，就从根ID到当前ID的上级ID
			else {
				// 获得当前ID的所有结点
				$current = $this->items[$this->current_id];
				$nodes = array_filter(explode('_', $current->node), 'trim');
				// 在节点的开头加上0，即从0开始
				array_unshift($nodes, '0');
			}
		}
		// 如果没有设置当前ID，但是设置了上级ID，那么就从根ID显示到上级ID
		elseif ($this->parent_id > 0) {
			// 获得父ID的所有结点
			$parent = $this->items[$this->parent_id];
			$nodes = array_filter(explode('_', $parent->node), 'trim');
			array_push($nodes, $this->parent_id);
			// 在节点的开头加上0，即从0开始
			array_unshift($nodes, '0');
		}
		// 如果没有设置当前ID和上级ID，则只显示一个根ID
		else {
			$nodes[] = $this->root_id;
		}
		
		// 如果所有结点不包含起始ID
		if (!array_key_exists($this->root_id, $nodes)) {
			unset($nodes);
			$nodes[] = $this->root_id;
		}
		else {
			// 如果起始ID > 0，则将起始ID之前的节点移除
			if ($this->root_id > 0) {
				foreach ($nodes as $k => $v) {
					if ($v == $this->root_id) {
						break;
					}
					array_shift($nodes);
				}
			}
		}
		// 		echo "root: ".$this->root_id; echo " | parent: " . $this->parent_id; echo " | current:".$this->current_id." | nodes: ";print_r($nodes);
		// -
		$out = $this->getOut($nodes);
		return $out;
	}
	// 递归调用生成所有下接框
	protected function getOut($nodes) {

		$str = '';
		
		if (count($nodes) > 0) {
			$node = array_shift($nodes);
			
			$children = Helper_Array::getChildren($this->items, $node);
			$children = Helper_Array::removeKey($children, $this->current_id);
			
			if (count($children) == 0 && $node > 0) {
				return "";
			}
			$str .= "<select onChange=\"jQuery.getRSelect(this, '" . $this->id() . "', '" . $this->model . "', '" . $this->current_id . "', '" . $this->caption . "', '" . $this->callback . "', '" . $this->filters . "');\" id='" . $this->model . "_" . $node . "' name='" . $this->model . "_" . $node . "'>";
			$str .= "<option value=\"0\">" . $this->_label . "</option>";
			foreach ($children as $key => $val) {
				$str .= "<option value=\"" . $key . "\"" . (count($nodes) > 0 && $key == $nodes[0] ? " selected" : "") . ">" . $val->name . "</option>";
			}
			$str .= "</select>" . $this->getOut($nodes);
		}
		
		return $str;
	}
}


