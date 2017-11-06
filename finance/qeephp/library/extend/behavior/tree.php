<?php
// $Id$


/**
 * @file
 * 定义 Behavior_Tree 类
 *
 * @ingroup behavior
 *
 * @{
 */

/**
 * Behavior_Tree 使用改进型先根遍历算法存储树状结构
 */
class Model_Behavior_Tree extends QDB_ActiveRecord_Behavior_Abstract {

	/**
	 * 设置
	 *
	 * @var array
	 */
	protected $_settings = array(
			//! 父对象 ID 属性
			'parent_id_prop' => 'parent_id',
			//! 存储左值的属性
			'left_prop' => 'lft',
			//! 存储右值的属性
			'right_prop' => 'rgt',
			//! 父对象映射为对象的什么属性
			'parent_mapping' => 'parent_node',
			//! 子对象映射为对象的什么属性
			'childs_mapping' => 'child_nodes'
	);

	/**
	 * 绑定插件
	 */
	function bind() {

		$config = array(
				QDB::HAS_MANY => $this->_meta->class_name,
				'target_key' => $this->_settings['parent_id_prop']
		);
		$this->_meta->addProp($this->_settings['childs_mapping'], $config);
		$config = array(
				QDB::BELONGS_TO => $this->_meta->class_name,
				'source_key' => 'parent_id'
		);
		$this->_meta->addProp($this->_settings['parent_mapping'], $config);
		
		$this->_addEventHandler(self::BEFORE_CREATE, array(
				$this,
				'_before_create'
		));
		
		$this->_addEventHandler(self::AFTER_DESTROY, array(
				$this,
				'_after_destroy'
		));
		
		$this->_addDynamicMethod('getParentNode', array(
				$this,
				'getParentNode'
		));
		$this->_addDynamicMethod('getPathNodes', array(
				$this,
				'getPathNodes'
		));
		$this->_addDynamicMethod('getAllChildNodes', array(
				$this,
				'getAllChildNodes'
		));
		$this->_addDynamicMethod('getSiblingNodes', array(
				$this,
				'getSiblingNodes'
		));
		$this->_addDynamicMethod('getAllChildsCount', array(
				$this,
				'getAllChildsCount'
		));
		$this->_addDynamicMethod('destroyAllChildNodes', array(
				$this,
				'destroyAllChildNodes'
		));
		$this->_addDynamicMethod('deleteAllChildNodes', array(
				$this,
				'deleteAllChildNodes'
		));
	}

	/**
	 * 撤销绑定
	 */
	function unbind() {

		parent::unbind();
		$this->_meta->removeAssoc($this->_settings['childs_mapping']);
		$this->_meta->removeAssoc($this->_settings['parent_mapping']);
	}

	/**
	 * 在数据库中创建 ActiveRecord 对象前调用
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function _before_create(QDB_ActiveRecord_Abstract $obj) {

		/**
		 * 创建一个新节点时，需要更新其他节点的左值和右值
		 */
		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		$pid_pn = $this->_settings['parent_id_prop'];
		
		$parent = $this->getParentNode($obj);
		if ($parent->id()) {
			/**
			 * 设定当前对象的左值和右值
			 */
			$rgt = $parent->{$rgt_pn};
			$obj->{$lft_pn} = $rgt;
			$obj->{$rgt_pn} = $rgt + 1;
			
			/**
			 * 根据父节点的左值和右值更新其他节点的左值和右值
			 */
			$row = new QDB_Expr("{$lft_pn} = {$lft_pn} + 2");
			$this->_meta->updateDbWhere($row, "{$lft_pn} > {$rgt}");
			
			$row = new QDB_Expr("{$rgt_pn} = {$rgt_pn} + 2");
			$this->_meta->updateDbWhere($row, "{$rgt_pn} >= {$rgt}");
		}
		else {
			$obj->{$pid_pn} = 0;
			$obj->{$lft_pn} = 0;
			$obj->{$rgt_pn} = 1;
		}
	}

// 	function _after_create() {

// 		print_r(QDB::getConn()->getSql());
// 		exit();
// 	}

	/**
	 * 在数据库中删除记录后调用
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function _after_destroy(QDB_ActiveRecord_Abstract $obj) {

		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		
		/**
		 * 更新其他节点的左值和右值
		 */
		$value = $obj->{$rgt_pn} - $obj->{$lft_pn} + 1;echo $obj->name . " | " . $obj->id ." | ";echo $value;echo " | ";
		$row = new QDB_Expr("{$lft_pn} = {$lft_pn} - " . $value);
		$this->_meta->updateDbWhere($row, "{$lft_pn} > " . $obj->{$lft_pn});
		 echo QDB::getConn()->getLastSql();echo "<br>";
		$row = new QDB_Expr("{$rgt_pn} = {$rgt_pn} - " . $value);
		$this->_meta->updateDbWhere($row, "{$rgt_pn} > " . $obj->{$rgt_pn}); echo QDB::getConn()->getLastSql();echo "<br>";
	}

	/**
	 * 取得当前对象的父对象
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 *
	 * @return QDB_ActiveRecord_Abstract
	 */
	function getParentNode(QDB_ActiveRecord_Abstract $obj) {

		$pid_pn = $this->_settings['parent_id_prop'];
		return $this->_meta->find(array(
				reset($this->_meta->idname) => $obj->{$pid_pn}
		))->query();
	}

	/**
	 * 返回根节点到指定节点路径上的所有节点
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 *
	 * @return QDB_ActiveRecord_Association_Coll array
	 */
	function getPathNodes(QDB_ActiveRecord_Abstract $obj) {

		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		
		return $this->_meta->find("[{$lft_pn}] < ? AND [{$rgt_pn}] > ?", $obj->{$lft_pn}, $obj->{$rgt_pn})->all()->order("[{$lft_pn}] ASC")->query();
	}

	/**
	 * 返回指定节点的所有子节点
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 *
	 * @return QDB_ActiveRecord_Association_Coll array
	 */
	function getAllChildNodes(QDB_ActiveRecord_Abstract $obj) {

		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		
		return $this->_meta->find("[{$lft_pn}] BETWEEN ? AND ?", $obj->{$lft_pn}, $obj->{$rgt_pn})->all()->order("[{$lft_pn}] ASC")->query();
	}

	/**
	 * 获取指定节点同级别的所有节点
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 *
	 * @return QDB_ActiveRecord_Association_Coll array
	 */
	function getSiblingNodes(QDB_ActiveRecord_Abstract $obj) {

		$lft_pn = $this->_settings['left_prop'];
		$pid_pn = $this->_settings['parent_id_prop'];
		
		return $this->_meta->find("[{$pid_pn}] = ?", $obj->{$pid_pn})->all()->order("[{$lft_pn}] ASC")->query();
	}

	/**
	 * 计算所有子节点的总数
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 *
	 * @return int
	 */
	function getAllChildsCount(QDB_ActiveRecord_Abstract $obj) {

		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		return intval(($obj->{$rgt_pn} - $obj->{$lft_pn} - 1) / 2);
	}

	/**
	 * 查询指定对象的所有子节点对象，并销毁这些对象
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function destroyAllChildNodes(QDB_ActiveRecord_Abstract $obj) {

		$childs = $this->getAllChildNodes($obj);
		$this->_meta->getAssoc($this->_settings['childs_mapping'])->disable();
		$this->_meta->getAssoc($this->_settings['parent_mapping'])->disable();
		
		foreach ($childs as $child) {
			/* @var $child QDB_ActiveRecord_Abstract */
			$child->destroy();
		}
		
		$this->_meta->getAssoc($this->_settings['childs_mapping'])->enable();
		$this->_meta->getAssoc($this->_settings['parent_mapping'])->enable();
	}

	/**
	 * 直接删除指定对象的所有子节点对象
	 *
	 * @param QDB_ActiveRecord_Abstract $obj
	 */
	function deleteAllChildNodes(QDB_ActiveRecord_Abstract $obj) {

		$rgt_pn = $this->_settings['right_prop'];
		$lft_pn = $this->_settings['left_prop'];
		
		$this->_meta->deleteWhere("[{$lft_pn}] BETWEEN ? AND ?", $obj->{$lft_pn}, $obj->{$rgt_pn});
	}
}

/**
 * @}
 */
