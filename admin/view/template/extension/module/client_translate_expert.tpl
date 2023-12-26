<?php echo $header; ?><?php if (isset($column_left)) echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-bestseller" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i><?php if (version_compare(VERSION, '2.0', '<')) { echo $button_save; } ?></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i><?php if (version_compare(VERSION, '2.0', '<')) { echo $button_cancel; } ?></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-bestseller" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="<?php echo $tab_general_class; ?>"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-translate-site" data-toggle="tab"><?php echo $tab_translate_site; ?></a></li>
            <li><a href="#tab-localization" data-toggle="tab"><?php echo $tab_localization; ?></a></li>
            <li class="<?php echo $tab_debug_class; ?>"><a href="#tab-debug" data-toggle="tab"><?php echo $tab_debug; ?></a></li>
            <li><a href="#tab-about" data-toggle="tab"><?php echo $tab_about; ?></a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane <?php echo $tab_general_class; ?>" id="tab-general">
				  <!-- <?php if (isset($error_unlicensed) && !$license_info->licensed) { ?>
				  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_unlicensed; ?>
				      <button type="button" class="close" data-dismiss="alert">&times;</button>
				  </div>	 -->
				  <?php } ?>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-module_client_translate_expert_status"><?php echo $entry_status; ?></label>
					<div class="col-sm-10">
					  <select name="module_client_translate_expert_status" id="input-module_client_translate_expert_status" class="form-control">
						<?php if ($module_client_translate_expert_status) { ?>
						<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
						<option value="0"><?php echo $text_disabled; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_enabled; ?></option>
						<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
						<?php } ?>
					  </select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-module_client_translate_expert_key"><?php echo $entry_server_key; ?></label>
					<div class="col-sm-10">
					  <input type="text" name="module_client_translate_expert_key" value="<?php echo $module_client_translate_expert_key; ?>" placeholder="<?php echo $entry_server_key; ?>" id="input-module_client_translate_expert_key" class="form-control" />
					  <?php if ($error_name) { ?>
					  <div class="text-danger"><?php echo $error_name; ?></div>
					  <?php } ?>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label"><?php echo $entry_try_translate; ?></label>
					<div class="col-sm-10">
						<?php foreach ($languages as $language) { ?>
                        <div class="input-group">
							<span class="input-group-addon"><img src="<?php echo $language['image_url']; ?>" title="<?php echo $language['name']; ?>" /></span>
							<textarea name="test[<?php echo $language['language_id']; ?>][translation]" rows="7" placeholder="<?php echo $entry_try_translate_text; ?>" class="form-control"></textarea>
                        </div>
                        <?php } ?>
						</div>
				  </div>
				</div>
				<div class="tab-pane" id="tab-translate-site">
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-mode"><?php echo $entry_analization_mode; ?></label>
					<div class="col-sm-10">
						<select id="te-analization-mode" >
							<option value="both" <?php if ($te_mode == 'both') { echo 'selected="selected"'; } ?>><?php echo $value_analization_mode_both; ?></option>
							<option value="only_empty" <?php if ($te_mode == 'only_empty') { echo 'selected="selected"'; } ?>><?php echo $value_analization_mode_only_empty; ?></option>
							<option value="same_value" <?php if ($te_mode == 'same_value') { echo 'selected="selected"'; } ?>><?php echo $value_analization_mode_same_value; ?></option>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-table"><?php echo $entry_table; ?></label>
					<div class="col-sm-10">
						<select id="te-analization-table">
							<option value="" <?php if (!$te_table) { echo 'selected="selected"'; } ?>><?php echo $entry_all_tables; ?></option>
							<?php foreach ($tables as $table) { ?>
							<option value="<?php echo $table; ?>" <?php if ($te_table == $table) { echo 'selected="selected"'; } ?>><?php echo $table; ?></option>
							<?php } ?>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-language-from"><?php echo $entry_analization_language_from; ?></label>
					<div class="col-sm-10">
						<select id="te-analization-language-from">
							<?php foreach ($languages as $language) { ?>
							<option value="<?php echo $language['language_id']; ?>" <?php if ($te_langIdFrom == $language['language_id']) { echo 'selected="selected"'; } ?>><?php echo $language['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-language-to"><?php echo $entry_analization_language_to; ?></label>
					<div class="col-sm-10">
						<select id="te-analization-language-to">
							<?php foreach ($languages as $language) { ?>
							<option value="<?php echo $language['language_id']; ?>" <?php if ($te_langIdTo == $language['language_id']) { echo 'selected="selected"'; } ?>><?php echo $language['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-table-deep"><?php echo $entry_table_deep; ?></label>
					<div class="col-sm-10">
						<input type="checkbox" id="te-analization-table-deep" value="1" checked="checked"><?php echo $entry_table_deep_description; ?>
					</div>
				  </div>

				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-analization-product-status-filter"><?php echo $entry_table_product_filter; ?></label>
					<div class="col-sm-10">
						<input type="checkbox" id="te-analization-product-status-filter" value="1" <?php if ($te_product_status) { echo 'checked="checked"'; } ?>> <?php echo $entry_table_product_status_filter; ?><br><br>
						<input type="checkbox" id="te-analization-product-quantity-filter" value="1" <?php if ($te_product_quantity) { echo 'checked="checked"'; } ?>> <?php echo $entry_table_product_quantity_filter; ?><br><br>
						<select id="te-analization-product-category-id-filter">
							<option value="" <?php if (!$te_product_category_id) { echo 'selected="selected"'; } ?>><?php echo $entry_filter_all; ?></option>
							<?php foreach($categories as $category) { ?>
							<?php if($category['category_id'] == $te_product_category_id) { ?>
							<option value="<?php echo $category['category_id']; ?>" selected="selected"><?php echo $category['name']; ?></option>
							<?php } else { ?>
							<option value="<?php echo $category['category_id']; ?>"><?php echo $category['name']; ?></option>
							<?php } ?>
							<?php } ?>
						</select> <?php echo $entry_table_product_category_id_filter; ?><br><br>
						<select id="te-analization-product-stock-status-filter">
							<option value="" <?php if (!$te_stock_status) { echo 'selected="selected"'; } ?>><?php echo $entry_filter_all; ?></option>
							<?php foreach ($stock_statuses as $stock_status) { ?>
							<option value="<?php echo $stock_status['stock_status_id']; ?>" <?php if ($te_stock_status == $stock_status['stock_status_id']) { echo 'selected="selected"'; } ?>><?php echo $stock_status['name']; ?></option>
							<?php } ?>
						</select> <?php echo $entry_table_product_stock_status_filter; ?><br><br>
					</div>
				  </div>

				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-begin-analization"><?php echo $entry_analization; ?></label>
					<div class="col-sm-10">
						<input type="button" id="te-begin-analization" value="<?php echo $entity_begin_analization; ?>">
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-module_client_translate_expert_status"><?php echo $entry_analization_result; ?></label>
					<div class="col-sm-10">
						<div id="te-analization-result">
						</div>
					</div>
				  </div>
				</div>
				
				
				
				
				<div class="tab-pane" id="tab-localization">
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-localization-language-from"><?php echo $entry_localization_language_from; ?></label>
					<div class="col-sm-10">
						<select id="te-localization-language-from">
							<?php foreach ($languages as $language) { ?>
							<option value="<?php echo $language['language_id']; ?>" <?php if ($te_langIdFrom == $language['language_id']) { echo 'selected="selected"'; } ?>><?php echo $language['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-localization-language-to"><?php echo $entry_localization_language_to; ?></label>
					<div class="col-sm-10">
						<select id="te-localization-language-to">
							<?php foreach ($languages as $language) { ?>
							<option value="<?php echo $language['language_id']; ?>" <?php if ($te_langIdTo == $language['language_id']) { echo 'selected="selected"'; } ?>><?php echo $language['name']; ?></option>
							<?php } ?>
						</select>
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="te-localization-analization"><?php echo $entry_localization_analization; ?></label>
					<div class="col-sm-10">
						<input type="button" id="te-begin-localization-analization" value="<?php echo $entity_begin_analization; ?>">
					</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" for="input-module_client_translate_expert_status"><?php echo $entry_localization_analization_result; ?></label>
					<div class="col-sm-10">
						<div id="te-localization-analization-result">
						</div>
					</div>
				  </div>	  
				</div>
				
				
				
				
				<div class="tab-pane <?php echo $tab_debug_class; ?>" id="tab-debug">
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="client_translate_expert_version"><?php echo $entry_translate_expert_version; ?></label>
						<div class="col-sm-10">
							<strong name="client_translate_expert_version" id="client_translate_expert_version" class="form-control"><?php echo $translate_expert_version; ?></strong>
						</div>
				  </div>
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="php_version"><?php echo $entry_php_version; ?></label>
						<div class="col-sm-10">
							<strong name="php_version" id="php_version" class="form-control"><?php echo $php_version; ?></strong>
						</div>
				  </div>
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="opencart_version"><?php echo $entry_opencart_version; ?></label>
						<div class="col-sm-10">
							<strong name="opencart_version" id="opencart_version" class="form-control"><?php echo $opencart_version; ?></strong>
						</div>
				  </div>
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="license_info"><?php echo $entry_license_info . ' - Active'; ?></label>
						<div class="col-sm-10">
							<strong name="license_info" id="license_info" class="form-control"><?php echo $license_info->active; ?></strong>
						</div>
				  </div>
				  <div class="form-group">
						<label class="col-sm-2 control-label" for="license_info"><?php echo $entry_license_info . ' - Licensed'; ?></label>
						<div class="col-sm-10">
							<strong name="license_info" id="license_info" class="form-control"><?php echo $license_info->licensed; ?></strong>
						</div>
				  </div>
				  <div class="form-group">
					<label class="col-sm-2 control-label" ><?php echo $entry_debug_status; ?></label>
					<div class="col-sm-10">
						<div class="btn-group" data-toggle="buttons">
							<label class="btn btn-primary <?php if ($module_client_translate_expert_debug_status) { ?>active<?php } ?>">
								<input type="radio" name="module_client_translate_expert_debug_status" value="1"
									<?php if ($module_client_translate_expert_debug_status) { ?>checked="checked"<?php } ?>> <?php echo $text_enabled; ?>
							</label>
							<label class="btn btn-primary <?php if (!$module_client_translate_expert_debug_status) { ?>active<?php } ?>">
								<input type="radio" name="module_client_translate_expert_debug_status" value="0"
									<?php if (!$module_client_translate_expert_debug_status) { ?>checked="checked"<?php } ?>> <?php echo $text_disabled; ?>
							</label>
                        </div>
					</div>
				  </div>
				  
					<div class="panel panel-default">

						<div class="container-fluid">
							<div class="pull-left">
								<?php if (isset($debug_log)) { ?>
								<a href="<?php echo $download_href; ?>" data-toggle="tooltip" title="<?php echo $button_download; ?>" class="btn btn-primary"><i class="fa fa-download"></i></a>
								<a onclick="confirm('<?php echo $text_clear_confirm; ?>') ? location.href='<?php echo $clear_href; ?>' : false;" data-toggle="tooltip" title="<?php echo $button_clear; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i></a>
								<?php } else { ?>
								<a class="btn btn-info" href="<?php echo $view_href; ?>" data-toggle="tooltip" title="" data-original-title="<?php echo $button_view; ?>"><i class="fa fa-eye"><?php echo $button_view; ?></i></a>							
								<?php } ?>
							</div>
						</div>
						<div class="container-fluid">
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-exclamation-triangle"></i> <?php echo $text_debug_log; ?></h3>
							</div>
						</div>
						<?php if (isset($debug_log)) { ?>
							<?php if (isset($error_debug_log_warning_frm) && $error_debug_log_warning_frm) { ?>
							<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_debug_log_warning_frm; ?>
								<button type="button" class="close" data-dismiss="alert">&times;</button>
							</div>
							<?php } ?>
							<div class="panel-body">
								<textarea wrap="off" rows="30" readonly class="form-control"><?php echo $debug_log; ?></textarea>
							</div>
						<?php } ?>
					</div>
				  
				</div>
				<div class="tab-pane" id="tab-about">
					<?php echo $text_about_page; ?>
				</div>
			</div>
        </form>
      </div>
    </div>
  </div>  
</div>
<!-- Modal -->
<div class="modal fade" id="translateExpertModal" tabindex="-1" role="dialog" aria-labelledby="translateExpertModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="translateExpertModalLabel"><?php echo $heading_title; ?> - <?php echo $tab_translate_site; ?></h5>
        <button type="button" class="btn btn-secondary stop_modal_process" onclick="return confirmStopTranslationModelProcess('<?php echo $entry_stop_modal_process_confirmation; ?>')"><?php echo $entry_stop_modal_process; ?></button>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary close_modal" onclick="$('#translateExpertModal').modal('hide');"><?php echo $entry_close; ?></button>
      </div>
    </div>
  </div>
</div>
<!-- js constants -->
<script>
	document.js_const_done_status = "<?php echo $js_const_done_status; ?>";
	document.js_const_in_progress_status = "<?php echo $js_const_in_progress_status; ?>";
	document.js_const_statistic_html = "<?php echo str_replace(array('\n', '\r', PHP_EOL), '', $js_const_statistic_html); ?>";
	document.js_const_analization_process_is_started = "<?php echo $js_const_analization_process_is_started; ?>";
</script>
<?php echo $footer; ?>


