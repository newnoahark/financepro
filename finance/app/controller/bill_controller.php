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
		$table = array(  array("name1"=>"pcm","name2"=>"pcm","name3"=>"pcm"),
						array("name1"=>"pcm","name2"=>"pcm","name3"=>"pcm"),
						array("name1"=>"pcm","name2"=>"pcm","name3"=>"pcm"),
						array("name1"=>"pcm","name2"=>"pcm","name3"=>"pcm"),
			); 
		echo json_encode($table);
	}
	function actionSavedata(){
		if($this->_context->isPOST()){
			//存储药物信息
			$error = "";
			$drugarr = array(
					"name" => isset($_POST['drug_name'])?$_POST['drug_name']:"",
					"drug_specifications" => isset($_POST['drug_specifications'])?$_POST['drug_specifications']:"",
					"approval_number" => isset($_POST['approval_number'])?$_POST['approval_number']:"",
					"dosage_form" => isset($_POST['dosage_form'])?$_POST['dosage_form']:"",
					"zone" => isset($_POST['zone'])?$_POST['zone']:"",
					"manufacturer" => isset($_POST['manufacturer'])?$_POST['manufacturer']:"",
					"supplier" => isset($_POST['supplier'])?$_POST['supplier']:"",
					"validity" => isset($_POST['validity'])?$_POST['validity']:""
			);

			


			$drugobj = new Drug($drugarr);
			
			
			try {
				$drugobj->save();
			} catch (Exception $e) {
				$error = "存储失败";
				
			}

			if($error==""){
				$drug_id = $drugobj->id();
				$date = isset($_POST['billdate'])?strtotime($_POST['billdate']):"";
				$cost = isset($_POST['cost'])?intval($_POST['cost'])*100:"";
				$price = isset($_POST['price'])?intval($_POST['price'])*100:"";
				//存储单据信息
				$billarr = array(
						"drug_id" => $drug_id,
						"billdate" => $date,
						"billnumber" => isset($_POST['billnumber'])?$_POST['billnumber']:"",
						"billtype" => isset($_POST['billtype'])?$_POST['billtype']:"",
						"location" => isset($_POST['location'])?$_POST['location']:"",
						"unit" => isset($_POST['unit'])?$_POST['unit']:"",
						//数量
						"quantity" => intval($_POST['quantity']),
						//入库数量
						"intoquantity" => isset($_POST['intoquantity'])?intval($_POST['intoquantity']):0,
						//出库数量
						"exitquantity" => isset($_POST['exitquantity'])?intval($_POST['exitquantity']):0,
						//成本
						"cost" => intval($cost),
						//单价
						"price" => intval($price),
						"tradeunit" => isset($_POST['tradeunit'])?$_POST['tradeunit']:"",
						"handleperson" => isset($_POST['handleperson'])?$_POST['handleperson']:"",
						"singler" => isset($_POST['singler'])?$_POST['singler']:""
				);

				$billObj = new Bill($billarr);
				//成本金额 = 成本*数量
				$billObj->costamount = $billObj->cost*$billObj->quantity;

				//金额
				$billObj->amount = $billObj->price*$billObj->exitquantity;
				try {

					$billObj->save();
				

				} catch (Exception $e) {
					$error = "存储失败".$e->getMessage();
					
				}
				
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


