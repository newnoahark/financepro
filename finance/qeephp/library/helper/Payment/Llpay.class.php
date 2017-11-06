<?php
import("ORG.Util.Payment.PaymentAbstract");
import("ORG.Util.Payment.HelperLlpay");
import('ORG.Util.Payment.Json');


/**
 *
 * @author Yubb
 * @copyright Copyright (c) 2013-2015 LabPHP Inc.
 * @link http://www.labphp.com/
 */
class Llpay extends PaymentAbstract {

	private $_config = array(
			
			//商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201306081000001016
// 			'oid_partner' => '201408071000001546',
			'oid_partner' => '201408071000001545',
			//安全检验码，以数字和字母组成的字符
			'key' => '201408071000001545test_20140812',
			
			//版本号
			'version' => '1.0',
			
			//防钓鱼ip
			'userreq_ip' => '10.10.246.110',
			
			//证件类型
			'id_type' => '0',
			//签名方式 不需修改
			'sign_type' => 'MD5',
			//订单有效时间  分钟为单位，默认为10080分钟（7天）
			'valid_order' => "30",
			
			//字符编码格式 目前支持 gbk 或 utf-8
			'input_charset' => 'utf-8',
			
			//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
			'transport' => 'http'
	);

	/**
	 * 连连支付网关地址
	 * 线上地址为https://yintong.com.cn/llpayh5/payment.htm?
	 */
	var $llpay_gateway_new = 'https://yintong.com.cn/payment/bankgateway.htm';
	
	//服务器异步通知页面路径
	private $notify_url = "http://www.wjtrw.com/Icbc/llpaynotify";
	//需http://格式的完整路径，不能加?id=123这类自定义参数
	

	//页面跳转同步通知页面路径
	private $return_url = "http://www.wjtrw.com/Icbc/llpayreturn";
	//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	
	//异步通知返回结果
	private $notify_result = false;
	// 异步通知返回数据
	private $notifyResp = array();

	// 全局唯一实例
	private static $_app = null;

	private function __construct($config) {

		if (isset($config['partner'])) {
			$this->_config['partner'] = $config['partner'];
		}
	}

	function init($config) {

		if (null == self::$_app) {
			self::$_app = new Llpay($config);
		}
		
		return self::$_app;
	}

	function request() {

		/**
		 * ************************请求参数*************************
		 */
		
		//商户用户唯一编号
		$user_id = $_POST['user_id'];
		
		//支付类型
		$busi_partner = $_POST['busi_partner'];
		
		//商户订单号
		$no_order = $_POST['no_order'];
		//商户网站订单系统中唯一订单号，必填
		

		//付款金额
		$money_order = $_POST['money_order'];
		//必填
		

		//商品名称
		$name_goods = $_POST['name_goods'];
		
		//订单地址
		$url_order = $_POST['url_order'];
		
		//订单描述
		$info_order = $_POST['info_order'];
		
		//银行网银编码
		$bank_code = $_POST['bank_code'];
		
		//支付方式
		$pay_type = $_POST['pay_type'];
		
		//卡号
		$card_no = $_POST['card_no'];
		
		//姓名
		$acct_name = $_POST['acct_name'];
		
		//身份证号
		$id_no = $_POST['id_no'];
		
		//协议号
		$no_agree = $_POST['no_agree'];
		
		//修改标记
		$flag_modify = $_POST['flag_modify'];
		
		//风险控制参数
		$risk_item = $_POST['risk_item'];
		
		//分账信息数据
		$shareing_data = $_POST['shareing_data'];
		
		//返回修改信息地址
		$back_url = $_POST['back_url'];
		
		//订单有效期
		$valid_order = $_POST['valid_order'];
		
		/**
		 * *********************************************************
		 */
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"version" => trim($this->_config['version']),
				"oid_partner" => trim($this->_config['oid_partner']),
				"sign_type" => trim($this->_config['sign_type']),
				"userreq_ip" => trim($this->_config['userreq_ip']),
				"id_type" => trim($this->_config['id_type']),
				"valid_order" => trim($this->_config['valid_order']),
				"user_id" => $user_id,
				"timestamp" => date('YmdHis', time()),
				"busi_partner" => $busi_partner,
				"no_order" => $no_order,
				"dt_order" => date('YmdHis', time()),
				"name_goods" => $name_goods,
				"info_order" => $info_order,
				"money_order" => $money_order,
				"notify_url" => $this->notify_url,
				"url_return" => $this->return_url,
				"url_order" => $url_order,
				"bank_code" => $bank_code,
				"pay_type" => $pay_type,
				"no_agree" => $no_agree,
				"shareing_data" => $shareing_data,
				"risk_item" => $risk_item,
				"id_no" => $id_no,
				"acct_name" => $acct_name,
				"flag_modify" => $flag_modify,
				"card_no" => $card_no,
				"back_url" => $back_url
		);
		
		//建立请求
		$html_text = $this->buildRequestForm($parameter, "post", "确认");
		header("Content-type: text/html; charset=utf-8");
		echo $html_text;
	}

// 	function buildRequestForm($para_temp, $method = 'POST', $button_name = "") {


// 		$sHtml = "<form id='gongshangsubmit' name='gongshangsubmit' action='" . $this->_config['form_url'] . "' method='" . $method . "'>";


// 		foreach ($para_temp as $key => $val){
// 			$sHtml .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
// 		}


// 		//submit按钮控件请不要含有name属性
// 		$sHtml = $sHtml . "<input type='submit' value=''></form>";


// 		$sHtml = $sHtml . "<script>document.forms['gongshangsubmit'].submit();</script>";


// 		return $sHtml;
// 	}
	

	/**
	 * 同步通知 接口
	 *
	 * @see PaymentAbstract::response()
	 */
	
	/**
	 * 针对return_url验证消息是否是连连支付发出的合法消息
	 * @return 验证结果
	 */
	
	function response() {
		
		if (empty ($_POST)) { //判断POST来的数组是否为空
			return false;
		} else {
			//首先对获得的商户号进行比对
			if (trim($_POST['oid_partner' ]) != $this->_config['oid_partner']) {
				//商户号错误
				return false;
			}
		
			//生成签名结果
			$parameter = array (
					'oid_partner' => $_POST['oid_partner' ],
					'sign_type' => $_POST['sign_type'],
					'dt_order' => $_POST['dt_order' ],
					'no_order' =>  $_POST['no_order' ],
					'oid_paybill' => $_POST['oid_paybill' ],
					'money_order' => $_POST['money_order' ],
					'result_pay' =>  $_POST['result_pay'],
					'settle_date' => $_POST['settle_date'],
					'info_order' =>$_POST['info_order'],
					'pay_type'=>$_POST['pay_type'],
					'bank_code'=>$_POST['bank_code'],
			);
		
			if (!$this->getSignVeryfy($parameter, trim($_POST['sign' ]))) {
				return false;
			}
			header("location: http://www.wjtrw.com/Icbc/paySuccess/order_no/" . $_POST['no_order' ]);
		}
	}

	/**
	 * 异步通知接口
	 *
	 * @see PaymentAbstract::notify()
	 */
	function notify() {

		$this->verifyNotify();
		
		if ($this->notify_result) { //验证成功
			//获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
			$no_order = $this->notifyResp['no_order']; //商户订单号
			$oid_paybill = $this->notifyResp['oid_paybill']; //连连支付单号
			$result_pay = $this->notifyResp['result_pay']; //支付结果，SUCCESS：为支付成功
			$money_order = $this->notifyResp['money_order']; // 支付金额
			if ($result_pay == "SUCCESS") {
				//请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
				//——请根据您的业务逻辑来编写程序——
				//payAfter($this->notifyResp);
			}
			file_put_contents("log.txt", "异步通知 验证成功--" . $no_order ."\n", FILE_APPEND);
			die("{'ret_code':'0000','ret_msg':'交易成功'}"); //请不要修改或删除
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
		else {
			file_put_contents("log.txt", "异步通知" . $this->notifyResp['no_order'] . " 验证失败" . time() ."\n", FILE_APPEND);
			//验证失败
			die("{'ret_code':'9999','ret_msg':'验签失败'}");
			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}

			//处理 交易逻辑
			/**
			 * 1: 修改订单状态
			 * 2： 修改交易状态
			 * 3：修改payment 状态
			 */
			
			// 付款
// 			//修改订单
// 			$res = EventOrderModel::m()->where(array(
// 					'order_no' => $out_trade_no
// 			))->save(array(
// 					'status' => 1,
// 					'confirm_time' => time(),
// 					'update_time' => time()
// 			));
// 			HelperAlipay::logResult(EventOrderModel::m()->getLastSql());
	}

	//↓↓↓↓↓↓↓↓↓↓提交表单使用到的方法↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
	/**
	 * 生成签名结果
	 *
	 * @param $para_sort 已排序要签名的数组 return 签名结果字符串
	 */
	function buildRequestMysign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = HelperLlpay::createLinkstring($para_sort);
		$mysign = "";
		switch (strtoupper(trim($this->_config['sign_type']))) {
			case "MD5":
				$mysign = HelperLlpay::md5Sign($prestr, $this->_config['key']);
				break;
			default:
				$mysign = "";
		}
		file_put_contents("log.txt", "签名:" . $mysign . "\n", FILE_APPEND);
		return $mysign;
	}

	/**
	 * 生成要请求给连连支付的参数数组
	 *
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组
	 */
	function buildRequestPara($para_temp) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = HelperLlpay::paraFilter($para_temp);
		//对待签名参数数组排序
		$para_sort = HelperLlpay::argSort($para_filter);
		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->_config['sign_type']));
		foreach ($para_sort as $key => $value) {
			$para_sort[$key] = $value;
		}
		return $para_sort;
		//return urldecode(json_encode($para_sort));
	}

	/**
	 * 生成要请求给连连支付的参数数组
	 *
	 * @param $para_temp 请求前的参数数组
	 * @return 要请求的参数数组字符串
	 */
	function buildRequestParaToString($para_temp) {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		$request_data = HelperLlpay::createLinkstringUrlencode($para);
		
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
	function buildRequestForm($para_temp, $method, $button_name) {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		$sHtml = "<form id='llpaysubmit' name='llpaysubmit' action='" . $this->llpay_gateway_new . "' method='" . $method . "'>";
		$sHtml .= "<input type='hidden' name='version' value='" . $para['version'] . "'/>";
		$sHtml .= "<input type='hidden' name='oid_partner' value='" . $para['oid_partner'] . "'/>";
		$sHtml .= "<input type='hidden' name='user_id' value='" . $para['user_id'] . "'/>";
		$sHtml .= "<input type='hidden' name='timestamp' value='" . $para['timestamp'] . "'/>";
		$sHtml .= "<input type='hidden' name='sign_type' value='" . $para['sign_type'] . "'/>";
		$sHtml .= "<input type='hidden' name='sign' value='" . $para['sign'] . "'/>";
		$sHtml .= "<input type='hidden' name='busi_partner' value='" . $para['busi_partner'] . "'/>";
		$sHtml .= "<input type='hidden' name='no_order' value='" . $para['no_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='dt_order' value='" . $para['dt_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='name_goods' value='" . $para['name_goods'] . "'/>";
		$sHtml .= "<input type='hidden' name='info_order' value='" . $para['info_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='money_order' value='" . $para['money_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='notify_url' value='" . $para['notify_url'] . "'/>";
		$sHtml .= "<input type='hidden' name='url_return' value='" . $para['url_return'] . "'/>";
		$sHtml .= "<input type='hidden' name='userreq_ip' value='" . $para['userreq_ip'] . "'/>";
		$sHtml .= "<input type='hidden' name='url_order' value='" . $para['url_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='valid_order' value='" . $para['valid_order'] . "'/>";
		$sHtml .= "<input type='hidden' name='bank_code' value='" . $para['bank_code'] . "'/>";
		$sHtml .= "<input type='hidden' name='pay_type' value='" . $para['pay_type'] . "'/>";
		$sHtml .= "<input type='hidden' name='no_agree' value='" . $para['no_agree'] . "'/>";
		$sHtml .= "<input type='hidden' name='shareing_data' value='" . $para['shareing_data'] . "'/>";
		$sHtml .= "<input type='hidden' name='risk_item' value='" . $para['risk_item'] . "'/>";
		$sHtml .= "<input type='hidden' name='id_type' value='" . $para['id_type'] . "'/>";
		$sHtml .= "<input type='hidden' name='id_no' value='" . $para['id_no'] . "'/>";
		$sHtml .= "<input type='hidden' name='acct_name' value='" . $para['acct_name'] . "'/>";
		$sHtml .= "<input type='hidden' name='flag_modify' value='" . $para['flag_modify'] . "'/>";
		$sHtml .= "<input type='hidden' name='card_no' value='" . $para['card_no'] . "'/>";
		$sHtml .= "<input type='hidden' name='back_url' value='" . $para['back_url'] . "'/>";
		//submit按钮控件请不要含有name属性
		$sHtml = $sHtml . "<input type='submit' value='" . $button_name . "'></form>";
		$sHtml = $sHtml . "<script>document.forms['llpaysubmit'].submit();</script>";
		return $sHtml;
	}

	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果
	 *
	 * @param $para_temp 请求参数数组
	 * @return 连连支付处理结果
	 */
	function buildRequestHttp($para_temp) {

		$sResult = '';
		
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);
		
		//远程获取数据
		$sResult = HelperLlpay::getHttpResponsePOST($this->llpay_gateway_new, $this->_config['cacert'], $request_data, trim(strtolower($this->_config['input_charset'])));
		
		return $sResult;
	}

	/**
	 * 建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果，带文件上传功能
	 *
	 * @param $para_temp 请求参数数组
	 * @param $file_para_name 文件类型的参数名
	 * @param $file_name 文件完整绝对路径
	 * @return 连连支付返回处理结果
	 */
	function buildRequestHttpInFile($para_temp, $file_para_name, $file_name) {
		
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		$para[$file_para_name] = "@" . $file_name;
		
		//远程获取数据
		$sResult = HelperLlpay::getHttpResponsePOST($this->llpay_gateway_new, $this->_config['cacert'], $para, trim(strtolower($this->_config['input_charset'])));
		
		return $sResult;
	}

	/**
	 * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
	 * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
	 * return 时间戳字符串
	 */
	function query_timestamp() {

		$url = $this->llpay_gateway_new . "service=query_timestamp&partner=" . trim(strtolower($this->_config['partner'])) . "&_input_charset=" . trim(strtolower($this->_config['input_charset']));
		$encrypt_key = "";
		
		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName("encrypt_key");
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
		
		return $encrypt_key;
	}
	//↑↑↑↑↑↑↑↑↑↑提交表单使用到的方法↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
	
	//↓↓↓↓↓↓↓↓↓↓接收异步通知使用到的方法↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
	/**
	 * 针对notify_url验证消息是否是连连支付发出的合法消息
	 * @return 验证结果
	 */
	function verifyNotify() {
		file_put_contents("log.txt", "1--\n", FILE_APPEND);
		//生成签名结果
		$is_notify = true;
		$json = new Json;
		$str = file_get_contents("php://input");
		$val = $json->decode($str);
		$oid_partner = HelperLlpay::getJsonVal($val,'oid_partner' );
		$sign_type = HelperLlpay::getJsonVal($val,'sign_type' );
		$sign = HelperLlpay::getJsonVal($val,'sign' );
		$dt_order = HelperLlpay::getJsonVal($val,'dt_order' );
		$no_order = HelperLlpay::getJsonVal($val,'no_order' );
		$oid_paybill = HelperLlpay::getJsonVal($val,'oid_paybill' );
		$money_order = HelperLlpay::getJsonVal($val,'money_order' );
		$result_pay = HelperLlpay::getJsonVal($val,'result_pay' );
		$settle_date = HelperLlpay::getJsonVal($val,'settle_date' );
		$info_order = HelperLlpay::getJsonVal($val,'info_order');
		$pay_type = HelperLlpay::getJsonVal($val,'pay_type' );
		$bank_code = HelperLlpay::getJsonVal($val,'bank_code' );
		$no_agree = HelperLlpay::getJsonVal($val,'no_agree' );
		$id_type = HelperLlpay::getJsonVal($val,'id_type' );
		$id_no = HelperLlpay::getJsonVal($val,'id_no' );
		$acct_name = HelperLlpay::getJsonVal($val,'acct_name' );
	
		file_put_contents("log.txt", "2--\n", FILE_APPEND);
		//首先对获得的商户号进行比对
		if ($oid_partner != $this->_config['oid_partner']) {
			//商户号错误
			file_put_contents("log.txt", "3--\n", FILE_APPEND);
			return;
		}
		file_put_contents("log.txt", "4--\n", FILE_APPEND);
		$parameter = array (
				'oid_partner' => $oid_partner,
				'sign_type' => $sign_type,
				'dt_order' => $dt_order,
				'no_order' => $no_order,
				'oid_paybill' => $oid_paybill,
				'money_order' => $money_order,
				'result_pay' => $result_pay,
				'settle_date' => $settle_date,
				'info_order' => $info_order,
				'pay_type' => $pay_type,
				'bank_code' => $bank_code,
				'no_agree' => $no_agree,
				'id_type' => $id_type,
				'id_no' => $id_no,
				'acct_name' => $acct_name
		);
		file_put_contents("log.txt", "5--\n", FILE_APPEND);
		if (!$this->getSignVeryfy($parameter, $sign)) {
			file_put_contents("log.txt", "6--\n", FILE_APPEND);
			return;
		}
		file_put_contents("log.txt", "7--\n", FILE_APPEND);
		$this->notifyResp = $parameter;
		$this->notify_result = true;
		return true;
	}
	
	/**
	 * 获取返回时的签名验证结果
	 * @param $para_temp 通知返回来的参数数组
	 * @param $sign 返回的签名结果
	 * @return 签名验证结果
	 */
	function getSignVeryfy($para_temp, $sign) {
		//除去待签名参数数组中的空值和签名参数
		$para_filter = HelperLlpay::paraFilter($para_temp);
	
		//对待签名参数数组排序
		$para_sort = HelperLlpay::argSort($para_filter);
	
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = HelperLlpay::createLinkstring($para_sort);
	
		//file_put_contents("log.txt", "原串:" . $prestr . "\n", FILE_APPEND);
		//file_put_contents("log.txt", "sign:" . $sign . "\n", FILE_APPEND);
		$isSgin = false;
		switch (strtoupper(trim($this->_config['sign_type']))) {
			case "MD5" :
				$isSgin = HelperLlpay::md5Verify($prestr, $sign, $this->_config['key']);
				break;
			default :
				$isSgin = false;
		}
	
		return $isSgin;
	}
	
	//↑↑↑↑↑↑↑↑↑↑接收异步通知使用到的方法↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
}
