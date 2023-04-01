<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">
	<section class="content-header">
	  <h1> <?php echo $heading_title; ?></h1>
	  <ol class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
			<?php echo $breadcrumb; ?>
		<?php } ?>
	  </ol>
	</section>

	<section class="content" id="content">
	  <div class="box">
		<div class="box-header with-border">
		  <h3 class="box-title"><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_list.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_list_template; ?></h3>
		  <div class="box-tools pull-right">
			<a href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_template_insert; ?>" class="btn btn-sm btn-success"><i class="fa fa-plus"></i></a>
			<button type="button" data-action="copy" data-toggle="tooltip" title="<?php echo $button_copy; ?>" class="btn btn-sm btn-primary need-selected"><i class="fa fa-copy"></i></button>
			<button type="button" data-action="delete" data-toggle="tooltip" title="<?php echo $button_template_delete; ?>" class="btn btn-sm btn-danger need-selected"><i class="fa fa-trash-o"></i></button>
		  </div>
		</div>
		<div class="box-body">
		  <div class="table-templates table-responsive">
			<form action="" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
			<table class="table table-bordered table-hover">
			  <thead>
				<tr class="active">
				  <th style="width: 60px;" class="text-center">
					<input type="checkbox" class="minimal check-all-selected" title="<?php echo $text_select_all; ?>"/>
				  </th>
				  <th class="text-center" style="width: 80px;">
					<a href="<?php echo $sort_template_id; ?>" class="sort-list <?php echo ($sort == 'template_id' ? strtolower($order) : ''); ?>"><?php echo $column_template_id; ?></a>
				  </th>
				  <th class="text-left" style="width: 120px;">
					<a href="<?php echo $sort_code; ?>" class="sort-list <?php echo ($sort == 'code' ? strtolower($order) : ''); ?>"><?php echo $column_template_code; ?></a>
				  </th>
				  <?php if ($get_page == 'filter' || $get_page == 'filter_product') { ?>
				  <th class="text-left" style="min-width: 160px;">
					<a href="<?php echo $sort_filter_id; ?>" class="sort-list <?php echo ($sort == 'filter_id' ? strtolower($order) : ''); ?>"><?php echo $column_filter_id; ?></a>
				  </th>
				  <?php } ?>
				  <th class="text-left" style="min-width: 200px;">
					<a href="<?php echo $sort_name; ?>" class="sort-list <?php echo ($sort == 'name' ? strtolower($order) : ''); ?>"><?php echo $column_template_name; ?></a>
				  </th>
				  <th class="text-left"><?php echo $column_template_description; ?></th>
				  <th class="text-left" style="min-width: 160px;">
					<a href="<?php echo $sort_date_added; ?>" class="sort-list <?php echo ($sort == 'date_added' ? strtolower($order) : ''); ?>"><?php echo $column_date_added; ?></a>
				  </th>
				  <th class="text-left" style="min-width: 160px;">
					<a href="<?php echo $sort_date_modified; ?>" class="sort-list <?php echo ($sort == 'date_modified' ? strtolower($order) : ''); ?>"><?php echo $column_date_modified; ?></a>
				  </th>
				  <th class="text-right" style="width: 80px;"><?php echo $column_action; ?></th>
				</tr>
			  </thead>
			  <tbody>
				<tr id="filters">
				  <td></td>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" name="filter_template_id" value="<?php echo $filter_template_id; ?>" class="filter-tpl form-control" />
					</div>
				  </td>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" name="filter_code" value="<?php echo $filter_code; ?>" class="filter-tpl form-control" />
					</div>
				  </td>
				  <?php if ($get_page == 'filter' || $get_page == 'filter_product') { ?>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" name="filter_filter_id" value="<?php echo $filter_filter_id; ?>" class="filter-tpl form-control" />
					</div>
				  </td>
				  <?php } ?>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" class="filter-tpl form-control" />
					</div>
				  </td>
				  <td></td>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" id="filter_date_added" name="filter_date_added" value="<?php echo $filter_date_added; ?>" class="datepicker_default filter-tpl form-control" />
					</div>
				  </td>
				  <td class="text-left">
					<div class="input-group input-group-sm full-width">
						<input type="text" id="filter_date_modified" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" class="datepicker_default filter-tpl form-control" />
					</div>
				  </td>
				  <td class="text-right">
					<div class="btn-group btn-group-sm">
						<button type="button" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_clear_all; ?>" onclick="$('.filter-tpl').val('').trigger('change');"><i class="fa fa-times-circle"></i></button>
						<button type="button" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $button_apply_filter; ?>" onclick="$('.filter-tpl').trigger('change');"><i class="fa fa-filter"></i></button>
					</div>
				  </td>
				</tr>

				<?php if ($templates) { ?>
				  <?php foreach ($templates as $template) { ?>
					<tr>
					  <td class="text-center">
						<input type="checkbox" name="selected[]" value="<?php echo $template['template_id']; ?>" class="minimal"/>
					  </td>
					  <td class="text-center"><?php echo $template['template_id']; ?></td>
					  <td class="text-left"><?php echo $template['code']; ?></td>
					  <?php if ($get_page == 'filter' || $get_page == 'filter_product') { ?>
					  <td class="text-left"><?php echo $template['filter_id']; ?></td>
					  <?php } ?>
					  <td class="text-left"><?php echo $template['name']; ?></td>
					  <td class="text-left"><?php echo $template['description']; ?></td>
					  <td class="text-left"><?php echo $template['date_added']; ?></td>
					  <td class="text-left"><?php echo $template['date_modified']; ?></td>
					  <td class="text-right">
						<div class="btn-group btn-group-sm"><a href="<?php echo $template['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_template_edit; ?>" class="btn btn-primary"><i class="fa fa-edit"></i></a></div>
					  </td>
					</tr>
				  <?php } ?>
				<?php } else { ?>
				  <tr>
					<td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
				  </tr>
				<?php } ?>
			  </tbody>
			</table>
			</form>
		  </div>
		  <div class="row">
			<div class="col-sm-6 text-left pagination-sm"><?php echo $pagination; ?></div>
			<div class="col-sm-6 text-right pagination_results"><?php echo $pagination_results; ?></div>
		  </div>
		</div>
	  </div>
	</section>

<script type="text/javascript"><!--

$(document).delegate('.filter-tpl', 'change', function() {
	url = 'index.php?route=sale/ompro/templateList&<?php echo $strtoken; ?>&get_page=<?php echo $get_page . $block_target; ?>';

	var filter_template_id = $('input[name=\'filter_template_id\']').val();
	if (filter_template_id) {
		url += '&filter_template_id=' + encodeURIComponent(filter_template_id);
	}

	var filter_code = $('input[name=\'filter_code\']').val();
	if (filter_code) {
		url += '&filter_code=' + encodeURIComponent(filter_code);
	}

	var filter_filter_id = $('input[name=\'filter_filter_id\']').val();
	if (filter_filter_id) {
		url += '&filter_filter_id=' + encodeURIComponent(filter_filter_id);
	}

	var filter_name = $('input[name=\'filter_name\']').val();
	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_date_added = $('input[name=\'filter_date_added\']').val();
	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
	if (filter_date_modified) {
		url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
	}

	$('#content-omanager').load(url + ' #content-omanager > *', function() {
		iCheckStart();
		dpStart();
	});
});

var has_permission = '<?php echo $has_permission; ?>';

$(document).delegate('.need-selected', 'click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var selected = $('input[name^=\'selected\']:checked');
	if (!selected.length) {
		toastr.warning('<?php echo $text_alert_not_selected; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var action = $(this).attr('data-action');

	if (action == 'delete') {
		if (!confirm('<?php echo $confirm_template_delete; ?>')) { return false; }
		var url = '<?php echo $delete; ?>';
		url = url.replace(/&amp;/g, "&");
	}

	if (action == 'copy') {
		var url = '<?php echo $copy; ?>';
		url = url.replace(/&amp;/g, "&");
	}

	$('#form').attr('action', url);
	$('#form').attr('target', '_self');
	$('#form').submit();
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

	iCheckStart();
	dpStart();
	tooltipRefresh();
});

//--></script>

</div>
<?php echo $footer; ?>