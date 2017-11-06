<?php
/**
 * 谷歌地图控件
 *
 * @author sqlhost
 * @version 1.0.0
 */
class Control_Swfupload extends QUI_Control_Abstract {
	/**
	 * (non-PHPdoc)
	 *
	 * @see QUI_Control_Abstract::render()
	 *
	 * @param
	 * id: 控件ID
	 * caption: 控件标题
	 * value: 控件的值
	 * size: 控件的宽度
	 * file_ext: 允许的扩展名
	 * max_size: 允许上传的最大容量
	 * file_limit: 同时允许上传的文件数
	 * post_key: 主题的post_key
	 * 
	 */
	function render() {
		$this->_view['id'] = $this->id();
		$this->_view['caption'] = $this->_label;
		$this->_view['value'] = $this->value;
		$this->_view['size'] = $this->size;
		$this->_view['file_ext'] = $this->file_ext;
		$this->_view['file_desc'] = $this->file_desc;
		$this->_view['max_size'] = $this->max_size;
		$this->_view['file_limit'] = $this->file_limit ? $this->file_limit : 0;
		$this->_view['post_key'] = isset($_SESSION['post_key']) ? $_SESSION['post_key'] : '';
		$this->_view['model'] = $this->model;
		$this->_view['field'] = $this->field;
		$this->_view['replace'] = $this->replace;
		$this->_view['target'] = $this->target ? $this->target : 'Photo';
		$this->_view['loadjs'] = $this->loadjs;
		
		return $this->_fetchView(dirname(__FILE__) . '/swfupload_view');
	}
}


