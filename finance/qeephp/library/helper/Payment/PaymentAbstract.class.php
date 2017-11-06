<?php
// $Id: Payment.class.php 2014-2-21 XieJH $


/**
 * Payment 类定义了支付接口的公共方法
 *
 * @author XieJH
 * @copyright Copyright (c) 2013-2015 LabPHP Inc.
 * @link http://www.labphp.com/
 */
abstract class PaymentAbstract {

	abstract function request();
	
	abstract function response();
	
	abstract function notify();
}
