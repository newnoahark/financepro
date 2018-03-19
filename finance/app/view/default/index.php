<?php $this->_extends('_layouts/default_layout'); ?>

<?php $this->_block('title'); ?> - 欢迎<?php $this->_endblock(); ?>
<?php $this->_block('cssstart'); ?>

<?php $this->_endblock(); ?>
<?php $this->_block('west'); ?>


<?php $this->_endblock(); ?>
<?php $this->_block('body'); ?>
<div id="wrapper">
	<nav class="navbar navbar-default top-navbar" role="navigation">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".sidebar-collapse">
				<span class="sr-only">Toggle navigation</span> <span
					class="icon-bar"></span> <span class="icon-bar"></span> <span
					class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo url("default/index");?>"><i class="fa fa-comments"></i>
				<strong>MASTER </strong></a>
		</div>

		<ul class="nav navbar-top-links navbar-right">
			<li class="dropdown"><a class="dropdown-toggle"
				data-toggle="dropdown" href="#" aria-expanded="false"> <i
					class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>
			</a>
				<ul class="dropdown-menu dropdown-messages">

				</ul> <!-- /.dropdown-messages --></li>
			<!-- /.dropdown -->
			<li class="dropdown">
			<a class="dropdown-toggle"
				data-toggle="dropdown" href="#" aria-expanded="false"> <i
					class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
			</a>
				<ul class="dropdown-menu dropdown-tasks">

				</ul> <!-- /.dropdown-tasks --></li>
			<!-- /.dropdown -->
			<li class="dropdown"><a class="dropdown-toggle"
				data-toggle="dropdown" href="#" aria-expanded="false"> <i
					class="fa fa-bell fa-fw"></i> <i class="fa fa-caret-down"></i>
			</a>
				<ul class="dropdown-menu dropdown-alerts">

				</ul> <!-- /.dropdown-alerts --></li>
			<!-- /.dropdown -->
			<li class="dropdown"><a class="dropdown-toggle"
				data-toggle="dropdown" href="#" aria-expanded="false"> <i
					class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
			</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a></li>
					<li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a></li>
					<li class="divider"></li>
					<li><a href="#"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
				</ul> <!-- /.dropdown-user --></li>
			<!-- /.dropdown -->
		</ul>
	</nav>
	<!--/. NAV TOP  -->
	<nav class="navbar-default navbar-side" role="navigation">
		<div class="sidebar-collapse">
			<ul class="nav" id="main-menu">
				<li style="background-color: #283643;"><a id="desktop-a" href="#"><i
						class="fa fa-desktop"></i>桌面</a></li>
				<li><a href="#"><i class="fa fa-qrcode"></i>常用类型</a></li>

				<li><a id="tableshow" href="#"><i class="fa fa-table"></i>日常数据</a></li>
				<li><a id="billfillin" href="#"><i class="fa fa-edit"></i>单据填写</a></li>
			</ul>
		</div>
	</nav>
	<!-- /. NAV SIDE  -->
	<div id="page-wrapper" style="border: 0; with: 100%; padding: 20px;">
		<div id="page-inner"
			style="border: 0; with: 100%; margin: 0; padding: 10px;"></div>
	</div>
	<!-- /. PAGE WRAPPER  -->
</div>
<?php $this->_endblock(); ?>

<?php $this->_block('jsend'); ?>
<script type="text/javascript">

//判断是否为数字函数
function isNumber(val){
    var regPos = /^\d+(\.\d+)?$/; //非负浮点数
    var regNeg = /^(-(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*)))$/; //负浮点数
    if(regPos.test(val) || regNeg.test(val)){
        return true;
    }else{
        return false;
    }

}



//切换标签页
$(function(){
	$("#page-inner").panel({
		href:'<?php echo url("bill/desktop");?>',
	});;
	
});
	$("#billfillin").click(function(){
		$("#page-inner").panel({
			 				href:'<?php echo url("bill/editform");?>',
							});;
	});
	$("#desktop-a").click(function(){
		$("#page-inner").panel({
			 				href:'<?php echo url("bill/desktop");?>',
							});;
	});
	$("#tableshow").click(function(){
		$("#page-inner").panel({
			 				href:'<?php echo url("bill/tableshow");?>',
							});
		
		
	});
//提交表单
$(document).on("click","#submit-button",function(){

	if($("#goods-name").val()==""){
		$.messager.alert('温馨提示','请填写商品名称','warning',function(){
			$('#goods-name').focus();
			//$('#drug-name').select();
		});
		return;
	}
	if($("#goods-price").val()==""){
		$.messager.alert('温馨提示','请填写商品价格','warning',function(){
			$('#goods-price').focus();
			//$('#drug-name').select();
		});
		return;
	}

	if(!isNumber($("#goods-price").val())){
		$.messager.alert('温馨提示','商品价格应为数字','warning',function(){
			$('#goods-name').focus();
			//$('#drug-name').select();
		});
		return;
	}



	var param=$("#bill-form").serializeArray();

	var url = "<?php echo url("bill/savedata");?>";	
	$.post(url,param,function(data){
		if(data.status){
			$.messager.show({
				title: '提示',
				msg: '操作成功!',
				style: {
					right:'',
					bottom:''
				}
			});	
			
			$("#bill-form")[0].reset();
			//window.location.reload();
		}else{
			$.messager.alert('温馨提示',data.message,'warning');
		}
	},'json');

});

var msg = ""; 

	
</script>

<?php $this->_endblock();?>




