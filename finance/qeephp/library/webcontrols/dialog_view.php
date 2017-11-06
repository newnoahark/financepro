<div id="<?php echo $id;?>" title="<?php echo $title;?>"></div>
<script type="text/javascript">
jQuery(function($) {
	$( "#<?php echo $id;?>" ).dialog({
		autoOpen: false,
		height: 180,
		width: 345,
		modal: false,
		resizable: true,
		show: "slide"
	});
});
</script>