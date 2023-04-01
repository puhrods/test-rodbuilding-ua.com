<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">

<link rel="stylesheet" type="text/css" href="view/javascript/ompro/codemirror/lib/codemirror.css"/>
<script type="text/javascript" src="view/javascript/ompro/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/addon/matchbrackets.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/addon/autorefresh.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/mode/xml.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/mode/css.js"></script>
<script type="text/javascript" src="view/javascript/ompro/codemirror/mode/javascript.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/codemirror/theme/rubyblue.css"/>

<link rel="stylesheet" href="view/javascript/ompro/summernote/summernote.css" type="text/css"/>
<script type="text/javascript" src="view/javascript/ompro/summernote/summernote.js"></script>
<script type="text/javascript" src="view/javascript/ompro/summernote/opencart.js"></script>
<script type="text/javascript" src="view/javascript/ompro/summernote/lang/summernote-ru-RU.js"></script>

<script src="view/javascript/ompro/Sortable.js"></script>

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

<section class="content template-form template-editor">
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
			<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>

	<div class="box-body">
		<div class="nav-tabs-custom">
		  <ul class="nav nav-tabs" id="nav-tabs">
            <li><a href="#tab-columns" data-toggle="tab"><?php echo $tab_template_columns; ?></a></li>
            <li><a href="#tab-html-css" data-toggle="tab"><?php echo $tab_template_html_css; ?></a></li>
            <li><a href="#tab-preview" data-toggle="tab" id="trigger-preview"><?php echo $tab_template_preview; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-columns">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_order.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $entry_table_orders_info; ?></p>
			  </div>
			  <div class="row">
				<div class="col-sm-12">
				  <div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $entry_table_head_status; ?></label>
					<div class="col-sm-4">
						<select name="template[head_status]"class="form-control">
							<option value="1" <?php if (isset($template['head_status']) && $template['head_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (isset($template['head_status']) && $template['head_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
						</select>
					</div>
					<label class="col-sm-2 control-label"><?php echo $entry_table_checkbox_status; ?></label>
					<div class="col-sm-4">
					  <div class="input-group">
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_table_checkbox_status_title; ?>"><i class="fa fa-info text-blue"></i></span>
						<select name="template[checkbox_status]"class="form-control">
							<option value="1" <?php if (isset($template['checkbox_status']) && $template['checkbox_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (isset($template['checkbox_status']) && $template['checkbox_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
						</select>
					  </div>
					</div>
				  </div>

				  <div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $entry_table_filter_row_status; ?></label>
					<div class="col-sm-4">
						<select name="template[filter_row_status]" class="form-control">
							<option value="1" <?php if (isset($template['filter_row_status']) && $template['filter_row_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (isset($template['filter_row_status']) && $template['filter_row_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
						</select>
					</div>
					<label class="col-sm-2 control-label"><?php echo $entry_table_joystick_status; ?></label>
					<div class="col-sm-4">
					  <div class="input-group">
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_table_joystick_status_help; ?>"><i class="fa fa-info text-blue"></i></span>
						<select name="template[joystick_status]" class="form-control">
							<option value="1" <?php if (isset($template['joystick_status']) && $template['joystick_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (isset($template['joystick_status']) && $template['joystick_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
						</select>
					  </div>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#product-tables-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_order_table_info_block_title; ?></a></div>
					<div id="product-tables-info" class="panel-collapse collapse">
					  <div class="panel-body">
						<p>
							<i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_order_table_info_block_1; ?>
							<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_order_table_info_block_2; ?>
							<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_order_table_info_block_3; ?>
							<br/><?php echo $text_order_table_codes_help; ?>
						</p>
						<div class="row">
							<div class="col-lg-6 col-sm-12">
								<a class="btn btn-default btn-block" onclick="getTableCodes('product_table');"><i class="fa fa-th-list"></i>&nbsp;  <?php echo $btn_product_table_tpl; ?></a>
								<a class="btn btn-default btn-block" onclick="getTableCodes('history_table');"><i class="fa fa-history"></i>&nbsp;  <?php echo $btn_history_table_tpl; ?></a>
							</div>
							<div class="col-lg-6 col-sm-12">
								<h5><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <strong><?php echo $entry_adding_codes; ?></strong></h5>
								<div class="callout callout-default"><p><?php echo $adding_codes_orders_info; ?></p></div>
							</div>
						</div>
					  </div>
					</div>
				  </div>

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
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_print_orders_example; ?>
									<?php echo $table_btn_print_orders; ?>
									</p>
								  </div>
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_print_orders_table_example; ?>
									<?php echo $table_btn_print_orders_table; ?></p>
								  </div>
								</div>
							  </div>
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
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
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_send_mail_example; ?>
									<?php echo $table_btn_send_mail; ?>
									</p>
								  </div>
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
									<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $table_btn_send_sms_example; ?>
									<?php echo $table_btn_send_sms; ?>
									</p>
								  </div>
								</div>
							  </div>
							  <div class="panel-body">
								<div class="row">
								  <div class="col-sm-6" style="max-height: 360px; overflow: auto;">
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

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-total"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_order_totals_heading; ?></a></div>
					<div id="set-total" class="panel-collapse collapse">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-3 control-label">
								<p><?php echo $entry_order_totals_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_hel2; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-9">
							  <textarea name="template[total_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['total_tpl']) ? $template['total_tpl'] : '<b>{total_name}:</b> {total_value}<br>'; ?></textarea>
							</div>
						  </div>
						</div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-table"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_table; ?></a></div>
					<div id="set-table" class="panel-collapse collapse in">
					  <div class="panel-body">

						  <div class="row">
							<div class="col-sm-12">
							<div class="table-responsive">
							  <table class="table table-bordered table-hover" id="order-table">
								<thead>
									<tr class="active">
										<th class="text-center" style="width: 1%;">#</th>
										<th style="width: 20%;"><?php echo $column_title_param; ?></th>
										<th style="width: 20%;"><?php echo$column_filter_param; ?></th>
										<th style="width:59%;"><?php echo $column_cell_data; ?>&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('order');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a></th>
									</tr>
								</thead>

								<tbody class="tbody-sortable">
									<tr id="order-table-row1" class="checkbox-row">
										<td class="text-center" data-toggle="tooltip" title="<?php echo $param_checkbox_info; ?>"> <i class="fa fa-info text-blue"></i> </td>
										<td>...</td>
										<td>...</td>
										<td><input type="checkbox" checked disabled class="minimal" /></td>
									</tr>

								<?php $row = 2; ?>
								<?php if (!empty($template['columns'])) { ?>
								  <?php foreach ($template['columns'] as $column_id => $column) { ?>
									<tr id="order-table-row<?php echo $column_id; ?>">
										<td class="text-center handle"><i class="fa fa-arrows" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"></i><br><?php echo $column_id; ?>
											<input type="hidden" name="template[columns][<?php echo $column_id; ?>][id]" value="<?php echo $column_id; ?>" />
										</td>

										<td>
											<div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_title; ?>"><i class="fa fa-text-width"></i></span>
												<input type="text" name="template[columns][<?php echo $column_id; ?>][name]" value="<?php echo isset($column['name']) ? $column['name'] : ''; ?>" class="column-name form-control" placeholder="<?php echo $param_name_placeholder; ?>" />
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</div>

											<br/>
											<div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_sort_title; ?>"><i class="fa fa-sort-amount-desc"></i></span>
												<select name="template[columns][<?php echo $column_id; ?>][sort]" class="select-sort-key multiselect" data-column_id="<?php echo $column_id; ?>">
													<option value="" ><?php echo $text_option_select_sort; ?></option>
													<?php foreach ($order_codes as $code) { ?>
													  <?php if ($code['sort_key']) { ?>
														<option value="<?php echo $code['sort_key']; ?>" codeTitle="<?php echo $code['title']; ?>" <?php if (isset($column['sort']) && $column['sort'] == $code['sort_key']) { ?> selected="selected"<?php } ?>> <?php echo $code['name']; ?></option>
													  <?php } ?>
													<?php } ?>
												</select>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_sort_info; ?>"><i class="fa fa-info text-blue"></i></span>
												<input type="hidden" id="sort_by<?php echo $column_id; ?>" name="template[columns][<?php echo $column_id; ?>][sort_by]" value="<?php echo isset($column['sort_by']) ? $column['sort_by'] : ''; ?>"/>
											</div>

											<br/>
											<div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_toggle_class_title; ?>"><i class="fa fa-sort"></i></span>
												<input type="text" name="template[columns][<?php echo $column_id; ?>][toggle_class]" value="<?php echo isset($column['toggle_class']) ? $column['toggle_class'] : ''; ?>" class="form-control" placeholder="<?php echo $param_toggle_class_placeholder; ?>"/>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_toggle_class_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</div>

											<br/>
											<div class="valid-block">
												<span class="input-group">
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_title; ?>"><i class="fa fa-css3"></i></span>
													<input type="text" name="template[columns][<?php echo $column_id; ?>][class]" value="<?php echo isset($column['class']) ? $column['class'] : ''; ?>" class="field-input validclass form-control" placeholder="class"/>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_classes_help; ?>"><i class="fa fa-info text-blue"></i></span>
												</span>
											</div>
										</td>

										<td>
											<?php if (!empty($filters)) { ?>
											  <div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_title; ?>"><i class="fa fa-filter"></i></span>
												<select name="template[columns][<?php echo $column_id; ?>][filter_template_id]" class="multiselect">
													<option value="" ><?php echo $text_option_select_filter; ?></option>
												  <?php foreach ($filters as $filter) { ?>
													<option value="<?php echo $filter['filter_id']; ?>" data-toggle="tooltip" title="<?php echo $filter['description']; ?>" <?php if (isset($column['filter_template_id']) && $column['filter_template_id'] == $filter['filter_id']) { ?> selected="selected"<?php } ?>>&nbsp;&nbsp;<?php echo $filter['name']; ?></option>
												  <?php } ?>
												</select>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_reload_info; ?>">
												  <input type="checkbox" value="1" name="template[columns][<?php echo $column_id; ?>][filter_reload]" <?php if (isset($column['filter_reload']) && $column['filter_reload'] == 1) { ?> checked="checked"<?php } ?> />
												</span>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_info; ?>"><i class="fa fa-info text-blue"></i></span>
											  </div>
											  <br/>
											<?php } ?>

											<span class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_size_title; ?>"><i class="fa fa-filter" style="text-decoration: underline;"></i></span>
												<select name="template[columns][<?php echo $column_id; ?>][filter_size]" class="form-control">
											  <?php foreach ($filter_size_list as $filter) { ?>
												<option value="<?php echo $filter['size']; ?>" <?php if (isset($column['filter_size']) && $column['filter_size'] == $filter['size']) { ?> selected="selected"<?php } ?>><?php echo $filter['text']; ?></option>
											  <?php } ?>
												</select>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_size_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</span>

											<br/>
											<span class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_color_source_title; ?>"><i class="fa fa-adjust"></i></span>
												<select name="template[columns][<?php echo $column_id; ?>][color_source]" class="form-control">
												  <option value=""><?php echo $text_option_select_colors; ?></option>
												  <?php foreach ($color_source_list as $source) { ?>
													<option value="<?php echo $source['value']; ?>" <?php if (isset($column['color_source']) && $column['color_source'] == $source['value']) { ?> selected="selected"<?php } ?>><?php echo $source['text']; ?></option>
												  <?php } ?>
												</select>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_color_source_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</span>

											<br/>
											<div class="valid-block">
												<span class="input-group">
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_title; ?>"><i class="fa fa-code"></i></span>
													<textarea name="template[columns][<?php echo $column_id; ?>][style]" class="field-input notcyrillics form-control" placeholder="style" rows="1"><?php echo isset($template['columns'][$column_id]['style']) ? $template['columns'][$column_id]['style'] : ''; ?></textarea>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_styles_help; ?>"><i class="fa fa-info text-blue"></i></span>
												</span>
											</div>

											<br/>
											<div class="input-group">
											  <div class="input-group-btn" data-toggle="tooltip" title="<?php echo $param_column_remove; ?>">
												<a onclick="$('#order-table-row<?php echo $column_id; ?>').remove(); tooltipRefresh();"  class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a>
											  </div>

											  <select name="template[columns][<?php echo $column_id; ?>][status]" class="<?php echo isset($column['status']) && $column['status'] == 1 ? 'text-olive' : 'text-red'; ?> form-control" >
												<option value="1" <?php if (isset($column['status']) && $column['status'] == 1) { ?> selected="selected"<?php } ?> class="text-olive"><?php echo $text_enabled; ?></option>
												<option value="0" <?php if (isset($column['status']) && $column['status'] == '0') { ?> selected="selected"<?php } ?> class="text-red"><?php echo $text_disabled; ?></option>
											  </select>
											  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_column_status_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</div>

										</td>

										<td>
										  <div class="box box-default no-border">
											<div class="box-header with-border">
												<div class="pull-left box-tools" style="width: 10%; max-width: 80px;">
												  <div class="input-group">
													<div class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_summernote_editor; ?>">
														<div class="btn-group" data-toggle="buttons">
															<label class="btn btn-default" onclick="summernote('<?php echo $column_id; ?>');"><input type="checkbox"/><i id="btn_start_editor_icon<?php echo $column_id; ?>" class="fa fa-edit text-red"></i></label>
														</div>
													</div>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $btn_summernote_editor_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
												  </div>
												</div>

												<div class="pull-right box-tools" id="load-block-<?php echo $column_id; ?>" style="width: calc(100% - 85px); display: none;">
												  <div class="input-group">
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">
													  <input type="checkbox" id="tpl-add<?php echo $column_id; ?>" value="1" />
													</span>
													<select data-add-id="<?php echo $column_id; ?>" class="load-tpl-block form-control">
														<option value="" >&nbsp;&nbsp;<?php echo $text_load_tpl; ?></option>
													  <?php foreach ($order_blocks as $block) { ?>
														<option value="<?php echo $block['template_id']; ?>" >&nbsp;&nbsp;<?php echo $block['template_id'] .' - '.$block['name']; ?></option>
													  <?php } ?>
													</select>
													<span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock('order', 'order-table-row<?php echo $column_id; ?>');" ><i class="fa fa-save"></i></a></span>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>
												  </div>
												</div>
											</div>
											<div class="box-body">
												<textarea name="template[columns][<?php echo $column_id; ?>][data]" id="textarea<?php echo $column_id; ?>" rows="8" class="column-data form-control" placeholder="HTML code"><?php echo isset($column['data']) ? $column['data'] : ''; ?></textarea>
											</div>
										  </div>
										</td>
									</tr>
									<?php $row=$column_id>=$row?$column_id+1:$row; ?>
								  <?php } ?>
								<?php } else { ?>
									<tr id="columns_no_results"><td colspan="6" class="text-center"><?php echo $text_no_results; ?></td></tr>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr class="active">
										<td></td>
										<td colspan="4" id="order-table-add-column"><a onclick="addColumn('<?php echo $row; ?>');" class="btn btn-success" data-toggle="tooltip" title="<?php echo $button_add_column; ?>"><i class="fa fa-plus"></i> <?php echo $button_add_column; ?></a></td>
									</tr>
								</tfoot>
							  </table>
							</div>
							</div>
						  </div>

					  </div>
					</div>
				  </div>
			  </div>

			</div>

			<!-- HTML & CSS -->
			<div class="tab-pane" id="tab-html-css">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_order.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $entry_table_class_style_info; ?></p>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <div class="input-group">
					<span class="input-group-addon" style="width: 90px;"><?php echo $entry_class_table; ?></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_elem; ?>"><i class="fa fa-css3"></i></span>
					<input type="text" name="template[class][table]" value="<?php echo isset($template['class']['table']) ? $template['class']['table'] : 'table table-bordered table-striped'; ?>" class="field-input validclass form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_elem; ?>"><i class="fa fa-code"></i></span>
					<input name="template[style][table]" value="<?php echo isset($template['style']['table']) ? $template['style']['table'] : ''; ?>" class="field-input notcyrillics form-control" placeholder="style" />
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <div class="input-group">
					<span class="input-group-addon" style="width: 90px;"><?php echo $entry_class_thead; ?></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_elem; ?>"><i class="fa fa-css3"></i></span>
					<input type="text" name="template[class][thead]" value="<?php echo isset($template['class']['thead']) ? $template['class']['thead'] : ''; ?>" class="field-input validclass form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_elem; ?>"><i class="fa fa-code"></i></span>
					<input name="template[style][thead]" value="<?php echo isset($template['style']['thead']) ? $template['style']['thead'] : ''; ?>" class="field-input notcyrillics form-control" placeholder="style" />
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <div class="input-group">
					<span class="input-group-addon" style="width: 90px;"><?php echo $entry_class_th; ?></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_elem; ?>"><i class="fa fa-css3"></i></span>
					<input type="text" name="template[class][th]" value="<?php echo isset($template['class']['th']) ? $template['class']['th'] : ''; ?>" class="field-input validclass form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_elem; ?>"><i class="fa fa-code"></i></span>
					<input name="template[style][th]" value="<?php echo isset($template['style']['th']) ? $template['style']['th'] : ''; ?>" class="field-input notcyrillics form-control" placeholder="style" />
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <div class="input-group">
					<span class="input-group-addon" style="width: 90px;"><?php echo $entry_class_tbody; ?></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_elem; ?>"><i class="fa fa-css3"></i></span>
					<input type="text" name="template[class][tbody]" value="<?php echo isset($template['class']['tbody']) ? $template['class']['tbody'] : ''; ?>" class="field-input validclass form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_elem; ?>"><i class="fa fa-code"></i></span>
					<input name="template[style][tbody]" value="<?php echo isset($template['style']['tbody']) ? $template['style']['tbody'] : ''; ?>" class="field-input notcyrillics form-control" placeholder="style" />
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <div class="input-group">
					<span class="input-group-addon" style="width: 90px;"><?php echo $entry_class_td; ?></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_elem; ?>"><i class="fa fa-css3"></i></span>
					<input type="text" name="template[class][td]" value="<?php echo isset($template['class']['td']) ? $template['class']['td'] : ''; ?>" class="field-input validclass form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_elem; ?>"><i class="fa fa-code"></i></span>
					<input name="template[style][td]" value="<?php echo isset($template['style']['td']) ? $template['style']['td'] : ''; ?>" class="field-input notcyrillics form-control" placeholder="style" />
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<label class="col-sm-2 control-label">CSS:</label>
				<div class="col-sm-10">
				  <textarea name="template[css]" rows="10" class="cssTextArea field-input notcyrillics form-control"><?php echo isset($template['css']) ? $template['css'] : ''; ?></textarea>
				</div>
			  </div>

			  <div class="form-group">
				<label class="col-sm-2 control-label">Java Script:</label>
				<div class="col-sm-10">
				  <textarea name="template[script]" rows="10" class="jsTextArea form-control"><?php echo isset($template['script']) ? $template['script'] : ''; ?></textarea>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab-preview">
			  <div class="form-group">
				<div class="col-sm-3">
				  <div class="input-group full-width" style="height: 34px;">
					<div class="input-group-btn"><a class="btn btn-info" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_order.html#tab=0&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i></a></div>
					<span class="input-group-addon text-left" style="width: calc(100% - 40px);"><?php echo $text_view_edit_link; ?></span>
					<span class="input-group-addon" style="width: 40px; border-left: 1px solid #d2d6de;">
						<input type="checkbox" id="view_edit_link" value="1" style="cursor: pointer;"/>
					</span>
				  </div>
				</div>
			  </div>
			  <div class="form-group"><div class="col-sm-12" id="preview-content"></div></div>
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

$(document).delegate('#form, #form input, #form sellect, #form textarea', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

$(document).delegate('.select-sort-key', 'change', function() {
	var column_id = $(this).data('column_id');
	var codeTitle = $(this).find('option:selected').attr('codeTitle');
	if (codeTitle) {
		$('#sort_by'+column_id).val(codeTitle);
	} else {
		$('#sort_by'+column_id).val('');
	}
});

$('.select-sort-key').trigger('change');

$(document).delegate('#trigger-preview', 'click', function() {
	if ($('#preview-content').html() == '') { getOrdersTable(); }
});

$(document).delegate('#view_edit_link', 'change', function() {
	getOrdersTable();
});

function getOrdersTable() {
	var tpl_code = '<?php echo $tpl_code; ?>';

	if (!tpl_code) {
		alert('<?php echo $error_template_preview; ?>'); return false;
	}

	var url = 'index.php?route=sale/ompro/getOrdersTable&<?php echo $strtoken; ?>&constructor=true&tpl_code=' + encodeURIComponent(tpl_code) + '&limit=5&order=DESC';

	to_edit = $('#view_edit_link').prop('checked') ? 1 : 0;
	if (to_edit) {
		url += '&to_edit=true';
	}

	var data = filter();

	$.ajax({
		url: url,
		dataType: 'json',
		data: data,
		type: 'POST',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+ json['error']+'</p>', '');
			}
			if (json['content']) {
				$('#preview-content').html(json['content']);
				dpStart();
				multiselectStart();
				iCheckStart();
				joystickStart();
				tooltipRefresh('#preview-content');
				xEditableStart();
				$('[data-mask]').inputmask();

			} else {
				$('#preview-content').html('<?php echo $text_no_results; ?>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

var lang = '<?php echo $summernote_lang; ?>';
var toolbar = [['insert',['picture','link','video','table']], ['style',['bold','italic','underline']], ['font', ['strikethrough', 'superscript', 'subscript']],['fontsize', ['fontsize','fontname']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph','style']], ['height', ['height','codeview']],];
var fontNames = ['Arial','Times New Roman','Helvetica','Verdana'];
var placeholder = '<?php echo $text_summernote_placeholder; ?>';

function summernote(id) {
	var td = $('#textarea'+id).closest('td');
	var load_block = $('#load-block-'+id);
	if (!td.hasClass('editing')) {
		var code = $('#textarea'+id).val();
		$('#textarea'+id).summernote({
			callbacks: {
				onChange: function(contents, $editable) {
					if ($('#button_save').hasClass('btn-primary')) {
						$('#button_save').removeClass('btn-primary').addClass('btn-danger');
					}
				}
			},
			blank: '', emptyPara: '', lang: lang, height:'auto', toolbar: toolbar,fontNames: fontNames, placeholder: placeholder,
			codemirror: { height:'auto', mode: "text/html", theme: 'rubyblue'﻿, lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true }
		});

		$('#textarea'+id).summernote('code', code);
		load_block.show();
		td.addClass('editing');
		toastr.options.timeOut = 2000;
		toastr.success('Редактор включен!', 'Выполнено:' );
	} else {
		load_block.hide();
		td.removeClass('editing');
		var code = $('#textarea'+id).summernote('code');
		$('#textarea'+id).summernote('destroy');
		$('#textarea'+id).val(code);
		toastr.options.timeOut = 2000;
		toastr.warning('Редактор выключен!', 'Выполнено:' );
	}
	$('#btn_start_editor_icon'+id).toggleClass('text-red text-blue');
}

function editProductTable() {
	location = '<?php echo $server; ?>index.php?route=sale/ompro/templateEdit&<?php echo $strtoken; ?>&get_page=product&template_id=' + $('#input-product-table').val();
}

function saveBlock(target, parent_id) {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var data = {};
	data['template'] = {};
	data['template']['target'] = target;

	var name = $('#'+ parent_id +' .column-name').val();

	if (name == '') {
		toastr.error('<?php echo $text_error_name_block; ?>', '<?php echo $text_alert_error; ?>');
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

$(document).delegate('.load-tpl-block', 'change', function() {
	var template_id = $(this).val();
	if (!template_id) {return;}
	var add_id = $(this).attr('data-add-id');
	var add_status = $('#tpl-add'+add_id).prop('checked') ? 1 : 0;
 	var selected = $('#textarea'+add_id).summernote('createRange');
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
						$('#textarea'+add_id).summernote('code', json['tpl']);
					} else {
						$('#textarea'+add_id).summernote('editor.pasteHTML', json['tpl']);
					}
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

function addColumn(row){
	html = '<tr id="order-table-row'+row+'">';
	html += '<td class="text-center handle"><i class="fa fa-arrows" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"></i><br>';
	html += row+'<input type="hidden" name="template[columns]['+row+'][id]" value="' + row +'"/>';
	html += '</td>';

	html += '<td>';
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_title; ?>"><i class="fa fa-text-width"></i></span>';
	html += '  <input type="text" name="template[columns]['+row+'][name]" value="" class="column-name form-control" placeholder="<?php echo $param_name_placeholder; ?>" />';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';
	html += '<br/>';
	html += ' <div class="input-group">';
	html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_sort_title; ?>"><i class="fa fa-sort-amount-desc"></i></span>';
	html += ' <select name="template[columns]['+row+'][sort]" class="multiselect">';
	html += '  <option value=""><?php echo $text_option_select_sort; ?></option>';
	<?php foreach ($order_codes as $code) { ?>
	<?php if ($code['sort_key']) { ?>
	html += '  <option value="<?php echo $code['sort_key']; ?>"> <?php echo $code['name']; ?></option>';
	<?php } ?>
	<?php } ?>
	html += ' </select>';
	html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_sort_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';
	html += '<br/>';
	html += ' <div class="input-group">';
	html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_toggle_class_title; ?>"><i class="fa fa-sort"></i></span>';
	html += ' <input type="text" name="template[columns]['+row+'][toggle_class]" value="" class="form-control" placeholder="<?php echo $param_toggle_class_placeholder; ?>"/>';
	html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_toggle_class_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';

	html += '<br/>';
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_title; ?>"><i class="fa fa-css3"></i></span>';
	html += '  <input type="text" name="template[columns]['+row+'][class]" value="" class="field-input validclass form-control " placeholder="class"/>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_classes_help; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div>';

	html += '</td>';
	html += '<td>';
	<?php if (!empty($filters)) { ?>
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_title; ?>"><i class="fa fa-filter"></i></span>';
	html += '  <select name="template[columns]['+row+'][filter_template_id]" class="multiselect">';
	html += '   <option value="" ><?php echo $text_option_select_filter; ?></option>';
	  <?php foreach ($filters as $filter) { ?>
	html += '   <option value="<?php echo $filter['template_id']; ?>">&nbsp;&nbsp;<?php echo $filter['name']; ?></option>';
	  <?php } ?>
	html += '  </select>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_reload_info; ?>">';
	html += '   <input type="checkbox" value="1" name="template[columns]['+row+'][filter_reload]" checked="checked" />';
	html += '  </span>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';
	html += '<br/>';
	<?php } ?>
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_size_title; ?>"><i class="fa fa-filter" style="text-decoration: underline;"></i></span>';
	html += '  <select name="template[columns]['+row+'][filter_size]"class="form-control">';
	<?php foreach ($filter_size_list as $filter) { ?>
	html += '   <option value="<?php echo $filter['size']; ?>"><?php echo $filter['text']; ?></option>';
	<?php } ?>
	html += '  </select>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_filter_size_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';
	html += '<br/>';
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_color_source_title; ?>"><i class="fa fa-adjust"></i></span>';
	html += '  <select name="template[columns]['+row+'][color_source]" class="form-control">';
	html += '    <option value=""><?php echo $text_option_select_colors; ?></option>';
	<?php foreach ($color_source_list as $source) { ?>
	html += '    <option value="<?php echo $source['value']; ?>"><?php echo $source['text']; ?></option>';
	<?php } ?>
	html += '  </select>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_color_source_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '</div>';

	html += '<br/>';
	html += ' <div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_title; ?>"><i class="fa fa-code"></i></span>';
	html += '  <textarea name="template[columns]['+row+'][style]" class="field-input notcyrillics form-control" placeholder="style" rows="1"></textarea>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_styles_help; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div>';

	html += '<br/>';
	html += ' <div class="input-group">';
	html += '  <div class="input-group-btn">';
	html += '   <a onclick="$(\'#order-table-row'+row+'\').remove();"  class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a>';
	html += '  </div>';
	html += '  <select name="template[columns]['+row+'][status]" class="text-green form-control">';
	html += '   <option value="1" class="text-green"><?php echo $text_enabled; ?></option>';
	html += '   <option value="0" class="text-red"><?php echo $text_disabled; ?></option>';
	html += '  </select>';
	html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_column_status_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div>';

	html += '</td>';
	html += '<td>';
	html += ' <div class="box box-default no-border">';
	html += '  <div class="box-header with-border">';
	html += '   <div class="pull-left box-tools" style="width: 10%; max-width: 80px;"><div class="input-group"><div class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_summernote_editor; ?>"><div class="btn-group" data-toggle="buttons"><label class="btn btn-default" onclick="summernote(\''+row+'\');"><input type="checkbox"/><i id="btn_start_editor_icon'+row+'" class="fa fa-edit text-red"></i></label></div></div><span class="input-group-addon" data-toggle="tooltip" title="<?php echo $btn_summernote_editor_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span></div></div>';
	html += '   <div class="pull-right box-tools" id="load-block-'+row+'" style="width: 90%; display: none;">';
	html += '	   <div class="input-group">';
	html += '  	<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">';
	html += '  	 <input type="checkbox" id="tpl-add'+row+'" value="1" />';
	html += '  	 </span>';
	html += '  	 <select data-add-id="'+row+'" class="load-tpl-block form-control">';
	html += '  	  <option value="" >&nbsp;&nbsp;<?php echo $text_load_tpl; ?></option>';
					  <?php foreach ($order_blocks as $block) { ?>
	html += '  	  <option value="<?php echo $block['template_id']; ?>" >&nbsp;&nbsp;<?php echo $block['name']; ?></option>';
					  <?php } ?>
	html += '  	 </select>';
	html += '  	 <span class="input-group-btn" data-toggle="tooltip" title="Сохранить блок данных"><a class="btn btn-default" onclick="saveBlock(\'order\', \'order-table-row'+row+'\');"><i class="fa fa-save"></i></a></span>';
	html += '  	 <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>';
	html += ' 		</div>';
	html += ' 	  </div>';
	html += ' 	</div>';
	html += ' 	<div class="box-body">';
	html += ' 	 <textarea name="template[columns]['+row+'][data]" id="textarea'+row+'" rows="8" class="column-data form-control"></textarea>';
	html += ' 	</div>';
	html += ' </div>';
	html += '</td>';
	html += '</tr>';

	var no_result = $('#columns_no_results').html();
	if (no_result !='') {
		$('#columns_no_results').remove();
	}

	$('#order-table > tbody').append(html);
	$('#input-style-type').trigger('change');

	row++;

	$('#order-table-add-column > a').attr('onclick', 'addColumn('+row+')');

	multiselectStart();
	tooltipRefresh('#order-table');
}

--></script>

<script type="text/javascript"><!--

$(document).ready(function(){
	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	$('.cssTextArea').each(function(){
		var cssTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "150px", mode: "text/x-scss", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			cssTextArea.html(editor.getValue()).trigger('change');
		});
	});

	$('.jsTextArea').each(function(){
		var jsTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "150px", mode: "text/typescript", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true})
		.on('change', editor => {
			jsTextArea.html(editor.getValue()).trigger('change');
		});
	});

	$('.htmlTextArea').each(function(){
		var htmlTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "auto", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			htmlTextArea.html(editor.getValue()).trigger('change');
		});
	});

	var dropElSelector = '.tbody-sortable';
	var param = {}; param['group'] = {};
	param['group']['name'] = 'table-rows';
	param['handle'] = '.handle';
	param['draggable'] = 'tr';
	param['filter'] = '.checkbox-row';
	param['ghostClass'] = 'sortable-elem';

	sortableStartGroup(dropElSelector, param);

	iCheckStart();
	multiselectStart();
	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('#nav-tabs a:first').tab('show');
});

//--></script>

</div>
<?php echo $footer; ?>