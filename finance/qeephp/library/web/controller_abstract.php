<?php
// $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $

/**
 * 定义 QController_Abstract 类
 *
 * @link http://qeephp.com/
 * @copyright Copyright (c) 2006-2009 Qeeyuan Inc. {@link
 *            http://www.qeeyuan.com}
 * @license New BSD License {@link http://qeephp.com/license/}
 * @version $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */

/**
 * QController_Abstract 实现了一个其它控制器的基础类
 *
 * @author YuLei Liao <liaoyulei@qeeyuan.com>
 * @version $Id: controller_abstract.php 2010 2009-01-08 18:56:36Z dualface $
 * @package mvc
 */
abstract class QController_Abstract{
	
	/**
	 * 封装请求的对象
	 *
	 * @var QContext
	 */
	protected $_context;
	
	// 控制器要使用的模型的名称
	protected $_model_name;
	
	// 控制器要使用的模型
	protected $_model;
	
	// 数据库前前缀
	protected $_dbPrefix;
	
	// 每页显示的记录数
	protected $_page_rows;
	
	// 最大显示多少页
	protected $_page_pages;
	
	// 控制器要使用的表单名称
	protected $_form_name;
	
	// 控制器要使用的表单
	protected $_form;
	
	// update时是否跳过表单验证
	protected $_skip_validate = false;
	
	// 当前表单是否生成post_key
	protected $_post_key = false;
	
	// 是否使用引导式内存编辑
	protected $_step_form = false;
	
	/**
	 * 默认显示的curd操作按钮
	 */
	protected $_curd_switcher = array(
			'create',
			'update',
			'read',
			'delete' 
	);
	
	// 是否使用回收站
	protected $_recycle = false;
	
	// 控制器要使用哪些字段进行过滤
	protected $_filter_items = array();
	
	// 过滤条件
	protected $_filters = array();
	
	// 列表页的参数
	protected $_finder = '';
	
	// 当前用户
	protected $_user = null;
	
	/**
	 * 指示编辑记录时显示的多对多关联
	 */
	protected $_many_to_many = array();
	
	/**
	 * 是否进行地区和部门限制，是否允许发布人、所有人
	 */
	protected $_zone_limit = false;
	
	protected $_department_limit = false;
	// protected $_allow_owner = true;
	// protected $_allow_creator = true;
	/**
	 * 构造函数
	 */
	function __construct(){

		$this->_context = QContext::instance();
		$this->_view['udi'] = $this->_context;
		
		$this->_dbPrefix = Q::ini("db_dsn_pool/default/prefix");
		
		// 设置URL传入的参数
		Helper_Array::unsetKey($_GET, array(
				'module',
				'submodule',
				'controller',
				'action' 
		));
		$this->_view['_curd_switcher'] = $this->_curd_switcher;
		$this->_view['step_form'] = $this->_step_form;
		
		/*
		 * 设置控制器要使用的模型和表单对象
		 */
		$this->_model = $this->_get_model();
		$this->_form = $this->_get_form();
		//
	}

	/**
	 * 检查指定的动作方法是否存在
	 *
	 * @param string $action_name        
	 *
	 * @return boolean
	 */
	function existsAction($action_name){

		$action_method = "action{$action_name}";
		return method_exists($this, $action_method);
	}

	/**
	 * 转发请求到控制器的指定动作
	 *
	 * @param string $udi        
	 *
	 * @return mixed
	 */
	protected function _forward($udi){

		$args = func_get_args();
		array_shift($args);
		return new QController_Forward($udi, $args);
	}

	/**
	 * 返回一个 QView_Redirect 对象
	 *
	 * @param string $url        
	 * @param int $delay        
	 *
	 * @return QView_Redirect
	 */
	protected function _redirect($url, $delay = 0){

		return new QView_Redirect($url, $delay);
	}

	/**
	 * 渲染指定模板并返回内容
	 *
	 * @param string $viewname        
	 *
	 * @return string
	 *
	 */
	protected function fetch($viewname = null){
		
		// 指定渲染模板
		if($viewname){
			
			$_view = $this->_getViewName();
			
			$this->_viewname = $viewname;
		}
		
		// 渲染视图
		
		$response = $this->_fetch($viewname);
		
		$result = $response->fetch($this->_getViewName());
		
		// 恢复原渲染模板
		
		if(isset($_view)){
			
			$this->_view = $_view;
		}
		
		return $result;
	}

	/**
	 * 获取模板对象
	 *
	 * @param string $viewname        
	 *
	 * @return object
	 *
	 */
	protected function _fetch(){

		$config = array(
				'view_dir' => $this->_getViewDir() 
		);
		
		$response = new $this->_view_class($config);
		
		$response->setViewname($this->_getViewName())
			->assign($this->_view);
		
		$this->_before_render($response); // 渲染视图前调用（一些旧版会报错，如果报错，删除此行或者添加一个_before_render的方法）
		
		return $response;
	}

	/**
	 * 设置finder参数
	 */
	protected function _finder(){

		if($this->_recycle){
			if($this->_context->action_name == 'recycle'){
				$this->_filters['recycle'] = '1';
			}else{
				$this->_filters['recycle'] = 0;
			}
		}
		
		$this->_assign(array(
				'finder' => $this->_finder,
				'filters' => $this->_filters,
				'model' => $this->_model,
				'form' => $this->_form,
				'recycle' => $this->_recycle,
				'filter_items' => $this->_filter_items,
				'page_rows' => $this->_page_rows,
				'page_pages' => $this->_page_pages 
		));
	}

	/**
	 * 默认的index()方法
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-8
	 */
	function actionIndex(){

		$rtn = $this->_before_index();
		if($rtn){
			return $rtn;
		}
		
		if(!empty($this->_finder)){
			$this->_finder();
		}
		
		$rtn = $this->_after_index();
		if($rtn){
			return $rtn;
		}
	}

	/**
	 * 默认的recycle()方法
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-15
	 */
	function actionRecycle(){

		$rtn = $this->_before_recycle();
		if($rtn){
			return $rtn;
		}
		
		if(!empty($this->_finder)){
			$this->_finder();
		}
		
		$rtn = $this->_after_recycle();
		if($rtn){
			return $rtn;
		}
	}

	/**
	 * 默认的create()方法
	 *
	 * @author sqlhost
	 * @version 1.0.0
	 *          2012-4-8
	 */
	function actionCreate(){

		if($rtn = $this->checkModel() || $rtn = $this->checkForm()){
			return $this->checkModel();
		}
		
		if($rtn = $this->_before_create()){
			return $rtn;
		}
		
		// 指定表单动作
		if($this->_form->action == ''){
			$this->_form->action = url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/create");
		}
		
		// 获得模型的属性
		$props = $this->_model->meta()->props;
		
		// 自动生成create_uid, update_uid
		if($this->_user->uid){
			if(isset($props['create_uid'])){
				$_POST['create_uid'] = $this->_user->uid;
			}
			if(isset($props['uid'])){
				$_POST['uid'] = $this->_user->uid;
			}
			if(isset($props['update_uid'])){
				$_POST['update_uid'] = $this->_user->uid;
			}
		}
		
		// 处理表单提交
		if($this->_context->isPOST() && $this->_form->validate($_POST)){
			
			/*
			 * 判断post_key是否存在，如果存在，则更新post_key，用于重复提交记录
			 */
			if($this->_post_key){
				$count = $this->_model->meta()
					->find(array(
						'post_key' => $this->_form['post_key']->value 
				))
					->getCount();
				if($count > 0){
					return $this->_error("新增失败", "不允许重复提交", url($this->_context->submodule_name . '::' . $this->_context->controller_name, $this->_context->get()));
					exit();
				}
			}
			
			// 添加一个 hidden 到表单
			// $this->_form->add(QForm::ELEMENT, 'uid', array(
			// '_ui' => 'hidden',
			// 'value' => $this->_user->uid
			// ));
			
			/*
			 * 处理一些特殊的字段，如： static, readonly, many_to_many, memo, checkboxgroup and field's property is text or varchar
			 */
			// 要移除的元素
			$protects = array();
			// 循环模型的属性
			foreach($this->_form->elements() as $key => $val){
				if(!array_key_exists($key, $props)){
					continue;
				}
				// 如果是static或者只读字段
				if($val->_ui == 'static'){
					$protects[] = $key;
					continue;
				} // 如果是many_to_many
				elseif($props[$key]['assoc'] == 'many_to_many'){
					
					if(is_array($this->_form[$key]->value) || !$this->_form[$key]->value){
						$objName = $props[$key]['assoc_class'];
						$obj = new $objName();
						$this->_form[$key]->value = $obj->find(array(
								$obj->idName() => $this->_form[$key]->value 
						))
							->getAll();
					}
				}elseif($val->_ui == 'memo'){
					$this->_form[$key]->value = h($val->value);
				}elseif($val->_ui == 'checkboxgroup' && in_array($props[$key]['ptype'], array(
						'text',
						'varchar' 
				))){
					$this->_form[$key]->value = serialize($val->value);
				}
			}
			
			foreach($protects as $key){
				unset($this->_form[$key]);
			}
			// --
			// 创建模型
			$row = new $this->_model($this->_form->values());
			
			// 处理belongs_to关联，如Usinfo->User，避免错误提示
			$idName = $this->_model->idName();
			$assocClass = null;
			$assocName = '';
			foreach($props as $key => $val){
				if(isset($val['assoc_params']) && isset($val['assoc_params']['source_key']) && $val['assoc_params']['source_key'] == $idName && $val['assoc'] == 'belongs_to'){
					$assocClass = $val['assoc_class'];
					$assocName = $key;
					if($assocClass){
						// $row->changePropForce($val['assoc_params']['source_key'],
						// $this->_context->{$val['assoc_params']['source_key']});
						$assocModel = new $assocClass();
						$row->{$assocName} = $assocModel->meta()
							->find($assocModel->idName() . " = ?", $this->_context->{$val['assoc_params']['source_key']})
							->query();
					}
				}
			}
			
			/**
			 * 处理相关主题，可能会存在问题
			 */
			foreach($this->_many_to_many as $key => $val){
				if(!empty($_POST[$val])){
					$assocName = $props[$val]['assoc_class'];
					$assocModel = new $assocName();
					
					$rows = $assocModel->find(array(
							$assocModel->idName() => $_POST[$val] 
					))
						->getAll();
					
					$row->{$val} = $rows;
				}
			}
			// --
			
			// 第一次保存，以产生$row->id
			$row->save();
			/*
			 * 处理附件
			 */
			if($this->_post_key && isset($this->_form['post_key'])){
				$attachs = Attach::find(array(
						'post_key' => $this->_form['post_key']->value 
				))->getAll();
				if(count($attachs)){
					Attach::meta()->updateWhere(array(
							'post_id' => $row->id() 
					), array(
							'id' => Helper_Array::toHashmap($attachs, 'id', 'id') 
					));
				}
				unset($attachs);
			}
			
			if($rtn = $this->_after_create($row)){
				return $rtn;
			}
			
			// 添加日志
			$thisfun = substr(__FUNCTION__, 6);
			$logcont = $thisfun . '了1条数据';
			$this->createlog($this->_form->action, $logcont);
			
			return $this->_success('创建成功', '创建成功', url($this->_context->submodule_name . '::' . $this->_context->controller_name, $this->_context->get()));
		} // 如果验证通不过
		elseif($this->_context->isPOST()){
			// print_r($this->_form);exit;
			/**
			 * 处理特殊元素
			 * static：重设置记录的值
			 * rradio: 查询出当前选中项的关联记录
			 * many_to_many：查询出当前选中项的关联记录
			 */
			foreach($this->_form->elements() as $key => $val){
				/*
				 * 处理表单验证通不过的字段，供ajax_form用
				 */
				if(is_array($val->errorMsg()) && count($val->errorMsg())){
					// $error[$key] = nl2br(h(implode("，", $val->errorMsg())));
					$error = implode("，", $val->errorMsg());
					break;
				}
				// -
				if(!array_key_exists($key, $props)){
					continue;
				}
				if($val->_ui == 'static'){
					// $this->_form[$key]->value = $row[$key];
				}elseif($val->_ui == 'rradio'){
					$objName = $val->assoc_class;
					$this->_form[$key]->parent_items = Helper_Array::toHashMap($row->$objName, 'id', 'name');
					$objName = $props[$key]['assoc_class'];
					$obj = new $objName();
					$this->_form[$key]->current_id = helper_Array::toHashMap($obj->find(array(
							$obj->idName() => $this->_form[$key]->value 
					))
						->getAll(), 'id');
				} // rselect
				elseif($val->_ui == 'rselect'){
					$this->_form[$key]->parent_id = $this->_form[$key]->value;
				}elseif($props[$key]['assoc'] == 'many_to_many'){
					$objName = $props[$key]['assoc_class'];
					$obj = new $objName();
					$this->_form[$key]->value = $obj->find(array(
							$obj->idName() => $this->_form[$key]->value 
					))
						->getAll();
				}
			}
			unset($obj);
			if($this->_context->_ajax || $this->_context->isAJAX()){
				$this->_error('新增失败', $error);
			}
		}else{
			foreach($this->_form->elements() as $key => $val){
				if(!array_key_exists($key, $props)){
					continue;
				}elseif($val->_ui == 'rselect'){
					$this->_form[$key]->parent_id = isset($this->_context->$key) ? $this->_context->$key : 0;
				}
			}
			// 生成随机唯一字符串
			if($this->_post_key){
				$_SESSION['post_key'] = Helper_Common::unique_id();
				$this->_form['post_key']->value = $_SESSION['post_key'];
			}
		}
		
		// 指定视图变量
		$this->_view['form'] = $this->_form;
		$this->_view['finder'] = $this->_finder;
	}

	/**
	 * 默认的编辑方法
	 */
	function actionUpdate(){

		if($rtn = $this->checkModel() || $rtn = $this->checkForm()){
			return $this->checkModel();
		}
		
		/*
		 * 查询记录 如果存在主表，则
		 */
		$id = intval($this->_context->{$this->_model->idName()});
		
		if(empty($id)){
			return $this->_error('修改失败', '操作错误！', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
		}
		
		if($this->_zone_limit || $this->_department_limit){
			$this->_filters[] = $this->_check_limit();
		}
		
		// 获得记录
		$this->_filters[$this->_model->idName()] = $id;
		$row = $this->_model->find($this->_filters)
			->query();
		// 如果的 ID 无效
		if(!$row->id()){
			return $this->_error('修改失败', '记录不存在！', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
		}
		
		if($rtn = $this->_before_update($row)){
			return $rtn;
		}
		
		// 获得模型的属性
		$props = $this->_model->meta()->props;
		
		// 重新设置URL传入变量，移除$id
		// unset($_GET[$this->_model->idName()]);
		
		// 指定表单动作
		if($this->_form->action == ''){
			$this->_form->action = url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/update", $this->_context->get());
		}
		
		// 如果没有设置主键，就添加一个 hidden 元素到表单
		if(!isset($this->_form[$this->_model->idName()])){
			$this->_form->add(QForm::ELEMENT, $this->_model->idName(), array(
					'_ui' => 'hidden' 
			));
		}
		
		// 自动生成create_uid, update_uid
		if($this->_user->uid){
			if(isset($props['update_uid'])){
				$_POST['update_uid'] = $this->_user->uid;
			}
		}
		
		/**
		 * 提交表单
		 */
		if($this->_context->isPOST() && $this->_form->validate($_POST)){
			/**
			 * 处理特殊字段
			 */
			// 要移除的元素
			$protects = array();
			
			// 附件保存的模型
			// $attach_models = array();
			
			// 循环模型的属性
			foreach($this->_form->elements() as $key => $val){
				
				if(!array_key_exists($key, $props)){
					continue;
				}
				
				/*
				 * 找出图片和附件指定保存在哪个模型中
				 */
				// if ($val->photo_target && !in_array($val->photo_target,
				// $attach_models)) {
				// $attach_models[] = $val->photo_target;
				// }
				// if ($val->attach_target && !in_array($val->attach_target,
				// $attach_models)) {
				// $attach_models[] = $val->attach_target;
				// }
				// -
				
				// 如果是static或者只读字段
				if($val->_ui == 'static' || $props[$key]['readonly'] && $key != $this->_model->idName()){
					$protects[] = $key;
					continue;
				} // 如果是many_to_many
				elseif($props[$key]['assoc'] == 'many_to_many'){
					
					/*
					 * 数组有元素，但是以下判断却为true，不知道是什么原因，故改成$arr
					 */
					// if (empty($this->_form[$key]->value)) {
					// echo "d";
					// }
					// else {
					// echo "c";
					// }
					
					$arr = $this->_form[$key]->value;
					
					if(empty($arr)){
						$this->_form[$key]->value = array();
					}elseif(is_array($arr) || $arr){
						$objName = $props[$key]['assoc_class'];
						$obj = new $objName();
						$this->_form[$key]->value = $obj->find(array(
								$obj->idName() => $arr 
						))
							->getAll();
					}
				}elseif($val->_ui == 'memo'){
					$this->_form[$key]->value = h($val->value);
				}elseif($val->_ui == 'checkboxgroup' && in_array($props[$key]['ptype'], array(
						'text',
						'varchar' 
				))){
					$this->_form[$key]->value = serialize($val->value);
				}
				// elseif ($props[$key]['assoc'] == 'has_one' ||
				// $props[$key]['assoc'] == 'has_many') {
				
				// }
			}
			unset($obj);
			
			foreach($protects as $key){
				unset($this->_form[$key]);
			}
			
			// changeProps() 方法可以批量修改对象的属性，但不会修改只读属性的值
			$row->changeProps($this->_form->values());
			
			/*
			 * 处理附件
			 */
			if($this->_post_key && isset($this->_form['post_key'])){
				// 处理图片
				$attachs = Attach::find(array(
						'post_key' => $this->_form['post_key']->value,
						"(post_id is null or post_id = '')" 
				))->getAll();
				if(count($attachs)){
					Attach::meta()->updateWhere(array(
							'post_id' => $row->id() 
					), array(
							'id' => Helper_Array::toHashmap($attachs, 'id', 'id') 
					));
				}
				unset($attachs);
				// --
			}
			
			// 保存并重定向浏览器
			$row->save();
			
			if($rtn = $this->_after_update($row)){
				return $rtn;
			}
			
			// 添加日志
			$thisfun = substr(__FUNCTION__, 6);
			$logcont = $thisfun . '了1条数据';
			$this->createlog($this->_form->action, $logcont);
			
			return $this->_success('修改成功', '您已经成功修改了一条记录！', url($this->_context->submodule_name . '::' . $this->_context->controller_name, $this->_context->get()));
		} // 如果验证通不过
		elseif($this->_context->isPOST()){
			$this->_error('表单验证失败', $this->_form_invalidate_message());
		} // 否则的则把对象值导入表单
		else{
			// 生成随机唯一字符串
			if($this->_post_key){
				if(empty($row->post_key)){
					$row->post_key = Helper_Common::unique_id();
					$row->save();
				}
				$_SESSION['post_key'] = $row->post_key;
			}
			
			$this->_form->import($row);
			$this->_after_import($row);
			
			/**
			 * 处理特殊元素
			 */
			foreach($this->_form->elements() as $key => $element){
				
				if($element->_ui == 'static' && isset($props[$element->id])){
					$this->_form[$key]->caption = $element->value;
				} // checkboxgroup and serialize
				elseif($element->_ui == 'checkboxgroup' && in_array($props[$key], array(
						'text',
						'varchar' 
				))){
					$this->_form[$key]->value = unserialize($element->value);
				}elseif($element->_ui == 'textbox' && isset($props[$key]) && isset($props[$key]['assoc']) && $props[$key]['assoc'] == 'many_to_many'){
					$value = array();
					foreach($this->_form[$key]->value as $k => $v){
						$value[] = $v->id;
					}
					$this->_form[$key]->value = implode(' ', $value);
				}
			}
		}
		
		// 指定表单变量
		$this->_view['form'] = $this->_form;
		$this->_view['row'] = $row;
		$this->_view['finder'] = $this->_finder;
	}

	/**
	 * 默认delete()方法
	 */
	function actionDelete(){

		if($rtn = $this->checkModel()){
			return $this->checkModel();
		}
		
		if($rtn = $this->_before_delete()){
			return $rtn;
		}
		
		// 获得要删除的id
		if(is_array($this->_model->idName())){
			$id = $this->_context->id;
		}else{
			$id = Helper_Common::safeIntval($this->_context->{$this->_model->idName()});
		}
		
		if(empty($id)){
			return $this->_error('删除失败', '请选择要删除的记录 ', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/' . $action, $this->_context->get()));
		}
		
		if($this->_zone_limit || $this->_department_limit){
			$this->_filters[] = $this->_check_limit();
		}
		
		$count = 0;
		
		// 如果开启了回收站，并且不是在回收站中点击的删除，则放入回收站
		$get = $this->_context->get();
		if($this->_recycle && (!array_key_exists('recycle', $this->_context->get()) || empty($get['recycle']))){
			$this->_filters[$this->_model->idName()] = $id;
			$count = $this->_model->meta()
				->updateWhere(array(
					'recycle' => '1' 
			), $this->_filters);
		}else{
			if(is_array($this->_model->idName())){
				foreach($id as $key => $val){
					$this->_filters = array_merge($this->_filters, $val);					
				}
				$count += $this->_model->meta()->destroyWhere($this->_filters);
			}else{
				$this->_filters[$this->_model->idName()] = $id;
				$count = $this->_model->meta()
					->destroyWhere($this->_filters);
			}
		}
		
		// 重新设置URL传入变量，移除$id
		if(!is_array($this->_model->idName())){
			unset($_GET[$this->_model->idName()]);
		}
		
		// 设置删除后的跳转动作
		if($this->_recycle && array_key_exists('recycle', $this->_context->get()) && $get['recycle'] == '1'){
			$action = 'recycle';
		}else{
			$action = 'index';
		}
		
		if($rtn = $this->_after_delete()){
			return $rtn;
		}
		
		// 添加日志
		$actions = $_SERVER['REQUEST_URI'];
		$thisfun = substr(__FUNCTION__, 6);
		$logcont = $thisfun . '了' . $count . '条数据';
		$this->createlog($actions, $logcont);
		
		return $this->_success('删除成功', '您已经成功删除了 ' . $count . ' 条记录', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/' . $action, $this->_context->get()));
	}

	/**
	 * 默认的从回收站恢复记录
	 */
	function actionRestore(){

		if($rtn = $this->checkModel()){
			return $this->checkModel();
		}
		// 如果没有开启回收站
		if(!$this->_recycle){
			return $this->_error('恢复失败', '系统没有开启回收站', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/index'));
		}
		
		if($rtn = $this->_before_restore()){
			return $rtn;
		}
		
		// 获得要回复的id
		$id = Helper_Common::safeIntval($this->_context->{$this->_model->idName()});
		$this->_filters[$this->_model->idName()] = $id;
		
		if($this->_zone_limit || $this->_department_limit){
			$this->_filters[] = $this->_check_limit();
		}
		
		$count = 0;
		$count = $this->_model->meta()
			->updateWhere(array(
				'recycle' => '0' 
		), $this->_filters);
		
		// 重新设置URL传入变量，移除$id
		unset($_GET[$this->_model->idName()]);
		
		if($rtn = $this->_after_restore()){
			return $rtn;
		}
		
		// 添加日志
		// $actions= implode(array_keys($this->_context->get()));
		$actions = $_SERVER['REQUEST_URI'];
		$thisfun = substr(__FUNCTION__, 6);
		$logcont = $thisfun . '了' . $count . '条数据';
		$this->createlog($actions, $logcont);
		
		return $this->_success('恢复成功', '您已经成功恢复了 ' . $count . ' 条记录', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/recycle', $this->_context->get()));
	}

	/**
	 * 审核
	 */
	function actionVerify(){

		if($this->_context->isPOST()){
			if($rtn = $this->checkModel() || $rtn = $this->checkForm()){
				return $this->checkModel();
			}
			
			if($rtn = $this->_before_verify()){
				return $rtn;
			}
			
			if(is_array($this->_model->idName())){
				$id = $this->_context->id;
			}else{
				$id = Helper_Common::safeIntval($this->_context->{$this->_model->idName()});
			}
			
			if(empty($id)){
				return $this->_error('系统提示', '操作错误', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
			}
			
			if(!isset($_POST['status'])){
				return $this->_error('系统提示', '操作错误', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
			}
			
			if($this->_zone_limit || $this->_department_limit){
				$this->_filters[] = $this->_check_limit();
			}
			
			// 获得模型的属性
			$props = $this->_model->meta()->props;
			
			// 修改 update_uid
			if($this->_user->uid){
				if(isset($props['update_uid'])){
					$_POST['update_uid'] = $this->_user->uid;
				}
				if(isset($props['verify_uid'])){
					$_POST['verify_uid'] = $this->_user->uid;
				}
				if(isset($props['verified'])){
					$_POST['verified'] = time();
				}
			}
			
			if(is_array($this->_model->idName())){
				foreach($id as $key => $val){
					$this->_filters = array_merge($this->_filters, $val);
					$this->_model->meta()
						->updateWhere($_POST, $this->_filters);
				}
			}else{
				
				$this->_filters[$this->_model->idName()] = $id;
				
				$this->_model->meta()
					->updateWhere($_POST, $this->_filters);
			}
			
			if($rtn = $this->_after_verify()){
				return $rtn;
			}
			
			// 重新设置URL传入变量，移除$id
			if(!is_array($this->_model->idName())){
				unset($_GET[$this->_model->idName()]);
			}
			
			return $this->_success('系统提示', '操作成功', url($this->_context->submodule_name . '::' . $this->_context->controller_name, $this->_context->get()));
		}
	}

	/**
	 * 修改某个字段的编辑方法
	 */
	function actionModify(){

		if($this->_context->isPOST()){
			if($rtn = $this->checkModel() || $rtn = $this->checkForm()){
				return $this->checkModel();
			}
			
			if($rtn = $this->_before_modify()){
				return $rtn;
			}
			
			if(is_array($this->_model->idName())){
				$id = $this->_context->id;
			}else{
				$id = Helper_Common::safeIntval($this->_context->{$this->_model->idName()});
			}
			
			if(empty($id)){
				return $this->_error('系统提示', '操作错误', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
			}
			
			if($this->_zone_limit || $this->_department_limit){
				$this->_filters[] = $this->_check_limit();
			}
			
			// 获得模型的属性
			$props = $this->_model->meta()->props;
			
			// 自动生成create_uid, update_uid
			if($this->_user->uid){
				if(isset($props['update_uid'])){
					$_POST['update_uid'] = $this->_user->uid;
				}
			}
			
			/**
			 * 处理特殊字段
			 */
			// 循环模型的属性
			foreach($_POST as $key => $val){
				
				if(!array_key_exists($key, $props)){
					unset($_POST[$key]);
				} // 如果是static或者只读字段
				elseif($this->_form[$key]->_ui == 'static' || $props[$key]['readonly'] && $key != $this->_model->idName()){
					unset($_POST[$key]);
				} // 如果是many_to_many
				elseif($props[$key]['assoc'] == 'many_to_many'){
					if(empty($_POST[$key])){
						$_POST[$key] = array();
					}elseif($_POST[$key] || $_POST[$key]){
						$objName = $props[$key]['assoc_class'];
						$obj = new $objName();
						$_POST[$key] = $obj->find(array(
								$obj->idName() => $val 
						))
							->getAll();
						unset($obj);
					}
				}elseif($this->_form[$key]->_ui == 'memo'){
					$_POST[$key] = h($val);
				}elseif($this->_form[$key]->_ui == 'checkboxgroup' && in_array($props[$key]['ptype'], array(
						'text',
						'varchar' 
				))){
					$_POST[$key] = serialize($val);
				}elseif($key == 'status'){
					if($this->_user->uid){
						if(isset($props['verify_uid'])){
							$_POST['verify_uid'] = $this->_user->uid;
						}
						if(isset($props['verified'])){
							$_POST['verified'] = time();
						}
					}
				}
			}
			
			if(is_array($this->_model->idName())){
				foreach($id as $key => $val){
					$this->_filters = array_merge($this->_filters, $val);
					$this->_model->meta()
						->updateWhere($_POST, $this->_filters);
				}
			}else{
				
				$this->_filters[$this->_model->idName()] = $id;
				
				$this->_model->meta()
					->updateWhere($_POST, $this->_filters);
			}
			
			if($rtn = $this->_after_modify()){
				return $rtn;
			}
			
			// 重新设置URL传入变量，移除$id
			if(!is_array($this->_model->idName())){
				unset($_GET[$this->_model->idName()]);
			}
			
			// 添加日志
			$actions = $_SERVER['REQUEST_URI'];
			$thisfun = substr(__FUNCTION__, 6);
			$logcont = $thisfun . '了1条数据';
			$this->createlog($actions, $logcont);
			
			return $this->_success('系统提示', '操作成功', url($this->_context->submodule_name . '::' . $this->_context->controller_name, $this->_context->get()));
		}
	}

	protected function actionClone(){

		if($rtn = $this->checkModel()){
			return $this->checkModel();
		}
		
		// 获得参数
		if(is_array($this->_model->idName())){
			// $id = $this->_context->id;
			return $this->_error('复制失败', '该类型的记录不允许复制', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
		}else{
			$id = Helper_Common::safeIntval($this->_context->{$this->_model->idName()});
		}
		
		if(empty($id)){
			return $this->_error('复制失败', 'ID错误', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
		}
		
		// 获得记录
		$where = array(
				$this->_model->idName() => $id 
		);
		$row = $this->_model->find($where)
			->query();
		if(!$row->id()){
			return $this->_error('复制失败', '记录不存在', url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/index"));
		}
		
		$new = clone $row;
		$new->save();
		
		// 获得模型的属性
		$props = $this->_model->meta()->props;
		
		// 遍历模型的属性，复制关联对象
		foreach($props as $key => $val){
			if($val['assoc'] == 'many_to_many'){
				$new->{$key} = $row->{$key};
			}elseif($val['assoc'] == 'has_many'){
				$targets = $row->{$key};
				foreach($targets as $k => $v){
					$new->{$key}[] = clone $v;
				}
			}elseif($val['assoc'] == 'has_one'){
				$new->{$key} = clone $row->{$key};
			}
		}
		
		// 存在关联对象
		$new->save();
		
		return $this->_success('复制成功', '您已经成功复制了 1 条记录', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/index', $this->_context->get()));
	}

	protected function actionFinder(){

		$this->_before_finder();
		// 获得finder
		if(!empty($this->_finder)){
			$this->_finder();
		}
		
		// 合并finderfilter_view中post变量
		$args = array_merge($this->_filters, $this->_context->get(), $this->_context->post());
		
		Helper_Array::removeEmpty($args);
		
		$args = Helper_Array::removeKey($args, array(
				'page',
				'rows' 
		));
		
		// 排序不作为筛选条件
		if(isset($args['sort'])){
			unset($args['sort']);
		}
		if(isset($args['order'])){
			unset($args['order']);
		}
		if(isset($args['_'])){
			unset($args['_']);
		}
		
		foreach($args as $key => $val){
			$this->_filters[str_replace('finder_search_', '', $key)] = $val;
		}
		// 设置上下文 $_context->args，将数组类型的值转换成字符串
		foreach($args as $key => $val){
			if(is_array($val)){
				$param = $comma = '';
				foreach($val as $k => $v){
					// $param .= $comma.$key.'['.$k.']='.$v;
					// $comma = '&';
					$args[$key . "[" . $k . "]"] = $v;
				}
				$args[$key] = $param;
			}
		}
		
		$this->_view['args'] = $args;
		
		// 单独添加recycle条件
		// if (isset($args['recycle'])) {
		// $this->_filters['recycle'] = $args['recycle'];
		// }
		
		// 移除值为空的条件
		Helper_Array::removeEmpty($this->_filters);
		
		// 设置过滤条件
		$props = $this->_model->meta()->props;
		foreach($this->_form->elements() as $key => $val){
			// 如果表单提交的项不在过滤表中，跳过
			if(!array_key_exists($key, $this->_filters)){
				continue;
			}
			
			// 如果提交的值为空，删除该过滤条件
			if(!isset($this->_filters[$key])){
				unset($this->_filters[$key]);
				continue;
			}
			// textbox
			if($val->_ui == 'textbox'){
				$this->_filters[] = new QDB_Expr($key . " like '%" . $this->_filters[$key] . "%'");
				unset($this->_filters[$key]);
			} // checkboxgroup 和 rcheckbox
			elseif($val->_ui == 'checkboxgroup' || $val->_ui == 'rcheckbox'){
				if($props[$key]['assoc'] == 'many_to_many'){
					$objName = $props[$key]['assoc_class'];
					$obj = new $objName();
					$this->_filters["[" . $key . "." . $obj->idName() . "]"] = $this->_filters[$key];
					unset($this->_filters[$key]);
				}elseif($props[$key]['ptype'] == 'set'){
					$comma = '';
					foreach($this->_filters[$key] as $v){
						$temp = $comma . $v;
						$comma = '|';
					}
					$this->_filters[] = new QDB_Expr($key . " REGEXP '" . $temp . "'");
					unset($this->_filters[$key]);
				}
			} // rselect
			elseif($val->_ui == 'rselect' && $key != 'parent_id'){
				$this->_filters["parent_id"] = $this->_filters[$key];
				unset($this->_filters[$key]);
			}
		}
		unset($temp, $comma);
		
		if($this->_zone_limit || $this->_department_limit){
			$this->_filters[] = $this->_check_limit();
		}
		
		// 分页
		$pagination = null;
		$page = intval($this->_context->page) > 0 ? intval($this->_context->page) : 1;
		$limit = intval($this->_context->rows) > 0 ? intval($this->_context->rows) : ($this->_page_rows ? $this->_page_rows : 15);
		
		// 排序
		$sort = isset($this->_context->sort) ? $this->_context->sort : '';
		$order = isset($this->_context->order) ? $this->_context->order : '';
		$order_str = '';
		if(strlen($sort) > 0 && strlen($order) > 0){
			$order_str = $sort . ' ' . $order;
		}
		// dump($pagination);
		// 查询记录
		if($this->_finder == 'datagrid'){
			$rows = $this->_model->find($this->_filters)
				->order($order_str)
				->limitPage($page, $limit)
				->fetchPagination($pagination)
				->getAll();
			
			$this->_after_finder($rows);
			
			$rtn = array(
					'total' => $pagination['record_count'],
					'rows' => $rows->toArray() 
			);
			
			if(is_array($this->_model->idName())){
				foreach($rtn['rows'] as $key => $val){
					$id = '';
					$comma = '';
					foreach($this->_model->idName() as $k => $v){
						$id .= $comma . $val[$v];
						$comma = ',';
					}
					$rtn['rows'][$key]['id'] = $id;
				}
			}
			
			/*
			 * 如果是在创建或者编辑界面中输出多对多相关主题列表时，需要设置多对多键名值
			 */
			$_many_to_many = isset($_REQUEST['many_to_many']) ? $_REQUEST['many_to_many'] : '';
			if($_many_to_many){
				foreach($rtn['rows'] as $key => $val){
					$rtn['rows'][$key][$_many_to_many . "[]"] = $val['id'];
				}
			}
		}else{
			$dynamic_tree = isset($this->_filters['_DYNAMIC_TREE']) ? intval($this->_filters['_DYNAMIC_TREE']) : 0;
			$id = intval($this->_context->{$this->_model->idName()});
			if($dynamic_tree){
				if($this->_context->_recycle == 0){
					$this->_filters['parent_id'] = $id;
				}
				unset($this->_filters['id']);
				unset($this->_filters['_DYNAMIC_TREE']);
			}
			$rows = $this->_model->find($this->_filters)
				->order('sort')
				->getAll();
			
			$this->_after_finder($rows);
			
			if($dynamic_tree){
				$datas = array();
				foreach($rows as $key => $val){
					$datas[$key] = $val->toArray();
					if(count($val->children)){
						$datas[$key]['state'] = 'closed';
					}else{
						$datas[$key]['state'] = 'open';
					}
				}
				$rtn = $datas;
			}else{
				$rtn = array(
						'total' => count($rows),
						'rows' => Helper_Array::toTree($rows->toArray(), 'id', 'parent_id', 'children') 
				);
			}
		}
		
		unset($filters, $finder);
		
		echo json_encode($rtn);
		exit();
	}

	/**
	 * 无限分类模型获得树型列表
	 */
	function actionAjaxGetTree(){

		if(isset($this->_context->parent_id)){
			$parent_id = intval($this->_context->parent_id);
		}
		
		$rows = $this->_model->cache();
		
		$array = array();
		
		foreach($rows as $val){
			$array[] = array(
					'id' => $val['id'],
					'text' => $val['name'],
					'parent_id' => $val['parent_id'] 
			);
		}
		
		if(isset($parent_id)){
			$tree = Helper_Array::toTree($array, 'id', 'parent_id', 'children', false, $ref);
			echo json_encode(array(
					0 => $ref[$parent_id] 
			));
		}else{
			$tree = Helper_Array::toTree($array, 'id', 'parent_id', 'children');
			echo json_encode(array(
					0 => array(
							'id' => 0,
							'text' => '全部',
							'parent_id' => 0,
							'children' => $tree 
					) 
			));
		}
		exit();
	}

	/**
	 */
	function actionAjaxGetList(){

		if(isset($this->_context->id)){
			$parent_id = intval($this->_context->id);
		}
		
		$state = intval($this->_context->dynamic) ? 'closed' : 'open';
		
		$show_parent = intval($this->_context->showParent) ? 1 : 0;
		
		$rows = $this->_model->cache();
		$array = array();
		foreach($rows as $val){
			if(isset($parent_id)){
				if($val['id'] == $parent_id){
					$parent = $val;
				}
				
				if($val['parent_id'] != $parent_id){
					continue;
				}
			}elseif(isset($val['parent_id'])){
				if($val['parent_id'] != 0){
					continue;
				}
			}
			$array[] = array(
					'id' => $val['id'],
					'text' => $val['name'],
					'state' => $state 
			);
		}
		if(isset($parent_id)){
			if($show_parent){
				echo json_encode(array(
						0 => array(
								'id' => $parent['id'],
								'text' => $parent['name'],
								'parent_id' => $parent['parent_id'],
								'children' => $array 
						) 
				));
			}else{
				echo json_encode($array);
			}
		}else{
			echo json_encode(array(
					0 => array(
							'id' => 0,
							'text' => '全部',
							'parent_id' => 0,
							'children' => $array 
					) 
			));
		}
		exit();
	}

	function actionAjaxGet(){

		$id = intval($this->_context->{$this->_model->idName()});
		
		// 获得记录
		$where = array(
				$this->_model->idName() => $id 
		);
		$row = $this->_model->find($where)
			->query();
		
		echo json_encode(array(
				0 => array(
						'id' => 0,
						'text' => '全部',
						'parent_id' => 0,
						'children' => array(
								0 => array(
										'id' => $row->id,
										'text' => $row->name 
								) 
						) 
				) 
		));
		exit();
	}

	/**
	 * 获得控制器使用的模型
	 */
	protected function _get_model(){

		if(empty($this->_model_name)){
			return false;
		}
		$model = new $this->_model_name();
		if(!is_object($model)){
			return false;
		}else{
			return $model;
		}
	}

	/**
	 * 获得控制器使用的表单
	 */
	protected function _get_form(){

		if(empty($this->_form_name)){
			return false;
		}
		$form = new $this->_form_name("");
		
		if(!is_object($form)){
			return false;
		}else{
			return $form;
		}
	}

	private function checkModel(){

		if(!is_object($this->_model)){
			return $this->_error('编辑失败', '没有指定编辑对象', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/index'));
		}
	}

	private function checkForm(){

		if(!is_object($this->_form)){
			return $this->_error('编辑失败', '没有指定表单', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/index'));
		}
	}

	/**
	 */
	function cloneRow($post_key){

		return $this->_error('操作失败', '暂时不允许重复提交', url($this->_context->submodule_name . '::' . $this->_context->controller_name . '/index', $this->_context->get()));
	}

	protected function _error($title, $message, $url = ''){

		if($this->_context->_ajax || $this->_context->isAJAX()){
			echo json_encode(array(
					'status' => false,
					'title' => $title,
					'message' => $message 
			));
			exit();
		}else{
			$url = empty($url) ? url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/" . $this->_context->action_name, $this->_context->get()) : $url;
			return $this->_redirectMessage($title, $message, $url);
		}
	}

	protected function _success($title, $message, $url = '', $data = array()){

		if($this->_context->_ajax || $this->_context->isAJAX()){
			
			$return = array(
					'status' => true,
					'title' => $title,
					'message' => $message,
					'url' => $url 
			);
			
			echo json_encode(array_merge($return, $data));
			exit();
		}else{
			$url = empty($url) ? url($this->_context->submodule_name . '::' . $this->_context->controller_name . "/" . $this->_context->action_name, $this->_context->get()) : $url;
			return $this->_redirect($url);
			// return $this->_redirectMessage($title, $message, $url);
		}
	}

	protected function _assign($name, $value = null){

		if(is_array($name)){
			foreach($name as $key => $val){
				$this->_assign($key, $val);
			}
		}else{
			$this->_view[$name] = $value;
		}
	}

	protected function _form_invalidate_message(){
		
		// 获得模型及关联模型的属性
		$props = $this->_model->meta()->props;
		
		$error = array();
		/**
		 * 处理特殊元素
		 * static：重设置记录的值
		 * rradio: 查询出当前选中项的关联记录
		 * many_to_many：查询出当前选中项的关联记录
		 */
		foreach($this->_form->elements() as $key => $val){
			/*
			 * 处理表单验证通不过的字段，供ajax_form用
			 */
			if(is_array($val->errorMsg()) && count($val->errorMsg())){
				$error[$key] = nl2br(h(implode("，", $val->errorMsg())));
			}
			if(!array_key_exists($key, $props)){
				continue;
			}
			if($val->_ui == 'static'){
				// 这里存在问题，如果$key是从表的字段，就会错误，解决方案：对static的表单元素添加一个hidden域
				$this->_form[$key]->value = $row->{$key};
				$this->_form[$key]->caption = $row->{$key};
			}elseif(isset($props[$key]) && isset($props[$key]['assoc']) && $props[$key]['assoc'] == 'many_to_many' && $val->_ui != 'textbox'){
				$objName = $props[$key]['assoc_class'];
				$obj = new $objName();
				$this->_form[$key]->value = $obj->find(array(
						$obj->idName() => $this->_form[$key]->value 
				))
					->getAll();
			}
		}
		unset($obj);
		return $error;
	}

	/**
	 * 返回当前最后一条SQL语句
	 *
	 * @author sqlhost 2013-4-12
	 */
	protected function _getLastSql(){

		return QDB::getConn()->getLastSql();
	}

	/**
	 * 返回当前所有的SQL语句
	 */
	protected function _getSql(){

		return QDB::getConn()->getSql();
	}

	protected function _before_index(){

	}

	protected function _after_index(){

	}

	protected function _before_update(QDB_ActiveRecord_Abstract $row){

	}

	protected function _after_update(QDB_ActiveRecord_Abstract $row){

	}

	protected function _before_create(){

	}

	protected function _after_create(QDB_ActiveRecord_Abstract $row){

	}

	protected function _before_delete(){

	}

	protected function _after_delete(){

	}

	protected function _before_recycle(){

	}

	protected function _after_recycle(){

	}

	protected function _before_restore(){

	}

	protected function _after_restore(){

	}

	protected function _before_modify(){

	}

	protected function _after_modify(){

	}

	protected function _before_verify(){

	}

	protected function _after_verify(){

	}

	protected function _before_finder(){

	}

	protected function _after_finder($rows){

	}

	protected function _after_import(QDB_ActiveRecord_Abstract $row){

	}

	protected function _after_clone(QDB_ActiveRecord_Abstract $row){

	}

	/**
	 * 创建系统日志
	 * $action 操作模型
	 * $describe 操作内容
	 */
	protected function createlog($action = null, $describe = null){

		$log = new Syslog();
		if(!$this->_user->uid){
			$data = $this->_app->currentUser();
			$user = User::find('uid = ?', $data['id'])->query();
			$this->_user = $user;
		}
		$log->create_uid = $this->_user->uid;
		$log->name = $this->_user->name;
		$log->username = $this->_user->username;
		
		$log->ip = IP;
		$log->action = $action;
		$log->describe = $describe;
		$log->created = time();
		
		$log->save();
	}
}
