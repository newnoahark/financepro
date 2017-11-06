<?php

/**
 * 无限分类行为插件
 *
 * @author sqlhost
 * @version 2.1.0 2013-9-29
 */
class Model_Behavior_Treeview extends QDB_ActiveRecord_Behavior_Abstract {

	/**
	 * 插件的设置信息
	 *
	 * @var array
	 * @param string controller 模型对应的控制器名称
	 * @param string type - 'node' 父子ID加节点法 - 'sort' 父子ID加预排序遍历法
	 */
	protected $_settings = array(
			'controller' => 'category'
	);

	/**
	 * 保存状态
	 *
	 * @var array
	 */
	protected $_saved_state = array();
	
	// 保存列表
	protected $_items = array();

	private $_tableName;

	private $_db;

	/**
	 * 绑定行为插件
	 */
	function bind() {
		
		// 获得数据接口
		$this->_addEventHandler(self::BEFORE_CREATE, array(
				$this,
				'_before_create'
		));
		$this->_addEventHandler(self::BEFORE_UPDATE, array(
				$this,
				'_before_update'
		));
		$this->_addEventHandler(self::BEFORE_DESTROY, array(
				$this,
				'_before_destroy'
		));
		$this->_addStaticMethod('rebuildNode', array(
				$this,
				'rebuildNode'
		));
		$this->_addStaticMethod('toTree', array(
				$this,
				'toTree'
		));
		$this->_addStaticMethod('printTree', array(
				$this,
				'printTree'
		));
		$this->_addStaticMethod('getAncestors', array(
				$this,
				'getAncestors'
		));
		$this->_addStaticMethod('getDescendants', array(
				$this,
				'getDescendants'
		));
	}

	/**
	 * 在数据库中创建 ActiveRecord 对象前调用
	 * 插入前设置当前祖先和子孙节点
	 */
	function _before_create(QDB_ActiveRecord_Abstract $obj) {

		$this->_beforeCreateByNode($obj);
	}

	/**
	 * 更新分类
	 */
	function _before_update(QDB_ActiveRecord_Abstract $obj) {

		$this->_beforeUpdateByNode($obj);
	}

	/**
	 * 删除分类
	 *
	 * @param QDB_ActiveRecord_Abstract $current
	 */
	function _before_destroy(QDB_ActiveRecord_Abstract $obj) {

		$this->_beforeDeleteBySort($obj);
	}

	/**
	 * 重建节点
	 */
	function rebuildNode() {
		// 先将node, seed重置
		$this->_meta->updateDbWhere(array(
				'node' => '_'
		), "1 = 1");
		// 查询所有记录
		$rows = $this->_meta->find()->getAll();
		foreach ($rows as $row) {
			$row->node = $this->_getNode($row);
			$row->save();
		}
	}

	private function _beforeCreateBySort(QDB_ActiveRecord_Abstract $obj) {

		if ($obj->upid == 0) {
			
			$lft = $this->_meta->table->select()->max('rgt')->query();
			$obj->lft = $lft['max_value'] + 1;
			$obj->rgt = $obj->lft + 1;
		}
		else {
			$parent = $obj->parent;
			$obj->lft = $parent->lft + 1;
			$obj->rgt = $obj->lft + 1;
			
			$expr = new QDB_Expr("lft = lft + 2");
			// 			$this->_meta->table->update($expr, 'lft > ' . $parent->lft);
			$this->_meta->updateDbWhere($expr, 'lft > ' . $parent->lft);
			$expr = new QDB_Expr("rgt = rgt + 2");
			$this->_meta->updateDbWhere($expr, 'rgt > ' . $parent->lft);
		}
	}

	private function _beforeCreateByNode(QDB_ActiveRecord_Abstract $obj) {

		$obj->node = $this->_getNode($obj);
	}

	private function _beforeUpdateByNode(QDB_ActiveRecord_Abstract $obj) {
		
		// 更新前的节点及上级分类
		$node = $obj->node;
		$nodes = array_filter(explode('_', $node), 'trim');
		$upid = end($nodes);
		// 更新后的节点
		$obj->node = $this->_getNode($obj);
		// 如果上级分类改变了
		if ($node !== $obj->node) {
			// 自己不能移动到自己的后辈
			if ($obj->upid == $obj->id()) {
				throw new Treeview_MoveToMyselfException("上级分类不能为当前分类");
			}
			// 父亲不能移动到儿子的后辈
			$children = $this->_meta->find("node like '%_" . $obj->id() . "_%'")->getAll();
			$childrenIds = Helper_Array::toHashmap($children, 'id', 'id');
			if (in_array($obj->upid, $childrenIds)) {
				throw new Treeview_MoveToChildException("上级分类不能为当前分类的子分类");
			}
			
			// 移动所有后代
			$expr = new QDB_Expr("node = replace(node, '" . $node . $obj->id() . "_', '" . $obj->node . $obj->id() . "_')");
			$this->_meta->updateDbWhere($expr, "node like '%" . $node . $obj->id() . "_%'");
		}
	}

	private function _beforeDeleteBySort(QDB_ActiveRecord_Abstract $obj) {

		$value = $obj->rgt - $obj->lft + 1;
		
		$this->_meta->destroyWhere("lft > ? and rgt < ?", $obj->lft, $obj->rgt);
		
		$expr = new QDB_Expr("lft = lft - " . $value);
		$this->_meta->updateDbWhere($expr, 'lft > ' . $obj->lft);
		
		$expr = new QDB_Expr("rgt = rgt - " . $value);
		$this->_meta->updateDbWhere($expr, 'rgt > ' . $obj->rgt);
	}

	/**
	 * 获得当前节点
	 */
	private function _getNode(QDB_ActiveRecord_Abstract $obj) {

		$parent = $obj->parent;
		if (!$parent->id()) {
			return "_";
		}
		else {
			return $parent->node . $parent->id() . "_";
		}
	}

	/**
	 * 获得所有祖先
	 */
	public function getAncestors(QDB_ActiveRecord_Abstract $obj, $root = 0) {

		$nodes = Helper_Array::toArray($obj->node, '_');
		rsort($nodes);
		$ancestors = array();
		foreach ($nodes as $val) {
			if ($val === $root) {
				break;
			}
			$ancestors[] = $obj->find("id = ?", $val);
		}
		rsort($ancestors);
		return $ancestors;
	}

	/**
	 * 获得所有后代
	 */
	public function getDescendants(QDB_ActiveRecord_Abstract $obj) {

		$node = $obj->node;
		$rows = $obj->find("node like '%?%'", $node);
		return $rows;
	}

	/**
	 * 获得树型数组
	 */
	public function toTree(QColl $rows) {

		$array = $rows->toArray();
		
		return Helper_Array::toTree($array, 'id', 'upid', 'children');
	}

	function printTree(& $tree, $udi = '', $args = array(), $target = '_self') {

		if (empty($tree) || !is_array($tree)) {
			return '';
		}
		$string = "";
		
		foreach ($tree as $item) {
			$url = $udi ? url($udi, array_merge($args, array(
					"id" => $item['id']
			))) : url($this->_settings['controller'] . "/index", array(
					'id' => $item['id']
			));
			$string .= "<li><span" . ($item['children'] ? " class=\"folder\"" : " class=\"file\"") . "><a href=\"" . $url . "\" target=\"" . $target . "\">" . $item['name'] . "</a></span>";
			if ($item['children']) {
				$string .= self::printTree($item['children']);
			}
			$string .= "</li>\n";
		}
		
		$string .= "";
		
		echo $string;
	}
}


