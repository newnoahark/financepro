
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
								<label>商品名称</label> <input id="drug-name" class="form-control"
									name="drug_name" placeholder="商品名称">
							</div>
							<div class="form-group">
								<label>商品规格</label> <input class="form-control"
									name="drug_specifications" placeholder="商品规格">
							</div>
							<div class="form-group">
								<label>批准文号</label> <input class="form-control"
									name="approval_number" placeholder="批准文号">
							</div>
							<div class="form-group">
								<label>剂型</label> <input class="form-control" name="dosage_form"
									placeholder="剂型">
							</div>
							<div class="form-group">
								<label>产地</label> <input class="form-control" name="zone"
									placeholder="产地">
							</div>
							<div class="form-group">
								<label>生产厂家</label> <input class="form-control"
									name="manufacturer" placeholder="生产厂家">
							</div>
							<div class="form-group">
								<label>供应商</label> <input class="form-control" name="supplier"
									placeholder="供应商">
							</div>
							<div class="form-group">
								<label>效期</label> <input id="validity-input"
									class="form-control" name="validity" style="cursor: pointer;"
									placeholder="效期" data-toggle="dropdown">
								<ul id="validity-ul" class="dropdown-menu" role="menu">
									<li><a id="sixmonth" href="#">六个月</a></li>
									<li><a id="oneyear" href="#">一年</a></li>
									<li><a id="threeyear" href="#">三年</a></li>
									<li><a id="sixyear" href="#">六年</a></li>
								</ul>
							</div>

						</div>

						<!-- /.col-lg-6 (nested) <input class="col-md-2 control-label" name="billdate" placeholder="单据日期">-->
						<div class="col-lg-6">
							<div class="form-group">
								<label>单据日期</label> <input id="datetimepicker"
									style="cursor: pointer;" class="form-control date form_date"
									name="billdate" placeholder="单据日期"
									data-date-format="yyyy-mm-dd hh:ii">
							</div>
							<div class="form-group">
								<label>单据编号</label> <input class="form-control"
									name="billnumber" placeholder="单据编号">
							</div>
							<div class="form-group">
								<label>单据类型</label> <input class="form-control" name="billtype"
									placeholder="单据类型">
							</div>
							<div class="form-group">
								<label>货位</label> <input class="form-control" name="location"
									placeholder="货位">
							</div>
							<div class="form-group">
								<label>单位</label> <input class="form-control" name="unit"
									placeholder="单位">
							</div>
							<div class="form-group">
								<span>数量<input name="quantity" class="form-control"
									placeholder="数量" style="display: inline; width: 13%;"></span> <span>入库数量<input
									name="intoquantity" class="form-control" placeholder="入库数量"
									style="display: inline; width: 13%;"></span> <span>出库数量<input
									name="exitquantity" class="form-control" placeholder="出库数量"
									style="display: inline; width: 13%;"></span>
							</div>
							<div class="form-group">
								<span>成本价<input name="cost" class="form-control"
									placeholder="成本价" style="display: inline; width: auto;"></span>
								<span>单价<input name="price" class="form-control"
									placeholder="单价" style="display: inline; width: auto;"></span>
							</div>
							<div class="form-group">
								<label>往来单位</label> <input name="tradeunit" class="form-control"
									placeholder="往来单位">
							</div>
							<div class="form-group">
								<label>经手人</label> <input name="handleperson"
									class="form-control" placeholder="经手人">
							</div>
							<div class="form-group">
								<label>制单人</label> <input name="singler" class="form-control"
									placeholder="制单人">
							</div>
							<button id="submit-button" type="button" class="btn btn-default">Submit
								Button</button>
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