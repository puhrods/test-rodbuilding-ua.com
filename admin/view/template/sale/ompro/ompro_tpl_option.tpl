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
					<textarea name="template[description]" rows="1" class="form-control"><?php echo isset($template['description']) ? $template['description'] : ''; ?></textarea>
				</span>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-2 box-tools pull-right text-right">
			<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>

	<div class="box-body">
	  <div class="row">
		<div class="col-sm-12">
		  <div class="callout callout-default">
			<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_option.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_option_value_info; ?></p>
		  </div>
		</div>
	  </div>
	  <div class="panel panel-default">
		<table id="option-value" class="table table-bordered table-hover">
			<thead>
				<tr class="active">
				  <th class="text-left" style="width: 80px;"><?php echo $entry_option_value; ?></th>
				  <th class="text-left"><?php echo $entry_option_name; ?></th>
				  <th class="text-left"><?php echo $entry_sort_order; ?></th>
				  <th style="width: 50px;"></th>
				</tr>
			</thead>
			<tbody class="">
			  <?php $option_value_row = 0; ?>
			  <?php if (!empty($template['option_value'])) { ?>
			  <?php foreach ($template['option_value'] as $option_value) { ?>
			  <tr id="option-value-row<?php echo $option_value_row; ?>">
				<td class="text-center"><?php echo $option_value['option_value_id']; ?></td>
				<td class="text-left">
				  <input type="hidden" name="template[option_value][<?php echo $option_value_row; ?>][option_value_id]" value="<?php echo $option_value['option_value_id']; ?>" />
				  <?php foreach ($languages as $language) { ?>
				  <div class="input-group valid-block">
					<span class="input-group-addon">
					  <?php if ($ocversion >= 220) { ?>
						<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png"/>
					  <?php } else { ?>
						<img src="view/image/flags/<?php echo $language['image']; ?>"/>
					  <?php } ?>
					</span>
					<input type="text" name="template[option_value][<?php echo $option_value_row; ?>][option_value_description][<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($option_value['option_value_description'][$language['language_id']]) ? $option_value['option_value_description'][$language['language_id']]['name'] : ''; ?>" placeholder="значение" class="field-input mustbe form-control" />
				  </div>
				  <?php } ?>
				</td>
				<td class="text-right">
					<div class="input-group valid-block fullwidth">
						<input type="text" name="template[option_value][<?php echo $option_value_row; ?>][sort_order]" value="<?php echo $option_value['sort_order']; ?>" class="field-input digit form-control" />
					</div>
				</td>
				<td class="text-left">
					<button type="button" onclick="deleteOptionValue('option-value-row<?php echo $option_value_row; ?>');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
				</td>
			  </tr>
			  <?php $option_value_row++; ?>
			  <?php } ?>
			  <?php } ?>
			</tbody>
			<tfoot>
			  <tr>
				<td colspan="3"></td>
				<td class="text-left"><button type="button" onclick="addOptionValue();" data-toggle="tooltip" title="<?php echo $button_option_value_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
			  </tr>
			</tfoot>
		</table>
	  </div>

	</div>
  </div>
</form>
</section>

<script type="text/javascript"><!--

function deleteOptionValue(option_value_row, op_new = false) {
	if (!op_new && !confirm('<?php echo $confirm_option_value_delete; ?>')) { return false; }
	$('#'+option_value_row).remove();
	tooltipRefresh();
}

var option_value_row = <?php echo $option_value_row; ?>;

function addOptionValue() {
	html  = '<tr id="option-value-row' + option_value_row + '">';
	html += '  <td class="text-center"></td>';
	html += '  <td class="text-left"><input type="hidden" name="template[option_value][' + option_value_row + '][option_value_id]" value="0" />';
	<?php foreach ($languages as $language) { ?>
	html += '    <div class="input-group">';
	html += '      <span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span><input type="text" name="template[option_value][' + option_value_row + '][option_value_description][<?php echo $language['language_id']; ?>][name]" value="" placeholder="Название" class="form-control" />';
	html += '    </div>';
	<?php } ?>
	html += '  </td>';
	html += '  <td class="text-right"><input type="text" name="template[option_value][' + option_value_row + '][sort_order]" value="" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" /></td>';
	html += '  <td></td>';
	html += '  <td class="text-left"><button type="button" onclick="deleteOptionValue(\'option-value-row' + option_value_row + '\', 1);" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';

	html += '</tr>';

	$('#option-value tbody').append(html);

	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh();

	option_value_row++;
}

--></script>

<script type="text/javascript"><!--

var has_permission = '<?php echo $has_permission; ?>';

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

	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
});
//--></script>

</div>
<?php echo $footer; ?>