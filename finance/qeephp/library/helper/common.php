<?php

/**
 * 通用函数库
 *
 * @author E.X.
 */
class Helper_Common{

	/**
	 * 测试函数
	 */
	static function test(){

		Session::meta()->table->insert(array(), false);
	}

	/**
	 * 根据ip获取地区信息
	 * @param unknown_type $ip
	 * @param unknown_type $key
	 * @return boolean|string
	 * $key的取值
	 * Array
	 *	(
	 *	    [start] => -1
	 *	    [end] => -1
	 *	    [country] => 中国
	 *	    [province] => 山东
	 *	    [city] => 青岛
	 *	    [district] => 
	 *	    [isp] => 
	 *	    [type] => 
	 *	    [desc] => 
	 *	    [ip] => 121.42.44.92
	 *	)
	 */
	static function iptozone($ip = '', $key = ''){

		if(empty($ip)){
			$ip = IP;
		}
		$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
		if(empty($res)){
			return false;
		}
		$jsonMatches = array();
		preg_match('#\{.+?\}#', $res, $jsonMatches);
		if(!isset($jsonMatches[0])){
			return false;
		}
		$data = json_decode($jsonMatches[0], true);
		if(isset($data['ret']) && $data['ret'] == 1){
			$data['ip'] = $ip;
			unset($data['ret']);
		}else{
			return false;
		}
		
		if(empty($type)){
			return $data;
		}else{
			return $data[$type];
		}
	}

	/**
	 * 时间显示函数t
	 * @param int or string $unixtime 时间戳或者时间字符串
	 * @param int $limit 相差时间间隔
	 * @param string $format 超出时间间隔的日期显示格式
	 * @return string 返回需要的时间格式
	 */
	static function showtime($unixtime, $limit = 18000, $format = "Y-m-d"){

		$nowtime = CURRENT_TIMESTAMP;
		$showtime = "";
		if(!is_int($unixtime)){
			$unixtime = strtotime($unixtime);
		}
		$differ = $nowtime - $unixtime;
		if($differ >= 0){
			if($differ > $limit){
				$showtime = date($format, $unixtime);
			}else{
				$showtime = $differ > 86400 ? floor($differ / 86400) . "天前" : ($differ > 3600 ? floor($differ / 3600) . "小时前" : floor($differ / 60) . "分钟前");
			}
		}else{
			if(-$differ > $limit){
				$showtime = date($format, $unixtime);
			}else{
				$showtime = -$differ > 86400 ? floor(-$differ / 86400) . "天" : (-$differ > 3600 ? floor(-$differ / 3600) . "小时" : floor(-$differ / 60) . "分钟");
			}
		}
		return $showtime;
	}

	/**
	 * 返回客户端IP地址
	 *
	 * @return string
	 */
	static function getip(){

		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')){
			$onlineip = getenv('HTTP_CLIENT_IP');
		}elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')){
			$onlineip = getenv('HTTP_X_FORWARDED_FOR');
		}elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')){
			$onlineip = getenv('REMOTE_ADDR');
		}elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')){
			$onlineip = $_SERVER['REMOTE_ADDR'];
		}
		preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
		return $onlineipmatches[0] ? $onlineipmatches[0] : 'unknown';
	}

	/**
	 * 获得唯一字符串
	 *
	 * @return string
	 */
	static function unique_id(){

		srand((double)microtime() * 1000000);
		return md5(uniqid(rand()));
	}

	/**
	 * 获取随机数
	 *
	 * @param unknown_type $length
	 * @param unknown_type $numeric
	 * @return string
	 */
	static function random($length, $numeric = 0){

		PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
		$seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
		$hash = '';
		$max = strlen($seed) - 1;
		for($i = 0; $i < $length; $i++){
			$hash .= $seed[mt_rand(0, $max)];
		}
		return $hash;
	}

	/**
	 * 获取随机数
	 */
	static function random2($length){

		$key = NULL;
		$pattern = "1234567890abcdefghijklmnopqrstuvwxyz";
		for($i = 0; $i < $length; $i++){
			$key .= $pattern{rand(0, 35)};
		}
		return $key;
	}

	/**
	 * 判断日期函数
	 */
	static function retime($unixtime){
		// $nowtime = date("Y-m-d H:i:s");
		$nowtime = CURRENT_TIMESTAMP;
		$days = round(($nowtime - $unixtime) / 3600 / 24);
		return $days;
	}

	/**
	 * 将\n\r移除
	 */
	static function nl2empty($str){

		return preg_replace('/[\r\n]/', '', $str);
	}

	/**
	 * 安全的intval函数
	 * 主要是用于表单提交时过滤数组中的不安全字符串
	 *
	 * @param string/array $str
	 * @return string/array
	 */
	static function safeIntval($str){

		if(is_array($str)){
			$arr = array_map('intval', $str);
		}else{
			$arr = intval($str);
		}
		return $arr;
	}

	static function myImplode($arr){

		$str = '';
		foreach($arr as $key => $value){
			if(is_array($value)){
				$str .= $key . '=>' . self::myImplode($value) . "\n";
			}else{
				$str .= $key . '=>' . $value . "\n";
			}
		}
		return $str;
	}

	/**
	 * 检查邮箱是否正确
	 *
	 * @return boolean
	 */
	static function checkEmail($email){

		$num = preg_match("/[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+\.[a-zA-Z]{2,4}/", $email, $match);
		
		if($num == 0){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * 检查手机号码是否正确
	 * 移动：
	 *  2G号段（GSM）：134-139、150、151、152、158-159；
	 *  3G号段（TD-SCDMA)：157、187、188、147.
	 * 联通：
	 *  2G号段(GSM)：130-132、155-156；
	 *  3G号段(WCDMA)：185、186.
	 * 电信：
	 *  2G号段(CDMA)：133、153；
	 *  3G号段(CDMA2000)：180、189.
	 *  可以写出一个正则表达式：
	 *  var myreg = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/;  
	 * @return boolean
	 */
	static function checkMobile($mobile){
		// /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/
		$myreg = "/^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/";
		$num = preg_match($myreg, $mobile, $match);
		if($num == 0){
			return false;
		}else{
			return true;
		}
	}

	/**
	 * 截取utf-8格式的中文字符串
	 *
	 * @param $sourcestr 是要处理的字符串
	 * @param $cutlength 为截取的长度(即字数)
	 */
	static function cut_str($sourcestr, $cutlength, $dot = '...'){

		$returnstr = '';
		$i = 0;
		$n = 0;
		$str_length = strlen($sourcestr); // 字符串的字节数
		while(($n < $cutlength) and ($i <= $str_length)){
			$temp_str = substr($sourcestr, $i, 1);
			$ascnum = Ord($temp_str); // 得到字符串中第$i位字符的ascii码
			if($ascnum >= 224) // 如果ASCII位高与224，
{
				$returnstr = $returnstr . substr($sourcestr, $i, 3); // 根据UTF-8编码规范，将3个连续的字符计为单个字符
				$i = $i + 3; // 实际Byte计为3
				$n++; // 字串长度计1
			}elseif($ascnum >= 192) // 如果ASCII位高与192，
{
				$returnstr = $returnstr . substr($sourcestr, $i, 2); // 根据UTF-8编码规范，将2个连续的字符计为单个字符
				$i = $i + 2; // 实际Byte计为2
				$n++; // 字串长度计1
			}elseif($ascnum >= 65 && $ascnum <= 90) // 如果是大写字母，
{
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; // 实际的Byte数仍计1个
				$n++; // 但考虑整体美观，大写字母计成一个高位字符
			}else // 其他情况下，包括小写字母和半角标点符号，
{
				$returnstr = $returnstr . substr($sourcestr, $i, 1);
				$i = $i + 1; // 实际的Byte数计1个
				$n = $n + 0.5; // 小写字母和半角标点等与半个高位字符宽...
			}
		}
		if($str_length > strlen($returnstr)){
			$returnstr = $returnstr . $dot; // 超过长度时在尾处加上省略号
		}
		return $returnstr;
	}

	/**
	 * +----------------------------------------------------------
	 * 产生随机字串，可用来自动生成密码
	 * 默认长度6位 字母和数字混合 支持中文
	 * +----------------------------------------------------------
	 *
	 * @param string $len 长度
	 * @param string $type 字串类型
	 *        0 字母 1 数字 其它 混合
	 * @param string $addChars 额外字符
	 *        +----------------------------------------------------------
	 * @return string +----------------------------------------------------------
	 */
	static function rand_string($len = 6, $type = '', $addChars = ''){

		$str = '';
		switch($type){
			case 0 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			case 1 :
				$chars = str_repeat('0123456789', 3);
				break;
			case 2 :
				$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
				break;
			case 3 :
				$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
				break;
			default :
				// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
				$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
				break;
		}
		if($len > 10){ // 位数过长重复字符串一定次数
			$chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
		}
		if($type != 4){
			$chars = str_shuffle($chars);
			$str = substr($chars, 0, $len);
		}else{
			// 中文随机字
			for($i = 0; $i < $len; $i++){
				$str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
			}
		}
		return $str;
	}

	/**
	 * 用于 多条件筛选
	 * @type 适用于 刷新
	 * @param1  链接地址 string
	 * @param2 路径参数 array
	 * @return url string
	 */
	static function url_format($url, $params){

		$data = array();
		
		if(isset($_REQUEST['zone_id']) && $_REQUEST['zone_id']){
			$data['zone_id'] = $_REQUEST['zone_id'];
		}
		
		if(isset($_REQUEST['type']) && $_REQUEST['type']){
			$data['type'] = $_REQUEST['type'];
		}
		
		if(is_array($data)){
			
			if(!empty($params)){
				$data = array_merge($data, $params);
			}
			
			return url($url, $data);
		}else{
			return false;
		}
	}
	
	// 解析微博UBB内容
	static function ubb($text){

		$p = array(
				'/\[T\](.*?)\[\/T\]/i' 
		);
		$r = array(
				"<a href=''>#\\1#</a>" 
		);
		$text = preg_replace($p, $r, $text);
		return $text;
	}

	/**
	 * 获取图片地址
	 */
	static function imageUrl($image, $type = "large"){

		return $image ? $image : "/images/img4.jpg";
	}

	static function setCookie($name, $value, $time = 0){

		$value = base64_encode(serialize($value));
		setcookie(Q::ini('session_cookie_name') . $name, $value, $time, Q::ini('acl_cookie_path'));
	}

	static function getCookie($name){

		$value = isset($_COOKIE[Q::ini('session_cookie_name') . $name]) ? $_COOKIE[Q::ini('session_cookie_name') . $name] : '';
		$value = $value ? unserialize(base64_decode($value)) : '';
		return $value;
	}

	static function deleteCookie($name){

		setcookie(Q::ini('session_cookie_name') . $name, '', 10, Q::ini('acl_cookie_path'));
		unset($_COOKIE[Q::ini('session_cookie_name') . $name]);
	}

	/**
	 * $num: 需要格式化的数字
	 * $count_after_dot:小数点后保留的数字个数
	 **/
	static function format_number($num, $count_after_dot = 2){

		$count_after_dot = (int)$count_after_dot;
		$pow = pow(10, $count_after_dot);
		$tmp = $num * $pow;
		$tmp = floor($tmp) / $pow;
		$format = sprintf('%%.%df', (int)$count_after_dot);
		$result = sprintf($format, (float)$tmp);
		return $result;
	}

	/**
	 * 计算 字符串的长度
	 *
	 * @param str 字符串
	 * @param method 计算方式
	 *
	 *        method 1 中文算两位，英文算一位
	 */
	static function count_str_length($str, $method){

		switch($method){
			case 1 :
				$length = mb_strlen($str, 'UTF8');
				break;
			case 2 :
				$length = (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
				break;
			case 3 :
				$length = strlen($str);
				break;
			default :
				$length = (strlen($str) + mb_strlen($str, 'UTF8')) / 2;
				break;
		}
		
		return $length;
	}

	/**
	 *  处理图片 方法
	 *  假如是  数字 就是 随机图
	 *  假如是 路径是不存在的，则显示，缺省图
	 *  不然则显示 原图
	 */
	
	static function image_format($image_url, $type = "thumb"){

		if(is_numeric($image_url) && $image_url){
			$image_format = 'attached/image/rand/' . $type . '/' . $image_url . '.jpg';
		}else{
			// $image_format = image_exists($image_url)?$image_url:'attached/image/default/' . $type . '/default.jpg';;
			$image_format = false;
		}
		return $image_format;
	}

	static function getcsv($file_name) {

		if (!file_exists($file_name)) {
			exit('文件不存在');
		}
		
		$pathinfo = pathinfo($file_name);
		if (strtolower($pathinfo['extension']) != 'csv') {
			exit('必须是CSV文件');
		}
		
		$handle = fopen($file_name, "r");
		
		$out = array();
		$n = 0;
		$row = 1;
		while ($data = fgetcsv($handle, 10000)) {
			$num = count($data);
			for($i = 0; $i < $num; $i++) {
				$out[$n][$i] = $data[$i];
			}
			$n++;
		}
		
		fclose($handle);
		
		return $out;
	}
}