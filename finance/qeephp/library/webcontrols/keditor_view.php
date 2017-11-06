<textarea id="keditor-<?php echo $id;?>" name="<?php echo $name;?>" style="width:<?php echo $width;?>;height:<?php echo $height;?>;">
<?php echo htmlspecialchars($value, ENT_QUOTES);?>
</textarea>
<script type="text/javascript">
var editor_<?php echo $id;?>;
	editor_<?php echo $id;?> = KindEditor.create('#keditor-<?php echo $id;?>', {
		uploadJson : '<?php echo url("plugin::keditor/upload");?>'
		,fileManagerJson : '<?php echo url("plugin::keditor/manager");?>'
		,allowFileManager : true
		,items : <?php echo $items; ?>
		,extraFileUploadParams : {"post_key" : "<?php echo $post_key; ?>", 
			"model":"<?php echo $model;?>", 
			"field":"<?php echo $field;?>", 
			"replace":"<?php echo $replace;?>"}
		//,fileSizeLimit: '<?php echo Q::ini('app_settings/file_upload_limit/value');?>'
		//,fileTypes: '<?php echo Helper_Filesys::getFileOpenDialogExt(Q::ini('app_settings/file_allowed_ext/value'));?>'
		//,flashSizeLimit: '<?php echo Q::ini('app_settings/flash_upload_limit/value');?>'
		//,flashTypes: '<?php echo Helper_Filesys::getFileOpenDialogExt(Q::ini('app_settings/flash_allowed_ext/value'));?>'
		,imageSizeLimit: '<?php echo Q::ini('app_settings/image_upload_limit/value');?>'
		,imageFileTypes: '<?php echo Helper_Filesys::getFileOpenDialogExt(Q::ini('app_settings/image_allowed_ext/value'));?>'
		//,mediaSizeLimit: '<?php echo Q::ini('app_settings/media_upload_limit/value');?>'
		//,mediaTypes: '<?php echo Helper_Filesys::getFileOpenDialogExt(Q::ini('app_settings/media_allowed_ext/value'));?>'
		/*
		 * 编辑创建成功后事件
		,afterCreate : function() {
			var self = this;
			$.ctrl(document, 13, function() {
				self.sync();
				K('form[name=example]')[0].submit();
			});
			$.ctrl(self.edit.doc, 13, function() {
				self.sync();
				K('form[name=example]')[0].submit();
			});
		}
		*/
	});
	prettyPrint();

</script>