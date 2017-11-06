<?php if ($value):?><img src="<?php echo $value; ?>"
	width="<?php echo ($width)?$width:'200';?>"
	height="<?php echo ($height)?$height:'200';?>"
	id="kupload_image_<?php echo $id;?>">
<br><?php endif;?><input size="<?php echo $size;?>" type="text"
	id="<?php echo $id;?>" name="<?php echo $name;?>"
	value="<?php echo $value;?>" />
<a id="kupload_<?php echo $id;?>" class="easyui-linkbutton"
	data-options="plain:true,iconCls:'icon-add'">上传</a>
<script type="text/javascript">
<?php if ($style == 'button'):?>
var uploadbutton = KindEditor.uploadbutton({
	button : KindEditor('#kupload_<?php echo $id;?>')[0],
	fieldName : 'imgFile',
	url : '<?php echo url("plugin::keditor/upload");?>',
	extraParams : {"post_key" : "<?php echo $post_key; ?>",
		"model":"<?php echo $model;?>", 
		"field":"<?php echo $field;?>", 
		"replace":"<?php echo $replace;?>",
		"temporary":"<?php echo $temporary;?>"},
	afterUpload : function(data) {
		if (data.error === 0) {
			var url = KindEditor.formatUrl(data.url, 'absolute');
			KindEditor('#<?php echo $id;?>').val(url);
			KindEditor('#kupload_image_<?php echo $id;?>').attr('src',url);
		} else {
			alert(data.message);
		}
	},
	afterError : function(str) {
		alert('自定义错误信息: ' + str);
	}
});
uploadbutton.fileBox.change(function(e) {
	uploadbutton.submit();
});
<?php else:?>
var editor_<?php echo $id;?> = KindEditor.editor({
	allowFileManager : <?php echo $manager ? 'true' : 'false';?>
	,uploadJson : '<?php echo url("plugin::keditor/upload");?>'
	,fileManagerJson : '<?php echo url("plugin::keditor/manager");?>'
	,extraFileUploadParams : {"post_key" : "<?php echo $post_key; ?>", 
		"model":"<?php echo $model;?>", 
		"field":"<?php echo $field;?>", 
		"replace":"<?php echo $replace;?>",
		"temporary":"<?php echo $temporary;?>"},
	<?php if ($multi && false):?>
	,imageSizeLimit:"<?php echo $sizeLimit;?>"
	,imageFileTypes: '<?php echo $fileTypes;?>'
	<?php endif;?>
});
KindEditor('#kupload_<?php echo $id;?>').click(function() {
	editor_<?php echo $id;?>.loadPlugin('<?php echo $plugin;?>', function() {
		editor_<?php echo $id;?>.plugin.<?php echo $dialog;?>({
			clickFn : function(<?php if (! $multi):?>url, title<?php else:?>urlList<?php endif;?><?php if ($type == 'image' && ! $multi):?>, width, height, border, align<?php endif;?>) {
				<?php if (! $multi):?>
				KindEditor('#<?php echo $id;?>').val(url);
				KindEditor('#kupload_image_<?php echo $id;?>').attr('src',url);
				<?php else:?>
				alert(urlList && ! $multi);
				<?php endif;?>
				editor_<?php echo $id;?>.hideDialog();
			}
			// 将原地址设置到上传文件窗口的文本框中
			<?php if (! $multi):?>
			,<?php echo $type;?>Url : KindEditor('#<?php echo $id;?>').val()
			<?php endif; ?>
			<?php if ($manager && ! $multi):?>
			// 文件管理空间排列方式
			,viewType : 'VIEW'
			// 保存路径
			,dirName : '<?php echo $type;?>'
			<?php endif;?>
			// 远程文件
			<?php if ($style == 'remote' && ! $multi):?>
			,showRemote : true
			,showLocal : false
			<?php endif;?>
			// 本地上传
			<?php if ($style == 'local' && ! $multi):?>
			,showRemote : false
			,showLocal : true
			<?php endif;?>
			// 远程+本地
			<?php if ($style == 'double' && ! $multi):?>
			,showRemote : true
			,showLocal : true
			<?php endif;?>
		});
	});
});
<?php endif;?>
</script>