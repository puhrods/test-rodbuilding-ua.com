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
	<small><?php echo $heading_title_small; ?></small>
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
		  <div class="col-sm-10 box-tools pull-left">
			<div class="col-sm-5">
			  <div class="form-group valid-block">
				<span class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_code; ?>"><?php echo $text_tpl_code; ?></div>
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_name; ?>"><i class="fa fa-text-width"></i></div>
					<input type="text" id="input-name" name="template[name]" value="<?php echo isset($template['name']) ? $template['name'] : $text_new_template; ?>" class="field-input mustbe form-control" />
				</span>
			  </div>
			</div>
			<div class="col-sm-7">
			  <div class="form-group">
				<span class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_description; ?>"><i class="fa fa-info"></i></div>
					<textarea name="template[description]" id="input-description" rows="1" class="form-control"><?php echo isset($template['description']) ? $template['description'] : ''; ?></textarea>
				</span>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-2 box-tools pull-right text-right">
			<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i> &nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>

	<div class="box-body box-content hidden-default">
		<div class="callout callout-default">
			<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/<?php echo $doc_page; ?>"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_template_block_info; ?></p>
		</div>
		<div class="nav-tabs-custom">
		  <ul id="nav-tabs" class="nav nav-tabs">
            <li><a href="#tab-template" data-toggle="tab"><?php echo $text_tab_template_block; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
			<div class="tab-pane" id="tab-template">
				<div class="panel">
					<textarea name="template[template]" rows="4" class="textarea-summernote form-control"><?php echo isset($template['template']) ? $template['template'] : ''; ?></textarea>
					<input type="hidden" name="template[target]" value="<?php echo $block_target; ?>"/>
				</div>
				<div class="panel panel-default">
					<?php echo $html_codes_panel_heading; ?>
					<?php echo $html_codes_panel_body; ?>
				</div>
				<?php if ($block_target == 'order') { ?>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#btn-action-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_btn_action_vars_heading; ?></a></div>
					<div id="btn-action-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="callout callout-default">
							<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $table_btn_action_orders_info; ?></p>
						</div>

						<div class="panel-group">

						  <div class="panel panel-default">
							<div class="panel-heading"><a data-toggle="collapse" href="#btn-print-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_btn_action_print_vars_heading; ?></a></div>
							<div id="btn-print-info" class="panel-collapse collapse">
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_print_orders_example; ?>
									<?php echo $table_btn_print_orders; ?>
									</p>
								  </div>
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_print_orders_table_example; ?>
									<?php echo $table_btn_print_orders_table; ?></p>
								  </div>
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_print_products_example; ?>
									<?php echo $table_btn_print_products_table; ?></p>
								  </div>
								</div>
							  </div>
							</div>
						  </div>

						  <div class="panel panel-default">
							<div class="panel-heading"><a data-toggle="collapse" href="#btn-excel-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_btn_action_excel_vars_heading; ?></a></div>
							<div id="btn-excel-info" class="panel-collapse collapse">
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_excel_orders_example; ?>
									<?php echo $table_btn_excel_orders; ?>
									</p>
								  </div>
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_excel_orders_products_example; ?>
									<?php echo $table_btn_excel_orders_products; ?>
									</p>
								  </div>
								</div>
							  </div>
							</div>
						  </div>

						  <div class="panel panel-default">
							<div class="panel-heading"><a data-toggle="collapse" href="#btn-notify-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_btn_action_notify_vars_heading; ?></a></div>
							<div id="btn-notify-info" class="panel-collapse collapse">
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_send_mail_example; ?>
									<?php echo $table_btn_send_mail; ?>
									</p>
								  </div>
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_send_sms_example; ?>
									<?php echo $table_btn_send_sms; ?>
									</p>
								  </div>
								  <div class="col-sm-4" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_send_tlgrm_example; ?>
									<?php echo $table_btn_send_tlgrm; ?>
									</p>
								  </div>
								</div>
							  </div>
							</div>
						  </div>

						  <div class="panel panel-default">
							<div class="panel-heading"><a data-toggle="collapse" href="#btn-openwindow-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_btn_action_adding_vars_heading; ?></a></div>
							<div id="btn-openwindow-info" class="panel-collapse collapse">
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p>
										<?php echo $table_btn_openwindow; ?>
										<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_openwindow_example; ?>
									</p>
								  </div>
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p>
										<?php echo $table_btn_action_adding; ?>
										<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_action_adding_example; ?>
									</p>
								  </div>
								</div>
								<div class="row">
								  <div class="col-sm-12" style="max-height: 360px; overflow: auto;">
									<p><?php echo $table_btn_action_quick_status; ?></p>
								  </div>
								</div>
							  </div>
							</div>
						  </div>

						</div>

					  </div>
					</div>
				  </div>

				<?php } ?>
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
		toastr.error('<?php echo $text_error_form; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	} else {
		var url = '<?php echo $action; ?>';
		url = url.replace(/&amp;/g, "&");
		$('#form').attr('action', url).submit();
	}
});

$(document).delegate('#form', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});


var lang = '<?php echo $summernote_lang; ?>';
var toolbar = [['insert',['picture','link','video','table']], ['style',['bold','italic','underline']], ['font', ['strikethrough', 'superscript', 'subscript']],['fontsize', ['fontsize','fontname']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph','style']], ['height', ['height','codeview']],];
var fontNames = ['Arial','Times New Roman','Helvetica','Verdana'];
var placeholder = '<?php echo $text_summernote_placeholder; ?>';

$(document).ready(function(){
	$('#block_type').trigger('change');
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	$('.textarea-summernote').summernote({
		blank: '', emptyPara: '', lang: lang, height: 'auto', toolbar: toolbar,fontNames: fontNames, placeholder: placeholder,
		codemirror: { height:'auto', mode: "text/html", theme: 'rubyblue'﻿, lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true },
		callbacks: {
			onChange: function(contents, $editable) {
				if ($('#button_save').hasClass('btn-primary')) {
					$('#button_save').removeClass('btn-primary').addClass('btn-danger');
				}
			}
		}
	});

	$('#button_save').removeClass('btn-danger').addClass('btn-primary');
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

	$('.box-content').show();
});
//--></script>

</div>
<?php echo $footer; ?>