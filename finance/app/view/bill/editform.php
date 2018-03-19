
<style type="text/css">
.form-control {
	width: 60%;
}
</style>
<link rel="stylesheet" type="text/css"
	href="<?php echo $_BASE_DIR; ?>css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript"
	src="<?php echo $_BASE_DIR; ?>js/plugins/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript"
	src="<?php echo $_BASE_DIR; ?>js/plugins/locales/bootstrap-datetimepicker.fr.js"></script>
</style>
<div class="row">
	<div class="col-md-12">
		<h1 class="page-header">
			<small>单据填写</small>
		</h1>
	</div>
</div>
<!-- /. ROW  -->
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">基本信息</div>
			<div class="panel-body" style="border: 0;">

				<div class="row">
					<form id="bill-form" role="form">
						<div class="col-lg-6">
							<div class="form-group">
								<label>商品名称</label> <input id="goods-name" class="form-control"
									name="goods_name" placeholder="商品名称">
							</div>
							<div class="form-group">
								<label>商品价格</label> <input class="form-control" id="goods-price"
									name="goods_price" placeholder="商品价格">
							</div>
							<div class="form-group">
								<label>数量</label> <input class="form-control" id="goods-amount"
									name="goods_amount" placeholder="数量">
							</div>
							<button id="submit-button" type="button" class="btn btn-default">SubmitButton</button>
							<button type="reset" class="btn btn-default">Reset Button</button>
						</div>
					</form>
					<!-- /.col-lg-6 (nested) -->

				</div>
				<!-- /.row (nested) -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
		<footer>
			<p>author:panchangming</p>
		</footer>
	</div>
	<!-- /.col-lg-12 -->
	<script>

 $('#datetimepicker').datetimepicker({
	 format: 'yyyy-mm-dd ',/*此属性是显示顺序，还有显示顺序是mm-dd-yyyy*/
	 todayHighlight: true,
	 minView:'2',
	 autoclose:true
	 
	 });
$("#sixmonth").click(function(){
	$("#validity-input").val("六个月");
});
$("#oneyear").click(function(){
	$("#validity-input").val("一年");
});
$("#threeyear").click(function(){
	$("#validity-input").val("三年");
});
$("#sixyear").click(function(){
	$("#validity-input").val("六年");
});

 
 </script>