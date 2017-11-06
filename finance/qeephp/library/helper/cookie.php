<?php
// $Id: Cookie.class.php 2702 2012-02-02 12:35:01Z liu21st $


/**
 * +------------------------------------------------------------------------------
 * Cookie管理类
 * +------------------------------------------------------------------------------
 * 
 * @category Think
 * @package Think
 * @subpackage Util
 * @author liu21st <liu21st@gmail.com>
 * @version $Id: Cookie.class.php 2702 2012-02-02 12:35:01Z liu21st $
 *         
 *          +------------------------------------------------------------------------------
 */
abstract class Helper_Cookie {
	
	// 判断Cookie是否存在
	static function is_set($name) {

		return isset($_COOKIE[Q::ini('appini/cookie/prefix') . $name]);
	}
	
	// 获取某个Cookie值
	static function get($name) {

		$value = isset($_COOKIE[Q::ini('appini/cookie/prefix') . $name]) ? $_COOKIE[Q::ini('appini/cookie/prefix') . $name] : '';
		$value = unserialize(base64_decode($value));
		return $value;
	}
	
	// 设置某个Cookie值
	static function set($name, $value, $expire = '', $path = '', $domain = '') {

		if ($expire == '') {
			$expire = Q::ini('appini/cookie/expire');
		}
		if (empty($path)) {
			$path = Q::ini('appini/cookie/path');
		}
		if (empty($domain)) {
			$domain = Q::ini('appini/cookie/domain');
		}
		$expire = !empty($expire) ? time() + $expire : 0;
		$value = base64_encode(serialize($value));
		setcookie(Q::ini('appini/cookie/prefix') . $name, $value, $expire, $path, $domain);
		$_COOKIE[Q::ini('appini/cookie/prefix') . $name] = $value;
	}
	
	// 删除某个Cookie值
	static function delete($name) {

		self::set($name, '', -3600);
		unset($_COOKIE[Q::ini('appini/cookie/prefix') . $name]);
	}
	
	// 清空Cookie值
	static function clear() {

		unset($_COOKIE);
	}
}