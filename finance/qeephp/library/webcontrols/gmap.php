<?php
/**
 * 谷歌地图控件
 * 
 * @author sqlhost
 * @version 1.0.0
 */
class Control_GMap extends QUI_Control_Abstract {
	function render() {
		$this->_view['id'] = $this->id();
		$this->_view['value'] = $this->value;
		$this->_view['size'] = $this->size;
		
		// 获得经纬度
		$coords = Helper_Array::toArray($this->value, ', ');
		$this->_view['coords'] = $coords;
		
		return $this->_fetchView(dirname(__FILE__) . '/gmap_view');
	}
}


