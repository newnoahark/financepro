<?php
// $Id: format.php 2013-10-19 17:05 sqlhost $


/**
 * 定义 Helper_Format 类
 * Helper_Format 提供了一组格式化方法，以及调用格式化方法的接口
 *
 * @link http://labphp.com/
 * @copyright Copyright (c) 2006-2009 LabPHP Inc. {@link http://www.labphp.com}
 * @license New BSD License {@link http://labphp.com/license/}
 * @version $Id: format.php 2013-10-19 17:05 sqlhost $
 * @package helper
 */

abstract class Helper_Format {

	public static $internalFuncs = array(
			'date'
	);

	/**
	 * 调用格式化方法
	 */
// 	static public function format($value, $method) {

// 		foreach ($method as $key => $val) {
// 			if (is_array($val)) {
// 				$result = self::format($value, $val);
// 			}
// 			else {
// 				$result = self::formatByArg($value, $val);
// 			}
// 		}
// 		return $result;
// 	}

	static function format($value, $format) {
		
		if (in_array($format[0], self::$internalFuncs)) {
			$result = call_user_func(array(
					__CLASS__,
					'format_' . $format[0]
			), $value, $format[1]);
		}
		elseif (function_exists($format)) {
			$result = call_user_func($format[0], $value, $format[1]);
		}
		else {
			$result = $value;
		}
		
		return $result;
	}

	static private function format_date($value, $arg = "Y-m-d") {

		if (!$value) {
			return '';
		}
		
		if(!is_numeric($value)){
			if('0000-00-00' == $value || '1970-01-01' == $value){
				return '';
			}
			$value = strtotime($value);
		}
		return date($arg, $value);
	}

}