<div id="wrap_<?php echo $id;?>" class=<?php echo $id;?>>
	<input type="button" id="button_<?php echo $id;?>"
		value="<?php echo $caption;?>" />
	<?php foreach ($value as $val): ?>
	<input id="<?php echo $id;?>_<?php echo $val->id;?>" type="checkbox"
		name="<?php echo $id;?>[]" value="<?php echo $val->id;?>" checked="checked">
	<label for="<?php echo $id;?>_<?php echo $val->id;?>"><?php echo $val->name;?></label>
	<?php endforeach;?>
</div>
<div id="dialog_<?php echo $id;?>" class="rcheckbox_dialog"
	title="<?php echo $caption;?>">
	<ul>
	<?php foreach ($items as $item):?>
		<li><img src="images/admin/add.gif" /><input type="checkbox"
			id="rcheckbox_<?php echo $id;?>_<?php echo $item['id'];?>"
			value="<?php echo $item['id']?>" name="rcheckbox_<?php echo $id;?>"
			<?php if (in_array($item['id'], $selected)) echo " checked";?> /><label
			for="rcheckbox_<?php echo $id;?>_<?php echo $item['id'];?>"><?php echo $item['name'];?></label></li>
	<?php endforeach;?>
	</ul>
</div>
<style type="text/css">
<!--
.rcheckbox_dialog ul {
	width: 100%;
}

.rcheckbox_dialog li {
	display: inline;
	float: left;
	margin-left: 10px;
}

.rcheckbox_dialog li img {
	cursor: pointer;
}
-->
</style>
<script type="text/javascript">
jQuery(function($) {
	/* 定义rcheckbox ui dialog */
	$( "#dialog_<?php echo $id;?>" ).dialog({
		autoOpen: false,
		height: 380,
		width: 500,
		modal: false,
		resizable: true,
		buttons: {
			"关闭": function() {
				$( this ).dialog( "close" );
			}
		}
	});

	/* 设置rcheckbox点击事件 */
	$("#button_<?php echo $id;?>").click(function(){
		$( "#dialog_<?php echo $id;?>" ).dialog('open');
	});

	/* rcheckbox列表项的点击事件*/
	$("#dialog_<?php echo $id;?>").on("click", "li input:checkbox", function() {
		var value = $(this).val();
		var check = $(this).get(0).checked;
		var label = $(this).next("label").text();
		var found = false;

		$("#wrap_<?php echo $id;?> :checkbox").each(function() {
			if ($(this).val() == value) {
				if (!check) {
					$(this).next("label").remove();
					$(this).remove();
				}
				found = true;
			}
		});

		if (check && !found) {
			$("<input type=\"checkbox\" />").appendTo("#wrap_<?php echo $id;?>")
					.attr('name', '<?php echo $id;?>[]')
					.attr('value', value)
					.attr('checked', true)
					.attr('id', '<?php echo $id;?>_' + value)
					.after('<label for="<?php echo $id?>_' + value + '">' + label + '</label>');
		}
	});

	/* rcheckbox列表项的图片的点击事件 */
	$("#dialog_<?php echo $id;?>").on("click", "li img", function() {
		if ($(this).attr('src') == 'images/admin/add.gif') {
			var _this = $(this);
			$.get("index.php", {
				"submodule" : "default",
				"controller" : "ajaxs",
				"action" : "getRcheckbox",
				"id" : '<?php echo $id;?>',
				"root_id" : $(this).next('input:checkbox').val(),
				"model" : '<?php echo $model;?>',
				"value": "<?php echo implode(',', $selected);?>"
			}, function(data) {
				if (data.length > 0) {
					_this.parent().append(data);
					if (_this.parent().prev('li').length > 0) {
						$("<ul class=\"cl\" />").insertAfter(_this.parent().parent()).append(_this.parent());
					}
					else if (_this.parent().nextAll().length > 0) {
						$("<ul class=\"cl\" />").insertAfter(_this.parent().parent()).append(_this.parent().nextAll());
					}
					_this.attr('src', 'images/admin/desc.gif');
				}
			});
		}
		else {
			var obj = $(this).parent().parent();

			$(this).nextAll('ul').remove();
			$(this).attr('src', 'images/admin/add.gif');

			var found = false;

			obj.prevAll().each(function() {
				if ($(this).find("ul").length == 0) {
					$(this).append(obj.children("li"));
					obj.remove();
					found = true;
				}
			});

			if (! found) {
				obj.nextAll().each(function() {
					if ($(this).find("ul").length == 0) {
						$(this).prepend(obj.children("li"));
						obj.remove();
						found = true;
					}
				});
			}
			
		}
	});
});

</script>