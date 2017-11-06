<?php
// $Id: array.php 2286 2009-03-05 08:50:23Z dualface $

/**
 * 定义 Helper_Array 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 *            http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: array.php 2286 2009-03-05 08:50:23Z dualface $
 * @package helper
 */

/**
 * Helper_Array 类提供了一组简化数组操作的方法
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: array.php 2286 2009-03-05 08:50:23Z dualface $
 * @package helper
 */
abstract class Helper_Array {

	/**
	 * 从数组中删除空白的元素（包括只有空白字符的元素）
	 *
	 * 用法：
	 * @code php
	 * $arr = array('', 'test', ' ');
	 * Helper_Array::removeEmpty($arr);
	 *
	 * dump($arr);
	 * // 输出结果中将只有 'test'
	 * @endcode
	 *
	 * @param array $arr
	 *        	要处理的数组
	 * @param boolean $trim
	 *        	是否对数组元素调用 trim 函数
	 */
	static function removeEmpty(& $arr, $trim = true) {

		foreach ($arr as $key => $value) {
			if (is_array($value)) {
				self::removeEmpty($arr[$key]);
			}
			else {
				$value = trim($value);
				if ($value == '') {
					unset($arr[$key]);
				}
				elseif ($trim) {
					$arr[$key] = $value;
				}
			}
		}
	}

	/**
	 * 从一个二维数组中返回指定键的所有值
	 *
	 * 用法：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1'),
	 * array('id' => 2, 'value' => '2-1'),
	 * );
	 * $values = Helper_Array::cols($rows, 'value');
	 *
	 * dump($values);
	 * // 输出结果为
	 * // array(
	 * // '1-1',
	 * // '2-1',
	 * // )
	 * @endcode
	 *
	 * @param array $arr
	 *        	数据源
	 * @param string $col
	 *        	要查询的键
	 *        	
	 * @return array 包含指定键所有值的数组
	 */
	static function getCols($arr, $col) {

		$ret = array();
		foreach ($arr as $row) {
			if (isset($row[$col])) {
				$ret[] = $row[$col];
			}
		}
		return $ret;
	}

	/**
	 * 将一个二维数组转换为 HashMap，并返回结果
	 *
	 * 用法1：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1'),
	 * array('id' => 2, 'value' => '2-1'),
	 * );
	 * $hashmap = Helper_Array::hashMap($rows, 'id', 'value');
	 *
	 * dump($hashmap);
	 * // 输出结果为
	 * // array(
	 * // 1 => '1-1',
	 * // 2 => '2-1',
	 * // )
	 * @endcode
	 *
	 * 如果省略 $value_field 参数，则转换结果每一项为包含该项所有数据的数组。
	 *
	 * 用法2：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1'),
	 * array('id' => 2, 'value' => '2-1'),
	 * );
	 * $hashmap = Helper_Array::hashMap($rows, 'id');
	 *
	 * dump($hashmap);
	 * // 输出结果为
	 * // array(
	 * // 1 => array('id' => 1, 'value' => '1-1'),
	 * // 2 => array('id' => 2, 'value' => '2-1'),
	 * // )
	 * @endcode
	 *
	 * @param array $arr
	 *        	数据源
	 * @param string $key_field
	 *        	按照什么键的值进行转换
	 * @param string $value_field
	 *        	对应的键值
	 *        	
	 * @return array 转换后的 HashMap 样式数组
	 */
	static function toHashmap($arr, $key_field, $value_field = null) {

		$ret = array();
		if (empty($arr)) {
			return $ret;
		}
		if ($value_field) {
			foreach ($arr as $row) {
				if (isset($row[$key_field])) {
					$ret[$row[$key_field]] = $row[$value_field];
				}
			}
		}
		else {
			foreach ($arr as $row) {
				$ret[$row[$key_field]] = $row;
			}
		}
		return $ret;
	}

	/**
	 * 将一个二维数组按照指定字段的值分组
	 *
	 * 用法：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1', 'parent' => 1),
	 * array('id' => 2, 'value' => '2-1', 'parent' => 1),
	 * array('id' => 3, 'value' => '3-1', 'parent' => 1),
	 * array('id' => 4, 'value' => '4-1', 'parent' => 2),
	 * array('id' => 5, 'value' => '5-1', 'parent' => 2),
	 * array('id' => 6, 'value' => '6-1', 'parent' => 3),
	 * );
	 * $values = Helper_Array::groupBy($rows, 'parent');
	 *
	 * dump($values);
	 * // 按照 parent 分组的输出结果为
	 * // array(
	 * // 1 => array(
	 * // array('id' => 1, 'value' => '1-1', 'parent' => 1),
	 * // array('id' => 2, 'value' => '2-1', 'parent' => 1),
	 * // array('id' => 3, 'value' => '3-1', 'parent' => 1),
	 * // ),
	 * // 2 => array(
	 * // array('id' => 4, 'value' => '4-1', 'parent' => 2),
	 * // array('id' => 5, 'value' => '5-1', 'parent' => 2),
	 * // ),
	 * // 3 => array(
	 * // array('id' => 6, 'value' => '6-1', 'parent' => 3),
	 * // ),
	 * // )
	 * @endcode
	 *
	 * @param array $arr
	 *        	数据源
	 * @param string $key_field
	 *        	作为分组依据的键名
	 *        	
	 * @return array 分组后的结果
	 */
	static function groupBy($arr, $key_field) {

		$ret = array();
		foreach ($arr as $row) {
			$key = $row[$key_field];
			$ret[$key][] = $row;
		}
		return $ret;
	}

	/**
	 * 将一个平面的二维数组按照指定的字段转换为树状结构
	 *
	 * 用法：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1', 'parent' => 0),
	 * array('id' => 2, 'value' => '2-1', 'parent' => 0),
	 * array('id' => 3, 'value' => '3-1', 'parent' => 0),
	 *
	 * array('id' => 7, 'value' => '2-1-1', 'parent' => 2),
	 * array('id' => 8, 'value' => '2-1-2', 'parent' => 2),
	 * array('id' => 9, 'value' => '3-1-1', 'parent' => 3),
	 * array('id' => 10, 'value' => '3-1-1-1', 'parent' => 9),
	 * );
	 *
	 * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes');
	 *
	 * dump($tree);
	 * // 输出结果为：
	 * // array(
	 * // array('id' => 1, ..., 'nodes' => array()),
	 * // array('id' => 2, ..., 'nodes' => array(
	 * // array(..., 'parent' => 2, 'nodes' => array()),
	 * // array(..., 'parent' => 2, 'nodes' => array()),
	 * // ),
	 * // array('id' => 3, ..., 'nodes' => array(
	 * // array('id' => 9, ..., 'parent' => 3, 'nodes' => array(
	 * // array(..., , 'parent' => 9, 'nodes' => array(),
	 * // ),
	 * // ),
	 * // )
	 * @endcode
	 *
	 * 如果要获得任意节点为根的子树，可以使用 $refs 参数：
	 * @code php
	 * $refs = null;
	 * $tree = Helper_Array::tree($rows, 'id', 'parent', 'nodes', $refs);
	 *
	 * // 输出 id 为 3 的节点及其所有子节点
	 * $id = 3;
	 * dump($refs[$id]);
	 * @endcode
	 *
	 * @param array $arr
	 *        	数据源
	 * @param string $key_node_id
	 *        	节点ID字段名
	 * @param string $key_parent_id
	 *        	节点父ID字段名
	 * @param string $key_childrens
	 *        	保存子节点的字段名
	 * @param boolean $refs
	 *        	是否在返回结果中包含节点引用
	 *        	
	 *        	return array 树形结构的数组
	 */
	static function toTree($arr, $key_node_id, $key_parent_id = 'parent_id', $key_childrens = 'children', $treeIndex = false, & $refs = null) {

		$refs = array();
		foreach ($arr as $offset => $row) {
			$arr[$offset][$key_childrens] = array();
			$refs[$row[$key_node_id]] = & $arr[$offset];
		}
		
		$tree = array();
		foreach ($arr as $offset => $row) {
			$parent_id = $row[$key_parent_id];
			if ($parent_id) {
				if (!isset($refs[$parent_id])) {
					if ($treeIndex) {
						$tree[$offset] = & $arr[$offset];
					}
					else {
						$tree[] = & $arr[$offset];
					}
					continue;
				}
				$parent = & $refs[$parent_id];
				if ($treeIndex) {
					$parent[$key_childrens][$offset] = & $arr[$offset];
				}
				else {
					$parent[$key_childrens][] = & $arr[$offset];
				}
			}
			else {
				if ($treeIndex) {
					$tree[$offset] = & $arr[$offset];
				}
				else {
					$tree[] = & $arr[$offset];
				}
			}
		}
		
		return $tree;
	}

	static function printTree(& $tree) {

		if (empty($tree) || !is_array($tree)) {
			return '';
		}
		$string = "<ul>\n";
		
		foreach ($tree as $item) {
			$string .= "<li><span" . ($item['children'] ? " class=\"folder\"" : " class=\"file\"") . "><a href=\"" . $item['id'] . "\">" . $item['name'] . "</a></span>";
			if ($item['children']) {
				$string .= self::printTree($item['children']);
			}
			$string .= "</li>\n";
		}
		
		$string .= "</ul>\n";
		
		return $string;
	}

	/**
	 * 将树形数组展开为平面的数组
	 *
	 * 这个方法是 tree() 方法的逆向操作。
	 *
	 * @param array $tree
	 *        	树形数组
	 * @param string $key_childrens
	 *        	包含子节点的键名
	 *        	
	 * @return array 展开后的数组
	 */
	static function treeToArray($tree, $key_childrens = 'childrens') {

		$ret = array();
		if (isset($tree[$key_childrens]) && is_array($tree[$key_childrens])) {
			$childrens = $tree[$key_childrens];
			unset($tree[$key_childrens]);
			$ret[] = $tree;
			foreach ($childrens as $node) {
				$ret = array_merge($ret, self::treeToArray($node, $key_childrens));
			}
		}
		else {
			unset($tree[$key_childrens]);
			$ret[] = $tree;
		}
		return $ret;
	}

	/**
	 * 根据指定的键对数组排序
	 *
	 * 用法：
	 * @code php
	 * $rows = array(
	 * array('id' => 1, 'value' => '1-1', 'parent' => 1),
	 * array('id' => 2, 'value' => '2-1', 'parent' => 1),
	 * array('id' => 3, 'value' => '3-1', 'parent' => 1),
	 * array('id' => 4, 'value' => '4-1', 'parent' => 2),
	 * array('id' => 5, 'value' => '5-1', 'parent' => 2),
	 * array('id' => 6, 'value' => '6-1', 'parent' => 3),
	 * );
	 *
	 * $rows = Helper_Array::sortByCol($rows, 'id', SORT_DESC);
	 * dump($rows);
	 * // 输出结果为：
	 * // array(
	 * // array('id' => 6, 'value' => '6-1', 'parent' => 3),
	 * // array('id' => 5, 'value' => '5-1', 'parent' => 2),
	 * // array('id' => 4, 'value' => '4-1', 'parent' => 2),
	 * // array('id' => 3, 'value' => '3-1', 'parent' => 1),
	 * // array('id' => 2, 'value' => '2-1', 'parent' => 1),
	 * // array('id' => 1, 'value' => '1-1', 'parent' => 1),
	 * // )
	 * @endcode
	 *
	 * @param array $array
	 *        	要排序的数组
	 * @param string $keyname
	 *        	排序的键
	 * @param int $dir
	 *        	排序方向
	 *        	
	 * @return array 排序后的数组
	 */
	static function sortByCol($array, $keyname, $dir = SORT_ASC) {

		return self::sortByMultiCols($array, array(
				$keyname => $dir 
		));
	}

	/**
	 * 将一个二维数组按照多个列进行排序，类似 SQL 语句中的 ORDER BY
	 *
	 * 用法：
	 * @code php
	 * $rows = Helper_Array::sortByMultiCols($rows, array(
	 * 'parent' => SORT_ASC,
	 * 'name' => SORT_DESC,
	 * ));
	 * @endcode
	 *
	 * @param array $rowset
	 *        	要排序的数组
	 * @param array $args
	 *        	排序的键
	 *        	
	 * @return array 排序后的数组
	 */
	static function sortByMultiCols($rowset, $args) {

		$sortArray = array();
		$sortRule = '';
		foreach ($args as $sortField => $sortDir) {
			foreach ($rowset as $offset => $row) {
				$sortArray[$sortField][$offset] = $row[$sortField];
			}
			$sortRule .= '$sortArray[\'' . $sortField . '\'], ' . $sortDir . ', ';
		}
		if (empty($sortArray) || empty($sortRule)) {
			return $rowset;
		}
		eval('array_multisort(' . $sortRule . '$rowset);');
		return $rowset;
	}

	/**
	 * 将数组用分隔符连接并输出
	 *
	 * @param
	 *        	$array
	 * @param
	 *        	$comma
	 * @param
	 *        	$find
	 * @return string
	 */
	static function toString($array, $comma = ',', $find = '') {

		$str = '';
		$comma_temp = '';
		
		if (!empty($find)) {
			if (!is_array($find)) {
				$find = self::toArray($find);
			}
			foreach ($find as $key) {
				$str .= $comma_temp . $array[$key];
				$comma_temp = $comma;
			}
		}
		else {
			foreach ($array as $value) {
				$str .= $comma_temp . $value;
				$comma_temp = $comma;
			}
		}
		return $str;
	}
	
	// 等同于
	// $str = preg_split("[/, .\t\n\|]/", $string);
	static function toArray($string, $comma = ", .\t\n|") {

		$array = array();
		$tok = strtok($string, $comma);
		while ($tok !== false) {
			$array[] = $tok;
			$tok = strtok($comma);
		}
		return $array;
	}

	/**
	 * 获得无限分类的所有孩子
	 */
	static function getChildren($array, $parent_id = 0) {

		$ret = array();
		
		foreach ($array as $k => $v) {
			if ($v['parent_id'] == $parent_id) {
				$ret[$k] = $v;
			}
		}
		return $ret;
	}

	/**
	 * 获得无限分类的所有同辈兄弟姐妹
	 *
	 * @return array
	 */
	static function getSiblings($array, $self) {

		$ret = array();
		$current = $array[$self];
		if (empty($current)) {
			return $ret;
		}
		
		$parent_id = $current['parent_id'];
		
		foreach ($array as $key => $value) {
			if ($value['parent_id'] == $parent_id && $value['id'] != $self) {
				$ret[$key] = $value;
			}
		}
		return $ret;
	}

	static function getDescendants(&$tree, $key_node_id = 'id', $key_childrens = 'children') {

		if (empty($tree) || !is_array($tree)) {
			return;
		}
		
		$array = array();
		foreach ($tree[$key_childrens] as $val) {
			$array[] = $val[$key_node_id];
			if ($val[$key_childrens]) {
				$array = array_merge($array, self::getDescendants($val, $key_node_id, $key_childrens));
			}
		}
		return $array;
	}

	/**
	 * 将数组转换成SQL语句
	 *
	 * @return string
	 */
	static function toSQL($array, $key = 0) {

		if (!count($array)) {
			return false;
		}
		$sql = $comma = '';
		
		foreach ($array as $k => $v) {
			$sql .= $comma . "'" . ($key ? $k : $v) . "'";
			$comma = ',';
		}
		
		return $sql;
	}

	/**
	 * 去掉指定的项
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-11
	 *         
	 *          同时兼容字符串和数组
	 *         
	 * @author sqlhost
	 * @version 1.0.1
	 *          2012-4-19
	 */
	static function removeKey(&$array, $keys) {

		if (!is_array($keys)) {
			$keys = array(
					$keys 
			);
		}
		return array_diff_key($array, array_flip($keys));
	}

	static function unsetKey(&$array, $keys) {

		if (is_array($keys)) {
			foreach ($keys as $key) {
				self::unsetKey($array, $key);
			}
		}
		else {
			if (array_key_exists($keys, $array)) {
				unset($array[$keys]);
			}
		}
	}

	/**
	 * 通过code获得元素
	 * 如果数组中含有code键的话
	 */
	static function getByCode(&$array, $code) {

		foreach ($array as $key => $val) {
			if ($val['code'] == $code) {
				return $val;
			}
		}
	}

	/**
	 * 从二维数组中查找结果
	 *
	 * @param $ref 按某个字段来查找        	
	 * @param $value 查找的值，即$ref字段的值，如果不存在$ref，即二维数组的键就是记录的ID        	
	 * @param $return 要返回的字段        	
	 */
	static function find(&$array, $ref = null, $value = 'id', $return = null, $single = false) {

		$found = null;
		if ($ref) {
			if (!is_array($value)) {
				$value = self::toArray($value);
			}
			foreach ($array as $key => $val) {
				if (in_array($val[$ref], $value)) {
					if ($single) {
						$found = $return ? $val[$return] : $val;
						break;
					}
					$found[$key] = $return ? $val[$return] : $val;
				}
			}
		}
		else {
			if (is_array($value)) {
				foreach ($value as $val) {
					$found[] = $return ? $array[$val][$return] : $array[$val];
				}
			}
			else {
				$found = $return ? $array[$value][$return] : $array[$value];
			}
		}
		return $found;
	}

	/**
	 * 替换数组中的某个值
	 */
	static function replace(&$array, $arr) {

		$return = $array;
		foreach ($arr as $key => $val) {
			if (isset($return[$key])) {
				$return[$key] = $val;
			}
		}
		return $return;
	}

	/**
	 * 将数组中的每个元素的头或尾填充字符串
	 */
	static function fill(& $array, $string, $pos = 'left') {

		foreach ($array as $k => $v) {
			$array[$k] = $pos == 'left' ? "*." . $v : $v . "*.";
		}
	}

	/**
	 * 将二维数组转换成多维数组
	 *
	 * @param unknown_type $arr        	
	 * @param unknown_type $id        	
	 * @param unknown_type $value        	
	 */
	static function fromHashmap($arr, $id, $value) {

		$rtn = array();
		foreach ($arr as $key => $val) {
			$rtn[] = array(
					$id => $key,
					$value => $val 
			);
		}
		return $rtn;
	}

	/**
	 * 是否是最后一个
	 */
	static function isLast(&$arr) {

		$array = $arr;
		end($array);
		if (key($arr) === key($array)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 是否是最后一个
	 */
	static function isFirst(&$arr) {

		$array = $arr;
		echo key($array) . "<br />";
		if (key($arr) === key($array)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 数组转换为字符串
	 *
	 * @param unknown $arr        	
	 * @param string $glue        	
	 * @param string $key        	
	 * @param string $field        	
	 * @return string
	 */
	static function implode(&$arr, $glue = ',', $key = 'id', $field = 'id') {

		if (empty($arr) || !count($arr)) {
			return '';
		}
		$arr = self::toHashmap($arr, $key, $field);
		
		return implode($glue, $arr);
	}
}

