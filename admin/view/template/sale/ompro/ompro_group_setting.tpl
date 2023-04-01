<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">
<!--
@author	Konstantin Kornelyuk
@link https://opencartforum.com/user/28448-brest001/?tab=idm
-->

<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/bootstrap-checkbox.min.js"></script>
<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/i18n/ru.js"></script>

<script type="text/javascript" src="view/javascript/ompro/jscolor.js"></script>
<script src="view/javascript/ompro/Sortable.js"></script>

<?php if ($user_group_id) { ?>

<section class="content-header">
  <h1><?php echo $heading_title; ?> <small><?php echo $user_group_name; ?></small></h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section class="content setting-pages">

<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
  <div class="box box-default">
	<div class="box-header with-border">
		<div class="row">
		  <div class="col-sm-6 box-tools pull-right text-right">
			<div class="btn-group">
				<button id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></button>
				<button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-right" role="menu">
					<h6 class="dropdown-header"><?php echo $heading_title; ?></h6>
					<li><a href="#" id="default_setting"><i class="fa fa-undo"></i>&nbsp;&nbsp;<?php echo $button_default_setting; ?></a></li>
				</ul>
			</div>
			<button type="button" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default" onclick="location='<?php echo $cancel; ?>';"><i class="fa fa-reply"></i></button>
		  </div>
		</div>
	</div>

	<div class="box-body">

      <div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li><a href="#tab_access" data-toggle="tab"><i class="fa fa-check"></i>&nbsp;&nbsp;<?php echo $text_tab_target_access; ?></a></li>
			<li><a href="#tab_select_pages" data-toggle="tab"><i class="fa fa-files-o"></i>&nbsp;&nbsp;<?php echo $text_tab_select_pages; ?></a></li>
			<li><a href="#tab_methods_list" data-toggle="tab"><i class="fa fa-list"></i>&nbsp;&nbsp;<?php echo $text_tab_methods_list; ?></a></li>
			<li><a href="#tab_select_orders" role="tab" data-toggle="tab"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;<?php echo $text_tab_select_orders; ?></a></li>
			<li><a href="#tab_edit_colors" data-toggle="tab"><i class="fa fa-adjust"></i>&nbsp;&nbsp;<?php echo $text_tab_edit_colors; ?></a></li>
			<li><a href="#tab_filters_default" role="tab" data-toggle="tab"><i class="fa fa-filter"></i>&nbsp;&nbsp;<?php echo $text_tab_filters_default; ?></a></li>
			<li><a href="#tab_order_formats" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<?php echo $text_tab_order_formats; ?></a></li>
			<li><a href="#tab_product_formats" role="tab" data-toggle="tab"><i class="fa fa-tag"></i>&nbsp;&nbsp;<?php echo $text_tab_product_formats; ?></a></li>
			<li class=""><a href="#tab_comment_status_template" role="tab" data-toggle="tab"><i class="fa fa-cc"></i>&nbsp;&nbsp;<?php echo $text_tab_comment_status_template; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
		</ul>

		<div class="tab-content">
		  <div class="tab-pane" id="tab_access">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_access_help; ?></p>
			</div>
			  <div class="row">
				<div class="col-sm-4">
					<table class="table-mini  full-width">
					  <thead>
						<tr>
							<th><b><?php echo $entry_user_group_target; ?></b>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $entry_user_group_target_help; ?>"></i></th>
						</tr>
					  </thead>
					  <tbody>
						<?php foreach ($user_group_targets as $target) { ?>
						<tr>
							<td>
								<label>
									<input type="radio" class="minimal" name="settings[group_target]" value="<?php echo $target['value']; ?>" <?php if ($settings['group_target'] == $target['value']) { ?> checked="checked" <?php } ?>/>&nbsp;&nbsp; <?php echo $target['text']; ?>
								</label>
							</td>
						</tr>
						<?php } ?>
					  </tbody>
					</table>
				</div>
				<div class="col-sm-8">
					<div class="table-responsive">
						<table class="table-mini table-hover full-width">
						  <thead>
							  <tr>
								<th><?php echo $text_access_order_table_title; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_access_order_info; ?>"></i></th>
								<th class="text-center" style="width: 100px;"><?php echo $text_as_status; ?> </th>
							  </tr>
						  </thead>
						  <tbody>
							<?php foreach ($access_actions as $act) { ?>
							  <tr>
								<td><?php echo $act['name']; ?></td>
								<td class="text-center">
									<input type="checkbox" data-group-cls="btn-group-sm" data-switch-always name="settings[access_actions][]" value="<?php echo $act['action']; ?>" <?php echo $act['checked']; ?> class="checkboxpicker" />
								</td>
							  </tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				  </div>
			  </div>
		  </div>

		  <div class="tab-pane" id="tab_select_pages">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_select_pages_help; ?></p>
			</div>
			<table id="page-table" class="table table-bordered table-hover">
				<thead>
					<tr class="active">
					  <th class="text-center" style="width: 50px;">#</th>
					  <th class="text-center" style="width: 50px;"><?php echo $column_template_id; ?></th>
					  <th class="text-left"><?php echo $column_template_name; ?></th>
					  <th class="text-left"><?php echo $column_template_description; ?></th>
					  <th style="width: 94px;"><?php echo $column_column_status; ?></th>
					</tr>
				</thead>
				<tbody class="tbody-sortable">
				  <?php foreach ($order_pages as $page) { ?>
				   <tr id="page-row-<?php echo $page['template_id']; ?>>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-center"><?php echo $page['template_id']; ?></td>
					  <td class="text-left"><?php echo $page['name']; ?></td>
					  <td class="text-left"><?php echo $page['description']; ?></td>
					  <td class="text-center">
						<input type="checkbox" data-group-cls="btn-group-sm" data-switch-always name="settings[pages][]" value="<?php echo $page['code']; ?>" <?php echo $page['checked']; ?> class="checkboxpicker" />
					  </td>
					</tr>
				  <?php } ?>
				</tbody>
			</table>
		  </div>

		  <div class="tab-pane" id="tab_methods_list">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_global&item=item_003"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_methods_list_info; ?>
				<br><i class="fa fa-exclamation-triangle text-orange"></i> <?php echo $text_tab_methods_list_help; ?>
				<br><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_tab_methods_list_help1; ?>
				<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_methods_list_help2; ?>
				<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_methods_list_help3; ?></p>
			</div>
			<div class="panel panel-default">
				<div class="panel-body">
				  <div class="row">
					<div class="col-sm-6">
					  <label class="control-label"><?php echo $entry_payment_methods_list; ?></label>
					  <div class="input-group">
						<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_payment_methods_list_info; ?>"><i class="fa fa-info text-blue"></i></span>
						<select name="settings[payments_list]" class="methods-list form-control">
						<?php foreach ($payment_methods_list as $method) { ?>
							<option value="<?php echo $method['value']; ?>" <?php echo $method['selected']; ?>><?php echo $method['text']; ?></option>
						<?php } ?>
						</select>
					  </div>
					</div>
					<div class="col-sm-6">
					  <label class="control-label"><?php echo $entry_shippings_methods_list; ?></label>
					  <div class="input-group">
						<span class="input-group-addon"><i class="fa fa-truck"></i></span>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_shippings_methods_list_info; ?>"><i class="fa fa-info text-blue"></i></span>
						<select name="settings[shippings_list]" class="methods-list form-control">
						<?php foreach ($shipping_methods_list as $method) { ?>
							<option value="<?php echo $method['value']; ?>" <?php echo $method['selected']; ?>><?php echo $method['text']; ?></option>
						<?php } ?>
						</select>
					  </div>
					</div>
				  </div>
				</div>
			</div>
		  </div>

		  <div class="tab-pane" id="tab_select_orders">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_select_orders_help; ?></p>
			</div>
			  <div class="row">
				<?php foreach ($user_group_params as $param) { ?>
				  <div class="col-sm-6">
					<table class="table-mini full-width">
						<thead><tr>
							<th colspan="2"><?php echo $param['title']; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $param['help']; ?>"></i></th></tr>
						</thead>
						<tr><td></td>
							<td class="text-left"><a class="check-trigger-all"><?php echo $text_select_all; ?></a></td></tr>
						<tbody>
						<?php foreach ($param['data'] as $set) { ?>
							<tr>
								<td style="width: 40px;" class="text-center"><input type="checkbox" class="minimal" name="settings[select_orders][<?php echo $param['id']; ?>][]" value="<?php echo $set['id']; ?>" <?php if (isset($settings['select_orders'][$param['id']]) && in_array($set['id'], $settings['select_orders'][$param['id']])) { ?> checked="checked" <?php } ?>/></td>
								<td class="text-left"><?php echo $set['text']; ?></td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				  </div>
				<?php } ?>
			  </div>
		  </div>

		  <div class="tab-pane" id="tab_edit_colors">
			<div class="row">
			  <div class="col-sm-12">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_04"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_edit_colors_help; ?></p>
				</div>
			  </div>
			</div>
			<div class="form-group">
			  <div class="col-sm-4">
				<table class="table-mini">
				  <thead>
					<tr>
					  <th colspan="3"><?php echo $entry_order_statuses; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $entry_order_statuses_color_help; ?>"></i></th>
					</tr>
					<tr>
					  <th><?php echo $column_status; ?></th>
					  <th><?php echo $select_text_color; ?></th>
					  <th><?php echo $select_background_color; ?></th>
					</tr>
				  </thead>
				  <tbody>
				  <?php foreach ($order_statuses as $status) { ?>
					<tr>
						<td style="color: <?php echo '#'.$settings['order_statuses'][$status['id']]['text_color_id']; ?>; background-color: <?php echo '#'.$settings['order_statuses'][$status['id']]['back_color_id']; ?>;" id="orderstatus<?php echo $status['id']; ?>"><?php echo $status['text']; ?></td>
						<td>
							<input class="form-control text-color jscolor {mode:'HVS',position:'right'}" data-id="orderstatus<?php echo $status['id']; ?>" name="settings[order_statuses][<?php echo $status['id']; ?>][text_color_id]" value="<?php echo $settings['order_statuses'][$status['id']]['text_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_statuses'][$status['id']]['text_color_id']; ?>;" >
						</td>
						<td>
							<input class="form-control background-color jscolor {mode:'HVS',position:'right'}" data-id="orderstatus<?php echo $status['id']; ?>" name="settings[order_statuses][<?php echo $status['id']; ?>][back_color_id]" value="<?php echo $settings['order_statuses'][$status['id']]['back_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_statuses'][$status['id']]['back_color_id']; ?>;">
						</td>
					</tr>
				  <?php } ?>
				  </tbody>
				</table>
			  </div>
			  <div class="col-sm-4">
				<table class="table-mini">
				  <thead>
					<tr>
					  <th colspan="3"><?php echo $entry_order_payments; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $entry_order_payments_color_help; ?>"></i></th>
					</tr>
					<tr>
					  <th><?php echo $text_payment_method; ?></th>
					  <th><?php echo $select_text_color; ?></th>
					  <th><?php echo $select_background_color; ?></th>
					</tr>
				  </thead>
				  <tbody>
				  <?php foreach ($order_payments as $key => $payment) { ?>
					<tr>
						<td style="color: <?php echo '#'.$settings['order_payments'][$payment['id']]['text_color_id']; ?>; background-color: <?php echo '#'.$settings['order_payments'][$payment['id']]['back_color_id']; ?>;" id="orderpayments<?php echo $key; ?>"><?php echo $payment['text']; ?></td>
						<td>
							<input class="form-control text-color jscolor {mode:'HVS',position:'right'}" data-id="orderpayments<?php echo $key; ?>" name="settings[order_payments][<?php echo $payment['id']; ?>][text_color_id]" value="<?php echo $settings['order_payments'][$payment['id']]['text_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_payments'][$payment['id']]['text_color_id']; ?>;" >
						</td>
						<td>
							<input class="form-control background-color jscolor {mode:'HVS',position:'right'}" data-id="orderpayments<?php echo $key; ?>" name="settings[order_payments][<?php echo $payment['id']; ?>][back_color_id]" value="<?php echo $settings['order_payments'][$payment['id']]['back_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_payments'][$payment['id']]['back_color_id']; ?>;">
						</td>
					</tr>
				  <?php } ?>
				  </tbody>
				</table>
			  </div>
			  <div class="col-sm-4">
				<table class="table-mini">
				  <thead>
					<tr>
					  <th colspan="3"><?php echo $entry_order_shippings; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $entry_order_shippings_color_help; ?>"></i></th>
					</tr>
					<tr>
					  <th><?php echo $text_shipping_method; ?></th>
					  <th><?php echo $select_text_color; ?></th>
					  <th><?php echo $select_background_color; ?></th>
					</tr>
				  </thead>
				  <tbody>
				  <?php foreach ($order_shippings as $key => $shipping) { ?>
					<tr>
						<td style="color: <?php echo '#'.$settings['order_shippings'][$shipping['id']]['text_color_id']; ?>; background-color: <?php echo '#'.$settings['order_shippings'][$shipping['id']]['back_color_id']; ?>;" id="ordershippings<?php echo $key; ?>"><?php echo $shipping['text']; ?></td>
						<td>
							<input class="form-control text-color jscolor {mode:'HVS',position:'right'}" data-id="ordershippings<?php echo $key; ?>" name="settings[order_shippings][<?php echo $shipping['id']; ?>][text_color_id]" value="<?php echo $settings['order_shippings'][$shipping['id']]['text_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_shippings'][$shipping['id']]['text_color_id']; ?>;" >
						</td>
						<td>
							<input class="form-control background-color jscolor {mode:'HVS',position:'right'}" data-id="ordershippings<?php echo $key; ?>" name="settings[order_shippings][<?php echo $shipping['id']; ?>][back_color_id]" value="<?php echo $settings['order_shippings'][$shipping['id']]['back_color_id']; ?>" style="background-color: <?php echo '#'.$settings['order_shippings'][$shipping['id']]['back_color_id']; ?>;">
						</td>
					</tr>
				  <?php } ?>
				  </tbody>
				</table>
			  </div>
			</div>
			<?php if ($scheduler_status) { ?>
			<div class="form-group">
			  <div class="col-sm-4">
				<table id="days_to_ship_colors" class="table-mini">
				  <thead>
					<tr>
					  <th colspan="5"><?php echo $entry_order_date_shippings; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $entry_order_date_shippings_help; ?>"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-orange" data-toggle="tooltip" title="<?php echo $entry_order_date_shippings_help2; ?>"></i></th>
					</tr>
					<tr>
					  <th><?php echo $column_example_date; ?></th>
					  <th><?php echo $column_days_to_ship; ?></th>
					  <th><?php echo $select_text_color; ?></th>
					  <th><?php echo $select_background_color; ?></th>
					  <th></th>
					</tr>
				  </thead>
				  <tbody>
					<?php $days_id = 0; ?>
					<?php if (isset($settings['days_to_ship']) && !empty($settings['days_to_ship'])) { ?>
					  <?php foreach ($settings['days_to_ship'] as $days_id => $colors) { ?>
						<tr id="days_to_ship_row<?php echo $days_id; ?>">
							<td style="color: <?php echo '#'.$settings['days_to_ship'][$days_id]['text_color_id']; ?>; background-color: <?php echo '#'.$settings['days_to_ship'][$days_id]['back_color_id']; ?>;" id="days_to_ship<?php echo $days_id; ?>"><?php echo $settings['days_to_ship_dates'][$days_id]; ?></td>
							<td>
								<input class="form-control" name="settings[days_to_ship][<?php echo $days_id; ?>][days]" value="<?php echo $colors['days']; ?>">
							</td>
							<td>
								<input class="form-control text-color jscolor {mode:'HVS',position:'right'}" data-id="days_to_ship<?php echo $days_id; ?>" name="settings[days_to_ship][<?php echo $days_id; ?>][text_color_id]" value="<?php echo $colors['text_color_id']; ?>" style="background-color: <?php echo '#'.$settings['days_to_ship'][$days_id]['text_color_id']; ?>;" >
							</td>
							<td>
								<input class="form-control background-color jscolor {mode:'HVS',position:'right'}" data-id="days_to_ship<?php echo $days_id; ?>" name="settings[days_to_ship][<?php echo $days_id; ?>][back_color_id]" value="<?php echo $colors['back_color_id']; ?>" style="background-color: <?php echo '#'.$settings['days_to_ship'][$days_id]['back_color_id']; ?>;">
							</td>
							<td class="left" colspan="3">
							  <span class="btn-group">
								<a onclick="$('#days_to_ship_row<?php echo $days_id; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus"></i></a>
							  </span>
							</td>
						</tr>
					  <?php } ?>
					  <?php $days_id++; ?>
					<?php } else { ?>
						<tr id="days_no_result"><td class="center" colspan="5"><?php echo $text_no_results; ?></td></tr>
					<?php } ?>
					<tr id="add_days_row">
					  <td colspan="4"></td>
					  <td class="left">
						<a onclick="addDaysRow();" data-toggle="tooltip" title="<?php echo $button_add_row; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></a>
					  </td>
					</tr>
				  </tbody>
				</table>
				<script type="text/javascript"><!--
				var days_id = <?php echo $days_id; ?>;
				function addDaysRow() {
					html  = '<tr id="days_to_ship_row' + days_id + '">';
					html += ' <td style="color: #595959; background-color: #FFFFFF;" id="days_to_ship' + days_id + '"><?php echo date('d.m.Y'); ?></td>';
					html += ' <td><input class="form-control" name="settings[days_to_ship][' + days_id + '][days]" value="0" style="background-color: #FFFFFF;" ></td>';
					html += ' <td><input class="form-control text-color jscolor {mode:\'HVS\',position:\'right\'}" data-id="days_to_ship' + days_id + '" name="settings[days_to_ship][' + days_id + '][text_color_id]" value="595959" style="color: #FFFFFF; background-color: #595959;" ></td>';
					html += ' <td><input class="form-control background-color jscolor {mode:\'HVS\',position:\'right\'}" data-id="days_to_ship' + days_id + '" name="settings[days_to_ship][' + days_id + '][back_color_id]" value="FFFFFF" style="background-color: #FFFFFF;" ></td>';
					html += ' <td class="left" colspan="3"><span class="btn-group"><a onclick="$(\'#days_to_ship_row' + days_id + '\').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a></span></td>';
					html += '</tr>';

					var days_no_result = $('#days_no_result').html();
					if (days_no_result !='') {
						$('#days_no_result').remove();
					}

					$('#add_days_row').before(html);
					days_id++;

					$('#days_to_ship_colors .jscolor').each(function() {
						new jscolor($(this)[0]);
					});
					jscolorStart();
					tooltipRefresh();
				}
				//--></script>
			  </div>
			</div>
			<?php } ?>
		  </div>

		  <div class="tab-pane" id="tab_filters_default">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_05"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_tab_filters_default_help; ?></p>
			</div>
			<table id="filter-table" class="table table-bordered table-hover">
				<thead>
					<tr class="active">
					  <th class="text-center" style="width: 50px;">#</th>
					  <th class="text-left" style="width: 50%;"><?php echo $text_filter_id; ?></th>
					  <th class="text-left"><?php echo $text_filter_value_default; ?></th>
					  <th style="width: 80px;"></th>
					</tr>
				</thead>
				<tbody class="tbody-sortable">
				<?php $filters_default_row = 1; ?>

				<?php if (!empty($filters_default)) { ?>
				  <?php foreach ($filters_default as $filter_default) { ?>
				   <tr id="filter-row-<?php echo $filters_default_row; ?>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-left"><?php echo $filter_default['filter_id']; ?>
						<input type="hidden" name="settings[filters_default][<?php echo $filter_default['filter_id']; ?>][filter_id]" value="<?php echo $filter_default['filter_id']; ?>" class="'filter-id" />
						<input type="hidden" name="settings[filters_default][<?php echo $filter_default['filter_id']; ?>][default_val]" value="<?php echo $filter_default['default_val']; ?>" id="default_val_<?php echo $filter_default['filter_id']; ?>" />
					  </td>

					  <td class="text-left" id="filter-value<?php echo $filters_default_row; ?>">
						<?php echo $filter_default['html']; ?>
					  </td>

					  <td class="text-left"><button type="button" onclick="$('#filter-row-<?php echo $filters_default_row; ?>').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
					</tr>
					<?php $filters_default_row++; ?>
				  <?php } ?>
				<?php } ?>

				</tbody>
				<tfoot>
					<tr>
					  <td colspan="2" class="text-right text-middle"><b><?php echo $text_add_filter; ?></b> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_add_filter_info; ?>"></i></td>
					  <td class="text-left">
						<select id="filter_default_tplid" class="multiselect form-control">
							<option value=""><?php echo $text_not_selected; ?></option>
						  <?php foreach ($filters as $filter) { ?>
							<option value="<?php echo $filter['filter_id']; ?>"><?php echo $filter['name']; ?></option>
						  <?php } ?>
						</select>
					  </td>
					  <td class="text-left"><button type="button" id="button_add_filter_row" onclick="addFilterRow('<?php echo $filters_default_row; ?>');" data-toggle="tooltip" title="<?php echo $button_add_row; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
					</tr>
				</tfoot>
			</table>
		  </div>

		  <div class="tab-pane" id="tab_order_formats">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_06"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;   <?php echo $text_tab_order_formats_help; ?></p>
			</div>
			<table id="order-format-table" class="table table-bordered table-hover">
				<thead>
					<tr class="active">
					  <th class="text-center" style="width: 50px;">#</th>
					  <th class="text-left" style="width: 25%;"><?php echo $text_format_table_title_code; ?></th>
					  <th class="text-left" style="width: 35%;"><?php echo $text_format_table_title_format_as; ?></th>
					  <th class="text-left"><?php echo $text_format_table_title_format; ?></th>
					  <th style="width: 80px;"></th>
					</tr>
				</thead>
				<tbody class="tbody-sortable">
				  <?php $order_format_row = 1; ?>
				  <?php foreach ($order_formats as $o_format) { ?>
				   <tr id="order-format-row-<?php echo $order_format_row; ?>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-left"><?php echo $o_format['code']; ?>
						<input type="hidden" name="settings[order_formats][<?php echo $o_format['code']; ?>][code]" value="<?php echo $o_format['code']; ?>" class="order-code" />
					  </td>
					  <td class="text-left">
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<select name="settings[order_formats][<?php echo $o_format['code']; ?>][type]" data-format-code="<?php echo $o_format['code']; ?>" target-order-format="order-format-<?php echo $order_format_row; ?>" class="select-order-format-type form-control">
							  <?php foreach ($order_format_list as $type) { ?>
								<option value="<?php echo $type['type']; ?>" <?php if (isset($o_format['type']) && $o_format['type'] == $type['type']) { ?> selected="selected"<?php } ?>><?php echo $type['name']; ?></option>
							  <?php } ?>
							</select>
						</div>
					  </td>

					  <td class="text-left" id="order-format-<?php echo $order_format_row; ?>">
					   <?php if ($o_format['type'] == 'date') { ?>
						<div class="input-group valid-block">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>
							<input type="text" name="settings[order_formats][<?php echo $o_format['code']; ?>][date_format]" value="<?php echo isset($o_format['date_format']) ? $o_format['date_format'] : 'd.m.Y'; ?>" placeholder="<?php echo 'd.m.Y H:i:s'; ?>" class="field-input mustbe form-control" />
						</div>
					  <?php } ?>
					  <?php if ($o_format['type'] == 'method') { ?>
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
							<select name="settings[order_formats][<?php echo $o_format['code']; ?>][process_method]" class="form-control">
							  <?php foreach ($order_format_method_list as $method) { ?>
								<option value="<?php echo $method['process_method']; ?>" <?php if (isset($o_format['process_method']) && $o_format['process_method'] == $method['process_method']) { ?> selected="selected"<?php } ?>><?php echo $method['name']; ?></option>
							  <?php } ?>
							</select>
						</div>
					  <?php } ?>
					  </td>

					  <td class="text-left"><button type="button" onclick="$('#order-format-row-<?php echo $order_format_row; ?>').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
					</tr>
					<?php $order_format_row++; ?>
				  <?php } ?>
				</tbody>
				<tfoot>
					<tr>
					  <td colspan="3" class="text-right text-middle"><b><?php echo $text_add_format_for; ?></b> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_add_format_for_info; ?>"></i></td>
					  <td class="text-left">
						<select id="order_code" class="multiselect form-control">
						  <?php foreach ($order_codes as $code) { ?>
							<?php if ($code['sort_key']) { ?>
							<option value="<?php echo $code['code']; ?>"><?php echo $code['name']; ?></option>
							<?php } ?>
						  <?php } ?>
						</select>
					  </td>
					  <td class="text-left"><button type="button" id="button_add_order_row" onclick="addOrderFormatRow('<?php echo $order_format_row; ?>');" data-toggle="tooltip" title="<?php echo $button_add_row; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
					</tr>
				</tfoot>
			</table>
		  </div>

		  <div class="tab-pane" id="tab_product_formats">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_07"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_product_formats_help; ?></p>
			</div>
			<table id="product-format-table" class="table table-bordered table-hover">
				<thead>
					<tr class="active">
					  <th class="text-center" style="width: 50px;">#</th>
					  <th class="text-left" style="width: 25%;"><?php echo $text_format_table_title_code; ?></th>
					  <th class="text-left" style="width: 35%;"><?php echo $text_format_table_title_format_as; ?></th>
					  <th class="text-left"><?php echo $text_format_table_title_format; ?></th>
					  <th style="width: 80px;"></th>
					</tr>
				</thead>
				<tbody class="tbody-sortable">
				  <?php $product_format_row = 1; ?>
				  <?php foreach ($product_formats as $p_format) { ?>
				   <tr id="product-format-row-<?php echo $product_format_row; ?>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-left"><?php echo $p_format['code']; ?>
						<input type="hidden" name="settings[product_formats][<?php echo $p_format['code']; ?>][code]" value="<?php echo $p_format['code']; ?>" class="product-code" />
					  </td>
					  <td class="text-left">
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<select name="settings[product_formats][<?php echo $p_format['code']; ?>][type]" data-format-code="<?php echo $p_format['code']; ?>" target-product-format="product-format-<?php echo $product_format_row; ?>" class="select-product-format-type form-control">
							  <?php foreach ($product_format_list as $type) { ?>
								<option value="<?php echo $type['type']; ?>" <?php if (isset($p_format['type']) && $p_format['type'] == $type['type']) { ?> selected="selected"<?php } ?>><?php echo $type['name']; ?></option>
							  <?php } ?>
							</select>
						</div>
					  </td>

					  <td class="text-left" id="product-format-<?php echo $product_format_row; ?>">
					   <?php if ($p_format['type'] == 'date') { ?>
						<div class="input-group valid-block">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>
							<input type="text" name="settings[product_formats][<?php echo $p_format['code']; ?>][date_format]" value="<?php echo isset($p_format['date_format']) ? $p_format['date_format'] : 'd.m.Y'; ?>" placeholder="<?php echo 'd.m.Y H:i:s'; ?>" class="field-input mustbe form-control" />
						</div>
					  <?php } ?>
					  <?php if ($p_format['type'] == 'method') { ?>
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
							<select name="settings[product_formats][<?php echo $p_format['code']; ?>][process_method]" class="form-control">
							  <?php foreach ($product_format_method_list as $method) { ?>
								<option value="<?php echo $method['process_method']; ?>" <?php if (isset($p_format['process_method']) && $p_format['process_method'] == $method['process_method']) { ?> selected="selected"<?php } ?>><?php echo $method['name']; ?></option>
							  <?php } ?>
							</select>
						</div>
					  <?php } ?>
					  </td>

					  <td class="text-left"><button type="button" onclick="$('#product-format-row-<?php echo $product_format_row; ?>').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
					</tr>
					<?php $product_format_row++; ?>
				  <?php } ?>
				</tbody>
				<tfoot>
					<tr>
					  <td colspan="3" class="text-right text-middle"><b><?php echo $text_add_format_for; ?></b></td>
					  <td class="text-left">
						<select id="product_code" class="multiselect form-control">
						  <?php foreach ($product_codes as $code) { ?>
							<option value="<?php echo $code['code']; ?>"><?php echo $code['name']; ?></option>
						  <?php } ?>
						</select>
					  </td>
					  <td class="text-left"><button type="button" id="button_add_product_row" onclick="addProductFormatRow('<?php echo $product_format_row; ?>');" data-toggle="tooltip" title="<?php echo $button_add_row; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
					</tr>
				</tfoot>
			</table>
		  </div>

		  <div class="tab-pane" id="tab_comment_status_template">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_08"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_comment_status_template_help; ?></p>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading"><a data-toggle="collapse" href="#var-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_order_table_info_block_title; ?></a></div>
				<div id="var-info" class="panel-collapse collapse">
				  <div class="panel-body">
					<div class="row">
						<div class="col-lg-6 col-sm-12">
							<div class="btn-group btn-group-vertical btn-block">
								<a class="btn btn-default text-red" onclick="getTableCodes('print_orders');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_orders_table_tpl; ?></a>
								<a class="btn btn-default text-red" onclick="getTableCodes('print_orders_table');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_orders_table_table_tpl; ?></a>
								<a class="btn btn-default text-red" onclick="getTableCodes('print_products_table');"><i class="fa fa-file-pdf-o"></i>&nbsp;  <?php echo $btn_print_products_table_table_tpl; ?></a>
							</div>
						</div>
						<div class="col-lg-6 col-sm-12">
							<div class="btn-group btn-group-vertical btn-block">
								<a class="btn btn-default text-green" onclick="getTableCodes('excel_orders');"><i class="fa fa-file-excel-o"></i>&nbsp;  <?php echo $btn_excel_orders_table_tpl; ?></a>
								<a class="btn btn-default text-green" onclick="getTableCodes('excel_orders_products');"><i class="fa fa-file-excel-o"></i>&nbsp;  <?php echo $btn_excel_orders_products_table_tpl; ?></a>
							</div>
						</div>
					</div>
				  </div>
				</div>
			</div>

			<?php foreach ($order_statuses as $status) { ?>
			  <div class="form-group" style="border-bottom: 1px dashed #DDD !Important;">
				<label class="col-sm-2 control-label"><?php echo $status['text']; ?></label>
				<div class="col-sm-10">
				  <table class="table-mini" style="width: 100%; margin-bottom: 15px;">
					<?php foreach ($languages as $language) { ?>
						<tr><td style="width: 1px;">
						  <?php if ($ocversion >= 220) { ?>
							<img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" />
						  <?php } else { ?>
							<img src="view/image/flags/<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" />
						  <?php } ?>
						  </td>
						  <td>
							<textarea name="settings[comment_templates][<?php echo $status['id']; ?>][<?php echo $language['language_id']; ?>]" rows="2" class="form-control"><?php echo $settings['comment_templates'][$status['id']][$language['language_id']]; ?></textarea>
						  </td></tr>
					<?php } ?>
				  </table>
				</div>
			  </div>
			<?php } ?>
		  </div>

		  <div class="tab-pane" id="tab_backup">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_group&item=item_09"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_backup_help; ?>
				<br/><i class="fa fa-exclamation-triangle text-orange"></i> <?php echo $text_tab_backup_group_help; ?>
				</p>
			</div>
			<div class="panel panel-default">
			  <div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $entry_save_to_file; ?></label>
					<div class="col-sm-10">
					  <a class="btn btn-primary" target="_blank" href="<?php echo $backup_group_link ?>" id="button-backup"><?php echo $button_save ?></a>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 control-label"><?php echo $entry_restore_from_file; ?></label>
					<div class="col-sm-10">
					<table class="restore-table">
					  <tr>
						<td>
						  <input class="btn btn-default" type="file" name="import"/>
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

			<div class="panel panel-primary">
			  <div class="panel-heading"><i class="fa fa-download"></i>&nbsp;&nbsp; <?php echo $entry_export; ?></div>
			  <div class="panel-body">
			<div class="callout callout-default">
				<p><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_backup_default_help1; ?>
				<br><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_tab_backup_default_help4; ?>
				</p>
			</div>
				<div class="callout callout-default">
					<p><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_backup_default_help2; ?></p>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table class="table-mini full-width restore-table" style="margin-bottom: 10px;">
						  <tbody>
							  <tr>
								<th style="width: 160px; padding: 5px 10px;"><?php echo $entry_save_to_file; ?></th>
								<td>
								  <a class="btn btn-primary" target="_blank" href="<?php echo $backup_default ?>" id="button-backup"><?php echo $button_save ?></a>
								</td>
							  </tr>
						  </tbody>
						</table>
					</div>
				</div>
			  </div>
			</div>
			<div class="panel panel-info">
			  <div class="panel-heading"><b><i class="fa fa-upload"></i>&nbsp;&nbsp; <?php echo $entry_import_param; ?></b></div>
			  <div class="panel-body">
				<div class="row"  id="restore_all_params">

					<div class="col-sm-4">
						<table class="table-mini  full-width">
						  <thead>
							<tr><th><b><i class="fa fa-warning"></i>&nbsp;&nbsp; <?php echo $entry_import_fields_param; ?></b></th><th style="width: 32px;"><i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $entry_import_fields_param_help; ?>"></i></th></tr>
						  </thead>
						  <tbody>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_fields" id="restore_fields_setting_off" />&nbsp;&nbsp; <?php echo $text_restore_fields_setting_off; ?></label></td><td></td>
							</tr>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_fields" id="restore_fields_setting_all" checked="checked" />&nbsp;&nbsp; <?php echo $text_restore_fields_setting_all; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_fields_setting_all_help; ?>"></i></td>
							</tr>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_fields"  id="restore_fields_setting_new" />&nbsp;&nbsp; <?php echo $text_restore_fields_setting_new; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_fields_setting_new_help; ?>"></i></td>
							</tr>
						  </tbody>
						</table>
					</div>

					<div class="col-sm-4">
						<table class="table-mini  full-width">
						  <thead>
							<tr><th><b><i class="fa fa-copy"></i>&nbsp;&nbsp; <?php echo $entry_import_tpl_param; ?></b></th><th style="width: 32px;"></th></tr>
						  </thead>
						  <tbody>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_tpl" id="restore_tpl_off" />&nbsp;&nbsp; <?php echo $text_restore_tpl_off; ?></label></td><td></td>
							</tr>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_tpl"  id="restore_tpl_all" checked="checked" />&nbsp;&nbsp; <?php echo $text_restore_tpl_all; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_tpl_all_help; ?>"></i></td>
							</tr>
							<tr>
								<td><label><input type="radio" class="minimal" name="restore_tpl"  id="restore_tpl_new" />&nbsp;&nbsp; <?php echo $text_restore_tpl_new; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_tpl_new_help; ?>"></i></td>
							</tr>
						  </tbody>
						</table>
					</div>

					<div class="col-sm-4">
						<table class="table-mini  full-width">
						  <thead>
							<tr><th><b><i class="fa fa-cog"></i>&nbsp;&nbsp; <?php echo $entry_import_setting_param; ?></b></th><th style="width: 32px;"></th></tr>
						  </thead>
						  <tbody>
							<tr>
								<td><label><input type="checkbox" class="minimal" id="restore_admin_setting"  checked="checked" />&nbsp;&nbsp; <?php echo $text_restore_admin_setting; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_admin_setting_help; ?>"></i></td>
							</tr>
							<tr>
								<td><label><input type="checkbox" class="minimal" id="restore_group_setting" checked="checked" />&nbsp;&nbsp; <?php echo $text_restore_group_setting; ?></label></td><td><i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_restore_group_setting_help; ?>"></i></td>
							</tr>
						  </tbody>
						</table>
					</div>
				</div>

				<div class="row">
					<div class="col-sm-12">
						<table class="table-mini full-width restore-table">
						  <thead>
							<tr><th><b><i class="fa fa-uoload"></i>&nbsp;&nbsp; <?php echo $entry_import_from_file; ?></b></th><th></th></tr>
						  </thead>
						  <tbody>
							  <tr>
								<td style="width: 284px;">
								  <input class="btn btn-default" type="file" name="importall"/>
								  <input name="restoreall" type="hidden" value="0" id="restoreall" />
								</td>
								<td>
								  <div class="input-group">
									<div class="input-group-btn">
									  <a class="btn btn-primary button-restoreall" disabled="disabled"><?php echo $button_import; ?></a>
									</div>
								  </div>
								</td>
							  </tr>
						  </tbody>
						</table>
						<div class="callout callout-default">
							<p><i class="fa fa-exclamation-triangle text-orange"></i> <?php echo $text_tab_backup_default_help3; ?></p>
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

	$('#restore').val(1);
	var url = '<?php echo $action; ?>';
	url = url.replace(/&amp;/g, "&");
	$('#form').attr('action', url).submit();
});

$('input[name=\'importall\']').bind('change', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		$('input[name=\'importall\']').val('');
		return false;
	}
	var imp = $('input[name=\'importall\']').val();
	if (imp) {
		$('.button-restoreall').attr('disabled', false);
	} else {
		$('.button-restoreall').attr('disabled', true);
	}
});

$('.button-restoreall').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var i = 0;
	$('#restore_all_params input:not(input[id=\'restore_fields_setting_off\'], input[id=\'restore_tpl_off\'])').each(function() {
		if ($(this).prop('checked')) { i++; }
	});

	if (i < 1) {
		toastr.warning('<?php echo $text_alert_restore_param; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var imp = $('input[name=\'importall\']').val();
	if (!imp) { return false; }
	$('#restoreall').val(1);

	var url = '<?php echo $restore_default; ?>';
	url = url.replace(/&amp;/g, "&");
	if ($('input[id=\'restore_fields_setting_all\']').prop('checked')) { url += '&restore_fields_setting_all=1'; }
	if ($('input[id=\'restore_fields_setting_new\']').prop('checked')) { url += '&restore_fields_setting_new=1'; }
	if ($('input[id=\'restore_tpl_all\']').prop('checked')) { url += '&restore_tpl_all=1'; }
	if ($('input[id=\'restore_tpl_new\']').prop('checked')) { url += '&restore_tpl_new=1'; }
	if ($('input[id=\'restore_admin_setting\']').prop('checked')) { url += '&restore_admin_setting=1'; }
	if ($('input[id=\'restore_group_setting\']').prop('checked')) { url += '&restore_group_setting=1'; }

	$('#form').attr('action', url).submit();
});

$('#button_save').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}

	var pages = $('input[name^=\'settings[pages]\']:checked');

	if (!pages.length && !confirm('На вкладке Выбор страниц не выбрана ни одна страница для отображения просмотр заказов для данной группы будет невозможен! Продолжить?!')) {
		return false;
	}

	if (!window.validation.isValid({ container: '#form' })) {
		toastr.error('<?php echo $text_error_form; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	} else {
		var url = '<?php echo $action; ?>';
		url = url.replace(/&amp;/g, "&");
		$('#form').attr('action', url).submit();
	}
});

$('.methods-list ').bind('change', function() {
	$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');

	$('#button_save').trigger('click');
});

$('#default_setting').bind('click', function(e) {
	e.preventDefault();

	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	} else if (confirm("<?php echo $confirm_default_setting; ?>")) {
		var url = '<?php echo $default_setting; ?>';
		url = url.replace(/&amp;/g, "&");
		location = url;
	}
});

$(document).delegate('#form', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

$('.checkboxpicker').checkboxpicker();

$(document).delegate('.check-trigger-all', 'click', function() {
	var trigger = $(this);
	var table = trigger.closest('table');
	var clicks = table.data('clicks');

	if (table) {
		if (clicks) {
			table.find("input[type='checkbox']").iCheck("uncheck");
			trigger.text('<?php echo $text_select_all; ?>');
		} else {
			table.find("input[type='checkbox']").iCheck("check");
			trigger.text('<?php echo $text_unselect_all; ?>');
		}
		table.data("clicks", !clicks);
	}
});

//--></script>

<script type="text/javascript"><!--

$(document).delegate('.setting-pages [filter_Input]', 'change', function() {
	var filter = $(this);
	$('#default_val_'+filter.attr('id')).val(filter.val());
});

function addFilterRow(filter_row) {
	var filter_id = $('#filter_default_tplid').val();
	var double_id = false;

	$('#filter-table .filter-id').each(function(){
		if ($(this).val() == filter_id) {
			toastr.warning('<?php echo $text_error_filter_default_id; ?>', '<?php echo $text_alert_warning; ?>');
			double_id = true; return false;
		}
	});

	if (double_id) {
		return false;
	} else {
		var filter_html = '';
		$.ajax({
			url: 'index.php?route=sale/ompro/previewFilter&<?php echo $strtoken; ?>&type=filter&filter_id=' + filter_id,
			dataType: 'json',
			beforeSend: function() {
				$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
			},
			success: function(json) {
				$('.text-loading').remove();
				if (json['error']) {
					modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
				} else {
					if (json['tpl']) {
						html  = '<tr id="filter-row-'+ filter_row + '">';
						html += '<td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>';
						html += '<td class="text-left">'+ filter_id +'<input type="hidden" name="settings[filters_default]['+ filter_id +'][filter_id]" value="'+ filter_id +'" class="filter-id" /><input type="hidden" name="settings[filters_default]['+ filter_id +'][default_val]" value="" id="default_val_'+ filter_id +'" /></td>';

						html += '<td class="text-left">'+json['tpl']+'</td>';

						html += '<td class="text-left"><button type="button" onclick="$(\'#filter-row-'+ filter_row + '\').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
						html += '</tr>';

						$('#filter-table tbody').append(html);

						dpStart();
						multiselectStart();
						$('[data-mask]').inputmask();
						$(document).find('#'+filter_id).trigger('change');

						filter_row++;
						$('#button_add_filter_row').attr('onclick', 'addFilterRow(\''+filter_row+'\')');
					}
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
}

function addOrderFormatRow(format_row) {
	var order_code = $('#order_code').val();
	var double_code = false;

	$('#order-format-table .order-code').each(function(){
		if ($(this).val() == order_code) {
			toastr.warning('<?php echo $text_error_double_code; ?>', '<?php echo $text_alert_warning; ?>');
			double_code = true; return false;
		}
	});

	if (double_code) {
		return false;
	} else {
		html  = '<tr id="order-format-row-'+ format_row + '">';
		html += '<td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>';
		html += '<td class="text-left">'+ order_code +'<input type="hidden" name="settings[order_formats]['+ order_code +'][code]" value="'+ order_code +'" class="order-code" /></td>';
		html += '<td class="text-left">';
		html += ' <div class="input-group">';
		html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += '  <select name="settings[order_formats]['+ order_code +'][type]" data-format-code="'+ order_code +'" target-order-format="order-date-'+ format_row + '" class="select-order-format-type form-control">';
	  <?php foreach ($order_format_list as $type) { ?>
		html += '<option value="<?php echo $type['type']; ?>"><?php echo $type['name']; ?></option>';
	  <?php } ?>
		html += '  </select>';
		html += ' </div>';
		html += '</td>';
		html += '<td class="text-left" id="order-date-'+ format_row + '"></td>';
		html += '<td class="text-left"><button type="button" onclick="$(\'#order-format-row-'+ format_row + '\').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		html += '</tr>';

		$('#order-format-table tbody').append(html);
		$('#order-format-row-'+ format_row + ' .select-order-format-type').trigger('change');

		format_row++;
		$('#button_add_order_row').attr('onclick', 'addOrderFormatRow(\''+format_row+'\')');
	}
}

$(document).delegate('.select-order-format-type', 'change', function() {
	var order_code = $(this).attr("data-format-code");
	var target_id = $(this).attr("target-order-format");
	var type = $(this).val();

	if (type == 'method') {
		html = '<div class="input-group">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>';
		html += ' <select name="settings[order_formats]['+ order_code +'][process_method]" class="form-control">';
		<?php foreach ($order_format_method_list as $method) { ?>
		html += ' <option value="<?php echo $method['process_method']; ?>"><?php echo $method['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);

	} else if (type == 'date') {
		html = '<div class="input-group valid-block">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>';
		html += ' <input type="text" name="settings[order_formats]['+ order_code +'][date_format]" value="d.m.Y" placeholder="d.m.Y H:i:s" class="field-input mustbe form-control" />';
		html += '</div>';

		$('#'+target_id).html(html);
	} else {
		$('#'+target_id).html('');
	}
	tooltipRefresh('#order-format-table');
});

function addProductFormatRow(format_row) {
	var product_code = $('#product_code').val();
	var double_code = false;

	$('#product-format-table .product-code').each(function(){
		if ($(this).val() == product_code) {
			toastr.warning('<?php echo $text_error_double_code; ?>', '<?php echo $text_alert_warning; ?>');
			double_code = true; return false;
		}
	});

	if (double_code) {
		return false;
	} else {
		html  = '<tr id="product-format-row-'+ format_row + '">';
		html += '<td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>';
		html += '<td class="text-left">'+ product_code +'<input type="hidden" name="settings[product_formats]['+ product_code +'][code]" value="'+ product_code +'" class="product-code" /></td>';
		html += '<td class="text-left">';
		html += ' <div class="input-group">';
		html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += '  <select name="settings[product_formats]['+ product_code +'][type]" data-format-code="'+ product_code +'" target-product-format="product-date-'+ format_row + '" class="select-product-format-type form-control">';
	  <?php foreach ($product_format_list as $type) { ?>
		html += '<option value="<?php echo $type['type']; ?>"><?php echo $type['name']; ?></option>';
	  <?php } ?>
		html += '  </select>';
		html += ' </div>';
		html += '</td>';
		html += '<td class="text-left" id="product-date-'+ format_row + '"></td>';
		html += '<td class="text-left"><button type="button" onclick="$(\'#product-format-row-'+ format_row + '\').remove(); tooltipRefresh();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
		html += '</tr>';

		$('#product-format-table tbody').append(html);
		$('#product-format-row-'+ format_row + ' .select-product-format-type').trigger('change');

		format_row++;
		$('#button_add_product_row').attr('onclick', 'addProductFormatRow(\''+format_row+'\')');
	}
}

$(document).delegate('.select-product-format-type', 'change', function() {
	var product_code = $(this).attr("data-format-code");
	var target_id = $(this).attr("target-product-format");
	var type = $(this).val();

	if (type == 'method') {
		html = '<div class="input-group">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>';
		html += ' <select name="settings[product_formats]['+ product_code +'][process_method]" class="form-control">';
		<?php foreach ($product_format_method_list as $method) { ?>
		html += ' <option value="<?php echo $method['process_method']; ?>"><?php echo $method['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);

	} else if (type == 'date') {
		html = '<div class="input-group valid-block">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>';
		html += ' <input type="text" name="settings[product_formats]['+ product_code +'][date_format]" value="d.m.Y" placeholder="d.m.Y H:i:s" class="field-input mustbe form-control" />';
		html += '</div>';

		$('#'+target_id).html(html);
	} else {
		$('#'+target_id).html('');
	}
	tooltipRefresh('#product-format-table');
});

--></script>

<script type="text/javascript"><!--

function jscolorStart() {
	$('.text-color').bind('change', function() {
		$('#'+$(this).attr('data-id')).css('color', '#'+$(this).val());
	});
	$('.background-color').bind('change', function() {
		$('#'+$(this).attr('data-id')).css('background-color', '#'+$(this).val());
	});
}

$(document).ready(function(){
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	var dropElSelector = '.tbody-sortable';
	var param = {}; param['group'] = {};
	param['group']['name'] = 'table-rows';
	param['handle'] = '.handle';
	param['draggable'] = 'tr';
	param['ghostClass'] = 'sortable-elem';

	sortableStartGroup(dropElSelector, param);

	$(function () { window.validation.init({ container: '#form' });});

	jscolorStart();
	iCheckStart();
	tooltipRefresh();
	multiselectStart();

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
});

//--></script>

<?php } else { ?>
<section class="content">
  <div class="box">
	<div class="box-body">
		<div class="callout callout-danger">
		  <p><?php echo $text_no_results; ?></p>
		</div>
	</div>
  </div>
</section>
<?php } ?>

</div>
<?php echo $footer; ?>