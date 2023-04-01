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
<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/bootstrap-checkbox.js"></script>

<section class="content-header">
  <h1><?php echo $text_tab_setting_pages; ?>
	<small></small>
  </h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section id="content" class="content">
  <div class="box">
	<div class="box-header with-border">
		<div class="row" id="top_row">
		  <div class="col-sm-10 box-tools pull-left">
			<div class="row">
			  <div class="col-sm-4 valid-block">
				<div class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_code; ?>"><?php echo $text_tpl_code; ?></div>
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_name; ?>"><i class="fa fa-text-width"></i></div>
					<input type="text" id="input-name" name="setting[name]" value="<?php echo isset($setting['name']) ? $setting['name'] : $text_new_template; ?>" class="setdata field-input mustbe form-control" />
				</div>
			  </div>
			  <div class="col-sm-6">
				<div class="input-group">
					<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_description; ?>"><i class="fa fa-info"></i></div>
					<textarea name="setting[description]" id="input-description" rows="1" class="setdata form-control"><?php echo isset($setting['description']) ? $setting['description'] : ''; ?></textarea>
				</div>
			  </div>
			  <div class="col-sm-2">
				<div class="input-group valid-block">
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_template_icon; ?>"> <i class="fa fa-picture-o"></i> </span>
					<input type="text" name="setting[icon]" value="<?php echo isset($setting['icon']) ? $setting['icon'] : 'fa-file-text-o'; ?>" class="setdata field-input validclass form-control" />
				</div>
			  </div>
			</div>
		  </div>
		  <div class="col-sm-2 box-tools pull-right text-right">
			<a id="button_save" class="btn btn-primary" onclick="savePage();"><i class="fa fa-save"></i> &nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>
  </div>

  <div class="row">
	<div class="col-sm-12">
	  <div class="nav-tabs-custom">
		<ul class="nav nav-tabs" role="tablist">
			<li class="active"><a href="#tab_constructor" role="tab" data-toggle="tab"><i class="fa fa-sitemap"></i>&nbsp;&nbsp;<?php echo $text_tab_constructor; ?></a></li>
			<li><a href="#tab-css-js" data-toggle="tab"><?php echo $text_tab_css_js; ?></a></li>
			<li><a href="#tab_orders" role="tab" data-toggle="tab"><i class="fa fa-list"></i>&nbsp;&nbsp;<?php echo $text_tab_orders; ?></a></li>
			<li><a href="#tab_map" role="tab" data-toggle="tab"><i class="fa fa-globe"></i>&nbsp;&nbsp;<?php echo $text_tab_map; ?></a></li>
			<li><a href="#tab_order_formats" role="tab" data-toggle="tab"><i class="fa fa-shopping-cart"></i>&nbsp;&nbsp;<?php echo $text_tab_group_order_formats; ?></a></li>
			<li><a href="#tab_product_formats" role="tab" data-toggle="tab"><i class="fa fa-tag"></i>&nbsp;&nbsp;<?php echo $text_tab_group_product_formats; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="tab_constructor">
			  <p>
				<div class="btn-group btn-group-sm" data-toggle="buttons">
				  <a class="btn btn-info doc_link" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_01"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i></a>
				  <label class="btn btn-default mode_info" data-toggle="tooltip" title="<?php echo $btn_editor_mode_select; ?>"><i class="fa fa-info-circle text-blue"></i></label>
				  <label class="btn btn-success" data-toggle="tooltip" title="<?php echo $title_editor_mode_preview; ?>"><input type="radio" name="editor_mode" value="preview"/><i class="fa fa-eye"></i>&nbsp; <?php echo $btn_editor_mode_preview; ?></label>
				  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $title_editor_mode_blocks; ?>"><input type="radio" name="editor_mode" value="blocks"/><i class="fa fa-plus text-olive"></i>&nbsp; <i class="fa fa-arrows"></i>&nbsp; <i class="fa fa-edit text-orange"></i>&nbsp; <?php echo $btn_editor_mode_blocks; ?></label>
				  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $title_editor_mode_sortColElem; ?>"><input type="radio" name="editor_mode" value="sortColElem"/><i class="fa fa-plus text-olive"></i>&nbsp; <i class="fa fa-arrows"></i>&nbsp; <?php echo $btn_editor_mode_sortColElem; ?></label>
				  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $title_editor_mode_sortTools; ?>"><input type="radio" name="editor_mode" value="sortTools"/><i class="fa fa-plus text-olive"></i>&nbsp; <i class="fa fa-arrows"></i>&nbsp; <?php echo $btn_editor_mode_sortTools; ?></label>
				  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $title_editor_mode_btngroup; ?>"><input type="radio" name="editor_mode" value="btngroup"/><i class="fa fa-plus text-olive"></i>&nbsp; <i class="fa fa-arrows"></i>&nbsp;<?php echo $btn_editor_mode_btngroup; ?></label>
				  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $title_editor_mode_btn; ?>"><input type="radio" name="editor_mode" value="btn"/><i class="fa fa-edit text-orange"></i>&nbsp; <?php echo $btn_editor_mode_btn; ?></label>
				</div>
			  </p>

			  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-editor" class="">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title"><i class="fa fa-th-large"></i> <?php echo $text_constructor; ?></h4>
					</div>
					<div class="panel-body">
						<div id="editor" class="preview-mode omanager-content"></div>
						<div id="css_script"></div>
						<input type="hidden" id="data-constructor" class="setdata" name="setting[constructor]"/>
						<input type="hidden" id="data-filters-top" class="setdata" name="setting[filters_top]"/>
						<input type="hidden" id="data-filters-product" class="setdata" name="setting[filters_product]"/>
					</div>
				</div>
			  </form>

			  <div class="panel panel-default" id="box-elements" style="display: none;">
				<div class="panel-heading">
					<h4 class="panel-title"><i class="fa fa-th-list"></i> <?php echo $text_el_preview; ?></h4>
				</div>

				<div class="panel-body">
					<!-- page_blocks -->
					<?php foreach ($page_blocks as $block) { ?>
					<div class="btn-group <?php echo $block['class']; ?>" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $block['text']; ?>"> <i class="fa fa-<?php echo $block['icon']; ?> text-blue"></i> </div>
						<select class="load-element form-control" data-load-source="<?php echo $block['target']; ?>">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <?php foreach ($block['templates'] as $template) { ?>
							<option value="<?php echo $template['template_id']; ?>" ><?php echo $template['name']; ?></option>
						  <?php } ?>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>
					<?php } ?>

					<!-- page_html_content_vars -->
					<?php foreach ($page_html_content_vars as $content_var) { ?>
					<div class="btn-group <?php echo $content_var['class']; ?>" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $content_var['text']; ?>"> <i class="fa fa-<?php echo $content_var['icon']; ?> text-red"></i> </div>
						<select class="load-element form-control" data-load-source="<?php echo $content_var['target']; ?>">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <?php foreach ($content_var['vars'] as $key => $name) { ?>
							<option value="<?php echo $key; ?>" ><?php echo $name; ?></option>
						  <?php } ?>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>
					<?php } ?>

					<!-- filter_top -->
					<div class="btn-group blocks-el column-el" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_el_top_filters; ?>"> <i class="fa fa-filter"></i> </div>
						<select class="load-element form-control" data-load-source="filter_top">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <option value="*"><?php echo $text_el_all_top_filters; ?></option>
						  <?php foreach ($filters as $filter) { ?>
							<option value="<?php echo $filter['template_id']; ?>" ><?php echo $filter['name']; ?></option>
						  <?php } ?>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>

					<!-- filter_product_top -->
					<div class="btn-group blocks-el column-el" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_el_top_product_filters; ?>"> <i class="fa fa-tags"></i> </div>
						<select class="load-element form-control" data-load-source="filter_product_top">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <option value="*"><?php echo $text_el_all_top_filters; ?></option>
						  <?php foreach ($filters_product as $filter) { ?>
							<option value="<?php echo $filter['template_id']; ?>" ><?php echo $filter['name']; ?></option>
						  <?php } ?>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>

					<!-- order_table_templates_list -->
					<div class="btn-group blocks-el column-el" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_el_order_table_list; ?>"> <i class="fa fa-shopping-cart"></i> </div>
						<select class="load-element form-control" data-load-source="order_table">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <?php foreach ($order_table_templates_list as $template) { ?>
							<option value="<?php echo $template['template_id']; ?>" ><?php echo $template['name']; ?></option>
						  <?php } ?>
						  <option value="pagination"><?php echo $text_el_pagination; ?></option>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>

					<!-- filter_tools -->
					<div class="btn-group tools-el" style="max-width: 340px;">
					  <div class="input-group">
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_el_tools_filters; ?>"> <i class="fa fa-filter"></i> </div>
						<select class="load-element form-control" data-load-source="filter_tools" title="">
						  <option value=""><?php echo $text_not_selected; ?></option>
						  <?php foreach ($filters as $filter) { ?>
							<option value="<?php echo $filter['template_id']; ?>" ><?php echo $filter['name']; ?></option>
						  <?php } ?>
						</select>
						<div class="input-group-btn"><label type="button" class="btn btn-default load-element-repeat" data-toggle="tooltip" title="<?php echo $text_el_repeat; ?>"><i class="fa fa-download"></i></label></div>
					  </div>
					</div>

					<!-- btngroups -->
					<div class="btn-group dropup tools-el btngroup-el">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?php echo $text_btngroup; ?>
						&nbsp;<span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
						  <?php foreach ($btngroups as $group) { ?>
							<li class="dropdown-header">
								<div class="input-group">
								  <span class="input-group-addon"><?php echo $group['title']; ?></span>
								  <input type="text" value="3" class="form-control" id="qty_<?php echo $group['id']; ?>" style="min-width: 50px;" data-toggle="tooltip" title="<?php echo $text_el_entry_qty; ?>"/>
								  <div class="input-group-btn"><label class="btn btn-default" data-toggle="tooltip" title=<?php echo $text_load; ?> onclick="getElements('btngroups', '<?php echo $group['id']; ?>', 'qty_<?php echo $group['id']; ?>');"><i class="fa fa-download"></i></label></div>
								</div>
							</li>
						  <?php } ?>
							<li class="divider"></li>
							<li><a onclick="getElements('btngroups');"><?php echo $text_el_all_btngroups; ?></a></li>
						</ul>
					</div>

					<div class="btn-group">
						<button type="button" class="btn btn-default dropdown-toggle" onclick="$('#elements-preview').html('<p><?php echo $text_preview_el_placeholder; ?></p>');" data-toggle="tooltip" title="<?php echo $text_el_reset; ?>"><i class="fa fa-eraser"></i> <?php echo $text_btn_filter_clear_text; ?></button>
					</div>
				</div>

				<div id="elements-preview" class="box-footer">
				  <p><?php echo $text_preview_el_placeholder; ?></p>
				</div>

			  </div>

			  <div class="panel panel-default">
				<div class="panel-heading"><a data-toggle="collapse" href="#constructor-help"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_constructor_help_title; ?></a></div>
				<div id="constructor-help" class="panel-collapse collapse">
					<div class="panel-body">
						<p>
						<ol>
							<li><?php echo $text_constructor_help_1; ?></li>
							<li><?php echo $text_constructor_help_2; ?></li>
							<li><?php echo $text_constructor_help_3; ?></li>
							<li><?php echo $text_constructor_help_4; ?></li>
						</ol>
						</p>
					</div>
				</div>
			  </div>

			</div>

			<div class="tab-pane" id="tab-css-js">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_02"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $entry_table_class_style_info; ?></p>
			  </div>

			  <div class="panel panel-default">
				<div class="panel-body">
				  <div class="form-group">
					<label class="col-sm-2 control-label">CSS:</label>
					<div class="col-sm-10">
					  <textarea name="setting[css]" name="setting[css]" rows="10" class="cssTextArea setdata field-input notcyrillics form-control"><?php echo isset($setting['css']) ? $setting['css'] : ''; ?></textarea>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="panel panel-default">
				<div class="panel-body">
				  <div class="form-group">
					<label class="col-sm-2 control-label">Java Script:</label>
					<div class="col-sm-10">
					  <textarea name="setting[script]" rows="10" class="jsTextArea setdata field-input notcyrillics form-control"><?php echo isset($setting['script']) ? $setting['script'] : ''; ?></textarea>
					</div>
				  </div>
				</div>
			  </div>

			</div>

			<div class="tab-pane" id="tab_orders">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_03"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_orders_info; ?></p>
			  </div>

			  <div class="row">
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_limit; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-list-ol"></i></span>
						<input type="text" name="setting[order_limit]" value="<?php echo isset($setting['order_limit']) ? $setting['order_limit'] : 10; ?>" placeholder="<?php echo $entry_limit; ?>" class="setdata field-input digitmustbe form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_limit_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_sortdefault; ?></label>
				  <div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-sort-amount-asc"></i></span>
						<select name="setting[sortdefault]" class="setdata multiselect form-control">
						  <?php foreach ($order_codes as $code) { ?>
							<?php if ($code['sort_key']) { ?>
								<option value="<?php echo $code['sort_key']; ?>" <?php if (isset($setting['sortdefault']) && $setting['sortdefault'] == $code['sort_key']) { ?> selected="selected"<?php } ?>> <?php echo $code['name']; ?></option>
							<?php } ?>
						  <?php } ?>
						</select>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_sortdefault_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_orderdefault; ?></label>
				  <div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-sort"></i></span>
						<select name="setting[orderdefault]" class="setdata form-control">
						<?php if (isset($setting['orderdefault']) && $setting['orderdefault'] == 'ASC') { ?>
							<option value="ASC" selected="selected"><?php echo $text_sort_asc; ?></option>
							<option value="DESC"><?php echo $text_sort_desc; ?></option>
						<?php } else { ?>
							<option value="ASC"><?php echo $text_sort_asc; ?></option>
							<option value="DESC" selected="selected"><?php echo $text_sort_desc; ?></option>
						<?php } ?>
						</select>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="row">
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_pp_start; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-windows"></i></span>
						<input type="text" name="setting[pp_start]" value="<?php echo isset($setting['pp_start']) ? $setting['pp_start'] : 5; ?>" id="input-pp-start" class="setdata field-input digitmustbe form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_pp_start_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_pp_template; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-th-list"></i></span>
						<select name="setting[pp_template_id]" class="setdata multiselect form-control">
						  <?php foreach ($product_table_templates as $template) { ?>
							<option value="<?php echo $template['code']; ?>" <?php echo $template['selected']; ?>><?php echo $template['template_id'] .' - '.$template['name']; ?></option>
						  <?php } ?>
						</select>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_pp_template_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="row">

				<div class="col-sm-4">
					<div class="panel panel-default">
					  <div class="panel-heading">
						<span class="pull-right text-right" style="width: 60%;"><b><?php echo $entry_pp_trigger; ?></b> &nbsp;&nbsp;<i class="fa fa-info-circle text-danger" data-toggle="tooltip" title="<?php echo $entry_pp_trigger_help; ?>"></i></span>
						<span style="width: 40%;"><a data-toggle="collapse" href="#trigger-example"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_pp_trigger_view_example; ?></a></span>
					  </div>
					  <div id="trigger-example" class="panel-collapse collapse">
						<div class="panel-body">
						<p><pre><?php echo $entry_pp_trigger_example; ?></pre></p>
						<p><?php echo $entry_pp_trigger_info; ?></p>
						</div>
					  </div>
					</div>


				</div>
				<div class="col-sm-8">
					<div class="form-group">
					  <div class="input-group">
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_add_tpl; ?>">
						  <input type="checkbox" id="tpl-add-trigger" value="1" />
						</span>
						<select data-add-id="trigger" class="load-tpl-block form-control">
							<option value=""><?php echo $text_load_tpl; ?></option>
						  <?php foreach ($pptriggers as $rigger) { ?>
							<option value="<?php echo $rigger['template_id']; ?>"><?php echo $rigger['template_id'] .' - '.$rigger['name']; ?></option>
						  <?php } ?>
						</select>
						<span class="input-group-addon" data-toggle="tooltip" title="Название сохраняемого блока"><i class="fa fa-text-width"></i></span>
						<input type="text" id="tpl-name-trigger" class="form-control" placeholder="введите название чтобы сохранить" />
						<span class="input-group-btn" data-toggle="tooltip" title="<?php echo $btn_save_block_title; ?>"><a class="btn btn-default" onclick="saveBlock('pptrigger', 'trigger');"><i class="fa fa-save"></i></a></span>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $summernote_editor_info; ?>"><i class="fa fa-exclamation-triangle text-danger"></i></span>
					  </div>
					</div>

					<div class="form-group">
					  <textarea name="setting[pp_trigger_template]" id="textarea-trigger" rows="6" class="setdata textarea-summernote form-control"><?php echo isset($setting['pp_trigger_template']) ? $setting['pp_trigger_template'] : ''; ?></textarea>
					</div>
				</div>
			  </div>
			</div>

			<div class="tab-pane" id="tab_map">
			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_04"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_map_info; ?></p>
			  </div>
			  <div class="row">
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $text_ymap_apikey; ?></label>
				  <span data-toggle="tooltip" title="<?php echo $text_ymap_apikey_info; ?>"> <?php echo $text_get_apikey; ?></span>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-key"></i></span>
						<input type="text" name="setting[ymap_apikey]" value="<?php echo isset($setting['ymap_apikey']) ? $setting['ymap_apikey'] : ''; ?>" class="setdata field-input notcyrillics form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_ymap_apikey_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_height_map; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
						<input type="text" name="setting[height_map]" value="<?php echo isset($setting['height_map']) ? $setting['height_map'] : ''; ?>" placeholder="600" class="setdata field-input digit form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_height_map_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>

				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_shop_on_map; ?></label>
				  <div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
						<div class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_shop_on_map_help; ?>">
							<input type="checkbox" name="setting[shop_on_map]" value="1" <?php if (isset($setting['shop_on_map']) && $setting['shop_on_map'] == 1) { ?> checked="checked" <?php } ?> class="setdata" />
						</div>
						<input type="text" name="setting[shop_name]" value="<?php echo isset($setting['shop_name']) ? $setting['shop_name'] : ''; ?>" placeholder="<?php echo $entry_shop_name_placeholder; ?>" class="setdata form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_shop_name_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
			  </div>

			  <div class="row">
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_shop_adress; ?></label>
				  <div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-home"></i></span>
						<input type="text" name="setting[shop_adress]" value="<?php echo isset($setting['shop_adress']) ? $setting['shop_adress'] : ''; ?>" placeholder="<?php echo $entry_shop_adress_placeholder; ?>" class="setdata form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_shop_adress_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_map_zoom; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-globe"></i></span>
						<input type="text" name="setting[map_zoom]" value="<?php echo isset($setting['map_zoom']) ? $setting['map_zoom'] : 14; ?>" placeholder="14" class="setdata field-input digit form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_map_zoom_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_map_center; ?></label>
				  <div class="form-group">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-dot-circle-o"></i></span>
						<select name="setting[map_center]" id="input-map-center" class="setdata form-control">
							<option value="1"<?php if (isset($setting['map_center']) && $setting['map_center'] == 1) { ?> selected="selected"<?php } ?>><?php echo $text_selected_orders; ?></option>
							<option value="2"<?php if (isset($setting['map_center']) && $setting['map_center'] == 2) { ?> selected="selected"<?php } ?>><?php echo $text_shop_adress; ?></option>
						</select>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_map_center_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
			  </div>
			  <div class="row">
				<div class="col-lg-4">
				  <label class="control-label"><?php echo $entry_map_coords_default; ?></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-map-pin"></i></span>
						<input type="text" name="setting[map_coords_default]" value="<?php echo isset($setting['map_coords_default']) ? $setting['map_coords_default'] : '55.750358, 37.611149'; ?>" placeholder="14" class="setdata field-input coords form-control" />
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_map_coords_default_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
				<div class="col-lg-8">
				  <label class="control-label"><?php echo $entry_map_address_format; ?>&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('order');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a></label>
				  <div class="form-group valid-block">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-code"></i></span>
						<input type="text" name="setting[map_address_format]" value="<?php echo isset($setting['map_address_format']) ? $setting['map_address_format'] : '[[{shipping_country}]], [[{shipping_zone}]], [[{shipping_city}]], [[{shipping_address_1}]], [[{shipping_address_2}]]'; ?>" placeholder="[[{shipping_country}]], [[{shipping_zone}]], [[{shipping_city}]], [[{shipping_address_1}]], [[{shipping_address_2}]]" class="setdata field-input notcyrillics form-control" />
						<span class="input-group-addon" data-toggle="tooltip" data-placement="left" title="<?php echo $entry_map_address_format_help; ?>"><i class="fa fa-info text-blue"></i></span>
					</div>
				  </div>
				</div>
			  </div>
			</div>

		  <div class="tab-pane" id="tab_order_formats">
			<div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_05"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_group_order_formats_help; ?></p>
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

				<?php if (isset($setting['order_formats'])) { ?>
				  <?php foreach ($setting['order_formats'] as $o_format) { ?>
				   <tr id="order-format-row-<?php echo $order_format_row; ?>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-left"><?php echo $o_format['code']; ?>
						<input type="hidden" name="setting[order_formats][<?php echo $o_format['code']; ?>][code]" value="<?php echo $o_format['code']; ?>" class="setdata order-code" />
					  </td>
					  <td class="text-left">
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<select name="setting[order_formats][<?php echo $o_format['code']; ?>][type]" data-format-code="<?php echo $o_format['code']; ?>" target-order-format="order-format-<?php echo $order_format_row; ?>" class="select-order-format-type setdata form-control">
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
							<input type="text" name="setting[order_formats][<?php echo $o_format['code']; ?>][date_format]" value="<?php echo isset($o_format['date_format']) ? $o_format['date_format'] : 'd.m.Y'; ?>" placeholder="<?php echo 'd.m.Y H:i:s'; ?>" class="field-input mustbe setdata form-control" />
						</div>
					  <?php } ?>
					  <?php if ($o_format['type'] == 'method') { ?>
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
							<select name="setting[order_formats][<?php echo $o_format['code']; ?>][process_method]" class="setdata form-control">
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
				<?php } ?>
				</tbody>
				<tfoot>
					<tr>
					  <td colspan="3" class="text-right text-middle"><b><?php echo $text_add_format_for; ?></b> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_add_format_for_info; ?>"></i></td>
					  <td class="text-left">
						<select id="order_code" class="multiselect form-control">
						  <?php foreach ($order_codes as $code) { ?>
							<option value="<?php echo $code['code']; ?>"><?php echo $code['name']; ?></option>
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
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_page.html#tab=0&item=item_06"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_group_product_formats_help; ?></p>
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

				<?php if (isset($setting['product_formats'])) { ?>
				  <?php foreach ($setting['product_formats'] as $p_format) { ?>
				   <tr id="product-format-row-<?php echo $product_format_row; ?>">
					  <td class="text-center handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i></td>
					  <td class="text-left"><?php echo $p_format['code']; ?>
						<input type="hidden" name="setting[product_formats][<?php echo $p_format['code']; ?>][code]" value="<?php echo $p_format['code']; ?>" class="setdata product-code" />
					  </td>
					  <td class="text-left">
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>
							<select name="setting[product_formats][<?php echo $p_format['code']; ?>][type]" data-format-code="<?php echo $p_format['code']; ?>" target-product-format="product-format-<?php echo $product_format_row; ?>" class="select-product-format-type setdata form-control">
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
							<input type="text" name="setting[product_formats][<?php echo $p_format['code']; ?>][date_format]" value="<?php echo isset($p_format['date_format']) ? $p_format['date_format'] : 'd.m.Y'; ?>" placeholder="<?php echo 'd.m.Y H:i:s'; ?>" class="field-input mustbe setdata form-control" />
						</div>
					  <?php } ?>
					  <?php if ($p_format['type'] == 'method') { ?>
						<div class="input-group">
							<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_method_info; ?>"><i class="fa fa-exclamation-triangle text-red"></i></span>
							<select name="setting[product_formats][<?php echo $p_format['code']; ?>][process_method]" class="setdata form-control">
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
				  <form action="<?php echo $restore_link ?>" method="post" enctype="multipart/form-data" id="form-restore" class="form-horizontal">
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
				  </form>
				</div>
			  </div>
			</div>
		  </div>

		</div>
	  </div>
	</div>
  </div>

</section>
<script type="text/javascript" src="view/javascript/ompro/ompro.constructor.js?<?php echo $version; ?>"></script>

<div id="hidden_icons" class="box-body bg-info hidden-default"><div id="btn_icons"><?php echo $btn_icons; ?></div></div>

<script type="text/javascript"><!--

$(".doc_link").on('click', function(e) {
	e.preventDefault();
	window.open($(this).attr('href'));
});

$(".mode_info").on('click', function(e) {
	e.preventDefault(); return;
});

var lang = '<?php echo $summernote_lang; ?>';
var toolbar = [['insert',['picture','link','video','table']], ['style',['bold','italic','underline']], ['font', ['strikethrough', 'superscript', 'subscript']],['fontsize', ['fontsize','fontname']], ['color', ['color']], ['para', ['ul', 'ol', 'paragraph','style']], ['height', ['height','codeview']],];
var fontNames = ['Arial','Times New Roman','Helvetica','Verdana'];
var placeholder = '<?php echo $text_summernote_placeholder; ?>';

$(document).ready(function(){
	$('.textarea-summernote').each(function(){
		$(this).summernote({
			blank: '', emptyPara: '', lang: lang, height:'180px', toolbar: toolbar,fontNames: fontNames, placeholder: placeholder,
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
});

$(document).delegate('.load-tpl-block', 'change', function() {
	var template_id = $(this).val();
	if (!template_id) {return;}
	var add_id = $(this).attr('data-add-id');
	var add_status = $('#tpl-add-'+add_id).prop('checked') ? 1 : 0;
 	var selected = $('#textarea-'+add_id).summernote('createRange');
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
						$('#textarea-'+add_id).summernote('code', json['tpl']);
					} else {
						$('#textarea-'+add_id).summernote('editor.pasteHTML', json['tpl']);
					}
				}
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

function saveBlock(target, add_id) {
	var data = {};
	data['template'] = {};

	var name = $('#tpl-name-'+ add_id).val();
	if (name == '') {
		toastr.error('<?php echo $text_error_name_block; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	}
	var template = $('#textarea-trigger').val();
	if (template == '' || template == '<p><br></p>') {
		toastr.error('<?php echo $text_error_template_block; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	}
	data['template']['name'] = name;
	data['template']['target'] = target;
	data['template']['description'] = '';
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
				toastr.options.timeOut = 3000;
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				$('select[class*=\'load-tpl-block\']').each(function(){
					$(this).append('<option value="'+json['template_id']+'">' + json['template_id'] + ' - ' + name+'</option>');
				});
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

//--></script>

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
	if (!template_id) {
		toastr.error('<?php echo $error_restore_no_template; ?>', '<?php echo $text_alert_error; ?>');
		return false;
	} else {
		$('#restore').val(1);
		$('#form-restore').submit();
	}
});

function savePage() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	$('input[name="editor_mode"]:first').trigger('click');

	var pageid = '<?php echo $pageid; ?>';
	var str = '';

	if (pageid) { str = '&pageid='+pageid }
	else { str = '&pageid=0'; }

	parcing();

	var data = $('.setdata').serialize();

	if (!data.length) { return false; }

	var url = 'index.php?route=sale/ompro/savePage&<?php echo $strtoken; ?>' + str;

	$.ajax({
		url: url,
		type: 'post',
		dataType: 'json',
		data: data,
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['success'] && json['pageid']) {
				if (pageid == json['pageid']) {
					dpStart();
					multiselectStart();
					iCheckStart();
					joystickStart();
					xEditableStart();
					controlCheckedInput();
					prepareBatch();
					$('[data-mask]').inputmask();
				}
				toastr.options.timeOut = 3000;
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
			}

			if ($('#button_save').hasClass('btn-danger')) {
				$('#button_save').removeClass('btn-danger').addClass('btn-primary');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).delegate('.form', 'change', function() {
	$('#form-editor').trigger('change');
});

$('#tab_select_orders input[type^=\'checkbox\']').on('ifChanged', function(event) {
	$('#form-editor').trigger('change');
});

$(document).ready(function(){
	toastr.options.timeOut = 3000;

	var error = '<?php echo $error_warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	$(document).delegate('input[name="editor_mode"]', 'change', function() {
		hideBtnIcons();
		Pace.restart();
		var mode = $(this).val();
		if (mode == 'preview') {
			$(this).closest('.btn-group').find('>.btn:not(.doc_link)').attr('class', 'btn btn-default');
			$(this).parent().removeClass('btn-default').addClass('btn-success');
		} else {
			$(this).closest('.btn-group').find('>.btn:not(.doc_link)').attr('class', 'btn btn-default');
			$(this).parent().removeClass('btn-default').addClass('btn-danger');
		}

		if (mode == 'preview') {
			preview();
		}
		else if (mode == 'btn') {
			editBoxToosElem();
		}
		else if (mode == 'btngroup') {
			sortBtnGroupElem();
		}
		else if (mode == 'blocks') {
			editBlocks();
		}
		else if (mode == 'sortTools') {
			sortTools();
		} else if (mode == 'sortColElem') {
			sortColElem();
		}

		$('#elements-preview').html('<p><?php echo $text_preview_el_placeholder; ?></p>');
	});

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

	var pageid = '<?php echo $pageid; ?>';

	if (pageid > 0) {
		getContent('&constructor=true&pageid=<?php echo $pageid; ?>');
	} else {
		multiselectStart();
		iCheckStart();
		formatSortableStart();
		startValidationAdd();
		tooltipRefresh();
	}
});

//--></script>

<script type="text/javascript"><!--

function calloutWarning(text = '') {
	text = text == '' ? 'Кликните выделенный элемент или кнопку редактировния эелемента: здесь откроется форма редактирования.' : text;
	html = '<div class="alert callout callout-default">';
	html += '<p><i class="icon fa fa-ban"></i>'+text+'</p>';
	html += '</div>';
	return html;
}

//--></script>

<!-- forms -->
<script type="text/javascript"><!--

function formOrderTable(target_id, form_id) {
	var ordertable = $('#'+target_id);

	if ($('.control-sidebar #'+form_id).length) {
		$('.form-open').removeClass('form-open');
		initControllSidebar(); return false;
	}

	$('.form-open').removeClass('form-open');

	var html = '';

	if (ordertable.length) {
		var ordertpl = ordertable.attr('data-orderTPL');

		html += '<form class="form ordertable-edit-form" target-id="'+target_id+'" id="'+form_id+'">';
		html += '<div class="box box-success box-solid">';
		html += '<div class="box-header with-border">';
		html += ' <h3 class="box-title">ТАБЛИЦА ЗАКАЗОВ</h3>';
		html += ' <div class="box-tools pull-right">';
		html += '	 <button type="button" class="btn btn-box-tool pull-right" data-toggle="tooltip" title="Закрыть форму" onclick="$(this).closest(\'.form\').remove(); initControllSidebar(); tooltipRefresh();"><i class="fa fa-times"></i></button>';
		html += ' </div>';
		html += '</div>';
		html += '<div class="box-body bg-success">';

		html += '<div class="form-group">';
		html += ' <div class="input-group input-group-sm">';
		html += '  <div class="input-group-addon" data-toggle="tooltip" title="Шаблон таблицы заказов" >TPL:</div>';
		html += '  <select class="target-ordertpl form-control">';
		<?php foreach ($ordertpl_list as $tpl) { ?>
			var tpl_id = '<?php echo $tpl['template_id']; ?>';
			var tpl_code = '<?php echo isset($tpl['code']) && $tpl['code'] ? $tpl['code'] : $tpl['template_id']; ?>';
			var tpl_name = '<?php echo $tpl['name']; ?>';
			if (ordertpl == tpl_code) {
				html += '  <option value="'+tpl_code+'" selected="selected">'+tpl_id+' - '+tpl_name+'</option>';
			} else {
				html += '  <option value="'+tpl_code+'">'+tpl_id+' - '+tpl_name+'</option>';
			}
		<?php } ?>
		html += '  </select>';
		html += '  <span class="input-group-addon" data-toggle="tooltip" title="После изменения шаблона будет автоматически включён режим просмотра!"><i class="fa fa-exclamation-triangle text-red"></i></span>';
		html += ' </div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</form>';

		ordertable.addClass('form-open');
	}
	return html;
}

function formOrderTableChange() {
	$(document).delegate('.ordertable-edit-form', 'change', function(e) {
		var form = $(this); var target_id = $(this).attr('target-id');

		if ($('#'+target_id).length) {
			var form_id = 'form_'+target_id;
			var ordertpl = form.find('.target-ordertpl').val();
			if (ordertpl) {
				$.ajax({
					url: 'index.php?route=sale/ompro/getOrdersTable&<?php echo $strtoken; ?>&tpl_code=' + encodeURIComponent(ordertpl) + '&limit=5&order=ASC',
					dataType: 'json',
					beforeSend: function() {
						$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
					},
					success: function(json) {
						$('.text-loading').remove();
						if (json['error'] || !json['content']) {
							modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>Шаблон не найден!</p>', '');
							return false;
						} else if (json['content']) {
							$('#'+target_id).replaceWith(json['content']);

							dpStart();
							multiselectStart();
							formatSortableStart();
							startValidationAdd();

							iCheckStart();
							joystickStart();
							xEditableStart();
							controlCheckedInput();
							prepareBatch();

							$('input[name="editor_mode"]:first').parent().trigger('click');
							tooltipRefresh();
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}
	});
}

function formBtn(target_id, form_id) {
	var btn = $('#'+target_id);

	if ($('.control-sidebar #'+form_id).length && btn.hasClass('form-open') ) {
		btn.removeClass('form-open');
		initControllSidebar(); return false;
	}

	$('.form-open').removeClass('form-open');

	var html = '';

	if (btn.length) {
		classList = btn.attr('class').split(/\s+/);

		html += '<form class="form btn-edit-form" target-id="'+target_id+'" id="'+form_id+'">';
		html += '<div class="box box-success box-solid">';
		html += ' <div class="box-header with-border">';
		html += ' <h3 class="box-title">РЕДАКТИРОВАНИЕ КНОПКИ</h3>';
		html += '  <div class="box-tools pull-right">';
		html += '	 <button type="button" class="btn btn-box-tool pull-right" data-toggle="tooltip" title="Закрыть форму" onclick="$(this).closest(\'.form\').remove(); initControllSidebar(); tooltipRefresh();"><i class="fa fa-times"></i></button>';
		html += '  </div>';
		html += '</div>';
		html += ' <div class="box-body bg-success">';

		var size_disabled = ''; var toggle_buttons = false;
		var btn_group = null; var btn_group_id = '';

		if (btn.parent().hasClass('btn-group') || btn.parent().hasClass('btn-group-vertical')) {
			size_disabled = 'disabled';
			btn_group = btn.parent();
			if (btn_group.length) {

				btn_group_id = btn_group.attr('id');
				if (!btn_group_id) {
					btn_group_id = genPass('btngr_', 5);
					btn_group.attr('id', btn_group_id);
				}

				groupClassList = btn_group.attr('class').split(/\s+/);
				var size_name = genPass('n_', 4);
				var add_btn_group_title = '';
				if (btn_group.attr('data-toggle') && (btn_group.attr('data-toggle') == 'button' || btn_group.attr('data-toggle') == 'buttons')) {
					var toggle_buttons = true; add_btn_group_title = ' - переключателей';
				}

				html += '<div class="box box-success">';
				html += ' <div class="box-header with-border">';
				html += '  <h3 class="box-title">Группа кнопок'+add_btn_group_title+'</h3>';
				html += '  <div class="box-tools pull-right">';
				html += '	 <button type="button" class="btn btn-box-tool pull-right text-red" data-toggle="tooltip" title="Удалить группу"  onclick="removeElemByID(\''+btn_group_id+'\');"><i class="fa fa-trash"></i></button>';
				html += '  </div>';
				html += ' </div>';

				html += ' <div class="box-body">';
				html += ' <p>Размер</p>';

				html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';
				var sizeList = {}; sizeList['mini'] = 'btn-group-xs'; sizeList['small'] = 'btn-group-sm'; sizeList['middle'] = 'btn-group-md'; sizeList['large'] = 'btn-group-lg';
				$.each(sizeList, function (index, size) {
					if (groupClassList.in_array(size)) {
						html += ' <label class="btn btn-primary active" data-toggle="tooltip" title="'+index+'"><input type="radio" class="btn-group-size" name="'+size_name+'" value="'+size+'" checked="checked"/>'+index+'</label>';
					} else {
						html += ' <label class="btn btn-primary" data-toggle="tooltip" title="'+index+'"><input type="radio" class="btn-group-size" name="'+size_name+'" value="'+size+'"/>'+index+'</label>';
					}
				});
				html += '</div>';

				var pos_name = genPass('n_', 4);
				html += ' <p></p>';
				html += ' <p>Расположение</p>';
				html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';

				if (groupClassList.in_array('btn-group') && !groupClassList.in_array('btn-group-justified') && !groupClassList.in_array('btn-group-vertical')) {
					html += ' <label class="btn btn-default active" data-toggle="tooltip" title="горизонтально"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group" checked="checked"/><i class="fa fa-long-arrow-right"></i></label>';
				} else {
					html += ' <label class="btn btn-default" data-toggle="tooltip" title="горизонтально"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group"/><i class="fa fa-long-arrow-right"></i></label>';
				}

				if (groupClassList.in_array('btn-group') && groupClassList.in_array('btn-group-justified')) {
					html += ' <label class="btn btn-default active" data-toggle="tooltip" title="растянуть по ширине"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group btn-group-justified" checked="checked"/><i class="fa fa-arrows-h"></i></label>';
				} else {
					html += ' <label class="btn btn-default" data-toggle="tooltip" title="растянуть по ширине"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group btn-group-justified"/><i class="fa fa-arrows-h"></i></label>';
				}

				if (groupClassList.in_array('btn-group-vertical')) {
					html += ' <label class="btn btn-default active" data-toggle="tooltip" title="вертикально"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group-vertical" checked="checked"/><i class="fa fa-arrows-v"></i></label>';
				} else {
					html += ' <label class="btn btn-default" data-toggle="tooltip" title="вертикально"><input type="radio" class="btn-group-pos" name="'+pos_name+'" value="btn-group-vertical"/><i class="fa fa-arrows-v"></i></label>';
				}
				html += '</div>';

				html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';
				if (groupClassList.in_array('btn-block')) {
					html += ' <label class="btn btn-default active" data-toggle="tooltip" title="в виде блока"><input type="checkbox" class="btn-group-block" value="btn-block" checked="checked"/><i class="fa fa-square"></i></label>';
				} else {
					html += ' <label class="btn btn-default" data-toggle="tooltip" title="в виде блока"><input type="checkbox" class="btn-group-block" value="btn-block"/><i class="fa fa-square"></i></label>';
				}
				html += '</div>';
				html += '</div>';

				html += '</div>';

				html += ' <p></p>';
			}
		}

		html += '<div class="box box-success">';

		html += ' <div class="box-body">';

		html += '<div class="form-group valid-block">';
		html += '<div class="input-group input-group-sm"><div class="input-group-addon" data-toggle="tooltip" title="ID кнопки">ID:</div><input type="text" value="'+target_id+'" class="btn-id form-control field-input validid"/><div class="input-group-btn"><label class="btn btn-default" data-toggle="tooltip" title="Очистить" onclick="$(this).closest(\'.input-group\').find(\'.btn-id\').val(\'\').trigger(\'change\');"><i class="fa fa-times-circle"></i></label></div><div class="input-group-btn"><a class="btn btn-default remove-btn text-red" target-id="'+target_id+'" data-toggle="tooltip" title="Удалить кнопку" ><i class="fa fa-trash"></i></a></div></div>';
		html += '</div>';

		var btn_action = btn.attr('data-btnaction');
		btn_action = btn_action ? btn_action : '';
		var select_disabled = '';

		if (btn_action) {
			if (btn_action == 'clear_filter_this' || btn_action == 'print_orders' || btn_action == 'print_orders_table' || btn_action == 'print_products_table' || btn_action == 'excel_orders' || btn_action == 'excel_orders_products') { select_disabled = 'disabled'; }

			html += '<div class="form-group valid-block">';
			html += ' <div class="input-group input-group-sm">';
			html += '  <div class="input-group-addon" data-toggle="tooltip" title="Назначить действие кнопки" >ACT:</div>';
			html += '  <select '+select_disabled+' class="btn-target-action form-control">';
			html += '  <option value=""></option>';
			<?php foreach ($btn_actions as $action) { ?>
			  <?php if (!$action['template_id']) { ?>
				var action = '<?php echo $action['action']; ?>';
				var action_title = '<?php echo $action['title']; ?>';

				if (action == 'clear_filter_this' && !btn.parent().hasAttr('filterBtnClearGroup')) {
				} else if (btn_action == action) {
					html += '  <option value="'+action+'" selected="selected">'+action_title+'</option>';
				} else {
					html += '  <option value="'+action+'">'+action_title+'</option>';
				}
			  <?php } ?>
			<?php } ?>
			html += '  </select>';

			var addon_display = 'style="display: none;"';
			var slideid_display = 'style="display: none;"'; var slidecl_display = 'style="display: none;"';
			var action_target = btn.attr('data-target'); action_target = action_target ? action_target : '';

			if (btn_action == 'slide_on_id') {
				addon_display = ''; slideid_display = '';
			} else if (btn_action == 'slide_on_class') {
				addon_display = ''; slidecl_display = '';
			}

			html += '  <div class="input-group-addon target-addon" '+addon_display+' data-toggle="tooltip" title="Целевое значение" >=</div>';
			html += '  <input type="text" value="'+action_target+'" class="target-action-id form-control field-input validtargetid" '+slideid_display+' />';
			html += '  <input type="text" value="'+action_target+'" class="target-action-class form-control field-input validtargetclass" '+slidecl_display+' />';
			html += ' </div>';
			html += '</div>';
		}

		if (toggle_buttons) {
			var checkbox_selected = ''; var radio_selected = ''; var btn_input_name = ''; var btn_input_value = '';
			if (btn.find('input').length) {
				var btn_input = btn.find('input');
				var input_type = btn_input.attr('type') ? btn_input.attr('type') : '';
				if (input_type == 'checkbox') { checkbox_selected = 'selected="selected"'; }
				else if (input_type == 'radio') { radio_selected = 'selected="selected"'; }
				var btn_input_name = btn_input.attr('name') ? btn_input.attr('name') : '';
				var btn_input_value = btn_input.attr('value') ? btn_input.attr('value') : '';
			}
			html += '<div class="form-group">';
			html += ' <div class="input-group input-group-sm">';
			html += '  <div class="input-group-addon" data-toggle="tooltip" title="Тип поля. Добавление поля для кнопки позволяет видеть активное (пассивное) положение кнопки и использовать нужный тип переключения - чекбоксы или радио кнопки.">on/off:</div>';
			html += '  <select class="btn-input-type form-control" onchange="this.value ? $(\'#btn_input_attr\').show() : $(\'#btn_input_attr\').hide();" >';
			html += '  <option value="">- без поля -</option>';
			html += '  <option value="checkbox" '+checkbox_selected+'>checkbox</option>';
			html += '  <option value="radio" '+radio_selected+'>radio</option>';
			html += '  </select>';
			html += ' </div>';
			html += '</div>';

			var btn_input_attr_display = checkbox_selected || radio_selected ? '' : 'style="display: none;"';

			html += '<div class="form-group valid-block" id="btn_input_attr" '+btn_input_attr_display+'>';
			html += ' <div class="input-group input-group-sm"><div class="input-group-addon" data-toggle="tooltip" title="Название поля: используется функциями обработчиками. Редактируйте, только если понимаете, что делаете!">Name:</div><input type="text" value="'+btn_input_name+'" class="btn-input-name form-control field-input validclass"/>';
			html += '<div class="input-group-addon" data-toggle="tooltip" title="Значение поля: используется функциями обработчиками. Редактируйте, только если понимаете, что делаете!">Value:</div><input type="text" value="'+btn_input_value+'" class="btn-input-value form-control field-input digit" />';
			html += ' </div>';
			html += '</div>';
		}

		var input_style_name = genPass('n_', 5);
		html += ' <p>Стиль</p>';
		html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';
		var btn_styles = 'btn-default btn-initial btn-primary btn-info btn-success btn-warning btn-danger btn-link bg-maroon bg-purple bg-navy bg-orange bg-olive btn-box-tool';
		styleList = btn_styles.split(/\s+/);
		$.each(styleList, function (index, style) {
			if (classList.in_array(style)) {
				if (style === 'btn-box-tool') {
					size_disabled = 'disabled';
				}
				html += ' <label class="btn active '+style+'" data-toggle="tooltip" title="'+style+'"><input type="radio" class="btn-style" name="'+input_style_name+'" value="btn '+style+'" checked="checked"/><i class="fa fa-info"></i></label>';
			} else {
				html += ' <label class="btn '+style+'" data-toggle="tooltip" title="'+style+'"><input type="radio" class="btn-style" name="'+input_style_name+'" value="btn '+style+'"><i class="fa fa-info"></i></label>';
			}
		});
		html += '</div>';

		if (!btn.parent().hasAttr('filterBtnClearGroup')) {
			html += ' <p></p>';
			html += ' <p>Дополнительный стиль</p>';
			html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';

			var active_flat = ''; var check_flat = ''; var active_block = ''; var check_block = '';
			$.each(classList, function (i, cl) {
				var clas = cl.trim();
				if (clas === 'btn-flat') {
					active_flat = 'active'; check_flat = 'checked="checked"';
				} else if (clas === 'btn-block') {
					active_block = 'active'; check_block = 'checked="checked"';
				}
			});

			html += ' <label class="btn btn-default '+active_flat+'" data-toggle="tooltip" title="Прямые углы (btn-flat)"><input type="checkbox" class="btn-add-style" value="btn-flat" '+check_flat+'/><i class="fa fa-square-o"></i></label>';
			html += ' <label class="btn btn-default '+active_block+'" data-toggle="tooltip" title="В виде блока (btn-block)"><input type="checkbox" class="btn-add-style" value="btn-block" '+check_block+'/><i class="fa fa-square"></i></label>';
			html += '</div>';
		}

		var sizeList = {}; sizeList['mini'] = 'btn-xs'; sizeList['small'] = 'btn-sm'; sizeList['middle'] = 'btn-md'; sizeList['large'] = 'btn-lg';
		btn_size = '';
		$.each(sizeList, function (ii, cl) {
			if (classList.in_array(cl.trim())) { btn_size = cl.trim(); }
		});
		btn_size = btn_size ? btn_size : 'btn-md';

		if (!btn.parent().hasAttr('filterBtnClearGroup') && !btn.parent('.input-group-btn').length && !btn.parent('.btn-group').length && !btn.parent('.btn-group-vertical').length) {
			var input_size_name = genPass('n_', 4);
			html += ' <p></p>';
			html += ' <p>Размер</p>';
			html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';

			$.each(sizeList, function (index, size) {
				if (btn_size == size) {
					html += ' <label class="btn btn-primary active '+size_disabled+'" data-toggle="tooltip" title="'+index+'"><input type="radio" class="btn-size" name="'+input_size_name+'" value="'+size+'" checked="checked"/>'+index+'</label>';
				} else {
					html += ' <label class="btn btn-primary '+size_disabled+'" data-toggle="tooltip" title="'+index+'"><input type="radio" class="btn-size" name="'+input_size_name+'" value="'+size+'"/>'+index+'</label>';
				}
			});
			html += '</div>';
		} else if (btn.parent().hasAttr('filterBtnClearGroup')) {
			html += '<input type="hidden" value="'+btn_size+'" class="btn-size" />';
		}

		html += ' <p></p>';
		html += ' <p>Дополнительный класс(ы)</p>';
		var excludeBtnClassList = 'btn btn-default btn-initial btn-primary btn-info btn-success btn-warning btn-danger btn-link btn-box-tool bg-maroon bg-purple bg-navy bg-orange bg-olive btn-xs btn-sm btn-md btn-lg btn-flat btn-block active'.split(/\s+/);
		var add_class = '';
		$.each(classList, function (ii, cl) {
			if (!excludeBtnClassList.in_array(cl.trim())) {
				add_class += cl.trim()+' ';
			}
		});

		html += '<div class="form-group valid-block">';
		html += '<div class="input-group input-group-sm"><input type="text" value="'+add_class+'" class="btn-add-class form-control field-input validclass"/><div class="input-group-btn"><label class="btn btn-default" data-toggle="tooltip" title="Очистить" onclick="$(this).closest(\'.input-group\').find(\'.btn-add-class\').val(\'\').trigger(\'change\');"><i class="fa fa-times-circle"></i></label></div></div>';
		html += '</div>';

		var active_tooltip = ''; var check_tooltip = '';
		var toggle = btn.attr('data-toggle');
		if (toggle && toggle == 'dropdown') {
		}
		else if (toggle && toggle == 'tooltip') {
			var btn_title = btn.attr('data-original-title');
			btn.attr('title', btn_title);
			active_tooltip = 'active'; check_tooltip = 'checked="checked"';
		} else {
			var btn_title = btn.attr('title');
		}

		btn_title = btn_title ? btn_title : '';

		html += ' <p></p>';
		html += ' <p>Название кнопки (title)</p>';
		html += '<div class="input-group input-group-sm">';
		if (toggle !== 'dropdown') {
			html += '<div class="input-group-btn" data-toggle="button"><label class="btn btn-default '+active_tooltip+'" data-toggle="tooltip" title="Всплывающая подсказка (Вкл / Выкл). После изменения названия переключите галочку дважды!"><input type="checkbox" class="btn-tooltip" value="1" '+check_tooltip+'/></label></div>';
		}
		html += '<input type="text" value="'+btn_title+'" class="btn-title form-control"/><div class="input-group-btn"><label class="btn btn-default" data-toggle="tooltip" title="Очистить" onclick="$(this).closest(\'.input-group\').find(\'.btn-title\').val(\'\').trigger(\'change\');"><i class="fa fa-times-circle"></i></label></div>';
		html += '</div>';

		var icon_pos_name = genPass('n_', 5);
		var btn_html = btn.html();
		var btn_input_html = btn.find('input').length ? btn.find('input')[0].outerHTML : '';
		var btn_icon_html = btn.find('.fa').length ? btn.find('.fa')[0].outerHTML : '';
		var btn_text = btn.text();
		var btn_html_tmp = delAllSpaces(btn_html.replace(btn_input_html,''));
		var active_pos_left = ''; var check_pos_left = ''; var active_pos_right = ''; var check_pos_right = '';
		if (btn_html_tmp === (delAllSpaces(btn_icon_html + btn_text))) {
			active_pos_left = 'active'; check_pos_left = 'checked="checked"';
		} else {
			active_pos_right = 'active'; check_pos_right = 'checked="checked"';
		}

		html += ' <p></p>';
		html += ' <p>Текст кнопки</p>';
		html += '<div class="input-group input-group-sm"><input type="text" value="'+btn_text.trim()+'" class="btn-text form-control"/><div class="input-group-btn"><label class="btn btn-default" data-toggle="tooltip" title="Очистить" onclick="$(this).closest(\'.input-group\').find(\'.btn-text\').val(\'\').trigger(\'change\');"><i class="fa fa-times-circle"></i></label></div></div>';

		var load_icon = btn_icon_html ? btn_icon_html : '<i class="fa"></i>';

		html += ' <p></p>';
		html += ' <p>Иконка</p>';
		html += '<div class="btn-group btn-group-xs btn-group-justified" data-toggle="buttons">';
		html += '<div class="btn-group btn-group-xs" data-toggle="button"><button type="button" class="btn btn-default load-icon" data-target-id="icons-'+target_id+'" data-toggle="tooltip" title="Выбрать иконку">'+load_icon+'</button></div>';
		html += ' <label class="btn btn-primary '+active_pos_left+'" data-toggle="tooltip" title="слева"><input type="radio" class="btn-icon-pos" value="left" name="'+icon_pos_name+'" '+check_pos_left+'/><i class="fa fa-long-arrow-left"></i></label>';
		html += ' <label class="btn btn-primary '+active_pos_right+'" data-toggle="tooltip" title="справа"><input type="radio" class="btn-icon-pos" value="right" name="'+icon_pos_name+'" '+check_pos_right+'/><i class="fa fa-long-arrow-right"></i></label>';
		html += ' </div>';
		html += '<div id="icons-'+target_id+'" class="icons-block" style="display: none;"></div>';
		html += ' <p></p>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</div>';
		html += '</form>';

		btn.addClass('form-open');
	}
	return html;
}


$(document).delegate('.btn.check-control', 'change', function(e) {
	if ($(this).hasClass('active')) {
		$(this).removeClass('text-red').addClass('text-green');
	} else {
		$(this).removeClass('text-green').addClass('text-red');
	}
});

$('.btn.check-control').trigger('change');

$(document).delegate('.form', 'keypress', function(e) {
    if (e.keyCode == 13) {
		$(this).find('input:focus').blur();
		return false;
	}
});

//--></script>

<!--Elements -->
<script type="text/javascript"><!--

function getElements(source, template_id = 0, qty_id = false) {
	var template = template_id ? '&template_id=' + encodeURIComponent(template_id) : '';
	var quantity = qty_id ? $('#'+qty_id).val() : 0;
	var qty = quantity > 0 ? '&quantity=' + encodeURIComponent(quantity) : '';

	var url = 'index.php?route=sale/ompro/getElements&<?php echo $strtoken; ?>&source=' + encodeURIComponent(source) + template + qty;

	$.ajax({
		url: url,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="wait-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Loading...</span></div>');
		},
		success: function(json) {
			$('.wait-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}

			if (json['content']) {
				$('#elements-preview').html(json['content']);
				var editor_mode = $('input[name="editor_mode"]:checked').val();
				if (editor_mode == 'blocks') {
					editBlocks();
					iCheckStart();
					joystickStart();
					widgetsStart();
				}
				if (editor_mode == 'btn') {
					editBoxToosElem();
				}
				if (editor_mode == 'btngroup') {
					sortBtnGroupElem();
				}
				if (editor_mode == 'sortColElem') {
					sortColElem();
				}
				if (editor_mode == 'sortTools') {
					sortTools();
				}
				multiselectStart();
				dpStart();
				controlCheckedInput();
				$('[data-mask]').inputmask();
				tooltipRefresh('#box-elements');
				$(window).trigger('load');

			} else {
				$('#elements-preview').html('<p><?php echo $text_no_results; ?></p>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}

$(document).delegate('.load-element', 'change', function() {
	if ($(this).val()) {
		var val = $(this).val();
		var source = $(this).attr('data-load-source');
		source = val == 'pagination' || val == 'order_map' ? val : source;
		var tpl_id = val !== '*' ? val : '';
		getElements(source, tpl_id);
	} else {
		$('#elements-preview').html('<p><?php echo $text_preview_el_placeholder; ?></p>');
	}
});

$(document).delegate('.load-element-repeat', 'click', function() {
	$(this).closest('.btn-group').find('.load-element').trigger('change');
});

//--></script>

<!-- FORMATS -->
<script type="text/javascript"><!--

function formatSortableStart() {
	var dropElSelector = '.tbody-sortable';
	var param = {}; param['group'] = {};
	param['group']['name'] = 'table-rows';
	param['handle'] = '.handle';
	param['draggable'] = 'tr';
	param['ghostClass'] = 'sortable-elem';

	sortableStartGroup(dropElSelector, param);
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
		html += '<td class="text-left">'+ order_code +'<input type="hidden" name="setting[order_formats]['+ order_code +'][code]" value="'+ order_code +'" class="setdata order-code" /></td>';
		html += '<td class="text-left">';
		html += ' <div class="input-group">';
		html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += '  <select name="setting[order_formats]['+ order_code +'][type]" data-format-code="'+ order_code +'" target-order-format="order-format-'+ format_row + '" class="select-order-format-type setdata form-control">';
	  <?php foreach ($order_format_list as $type) { ?>
		html += '<option value="<?php echo $type['type']; ?>"><?php echo $type['name']; ?></option>';
	  <?php } ?>
		html += '  </select>';
		html += ' </div>';
		html += '</td>';
		html += '<td class="text-left" id="order-format-'+ format_row + '"></td>';
		html += '<td class="text-left"><button type="button" onclick="$(\'#order-format-row-'+ format_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
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
		html += ' <select name="setting[order_formats]['+ order_code +'][process_method]" class="setdata form-control">';
		<?php foreach ($order_format_method_list as $method) { ?>
		html += ' <option value="<?php echo $method['process_method']; ?>"><?php echo $method['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);

	} else if (type == 'date') {
		html = '<div class="input-group valid-block">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>';
		html += ' <input type="text" name="setting[order_formats]['+ order_code +'][date_format]" value="d.m.Y" placeholder="d.m.Y H:i:s" class="setdata field-input mustbe form-control" />';
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
		html += '<td class="text-left">'+ product_code +'<input type="hidden" name="setting[product_formats]['+ product_code +'][code]" value="'+ product_code +'" class="setdata product-code" /></td>';
		html += '<td class="text-left">';
		html += ' <div class="input-group">';
		html += '  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_select_format_type_info; ?>"><i class="fa fa-info text-blue"></i></span>';
		html += '  <select name="setting[product_formats]['+ product_code +'][type]" data-format-code="'+ product_code +'" target-product-format="product-date-'+ format_row + '" class="select-product-format-type setdata form-control">';
	  <?php foreach ($product_format_list as $type) { ?>
		html += '<option value="<?php echo $type['type']; ?>"><?php echo $type['name']; ?></option>';
	  <?php } ?>
		html += '  </select>';
		html += ' </div>';
		html += '</td>';
		html += '<td class="text-left" id="product-date-'+ format_row + '"></td>';
		html += '<td class="text-left"><button type="button" onclick="$(\'#product-format-row-'+ format_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
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
		html += ' <select name="setting[product_formats]['+ product_code +'][process_method]" class="setdata form-control">';
		<?php foreach ($product_format_method_list as $method) { ?>
		html += ' <option value="<?php echo $method['process_method']; ?>"><?php echo $method['name']; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += '</div>';

		$('#'+target_id).html(html);

	} else if (type == 'date') {
		html = '<div class="input-group valid-block">';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_format_date_info; ?>"><i class="fa fa-info  text-blue"></i></span>';
		html += ' <input type="text" name="setting[product_formats]['+ product_code +'][date_format]" value="d.m.Y" placeholder="d.m.Y H:i:s" class="setdata field-input mustbe form-control" />';
		html += '</div>';

		$('#'+target_id).html(html);
	} else {
		$('#'+target_id).html('');
	}

	tooltipRefresh('#product-format-table');
});

//--></script>

</div>

<?php echo $footer; ?>