<style>
#<?php echo $id;?>-tree-value-box .btn{width:80px; float:left;}
#<?php echo $id;?>-tree-value-box #<?php echo $id;?> {padding:0 10px; float:left; height:32px; line-height:32px; border:1px solid #bdbdbd;
text-align:center;
}
#<?php echo $id;?>-tree-select-box {width:100%; height:100%; position: absolute;left:0; top:0; z-index:999;}
#<?php echo $id;?>-tree-select-box .mask{width: 100%; height:100%; position: absolute; background:#000;filter:alpha(Opacity=50);-moz-opacity:0.5;opacity: 0.5;}
#<?php echo $id;?>-tree-select-box .select-window{max-height: 800px; width:300px; position: absolute;}
#<?php echo $id;?>-tree-select-box .select-value-box{background:#fff; float: left;width: 100%; padding:10px 5px;max-height: 500px;overflow-y:scroll; }
#<?php echo $id;?>-tree-select-box ul{padding:0; margin-bottom: 0;}
#<?php echo $id;?>-tree-select-box li{list-style-type:none;cursor: pointer;}
#<?php echo $id;?>-tree-select-box .next{margin-left: 20px;}
#<?php echo $id;?>-tree-select-box .title{font-size:14px; height:22px; line-height:22px;padding-left:3px;}
#<?php echo $id;?>-tree-select-box .cur{background-color: #3276b1; color:#fff;}
</style>
<div id="<?php echo $id;?>-tree-value-box">
<div type="text" id="<?php echo $id;?>" name="<?php echo $id;?>" style="<?php echo $style;?>">
<?php if(strlen($currentName) > 0):?><?php echo $currentName;?><?php else:?>- 请选择 -<?php endif;?>
</div>
<div class="hidden" id="<?php echo $id;?>-tree-selected-values">
	<?php if(isset($value)):?>
		<input name="<?php echo $id;?>" value="<?php echo $value;?>">
	<?php endif;?>
</div>
<a class="btn btn-primary" style="height:32px; line-height:32px; margin-left:8px;" id="<?php echo $id?>-tree-open-select-box">请选择</a>	
</div>
<div style="clear: both;"></div>
<div id="<?php echo $id;?>-tree-select-box" style="display:none;">
	<div class="mask"></div>
	<div style="position: fixed;">
		<div class="select-window panel window messager-window" id="<?php echo $id;?>-tree-windows">
			<div class="panel-header panel-header-noborder window-header">
				<div class="panel-title" style="">请选择</div>
				<div class="panel-tool"><a href="javascript:void(0)" class="panel-tool-close" id="<?php echo $id;?>-tool-close"></a></div>
			</div>
			<div class="select-value-box">
				<div>已选择：<span id="<?php echo $id;?>-tree-selected-name"></span></div>
				<ul>
					<?php foreach($items as $key=>$value):?>
						<li>
							<div class="title" title="<?php echo $value;?>" name="<?php echo $value;?>" level="<?php echo $key;?>" id="<?php echo $key;?>">
								<i class="fa fa-plus-square-o"></i><?php echo $value;?>
							</div>
							<div class="next">
							</div>
						</li>
					<?php endforeach;?>
				</ul>
			</div>
			<div style="padding: 10px 0 0 0;text-align: center;float: left; width: 100%;">
				<a href="javascript:void(0);" class="btn btn-primary" id="button-<?php echo $id;?>-tree-save">确认</a>
				<a href="javascript:void(0);" class="btn btn-primary" id="button-<?php echo $id;?>-tree-cancel">取消</a>
			</div>
		</div>
	</div>
</div>

<script>
var <?php echo $id;?>_NameSpace = window.NameSpace || {};
<?php echo $id;?>_NameSpace.func = new function() {
	var self = this;
	self.initTreeBox = function(){
		self.initTreePosition();
		$("#<?php echo $id;?>-tree-select-box").show();
	}

	self.initValue = function(){
		var selectedLevel = "<?php echo $values;?>";
		var next = '';
		var current = "";
		var tag = "";
		value = selectedLevel.split("-");
		if(value.length > 0){
			for(i=0,j=value.length-1; i<j; i++){
				next = $("#<?php echo $id;?>-tree-windows #"+value[i]).parent().find(".next");
				current = $("#<?php echo $id;?>-tree-windows #"+value[i]);
				current.addClass("show");
				if(i<j){
					current.parent().find("i").removeClass("fa-plus-square-o");
					current.parent().find("i").addClass("fa-minus-square-o");
				}
				var level = current.attr("level");
				var name = current.attr("name");
				$.ajax({
					type: "POST",
					url: "<?php echo url($url)?>", 
					data: "id="+value[i],
					async: false,
					success: function(data){
						if(data['status'] == 1){
							$.each(data['data'], function (i, val){
								html = '<li><div class="title" title="' + name + val + '" name="' + name + val + '" level="' + level + "-" + i + '" id="' + i + '">' + 
								'<i class="fa fa-plus-square-o"></i>' + val + '</div><div class="next"></div></li>';
								next.append(html);
							});
						}
				    },
				    dataType: 'json'
				});
			}
			$("#<?php echo $id;?>-tree-selected-name").html($("div[level='" + selectedLevel + "']").attr("name"));
			$("#<?php echo $id;?>-tree-windows div[level='" + selectedLevel + "']").addClass("cur");
		}
	}

	self.saveTreeBox = function(){
		if(typeof(level) != "undefined"){
			var level = $("#<?php echo $id;?>-tree-select-box .cur").attr("level");
			var values = level.split("-");
			var fillfield = "<?php echo $fillfield;?>";
			var fields = fillfield.split(",");
			var insertValues = "";
			for(i=0,j=fields.length; i<j; i++){
				insertValues += '<input type="hidden" name="' + fields[i] + '" value="' + values[i] + '" >';
				if(typeof(values[i])){
					$("#"+fields[i]).val(values[i]);
				} else {
					$("#"+fields[i]).val(0);
				}
			}
			$("#<?php echo $id;?>-tree-selected-values").html(insertValues);
			$("#<?php echo $id;?>").html($("#<?php echo $id;?>-tree-select-box .cur").attr("name"));
		} else {
			var currentSelect = $("#<?php echo $id;?>-tree-select-box .select-value-box .cur");
			var value = currentSelect.attr("id");
			var insertValues = '<input type="hidden" name="' + "<?php echo $id;?>" + '" value="' + value + '" >';

			$("#<?php echo $id;?>-tree-selected-values").html(insertValues);
			$("#<?php echo $id;?>").html(currentSelect.attr("name"));
		}
		$("#<?php echo $id;?>-tree-select-box").hide();
	}

	self.closeTreeBox = function(){
		var id = $("#<?php echo $id;?>-tree-selected-values input:last").val();
		if(typeof(id) != "undefined"){
			var currentValue = $("#<?php echo $id?>-tree-windows #"+id);
			var name = currentValue.attr("name");
			$("#<?php echo $id?>-tree-windows .cur").removeClass("cur");
			currentValue.addClass("cur");
			$("#<?php echo $id;?>-tree-selected-name").html(name);
		}
		$("#<?php echo $id;?>-tree-select-box").hide();
	}

	self.initTreePosition = function(){
		var control_width = $("#<?php echo $id;?>-tree-windows").width();
		var windows_width = $("html").width();
		var left = (windows_width - control_width) / 2;
		$("#<?php echo $id;?>-tree-windows").css("left", left+"px");
		$("#<?php echo $id;?>-tree-windows").css("top", "60px");
	}
};

$(document).ready(function() {
	if(typeof($("#body_tree_<?php echo $id;?>").html()) == "undefined"){
		var bodyBlock = "<div id=\"body_tree_<?php echo $id;?>\"></div>";
		$("body").append(bodyBlock);
	}
	var html = $("#<?php echo $id;?>-tree-select-box");
	<?php echo $id;?>_NameSpace.func.initValue();
	$("#body_tree_<?php echo $id;?>").html(html);

	$("#<?php echo $id;?>-tree-open-select-box").click(function() {
		<?php echo $id;?>_NameSpace.func.initTreeBox();
	});

	$("#button-<?php echo $id;?>-tree-save").click(function () {
		<?php echo $id;?>_NameSpace.func.saveTreeBox();
	});

	$("#button-<?php echo $id;?>-tree-cancel").click(function () {
		<?php echo $id;?>_NameSpace.func.closeTreeBox();
	});
	
	$("#<?php echo $id;?>-tree-select-box").on("mouseenter", " .title", function() {
		$(this).css("background-color", "#DDF0ED");
		$(this).css("color", "#000");
	});

	$("#<?php echo $id;?>-tree-select-box ").on("mouseleave", ".title", function() {
		$(this).css("background-color", "");
		$(this).css("color", "");
	});

	$("#<?php echo $id;?>-tree-select-box").on("click", " .title", function() {
		var url = "<?php echo url($url);?>";
		var id = $(this).attr("id");
		var html = "";
		var thisElement = $(this);
		var next = $(this).parent().find(".next:first");
		var tagI = $(this).children("i");
		var isPost = 0;
		var level = $(this).attr("level");
		var name = $(this).attr("name");
		if(typeof($(this).attr("ispost")) != "undefined"){
			isPost = parseInt($(this).attr("ispost"));
		}
		$("#<?php echo $id;?>-tree-selected-name").html($(this).attr("name"));
		if(typeof(next.find("div").html()) == 'undefined' && isPost == 0){
			thisElement.attr("ispost", "1");
			$.post(url, {id:id}, function(data){
				if(data['status'] == 1){
					$.each(data['data'], function (i, val){
						html = '<li><div class="title" title="' + name + val + '" name="' + name + val + '" level="' + level + "-" + i + '" id="' + i + '">' + 
							   '<i class="fa fa-plus-square-o"></i>' + val + '</div><div class="next"></div></li>';
						next.append(html);
					});
				} else {
					$(this).attr("ispost", 0);
					tagI.remove();
				}
			}, "json");
		}
		if (!$(this).hasClass("show")){
			$(this).addClass("show");
			tagI.addClass("fa-minus-square-o");
			tagI.removeClass("fa-plus-square-o");
			next.show();
		} else {
			$(this).removeClass("show");
			tagI.addClass("fa-plus-square-o");
			tagI.removeClass("fa-minus-square-o");
			next.hide();
		}
		if (!$(this).hasClass("cur")){
			$("#<?php echo $id;?>-tree-select-box .cur").removeClass("cur");
			$(this).addClass("cur");
		}
	});
	$("#<?php echo $id;?>-tool-close").click(function (){
		<?php echo $id;?>_NameSpace.func.closeTreeBox();
	});

	$("html").resize(function () {
		<?php echo $id;?>_NameSpace.func.initTreePosition();
	});
});
</script>