<?php
class Control_Dialog extends QUI_Control_Abstract {
	function render() {
		$id = $this->id();
		$this->_view['id'] = $id;
		return $this->_fetchView ( dirname ( __FILE__ ) . '/dialog_view' );
	}
}
?>