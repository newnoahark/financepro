<?php
// $Id: acl.php 2009 2009-01-08 18:52:43Z dualface $


/**
 * 定义 QACL 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 *            http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: acl.php 2009 2009-01-08 18:52:43Z dualface $
 * @package mvc
 */

/**
 * QACL 实现了权限检查服务
 *
 * “基于角色”通过比对拥有的角色和访问需要的角色来决定是否通过权限检查。
 *
 * 在进行权限检查时，要求分别提供角色组和访问控制列表（ACL）。
 * 然后由 QACL 比对角色组和 ACL，并返回检查结果。
 *
 * QACL::rolesBasedCheck() 用于比对权限，并返回结果。
 * QACL::normalize() 方法用于将 ACL 转换为符合规范的 ACL。
 *
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: acl.php 2009 2009-01-08 18:52:43Z dualface $
 * @package mvc
 */
class QACL {

	/**
	 * 预定义角色常量
	 */
	const ACL_EVERYONE = 'acl_everyone';

	const ACL_NULL = 'acl_null';

	const ACL_NO_ROLE = 'acl_no_role';

	const ACL_HAS_ROLE = 'acl_has_role';

	const ALL_CONTROLLERS = 'all_controllers';

	const ALL_ACTIONS = 'all_actions';

	/**
	 * 检查访问控制表是否允许指定的角色访问
	 *
	 * 详细使用说明请参考开发者手册“访问控制”章节。
	 *
	 * @param array $roles
	 *        要检查的角色
	 * @param array $acl
	 *        访问控制表
	 * @param boolean $skip_normalize
	 *        是否跳过对 ACL 的整理
	 *        
	 * @return boolean 检查结果
	 */
	function rolesBasedCheck($roles, $acl, $skip_normalize = false) {

		$roles = array_map('strtolower', Q::normalize($roles));
		if (!$skip_normalize) {
			$acl = $this->normalize($acl);
		}
		if ($acl['allow'] == self::ACL_EVERYONE) {
			// 如果 allow 允许所有角色，deny 没有设置，则检查通过
			if ($acl['deny'] == self::ACL_NULL) {
				return true;
			}
			
			// 如果 deny 为 acl_no_role，则只要用户具有角色就检查通过
			if ($acl['deny'] == self::ACL_NO_ROLE) {
				if (empty($roles)) {
					return false;
				}
				return true;
			}
			
			// 如果 deny 为 acl_has_role，则只有用户没有角色信息时才检查通过
			if ($acl['deny'] == self::ACL_HAS_ROLE) {
				if (empty($roles)) {
					return true;
				}
				return false;
			}
			
			// 如果 deny 也为 acl_everyone，则表示 acl 出现了冲突
			if ($acl['deny'] == self::ACL_EVERYONE) {
				throw new QACL_Exception('Invalid acl');
			}
			
			// 只有 deny 中没有用户的角色信息，则检查通过
			foreach ($roles as $role) {
				if (in_array($role, $acl['deny'])) {
					return false;
				}
			}
			return true;
		}
		
		do {
			// 如果 allow 要求用户具有角色，但用户没有角色时直接不通过检查
			if ($acl['allow'] == self::ACL_HAS_ROLE) {
				if (!empty($roles)) {
					break;
				}
				return false;
			}
			
			// 如果 allow 要求用户没有角色，但用户有角色时直接不通过检查
			if ($acl['allow'] == self::ACL_NO_ROLE) {
				if (empty($roles)) {
					break;
				}
				return false;
			}
			
			if ($acl['allow'] != self::ACL_NULL) {
				// 如果 allow 要求用户具有特定角色，则进行检查
				$passed = false;
				foreach ($roles as $role) {
					if (in_array($role, $acl['allow'])) {
						$passed = true;
						break;
					}
				}
				if (!$passed) {
					return false;
				}
			}
		}
		while (false);
		
		// 如果 deny 没有设置，则检查通过
		if ($acl['deny'] == self::ACL_NULL) {
			return true;
		}
		
		// 如果 deny 为 acl_no_role，则只要用户具有角色就检查通过
		if ($acl['deny'] == self::ACL_NO_ROLE) {
			if (empty($roles)) {
				return false;
			}
			return true;
		}
		// 如果 deny 为 acl_has_role，则只有用户没有角色信息时才检查通过
		if ($acl['deny'] == self::ACL_HAS_ROLE) {
			if (empty($roles)) {
				return true;
			}
			return false;
		}
		
		// 如果 deny 为 acl_everyone，则检查失败
		if ($acl['deny'] == self::ACL_EVERYONE) {
			return false;
		}
		
		// 只有 deny 中没有用户的角色信息，则检查通过
		foreach ($roles as $role) {
			if (in_array($role, $acl['deny'])) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 对 ACL 整理，返回整理结果
	 *
	 * @param array $acl
	 *        要整理的 ACL
	 *        
	 * @return array
	 */
	function normalize(array $acl) {

		$acl = array_change_key_case($acl, CASE_LOWER);
		$ret = array();
		$keys = array(
				'allow',
				'deny'
		);
		foreach ($keys as $key) {
			do {
				if (!isset($acl[$key])) {
					$values = self::ACL_NULL;
					break;
				}
				
				$acl[$key] = strtolower($acl[$key]);
				if ($acl[$key] == self::ACL_EVERYONE || $acl[$key] == self::ACL_HAS_ROLE || $acl[$key] == self::ACL_NO_ROLE || $acl[$key] == self::ACL_NULL) {
					$values = $acl[$key];
					break;
				}
				
				$values = Q::normalize($acl[$key]);
				
				if (empty($values)) {
					$values = self::ACL_NULL;
				}
			}
			while (false);
			$ret[$key] = $values;
		}
		
		return $ret;
	}

	/**
	 * 获得缓存了的acl
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-11
	 */
	static function getACL() {

		global $root_dir;
		// 初始化变量
		$cache_dir = $root_dir . "/tmp/cache";
		// 实例化文件缓存对象
		$cache = new QCache_File(array(
				'cache_dir' => $cache_dir,
				'life_time' => null
		));
		
		$acl = $cache->get('acl');
		
		if (empty($acl)) {
			QACL::cacheACL();
			$acl = $cache->get('acl');
		}
		Q::changeIni('acl_global', $acl);
	}

	/**
	 * 缓存acl
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-11
	 */
	static function cacheACL() {

		global $root_dir;
		// 初始化变量
		$cache_dir = $root_dir . "/tmp/cache";
		// 实例化文件缓存对象
		$cache = new QCache_File(array(
				'cache_dir' => $cache_dir,
				'life_time' => null
		));
		// 初始化ACL
		$acl = $tmp = array();
		// 获得所有角色
		$roles = Role::find()->getAll();
		$roles_array = Helper_Array::toHashMap($roles, 'id', 'id');
		// 获得命名空间
		$submodules = Submodule::find('status = 1')->getAll();
		
		foreach ($submodules as $submodule) {
			if ($submodule->code != 'default') {
				$tmp = array(
						'default' => array(
								'allow' => 'ACL_EVERYONE'
						),
						'all_controllers' => array(
								'deny' => 'ACL_EVERYONE'
						)
				);
			}
			
			// 控制器ACL
			if (count($submodule->controllers) > 0) {
				foreach ($submodule->controllers as $controller) {
					if (count($controller->roles) > 0) {
						$tmp[$controller->code] = array(
								'allow' => Helper_Array::toString(Helper_Array::toHashMap($controller->roles, 'id', 'id'))
						);
					}
					// 如果存在动作的角色，则生成动作ACL，如果不存在，则表示按照控制ACL
					if (count($controller->actions) > 0) {
						$tmp[$controller->code]['actions'] = array();
						foreach ($controller->actions as $action) {
							if (count($action->roles) > 0) {
								$tmp[$controller->code]['actions'][$action->code] = array(
										'deny' => Helper_Array::toString(array_diff($roles_array, Helper_Array::toHashMap($action->roles, 'id', 'id'))),
										'allow' => Helper_Array::toString(Helper_Array::toHashmap($action->roles, 'id', 'id'))
								);
							}
						}
					}
				}
			}
			
			if ($submodule->code != 'default') {
				$acl[$submodule->code] = $tmp;
			}
			else {
				$acl = $tmp;
			}
		}
		$cache->set("acl", $acl);
	}
}

