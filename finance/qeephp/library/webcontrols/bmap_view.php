<link rel="stylesheet" href="http://api.map.baidu.com/library/SearchInfoWindow/1.5/src/SearchInfoWindow_min.css" />
<style>
.<?php echo $id;?>-map-block{width:700px; height:429px;}
.<?php echo $id;?>-map-tools{width:100%; height:35px; margin:0 auto;text-align: center;}
#<?php echo $id;?>-map-box{width:100%; height:100%; position: absolute;left:0; top:0; z-index:999;}
#<?php echo $id;?>-map-box .mask{width: 100%; height:100%; position: absolute; background:#000;filter:alpha(Opacity=50);-moz-opacity:0.5;opacity: 0.5;}
</style>
<div>
	<div style="float: left;">
		<input id="<?php echo $id;?>" value="<?php echo $value;?>" type="text" name="<?php echo $id;?>" style="width:300px;<?php echo $style;?>">
	</div>
	<div style="float: left;">
		<a id="<?php echo $id;?>-map-select" style="height:32px; padding:0 18px; line-height:32px; margin-left:8px;" class="btn btn-primary">选择坐标</a>
	</div>
</div>
<div id="<?php echo $id;?>-map-box" style="visibility:hidden;">
	<div class="mask"></div>
	<div style="position: fixed;">
		<div class="select-window panel window messager-window" id="<?php echo $id;?>-map-window" style="width:700px;">
			<div class="panel-header panel-header-noborder window-header">
				<div class="panel-title" style="">请选择</div>
				<div class="panel-tool"><a href="javascript:void(0)" class="panel-tool-close" id="<?php echo $id;?>-map-tool-close"></a></div>
			</div>
			<div class="<?php echo $id;?>-map-block" id="<?php echo $id;?>-map-block"></div>
			<div class="<?php echo $id;?>-map-tools">
				搜索地区 <input name="<?php echo $id;?>-map-search" type="text"> <a id="<?php echo $id;?>-map-tools-search" style="height:32px; padding:0 18px; line-height:32px; margin-left:8px;" class="btn btn-primary">搜索</a>
				<a id="<?php echo $id;?>-map-tools-ok" style="height:32px; padding:0 18px; line-height:32px; margin-left:8px;" class="btn btn-primary">确定</a>
			</div>
		</div>
	</div>
</div>
<div style="clear:both;"></div>
<script>
	var marker = 0;
	var isInit = false;
	var map = "";
	function loadMap(){
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = "http://api.map.baidu.com/api?v=2.0&ak=gHKshOZ0uNGgEl8SXSvsbkhe&callback=init";
		script.id = "bmap";
		document.body.appendChild(script);
	}
	
	function init() {
		map = new BMap.Map("<?php echo $id;?>-map-block", {enableMapClick : false});//构造底图时，关闭底图可点功能
		//百度地图初始化
		var curPoint = $("#<?php echo $id;?>").val();
		var point = "" // 创建点坐标
		if(curPoint.length > 0){
			curPoint = curPoint.split(',');
			point = new BMap.Point(curPoint[0], curPoint[1]);
		} else {
			point = new BMap.Point(107.988158,26.573057);
		}
		setMarker(point);
		map.centerAndZoom(point, 10); // 初始化地图,设置中心点坐标和地图级别。
		map.enableScrollWheelZoom();
		map.disableDoubleClickZoom();
		//添加放大、缩小控件到地图
		map.addControl(new BMap.NavigationControl({anchor: BMAP_ANCHOR_TOP_RIGHT, type: BMAP_NAVIGATION_CONTROL_LARGE}));
		map.addEventListener("click",function(e){
			var p = new BMap.Point(e.point.lng, e.point.lat);
			setMarker(p);
		});
		isInit = true;
		//$("#<?php echo $id;?>-map-box").replaceAll($("#body-map-<?php echo $id;?> #<?php echo $id;?>-map-box"));
	}

	function openMap(){
		var curPoint = $("#<?php echo $id;?>").val();
		var point = "" // 创建点坐标
		var zoom = 10;
		var curCenter = map.getCenter();
		if(marker != 0){
			map.removeOverlay(marker);
		}
		if(curPoint.length > 0){
			curPoint = curPoint.split(',');
			point = new BMap.Point(curPoint[0], curPoint[1]);
			zoom = 14;
		} else {
			point = new BMap.Point(107.988158,26.573057);
		}
		setMarker(point);
		map.setZoom(zoom);
		if(curCenter.lng!=point.lng || curCenter.lat!=point.lat){
			map.panTo(point);
		}
	}

	function setMarker(point){
		if(marker != 0){
			map.removeOverlay(marker);
		}
		marker = new BMap.Marker(point);
		marker.enableDragging();
		map.addOverlay(marker);
		marker.setAnimation(BMAP_ANIMATION_DROP);
	}
</script>

<script>
$(document).ready(function() {
	initPosition();
	if($("#body-map-<?php echo $id;?>").length == 0){
		var bodyBlock = "<div id=\"body-map-<?php echo $id;?>\"></div>";
		$("body").append(bodyBlock);
		var html = $("#<?php echo $id;?>-map-box");
		$("#<?php echo $id;?>-map-box").remove();
		$("#body-map-<?php echo $id;?>").html(html);
	} else {
		var html = $("#<?php echo $id;?>-map-box:first");
		$("#<?php echo $id;?>-map-box").remove();
		$("#body-map-<?php echo $id;?>").html(html);
	}
	

	$("#<?php echo $id;?>-map-select").click(function() {
		$("#<?php echo $id;?>-map-box").css("visibility", "visible");
		$("#<?php echo $id;?>-map-box").css("z-index", "10000");
		if(isInit){
			openMap();
		} else if($("#bmap").length == 0){
			loadMap();
		} else {
			init();
		}
		
	});

	$("#<?php echo $id;?>-map-box").on("click", "#<?php echo $id;?>-map-tool-close", function() {
		$("#<?php echo $id;?>-map-box").css("visibility", "hidden");
	});
	
	$("#<?php echo $id;?>-map-tools-ok").click(function () {
		var p = marker.getPosition();
		var value = p.lng + "," + p.lat;
		$("#<?php echo $id;?>").val(value);
		$("#<?php echo $id;?>-map-box").css("visibility", "hidden");
	});

	$("#<?php echo $id;?>-map-tools-search").click(function() {
		var zone = $("input[name='<?php echo $id;?>-map-search']").val();
		var localSearch = new BMap.LocalSearch(map);
		localSearch.setSearchCompleteCallback(function (searchResult) {
	        var poi = searchResult.getPoi(0);
	        if(typeof(poi) != "undefined"){
				setMarker(poi.point);
				map.panTo(poi.point);
		    }
	    });
		localSearch.search(zone);
		
	});
	
	$("html").resize(function () {
		initPosition();
	});
	
});
function initPosition(){
	var control_width = $("#<?php echo $id;?>-map-window").width();
	var windows_width = $("html").width();
	var left = (windows_width - control_width) / 2;
	$("#<?php echo $id;?>-map-window").css("left", left+"px");
	$("#<?php echo $id;?>-map-window").css("top", "30px");
}
</script>