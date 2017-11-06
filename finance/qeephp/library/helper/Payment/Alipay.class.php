<?php
// $Id: Alipay.class.php 2014-2-21 XieJH $

/**
 * Payment 类定义了支付接口的公共方法
 *
 * @author XieJH
 * @copyright Copyright (c) 2013-2015 LabPHP Inc.
 * @link http://www.labphp.com/
 */
class Alipay extends PaymentAbstract{
	
	private $_config = array(
			// 合作身份者id，以2088开头的16位纯数字
			'partner' => '2088521668128007',
			// 安全检验码，以数字和字母组成的32位字符
			'key' => 'ijencnxkjx70w37br49xxs627oun4bim',
			// 签名方式 不需修改
			'sign_type' => 'MD5',
			// 字符编码格式 目前支持 gbk 或 utf-8
			'charset' => 'utf-8',
			'input_charset' => 'utf-8',
			// ca证书路径地址，用于curl中ssl校验
			// 请保证cacert.pem文件在当前文件夹目录中
			'cacert' => '',
			// 访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			'transport' => 'http' 
	);
	
	/**
	 * 支付宝网关地址（新）
	 */
	private $_gateway_new = 'https://mapi.alipay.com/gateway.do?';
	
	// 服务器异步通知页面路径
	private $_notify_url = "http://www.qdnks.com/default/payment/alipaynotify";
	// private $_notify_url = "http://examine.huntersun.cc/default/payment/alipaynotify";
	// private $_notify_url = "https://test.klmisys.cn/default/payment/alipaynotify";
	// private $_notify_url = "http://www.yqdev.com/default/pay/alipaynotify";
	// 需http://格式的完整路径，不能加?id=123这类自定义参数
	
	// 页面跳转同步通知页面路径
	private $_return_url = "http://www.qdnks.com/default/payment/alipayresponse";
	// private $_return_url = "http://examine.huntersun.cc/default/payment/alipayresponse";
	// private $_return_url = "https://test.klmisys.cn/default/payment/alipayresponse";
	// private $_return_url = "http://www.yqdev.com/default/pay/alipayreturn";
	// 需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	
	/**
	 * HTTPS形式消息验证地址
	 */
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	
	/**
	 * HTTP形式消息验证地址
	 */
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';
	
	// 支付类型
	private $_payment_type = "1";
	
	// 卖家支付宝帐户
	private $_seller_email = 'qiang.su@klwlw.com.cn';
	
	// 构造请求字串
	private $_request = '';
	
	// 全局唯一实例
	private static $_app = null;

	private function __construct($config){

		$this->_config['cacert'] = getcwd() . '\\cacert.pem';
		
		if(isset($config['partner'])){
			$this->_config['partner'] = $config['partner'];
		}
		
		if(isset($config['key'])){
			$this->_config['key'] = $config['key'];
		}
		
		// 自动识别域名
		$this->_notify_url = 'http://' . $_SERVER['HTTP_HOST'] . url('default::payment/alipaynotify');
		$this->_return_url = 'http://' . $_SERVER['HTTP_HOST'] . url('default::payment/alipayresponse');
	}

	static function init($config){

		if(null == self::$_app){
			self::$_app = new Alipay($config);
		}
		
		return self::$_app;
	}

	function request(){
		
		// 商户订单号
		$out_trade_no = $_POST['WIDout_trade_no'];
		// 商户网站订单系统中唯一订单号，必填
		
		// 订单名称
		$subject = $_POST['WIDsubject'];
		// 必填
		
		// 付款金额
		$total_fee = $_POST['WIDtotal_fee'];
		// 必填
		
		// 订单描述
		$body = $_POST['WIDbody'];
		// 商品展示地址
		$show_url = $_POST['WIDshow_url'];
		// 需以http://开头的完整路径，例如：http://www.xxx.com/myorder.html
		
		// 防钓鱼时间戳
		$anti_phishing_key = "";
		// 若要使用请调用类文件submit中的query_timestamp函数
		
		// 客户端的IP地址
		$exter_invoke_ip = "";
		// 非局域网的外网IP地址，如：221.0.0.1
		
		/**
		 * *********************************************************
		 */
		
		// 构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "create_direct_pay_by_user",
				"partner" => trim($this->_config['partner']),
				"payment_type" => $this->_payment_type,
				"notify_url" => $this->_notify_url,
				"return_url" => $this->_return_url,
				"seller_email" => $this->_seller_email,
				"out_trade_no" => $out_trade_no,
				"subject" => $subject,
				"total_fee" => $total_fee,
				"body" => $body,
				"show_url" => $show_url,
				"anti_phishing_key" => $anti_phishing_key,
				"exter_invoke_ip" => $exter_invoke_ip,
				"_input_charset" => trim(strtolower($this->_config['charset'])) 
		);
		
		$this->_request = $this->buildRequestForm($parameter, 'post', '确认');
		header("Content-type: text/html; charset=utf-8");
		echo $this->_request;
	}

	/**
	 * 生成签名结果
	 *
	 * @param $para_sort 已排序要签名的数组
	 *        	return 签名结果字符串
	 */
	function buildRequestMysign($para_sort){
		// 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = HelperAlipay::createLinkstring($para_sort);
		
		$mysign = "";
		switch(strtoupper(trim($this->_config['sign_type']))){
			case "MD5" :
				$mysign = HelperAlipay::md5Sign($prestr, $this->_config['key']);
				break;
			default :
				$mysign = "";
		}
		
		return $mysign;
	}

	/**
	 * 生成要请求给支付宝的参数数组
	 *
	 * @param $para_temp 请求前的参数数组        	
	 * @return 要请求的参数数组
	 */
	function buildRequestPara($para_temp){
		// 除去待签名参数数组中的空值和签名参数
		$para_filter = HelperAlipay::paraFilter($para_temp);
		
		// 对待签名参数数组排序
		$para_sort = HelperAlipay::argSort($para_filter);
		
		// 生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		// 签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->_config['sign_type']));
		
		return $para_sort;
	}

	/**
	 * 生成要请求给支付宝的参数数组
	 *
	 * @param $para_temp 请求前的参数数组        	
	 * @return 要请求的参数数组字符串
	 */
	function buildRequestParaToString($para_temp){
		// 待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		// 把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		$request_data = HelperAlipay::createLinkstringUrlencode($para);
		
		return $request_data;
	}

	/**
	 * 建立请求，以表单HTML形式构造（默认）
	 *
	 * @param $para_temp 请求参数数组        	
	 * @param $method 提交方式。两个值可选：post、get        	
	 * @param $button_name 确认按钮显示文字        	
	 * @return 提交表单HTML文本
	 */
	function buildRequestForm($para_temp, $method, $button_name){
		// 待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		$sHtml = "<form id='alipaysubmit' style='display: none;' name='alipaysubmit' action='" . $this->_gateway_new . "_input_charset=" . trim(strtolower($this->_config['charset'])) . "' method='" . $method . "'>";
		while(!!list($key, $val) = each($para)){
			$sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
		}
		
		// submit按钮控件请不要含有name属性
		$sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";
		
		$sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";
		
		return $sHtml;
	}

	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
	 *
	 * @param $para_temp 请求参数数组        	
	 * @return 支付宝处理结果
	 */
	function buildRequestHttp($para_temp){

		$sResult = '';
		
		// 待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);
		
		// 远程获取数据
		$sResult = HelperAlipay::getHttpResponsePOST($this->_gateway_new, $this->_config['cacert'], $request_data, trim(strtolower($this->_config['charset'])));
		
		return $sResult;
	}

	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果，带文件上传功能
	 *
	 * @param $para_temp 请求参数数组        	
	 * @param $file_para_name 文件类型的参数名        	
	 * @param $file_name 文件完整绝对路径        	
	 * @return 支付宝返回处理结果
	 */
	function buildRequestHttpInFile($para_temp, $file_para_name, $file_name){
		
		// 待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		$para[$file_para_name] = "@" . $file_name;
		
		// 远程获取数据
		$sResult = HelperAlipay::getHttpResponsePOST($this->_gateway_new, $this->_config['cacert'], $para, trim(strtolower($this->_config['charset'])));
		
		return $sResult;
	}

	/**
	 * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
	 * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
	 * return 时间戳字符串
	 */
	function query_timestamp(){

		$url = $this->_gateway_new . "service=query_timestamp&partner=" . trim(strtolower($this->_config['partner'])) . "&_input_charset=" . trim(strtolower($this->_config['charset']));
		$encrypt_key = "";
		
		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName("encrypt_key");
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
		
		return $encrypt_key;
	}

	/**
	 * -------------------------------------------------------------
	 * 以下是响应部分
	 * -------------------------------------------------------------
	 */
	function response(){

		$verify_result = $this->verifyReturn();
		if($verify_result){
			// 商户订单号
			$out_trade_no = isset($_GET['out_trade_no']) ? $_GET['out_trade_no'] : '';
			// 支付宝交易号
			$trade_no = isset($_GET['trade_no']) ? $_GET['trade_no'] : '';
			// 交易状态
			$trade_status = isset($_GET['trade_status']) ? $_GET['trade_status'] : '';
			// 处理支付结果
			return $this->setPayment($out_trade_no, $trade_no, $trade_status);
		}else{
			// 验证失败
			// 如要调试，请看alipay_notify.php页面的verifyReturn函数
			return array(
					'status' => 0,
					'title' => '温馨提示',
					'message' => '缴费验证失败' 
			);
		}
	}

	function notify(){

		$verify_result = $this->verifyNotify();
		if($verify_result){
			// 商户订单号
			$out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
			// 支付宝交易号
			$trade_no = isset($_POST['trade_no']) ? $_POST['trade_no'] : '';
			// 交易状态
			$trade_status = isset($_POST['trade_status']) ? $_POST['trade_status'] : '';
			// 处理支付结果
			return $this->setPayment($out_trade_no, $trade_no, $trade_status);
		}else{
			// 验证失败
			return array(
					'status' => 0,
					'title' => '温馨提示',
					'message' => '缴费验证失败' 
			);
		}
	}

	/**
	 * 生成交易记录
	 * 
	 * @param unknown $out_trade_no 订单号
	 * @param unknown $trade_no 交易记录
	 * @param unknown $trade_status 响应状态
	 * @return boolean[]|string[]
	 */
	function setPayment($out_trade_no, $trade_no, $trade_status){

		$order = Order::find(array(
				'number' => $out_trade_no 
		))->getOne();
		if(!$order->id){
			return array(
					'status' => 0,
					'title' => '温馨提示',
					'message' => '缴费失败,订单不存在' 
			);
		}
		$pay = Payment::find(array(
				'number' => $out_trade_no,
				'order_id' => $order->id,
				'uid' => intval($order->uid) 
		))->getOne();
		if(!$pay->id){
			$pay->number = $out_trade_no;
			$pay->order_id = $order->id;
			$pay->uid = intval($order->uid);
			$pay->name = $order->name;
		}
		if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS'){
			if(!$pay->id){
				$pay->status = 1;
				$pay->memo = $order->name . date('Y年m月d日 H:i:s') . ' 缴费成功';
				$pay->save();
				
				// 设置订单状态
				$order->status = $order->status == 0 ? 1 : -1;
				$order->memo = date('Y年m月d日 H:i:s') . ' 缴费成功';
				$order->save();
				// 设置报名信息
				$inform = $order->inform;
				$inform->payment = 1;
				$inform->save();
			}
			return array(
					'status' => 1,
					'title' => '温馨提示',
					'message' => '缴费成功' 
			);
		}else{
			if(!$pay->id){
				$pay->status = -1;
				$pay->memo = $order->name . '在 ' . date('Y年m月d日 H:i:s') . ' 缴费失败，错误代码是：' . $trade_status;
				$pay->save();
			
			}
			return array(
					'status' => 0,
					'title' => '温馨提示',
					'message' => '缴费失败，错误代码是：' . $trade_status 
			);
		}
	}

	/**
	 * 针对notify_url验证消息是否是支付宝发出的合法消息
	 *
	 * @return 验证结果
	 */
	function verifyNotify(){

		if(empty($_POST)){ // 判断POST来的数组是否为空
			return 0;
		}else{
			// 生成签名结果
			$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
			// 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = '1';
			if(!empty($_POST["notify_id"])){
				$responseTxt = $this->getResponse($_POST["notify_id"]);
			}
			
			// 写日志记录
			if($isSign){
				$isSignStr = '1';
			}else{
				$isSignStr = '0';
			}
			$log_text = "responseTxt=" . $responseTxt . "\n notify_url_log:isSign=" . $isSignStr . ",";
			$log_text = $log_text . HelperAlipay::createLinkString($_POST);
			// HelperAlipay::logResult($log_text);
			
			// 验证
			// $responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			// isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if(preg_match("/true$/i", $responseTxt) && $isSign){
				return 1;
			}else{
				return 0;
			}
		}
	}

	/**
	 * 针对return_url验证消息是否是支付宝发出的合法消息
	 *
	 * @return 验证结果
	 */
	function verifyReturn(){

		if(empty($_GET)){ // 判断POST来的数组是否为空
			return 0;
		}else{
			unset($_GET['_URL_']);
			// 生成签名结果
			$isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
			// 获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
			$responseTxt = '1';
			if(!empty($_GET["notify_id"])){
				$responseTxt = $this->getResponse($_GET["notify_id"]);
			}
			
			// 写日志记录
			if($isSign){
				$isSignStr = '1';
			}else{
				$isSignStr = '0';
			}
			$log_text = "responseTxt=" . $responseTxt . "\n return_url_log:isSign=" . $isSignStr . ",";
			$log_text = $log_text . HelperAlipay::createLinkString($_GET);
			// HelperAlipay::logResult($log_text);
			
			// 验证
			// $responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
			// isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
			if(preg_match("/true$/i", $responseTxt) && $isSign){
				return 1;
			}else{
				return 0;
			}
		}
	}

	/**
	 * 获取返回时的签名验证结果
	 *
	 * @param $para_temp 通知返回来的参数数组        	
	 * @param $sign 返回的签名结果        	
	 * @return 签名验证结果
	 */
	function getSignVeryfy($para_temp, $sign){
		// 除去待签名参数数组中的空值和签名参数
		$para_filter = HelperAlipay::paraFilter($para_temp);
		
		// 对待签名参数数组排序
		$para_sort = HelperAlipay::argSort($para_filter);
		
		// 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = HelperAlipay::createLinkstring($para_sort);
		
		$isSgin = 0;
		switch(strtoupper(trim($this->_config['sign_type']))){
			case "MD5" :
				$isSgin = HelperAlipay::md5Verify($prestr, $sign, $this->_config['key']);
				break;
			default :
				$isSgin = 0;
		}
		
		return $isSgin;
	}

	/**
	 * 获取远程服务器ATN结果,验证返回URL
	 *
	 * @param $notify_id 通知校验ID        	
	 * @return 服务器ATN结果 验证结果集：
	 *         invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空
	 *         true 返回正确信息
	 *         false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
	 */
	function getResponse($notify_id){

		$transport = strtolower(trim($this->_config['transport']));
		$partner = trim($this->_config['partner']);
		$veryfy_url = '';
		if($transport == 'https'){
			$veryfy_url = $this->https_verify_url;
		}else{
			$veryfy_url = $this->http_verify_url;
		}
		$veryfy_url = $veryfy_url . "partner=" . $partner . "&notify_id=" . $notify_id;
		$responseTxt = HelperAlipay::getHttpResponseGET($veryfy_url, $this->_config['cacert']);
		
		return $responseTxt;
	}

	/**
	 * 关闭交易
	 * @param unknown $out_order_no
	 * @param string $trade_no
	 * @return unknown
	 */
	function closePayment($out_order_no, $trade_no = ''){
		
		// require_once ("lib/alipay_submit.class.php");
		// 商户订单号
		// $out_order_no = isset($_POST['out_order_no']) ? $_POST['out_order_no'] : '';
		// 支付宝交易号
		// $trade_no = isset($_POST['trade_no']) ? $_POST['trade_no'] : '';
		// 构造要请求的参数数组，无需改动
		$parameter = array(
				"service" => "close_trade",
				"partner" => trim($this->_config['partner']),
				"trade_no" => $trade_no,
				"out_order_no" => $out_order_no,
				"_input_charset" => trim(strtolower($this->_config['input_charset'])) 
		);
		
		// 建立请求
		$alipaySubmit = new AlipaySubmit($this->_config);
		$html_text = $alipaySubmit->buildRequestHttp($parameter);
		// 解析XML
		// 注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
		$xml = simplexml_load_string($html_text);
		$pay_close = new PaymentClose();
		$pay_close->out_order_no = $out_order_no;
		$pay_close->trade_no = $trade_no;
		if($xml->is_success == 'F'){
			// 订单关闭失败
			// 失败原因
			$error = $xml->error;
			$pay_close->memo = $error;
			$pay_close->status = 0;
			$pay_close->save();
			return array(
					'status' => 0,
					'message' => $error 
			);
		}elseif($xml->is_success == 'T'){
			// 订单关闭成功
			$error = $xml->error;
			$pay_close->memo = $error;
			$pay_close->status = 1;
			$pay_close->save();
			return array(
					'status' => 1,
					'message' => '订单关闭成功' 
			);
		}
	
	}
}
