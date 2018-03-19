<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo $_BASE_DIR; ?>js/plugins/dist/bootstrap-table.css">
<!--DataTables JS  -->
<script src="<?php echo $_BASE_DIR; ?>js/plugins/dist/bootstrap-table.min.js"></script>
<script src="<?php echo $_BASE_DIR; ?>js/plugins/dist/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="<?php echo $_BASE_DIR; ?>js/plugins/dist/extensions/export/bootstrap-table-export.js"></script>

<div class="row">
	<div class="col-md-12">
		<h1 class="page-header">
			日常数据 
		</h1>
	</div>
</div>
<!-- /. ROW  -->

<div class="row">
	<div class="col-md-12">
		<!-- Advanced Tables  table table-striped table-bordered table-hover-->
		<div class="panel panel-default">
			<div class="panel-heading">药单数据</div>
			<div class="panel-body" style="border: 0;">
				<div class="table-responsive">
					<table  id="dataTables-example" data-toggle="table">
						<thead>
							<tr>
								<th  data-field="goods_name">名称</th>
								<th  data-field="goods_price">单价</th>
								<th  data-field="goods_amount">数量</th>
								<th  data-field="goods_money">金额</th>							
							</tr>
						</thead> 
					</table>
				</div>

			</div>
		</div>
		<!--End Advanced Tables -->
	</div>
</div>
<!-- /. ROW  -->

<!-- Metis Menu Js -->


<script>

	$('#dataTables-example').bootstrapTable({
		queryParams: function (params) {
	        return {
	            offset: params.offset,  //页码
	            limit: params.limit,   //页面大小
	            search : params.search, //搜索
	            order : params.order, //排序
	            ordername : params.sort, //排序
	        };
	    },
	    showHeader : true,
	    showRefresh : true,
	    pagination: true,//分页
	    pageNumber : 1,
	    pageList: [5, 10, 15, 20],//分页步进值
	    search: true,//显示搜索框
	    showExport: true,
	    exportDataType: 'all',
        exportTypes:[ 'csv', 'txt', 'sql', 'doc', 'excel', 'xlsx', 'pdf']

	    });
	$.ajax({
		url:'<?php echo url("bill/tablesenddate");?>',
		 success:function(data){
		
			//服务器传过来为字符串  需要将其转为json 再转为为数组对象Array(JSON.parse(data))
			for (var x in data) {
				$('#dataTables-example').bootstrapTable("append",Array(data[x]));
				
					
			}	

			//$('#dataTables-example').bootstrapTable("load",Array(JSON.parse(data)));
			//$('#dataTables-example').bootstrapTable("load",Array(data));
		},
		dataType:'json'
	});

</script>

