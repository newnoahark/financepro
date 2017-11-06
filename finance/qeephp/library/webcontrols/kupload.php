<?php

/**
 *
 * @param
 *        $dir
 * @param string $type
 *        file, flash, media, image
 * @param boolen $manager
 *        是否显示文件管理空间
 * @param string $style
 *        button: 点击按钮弹出文件选择窗口
 *        local: 本地上传
 *        remote: 远程文件
 *        double: 本地上传 + 远程文件
 * @param boolen $multi
 *        是否是多图片上传
 * @param string $model
 *        指明是属于哪个模型的
 * @param string $target
 *        指明保存到哪个模型
 * @param string $field
 *        指明是属于哪个字段的
 * @param boolen $replace
 *        指明是否替换原记录
 */
class Control_Kupload extends QUI_Control_Abstract {

	function render() {

		$this->_view['id'] = $this->id;
		$this->_view['value'] = $this->value;
		$this->_view['name'] = $this->name;
		$this->_view['size'] = $this->size;
		$this->_view['type'] = isset($this->type) ? $this->type : 'image';
		$this->_view['style'] = isset($this->style) ? $this->style : 'local';
		$this->_view['manager'] = isset($this->manager) ? $this->manager : false;
		$this->_view['multi'] = isset($this->multi) ? $this->multi : false;
		$this->_view['sizeLimit'] = Q::ini('app_settings/' . $this->type . '_upload_limit/value');
		$this->_view['fileTypes'] = Helper_Filesys::getFileOpenDialogExt(Q::ini('app_settings/' . $this->type . '_allowed_ext/value'));
		$plugin = '';
		if ($this->type == 'file') {
			$plugin = 'insertfile';
			$dialog = 'fileDialog';
		}
		elseif ($this->type == 'flash') {
			$plugin = 'flash';
			$dialog = 'flash';
		}
		elseif ($this->type == 'media') {
			$plugin = 'media';
			$dialog = 'media';
		}
		else {
			if ($this->multi) {
				$plugin = 'multiimage';
				$dialog = 'multiImageDialog';
			}
			else {
				$plugin = 'image';
				$dialog = 'imageDialog';
			}
		}
		if ($this->style == 'manager') {
			$plugin = 'filemanager';
			$dialog = 'filemanagerDialog';
		}
		$this->_view['dialog'] = $dialog;
		$this->_view['plugin'] = $plugin;
		
		/**
		 * @since 2015-02-05
		 * @author yubb
		 * @param width and height
		 */
		
		$this->_view['width'] = $this->width;
		$this->_view['height'] = $this->height;
		
		$this->_view['post_key'] = isset($_SESSION['post_key']) ? $_SESSION['post_key'] : '';
		$this->_view['model'] = isset($this->model) ? $this->model : '';
		$this->_view['field'] = isset($this->field) ? $this->field : '';
		$this->_view['replace'] = isset($this->replace) ? $this->replace : '';
		$this->_view['temporary'] = isset($this->temporary) ? $this->temporary : false;
		
		return $this->_fetchView(dirname(__FILE__) . '/kupload_view');
	}
}
		