<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">

<section class="content-header">
  <h1><?php echo $heading_license; ?></h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section class="content">
  <div class="box box-info">
	<div class="box-header with-border">
	  <h3 class="box-title"><a onclick="$('#license-info').slideToggle();"><i class="fa fa-info"></i>&nbsp;&nbsp; <?php echo $text_license_info_title; ?></a></h3>
	</div>
	<div id="license-info" class="box-body">
	  <ol>
		<li><?php echo $text_license_info1; ?></li>
		<li><?php echo $text_license_info2; ?></li>
		<li><?php echo $text_license_info3; ?></li>
	  </ol>
	  <div class="alert callout callout-danger">
		<h4><i class="icon fa fa-ban"></i> <?php echo $text_license_warning_title; ?></h4>
		<p><?php echo $text_license_warning_content; ?></p>
	  </div>
	</div>
  </div>
  <div class="box box-primary activate-form">
	<div class="box-header with-border">
	  <h3 class="box-title"><?php echo $heading_form; ?><span class="text-danger text-bold"><?php echo $domain; ?></span></h3>
	</div>

	<div id="activate-form-box">
		<div class="form-horizontal box-body">
			<div class="form-group">
			  <label for="license_key" class="col-sm-2 control-label"><?php echo $text_key; ?></label>
			  <div class="col-sm-10 valid-block">
				<input type="text" id="license_key" name="license_key" class="field-input mustbe form-control" placeholder="<?php echo $placeholder_license_key; ?>" value="<?php echo $license_key; ?>" />
			  </div>
			</div>
		</div>
		<div class="box-footer">
		  <button class="btn btn-danger open-license-form" onclick="$('#license-form-box').slideDown(); $('#activate-form-box').slideUp();" <?php echo $btn_activate_attr; ?>><?php echo $text_request; ?></button>
		  <button class="save-button activate btn btn-primary pull-right" <?php echo $btn_activate_attr; ?>><?php echo $button_activate; ?></button>
		</div>
	</div>

	<div id="license-form-box" style="display: none;">
	  <div class="box-body">
		<div class="col-sm-6">
			<div class="form-group">
				<label for="test_domain" class="control-label"><?php echo $text_test_domain; ?></label>
				<div class="input-group valid-block">
				  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_test_domain_info; ?>"><i class="fa fa-spinner"></i></span>
				  <select id="test_domain" name="test_domain" class="field-select field-input mustbe form-control">
					<option value="" selected="selected" ></option>
					<option value="0"><?php echo $text_no; ?></option>
					<option value="1" ><?php echo $text_yes; ?></option>
				  </select>
				</div>
			</div>
			<div class="form-group">
			  <label for="email" class="control-label"><?php echo $text_email; ?></label>
			  <div class="input-group valid-block">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_email_info; ?>"><i class="fa fa-at"></i></span>
				<input type="text" id="email" name="email" class="field-input emailmusbe form-control" value="<?php echo $config_email; ?>"/>
			</div>
			</div>
		</div>
		<div class="col-sm-6">
			<div class="form-group">
			  <label for="sale_resourse" class="control-label"><?php echo $text_site; ?></label>
			  <div class="input-group valid-block">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_site_info; ?>"><i class="fa fa-globe"></i></span>
				<select id="sale_resourse" name="sale_resourse" class="field-select field-input mustbe form-control">
					<option value=""></option>
					<?php foreach ($sale_resourses as $resourse) { ?>
					<option value="<?php echo $resourse; ?>"><?php echo $resourse; ?></option>
					<?php } ?>
				</select>
			  </div>
			</div>
			<div class="form-group">
			  <label for="order_id" class="control-label"><?php echo $text_order_id; ?></label>
			  <div class="input-group valid-block">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_order_id_info; ?>"><i class="fa fa-shopping-cart"></i></span>
			    <input type="text" id="order_id" name="order_id" class="field-input digitmustbe form-control" />
			  </div>
			</div>
		</div>
	  </div>
	  <div class="box-footer">
		<button class="btn btn-default" onclick="$('#license-form-box').slideUp(); $('#activate-form-box').slideDown();"><?php echo $text_hide_form; ?></button>
		<button class="save-button get btn btn-primary pull-right" ><?php echo $button_request; ?></button>
	  </div>
	</div>
  </div>

</section>

<!-- license forms -->
<script type="text/javascript"><!--

/* activate-form */
$(function () { window.validation.init({ container: '#activate-form-box' });});

$('input[name=\'license_key\']').on('change, click, keyup', function () {
	if (window.validation.isValid({ container: '.activate-form' })) {
		$('button.save-button.activate').removeClass("btn-primary");
		$('button.save-button.activate').addClass("btn-success");
	} else {
		$('button.save-button.activate').removeClass("btn-success");
		$('button.save-button.activate').addClass("btn-primary");
	}
	$('button.save-button.activate').prop('disabled', false);
	$('button.open-license-form').prop('disabled', false);
});

$('.activate-form .save-button.activate').on('click', function () {
	if (window.validation.isValid({ container: '#activate-form-box' })) {
		var license_key = $('input[name=\'license_key\']').val();
		var dataString = 'license_key='+ license_key;
		url = '<?php echo $index_route; ?>/activate&<?php echo $strtoken; ?>';
		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: dataString,
			beforeSend: function() {
				$('button.save-button.activate').text('<?php echo $text_waiting; ?>');
			},
			success: function(json) {
				$('button.save-button.activate').text('<?php echo $button_activate; ?>');
				if (json['error']) {
					modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
				}
				if (json['success']) {
					var footer = '<button type="button" class="btn btn-outline pull-left" data-dismiss="modal"><?php echo $text_close; ?></button><button type="button" class="btn btn-outline" onclick="location = \'index.php?route=sale/ompro/admin&<?php echo $strtoken; ?>\';"><?php echo $text_goto; ?></button>';
					modalAlert('modal-success', '<?php echo $text_alert_success; ?>', '<p>'+json['success']+'</p>', footer);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	} else {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p><?php echo $text_alert_check_form; ?></p>', '');
		$('button.save-button.activate').removeClass("btn-success");
		$('button.save-button.activate').addClass("btn-primary");
	}
});

/* license-form */
$(function () { window.validation.init({ container: '#license-form-box' });});

$('#license-form-box .field-input').on('change, click, keyup', function () {
	if (window.validation.isValid({ container: '#license-form-box' })) {
		$('button.save-button.get').removeClass("btn-primary");
		$('button.save-button.get').addClass("btn-success");
	} else {
		$('button.save-button.get').removeClass("btn-success");
		$('button.save-button.get').addClass("btn-primary");
	}
});

$('#license-form-box .save-button.get').on('click', function () {
	if (window.validation.isValid({ container: '#license-form-box' })) {
		var sale_resourse = $('select[name=\'sale_resourse\']').val();
		var order_id = $('input[name=\'order_id\']').val();
		var test_domain = $('select[name=\'test_domain\']').val();
		var email = $('input[name=\'email\']').val();
		var dataString = 'sale_resourse=' + sale_resourse + '&order_id=' + order_id + '&test_domain=' + test_domain + '&email=' + email;
		url = '<?php echo $index_route; ?>/requestLicense&<?php echo $strtoken; ?>';

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'json',
			data: dataString,
			beforeSend: function() {
				$('button.save-button.get').text('<?php echo $text_waiting; ?>');
			},
			success: function(json) {
				if (json['error']) {
					modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
				}
				if (json['success']) {
					$('input[name=\'license_key\']').val(json['license_key']).trigger('change');
					$('#license-form-box').slideUp();
					$('#activate-form-box').slideDown();
					$('html, body').animate({ scrollTop: $("button.save-button.get").offset().top }, 2000);

					modalAlert('modal-success', '<?php echo $text_alert_success; ?>', '<p>'+json['success']+'</p>', '');
				}
				$('button.save-button.get').text('<?php echo $button_request; ?>');
			}
		});

	} else {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p><?php echo $text_alert_check_form; ?></p>', '');
		$('button.save-button.get').removeClass("btn-success");
		$('button.save-button.get').addClass("btn-primary");
	}
});

//--></script>

<!-- document ready -->
<script type="text/javascript"><!--
$(document).ready(function(){
	var warning = '<?php echo $error_warning; ?>';
	if (warning) {
		modalAlert('modal-warning', '<?php echo $text_alert_warning; ?>', '<p>'+warning+'</p>', '');
	}
	tooltipRefresh();
});
//--></script>

</div>
<?php echo $footer; ?>