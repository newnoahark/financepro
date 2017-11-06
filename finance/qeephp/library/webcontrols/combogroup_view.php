<div>
	<div style="line-height: 26px;" id="names_<?php echo $id;?>">
		<label style="font-size:14px;">
			已选：
			<?php if(count($values) > 0):?>
				<?php 
					$allSelected = '';
					foreach ($values as $val){
						$allSelected .= $val->name . '+';
					}
					$allSelected = mb_substr($allSelected, 0, -1, 'UTF-8');
					echo $allSelected;
				?>
					
			<?php else:?>
				未选择
			<?php endif;?>
		</label>
	</div>
	<div>
		<a href="javascript:void(0);" class="btn btn-primary" id="buttom_<?php echo $id;?>" style="<?php echo $style;?>">
			点击选择
		</a>
	</div>
</div>
<div id="<?php echo $id;?>_values">
	<?php if(count($values) > 0):?>
		<?php foreach($values as $val):?>
			<input type="hidden" value="<?php echo $val->id?>" name="<?php echo $id;?>[]">
		<?php endforeach;?>
	<?php endif;?>
	<?php if(isset($comboParentId) && count($comboParentId) > 0):?>
		<?php foreach($comboParentId as $val):?>
			<input name="comboParentId[]" type="hidden" value="<?php echo $val;?>" >
		<?php endforeach;?>
	<?php endif;?>
</div>
<style>
.cur{background-color: #F2F2F2;}
@-webkit-keyframes rotate{
	from{-webkit-transform:rotate(0deg)}
	to{-webkit-transform:rotate(100deg)}
}
@-moz-keyframes rotate{
	from{-moz-transform:rotate(0deg)}
	to{-moz-transform:rotate(100deg)}
}
@-ms-keyframes rotate{
	from{-ms-transform:rotate(0deg)}
	to{-ms-transform:rotate(100deg)}
}
@-o-keyframes rotate{
	from{-o-transform:rotate(0deg)}
	to{-o-transform:rotate(100deg)}
}
#<?php echo $id;?>-selected-name-box label{
	animation: 0.2s linear 0s normal none 1 rotate;
}
</style>
<div id="block_<?php echo $id;?>" style="width: 100%; height:100%; position: absolute; left:0; top:0; z-index:999; display:none;">
	<div style="width: 100%; height:100%; position: absolute; background:#000;filter:alpha(Opacity=50);-moz-opacity:0.5;opacity: 0.5;">
	</div>
	<div style="position: fixed;">
		<div id="windows_<?php echo $id;?>" class="panel window messager-window" style="width:480px;overflow: visible;">
			<div class="panel-header panel-header-noborder window-header">
				<div class="panel-title" style="">请选择</div>
				<div class="panel-tool"><a href="javascript:void(0)" class="panel-tool-close" id="<?php echo $id;?>-tool-close"></a></div>
			</div>
			<div style="background:#fff; float: left;width: 100%; max-height: 240px; overflow-y: auto;">
				<div style="padding:2px 5px; display: table;">
					<div>已选择：</div>
					<div id="<?php echo $id;?>-selected-name-box" style="height:26px; line-height:26px;">
					</div>
				</div>
				<ul style="padding:0;">
					<?php foreach($parent_items as $key => $value):?>
						<li style="padding: 0 5px;width: 25%; list-style-type:none; float:left; height:35px; line-height:35px;">
							<div switch-value="<?php echo $key;?>" style="height:35px; line-height:35px;padding:0 5px;cursor: pointer;" id="<?php echo $id;?>-value-switch-open">
								<i class="fa fa-plus-square-o"></i> <?php echo $value;?>
							</div>
							<div style="display: none;" id="<?php echo $id;?>-value-box-<?php echo $key;?>" class="<?php echo $id;?>-value-box">
								<div style="height: 29px;line-height: 29px;border: 2px solid #989898; background-color: #fff; border-bottom: 0;padding:0 5px;
									cursor: pointer;" switch-value="<?php echo $key;?>" id="<?php echo $id;?>-value-switch-close">
									<i class="fa fa-minus-square-o"></i> <?php echo $value;?>
								</div>
								<div style="width:400px; border: 2px solid #989898;position:absolute; background-color: #fff; padding:5px 5px;">
									<?php foreach($items as $k => $val):?>
										<?php if($val[$upfield] == $key):?>
											<span style="display:block; float:left; cursor: pointer; height:26px; line-height:26px;margin-left:5px;">
												<input name="<?php echo $id;?>-selected-value" id="<?php echo $id;?>-input-value-<?php echo $val['id'];?>" type="<?php echo $type?>" 
												 parent_id="<?php echo $key;?>" value="<?php echo $val['id'];?>" cultureName="<?php echo $val['name'];?>"> 
												<label for="<?php echo $id;?>-input-value-<?php echo $val['id'];?>">
													<?php echo $val['name'];?>
												</label>
											</span>
											<?php unset($items[$k]);?>
										<?php endif;?>
									<?php endforeach;?>
								</div>
								<div style="display: inline; position: fixed; height:2px; width:103px; background-color: #fff;margin-left:2px;"></div>
							</div>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
			<div style="padding: 10px 0 0 0;text-align: center;float: left; width: 100%;">
				<a href="javascript:void(0);" class="btn btn-primary" id="button-<?php echo $id;?>-save">确认</a>
				<a href="javascript:void(0);" class="btn btn-primary" id="button-<?php echo $id;?>-cancel">取消</a>
			</div>
		</div>
	</div>
</div>
<script>
var fieldName = "<?php echo $id?>[]";
$(document).ready(function () {
	if(typeof($("#body_<?php echo $id;?>").html()) == "undefined"){
		var bodyBlock = "<div id=\"body_<?php echo $id;?>\"></div>";
		$("body").append(bodyBlock);
	}
	var html = $("#block_<?php echo $id;?>");
	$("#body_<?php echo $id;?>").html(html);
	
	$("#buttom_<?php echo $id;?>").click(function (){
		initBox();
	});

	$("div[id='<?php echo $id;?>-value-switch-open']").click(function () {
		var boxValue = $(this).attr("switch-value");
		$("div[id='<?php echo $id;?>-value-switch-open']").show();
		$(this).hide();
		$(".<?php echo $id;?>-value-box").hide();
		$("#<?php echo $id;?>-value-box-"+boxValue).show();
	});

	$("div[id='<?php echo $id;?>-value-switch-open']").mouseover(function () {
		$(this).addClass("cur");
	});

	$("div[id='<?php echo $id;?>-value-switch-open']").mouseout(function () {
		$(this).removeClass("cur");
	});
	$(".<?php echo $id;?>-value-box span").mouseover(function () {
		$(this).addClass("cur");
	});

	$(".<?php echo $id;?>-value-box span").mouseout(function () {
		$(this).removeClass("cur");
	});

	$(".<?php echo $id;?>-value-box").mouseleave(function () {
		$("div[id='<?php echo $id;?>-value-switch-open']").show();
		$(this).hide();
	});

	$("div[id='<?php echo $id;?>-value-switch-close']").click(function () {
		var boxValue = $(this).attr("switch-value");
		$("div[id='<?php echo $id;?>-value-switch-open']").show();
		$("#<?php echo $id;?>-value-box-"+boxValue).hide();
	});

	$("#<?php echo $id;?>-tool-close, #button-<?php echo $id;?>-cancel").click(function () {
		closeBox();
	});

	$("#button-<?php echo $id;?>-save").click(function () {
		saveValue();
	});

	$("html").resize(function () {
		initPosition();
	});

	$("#<?php echo $id;?>-selected-name-box").on("mouseenter", "span", function (){
		$(this).find("label").show();
	});

	$("#<?php echo $id;?>-selected-name-box").on("mouseleave", "span", function (){
		$(this).find("label").hide();
	});
	
	$("#<?php echo $id;?>-selected-name-box").on("mouseenter", "span i", function (){
		$(this).css("color", "#f00");
	});

	$("#<?php echo $id;?>-selected-name-box").on("mouseleave", "span i", function (){
		$(this).css("color", "");
	});

	$("#<?php echo $id;?>-selected-name-box").on("click", "span i", function (){
		var id = $(this).parents("label").attr("for");
		$("#"+id).removeAttr("checked");
		$(this).parents("span").remove();
	});

	$("input[name='<?php echo $id;?>-selected-value']").click(function () {
		var nameBoxStart = '<span style="padding:0 8px 0 0; color:#494a49; display:block;float:left;cursor:pointer; height:26px; line-height:26px;">';
		var nameBoxEnd = '<label for="%" style="display: none;cursor:pointer;"><i class="fa fa-times" title="删除"></i></label></span>';
		var nameBoxInsert = "";
		var replacement = "<?php echo $id;?>-input-value-";
		var type = "<?php echo $type?>";
		switch(type){
			case "checkbox":
				if($(this).prop("checked")){
					replacement += $(this).val();
					nameBoxEnd = nameBoxEnd.replace("%", replacement);
					nameBoxInsert = nameBoxStart + $(this).attr("cultureName") + nameBoxEnd;
					$("#<?php echo $id;?>-selected-name-box").append(nameBoxInsert);
				} else {
					var id = $(this).attr("id");
					$("#<?php echo $id;?>-selected-name-box label[for='"+ id +"']").parents("span").remove();
				}
				break;
			case "radio":
				$("div[id='<?php echo $id;?>-selected-name-box']").html("");
				replacement += $(this).val();
				nameBoxEnd = nameBoxEnd.replace("%", replacement);
				nameBoxInsert = nameBoxStart + $(this).attr("cultureName") + nameBoxEnd;
				$("#<?php echo $id;?>-selected-name-box").append(nameBoxInsert);
				break;
		}
	});
});
function initBox(){
	initPosition();
	var values = $("input[name='<?php echo $id;?>[]']");
	var valueBoxInput = "#<?php echo $id;?>-input-value-";
	values.each(function (){
		$(valueBoxInput + $(this).val()).prop("checked", true);
	});
	initNameBox();
	$("div[id='<?php echo $id;?>-value-switch-open']").show();
	$(".<?php echo $id;?>-value-box").hide();
	$("#block_<?php echo $id;?>").show();
}

function closeBox(){
	$("input[name='<?php echo $id;?>-selected-value']:checked").removeAttr("checked");
	$("#block_<?php echo $id;?>").hide();
}

function saveValue(){
	var values = $("input[name='<?php echo $id;?>-selected-value']:checked");
	var start = " <input name=\"" + fieldName + "\" type=\"hidden\" value=\"";
	var end = "\" /> ";
	var insert = "";
	var cultureName = '';
	var parentIds = [];
	values.each(function (){
		insert += start + $(this).val() + end;
		cultureName += $(this).attr("cultureName") + "+";
		var parentId = $(this).attr("parent_id");
		if(jQuery.inArray(parentId, parentIds) == -1){
			parentIds.push(parentId);
		}
		
	});
	var html = "";
	if(parentIds.length > 0){
		for(i=0,j=parentIds.length; i<j; i++) {
			html += '<input name="comboParentId[]" type="hidden" value="' + parentIds[i] + '" >';
		};
	}
	insert += html;
	cultureName = cultureName.substr(0, (cultureName.length-1));
	$("#names_<?php echo $id;?> label").html(cultureName);
	$("#<?php echo $id;?>_values").html(insert);
	$("#block_<?php echo $id;?>").hide();
}

function initPosition(){
	var control_width = $("#windows_<?php echo $id;?>").width();
	var windows_width = $("html").width();
	var left = (windows_width - control_width) / 2;
	$("#windows_<?php echo $id;?>").css("left", left+"px");
	$("#windows_<?php echo $id;?>").css("top", "100px");
}

function initNameBox(){
	var values = $("input[name='<?php echo $id;?>-selected-value']:checked");
	var nameBoxStart = '<span style="padding:0 8px 0 0; color:#494a49; display:block;float:left;cursor:pointer; height:26px; line-height:26px;">';
	var nameBoxEnd = '<label for="%" style="display: none;cursor:pointer;"><i class="fa fa-times" title="删除"></i></label></span>';
	var nameBoxInsert = "";
	var replacement = "<?php echo $id;?>-input-value-";
	values.each(function (){
		var replace = replacement + $(this).val();
		var endstr = nameBoxEnd.replace("%", replace);
		nameBoxInsert += nameBoxStart + $(this).attr("cultureName") + endstr;
	});
	$("#<?php echo $id;?>-selected-name-box").html(nameBoxInsert);
}
</script>