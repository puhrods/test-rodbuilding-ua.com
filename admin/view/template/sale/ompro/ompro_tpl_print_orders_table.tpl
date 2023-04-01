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
            <li><a href="#tab-template" data-toggle="tab"><?php echo $tab_template_print; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-template">
			  <div class="nav-tabs-custom">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_print_orders_table.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_template_print_orders_table_info; ?></p>
				</div>
				<div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#var-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_print_vars_block_title; ?></a></div>
					<div id="var-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="row">
							<div class="col-lg-12 table-responsive"><?php echo $orders_tables; ?></div>
						</div>
					  </div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#tpl-template"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_print_template; ?></a></div>
					<div id="tpl-template" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="form-group">
							  <label class="col-sm-2 control-label"><?php echo $entry_print_load_block; ?></label>
							  <div class="col-sm-4 valid-block">
								<div class="input-group">
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">
									  <input type="checkbox" id="tpl-add" />
									</span>
									<span class="input-group-btn" style="width: 40%;">
									<select class="load-tpl-block form-control multiselect" style="font-weight: normal;">
										<option value="" ><?php echo $text_load_tpl; ?></option>
									  <?php foreach ($print_blocks as $block) { ?>
										<option value="<?php echo $block['template_id']; ?>" ><?php echo $block['template_id'] .' - '.$block['name']; ?></option>
									  <?php } ?>
									</select>
									</span>
									<span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock('print_orders_table');"><i class="fa fa-save"></i></a></span>
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>
								</div>
							  </div>
							  <label class="col-sm-2 control-label"><?php echo $entry_print_preview; ?></label>
							  <div class="col-sm-4 valid-block">
								<div class="input-group">
								  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_multi_orders; ?>">&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;</span>
								  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_print_select_last_order_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
								  <input type="text"  value="<?php echo $last_order; ?>" id="test-order-id" class="field-input digitovercoma form-control" placeholder="<?php echo $entry_select_last_order_placeholder; ?>"/>
								  <span class="input-group-btn">
									<a id="print-preview" data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-print"></i>&nbsp;&nbsp;<?php echo $button_print_preview; ?></a>
								  </span>
								  <span class="input-group-btn">
									<a id="pdf-preview" data-toggle="tooltip" title="" class="btn btn-default"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;<?php echo $button_pdf_preview; ?></a>
								  </span>
								</div>
							  </div>
							</div>
							<div class="form-group">
							  <label class="col-sm-2 control-label"><?php echo $entry_print_template_data; ?></label>
							  <div class="col-sm-10">
								<textarea class="form-control textarea-summernote" name="template[template]" id="textarea"><?php echo isset($template['template']) ? $template['template'] : '<div style="width: 100%;"></div>'; ?></textarea>
							  </div>
							</div>
						</div>
					</div>
				  </div>
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

$(document).delegate('#print-preview, #pdf-preview', 'click', function() {
	var url = '<?php echo $preview; ?>';
	url = url.replace(/&amp;/g, "&");
	if ( $(this).attr('id') == 'pdf-preview') {
		url += '&return_type=view_pdf';
	}
	var template_id = '<?php echo $template_id; ?>';
	var order_id = $('#test-order-id').val();
	if (template_id && order_id) {
		url += '&template_id=' + template_id + '&order_id=' + order_id;
		window.open(url, '_blank'); return false;
	}
});

$(document).delegate('.load-tpl-block', 'change', function() {
	var template_id = $(this).val();
	if (!template_id) {return;}
	var add_status = $('#tpl-add').prop('checked') ? 1 : 0;
 	var selected = $('#textarea').summernote('createRange');
	var str_length = selected.toString().length;
	$.ajax({
		url: 'index.php?route=sale/ompro/loadTemplateBlock&<?php echo $strtoken; ?>&template_id=' + template_id,
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
						$('textarea[id=\'textarea\']').summernote('code', json['tpl']);
					} else {
						$('textarea[id=\'textarea\']').summernote('editor.pasteHTML', json['tpl']);
					}
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

function saveBlock(target) {
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
	var template = $('#textarea').val();
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
				$('.multiselect').multiselect('destroy');
				$('.multiselect').append('<option value="'+json['template_id']+'">' + json['template_id'] + ' - ' +name+'</option>');
				multiselectStart();
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
		var height = '280px';
		if ($(this).attr('id') == 'textarea-container') { var height = '150px'; }
		$(this).summernote({
			blank: '', emptyPara: '', lang: lang, height: height, toolbar: toolbar,fontNames: fontNames, placeholder: placeholder,
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