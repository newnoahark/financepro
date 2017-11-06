<?php if ($loadjs):?>
<link type="text/css" href="js/plugins/swfupload/images/default.css" rel="stylesheet">
<script type="text/javascript" src="js/plugins/swfupload/js/swfupload.js"></script>
<script type="text/javascript" src="js/plugins/swfupload/js/handlers.js"></script>
<?php endif;?>
<script type="text/javascript">
var swfu;
jQuery(function($) {
	swfu = new SWFUpload({
		// Backend Settings
		upload_url: "index.php?submodule=plugin&controller=swfupload&action=upload",

		// File Upload Settings
		post_params: {"post_key" : "<?php echo $post_key; ?>", 
			"model":"<?php echo $model;?>", 
			"field":"<?php echo $field;?>", 
			"replace":"<?php echo $replace;?>",
			"target":"<?php echo $target;?>"},
		file_size_limit : "<?php echo $max_size;?>",
		file_types : "<?php echo $file_ext;?>",
		file_types_description : "<?php echo $file_desc;?>",
		file_upload_limit : <?php echo $file_limit;?>,

		// Event Handler Settings - these functions as defined in Handlers.js
		//  The handlers are not part of SWFUpload but are part of my website and control how
		//  my website reacts to the SWFUpload events.
		swfupload_preload_handler : preLoad,
		swfupload_load_failed_handler : loadFailed,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,

		// Button Settings
		button_image_url : "images/SmallSpyGlassWithTransperancy_17x18.png",
		button_placeholder_id : "spanButtonPlaceholder",
		button_width: 180,
		button_height: 18,
		button_text : '<span class="button">Select Images <span class="buttonSmall">(2 MB Max)</span></span>',
		button_text_style : '.button { font-family: Helvetica, Arial, sans-serif; font-size: 12pt; } .buttonSmall { font-size: 10pt; }',
		button_text_top_padding: 0,
		button_text_left_padding: 18,
		button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
		button_cursor: SWFUpload.CURSOR.HAND,
		
		// Flash Settings
		flash_url : "js/plugins/swfupload/swfupload.swf",
		flash9_url : "js/plugins/swfupload/swfupload_fp9.swf",

		custom_settings : {
			upload_target : "divFileProgressContainer",
			thumbnail_height: 400,
			thumbnail_width: 400,
			thumbnail_quality: 100
		},
		
		// Debug Settings
		debug: false
	});
});
</script>

<div id="wrap_<?php echo $id;?>" class=<?php echo $id;?>>
	<div
		style="width: 180px; height: 18px; border: solid 1px #7FAAFF; background-color: #C5D9FF; padding: 2px;">
		<span id="spanButtonPlaceholder"></span>
	</div>
	<div id="divFileProgressContainer"></div>
	<div id="thumbnails"></div>
</div>
