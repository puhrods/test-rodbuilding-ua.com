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
            <li><a href="#tab-template" data-toggle="tab"><?php echo $tab_template_history; ?></a></li>
            <li><a href="#tab-setting" data-toggle="tab"><?php echo $tab_template_print_setting; ?></a></li>
			<li><a href="#tab-preview" data-toggle="tab" id="trigger-preview"><?php echo $tab_template_preview; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-template">
			  <div class="nav-tabs-custom">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_history.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_template_history_info; ?></p>
				</div>

				<div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#var-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_print_vars_block_title; ?></a></div>
					<div id="var-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="row">
							<div class="col-lg-6 table-responsive"><?php echo $history_vars_table_add; ?></div>
							<div class="col-lg-6 table-responsive"><?php echo $history_vars_table; ?></div>
						</div>
					  </div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#tpl-container"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_history_container_heading; ?></a></div>
					<div id="tpl-container" class="panel-collapse collapse in">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-2 control-label">
								<p><?php echo $entry_history_container; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_history_container_help; ?>" class="fa fa-question-circle text-blue"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_history_container_help2; ?>" class="fa fa-question-circle text-red"></i>
								</p>
							</label>
							<div class="col-sm-10">
							  <textarea name="template[container]" rows="10" id="textarea-container" class="htmlTextArea form-control textarea-summernote"><?php echo isset($template['container']) ? $template['container'] : '<div style="width: 100%;">{history_template}</div>'; ?></textarea>
							</div>
						  </div>
						</div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#tpl-template"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_history_template_heading; ?></a></div>
					<div id="tpl-template" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="form-group">
							  <label class="col-sm-2 control-label"><?php echo $entry_history_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_history_template_help; ?>" class="fa fa-question-circle text-blue"></i></label>
							  <div class="col-sm-10">
								<textarea class="htmlTextArea form-control textarea-summernote" name="template[template]" id="textarea"><?php echo isset($template['template']) ? $template['template'] : ''; ?></textarea>
							  </div>
							</div>
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab-setting">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_history.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_setting_history_info; ?></p>
			  </div>
			  <div class="form-group">
				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_history_limit; ?></label>
				  <div class="valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
						<input type="text" name="template[limit]" value="<?php echo isset($template['limit']) ? $template['limit'] : 10; ?>" placeholder="<?php echo $entry_history_limit; ?>" class="setdata field-input digitmustbe form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_history_limit_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_orderdefault; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-sort-amount-asc"></i></span>
					<select name="template[orderdefault]" class="setdata form-control">
						<option value="DESC" <?php if (isset($template['orderdefault']) && $template['orderdefault'] == 'DESC') { ?>selected="selected"<?php } ?>><?php echo $text_sort_desc; ?></option>
						<option value="ASC" <?php if (isset($template['orderdefault']) && $template['orderdefault'] == 'ASC') { ?>selected="selected"<?php } ?>><?php echo $text_sort_asc; ?></option>
					</select>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_date_format ?></label>
				  <div class="valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
						<input type="text" name="template[date_format]" value="<?php echo isset($template['date_format']) ? $template['date_format'] : 'd.m.Y H:i:s'; ?>" placeholder="d.m.Y H:is" class="setdata field-input notcyrillicsmustbe form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_date_format_info; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_notify_email; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
					<select name="template[notify_email]" class="setdata form-control">
						<option value="2" <?php if (isset($template['notify_email']) && $template['notify_email'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_notify_all; ?></option>
						<option value="1" <?php if (isset($template['notify_email']) && $template['notify_email'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_notify_yes; ?></option>
						<option value="0" <?php if (isset($template['notify_email']) && $template['notify_email'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_notify_no; ?></option>
					</select>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_notify_email_info; ?>"><i class="fa fa-info text-blue"></i></span>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_notify_sms; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
					<select name="template[notify_sms]" class="setdata form-control">
						<option value="2" <?php if (isset($template['notify_sms']) && $template['notify_sms'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_notify_all; ?></option>
						<option value="1" <?php if (isset($template['notify_sms']) && $template['notify_sms'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_notify_yes; ?></option>
						<option value="0" <?php if (isset($template['notify_sms']) && $template['notify_sms'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_notify_no; ?></option>
					</select>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_notify_sms_info; ?>"><i class="fa fa-info text-blue"></i></span>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_notify_tlgrm; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-envelope-o"></i></span>
					<select name="template[notify_tlgrm]" class="setdata form-control">
						<option value="2" <?php if (isset($template['notify_tlgrm']) && $template['notify_tlgrm'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_notify_all; ?></option>
						<option value="1" <?php if (isset($template['notify_tlgrm']) && $template['notify_tlgrm'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_notify_yes; ?></option>
						<option value="0" <?php if (isset($template['notify_tlgrm']) && $template['notify_tlgrm'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_notify_no; ?></option>
					</select>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_notify_tlgrm_info; ?>"><i class="fa fa-info text-blue"></i></span>
				  </div>
				</div>
			  </div>

			  <div class="form-group">

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_comment_exist; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-comments-o"></i></span>
					<select name="template[comment_exist]" class="setdata form-control">
						<option value="0" <?php if (isset($template['comment_exist']) && $template['comment_exist'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_exist_all; ?></option>
						<option value="1" <?php if (isset($template['comment_exist']) && $template['comment_exist'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_exist_yes; ?></option>
						<option value="2" <?php if (isset($template['comment_exist']) && $template['comment_exist'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_exist_no; ?></option>
					</select>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_log_exist; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
					<select name="template[log_exist]" class="setdata form-control">
						<option value="0" <?php if (isset($template['log_exist']) && $template['log_exist'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_exist_all; ?></option>
						<option value="1" <?php if (isset($template['log_exist']) && $template['log_exist'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_exist_yes; ?></option>
						<option value="2" <?php if (isset($template['log_exist']) && $template['log_exist'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_exist_no; ?></option>
					</select>
				  </div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_file_name_exist; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-file-image-o"></i></span>
					<select name="template[file_name_exist]" class="setdata form-control">
						<option value="0" <?php if (isset($template['file_name_exist']) && $template['file_name_exist'] == '0') { ?>selected="selected"<?php } ?>><?php echo $text_exist_all; ?></option>
						<option value="1" <?php if (isset($template['file_name_exist']) && $template['file_name_exist'] == '1') { ?>selected="selected"<?php } ?>><?php echo $text_exist_yes; ?></option>
						<option value="2" <?php if (isset($template['file_name_exist']) && $template['file_name_exist'] == '2') { ?>selected="selected"<?php } ?>><?php echo $text_exist_no; ?></option>
					</select>
				  </div>
				</div>

			  </div>

			  <div class="form-group">
				<div class="col-sm-4">
					<label class="control-label" for="input-user-image"><?php echo $entry_user_image_size; ?></label>
					<div class="valid-block">
						<span class="input-group">
						  <span class="input-group-addon" data-toggle="tooltip" title="Ширина"><i class="fa fa-image"></i></span>
						  <input type="text" name="template[user_image_width]" value="<?php echo isset($template['user_image_width']) && !empty($template['user_image_width']) ? $template['user_image_width'] : 32; ?>" class="field-input digitmustbe form-control" style="max-width: calc (50% - 16px);" placeholder="32"/>
						  <span class="input-group-addon" data-toggle="tooltip" title="Высота"><i class="fa fa-image"></i></span>
						  <input type="text" name="template[user_image_height]" value="<?php echo isset($template['user_image_height']) && !empty($template['user_image_height']) ? $template['user_image_height'] : 32; ?>" class="field-input digitmustbe form-control" style="max-width: calc (50% - 16px);" placeholder="32>" />
						</span>
					</div>
				</div>

				<div class="col-sm-4">
				  <label class="control-label"><?php echo $entry_history_users_class; ?></label>
				  <div class="valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-users"></i></span>
						<input type="text" name="template[users_class]" value="<?php echo isset($template['users_class']) ? $template['users_class'] : ''; ?>" placeholder="right" class="setdata field-input validclass form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_history_users_class_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

			  </div>
			</div>

			<div class="tab-pane" id="tab-preview">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_history.html#tab=0&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_preview_history_info; ?></p>
				</div>

				<div class="form-group">
				  <div class="col-sm-3 valid-block">
					<div class="input-group">
					  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_last_order; ?>">&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;</span>
					  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_print_select_last_order_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
					  <input type="text"  value="<?php echo $last_order; ?>" id="test-order-id" class="field-input digit mustbe form-control" placeholder="<?php echo $entry_select_last_order_placeholder; ?>"/>
					  <span class="input-group-btn">
						<a id="btn-preview" data-toggle="tooltip" title="<?php echo $button_history_preview; ?>" class="btn btn-primary"><i class="fa fa-eye"></i></a>
					  </span>
					</div>
				  </div>
				  <div class="col-sm-9">
					<div class="panel panel-default"><div class="panel-body" id="preview"></div></div>
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

$(document).delegate('#btn-preview', 'click', function() {
	var template_id = '<?php echo $template_id; ?>';
	var order_id = $('#test-order-id').val();
	if (template_id && order_id) {
		var url = '<?php echo $preview; ?>';
		url = url.replace(/&amp;/g, "&");
		url += '&&history_page=true&history_template_id=' + template_id + '&order_id=' + order_id;
		$('#preview').load(url, function() {
			toastr.success('Данные обновлены!', '<?php echo $text_alert_success; ?>');
			tooltipRefresh();
		});
	}
});

$(document).delegate('#test-order-id', 'change', function() {
	$('#btn-preview').trigger('click');
});

$(document).delegate('#trigger-preview', 'click', function() {
	$('#btn-preview').trigger('click');
});

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
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "150px", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			htmlTextArea.html(editor.getValue()).trigger('change');
		});
	});

	multiselectStart();
	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
});

$("form").bind("keypress", function (e) {
    if (e.keyCode == 13) { return false; }
});

//--></script>

</div>
<?php echo $footer; ?>