<?php
// $Id: Payment.class.php 2014-2-21 XieJH $


import("ORG.Util.Payment.PaymentAbstract");
import("ORG.Util.Payment.HelperAlipay");

/**
 * Payment 类定义了支付接口的公共方法
 *
 * @author XieJH
 * @copyright Copyright (c) 2013-2015 LabPHP Inc.
 * @link http://www.labphp.com/
 */
class Gongshang extends PaymentAbstract {

	private $_config = array(
			//接口名称
			'interfaceName' => 'ICBC_PERBANK_B2C',
			//接口版本号
			'interfaceVersion' => '1.0.0.11',
			//签名方式 不需修改
			'sign_type' => 'base64',
			//字符编码格式 目前支持 gbk 或 utf-8
			'charset' => 'utf-8',
			// 语言
			'Language' => 'ZH_CN',
			//签名 地址
			'sign_url'=> 'http://172.17.2.47:8080/gatewayservice/icbc/sign.shtml',
			// 通知返回验签地址
			'back_sign_url'=>'http://172.17.2.47:8080/gatewayservice/icbc/verifySign.shtml',
			
			// 订单提交地址
			'form_url'=>'https://mybank3.dccnet.com.cn/servlet/ICBCINBSEBusinessServlet'
	);

	//服务器异步通知页面路径
	private $_notify_url = "http://101.69.254.235/TicketPayment/gongshangnotify";
	//需http://格式的完整路径，不能加?id=123这类自定义参数
	

	//页面跳转同步通知页面路径
	//private $_return_url = "http://www.wjtrw.com/TicketPayment/gongshangreturn";
	//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	
	//商户账号
	private $_merAcct = '1202207219900052254';
	
	//商户代码
	private $_merID = '1202EC24245829';//20
	

	// 构造请求字串
	private $_request = '';
	
	// 全局唯一实例
	private static $_app = null;

	private function __construct($config) {

		
		if (isset($config['partner'])) {
			$this->_config['partner'] = $config['partner'];
		}
	}

	function init($config) {

		if (null == self::$_app) {
			self::$_app = new Gongshang($config);
		}
		
		return self::$_app;
	}

	function request() {
		
	$xml = '<?xml version="1.0" encoding="GBK" standalone="no"?>
				<B2CReq>
				<interfaceName>'.$this->_config['interfaceName'] .'</interfaceName>
				<interfaceVersion>'.$this->_config['interfaceVersion'].'</interfaceVersion>
				<orderInfo>
					<orderDate>'.$_POST['orderdate'].'</orderDate>
					<curType>'.$_POST['curType'].'</curType>
					<merID>'.$this->_merID.'</merID>
					<subOrderInfoList>
					<subOrderInfo>
						<orderid>'.$_POST['orderid'].'</orderid>
						<amount>'.$_POST['amount'].'</amount>
						<installmentTimes>'.$_POST['installmentTimes'].'</installmentTimes>
						<merAcct>'.$this->_merAcct.'</merAcct>
						<goodsID>'.$_POST['goodsID'].'</goodsID>
						<goodsName>'.$_POST['goodsName'].'</goodsName>
						<goodsNum>'.$_POST['goodsNum'].'</goodsNum>
						<carriageAmt>'.$_POST['carriageAmt'].'</carriageAmt>
					</subOrderInfo>
					</subOrderInfoList>
				</orderInfo>
				<custom>
					<verifyJoinFlag>'.$_POST['verifyJoinFlag'].'</verifyJoinFlag>
					<Language>'.$this->_config['Language'].'</Language>
				</custom>
				<message>
					<creditType>'.$_POST['creditType'].'</creditType>
					<notifyType>'.$_POST['notifyType'].'</notifyType>
					<resultType>'.$_POST['resultType'].'</resultType>
					<merReference>'.$_POST['merReference'].'</merReference>
					<merCustomIp>'.$_POST['merCustomIp'].'</merCustomIp>
					<goodsType>'.$_POST['goodsType'].'</goodsType>
					<merCustomID>'.$_POST['merCustomID'].'</merCustomID>
					<merCustomPhone>'.$_POST['merCustomPhone'].'</merCustomPhone>
					<goodsAddress>'.$_POST['goodsAddress'].'</goodsAddress>
					<merOrderRemark>'.$_POST['merOrderRemark'].'</merOrderRemark>
					<merHint>'.$_POST['merHint'].'</merHint>
					<remark1>'.$_POST['remark1'].'</remark1>
					<remark2>'.$_POST['remark2'].'</remark2>
					<merURL>'.$this->_notify_url.'</merURL>
					<merVAR>'.$_POST['merVAR'].'</merVAR>
				</message>
				</B2CReq>';
		
// 		exit;
		// 处理 表单数据，进行 编码 和加密 
		
		$data_form = $this->dealTheForm($this->_config['sign_url'],array('tranData'=>$xml));
		
		//构造要请求的参数数组，无需改动
		$parameter = array(
				"interfaceName" => $this->_config['interfaceName'],
				"interfaceVersion" => $this->_config['interfaceVersion'],
				"tranData" => $data_form['tranData'],
				"merSignMsg" => $data_form['merSignMsg'],
				"merCert" => $data_form['merCert']
		);
		
		$this->_request = $this->buildRequestForm($parameter);
		header("Content-type: text/html; charset=utf-8");
		echo $this->_request;
		
		
	}
	
	function buildRequestForm($para_temp, $method = 'POST', $button_name = "") {

		$sHtml = "<form id='gongshangsubmit' name='gongshangsubmit' action='" . $this->_config['form_url'] . "' method='" . $method . "'>";
		
		foreach ($para_temp as $key => $val){
			$sHtml .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
		}
	
		//submit按钮控件请不要含有name属性
		$sHtml = $sHtml . "<input type='submit' value=''></form>";
	
		$sHtml = $sHtml . "<script>document.forms['gongshangsubmit'].submit();</script>";
	
		return $sHtml;
	}
	
	function response() {
	
		if (1) { //验证成功
			//请在这里加上商户的业务逻辑程序代码
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
				
	
			//商户订单号
			$out_trade_no = $_GET['out_trade_no'];
				
			//支付宝交易号
				
	
			$trade_no = $_GET['trade_no'];
				
			//交易状态
			$trade_status = $_GET['trade_status'];
				
			if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
			}
			else {
				//echo "trade_status=" . $_GET['trade_status'];
			}
				
			header("location: http://www.wjtrw.com/TicketPayment/paySuccess/order_no/" . $out_trade_no);
				
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
		}
		else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			echo "验证失败";
		}
	}


	
	function notify() {
		
		$post_data['notifyData'] = $_POST['notifyData'];
		$post_data['signMsg'] = $_POST['signMsg'];
		$post_data['merVAR'] = $_POST['merVAR'];
		
		file_put_contents('bbb.txt', ($post_data['notifyData']));
		file_put_contents('ccc.txt', base64_decode($post_data['notifyData']));
		$notify_result = $this->dealTheForm($this->_config['back_sign_url'], $post_data);
	
		if ($notify_result == 0) {
				

			//验证成功
			//请在这里加上商户的业务逻辑程序代
			
			
			//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
			// base 64 解码 先
			$post_data['notifyData'] = base64_decode($post_data['notifyData']);
			preg_match( "/\<orderid\>(.*?)\<\/orderid\>/s", $post_data['notifyData'], $bookblocks );
				
			if(isset($bookblocks['1'])){
				$out_trade_no = $bookblocks['1'];
			}
			
			//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序

			//处理 交易逻辑
			/**
			 * 1: 修改订单状态
			 * 2： 修改交易状态
			 * 3：修改payment 状态
			 *
			 */

			// 付款
			//修改订单
			$res = EventOrderModel::m()->where(array(
					'order_no' => $out_trade_no
			))->save(array(
					'status' => 1,
					'confirm_time' => time(),
					'update_time'  => time()
			));
			HelperAlipay::logResult(EventOrderModel::m()->getLastSql());
			
			//修改订单详情
			$filter['order_no'] = $out_trade_no;
			$order_info = EventOrderModel::m()->where($filter)->find();
	
			$res = EventOrderDetailModel::m()->where(array(
					'order_id' => $order_info['id']
			))->save(array(
					'status' => 1,
					'update_time'  => time()
			));
			HelperAlipay::logResult(EventOrderDetailModel::m()->getLastSql());
			
			//修改电影留言状态
			
			$res = EventMessageModel::m()->where(array(
					'order_id' => $order_info['id']
			))->save(array(
					'status' => 1
			));
			HelperAlipay::logResult(EventMessageModel::m()->getLastSql());
			
			//修改电影购物车
			$filter_movie['session_id'] = session_id();
			$order_info = MovieCartModel::m()->where($filter_movie)->delete();
			
			HelperAlipay::logResult(MovieCartModel::m()->getLastSql());
			
			//注意：
			//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。
			//调试用，写文本函数记录程序运行情况是否正常
			HelperAlipay::logResult("TRADE_SUCCESS");
			
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			$res_str = 'http://www.wjtrw.com/Ticketcomputer'; 
			header('Server: Apache/1.39'); 
			header('Content-Length: ' . strlen($res_str)); 
			header('Content-Type: text/html'); 
			echo $res_str;
		}
		else {
			//调试用，写文本函数记录程序运行情况是否正常
			HelperAlipay::logResult("<br />notify error");
		}
	}
	
	function dealTheForm($url, $post_data){
	
		// 		$url = "http://172.17.2.47:8080/gatewayservice/icbc/sign.shtml";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 我们在POST数据哦！
		curl_setopt($ch, CURLOPT_POST, 1);
		// 把post的变量加上
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		curl_close($ch);
	
		return json_decode($output,true);
	
	}
}
