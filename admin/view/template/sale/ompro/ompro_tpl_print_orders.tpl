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
            <li><a href="#tab-setting" data-toggle="tab"><?php echo $tab_template_print_setting; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-template">
			  <div class="nav-tabs-custom">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_print_orders.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_template_print_info; ?></p>
				</div>
				<div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#var-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_print_vars_block_title; ?></a></div>
					<div id="var-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<a class="btn btn-default btn-block" onclick="getTableCodes('product_table');"><i class="fa fa-th-list"></i>&nbsp;  <?php echo $btn_product_table_tpl; ?></a>
							</div>
							<div class="col-lg-6 col-sm-12">
								<h5><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <strong><?php echo $entry_adding_codes; ?></strong></h5>
								<div class="callout callout-default"><p><?php echo $adding_codes_print_orders_info; ?></p></div>
							</div>
						</div>
					  </div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-option"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_order_totals_heading; ?></a></div>
					<div id="set-option" class="panel-collapse collapse">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-2 control-label">
								<p><?php echo $entry_order_totals_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_hel2; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-10">
							  <textarea name="template[total_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['total_tpl']) ? $template['total_tpl'] : '<b>{total_name}:</b> {total_value}<br>'; ?></textarea>
							</div>
						  </div>
						</div>
					</div>
				  </div>
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#tpl-template"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_print_template; ?></a></div>
					<div id="tpl-template" class="panel-collapse collapse in">
						<div class="panel-body">
							<div class="form-group">
							  <label class="col-sm-2 control-label"><?php echo $entry_print_preview; ?></label>
							  <div class="col-sm-10 valid-block">
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
							  <label class="col-sm-2 control-label"><?php echo $entry_print_template_data; ?><br>&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('order');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a>
							  </label>
							  <div class="col-sm-10">
								<textarea class="form-control htmlTextArea" name="template[template]" id="textarea"><?php echo isset($template['template']) ? $template['template'] : ''; ?></textarea>
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
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_print_orders.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_setting_print_info; ?></p>
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
		url += '&order_id=' + order_id;
		window.open(url, '_blank'); return false;
	}
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