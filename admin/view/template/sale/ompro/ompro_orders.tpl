<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">
<section class="content-header">
  <h1><?php echo $heading_title; ?></h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>
<form action="" method="post" enctype="multipart/form-data" id="form-editor">
	<section class="content omanager-content" id="omanager"></section>
	<input type="hidden" id="orders-pageid" value="<?php echo $pageid; ?>" />
	<input type="hidden" id="sort" value=""/>
	<input type="hidden" id="order" value=""/>
	<input type="hidden" id="page" value=""/>
	<input type="hidden" filter_Input = "" input_quick_filter = "" filterReload="1" id="" name="" value="" />
</form>
<script type="text/javascript"><!--

$(document).ready(function(){
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		toastr.error(error, '<?php echo $text_alert_error; ?>');
	}
	if (success) {
		toastr.options.timeOut = 10000;
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}
	getContent('&pageid=' + encodeURIComponent('<?php echo $pageid; ?>'));
});

$(document).delegate('input', 'keypress', function(e) {
	if (e.keyCode == 13 ){
		var order_history_btn = $(this).closest('.input-group').find('.order_history_btn.direct-chat-history-btn');
		if (order_history_btn.length) {
			event.preventDefault(); event.stopPropagation();
			order_history_btn.trigger('click'); return false;
		}
	}
});
--></script>
</div>
<?php echo $footer; ?>