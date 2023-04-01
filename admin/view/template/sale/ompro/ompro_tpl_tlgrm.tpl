<?php echo $header; ?>
<div class="content-wrapper template-form history-template-page" id="content-omanager">

<link rel="stylesheet" type="text/css" href="view/javascript/ompro/codemirror/lib/codemirror.css"/>
<script type="text/javascript" src="view/javascript/ompro/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/addon/matchbrackets.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/addon/autorefresh.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/mode/xml.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/codemirror/theme/rubyblue.css"/>

<link rel="stylesheet" href="view/javascript/ompro/summernote/summernote.css" type="text/css"/>
<script type="text/javascript" src="view/javascript/ompro/summernote/summernote.js"></script>
<script type="text/javascript" src="view/javascript/ompro/summernote/opencart.js"></script>
<script type="text/javascript" src="view/javascript/ompro/summernote/lang/summernote-ru-RU.js"></script>

<section class="content-header">
  <h1>
	<?php echo $heading_title; ?>
	<small><?php echo $heading_template_edit; ?></small>
  </h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section class="content">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
  <div class="box">
	<div class="box-header with-border">
		<div class="row">
		  <div class="col-lg-10 box-tools pull-left">
			<div class="col-lg-5">
			  <div class="form-group valid-block">
				<span class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_code; ?>"><?php echo $text_tpl_code; ?></div>
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_name; ?>"><i class="fa fa-text-width"></i></div>
					<input type="text" id="input-name" name="template[name]" value="<?php echo isset($template['name']) ? $template['name'] : $text_new_template; ?>" class="field-input mustbe form-control" />
				</span>
			  </div>
			</div>
			<div class="col-lg-7">
			  <div class="form-group">
				<span class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_description; ?>"><i class="fa fa-info"></i></div>
					<textarea name="template[description]" id="input-description" rows="1" class="form-control"><?php echo isset($template['description']) ? $template['description'] : ''; ?></textarea>
				</span>
			  </div>
			</div>
		  </div>
		  <div class="col-lg-2 box-tools pull-right text-right">
			<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>
	<div class="box-body">
		<div class="nav-tabs-custom">
		  <ul id="nav-tabs" class="nav nav-tabs">
            <li><a href="#tab-template" data-toggle="tab"><?php echo $tab_template_tlgrm; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane" id="tab-template">
			  <div class="nav-tabs-custom">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_telegram.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_template_tlgrm_info; ?></p>
				</div>
				<div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#all-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_tlgrm_text_format; ?></a></div>
					<div id="all-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="callout callout-default">
							<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_template_tlgrm_info1; ?>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_template_tlgrm_info2; ?>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_template_tlgrm_info3; ?>
							</p>
						</div>
						<div class="row">
							<div class="col-lg-12"><?php echo $tlgrm_text_format_table; ?></div>
						</div>
					  </div>
					</div>
				  </div>
				</div>
				<?php if (!$user_telegram_id) { ?>
				  <div class="alert alert-danger">
					<p><i class="fa fa-warning"></i>&nbsp;&nbsp; <?php echo $text_alert_test_telegram; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></p>
				  </div>
				<?php } ?>
				<div class="form-group">
					<div class="col-sm-6">
						<div class="input-group">
						  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_test_comment; ?>">&nbsp;<i class="fa fa-comment"></i>&nbsp;</span>
						  <textarea name="test_comment" rows="1" id="test-comment" class="form-control"></textarea>
						  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_test_comment_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
						</div>
					</div>
					<div class="col-sm-6 valid-block">
						<div class="input-group">
						  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_last_order; ?>">&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;</span>
						  <input type="text"  value="<?php echo $last_order; ?>" id="test-order-id" class="field-input digit mustbe form-control" placeholder="<?php echo $entry_select_last_order_placeholder; ?>"/>
						  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_last_order_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
						  <span class="input-group-btn">
							<button type="button" id="send-me-tlgrm" data-toggle="tooltip" title="<?php echo $button_send_tlgrm; ?>" class="btn btn-primary"><i class="fa fa-location-arrow"></i></button>
						  </span>
						</div>
					</div>
				</div>

				<ul id="languages" class="nav nav-tabs">
				  <?php foreach ($languages as $language) { ?>
				  <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="<?php if ($ocversion >= 220) { echo 'language/'.$language['code'].'/'.$language['code'].'.png'; } else { echo 'view/image/flags/'. $language['image']; } ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
				  <?php } ?>
				</ul>

				<div class="tab-content">
				  <?php foreach ($languages as $language) { ?>
					<div id="language<?php echo $language['language_id']; ?>" class="tab-pane">
						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_tlgrm_main_template; ?>
						  <br><?php echo $order_vars_link; ?></label>
						  <div class="col-sm-10">
							<textarea class="htmlTextArea form-control" name="template[message][<?php echo $language['language_id']; ?>]"><?php echo isset($template['message'][$language['language_id']]) ? $template['message'][$language['language_id']] : ''; ?></textarea>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_tlgrm_totals_template; ?></label>
						  <div class="col-sm-10">
							<textarea rows="5" class="htmlTextArea form-control" name="template[total_tpl][<?php echo $language['language_id']; ?>]"><?php echo isset($template['total_tpl'][$language['language_id']]) ? $template['total_tpl'][$language['language_id']] : ''; ?></textarea>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_tlgrm_product_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help; ?>" class="fa fa-question-circle text-danger"></i>
						  <br><?php echo $product_vars_link; ?></label>
						  <div class="col-sm-10">
							<textarea class="htmlTextArea form-control" name="template[product_tpl][<?php echo $language['language_id']; ?>]"><?php echo isset($template['product_tpl'][$language['language_id']]) ? $template['product_tpl'][$language['language_id']] : ''; ?></textarea>
						  </div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">
								<p><?php echo $entry_attribute_group_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help2; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-10">
							  <textarea name="template[attribute_group_tpl][<?php echo $language['language_id']; ?>]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['attribute_group_tpl'][$language['language_id']]) ? $template['attribute_group_tpl'][$language['language_id']] : '*{attribute_group_name}:* [[{attribute}]]'; ?></textarea>
							</div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_tlgrm_product_attribute_template; ?></label>
						  <div class="col-sm-10">
							<textarea rows="5" class="htmlTextArea form-control" name="template[attribute_tpl][<?php echo $language['language_id']; ?>]"><?php echo isset($template['attribute_tpl'][$language['language_id']]) ? $template['attribute_tpl'][$language['language_id']] : '  • {attribute_name}: *{attribute_value}* '; ?></textarea>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_tlgrm_product_options_template; ?></label>
						  <div class="col-sm-10">
							<textarea rows="5" class="htmlTextArea form-control" name="template[option_tpl][<?php echo $language['language_id']; ?>]"><?php echo isset($template['option_tpl'][$language['language_id']]) ? $template['option_tpl'][$language['language_id']] : '  • {option_name}: {option_value}'; ?></textarea>
						  </div>
						</div>

					</div>
				  <?php } ?>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_backup">
				<div class="callout callout-default">
					<p><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_backup_help; ?></p>
				</div>
				<div class="panel panel-default">
				  <div class="panel-body">
					<div class="row" style="margin-bottom: 15px;">
						<label class="col-sm-2 control-label text-right"><?php echo $entry_save_to_file; ?></label>
						<div class="col-sm-10">
						  <a class="btn btn-primary" target="_blank" href="<?php echo $backup_link ?>" id="button-backup"><?php echo $button_save ?></a>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label text-right"><?php echo $entry_restore_from_file; ?></label>
						<div class="col-sm-10">
							<table class="restore-table">
							  <tr>
								<td>
								  <input class="btn btn-default" name="import" type="file" />
								  <input name="restore" type="hidden" value="0" id="restore" />
								</td>
								<td>
								  <a class="btn btn-primary button-restore" disabled="disabled"><?php echo $button_restore; ?></a>
								</td>
							  </tr>
							</table>
						</div>
					</div>
				  </div>
				</div>
			</div>
		  </div>
		</div>
	</div>
  </div>
</form>
</section>

<!-- import. restore, save  -->
<script type="text/javascript"><!--

var has_permission = '<?php echo $has_permission; ?>';

$('input[name=\'import\']').bind('change', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		$('input[name=\'import\']').val('');
		return false;
	}
	var imp = $('input[name=\'import\']').val();
	if (imp) {
		$('.button-restore').attr('disabled', false);
	} else {
		$('.button-restore').attr('disabled', true);
	}
});

$('.button-restore').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var imp = $('input[name=\'import\']').val();
	if (!imp) { return false; }

	var template_id = '<?php echo $template_id; ?>';

	if (template_id < 1) {
		toastr.error('<?php echo $error_restore_no_template; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	} else {
		$('#restore').val(1);
		var url = '<?php echo $restore_link; ?>';
		url = url.replace(/&amp;/g, "&");
		$('#form').attr('action', url).submit();
	}
});

$('#button_save').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	} else if (!window.validation.isValid({ container: '#form' })) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p><?php echo $text_error_form; ?></p>', '');
		return false;
	} else {
		var url = '<?php echo $action; ?>';
		url = url.replace(/&amp;/g, "&");
		$('#form').attr('action', url).submit();
	}
});

//--></script>

<script type="text/javascript"><!--

$('#send-me-tlgrm').on('click', function() {
	var template_id = '<?php echo $template_id; ?>';
	if (!template_id || template_id == 0) {
		toastr.options.preventDuplicates = true;
		toastr.error('<?php echo $error_template_preview; ?>', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var bot_token_status = '<?php echo $bot_token_status; ?>';

	if (!bot_token_status) {
		toastr.options.preventDuplicates = true;
		toastr.error('<?php echo $error_tlgrm_bot_token; ?>', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var order_id = $('#test-order-id').val();
	if (!order_id) {
		toastr.options.timeOut = 5000;
		toastr.error('№ заказа не указан!', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var comment = $('#test-comment').val();
	comment = comment ? '&comment=' + encodeURIComponent(comment) : '';

	$.ajax({
		url: 'index.php?route=sale/ompro/sendToTelegramTest&<?php echo $strtoken; ?>&order_id=' + order_id + '&template_id=' + template_id + comment,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['warning']) {
				toastr.warning(json['warning'], '<?php echo $text_alert_warning; ?>');
			}
			if (json['success']) {
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

//--></script>

<script type="text/javascript"><!--

$(document).delegate('#form, #form input, #form sellect, #form textarea', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

$(document).ready(function(){
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	$('.htmlTextArea').each(function(){
		var htmlTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "auto", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			htmlTextArea.html(editor.getValue()).trigger('change');
		});
	});

	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh();

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
});
/*
$("form").bind("keypress", function (e) {
    if (e.keyCode == 13) { return false; }
});
*/
//--></script>

</div>
<?php echo $footer; ?>