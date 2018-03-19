<?php
// $Id$

/**
 * Controller_Bill 控制器
 */
class Controller_Bill extends Controller_Abstract
{

	function actionEditform(){
		
	}
	function actionDesktop(){
		
	}
	function actionTableShow(){
		
	}
	
	function actionTableSendDate(){
		//$this->_viewname = "tableshow";
		//$this->_view['table'] = array("name1"=>"pcm","name2"=>"pcm","name3"=>"pcm");
		
		$goodsObj = Goods::find()->getAll();
		$table = array();
		foreach ($goodsObj as $value) {
			array_push($table,
				array(
				"goods_name"=>$value->goodsname,
				"goods_price"=>$value->goodsprice,
				"goods_amount"=>$value->goodsamount,
				"goods_money"=>$value->goodsmoney

			));
		}
		echo json_encode($table);
	}


	//数据存储
	function actionSavedata(){
		if($this->_context->isPOST()){
			//存储信息
			$error = "";
			$drugarr = array(
					"goodsname" => isset($_POST['goods_name'])?$_POST['goods_name']:"",
					"goodsprice" => isset($_POST['goods_price'])?$_POST['goods_price']:"",
					"goodsamount" => isset($_POST['goods_amount'])?$_POST['goods_amount']:"",

			);

			$goodsObj = new Goods($drugarr);

			

   			//数量不为空
   			if($goodsObj->goodsamount!=""){
   				$temp = (int) $goodsObj->goodsamount*(int)$goodsObj->goodsprice;
   				$goodsObj->goodsmoney = $temp;
   			}else{
   				$goodsObj->goodsmoney = 0;
   			}

			try {
				$goodsObj->save();
			} catch (Exception $e) {
				$error = "存储失败".$e->getMessage() ;
				
			}

			if($error!=""){
				return $this->_error("错误", $error);
			}else{
			return json_encode(array(
					'status' => true,
					'title' => "",
					'message' => "存储成功")); 
			}

		}
	}
	
}


