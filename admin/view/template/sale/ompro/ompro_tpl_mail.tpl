<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">

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
            <li><a href="#tab-template" data-toggle="tab"><?php echo $tab_template_mail; ?></a></li>
            <li><a href="#tab-setting" data-toggle="tab"><?php echo $tab_template_mail_setting; ?></a></li>
            <li><a id="trigger-preview" href="#tab-preview" data-toggle="tab"><?php echo $tab_template_mail_preview; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">

            <div class="tab-pane" id="tab-template">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_mail.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_mail_tamplate_info; ?></p>
			  </div>
			  <div class="nav-tabs-custom">
				<div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#var-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_order_table_info_block_title; ?></a></div>
					<div id="var-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<a class="btn btn-default btn-block" onclick="getTableCodes('product_table');"><i class="fa fa-th-list"></i>&nbsp;  <?php echo $btn_product_table_tpl; ?></a>
								<div class="btn-group btn-group-vertical btn-block">
									<a class="btn btn-default text-red" onclick="getTableCodes('print_orders');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_orders_table_tpl; ?></a>
									<a class="btn btn-default text-red" onclick="getTableCodes('print_orders_table');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_orders_table_table_tpl; ?></a>
									<a class="btn btn-default text-red" onclick="getTableCodes('print_products_table');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_products_table_table_tpl; ?></a>
								</div>
								<div class="btn-group btn-group-vertical btn-block">
									<a class="btn btn-default text-green" onclick="getTableCodes('excel_orders');"><i class="fa fa-file-excel-o"></i>&nbsp;  <?php echo $btn_excel_orders_table_tpl; ?></a>
									<a class="btn btn-default text-green" onclick="getTableCodes('excel_orders_products');"><i class="fa fa-file-excel-o"></i>&nbsp;  <?php echo $btn_excel_orders_products_table_tpl; ?></a>
								</div>
							</div>
							<div class="col-lg-6 col-sm-12">
								<div class="panel-body">
									<h5><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <strong><?php echo $entry_adding_codes; ?></strong></h5>
									<div class="callout callout-default"><p><?php echo $adding_codes_mail_info; ?></p></div>
								</div>
							</div>
						</div>
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
						  <label class="col-sm-2 control-label">
							<p><?php echo $entry_order_totals_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_hel2; ?>" class="fa fa-question-circle text-primary"></i></p>
						  </label>
						  <div class="col-sm-10">
							<textarea rows="5" class="htmlTextArea form-control" name="template[total_tpl][<?php echo $language['language_id']; ?>]"><?php echo isset($template['total_tpl'][$language['language_id']]) ? $template['total_tpl'][$language['language_id']] : '<b>{total_name}:</b> {total_value}<br>'; ?></textarea>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_template_mail_subject; ?></label>
						  <div class="col-sm-10 valid-block">
							<div class="input-group">
								<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_mail_subject; ?>"><i class="fa fa-text-width"></i></span>
								<textarea name="template[subject][<?php echo $language['language_id']; ?>]" class="field-input mustbe column-name form-control" rows="1" ><?php echo isset($template['subject'][$language['language_id']]) ? $template['subject'][$language['language_id']] : ''; ?></textarea>
								<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_mail_subject_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
								<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">
								  <input type="checkbox" id="tpl-add<?php echo $language['language_id']; ?>" />
								</span>
								<span class="input-group-btn" style="width: 40%;">
								<select data-add-id="<?php echo $language['language_id']; ?>" class="load-tpl-block form-control multiselect" style="font-weight: normal;">
									<option value="" ><?php echo $text_load_tpl; ?></option>
								  <?php foreach ($mail_blocks as $block) { ?>
									<option value="<?php echo $block['template_id']; ?>" ><?php echo $block['template_id'] .' - '.$block['name']; ?></option>
								  <?php } ?>
								</select>
								</span>
								<span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock('mail', 'language<?php echo $language['language_id']; ?>');"><i class="fa fa-save"></i></a></span>
								<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>
							</div>
						  </div>
						</div>

						<div class="form-group">
						  <label class="col-sm-2 control-label"><?php echo $entry_template_mail_message; ?><br>&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('order');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a>
						  </label>
						  <div class="col-sm-10">
							<textarea class="column-data  form-control textarea-summernote" name="template[message][<?php echo $language['language_id']; ?>]" id="textarea<?php echo $language['language_id']; ?>"><?php echo isset($template['message'][$language['language_id']]) ? $template['message'][$language['language_id']] : ''; ?></textarea>
						  </div>
						</div>
					  </div>
					<?php } ?>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab-setting">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_mail.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_mail_setting_info; ?></p>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_logo_size; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_logo_size_help; ?>" class="fa fa-question-circle text-primary"></i></label>
				<div class="col-sm-3 valid-block">
					<span class="input-group">
					  <span class="input-group-addon"><i class="fa fa-image"></i></span>
					  <input type="text" name="template[logo_width]" value="<?php echo isset($template['logo_width']) && !empty($template['logo_width']) ? $template['logo_width'] : $logo_width; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_width; ?>"/>
					  <input type="text" name="template[logo_height]" value="<?php echo isset($template['logo_height']) && !empty($template['logo_height']) ? $template['logo_height'] : $logo_height; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_height; ?>" />
					</span>
				</div>
				<label class="col-sm-3 control-label"><?php echo $entry_user_image_size; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_user_image_help; ?>" class="fa fa-question-circle text-primary"></i></label>
				<div class="col-sm-4 valid-block">
					<span class="input-group">
					  <span class="input-group-addon"><i class="fa fa-image"></i></span>
					  <input type="text" name="template[user_image_width]" value="<?php echo isset($template['user_image_width']) && !empty($template['user_image_width']) ? $template['user_image_width'] : $user_image_width; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_width; ?>"/>
					  <input type="text" name="template[user_image_height]" value="<?php echo isset($template['user_image_height']) && !empty($template['user_image_height']) ? $template['user_image_height'] : $user_image_height; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_height; ?>" />
					</span>
				</div>
			  </div>

			  <!-- structure -->
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_mail_style; ?><br><a onclick="$('#pre-code-style').slideToggle();" style="font-weight: normal; cursor: pointer;"><?php echo $entry_mail_structure; ?></a></label>
				<div class="col-sm-10">
				  <div class="callout callout-default">
					<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $entry_mail_style_info; ?></p>
				  </div>
				</div>
				<div class="col-sm-12" id="pre-code-style" style="display: none;">
<pre><code class="language-html" data-lang="html"><span class="nt">&lt;body</span> <span class="na">style=</span><span class="s">"..."</span><span class="nt">&gt;</span>
  <span class="nt">&lt;div</span> <span class="na">style=</span><span class="s">"..."</span><span class="nt">&gt;</span>
	Message...
  <span class="nt">&lt;/div&gt;</span>
<span class="nt">&lt;/body&gt;</span>
</code>
</pre>
<style type="text/css" title="ompro">.nt { color: #2f6f9f; }.na { color: #4f9fcf; }.s { color: #d44950; }.cs { color: #c1ab00; }.cc { color: #c50050; }</style>
				</div>
			  </div>

			  <!-- Style -->
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-body"><?php echo $entry_class_body; ?></label>
				<div class="col-sm-10 valid-block">
				  <textarea name="template[style][body]" class="field-input notcyrillics form-control" placeholder="style" id="input-body"><?php echo isset($template['style']['body']) ? $template['style']['body'] : ''; ?></textarea>
				</div>
			  </div>

			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-div"><?php echo $entry_class_div; ?></label>
				<div class="col-sm-10 valid-block">
				  <textarea name="template[style][div]" class="field-input notcyrillics form-control" placeholder="style" id="input-div"><?php echo isset($template['style']['div']) ? $template['style']['div'] : ''; ?></textarea>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab-preview">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_mail.html#tab=0&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_mail_preview_info; ?></p>
			  </div>
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
						<button type="button" id="send-me-email" data-toggle="tooltip" title="<?php echo $button_send_email; ?>" class="btn btn-primary"><i class="fa fa-envelope-o"></i></button>
					  </span>
					</div>
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-sm-12">
					<div class="input-group">
					  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_preview_template_mail_subject; ?>">&nbsp;<i class="fa fa-envelope"></i>&nbsp;</span>
					  <div class="input-group-addon form-control" id="preview-subject" style="padding: 8px 15px 2px 15px; background-color: #f5f5f5;"></div>
					</div>
				</div>
			  </div>
			  <div class="" id="preview-body" style="<?php echo isset($template['style']['body']) ? $template['style']['body'] : ''; ?> width: 80%;">
				<div id="preview-content" style="<?php echo isset($template['style']['div']) ? $template['style']['div'] : ''; ?>"></div>
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

$(document).delegate('#test-comment, #test-order-id', 'change', function() {
	preview();
});

$(document).delegate('#trigger-preview', 'click', function() {
	if ($('#preview-content').html() == '') { preview(); }
});

$('#send-me-email').on('click', function() {
	var template_id = '<?php echo $template_id; ?>';
	if (!template_id || template_id == 0) {
		toastr.options.preventDuplicates = true;
		toastr.error('<?php echo $error_template_preview; ?>', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var order_id = $('#test-order-id').val();
	if (!order_id) {
		toastr.options.timeOut = 5000;
		toastr.error('№ заказа не указан!', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var test_comment = $('#test-comment').val();

	$.ajax({
		url: 'index.php?route=sale/ompro/sendTestMail&<?php echo $strtoken; ?>&order_id=' + order_id + '&template_id=' + template_id + '&test_comment=' +  encodeURIComponent(test_comment),
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
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

function preview() {
	var template_id = '<?php echo $template_id; ?>';
	if (!template_id || template_id == 0) {
		toastr.options.preventDuplicates = true;
		toastr.error('<?php echo $error_template_preview; ?>', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var order_id = $('#test-order-id').val();
	if (!order_id) {
		toastr.options.timeOut = 5000;
		toastr.error('№ заказа не указан!', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var test_comment = $('#test-comment').val();

	$.ajax({
		url: 'index.php?route=sale/ompro/previewMail&<?php echo $strtoken; ?>&order_id=' + order_id + '&template_id=' + template_id + '&test_comment=' + encodeURIComponent(test_comment),
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['subject']) {
				$('#preview-subject').html(json['subject']);
			} else {
				$('#preview-subject').html('<?php echo $text_no_results; ?>');
			}
			if (json['message']) {
				$('#preview-content').html(json['message']);
			} else {
				$('#preview-content').html('<?php echo $text_no_results; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

--></script>

<script type="text/javascript"><!--

$(document).delegate('.load-tpl-block', 'change', function() {
	var template_id = $(this).val();
	if (!template_id) {return;}
	var add_id = $(this).attr('data-add-id');
	var add_status = $('#tpl-add'+add_id).prop('checked') ? 1 : 0;
 	var selected = $('#textarea'+add_id).summernote('createRange');
	var str_length = selected.toString().length;
	$.ajax({
		url: 'index.php?route=sale/ompro/loadTemplateBlock&<?php echo $strtoken; ?>&type=order&template_id=' + template_id,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['tpl']) {
				if (str_length > 0 && add_status) {
					selected.pasteHTML(json['tpl']);
				} else {
					if (!add_status) {
						$('textarea[id=\'textarea' + add_id + '\']').summernote('code', json['tpl']);
					} else {
						$('textarea[id=\'textarea' + add_id + '\']').summernote('editor.pasteHTML', json['tpl']);
					}
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

function saveBlock(target, parent_id) {
	var data = {};
	data['template'] = {};
	data['template']['target'] = target;

	var name = $('#input-name').val();

	if (name == '') {
		toastr.error('<?php echo $text_error_name_template; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	}
	data['template']['name'] = name;
	data['template']['description'] = '';
	var template = $('#'+parent_id + ' .column-data').val();
	if (template == '' || template == '<p><br></p>') {
		toastr.error('<?php echo $text_error_template_block; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	}
	data['template']['template'] = template;

	$.ajax({
		url: 'index.php?route=sale/ompro/saveTemplateBlock&<?php echo $strtoken; ?>',
		data: data,
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['success']) {
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				$('select[class*=\'load-tpl-block\']').each(function(){
					$(this).append('<option value="'+json['template_id']+'">' + json['template_id'] + ' - ' +name+'</option>');
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).delegate('#form, #form input, #form sellect, #form textarea', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

var lang = '<?php echo $summernote_lang; ?>';
var toolbar = [['insert',['picture','link','video','table']], ['style',['bold','italic','underline']], ['font', ['strikethrough', 'superscript', 'subscript']],['fontsize', ['fontsize','fontname']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph','style']], ['height', ['height','codeview']],];
var fontNames = ['Arial','Times New Roman','Helvetica','Verdana'];
var placeholder = '<?php echo $text_summernote_placeholder; ?>';

$(document).ready(function(){
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	$('.textarea-summernote').each(function(){
		$(this).summernote({
			blank: '', emptyPara: '', lang: lang, height:'auto', toolbar: toolbar,fontNames: fontNames, placeholder: placeholder,
			codemirror: { height:'auto', mode: "text/html", theme: 'rubyblue'﻿, lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true },
			callbacks: {
				onChange: function(contents, $editable) {
					if ($('#button_save').hasClass('btn-primary')) {
						$('#button_save').removeClass('btn-primary').addClass('btn-danger');
					}
				}
			}
		});
	});

	$('.htmlTextArea').each(function(){
		var htmlTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "auto", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			htmlTextArea.html(editor.getValue()).trigger('change');
		});
	});

	$(document).on('click', 'a[data-toggle=\'image\']', function(e) {
		var $element = $(this);
		var $popover = $element.data('bs.popover');
		e.preventDefault();
		$('a[data-toggle="image"]').popover('destroy');

		if ($popover) {
			return;
		}

		$element.popover({
			html: true,
			placement: 'right',
			trigger: 'manual',
			title: 'Изображение',
			content: function() {
				return '<a type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></a> <a type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>';
			}
		});

		$element.popover('show');

		$('#button-image').on('click', function() {
			var $button = $(this);
			var $icon   = $button.find('> i');
			$('#modal-image').remove();
			$.ajax({
				url: 'index.php?route=common/filemanager&<?php echo $strtoken; ?>&target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
				dataType: 'html',
				beforeSend: function() {
					$button.prop('disabled', true);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-circle-o-notch fa-spin');
					}
				},
				complete: function() {
					$button.prop('disabled', false);
					if ($icon.length) {
						$icon.attr('class', 'fa fa-pencil');
					}
				},
				success: function(html) {
					$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

					$('#modal-image').modal('show');
				}
			});

			$element.popover('destroy');
		});

		$('#button-clear').on('click', function() {
			$element.find('img').attr('src', $element.find('img').attr('data-placeholder'));
			$element.parent().find('input').val('');
			$element.popover('destroy');
		});
	});

	multiselectStart();
	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
});

//--></script>

</div>
<?php echo $footer; ?>