<style>
.tableinput-main-div{width:<?php if($size>0):?><?php echo $size.'px';?><?php else:?>200px<?php endif;?>;}
.tableinput-main-div table{width:100%; margin:0;}
.tableinput-main-div table tr th{text-align:center;}
</style>
<div class="tableinput-main-div">
	<table class="table table-bordered" id="<?php echo $id;?>">
		<thead>
			<tr>
				<?php foreach($field as $val):?>
					<th><?php echo $val;?></th>
				<?php endforeach;?>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0;?>
			<?php if(is_array($value) && count($value) > 0):?>
				<?php foreach($value as $key => $val):?>
					<tr num="<?php echo $i;?>">
						<td>
							<input id="<?php echo $id . '[' . $i . '][key]'?>" name="<?php echo $id . '[' . $i . '][key]'?>" type="text" value="<?php echo $key;?>">
						</td>
						<td>
							<input id="<?php echo $id . '[' . $i . '][val]'?>" name="<?php echo $id . '[' . $i++ . '][val]'?>" type="text" value="<?php echo $val;?>">
						</td>
					</tr>
				<?php endforeach;?>
			<?php endif;?>
		</tbody>
		<tfoot>
			<tr>
			 	<td colspan="2" style="text-align:center;">
			 		<a class="btn btn-primary btn-sm" href="javascript:void(0);" id="<?php echo $id;?>-tableinput-add">
			 			<i class="fa fa-plus-square"></i>&nbsp;新增
			 		</a>
			 	</td>
			</tr>
		</tfoot>
	</table>
</div>

<script>
$("#<?php echo $id;?>-tableinput-add").click(function() {
	var num = parseInt($("#<?php echo $id;?> tbody tr:last").attr("num")) + 1;
	var html = '<tr num="' + num + '"><td><input id="<?php echo $id?>[' + num + '][key]" name="<?php echo $id?>[' + num + '][key]"' + 
	'type="text" value=""></td><td><input id="<?php echo $id?>[' + num + '][val]" name="<?php echo $id?>[' + num + '][val]" type="text"' + 
	' value=""></td></tr>';
	$("#<?php echo $id;?> tbody").append(html);
});
</script>