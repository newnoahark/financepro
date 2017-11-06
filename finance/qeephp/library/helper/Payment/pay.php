<?php
// $Id: Payment.class.php 2014-2-21 XieJH $

/**
 * Pay 支付工厂类
 *
 * @author XieJH
 * @copyright Copyright (c) 2013-2015 LabPHP Inc.
 * @link http://www.labphp.com/
 */
abstract class Pay {

	const ALIPAY = 'alipay';

	const GONGSHANG = 'gongshang';

	const LLPAY = 'llpay';

	static function getApp($payment = self::ALIPAY, array $config = array()) {

		if (strtolower($payment) == self::ALIPAY) {
			$app = Alipay::init($config);
		}
		else if (strtolower($payment) == self::GONGSHANG) {
			$app = Gongshang::init($config);
		}
		else if (strtolower($payment) == self::LLPAY) {
			$app = LLPay::init($config);
		}
		else {
			return false;
		}
		
		return $app;
	}
}
