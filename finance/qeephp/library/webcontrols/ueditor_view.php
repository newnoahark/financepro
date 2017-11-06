<?php if ($loadjs):?>
<link type="text/css" href="ueditor/themes/default/ueditor.css" rel="stylesheet">
<script type="text/javascript" src="ueditor/editor_config.js"></script>
<script type="text/javascript" src="ueditor/editor_all.js"></script>
<?php endif;?>
<script type="text/plain" id="ueditor_<?php echo $id;?>">
<?php echo $value;?>
</script>
<script type="text/javascript">
var ueditor_<?php echo $id;?>;
jQuery(function($) {
	ueditor_<?php echo $id;?> = new baidu.editor.ui.Editor({
		textarea:'<?php echo $id;?>'
		, allHtmlEnabled:false
		<?php if ($height):?>
		, minFrameHeight:'<?php echo $height;?>'
		<?php endif;?>
		<?php if ($toolbars):?>
		, toolbars:<?php echo $toolbars;?>
		<?php endif;?>
		, model: '<?php echo $model;?>'
		, field: '<?php echo $field;?>'
		, target: '<?php echo $target;?>'
		<?php if ($post_key):?>
		, post_key: '<?php echo $post_key;?>'
		<?php endif;?>
	});
	ueditor_<?php echo $id;?>.render("ueditor_<?php echo $id;?>");
	//ueditor_<?php echo $id;?>.addListener("ready", function() {
	//	ueditor_<?php echo $id;?>.setContent('');
	//});
});
</script>