<?php

class Control_CKEditor extends QUI_Control_Abstract
{
	function render()
	{
		global $root_dir;
		$base_dir = 'js/';
		$base_dir = h(rtrim($base_dir, '/\\') . '/');
		$width = $this->get('width', "100%");
		$height = $this->get('height', "250px");
		$toolbar = $this->get('toolbar', "Basic");
		$skin = $this->get('skin', "v2");
		$id = $this->id();
		$value = $this->get('value', '');
		$cols = $this->get('cols', '60');
		$rows = $this->get('rows', '8');
		$showHtml = $this->get('showHtml', '1');
		
		$out = Q::control('memo', $this->id(), array('value'=>$value, 'cols' => $cols, 'rows' => $rows))->render();
		
		include_once $base_dir.'ckeditor/ckeditor.php';
		include_once $base_dir.'ckfinder/ckfinder.php';


		$ckeditor = new CKEditor();
		$ckeditor->returnOutput = true;
		$ckeditor->config['showHtml'] = $showHtml;
		$ckeditor->config['skin'] = $skin;
		$ckeditor->config['width'] = $width;
		$ckeditor->config['height'] = $height;
		$ckeditor->config['toolbar'] = $toolbar;
		CKFinder::SetupCKEditor($ckeditor, $base_dir.'ckfinder/');
		$content = $value;
		$out .= $ckeditor->editor('content', htmlentities($content));
		
		return $out;
	}
}
		
		
/* 

			

		$out .= <<<EOT

<script type="text/javascript" src="{$base_dir}ckeditor/ckeditor.js"></script>
<script type="text/javascript">
	var editor = CKEDITOR.replace( '{$id}', {
		skin : '{$skin}',
		toolbar : '{$toolbar}',
		height : '{$height}',
		width : '{$width}',
		filebrowserBrowseUrl : '{$base_dir}ckfinder/ckfinder.html',
		filebrowserImageBrowseUrl : '{$base_dir}ckfinder/ckfinder.html?Type=Images',
		filebrowserFlashBrowseUrl : '{$base_dir}ckfinder/ckfinder.html?Type=Flash',
		filebrowserUploadUrl : '{$base_dir}ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
		filebrowserImageUploadUrl : '{$base_dir}ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
		filebrowserFlashUploadUrl : '{$base_dir}ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash'
	});
</script>

EOT;

		return $out;
	}
}


*/