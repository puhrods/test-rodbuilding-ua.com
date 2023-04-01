<!--
@author	Konstantin Kornelyuk
@link https://opencartforum.com/user/28448-brest001/?tab=idm
-->
<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">
<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/bootstrap-checkbox.js"></script>
<script src="view/javascript/ompro/Sortable.js"></script>

<section class="content-header">
  <h1>
	<?php echo $title; ?>
	<small><?php echo $subtitle; ?></small>
  </h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section class="content">
  <div class="box box-default">
	<div class="box-header with-border">
	  <h3 class="box-title"><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_field&item=<?php echo $help_item; ?>"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $boxtitle; ?></h3>
	  <div class="box-tools pull-right">
		<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
		<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
	  </div>
	</div>
	<div class="box-body">
      <div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li><a href="#tab_field_setting" role="tab" data-toggle="tab"><i class="fa fa-tag"></i>&nbsp;&nbsp;<?php echo $text_tab_field_setting; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
		</ul>

		<div class="tab-content">
		  <div class="tab-pane" id="tab_field_setting">
			  <div class="alert alert-danger">
				<p><i class="fa fa-ban"></i>&nbsp;&nbsp; <?php echo $text_alert_developers_fields; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></p>
			  </div>
			<?php if ($page == 'product_as_fields') { ?>
			  <div id="panel-product-sql-info" class="panel panel-danger">
				<div class="panel-heading"><a data-toggle="collapse" href="#product-sql-info" style="color: unset;"><i class="fa fa-sort"></i>&nbsp;&nbsp; <?php echo $text_panel_product_sql_info_title; ?></a></div>
				<div id="product-sql-info" class="panel-collapse collapse" style="padding: 15px 15px 0px;">
				  <div class="panel panel-default">
					<div class="panel-heading"><?php echo $text_product_sql_heading; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_product_sql_heading_info; ?>"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $text_sql_heading_warning; ?>"></i></div>
					<div class="panel-body">
						<div class="alert callout callout-default bg-gray"><?php echo $text_product_sql; ?></div>
						<p><ul>
							<li><?php echo $text_product_sql_info1; ?></li>
							<li><?php echo $text_product_sql_info2; ?></li>
						</ul></p>
					</div>
				  </div>
				  <div class="panel panel-default">
					<div class="panel-heading"><?php echo $text_sumproduct_sql_heading; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_sumproduct_sql_heading_info; ?>"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $text_sql_heading_warning; ?>"></i></div>
					<div class="panel-body">
						<div class="alert callout callout-default bg-gray"><?php echo $text_sumproduct_sql; ?></div>
						<p><ul>
							<li><?php echo $text_sumproduct_sql_info1; ?></li>
							<li><?php echo $text_sumproduct_sql_info2; ?></li>
						</ul></p>
					</div>
				  </div>
					<div class="callout callout-default">
						<p><i class="fa fa-exclamation-triangle text-orange"></i> <?php echo $text_product_api_not_filtersort; ?></p>
						<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_order_check_sql; ?></p>
					</div>
				</div>
			  </div>
			<?php } ?>

			  <div class="panel panel-default">
				<div class="panel-body">
				  <div id="alert_log_sql" class="callout callout-default <?php if (!$log_sql) { ?>hidden-default<?php } ?>">
					<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_alert_log_sql; ?></p>
				  </div>
				  <div class="radio" style="padding-top: 0; padding-bottom: 0; min-height: auto;">
					<label><?php echo $text_log_sql; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_log_sql_info; ?>"></i></label>&nbsp;&nbsp;
					<label>
					  <input type="radio" name="log_sql" value="1" <?php if ($log_sql) { ?>checked<?php } ?> />
					  <?php echo $text_yes; ?>
					</label>
					<label>&nbsp;&nbsp;
					  <input type="radio" name="log_sql" value="0" <?php if (!$log_sql) { ?>checked<?php } ?> />
					  <?php echo $text_no; ?>
					</label>
				  </div>
				</div>
			  </div>

			<?php if ($page == 'product_fields') { ?>
			  <div class="panel panel-default">
				<div class="panel-heading"><h4 class="panel-title"><?php echo $text_add_fields_product_table; ?></h4></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table id="table-add-fields" class="table-mini table-hover full-width no-margin">
						  <thead>
							  <tr>
								<th style="min-width: 200px;"><?php echo $text_select_table; ?></th>
								<th style="min-width: 200px;"><?php echo $text_table_field_name; ?></th>
								<th><?php echo $text_table_field_params; ?></th>
								<th><?php echo $text_table_field_save; ?></th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td>
								<div class="input-group">
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_table_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
									<select  id="alter_select_table" class="form-control">
										<option value="product">product</option>
										<option value="order_product">order_product</option>
									</select>
								</div>
							  </td>
							  <td><input type="text" id="alter_table_field_name" value="" class="field-input unique_field_key field-key form-control" /></td>
							  <td>
								<div class="input-group">
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_table_field_params_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
									<select  id="alter_table_params" class="form-control">
									  <?php foreach ($sql_field_param_list as $param) { ?>
										<option value="<?php echo $param['value']; ?>"><?php echo $param['text']; ?></option>
									  <?php } ?>
									</select>
								</div>
							  </td>
							  <td class="text-left"><button type="button" id="button_alter_order_table" class="btn btn-primary"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp;<?php echo $text_table_field_button_add; ?></button></td>
							</tr>
						  </tbody>
						</table>
					</div>
				</div>
			  </div>
			<?php } ?>

			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
			  <div class="form-group">
				  <div class="col-sm-12">
					<div class="table-responsive joystick-table-responsive">
						<table id="table-fields" class="table-mini table-hover full-width joystick-table">
						  <thead>
							  <tr>
								<?php if ($page == 'product_fields') { ?>
									<th colspan="4"><?php echo $text_table_product_fields; ?></th>
									<th colspan="2" ><?php echo $text_product_fields_set_table_codes; ?></th>
								<?php } else { ?>
									<th colspan="3"></th>
									<th colspan="2" ><?php echo $text_product_fields_set_table_codes; ?></th>
									<th colspan="2" style="min-width: 400px;"><?php echo $text_table_order_sql; ?></th>
								<?php } ?>
								<th><?php echo $text_process; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $text_excl_edit_product_field_info; ?>"></i></th>
								<th colspan="5" ><?php echo $text_editing; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $text_excl_edit_product_field_info; ?>"></i></th>
								<th colspan="<?php echo count($user_groups); ?>" ><?php echo $text_access_api_edit; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_access_api_edit_title; ?>"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-red" data-toggle="tooltip" title="<?php echo $text_excl_edit_product_field_info; ?>"></i></th>
								<th></th>
							  </tr>

							  <tr>
								<th style="width: 40px;"></th>
								<th class="text-right" style="width: 40px;">#</th>
								<?php if ($page == 'product_fields') { ?>
									<th class="text-center" style="width: 40px;" data-toggle="tooltip" title="Db Table">T</th>
									<th style="min-width: 180px;"><?php echo $text_field_name_in_db; ?></th>
									<th style="min-width: 260px;"><?php echo $text_field_name_in_table_codes; ?></th>
									<th style="min-width: 134px; width: 134px;"><?php echo $text_field_group_in_table_codes; ?></th>
								<?php } else { ?>
									<th class="text-center" style="min-width: 94px;"><?php echo $text_as_status; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_as_status_info; ?>"></i></th>
									<th style="min-width: 260px;"><?php echo $text_field_name_in_table_codes; ?></th>
									<th style="min-width: 134px; width: 134px;"><?php echo $text_field_group_in_table_codes; ?></th>
									<th style="min-width: 500px; width: 50%;"><?php echo $text_as_sql_select; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_as_sql_vars_info; ?>"></i></th>
									<th style="min-width: 180px;"><?php echo $text_as_sql_value; ?></th>
								<?php } ?>
								<th style="min-width: 180px;"><?php echo $text_process_method; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_api_method_title_short; ?>"></i></th>
								<th style="min-width: 200px;"><?php echo $text_edit_form; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_edit_form_title; ?>"></i></th>
								<th style="min-width: 200px;"><?php echo $text_edit_param; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_edit_param_title; ?>"></i></th>
								<th class="bg-om-aqua-light text-center" style="width: 44px;">R <i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_reload_onsave; ?>"></i></th>
								<th class="bg-om-yellow-light text-center" style="width: 44px;">Log <i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_log; ?>"></i></th>
								<th class="bg-om-red-light" style="min-width: 200px; text-align: center;"><?php echo $text_xedit_action; ?> <i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_xedit_action_help; ?>"></i></th>
								<?php foreach ($user_groups as $user_group) { ?>
								<th style="min-width: 100px;" class="text-center">
									<label><input type="checkbox" class="access_fields_all" data-groupid="<?php echo $user_group['user_group_id']; ?>"  data-toggle="tooltip" title="<?php echo $text_select_all; ?>" />&nbsp;&nbsp;<span data-toggle="tooltip" title="<?php echo $user_group['name']; ?>"><?php echo $user_group['name']; ?></span></label>
								</th>
								<?php } ?>
								<th></th>
							  </tr>
						  </thead>

						  <tbody id="tbody_fields" class="tbody-sortable">
						  <?php $row = 1; ?>
						  <?php if ($fields) { ?>
						  <?php foreach ($fields as $id => $field) { ?>
						  <tr id="row<?php echo $row; ?>" class="<?php echo $field['disabled']; ?>">
							<td class="text-center handle" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
							<td class="text-right"><?php echo $row; ?></td>

							<?php if ($page == 'product_as_fields') { ?>
								<td class="text-center">
									<input type="checkbox" name="fields[<?php echo $id; ?>][status]" value="1" <?php echo $field['status_checked']; ?> class="checkbox_status" />
								</td>

								<td class="text-left">
								  <div class="input-group full-width valid-block">
									<input type="text" name="fields[<?php echo $id; ?>][name]" value="<?php echo $field['name']; ?>" class="field-input mustbe form-control" />
								  </div>
								</td>

								<td>
								  <div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">
									<?php foreach ($field['data_group'] as $group) { ?>
										<label class="btn btn-default <?php echo $group['active']; ?>" title="<?php echo $group['text']; ?>"><input type="radio" name="fields[<?php echo $id; ?>][data_group_id]" value="<?php echo $group['id']; ?>" <?php echo $group['checked']; ?> /><i class="<?php echo $group['icon']; ?>"></i></label>
									<?php } ?>
								  </div>
								</td>

								<td class="text-left">
								  <div class="input-group full-width valid-block">
									<input type="text" name="fields[<?php echo $id; ?>][sql]" value="<?php echo $field['sql']; ?>" class="field-input sqlwithexclude form-control" />
									<span class="input-group-addon" title="<?php echo $text_sum_sql_value; ?>">
										<input type="checkbox" name="fields[<?php echo $id; ?>][sum]" value="1" <?php echo $field['sum_checked']; ?>/>
									</span>
								  </div>
								</td>
								<td class="text-left">
								  <div class="input-group full-width valid-block">
									<input type="text" name="fields[<?php echo $id; ?>][key]" value="<?php echo $field['key']; ?>" class="field-input unique_field_key field-key form-control" />
									<input type="hidden" name="fields[<?php echo $id; ?>][dbTable]" value="pcustom"/>
								  </div>
								</td>
							<?php } else { ?>
								<td class="text-center"><?php if ($field['dbTable'] == 'product') { echo 'P';} elseif ($field['dbTable'] == 'order_product') { echo 'OP';} elseif ($field['dbTable'] == 'manufacturer') { echo 'M';}; ?></td>
								<td class="text-left">
								<?php if ($field['dbTable'] !== 'product' && !in_array($field['key'], $exclude_op_m_sum_list)) { ?>
								  <div class="input-group full-width">
									<span class="input-group-addon text-left" style="width: calc(100% - 40px);"><?php echo $field['key']; ?></span>
									<span class="input-group-addon" style="width: 40px; border-left: 1px solid #d2d6de;" title="<?php echo $text_sum_field; ?>">
										<input type="checkbox" name="fields[<?php echo $id; ?>][sum]" value="1" <?php echo $field['sum_checked']; ?>/>
									</span>
								  </div>
								<?php } else { ?>
									<?php echo $field['key']; ?>
								<?php } ?>
								  <input type="hidden" name="fields[<?php echo $id; ?>][key]" value="<?php echo $field['key']; ?>" class="field-key" />
								  <input type="hidden" name="fields[<?php echo $id; ?>][dbTable]" value="<?php echo $field['dbTable']; ?>"/>
								</td>

								<td class="text-left">
								  <div class="input-group full-width valid-block">
									<input type="text" name="fields[<?php echo $id; ?>][name]" value="<?php echo $field['name']; ?>" class="field-input mustbe form-control" />
								  </div>
								</td>

								<td>
								  <div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">
									<?php foreach ($field['data_group'] as $group) { ?>
										<label class="btn btn-default <?php echo $group['active']; ?>" title="<?php echo $group['text']; ?>"><input type="radio" name="fields[<?php echo $id; ?>][data_group_id]" value="<?php echo $group['id']; ?>" <?php echo $group['checked']; ?> /><i class="<?php echo $group['icon']; ?>"></i></label>
									<?php } ?>
								  </div>
								</td>

							<?php } ?>

							<td>
							  <?php if (!in_array($field['key'], $exclude_edit_list) && !in_array($field['key'], $exclude_process_list)) { ?>
								<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][process_method]" class="form-control">
									<option value=""><?php echo $text_not_selected; ?></option>
									<?php if ($field['process_methods']) { ?>
									<?php foreach ($field['process_methods'] as $method) { ?>
									<option value="<?php echo $method['process_method']; ?>" <?php echo $method['selected']; ?>><?php echo $method['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							  <?php } ?>
							</td>

							<td>
							  <?php if (!in_array($field['key'], $exclude_edit_list)) { ?>
								<div class="input-group">
									<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][eform]" targetid="eparam-field<?php echo $id; ?>" fieldid="<?php echo $id; ?>" class="select-eforms form-control">
										<option value=""><?php echo $text_not_selected; ?></option>
										<?php if ($field['eforms']) { ?>
										<?php foreach ($field['eforms'] as $eform) { ?>
										<option value="<?php echo $eform['eform']; ?>" <?php echo $eform['selected']; ?>><?php echo $eform['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							  <?php } ?>
							</td>

							<td class="text-left" id="eparam-field<?php echo $id; ?>">
								<?php if (!in_array($field['key'], $exclude_edit_list) && isset($field['eform']) && $field['eform']) { ?>
								  <?php if ($field['eform'] == 'datetime' || $field['eform'] == 'inputmask') { ?>
									<div class="input-group valid-block">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $field['eparam_title']; ?>"><i class="fa fa-info  text-blue"></i></span>
										<input <?php echo $field['disabled']; ?> type="text" name="fields[<?php echo $id; ?>][eparam]" value="<?php echo $field['eparam']; ?>" placeholder="<?php echo $field['eparam_plholder']; ?>" class="field-input mustbe form-control"/>
									</div>
								  <?php } ?>

								  <?php if ($field['eform'] == 'selector_api' || $field['eform'] == 'checklist_api') { ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_api_tpl; ?>"><i class="fa fa-info text-blue"></i></span>
										<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][eparam]" class="form-control">
										  <option value=""><?php echo $text_not_selected; ?></option>
										  <?php if ($field['api_valuelist']) { ?>
										  <?php foreach ($field['api_valuelist'] as $api_value) { ?>
											<option value="<?php echo $api_value['key']; ?>" <?php echo $api_value['selected']; ?>><?php echo $api_value['name']; ?></option>
										  <?php } ?>
										  <?php } ?>
										</select>
									</div>
								  <?php } ?>

								  <?php if ($field['eform'] == 'selector_option' || $field['eform'] == 'checklist_option') { ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_option; ?>"><i class="fa fa-info text-blue"></i></span>
										<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][eparam]" class="form-control">
										  <option value=""><?php echo $text_not_selected; ?></option>
										  <?php if ($field['option_valuelist']) { ?>
										  <?php foreach ($field['option_valuelist'] as $option_value) { ?>
											<option value="<?php echo $option_value['key']; ?>" <?php echo $option_value['selected']; ?>><?php echo $option_value['name']; ?></option>
										  <?php } ?>
										  <?php } ?>
										</select>
									</div>
								  <?php } ?>

								  <?php if ($field['eform'] == 'custom_api') { ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_custom_api_tpl; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
										<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][eparam]" class="form-control">
										  <option value=""><?php echo $text_not_selected; ?></option>
										  <?php if ($field['ecustom_api']) { ?>
										  <?php foreach ($field['ecustom_api'] as $method) { ?>
											<option value="<?php echo $method['key']; ?>" <?php echo $method['selected']; ?>><?php echo $method['name']; ?></option>
										  <?php } ?>
										  <?php } ?>
										</select>
									</div>
								  <?php } ?>

								<?php } ?>
							</td>

							<td class="bg-om-aqua-light text-center">
							  <?php if (!in_array($field['key'], $exclude_edit_list)) { ?>
							  <input type="checkbox" class="minimal" name="fields[<?php echo $id; ?>][reload_onsave]" value="1" <?php echo $field['reload_onsave_checked']; ?>/>
							  <?php } ?>
							</td>

							<td class="bg-om-yellow-light text-center">
							  <?php if ($field['dbTable'] !== 'product' && !in_array($field['key'], $exclude_edit_list)) { ?>
							  <input type="checkbox" class="minimal" name="fields[<?php echo $id; ?>][log]" value="1" <?php echo $field['log_checked']; ?>/>
							  <?php } ?>
							</td>

							<td>
							  <?php if (!in_array($field['key'], $exclude_edit_list)) { ?>
								<select <?php echo $field['disabled']; ?> name="fields[<?php echo $id; ?>][action]" class="form-control">
									<option value=""><?php echo $text_not_selected; ?></option>
									<?php if ($field['action_methods']) { ?>
									<?php foreach ($field['action_methods'] as $method) { ?>
									<option value="<?php echo $method['action']; ?>" <?php echo $method['selected']; ?>><?php echo $method['name']; ?></option>
									<?php } ?>
									<?php } ?>
								</select>
							  <?php } ?>
							</td>

							<?php foreach ($field['edit_access'] as $access) { ?>
								<?php if (!in_array($field['key'], $exclude_edit_list)) { ?>
								<td class="text-center">
									<input type="checkbox" name="fields[<?php echo $id; ?>][edit_access][]" value="<?php echo $access['user_group_id']; ?>" <?php echo $access['checked']; ?> class="checkbox_access" field_access="<?php echo $access['user_group_id']; ?>" />
								</td>
								<?php } else { ?>
									<td></td>
								<?php } ?>
							<?php } ?>

							<td class="text-left">
							  <?php if (!in_array($field['key'], $exclude_edit_list) && !in_array($field['key'], $freeze_set_list)) { ?>
							  <?php if ($field['btn_drop'] && $page == 'product_fields') { ?>
								<button type="button" onclick="editTableField('<?php echo $field['dbTable']; ?>','drop', '<?php echo $field['key']; ?>');" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-sm"><i class="fa fa-minus-circle"></i></button>
							  <?php } elseif ($page == 'product_as_fields') { ?>
								<a target-removeID="#row<?php echo $row; ?>" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-sm"><i class="fa fa-minus-circle"></i></a>
							  <?php } ?>
							  <?php } ?>
							</td>

						  </tr>
							<?php $row++; ?>
						  <?php } ?>
						  <?php } else { ?>
							<tr id="no_result"><td class="text-center" colspan="99"><?php echo $text_no_results; ?></td></tr>
						  <?php } ?>
						  </tbody>

						  <?php if ($page == 'product_as_fields') { ?>
							<tfoot>
								<tr>
								  <td class="text-left"><button type="button" id="button_add_row" onclick="addProductField();" data-toggle="tooltip" title="<?php echo $button_add_row; ?>" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle"></i></button></td>
								  <td colspan="99"></td>
								</tr>
							</tfoot>
						  <?php } ?>
						</table>
					</div>
				  </div>
			  </div>

			</form>
		  </div>
		  <div class="tab-pane" id="tab_backup">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-backup" class="form-horizontal">
			<div class="callout callout-default">
				<p><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_backup_help; ?></p>
			</div>
			<div class="panel panel-default form-horizontal">
			  <div class="panel-body">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="button-backup"><?php echo $entry_save_to_file; ?></label>
					<div class="col-sm-10">
					  <a class="btn btn-primary" target="_blank" href="<?php echo $backup_link ?>" id="button-backup"><?php echo $button_save ?></a>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 control-label" for="restore"><?php echo $entry_restore_from_file; ?></label>
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
			</form>
		  </div>
		</div>
	  </div>

	</div>
  </div>


<input type="hidden" id="check_keys" value="<?php echo $check_keys; ?>"/>

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
	$('#form-backup').submit();
});

$('input[name="log_sql"]').on('change', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var log_sql = $(this).val() == 1 ? 1 : 0;
	$.ajax({
		url: 'index.php?route=sale/ompro/editAdminLogSql&<?php echo $strtoken; ?>&log_sql=' + log_sql,
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
				if (log_sql) { $('#alert_log_sql').show('slow'); } else { $('#alert_log_sql').hide('slow'); }

			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$(document).delegate('.select-eforms', 'change', function(e) {
	var fieldid = $(this).attr("fieldid");
	var target_id = $(this).attr("targetid");
	var etype = $(this).val();

	if (etype == 'selector_api' || etype == 'checklist_api') {
		html = '<div class="input-group">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_api_tpl; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += ' <select name="fields['+ fieldid +'][eparam]" class="form-control">';
		<?php foreach ($values_api_list as $sel) { ?>
		html += ' <option value="<?php echo $sel['key']; ?>"><?php echo $sel['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);
	}
	else if (etype == 'selector_option' || etype == 'checklist_option') {
		html = '<div class="input-group">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_option; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += ' <select name="fields['+ fieldid +'][eparam]" class="form-control">';
		html += ' <option value=""><?php echo $text_not_selected; ?></option>';
		<?php foreach ($values_option_list as $sel) { ?>
		html += ' <option value="<?php echo $sel['key']; ?>"><?php echo $sel['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);
	}
	else if (etype == 'custom_api') {
		html = '<div class="input-group">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_custom_api_tpl; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>';
		html += ' <select name="fields['+ fieldid +'][eparam]" class="form-control">';
		<?php foreach ($ecustom_method_list as $method) { ?>
		html += ' <option value="<?php echo $method['key']; ?>"><?php echo $method['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);
	}
	else if (etype == 'datetime' || etype == 'inputmask') {
		var eparam_value = ''; var eparam_title = ''; var eparam_plholder = '';
		if (etype == 'datetime') {
			eparam_value = eparam_plholder = 'yyyy-mm-dd||hh:ii';
			eparam_title = '<?php echo $text_format_datetime_info; ?>';
		} else if (etype == 'inputmask') {
			eparam_value = '+7 (999) 999-99-99';
			eparam_title = '<?php echo $text_filter_info_title_inputmask; ?>';
			eparam_plholder = '+375 (99) 999-99-99';
		}

		html = '<div class="input-group valid-block">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="'+eparam_title+'"><i class="fa fa-info text-blue"></i></span>';
		html += ' <input type="text" name="fields['+ fieldid +'][eparam]" value="'+eparam_value+'" placeholder="'+eparam_plholder+'" class="field-input mustbe form-control" />';
		html += '</div>';

		$('#'+target_id).html(html);
	} else {
		$('#'+target_id).html('');
	}

	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh();
});

$('.checkbox_access').checkboxpicker({
    groupCls: 'btn-group-sm',
    offLabel: '<?php echo $text_no; ?>',
    onLabel: '<?php echo $text_yes; ?>',
    switchAlways: true,
});

$('.checkbox_status').checkboxpicker({
    groupCls: 'btn-group-sm',
    offLabel: 'Off',
    onLabel: 'On',
    switchAlways: true,
});

$('.access_fields_all').on('change', function() {
	var groupid = $(this).attr('data-groupid');
	var check = $(this).prop('checked');
	$('[field_access="'+groupid+'"]').each(function() {
		$(this).prop('checked', check);
	});
});

$(document).delegate('#form, #form input, #form sellect, #form textarea', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

$('#button_save').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	$('tr.disabled').find('[disabled]').removeAttr('disabled');

	if (!window.validation.isValid({ container: '#form' })) {
		toastr.error('<?php echo $text_error_form; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	} else {
		setTimeout(function() { $('#form').submit(); }, 100 );
	}
});
//--></script>

<script type="text/javascript"><!--
$(document).delegate('[target-removeID]', 'click', function() {
	if ($(this).hasClass('new')) {
		$($(this).attr('target-removeID')).remove(); tooltipRefresh();
	} else if (confirm("Удаление этих данных может привести к ошибкам при выводе данных предыдущих заказов. Продолжить?!")) {
		$($(this).attr('target-removeID')).remove(); tooltipRefresh(); $('#form').trigger('change');
	} else { return false; }
});

<?php if ($page == 'product_fields') { ?>
	$(document).delegate('#button_alter_order_table', 'click', function() {
		var table = $('#alter_select_table').val();
		var field = $('#alter_table_field_name').val();
		editTableField(table, 'add', field);
	});

	function editTableField(table, action, field = '') {
		if (!has_permission) {
			toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
			return false;
		}
		if (action == 'add' && !window.validation.isValid({ container: '#table-add-fields' })) {
			toastr.error('<?php echo $text_error_form; ?>', '<?php echo $text_alert_error; ?>'); return false;
		}

		if (action == 'add' && !field) {
			var field = $('#alter_table_field_name').val();
		}

		var params = $('#alter_table_params').val();
		var data = {}; var sql = '';
		data['table'] = table;
		data['field'] = field;
		data['action'] = action;

		if (action == 'add') {
			sql = 'ALTER TABLE `<?php echo $db_prefix; ?>'+table+'` ADD `'+field+'` ' + params +';';
			data['params'] = params;
		} else if (action == 'drop') {
			sql = 'ALTER TABLE `<?php echo $db_prefix; ?>'+table+'` DROP `'+field+'`;';
		}

		if (sql && (action == 'add' && confirm('Вы действительно хотите добавить новое поле, выполнив запрос "'+sql+'" ?')) || (action == 'drop' && confirm('Вы действительно хотите удалить поле, выполнив запрос "'+sql+'" ?'))) {
			$.post('index.php?route=sale/ompro/editTableField&<?php echo $strtoken; ?>', data, function(data) {
				document.location.reload();
			});
		} else { return false; }
	}
<?php } ?>

<?php if ($page == 'product_as_fields') { ?>
var row = '<?php echo $row; ?>';

function addProductField() {
	html  = '<tr id="row'+row+'">';
	html += ' <td class="text-center handle" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>';
	html += ' <td class="text-right">'+row+'</td>';
	html += ' <td class="text-center">';
	html += '   <input type="checkbox" name="fields['+row+'][status]" value="1" class="checkbox_status" />';
	html += ' </td>';
	html += ' <td class="text-left">';
	html += '  <div class="input-group full-width valid-block">';
	html += '   <input type="text" name="fields['+row+'][name]" value="" class="field-input mustbe form-control" />';
	html += '  </div> ';
	html += ' </td>';
	html += ' <td class="text-left">';
	html += '  <div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">';
	<?php foreach ($product_data_group_list as $group) { ?>
	html += '  <label class="btn btn-default" title="<?php echo $group['text']; ?>"><input type="radio" name="fields['+row+'][data_group_id]" value="<?php echo $group['id']; ?>" /><i class="<?php echo $group['icon']; ?>"></i></label> ';
	<?php } ?>
	html += '  </div> ';
	html += ' </td>';

	html += ' <td class="text-left">';
	html += '  <div class="input-group full-width valid-block">';
	html += '   <input type="text" name="fields['+row+'][sql]" value="" class="field-input sqlwithexclude form-control" />';
	html += '  <span class="input-group-addon" title="<?php echo $text_sum_sql_value; ?>">';
	html += '   <input type="checkbox" name="fields['+row+'][sum]" value="1"/>';
	html += '  </span> ';
	html += '  </div> ';
	html += ' </td>';
	html += ' <td class="text-left">';
	html += '  <div class="input-group full-width valid-block">';
	html += '   <input type="text" name="fields['+row+'][key]" value="" class="field-input unique_field_key field-key form-control" />';
	html += '   <input type="hidden" name="fields['+row+'][dbTable]" value="pcustom"/>';
	html += '  </div> ';
	html += ' </td>';
	html += ' <td>';
	html += '  <select name="fields['+row+'][process_method]" class="form-control">';
	html += '   <option value=""><?php echo $text_not_selected; ?></option>';
	<?php if ($product_process_method_list) { ?>
	<?php foreach ($product_process_method_list as $method) { ?>
	html += '   <option value="<?php echo $method['process_method']; ?>"><?php echo $method['name']; ?></option>';
	<?php } ?>
	<?php } ?>
	html += '  </select>';
	html += ' </td>';

	html += ' <td>';
	html += ' <div class="input-group">';
	html += '  <select name="fields['+row+'][eform]" targetid="eparam-field'+row+'" fieldid="'+row+'" class="select-eforms form-control">';
	html += '   <option value=""><?php echo $text_not_selected; ?></option>';
	<?php foreach ($eform_list as $form) { ?>
	html += '   <option value="<?php echo $form['id']; ?>"><?php echo $form['name']; ?></option>';
	<?php } ?>
	html += '  </select>';
	html += '  </div>';
	html += ' </td>';
	html += ' <td class="text-left" id="eparam-field'+row+'"></td>';
	html += ' <td class="bg-om-aqua-light"><input type="checkbox" class="minimal" name="fields['+row+'][reload_onsave]" value="1" /></td>';

	html += ' <td class="bg-om-yellow-light">';
	html += ' <input type="checkbox" class="minimal" name="fields['+row+'][log]" value="1" />';
	html += ' </td>';

	html += ' <td>';
	html += '  <select name="fields['+row+'][action]" class="form-control">';
	html += '   <option value=""><?php echo $text_not_selected; ?></option>';
	<?php if ($action_method_list) { ?>
	<?php foreach ($action_method_list as $method) { ?>
	html += '   <option value="<?php echo $method['action']; ?>"><?php echo $method['name']; ?></option>';
	<?php } ?>
	<?php } ?>
	html += '  </select>';
	html += ' </td>';

	<?php foreach ($user_groups as $user_group) { ?>
	html += ' <td class="text-center">';
	html += '  <input type="checkbox" data-group-cls="btn-group-sm" data-switch-always name="fields['+row+'][edit_access][]" value="<?php echo $user_group['user_group_id']; ?>" class="checkbox_access" field_access="<?php echo $user_group['user_group_id']; ?>" />';
	html += ' </td>';
	<?php } ?>

	html += ' <td class="text-left">';
	html += '  <a target-removeID="#row' + row + '" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger btn-sm new"><i class="fa fa-minus-circle"></i></a>';
	html += ' </td>';
	html += '</tr>';

	if ($('#ap_no_result').html() !='') {
		$('#ap_no_result').remove();
	}

	$('#tbody_fields').append(html);

	$(function () { window.validation.init({ container: '#form' });});

	$('.checkbox_access').checkboxpicker({
		groupCls: 'btn-group-sm',
		offLabel: '<?php echo $text_no; ?>',
		onLabel: '<?php echo $text_yes; ?>',
		switchAlways: true,
	});

	$('.checkbox_status').checkboxpicker({
		groupCls: 'btn-group-sm',
		offLabel: 'Off',
		onLabel: 'On',
		switchAlways: true,
	});

	iCheckStart();
	tooltipRefresh('#table-fields');
	$('#form').trigger('change');
	row++;
}
<?php } ?>

//--></script>

<script type="text/javascript"><!--

$(document).ready(function(){
	var dropElSelector = '.tbody-sortable';
	var param = {}; param['group'] = {};
	param['group']['name'] = 'table-rows';
	param['handle'] = '.handle';
	param['draggable'] = 'tr';
	param['ghostClass'] = 'sortable-elem';

	sortableStartGroup(dropElSelector, param);

	$(function () { window.validation.init({ container: '#form' });});
	$(function () { window.validation.init({ container: '#table-add-fields' });});

	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.options.timeOut = 5000;
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	iCheckStart();
	tooltipRefresh();

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

	joystickStart();
});

//--></script>

</div>
<?php echo $footer; ?>