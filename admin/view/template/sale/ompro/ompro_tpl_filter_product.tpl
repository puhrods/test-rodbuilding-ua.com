<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">
<link type="text/css" rel="stylesheet" href="view/javascript/ompro/chosen.min.css"/>
<script type="text/javascript" src="view/javascript/ompro/chosen.jquery.min.js"></script>

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

	<div class="box-body box-content hidden-default">
		<div class="nav-tabs-custom">
		  <ul id="nav-tabs" class="nav nav-tabs">
            <li><a href="#tab-template" data-toggle="tab"><?php echo $text_tab_template_filter; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
			<div class="tab-pane" id="tab-template">

			  <div class="panel panel-default">
				<div class="panel-body">
				  <div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_filter_product.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $text_filter_product_info_main; ?></p>
					<div id="alert_log_sql" class="<?php if (!$log_sql) { ?>hidden-default<?php } ?>">
						<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_alert_log_sql; ?></p>
					</div>
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

			  <div class="panel-group">
				  <!-- PARAMS -->
				  <div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-filter"></i>&nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="collapse" href="#set-params"><span style="color: #95b6ca;"><i class="fa fa-sort"></i></span>&nbsp;&nbsp; <?php echo $text_filter_param_block_title; ?></a></div>
					<div id="set-params" class="panel-collapse collapse in">
					  <div class="panel-body">

						<div class="panel panel-default">
							<div class="panel-heading"><a data-toggle="collapse" aria-expanded="false" class="collapsed" href="#class-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_validate_class_table; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_validate_class_table_info; ?>"></i></a></div>
							<div id="class-info" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
							  <div class="panel-body table-responsive"><?php echo $input_validate_class_table; ?></div>
							</div>
						</div>

						<div id="panel-params">
						  <div class="row">
							<div class="col-sm-8">
							  <div class="row">
								<div class="col-sm-3">
								  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_filter_id; ?></span></label>
								  <div class="panel valid-block">
									<span class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_required_input; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
										<input type="text" id="filter_id" name="template[filter_id]" value="<?php echo isset($template['filter_id']) ? $template['filter_id'] : ''; ?>" class="field-input validid form-control" placeholder="filter_order_id" target-error-id="error_filter_id"/>
									</span>
								  </div>
								</div>
								<div class="col-sm-3">
								  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_main_class; ?></span></label>
								  <div class="panel valid-block">
									<span class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_main_class; ?>"><i class="fa fa-info text-blue"></i></span>
										<input type="text" name="template[filter_main_class]" value="<?php echo isset($template['filter_main_class']) ? $template['filter_main_class'] : ''; ?>" class="field-input validclass form-control" placeholder="class..." target-error-id="error_filter_class"/>
									</span>
								  </div>
								</div>
								<div class="col-sm-3">
								  <label class="control-label" for="input-tooltip"><span class="font-weight-reset"><?php echo $text_filter_label_tooltip; ?></span></label>
								  <div class="panel">
									<textarea  id="input-tooltip" name="template[tooltip]" rows="1" class="form-control"><?php echo isset($template['tooltip']) ? $template['tooltip'] : ''; ?></textarea>
								  </div>
								</div>
								<div class="col-sm-3">
								  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_input_type ?></span></label>
								  <div class="panel">
									<select name="template[input_type]" id="input-type" class="form-control">
									  <?php foreach ($input_type_list as $input) { ?>
										<option value="<?php echo $input['type']; ?>" <?php if (isset($template['input_type']) && $template['input_type'] == $input['type']) { ?> selected="selected"<?php } ?>><?php echo $input['name']; ?></option>
									  <?php } ?>
									</select>
								  </div>
								</div>
							  </div>
							</div>
							<div class="col-sm-4 hidden-default" id="select_class_col">
								<label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_selector_class; ?></span></label>
								<div id="select_class_block" class="panel"></div>
							</div>
							<div class="col-sm-4 hidden-default" id="input_placeholder_col">
								<label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_placeholder; ?></span></label>
								<div id="input_placeholder_block" class="panel"></div>
							</div>
						  </div>
						  <div class="row hidden-default" id="select_value_row"></div>
						  <div class="row">
							<div class="col-sm-4 hidden-default" id="handler_class_col">
							  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_handler_class; ?></span></label>
							  <div id="handler_class_block" class="panel"></div>
							</div>
							<div class="col-sm-8">
							  <div class="row">
								<div class="col-sm-12 hidden-default" id="input_mask_col">
								  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_input_mask; ?> <small><a href="https://github.com/RobinHerbots/Inputmask/" target="_blank"><?php echo $text_documentation; ?></a></small></span></label>
								  <div id="input_mask_block" class="panel"></div>
								</div>
								<div class="col-sm-12 hidden-default" id="autocomplete_col">
								  <div id="autocomplete_block" class="panel"></div>
								</div>
							  </div>
							</div>
						  </div>
						  <div class="row hidden-default" id="multiselect_row"></div>
						  <div class="row hidden-default" id="dp_params_row"></div>
						</div>
					  </div>
					</div>
				  </div>

				  <!-- SEARCH SETTING -->
				  <div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-search"></i>&nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="collapse" href="#set-search"><span style="color: #95b6ca;"><i class="fa fa-sort"></i></span>&nbsp;&nbsp; <?php echo $text_filter_search_param_block_title; ?></a>&nbsp;&nbsp;  <a type="button" class="btn-link" onclick="$('#search-help').slideToggle();"><i class="fa fa-question text-warning"></i> &nbsp;<?php echo $button_help; ?></a></div>
					<div id="set-search" class="panel-collapse collapse in">
					  <div class="panel-body">
							<div id="search-help" class="hidden-default">
								<p><i class="fa fa-exclamation-triangle text-orange"></i>&nbsp; <?php echo $text_filter_search_help1; ?>
								<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_filter_product_search_help2; ?>
								<br/><i class="fa fa-exclamation-triangle text-red"></i>&nbsp; <?php echo $text_filter_product_search_help3; ?>
								<br/><i class="fa fa-exclamation-triangle text-red"></i>&nbsp; <?php echo $text_filter_search_help6; ?>
								<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_filter_product_search_help4; ?>
								<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_filter_product_search_help5; ?>
								</p>
							</div>

							<div class="panel panel-default">
							  <table id="tags_table" class="table table-bordered">
								<thead>
								  <tr class="active">
									<td colspan="4" class="valid-block">
									  <span class="input-group" style="width: 100%;">
										<span class="input-group-addon" data-toggle="tooltip"><?php echo $text_filter_entry_separator; ?></span>
										<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_separator_help; ?>"><i class="fa fa-info text-blue"></i></span>
										<input type="text" id="tags_separator" name="template[tags_separator]" value="<?php echo $template['tags_separator']; ?>" class="field-input notcyrillics form-control" />
									  </span>
									</td>
								  </tr>
								  <tr>
									<th colspan="3">
									  <div class="radio" style="padding-top: 0; padding-bottom: 0; min-height: auto;">
										<label>
										  <input type="radio" name="template[tags_manual_status]" value="0" <?php if (!$tags_manual_status) { ?>checked<?php } ?> class="tags_manual_status" />
										  <?php echo $text_filter_method_params; ?>
										</label>&nbsp;&nbsp;
										<label>
										  <input type="radio" name="template[tags_manual_status]" value="1" <?php if ($tags_manual_status) { ?>checked<?php } ?> class="tags_manual_status" />
										  <?php echo $text_filter_method_manual; ?>
										</label>
									  </div>
									</th>
								  </tr>
								</thead>
							  </table>
							</div>
					  </div>
					</div>
				  </div>

				  <!-- CLEAR BUTTON -->
				  <div class="panel panel-default">
					<div class="panel-heading"><i class="fa fa-times-circle"></i>&nbsp;&nbsp;&nbsp;&nbsp;<a data-toggle="collapse" href="#set-clearbtn"><span style="color: #95b6ca;"><i class="fa fa-sort"></i></span>&nbsp;&nbsp; <?php echo $text_clear_btn; ?></a></div>
					<div id="set-clearbtn" class="panel-collapse collapse in">
					  <div class="panel-body">

					  <div class="col-sm-1">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_status; ?></span></label>
						<div class="panel">
						  <select name="template[btn_clear_status]" id="input-table-head"  class="form-control">
							<option value="1" <?php if (isset($template['btn_clear_status']) && $template['btn_clear_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
							<option value="0" <?php if (isset($template['btn_clear_status']) && $template['btn_clear_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
						  </select>
						</div>
					  </div>
					  <div class="col-sm-1">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_position; ?></span></label>
						<div class="panel">
						  <select name="template[btn_clear_position]" class="form-control">
							<option value="right" <?php if (isset($template['btn_clear_position']) && $template['btn_clear_position'] == 'right') { ?> selected="selected"<?php } ?>><?php echo $text_right; ?></option>
							<option value="left" <?php if (isset($template['btn_clear_position']) && $template['btn_clear_position'] == 'left') { ?> selected="selected"<?php } ?>><?php echo $text_left; ?></option>
						  </select>
						</div>
					  </div>
					  <div class="col-sm-2">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_type; ?></span></label>
						<div class="panel">
							<select name="template[btn_clear_type]" class="form-control">
								<option value="icon" <?php if (isset($template['btn_clear_type']) && $template['btn_clear_type'] == 'icon') { ?> selected="selected"<?php } ?>><?php echo $text_btn_type_icon; ?></option>
								<option value="text" <?php if (isset($template['btn_clear_type']) && $template['btn_clear_type'] == 'text') { ?> selected="selected"<?php } ?>><?php echo $text_btn_type_text; ?></option>
								<option value="icontext" <?php if (isset($template['btn_clear_type']) && $template['btn_clear_type'] == 'icontext') { ?> selected="selected"<?php } ?>><?php echo $text_btn_type_icon_text; ?></option>
								<option value="icontext" <?php if (isset($template['btn_clear_type']) && $template['btn_clear_type'] == 'texticon') { ?> selected="selected"<?php } ?>><?php echo $text_btn_type_text_icon; ?></option>
							</select>
						</div>
					  </div>
					  <div class="col-sm-2">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_title_tooltip; ?></span></label>
						<div class="panel">
						  <div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_btn_info_title_type; ?>"><i class="fa fa-info text-blue"></i></span>
							<input type="text" name="template[btn_clear_title]" value="<?php echo isset($template['btn_clear_title']) && !empty($template['btn_clear_title']) ? $template['btn_clear_title'] : $text_clear; ?>" class="form-control" />
						  </div>
						</div>
					  </div>
					  <div class="col-sm-2">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_text; ?></span></label>
						<div class="panel">
						  <div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_btn_text_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<input type="text" name="template[btn_clear_text]" value="<?php echo isset($template['btn_clear_text']) && !empty($template['btn_clear_text']) ? $template['btn_clear_text'] : $text_clear; ?>" class="form-control" />
						  </div>
						</div>
					  </div>
					  <div class="col-sm-2">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_btn_class; ?></span></label>
						<div class="panel valid-block">
						  <div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_btn_class_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<input type="text" name="template[btn_clear_class]" value="<?php echo isset($template['btn_clear_class']) && !empty($template['btn_clear_class']) ? $template['btn_clear_class'] : 'btn btn-default'; ?>" class="field-input validclass form-control" placeholder="btn btn-default" />
						  </div>
						</div>
					  </div>
					  <div class="col-sm-2">
						<label class="control-label"><span class="font-weight-reset"><?php echo $text_label_icon_class; ?></span></label>
						<div class="panel valid-block">
						  <div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_icon_class_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<input type="text" name="template[btn_clear_icon]" value="<?php echo isset($template['btn_clear_icon']) && !empty($template['btn_clear_icon']) ? $template['btn_clear_icon'] : 'fa fa-times-circle'; ?>" class="field-input validclass form-control" placeholder="fa fa-times" />
						  </div>
						</div>
					  </div>

					  </div>
					</div>
				  </div>
			  </div>

			  <!-- PREVIEW -->
			  <div class="row">
				<div class="col-sm-12">
				  <div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><i class="fa fa-eye"></i>&nbsp;&nbsp;<?php echo $text_filter_preview_block_title; ?></h4>
					</div>
					<div class="panel-body" style="min-height: 100px;">
					  <div class="row">
						  <div class="col-sm-6 ">
							<div class="btn-group btn-group-justified">
							  <div class="btn-group">
								<button type="button" location="table" class="btn btn-default btn-preview"><?php echo $text_btn_preview_table_filter_row; ?></button>
							  </div>
							  <div class="btn-group">
								<button type="button" location="top" class="btn btn-default btn-preview"><?php echo $text_btn_preview_filter_panel; ?></button>
							  </div>
							</div>
						  </div>
						  <div class="col-sm-6" id="preview-block"><?php echo $text_no_results; ?></div>
					  </div>
					</div>
				  </div>
				</div>
			  </div>

			  <!--  INFO -->
			  <div class="row">
				<div class="col-sm-12">
				  <div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><?php echo $text_filter_info_block_title; ?></h4>
					</div>
					<div class="panel-body">
						<div class="col-sm-12 no-padding">
							<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_info_bootstrap; ?>
							<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_info_awesome; ?>
							<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_info_multiselect; ?>
							<br/><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $text_info_datepicker; ?></p>
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

<div id="hidden_elements" style="display: none;">
	<table id="hidden_tags_table">
		<tbody id="tags_table_manual_tbody">
		  <tr>
			<td colspan="3" class="valid-block">
			  <div class="input-group" style="width: 100%;">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_product_manual_input; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[tags_manual]" value="<?php echo $template['tags_manual']; ?>" class="field-input sqlwithexclude form-control" />
			  </div>
			</td>
		  </tr>
		</tbody>
		<tbody id="tags_table_params_tbody">
		  <tr class="active">
			<th style="width: 15%;"><?php echo $text_tags_multiple_status; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_tags_multiple_status_help; ?>"></i></th>
			<th style="width: 45%;"><?php echo $text_filter_tags; ?></th>
			<th style="width: 15%;"><?php echo $text_filter_single_operator; ?></th>
			<th style="width: 25%;" id="changed_title_operator"><?php echo $text_process_as; ?></th>
		  </tr>
		  <tr>
			<td>
			  <div class="radio">
				<label>
				  <input type="radio" name="template[tags_multiple_status]" value="1" <?php if ($tags_multiple_status) { ?>checked<?php } ?> class="tags_multiple_status" />
				  <?php echo $text_yes; ?>
				</label>&nbsp;&nbsp;
				<label>
				  <input type="radio" name="template[tags_multiple_status]" value="0" <?php if (!$tags_multiple_status) { ?>checked<?php } ?> class="tags_multiple_status" />
				  <?php echo $text_no; ?>
				</label>
			  </div>
			</td>
			<td class="valid-block">
			  <select id="select-tags" class="form-control" multiple>
				<?php foreach ($tags as $tag) { ?>
					<option value="<?php echo $tag['code']; ?>" <?php echo $tag['selected']; ?>>&nbsp;&nbsp;<?php echo $tag['name']; ?></option>
				<?php } ?>
			  </select>
			<input id="input-tags" type="hidden" name="template[tags]" value="" class="field-input mustbe" />
			</td>
			<td>
				<select name="template[operator]" class="select-operator form-control">
				  <?php foreach ($operator_list as $operator) { ?>
					<option value="<?php echo $operator['value']; ?>" <?php if (isset($template['operator']) && $template['operator'] == $operator['value']) { ?> selected="selected" <?php } ?>><?php echo $operator['text']; ?></option>
				  <?php } ?>
				</select>
			</td>
			<td id="changed_data_operator"></td>
		  </tr>
		</tbody>
	</table>

	<span id="group_likemask_operator" class="input-group valid-block">
	  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_likemask_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
	  <input type="text" name="template[likemask]" value="<?php echo isset($template['likemask']) ? $template['likemask'] : '%{value}%'; ?>" class="field-input sqlwithexclude form-control" />
	</span>

	<select id="select_process_as" name="template[process_as]" class="form-control">
	  <?php foreach ($process_as_list as $type) { ?>
		<option value="<?php echo $type['value']; ?>" <?php if (isset($template['process_as']) && $template['process_as'] == $type['value']) { ?> selected="selected" <?php } ?>><?php echo $type['text']; ?></option>
	  <?php } ?>
	</select>

	<span id="text_placeholder" class="input-group">
	  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_placeholder; ?>"><i class="fa fa-info text-blue"></i></span>
		<input type="text" name="template[placeholder]" value="<?php echo isset($template['placeholder']) ? $template['placeholder'] : ''; ?>" class="form-control"/>
	</span>

	<span id="handler_type_group" class="input-group">
	  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_handler_type; ?>"><i class="fa fa-info text-blue"></i></span>
	  <select id="handler_type" name="template[handler_type]" class="form-control">
		<option value="">&nbsp;&nbsp; <?php echo $text_not_selected; ?></option>
	  <?php foreach ($handler_type_list as $handler) { ?>
		<option value="<?php echo $handler['type']; ?>" <?php if (isset($template['handler_type']) && $template['handler_type'] == $handler['type']) { ?> selected="selected"<?php } ?>><?php echo $handler['name']; ?></option>
	  <?php } ?>
	  </select>
	</span>

	<span id="inputmask" class="input-group">
	  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_inputmask; ?>"><i class="fa fa-info text-blue"></i></span>
	  <input type="text" name="template[mask]" value="<?php echo isset($template['mask']) ? $template['mask'] : ''; ?>" class="form-control" placeholder="+375 (99) 999-99-99"/>
	</span>

	<div id="autocomplete_row" class="row">
	  <div class="col-sm-8">
		<label class="control-label" ><span class="font-weight-reset"><?php echo $text_filter_label_autocomplete; ?></span></label>
		<span class="input-group">
		  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_autocomplete; ?>"><i class="fa fa-info text-blue"></i></span>
		  <select id="autocomplete" name="template[autocomplete]" class="form-control">
		  <?php foreach ($autocompletes as $target => $name) { ?>
			<option value="<?php echo $target; ?>" <?php if (isset($template['autocomplete']) && $template['autocomplete'] == $target) { ?> selected="selected"<?php } ?>><?php echo $name; ?></option>
		  <?php } ?>
		  </select>
		</span>
	  </div>
	  <div class="col-sm-4 valid-block">
		<label class="control-label" ><span class="font-weight-reset"><?php echo $text_filter_label_autocomplete_limit; ?></span></label>
		<span class="input-group">
		  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_autocomplete_limit; ?>"><i class="fa fa-info text-blue"></i></span>
		  <input type="text" name="template[autocomplete_limit]" value="<?php echo isset($template['autocomplete_limit']) ? $template['autocomplete_limit'] : 5; ?>" class="field-input numeric form-control" placeholder="10"/>
		</span>
	  </div>
	</div>

	<span id="select_class_group" class="input-group">
	  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_select_class; ?>"><i class="fa fa-info text-blue"></i></span>
	  <select id="select_class" name="template[select_class]" class="form-control">
	  <?php foreach ($select_class_list as $class) { ?>
		<option value="<?php echo $class['class']; ?>" <?php if (isset($template['select_class']) && $template['select_class'] == $class['class']) { ?> selected="selected"<?php } ?>><?php echo $class['name']; ?></option>
	  <?php } ?>
	  </select>
	</span>

	<div id="method_type_group">
		<div class="col-sm-4">
		  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_label_value_list; ?></span></label>
		  <div class="panel">
			<span class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_api_method_description; ?>"><i class="fa fa-info text-blue"></i></span>
			  <select name="template[process_method]" class="form-control">
				<?php foreach ($process_method_list as $method) { ?>
				  <option value="<?php echo $method['process_method']; ?>" <?php if (isset($template['process_method']) && $template['process_method'] == $method['process_method']) { ?> selected="selected"<?php } ?>><?php echo $method['text']; ?></option>
				<?php } ?>
			  </select>
			</span>
		  </div>
		</div>
		<div class="col-sm-8">
		  <label class="control-label"><span class="font-weight-reset"><?php echo $text_filter_empty_value_status; ?></span></label>
		  <div class="panel">
			<span class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_empty_value_status; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[empty_value_status]" class="form-control">
				  <option value="1" <?php if (isset($template['empty_value_status']) && $template['empty_value_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				  <option value="0" <?php if (!isset($template['empty_value_status']) || (isset($template['empty_value_status']) && $template['empty_value_status'] == '0')) { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
				</select>
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filter_info_title_empty_value_text; ?>"><i class="fa fa-info text-blue"></i>&nbsp;&nbsp; <?php echo $text_filter_empty_value_text; ?></span>
				<input type="text" name="template[empty_value_text]" value="<?php echo isset($template['empty_value_text']) ? $template['empty_value_text'] : ''; ?>" class="form-control" />
			</span>
		  </div>
		</div>
	</div>

  <div class="col-sm-12" id="multiselect_col" style="margin-top: 15px;">
	<div class="panel panel-default">
	  <div class="panel-heading">
		<h1 class="panel-title"><i class="fa fa-list-ul"></i>&nbsp;&nbsp;<?php echo $text_title_multiselect; ?>&nbsp;&nbsp;<span class="text-blue" style="font-size: 13px;"><a href="http://davidstutz.github.io/bootstrap-multiselect/" target="_blank"><?php echo $text_documentation_multiselect; ?></a></span></h1>
	  </div>
	  <div class="panel-body">
		<div class="row">
		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">buttonClass</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_buttonClass_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][bCl]" value="<?php echo isset($template['multiselect']['bCl']) ? $template['multiselect']['bCl'] : 'btn btn-default'; ?>" class="field-input validclass form-control" placeholder="class..." />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">selectedClass</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_selectedClass_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][selCl]" value="<?php echo isset($template['multiselect']['selCl']) ? $template['multiselect']['selCl'] : 'active'; ?>" class="field-input validclass form-control" placeholder="class..." />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">dropRight</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dropRight_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][dR]" class="form-control">
					<option value="false" <?php if (isset($template['multiselect']['dR']) && $template['multiselect']['dR'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['dR']) && $template['multiselect']['dR'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">dropUp</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dropUp_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][dU]" class="form-control">
					<option value="false" <?php if (isset($template['multiselect']['dU']) && $template['multiselect']['dU'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['dU']) && $template['multiselect']['dU'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">maxHeight</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_maxHeight_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][mH]" value="<?php echo isset($template['multiselect']['mH']) ? $template['multiselect']['mH'] : ''; ?>" class="field-input numeric form-control" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">includeSelectAllOpt...</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_includeSelectAllOption_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][selAll]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['selAll']) && $template['multiselect']['selAll'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['selAll']) && $template['multiselect']['selAll'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>
		</div>
		<div class="row">
		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">includeSelectAllIf...</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_includeSelectAllIfMoreThan_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][selAllIfMo]" value="<?php echo isset($template['multiselect']['selAllIfMo']) ? $template['multiselect']['selAllIfMo'] : 0; ?>" class="field-input numeric form-control" placeholder="10" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">selectAllNumber</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_selectAllNumber_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][selAllNum]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['selAllNum']) && $template['multiselect']['selAllNum'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['selAllNum']) && $template['multiselect']['selAllNum'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">selectAllText</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_selectAllNumber_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][selAllText]" value="<?php echo isset($template['multiselect']['selAllText']) ? $template['multiselect']['selAllText'] : $text_select_all; ?>" class="form-control" placeholder="Select all" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">nonSelectedText</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_nonSelectedText_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][noSelText]" value="<?php echo isset($template['multiselect']['noSelText']) ? $template['multiselect']['noSelText'] : $text_not_selected; ?>" class="form-control" placeholder="<?php echo $text_not_selected; ?>" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">nSelectedText</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_nSelectedText_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][nSelText]" value="<?php echo isset($template['multiselect']['nSelText']) ? $template['multiselect']['nSelText'] : $text_selected; ?>" class="form-control" placeholder="<?php echo $text_selected; ?>" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">allSelectedText</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_allSelectedText_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][allSelText]" value="<?php echo isset($template['multiselect']['allSelText']) ? $template['multiselect']['allSelText'] : $text_all_selected; ?>" class="form-control" placeholder="<?php echo $text_all_selected; ?>" />
			  </div>
			</div>
		  </div>
		</div>
		<div class="row">
		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">numberDisplayed</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_numberDisplayed_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][numDisp]" value="<?php echo isset($template['multiselect']['numDisp']) ? $template['multiselect']['numDisp'] : 1; ?>" class="field-input numeric form-control" placeholder="1" />
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">enableFiltering</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_enableFiltering_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][enFilter]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['enFilter']) && $template['multiselect']['enFilter'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['enFilter']) && $template['multiselect']['enFilter'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">enableCaseIns...</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_enableCaseInsensitiveFiltering_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][caseIns]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['caseIns']) && $template['multiselect']['caseIns'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['caseIns']) && $template['multiselect']['caseIns'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">filterPlaceholder</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_filterPlaceholder_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][filterPH]" value="<?php echo isset($template['multiselect']['filterPH']) ? $template['multiselect']['filterPH'] : 'Search'; ?>" class="form-control" placeholder="Search..."/>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">includeFilterClearBtn</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_includeFilterClearBtn_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][fClearBtn]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['fClearBtn']) && $template['multiselect']['fClearBtn'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['fClearBtn']) && $template['multiselect']['fClearBtn'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">includeResetOption</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_includeResetOption_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][resetO]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['resetO']) && $template['multiselect']['resetO'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['resetO']) && $template['multiselect']['resetO'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
				</select>
			  </div>
			</div>
		  </div>
		</div>
		<div class="row">
		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">includeResetDivider</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_includeResetDivider_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[multiselect][resetD]" class="form-control" style="min-width: 70px;">
					<option value="false" <?php if (isset($template['multiselect']['resetD']) && $template['multiselect']['resetD'] == 'false') { ?> selected="selected"<?php } ?>><?php echo $text_no; ?></option>
					<option value="true" <?php if (isset($template['multiselect']['resetD']) && $template['multiselect']['resetD'] == 'true') { ?> selected="selected"<?php } ?>><?php echo $text_yes; ?></option>
				</select>
			  </div>
			</div>
		  </div>

		  <div class="col-sm-2">
			<label class="control-label"><span class="font-weight-reset">resetText</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_resetText_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[multiselect][resetT]" value="<?php echo isset($template['multiselect']['resetT']) ? $template['multiselect']['resetT'] : 'Reset'; ?>" class="form-control" placeholder="Reset"/>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>

  <div class="col-sm-12" id="dp_params_col" style="margin-top: 15px;">
	<div class="panel panel-default">
	  <div class="panel-heading">
		<h4 class="panel-title"><i class="fa fa-calendar"></i>&nbsp;&nbsp;<?php echo $text_dp_params; ?>&nbsp;&nbsp;<span class="text-blue" style="font-size: 13px;"><a href="http://t1m0n.name/air-datepicker/docs/index-ru.html" target="_blank"><?php echo $text_documentation_airdatepicker; ?></a></span></h4>
	  </div>
	  <div class="panel-body" style="padding: 0 15px 15px 15px;">
		<div class="row">
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">dateFormat</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_dateFormat_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[dpParam][dF]" value="<?php echo !empty($template['dpParam']['dF']) ? $template['dpParam']['dF'] : 'dd.mm.yyyy'; ?>" class="field-input notcyrillics form-control" />
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">position</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_position_info; ?>"><i class="fa fa-info text-blue"></i></span>
				  <select name="template[dpParam][pos]" class="form-control">
					<option value="bottom left" <?php if (isset($template['dpParam']['pos']) && $template['dpParam']['pos'] === 'bottom left') { ?> selected="selected"<?php } ?>><?php echo $text_dp_position_bottom_left; ?></option>
					<option value="bottom right" <?php if (isset($template['dpParam']['pos']) && $template['dpParam']['pos'] === 'bottom right') { ?> selected="selected"<?php } ?>><?php echo $text_dp_position_bottom_right; ?></option>
					<option value="top left" <?php if (isset($template['dpParam']['pos']) && $template['dpParam']['pos'] === 'top left') { ?> selected="selected"<?php } ?>><?php echo $text_dp_position_top_left; ?></option>
					<option value="top right" <?php if (isset($template['dpParam']['pos']) && $template['dpParam']['pos'] === 'top right') { ?> selected="selected"<?php } ?>><?php echo $text_dp_position_top_right; ?></option>
				  </select>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">multipleDates</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_multipleDates_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[dpParam][mD]" class="form-control">
					<option value="false" <?php if (isset($template['dpParam']['mD']) && $template['dpParam']['mD'] == 'false') { ?> selected="selected"<?php } ?>>false</option>
					<option value="true" <?php if (isset($template['dpParam']['mD']) && $template['dpParam']['mD'] == 'true') { ?> selected="selected"<?php } ?>>true</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">multipleDatesSeparator</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_multipleDatesSeparator_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[dpParam][mDs]" value="<?php echo !empty($template['dpParam']['mDs']) ? $template['dpParam']['mDs'] : ', '; ?>" class="field-input form-control"/>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">clearButton</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_clearButton_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[dpParam][clBtn]" class="form-control">
					<option value="true" <?php if (isset($template['dpParam']['clBtn']) && $template['dpParam']['clBtn'] == 'true') { ?> selected="selected"<?php } ?>>true</option>
					<option value="false" <?php if (isset($template['dpParam']['clBtn']) && $template['dpParam']['clBtn'] == 'false') { ?> selected="selected"<?php } ?>>false</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">autoClose</span></label>
			<div class="panel">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_autoClose_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[dpParam][aCl]" class="form-control">
					<option value="false" <?php if (isset($template['dpParam']['aCl']) && $template['dpParam']['aCl'] == 'false') { ?> selected="selected"<?php } ?>>false</option>
					<option value="true" <?php if (isset($template['dpParam']['aCl']) && $template['dpParam']['aCl'] == 'true') { ?> selected="selected"<?php } ?>>true</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">inline</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_inline_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<select name="template[dpParam][inl]" class="form-control">
					<option value="false" <?php if (isset($template['dpParam']['inl']) && $template['dpParam']['inl'] == 'false') { ?> selected="selected"<?php } ?>>false</option>
					<option value="true" <?php if (isset($template['dpParam']['inl']) && $template['dpParam']['inl'] == 'true') { ?> selected="selected"<?php } ?>>true</option>
				</select>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-3">
			<label class="control-label"><span class="font-weight-reset">classes</span></label>
			<div class="panel valid-block">
			  <div class="input-group">
				<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_dp_classes_info; ?>"><i class="fa fa-info text-blue"></i></span>
				<input type="text" name="template[dpParam][cls]" value="<?php echo !empty($template['dpParam']['cls']) ? $template['dpParam']['cls'] : ''; ?>" class="field-input validclass form-control" />
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>

</div>

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

--></script>

<script type="text/javascript"><!--
var template_id = '<?php echo $template_id; ?>';

$(document).delegate('#filter_id', 'change', function() {
	var filter_id = $(this).val();
	$.ajax({
		url: 'index.php?route=sale/ompro_helper/testFilterId&<?php echo $strtoken; ?>&type=filter_product&template_id=' + template_id + '&filter_id=' + filter_id,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: absolute; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+filter_id + json['error']+'</p>', '');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$(document).delegate('#handler_type', 'change', function() {
	var val = $(this).val();

	$('#input_mask_col').hide();
	$('#hidden_elements').append($('#inputmask'));
	$('#dp_params_row').hide();
	$('#hidden_elements').append($('#dp_params_col'));
	$('#autocomplete_col').hide();
	$('#hidden_elements').append($('#autocomplete_row'));

	if (val == 'inputmask') {
		$('#input_mask_block').append($('#inputmask'));
		$('#input_mask_col').show();
	} else if (val == 'datepicker') {
		$('#dp_params_row').append($('#dp_params_col'));
		$('#dp_params_row').show();
	} else if (val == 'autocomplete') {
		$('#autocomplete_block').append($('#autocomplete_row'));
		$('#autocomplete_col').show();
	}
	$(function () { window.validation.init({ container: '#form' });});
});

$('#handler_type').trigger('change');

$(document).delegate('#select_class', 'change', function() {
	if (!$(this).val() || $(this).val() == 'select_default') {
		$('#multiselect_row').hide();
		$('#hidden_elements').append($('#multiselect_col'));
	} else if ($(this).val() && ($(this).val() === 'multiselect_single' || $(this).val() === 'multiselect')) {
		$('#multiselect_row').append($('#multiselect_col'));
		$('#multiselect_row').show();
	}
	$(function () { window.validation.init({ container: '#form' });});
});

$('#select_class').trigger('change');

$(document).delegate('.tags_manual_status', 'change', function() {
	if ($(this).val() == 1) {
		$('#hidden_elements #hidden_tags_table').append($('#tags_table #tags_table_params_tbody'));
		$('#tags_table').append($('#tags_table_manual_tbody'));
	} else {
		$('#hidden_elements #hidden_tags_table').append($('#tags_table #tags_table_manual_tbody'));
		$('#tags_table').append($('#tags_table_params_tbody'));
	}

	$(function () { window.validation.init({ container: '#form' });});
});

<?php if (!$tags_manual_status) { ?>
	$('.tags_manual_status:first').trigger('change');
<?php } else { ?>
	$('.tags_manual_status:last').trigger('change');
<?php } ?>

$(document).delegate('.select-operator', 'change', function() {
	if ($(this).val() == 'LIKE' || $(this).val() == 'NOT LIKE') {
		$('#changed_title_operator').text('<?php echo $text_likemask; ?>');
		$('#changed_data_operator').append($('#group_likemask_operator'));
		$('#hidden_elements').append($('#select_process_as'));
	} else {
		$('#changed_title_operator').text('<?php echo $text_process_as; ?>');
		$('#hidden_elements').append($('#group_likemask_operator'));
		$('#changed_data_operator').append($('#select_process_as'));
	}
	$(function () { window.validation.init({ container: '#form' });});
});

$('.select-operator').trigger('change');

// https://harvesthq.github.io/chosen/
function chosenStart(max=100) {
	$("#select-tags").chosen({
		disable_search_threshold: 10,
		max_selected_options: max,
		width: "100%",
		placeholder_text_multiple: "Select Tag",
		no_results_text: "нет совпадения для: ",
		hide_results_on_select: false
	});
}

$(document).delegate('.tags_multiple_status', 'change', function() {
	var val = $(this).val();
	if (val != '0') {
		$("#select-tags").chosen("destroy");
		chosenStart(100);
		$('#select-tags').trigger('change');
	} else {
		var arr = $("#select-tags").val();
		$("#select-tags").chosen("destroy").val(arr[arr.length-1]);
		chosenStart(1);
		$("#select-tags").trigger("chosen:activate");
		$(':focus').blur();
		$('#select-tags').trigger('change');
	}
});

$('.tags_multiple_status:checked').trigger('change');

$(document).delegate('#select-tags', 'change', function() {
	var values = '';
	$('#select-tags').each(function() {
		if (values == '') {
			values += $(this).val();
		} else {
			values += ','+$(this).val();
		}
	});
	$('#input-tags').val(values);
});

$('#select-tags').trigger('change');

$(document).delegate('#input-type', 'change', function() {
	$('#select_value_row').hide();
	$('#hidden_elements').append($('#method_type_group'));
	$('#select_class_col').hide();
	$('#hidden_elements').append($('#select_class_group'));
	$('#input_placeholder_col').hide();
	$('#hidden_elements').append($('#text_placeholder'));
	$('#handler_class_col').hide();
	$('#hidden_elements').append($('#handler_type_group'));

	var val = $(this).val();

	if (val == 'select') {
		$('#select_value_row').append($('#method_type_group'));
		$('#select_value_row').show();
		$('#select_class_block').append($('#select_class_group'));
		$('#select_class_col').show();
		$('#handler_type').val('').trigger('change');
	} else if (val == 'text') {
		$('#input_placeholder_block').append($('#text_placeholder'));
		$('#input_placeholder_col').show();
		$('#handler_class_block').append($('#handler_type_group'));
		$('#handler_class_col').show();
		$('#select_class').val('select_default').trigger('change');
	}

	$(function () { window.validation.init({ container: '#form' });});
});

$('#input-type').trigger('change');

--></script>

<script type="text/javascript"><!--

$(document).delegate('.btn-preview', 'click', function() {
	var template_id = '<?php echo $template_id; ?>';
	if (template_id < 1) {
		toastr.error('<?php echo $error_template_preview; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	}
	var loc = $(this).attr('location');
	$.ajax({
		url: 'index.php?route=sale/ompro/previewFilter&<?php echo $strtoken; ?>&type=filter_product&template_id=' + template_id + '&location=' + loc,
		dataType: 'json',
		beforeSend: function() {
			$('#preview-block').html('<div class="text-loading" style="position: absolute; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			} else {
				if (json['tpl']) {
					$('#preview-block').html(json['tpl']);
					dpStart();
					multiselectStart();
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

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

	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

	$('.box-content').show();
});
//--></script>

</div>
<?php echo $footer; ?>