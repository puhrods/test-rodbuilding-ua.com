<?php echo $header; ?>
<div class="content-wrapper template-form" id="content-omanager">

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

	<div class="box-body">
		<div class="nav-tabs-custom">
		  <ul class="nav nav-tabs" id="nav-tabs">
            <li><a href="#tab-columns" data-toggle="tab"><?php echo $tab_template_columns; ?></a></li>
            <li><a href="#tab-html-css" data-toggle="tab"><?php echo $tab_template_html_css; ?></a></li>
            <li><a id="trigger-preview" href="#tab-preview" data-toggle="tab"><?php echo $tab_template_preview; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-columns">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_product.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_table_product_info; ?></p>
			  </div>
			  <div class="panel-group">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-attribute"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_attribute_heading; ?></a></div>
					<div id="set-attribute" class="panel-collapse collapse">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-3 control-label">
								<p><?php echo $entry_attribute_group_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help2; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-9">
							  <textarea name="template[attribute_group_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['attribute_group_tpl']) ? $template['attribute_group_tpl'] : '<b>{attribute_group_name}:</b> [[{attribute}]]<br>'; ?></textarea>
							</div>
						  </div>
						  <div class="form-group">
							<label class="col-sm-3 control-label">
								<p><?php echo $entry_attribute_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_tpl_help; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-9">
							  <textarea name="template[attribute_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['attribute_tpl']) ? $template['attribute_tpl'] : ' • {attribute_name}: {attribute_value}; '; ?></textarea>
							</div>
						  </div>
						</div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-option"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_option_heading; ?></a></div>
					<div id="set-option" class="panel-collapse collapse">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-3 control-label">
								<p><?php echo $entry_option_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_option_tpl_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_option_tpl_help2; ?>" class="fa fa-question-circle text-primary"></i></p>
							</label>
							<div class="col-sm-9">
							  <textarea name="template[option_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['option_tpl']) ? $template['option_tpl'] : '<b>{option_name}:</b> {option_value} [[(остаток: {option_quantity} шт.)]]<br>'; ?></textarea>
							</div>
						  </div>
						</div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-totals"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_total_heading; ?></a></div>
					<div id="set-totals" class="panel-collapse collapse">
					  <div class="panel-body">
						  <div class="form-group">
							<div class="col-sm-2">
							  <div class="input-group">
								<span class="input-group-addon"><?php echo $entry_total_status; ?></span>
								<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_total_heading_help; ?>"><i class="fa fa-info text-blue"></i></span>
								<select name="template[total_status]" class="form-control">
									<option value="1" <?php if (isset($template['total_status']) && $template['total_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
									<option value="0" <?php if (isset($template['total_status']) && $template['total_status'] == '0') { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
								</select>
							  </div>
							</div>

							<label class="col-sm-2 control-label" ><?php echo $entry_total_title_class; ?></label>
							<div class="col-sm-2 valid-block">
							  <input type="text" name="template[class][total_title]" value="<?php echo isset($template['class']['total_title']) ? $template['class']['total_title'] : 'active text-right'; ?>" class="field-input validclass form-control" />
							</div>

							<label class="col-sm-2 control-label"><?php echo $entry_total_title_style; ?></label>
							<div class="col-sm-4 valid-block">
							  <textarea name="template[style][total_title]" class="field-input notcyrillics form-control" placeholder="style"><?php echo isset($template['style']['total_title']) ? $template['style']['total_title'] : ''; ?></textarea>
							</div>
						  </div>
						  <div class="form-group">
							<div class="col-sm-2"></div>

							<label class="col-sm-2 control-label"><?php echo $entry_total_text_class; ?></label>
							<div class="col-sm-2 valid-block">
							  <input type="text" name="template[class][total_text]" value="<?php echo isset($template['class']['total_text']) ? $template['class']['total_text'] : ''; ?>" class="field-input validclass form-control" />
							</div>

							<label class="col-sm-2 control-label"><?php echo $entry_total_text_style; ?></label>
							<div class="col-sm-4 valid-block">
							  <textarea name="template[style][total_text]" class="field-input notcyrillics form-control" placeholder="style"><?php echo isset($template['style']['total_text']) ? $template['style']['total_text'] : ''; ?></textarea>
							</div>
						  </div>

					  </div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#tpl-container"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_product_table_container; ?></a></div>
					<div id="tpl-container" class="panel-collapse collapse">
						<div class="panel-body">
						  <div class="form-group">
							<label class="col-sm-3 control-label">
								<p><?php echo $entry_product_table_container_data; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_product_table_container_data_help; ?>" class="fa fa-question-circle text-danger"></i></p>
							</label>
							<div class="col-sm-9">
							  <textarea name="template[container]" rows="6" id="textarea-container" class="form-control htmlTextArea"><?php echo isset($template['container']) ? $template['container'] : '<div style="width: 100%;">{product_table_template}</div>'; ?></textarea>
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
							  <div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_head_view_status; ?></label>
								<div class="col-sm-2">
									<select name="template[view_status]" class="form-control">
										<option value="1" <?php if (isset($template['view_status']) && $template['view_status'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_head_status_column_on; ?></option>
										<option value="2" <?php if (isset($template['view_status']) && $template['view_status'] == 2) { ?> selected="selected"<?php } ?>><?php echo $text_head_status_column_off; ?></option>
										<option value="3" <?php if (isset($template['view_status']) && $template['view_status'] == 3) { ?> selected="selected"<?php } ?>><?php echo $text_head_status_column_oll; ?></option>
									</select>
								</div>
								<label class="col-sm-2 control-label"><?php echo $entry_image_size; ?></label>
								<div class="col-sm-2 valid-block">
									<span class="input-group">
									  <span class="input-group-addon"><i class="fa fa-image"></i></span>
									  <input type="text" name="template[image_width]" value="<?php echo isset($template['image_width']) ? $template['image_width'] : 64; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_width; ?>"/>
									  <input type="text" name="template[image_height]" value="<?php echo isset($template['image_height']) ? $template['image_height'] : 64; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_height; ?>" />
									</span>
								</div>
								<label class="col-sm-2 control-label"><?php echo $entry_manufacturer_image_size; ?></label>
								<div class="col-sm-2 valid-block">
									<span class="input-group">
									  <span class="input-group-addon"><i class="fa fa-image"></i></span>
									  <input type="text" name="template[man_image_width]" value="<?php echo isset($template['man_image_width']) ? $template['man_image_width'] : 16; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_width; ?>"/>
									  <input type="text" name="template[man_image_height]" value="<?php echo isset($template['man_image_height']) ? $template['man_image_height'] : 16; ?>" class="field-input digit form-control" style="max-width: 90px;" placeholder="<?php echo $placeholder_image_height; ?>" />
									</span>
								</div>
							  </div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 table-responsive">
							  <table class="table table-bordered table-hover" id="product-table">
								<thead>
									<tr class="active">
										<th class="text-center" style="width: 1%;">#</th>
										<th style="width: 24%;"><?php echo $column_column_cell_param; ?></th>
										<th style="width: 75%"><?php echo $column_column_data; ?>&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('product');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a></th>
									</tr>
								</thead>
								<tbody class="tbody-sortable">

								<?php $row = 1; ?>
								<?php if (!empty($template['columns'])) { ?>
								  <?php foreach ($template['columns'] as $column_id => $column) { ?>
									<tr id="product-table-row<?php echo $column_id; ?>">
										<td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i><br/><?php echo $column_id; ?></td>
										<td>
											<input type="hidden" name="template[columns][<?php echo $column_id; ?>][id]" value="<?php echo $column_id; ?>" />
											<div class="valid-block">
											  <div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_title; ?>"><i class="fa fa-css3"></i></span>
												<input type="text" name="template[columns][<?php echo $column_id; ?>][class]" value="<?php echo isset($template['columns'][$column_id]['class']) ? $template['columns'][$column_id]['class'] : ''; ?>" class="field-input validclass form-control" placeholder="class"/>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_classes_help; ?>"><i class="fa fa-info text-blue"></i></span>
											  </div>
											</div>
											<br/>
											<div class="valid-block">
											  <div class="input-group">
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_title; ?>"><i class="fa fa-code"></i></span>
												<textarea name="template[columns][<?php echo $column_id; ?>][style]" class="field-input notcyrillics form-control" placeholder="style" rows="2"><?php echo isset($template['columns'][$column_id]['style']) ? $template['columns'][$column_id]['style'] : ''; ?></textarea>
												<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_styles_help; ?>"><i class="fa fa-info text-blue"></i></span>
											  </div>
											</div>
											<br/>
											<div class="input-group">
											  <div class="input-group-btn" data-toggle="tooltip" title="<?php echo $param_column_remove; ?>">
												<a onclick="$('#product-table-row<?php echo $column_id; ?>').remove(); tooltipRefresh();"  class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a>
											  </div>
											  <select name="template[columns][<?php echo $column_id; ?>][status]" class="<?php echo isset($template['columns'][$column_id]['status']) && $template['columns'][$column_id]['status'] == 1 ? 'text-olive' : 'text-red'; ?> form-control" >
												<option value="1" <?php if (isset($template['columns'][$column_id]['status']) && $template['columns'][$column_id]['status'] == 1) { ?> selected="selected"<?php } ?> class="text-olive"><?php echo $text_enabled; ?></option>
												<option value="0" <?php if (isset($template['columns'][$column_id]['status']) && $template['columns'][$column_id]['status'] == '0') { ?> selected="selected"<?php } ?> class="text-red"><?php echo $text_disabled; ?></option>
											  </select>
											  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_column_status_info; ?>"><i class="fa fa-info text-blue"></i></span>
											</div>
										</td>

										<td>
										  <div class="nav-tabs-custom">
											<ul id="languages" class="nav nav-tabs">
											  <?php foreach ($languages as $language) { ?>
											  <li><a href="#language<?php echo $column_id.'-'.$language['language_id']; ?>" data-toggle="tab"><img src="<?php if ($ocversion >= 220) { echo 'language/'.$language['code'].'/'.$language['code'].'.png'; } else { echo 'view/image/flags/'. $language['image']; } ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
											  <?php } ?>
											</ul>
											<div class="tab-content">
											<?php foreach ($languages as $language) { ?>
											  <div id="language<?php echo $column_id.'-'.$language['language_id']; ?>" class="tab-pane">
												  <div class="box box-default no-border">
													<div class="box-header with-border">
														<div class="pull-left box-tools" style="width: 40%;">
														  <div class="input-group">
															<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_title; ?>"><i class="fa fa-text-width"></i></span>
															<input type="text" name="template[columns][<?php echo $column_id; ?>][name][<?php echo $language['language_id']; ?>]" value="<?php echo isset($template['columns'][$column_id]['name'][$language['language_id']]) ? $template['columns'][$column_id]['name'][$language['language_id']] : ''; ?>" class="column-name form-control" placeholder="<?php echo $column_title; ?>"/>
															<div class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_summernote_editor; ?>">
																<div class="btn-group" data-toggle="buttons">
																	<label class="btn btn-default" onclick="summernote('<?php echo $column_id.'-'.$language['language_id']; ?>');"><input type="checkbox"/><i id="btn_start_editor_icon<?php echo $column_id.'-'.$language['language_id']; ?>" class="fa fa-edit text-red"></i></label>
																</div>
															</div>
															<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $btn_summernote_editor_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
														  </div>
														</div>

														<div class="pull-right box-tools"  id="load-block-<?php echo $column_id.'-'.$language['language_id']; ?>" style="width: 60%; display: none;">
														  <div class="input-group">
															<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">
															  <input type="checkbox" id="tpl-add<?php echo $column_id.'-'.$language['language_id']; ?>" value="1" />
															</span>
															<select data-add-id="<?php echo $column_id.'-'.$language['language_id']; ?>" class="load-tpl-block form-control">
																<option value=""><?php echo $text_load_tpl; ?></option>
															  <?php foreach ($product_blocks as $block) { ?>
																<option value="<?php echo $block['template_id']; ?>"><?php echo $block['template_id'] .' - '.$block['name']; ?></option>
															  <?php } ?>
															</select>
															<span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock('product', 'language<?php echo $column_id.'-'.$language['language_id']; ?>');"><i class="fa fa-save"></i></a></span>
															<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>
														  </div>
														</div>

													</div>
													<div class="box-body">
														<textarea name="template[columns][<?php echo $column_id; ?>][data][<?php echo $language['language_id']; ?>]" id="textarea<?php echo $column_id.'-'.$language['language_id']; ?>" rows="4" class="column-data form-control"><?php echo isset($template['columns'][$column_id]['data'][$language['language_id']]) ? $template['columns'][$column_id]['data'][$language['language_id']] : ''; ?></textarea>
													</div>
												  </div>

											  </div>
											<?php } ?>
											</div>
										  </div>
										</td>
									</tr>

									<?php $row=$column_id>=$row?$column_id+1:$row; ?>
								  <?php } ?>
								<?php } else { ?>
									<tr id="columns_no_results"><td colspan="4" class="text-center"><?php echo $text_no_results; ?></td></tr>
								<?php } ?>
								</tbody>
								<tfoot>
									<tr class="active">
										<td></td>
										<td colspan="2" id="product-table-add-column"><a onclick="addColumn('<?php echo $row; ?>');" class="btn btn-success" data-toggle="tooltip" title="<?php echo $button_add_row; ?>"><i class="fa fa-plus"></i> <?php echo $button_add_column; ?></a></td>
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

			<div class="tab-pane" id="tab-html-css">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_product.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $entry_table_class_style_info; ?></p>
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
				  <textarea name="template[script]" rows="10" class="jsTextArea field-input notcyrillics form-control"><?php echo isset($template['script']) ? $template['script'] : ''; ?></textarea>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab-preview">
			  <div class="form-group">
				<div class="col-sm-4">
				  <div class="input-group full-width" style="height: 34px;">
					<div class="input-group-btn"><a class="btn btn-info" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_product.html#tab=0&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i></a></div>
					<span class="input-group-addon text-left" style="width: calc(100% - 40px);"><?php echo $text_view_edit_link; ?></span>
					<span class="input-group-addon" style="width: 40px; border-left: 1px solid #d2d6de;">
						<input type="checkbox" id="view_edit_link" value="1" style="cursor: pointer;"/>
					</span>
				  </div>
				</div>
				<div class="col-sm-4 valid-block">
				  <div class="input-group">
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_last_order; ?>">&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;</span>
					<input type="text" value="<?php echo $last_order; ?>" id="test-order-id" class="field-input digit mustbe form-control" />
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_last_order_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
				  </div>
				</div>
				<div class="col-sm-4"><a class="btn btn-default" onclick="preview(true);" ><?php echo $button_modal_preview; ?></a></div>
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

$(document).delegate('#trigger-preview', 'click', function() {
	if ($('#preview-content').html() == '') { preview(); }
});

$(document).delegate('#test-order-id', 'change', function() {
	preview();
});

$(document).delegate('#view_edit_link', 'change', function() {
	preview();
});

function preview(modal = false) {
	var template_id = '<?php echo $template_id; ?>';
	if (!template_id || template_id == 0) {
		toastr.options.preventDuplicates = true;
		toastr.error('<?php echo $error_template_preview; ?>', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var order_id = $('#test-order-id').val();
	if (!order_id) {
		toastr.options.timeOut = 5000;
		toastr.error('№ заказа не указан!', '<?php echo $text_alert_error; ?>' );
		return false;
	}

	var url = 'index.php?route=sale/ompro/getOrderProductsTable&<?php echo $strtoken; ?>&order_id=' + order_id + '&template_id=' + template_id;

	to_edit = $('#view_edit_link').prop('checked') ? 1 : 0;
	if (to_edit) {
		url += '&to_edit=true';
	}

	$.ajax({
		url: url,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			$('.alert').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['content']) {
				if (modal) {
					modalAlert('modal-default', json['title'], '<p>'+json['content']+'</p>', '', 'modal-lg');
				} else {
					$('#preview-content').html(json['content']);
				}
			} else {
				if (modal) {
					modalAlert('modal-default', json['title'], '<p><?php echo $text_no_results; ?></p>', '');
				} else {
					$('#preview-content').html('<?php echo $text_no_results; ?>');
				}
			}
			xEditableStart();
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

--></script>

<script type="text/javascript"><!--

function addColumn(row){
	html = '<tr id="product-table-row'+row+'">';
	html += '<td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i><br>'+row+'</td>';
	html += '<td>';
	html += ' <input type="hidden" name="template[columns]['+row+'][id]" value="' + row +'"/>';

	html += ' <div class="valid-block"><div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_class_title; ?>"><i class="fa fa-css3"></i></span>';
	html += '  <input type="text" name="template[columns]['+row+'][class]" value="" class="field-input validclass form-control" placeholder="class"/>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_classes_help; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div></div><br/>';

	html += ' <div class="valid-block"><div class="input-group">';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_style_title; ?>"><i class="fa fa-code"></i></span>';
	html += '  <textarea name="template[columns]['+row+'][style]" class="field-input notcyrillics form-control" placeholder="style" rows="2"></textarea>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $column_column_styles_help; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div></div><br/>';

	html += ' <div class="input-group">';
	html += '  <div class="input-group-btn" data-toggle="tooltip" title="<?php echo $param_column_remove; ?>">';
	html += '   <a onclick="$(\'#product-table-row'+row+'\').remove(); tooltipRefresh();" class="btn btn-default" data-toggle="tooltip" title="<?php echo $button_remove; ?>"><i class="fa fa-minus-circle"></i></a>';
	html += '  </div>';
	html += '  <select name="template[columns]['+row+'][status]" class="text-olive form-control">';
	html += '   <option value="1" class="text-olive"><?php echo $text_enabled; ?></option>';
	html += '   <option value="0" class="text-red"><?php echo $text_disabled; ?></option>';
	html += '  </select>';
	html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_column_status_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += ' </div>';
	html += '</td>';
	html += '<td>';
	html += ' <div class="nav-tabs-custom">';
	html += '  <ul id="languages" class="nav nav-tabs">';
	<?php foreach ($languages as $language) { ?>
	html += '   <li><a href="#language'+row+'-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="<?php if ($ocversion >= 220) { echo 'language/'.$language['code'].'/'.$language['code'].'.png'; } else { echo 'view/image/flags/'. $language['image']; } ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>';
	<?php } ?>
	html += '  </ul>';
	html += '  <div class="tab-content">';
	<?php foreach ($languages as $language) { ?>
	html += '   <div id="language'+row+'-<?php echo $language['language_id']; ?>" class="tab-pane">';
	html += '    <div class="box box-default no-border">';
	html += '     <div class="box-header with-border">';
	html += '      <div class="pull-left box-tools" style="width: 40%;">';
	html += '       <div class="input-group">';
	html += '        <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $param_name_title; ?>"><i class="fa fa-text-width"></i></span>';
	html += '        <input type="text" name="template[columns]['+row+'][name][<?php echo $language['language_id']; ?>]" value="" class="column-name form-control" placeholder="<?php echo $column_title; ?>"/>';
	html += '        <div class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_summernote_editor; ?>">';
	html += '         <div class="btn-group" data-toggle="buttons">';
	html += '          <label class="btn btn-default" onclick="summernote(\''+row+'-<?php echo $language['language_id']; ?>\');"><input type="checkbox"/><i id="btn_start_editor_icon'+row+'-<?php echo $language['language_id']; ?>" class="fa fa-edit text-red"></i></label>';
	html += '         </div>';
	html += '        </div>';
	html += '        <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $btn_summernote_editor_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>';
	html += '       </div>';
	html += '      </div>';
	html += '      <div class="pull-right box-tools"  id="load-block-'+row+'-<?php echo $language['language_id']; ?>" style="width: 60%; display: none;">';
	html += '       <div class="input-group">';
	html += '        <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">';
	html += '         <input type="checkbox" id="tpl-add'+row+'-<?php echo $language['language_id']; ?>" value="1" />';
	html += '        </span>';
	html += '        <select data-add-id="'+row+'-<?php echo $language['language_id']; ?>" class="load-tpl-block form-control">';
	html += '         <option value="" ><?php echo $text_load_tpl; ?></option>';
						<?php foreach ($product_blocks as $block) { ?>
	html += '         <option value="<?php echo $block['template_id']; ?>" ><?php echo $block['name']; ?></option>';
						<?php } ?>
	html += '        </select>';
	html += '        <span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock(\'product\', \'language'+row+'-<?php echo $language['language_id']; ?>\');" ><i class="fa fa-save"></i></a></span>';
	html += '        <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>';
	html += '       </div>';
	html += '      </div>';
	html += '     </div>';
	html += '     <div class="box-body">';
	html += '      <textarea name="template[columns]['+row+'][data][<?php echo $language['language_id']; ?>]" id="textarea'+row+'-<?php echo $language['language_id']; ?>" rows="4" class="column-data form-control"></textarea>';
	html += '     </div>';
	html += '    </div>';
	html += '   </div>';
				<?php } ?>
	html += '  </div>';
	html += ' </div>';
	html += '</td>';
	html += '</tr>';

	var no_result = $('#columns_no_results').html();
	if (no_result !='') {
		$('#columns_no_results').remove();
	}

	$('#product-table > tbody').append(html);
	$('#input-style-type').trigger('change');

	row++;

	$('#product-table-add-column > a').attr('onclick', 'addColumn('+row+')');
	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh('#product-table');

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});
}

var lang = '<?php echo $summernote_lang; ?>';
var toolbar = [['insert',['picture','link','video','table']], ['style',['bold','italic','underline']], ['font', ['strikethrough', 'superscript', 'subscript']],['fontsize', ['fontsize','fontname']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph','style']], ['height', ['height','codeview']],];
var fontNames = ['Arial','Times New Roman','Helvetica','Verdana'];
var placeholder = '<?php echo $text_summernote_placeholder; ?>';

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

function summernote(id) {
	var td = $('#textarea'+id).closest('td');
	var load_block = $('#load-block-'+id);
	if (!td.hasClass('editing'+id)) {
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
		td.addClass('editing'+id);
		toastr.options.timeOut = 2000;
		toastr.success('Редактор включен!', '<?php echo $text_alert_success; ?>');
	} else {
		load_block.hide();
		td.removeClass('editing'+id);
		var code = $('#textarea'+id).summernote('code');
		$('#textarea'+id).summernote('destroy');
		$('#textarea'+id).val(code);
		toastr.options.timeOut = 2000;
		toastr.warning('Редактор выключен!', '<?php echo $text_alert_success; ?>');
	}

	$('#btn_start_editor_icon'+id).toggleClass('text-red text-blue');
}

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
	$('.htmlTextArea').each(function(){
		var htmlTextArea = $(this);
		var cm = CodeMirror.fromTextArea($(this)[0], { height: "auto", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			htmlTextArea.html(editor.getValue()).trigger('change');
		});
	});

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
	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

});

//--></script>

</div>
<?php echo $footer; ?>