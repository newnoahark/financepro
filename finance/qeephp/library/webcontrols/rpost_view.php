<script>
var columns = [<?php echo json_encode($columns);?>]
</script>
<table id="rpost_<?php echo $id;?>"
	class="easyui-datagrid" title="<?php echo $caption;?>"
	data-options="
	iconCls: 'icon-diaog'
	,fix: true
	,fitColumns: true
	,idField: '<?php echo $id;?>[]'
	,rownumbers: true
	,singleSelect: false
	,columns: columns
	,url: '<?php echo url($url, array('many_to_many' => $id));?>'
	,pagination: true
	,pageSize:15
	,pageList: [15,30,50,100]
"></table>
