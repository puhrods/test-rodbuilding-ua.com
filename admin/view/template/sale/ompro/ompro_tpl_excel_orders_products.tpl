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

<script type="text/javascript" src="view/javascript/ompro/jscolor.js"></script>
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
            <li><a href="#tab-total" data-toggle="tab"><?php echo $tab_template_total; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
          </ul>

		  <div class="tab-content">
            <div class="tab-pane" id="tab-columns">
			  <div class="panel-group">
				  <div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_excel_orders_products.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_excel_vars_info; ?></p>
				  </div>
				  <div class="panel panel-default">
					<div class="panel-body">
						<div class="row">
						  <label class="col-sm-2 control-label"><?php echo $entry_excel_preview; ?></label>
						  <div class="col-sm-10 valid-block">
							<div class="input-group">
							  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_select_multi_orders; ?>">&nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;</span>
							  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_print_select_last_order_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
							  <input type="text"  value="<?php echo $last_order; ?>" id="test-order-id" class="field-input digitovercoma form-control" placeholder="<?php echo $entry_select_last_order_placeholder; ?>"/>
							  <span class="input-group-btn">
								<a id="export-preview" data-toggle="tooltip" title="" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;&nbsp;<?php echo $button_excel_preview; ?></a>
							  </span>
							</div>
						  </div>
					  </div>
					</div>
				  </div>

				  <div class="panel panel-default">
					<div class="panel-body">
					  <div class="row">
						<label class="col-sm-2 control-label"><?php echo $entry_col_widths; ?></label>
						<div class="col-sm-4">
							<div class="input-group valid-block">
							   <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_col_widths_info; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
							  <input type="text" name="template[col_widths]" value="<?php echo isset($template['col_widths']) ? $template['col_widths'] : ''; ?>" class="field-input digitovercomanotmustbe form-control" placeholder="20,40,50,40"/>
							</div>
						</div>
						<div class="col-sm-2">
						  <div class="radio">
							<label><?php echo $entry_product_group_by; ?> &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $entry_product_group_by_info; ?>"></i></label>&nbsp;&nbsp;
						  </div>
						</div>
						<div class="col-sm-4">
							<select name="template[product_group_by]" class="form-control">
							<?php foreach ($product_group_by_list as $group_by_value => $group_by_text) { ?>
								<option value="<?php echo $group_by_value; ?>" <?php if (isset($template['product_group_by']) && $template['product_group_by'] == $group_by_value) { ?> selected="selected"<?php } ?>><?php echo $group_by_text; ?></option>
							<?php } ?>
							</select>
						</div>
					  </div>
					</div>
				  </div>

				  <?php foreach ($row_list as $key => $text) { ?>

				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#set-<?php echo $key; ?>-rows"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text; ?></a></div>
					<div id="set-<?php echo $key; ?>-rows" class="panel-collapse collapse">
					  <div class="panel-body">

						<div id="<?php echo $key; ?>-row-group" class="panel-group">
							<?php if ($key == 'header' || $key == 'footer') { ?>
							  <div class="callout callout-default">
								<p><i class="fa fa-exclamation-triangle text-blue"></i>&nbsp; <?php echo $entry_excel_header_footer_info; ?></p>
							  </div>
							 <?php } ?>

						  <?php $i_row = 1; ?>
						  <?php if (!empty($template[$key])) { ?>
						  <?php foreach ($template[$key] as $row) { ?>

							<div class="panel panel-default <?php echo $key; ?>-row-panel">
							  <div class="panel-heading">
								<div class="valid-block">
								  <div class="input-group">
									<span class="input-group-addon">
										<i class="fa fa-arrows handle handle-row" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"></i>&nbsp;&nbsp; <a data-toggle="collapse" href="#set-<?php echo $key . $i_row; ?>"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_row . $i_row; ?></a>
									</span>
									<span class="input-group-addon"><?php echo $text_row_type; ?></span>
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_row_type_info; ?>"><i class="fa fa-info text-blue"></i></span>
									<select name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][type]" class="form-control" >
										<option value="title" <?php if (isset($row['type']) && $row['type'] == 'title') { ?> selected="selected"<?php } ?>><?php echo $text_row_type_title; ?></option>
										<option value="data" <?php if (isset($row['type']) && $row['type'] == 'data') { ?> selected="selected"<?php } ?>><?php echo $text_row_type_data; ?></option>
									</select>
									<span class="input-group-addon"><?php echo $text_row_height; ?></span>
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_row_height_info; ?>"><i class="fa fa-info text-blue"></i></span>
									<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][height]" value="<?php echo isset($row['height']) ? $row['height'] : 20; ?>" class="field-input numeric form-control" placeholder="20"/>
									<div class="input-group-btn">
										<a onclick="$(this).closest('.<?php echo $key; ?>-row-panel').remove(); tooltipRefresh();"  class="btn btn-danger" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>"><i class="fa fa-minus-circle"></i></a>
									</div>
								  </div>
								</div>
							  </div>

							  <div id="set-<?php echo $key . $i_row; ?>" class="panel-collapse collapse">
								<div class="panel-body">
								<div class="table-responsive">
								  <table class="table table-bordered table-striped <?php echo $key; ?>-col-table" id="<?php echo $key; ?>-col-table<?php echo $i_row; ?>">
									<thead>
										<tr class="">
											<th class="text-center" style="width: 1%;">#</th>
											<th style="min-width: 180px;"><?php echo $text_column_format; ?></th>
											<th style="min-width: 200px;"><?php echo $text_column_format_param; ?></th>
											<th style="min-width: 60px;"><i class="fa fa-clone"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_column_merge; ?>"></i></th>
											<th style="min-width: 185px; width: 185px;"><?php echo $text_column_align; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_column_align_info; ?>"></i></th>
											<th style="min-width: 400px;"><?php echo $text_column_value; ?>
											<?php if ($key == 'order' || $key == 'product') { ?>
											&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes('<?php echo $key; ?>');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a>
											<?php } ?>
											</th>
											<th style="width: "><?php echo $text_column_style; ?></th>
											<th style="width: 60px;"></th>
										</tr>
									</thead>
									<tbody class="tbody-sortable">
									  <?php $i_col = 1; ?>
									  <?php if (!empty($row['columns'])) { ?>
										<?php foreach ($row['columns'] as $column) { ?>
										<tr id="<?php echo $key; ?>-col-table-row<?php echo $i_row.'_'.$i_col; ?>" class="col-table-row">
											<td class="text-center handle handle-col" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i><br/><?php echo $i_col; ?></td>
											<td>
												<select name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][format]" class="select-col-format form-control" data-key="<?php echo $key; ?>" data-i_row="<?php echo $i_row; ?>" data-i_col="<?php echo $i_col; ?>">
												  <?php foreach ($col_format_list as $format => $format_name) { ?>
													<option value="<?php echo $format; ?>" <?php if (isset($column['format']) && $column['format'] == $format) { ?> selected="selected"<?php } ?>><?php echo $format_name; ?></option>
												  <?php } ?>
												</select>
											</td>
											<td id="format_add_<?php echo $key; ?>_<?php echo $i_row; ?>_<?php echo $i_col; ?>">
												<?php if (isset($column['format']) && $column['format'] == 'link') { ?>
												  <div class="input-group">
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_col_link_text; ?>"><i class="fa fa-text-width"></i></span>
													<textarea name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][text]" rows="1" class="htmlTextArea form-control"  style="min-width: 200px;"><?php echo isset($column['text']) ? $column['text'] : ''; ?></textarea>
												  </div>
												<?php } ?>
												<?php if (isset($column['format']) && $column['format'] == 'image') { ?>
												  <div class="input-group">
													  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_col_img_size; ?>"><i class="fa fa-image"></i></span>
													  <input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][img_width]" value="<?php echo isset($column['img_width']) ? $column['img_width'] : 50; ?>" class="field-input numericmustbe form-control" />
													  <span class="input-group-addon">x</span>
													  <input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][img_height]" value="<?php echo isset($column['img_height']) ? $column['img_height'] : 50; ?>" class="field-input numericmustbe form-control"  />
												  </div>
												<?php } ?>
											</td>
											<td>
												<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][merge]" value="<?php echo isset($column['merge']) ? $column['merge'] : 1; ?>" class="field-input numericmustbe form-control" placeholder="1" />
											</td>
											<td>
												<div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">
													<label class="btn btn-default <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'left') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_hor_left; ?>"><input type="radio" value="left" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][horizomtal]" <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'left') { ?>checked<?php } ?> /><i class="fa fa-align-left"></i></label>
													<label class="btn btn-default <?php if (!isset($column['alignment']['horizomtal']) || isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'center') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_hor_center; ?>"><input type="radio" value="center" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][horizomtal]" <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'center') { ?>checked<?php } ?> /><i class="fa fa-align-center"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'right') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_hor_right; ?>"><input type="radio" value="right" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][horizomtal]" <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'right') { ?>checked<?php } ?> /><i class="fa fa-align-right"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'justify') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_hor_justify; ?>"><input type="radio" value="justify" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][horizomtal]" <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'justify') { ?>checked<?php } ?> /><i class="fa fa-align-justify"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'distributed') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_hor_distributed; ?>"><input type="radio" value="distributed" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][horizomtal]" <?php if (isset($column['alignment']['horizomtal']) && $column['alignment']['horizomtal'] == 'distributed') { ?>checked<?php } ?> /><i class="fa fa-exchange"></i></label>
												</div>
												<div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">
													<label class="btn btn-default <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'top') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_ver_top; ?>"><input type="radio" value="top" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][vertical]" <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'top') { ?>checked<?php } ?> /><i class="fa fa-hourglass-start"></i></label>
													<label class="btn btn-default <?php if (!isset($column['alignment']['vertical']) || isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'center') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_ver_center; ?>"><input type="radio" value="center" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][vertical]" <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'center') { ?>checked<?php } ?> /><i class="fa fa-hourglass-o"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'bottom') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_ver_bottom; ?>"><input type="radio" value="bottom" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][vertical]" <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'bottom') { ?>checked<?php } ?> /><i class="fa fa-hourglass-end"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'justify') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_ver_justify; ?>"><input type="radio" value="justify" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][vertical]" <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'justify') { ?>checked<?php } ?> /><i class="fa fa-hourglass"></i></label>
													<label class="btn btn-default <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'distributed') { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_align_ver_distributed; ?>"><input type="radio" value="distributed" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][alignment][vertical]" <?php if (isset($column['alignment']['vertical']) && $column['alignment']['vertical'] == 'distributed') { ?>checked<?php } ?> /><i class="fa fa-hourglass-2"></i></label>
												</div>
											</td>
											<td>
												<textarea name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][value]" rows="1" class="htmlTextArea form-control"  style="min-width: 200px;"><?php echo isset($column['value']) ? $column['value'] : ''; ?></textarea>
											</td>
											<td id="style_<?php echo $key; ?>_<?php echo $i_row; ?>_<?php echo $i_col; ?>">
												<?php if (!empty($column['style'])) { ?>
												  <div class="input-group">
													<div class="input-group-btn"><a onclick="toggleStyle('remove','<?php echo $key; ?>','<?php echo $i_row; ?>','<?php echo $i_col; ?>');" class="btn btn-default" data-toggle="tooltip" title="<?php echo $btn_remove_style; ?>"><i class="fa fa-times-circle"></i></a></div>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font; ?>"><i class="fa fa-font"></i></span>
													<select name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][name]" class="form-control" style="min-width: 160px;">
													  <?php foreach ($font_list as $font) { ?>
														<option value="<?php echo $font; ?>" <?php if (isset($column['style']['font']['name']) && $column['style']['font']['name'] == $font) { ?> selected="selected"<?php } ?>><?php echo $font; ?></option>
													  <?php } ?>
													</select>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_size; ?>"><i class="fa fa-text-height"></i></span>
													<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][size]" value="<?php echo isset($column['style']['font']['size']) ? $column['style']['font']['size'] : 10; ?>" class="field-input numericmustbe form-control" placeholder="10" style="min-width: 40px;" />
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_color; ?>"><i class="fa fa-adjust"></i></span>
													<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][color][rgb]" value="<?php echo isset($column['style']['font']['color']) && isset($column['style']['font']['color']['rgb']) ? $column['style']['font']['color']['rgb'] : '000000'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />

													<div class="input-group-btn" data-toggle="buttons">
														<label class="btn btn-default <?php if (isset($column['style']['font']['bold']) && $column['style']['font']['bold'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_bold; ?>"><input type="checkbox" value="1" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][bold]" <?php if (isset($column['style']['font']['bold']) && $column['style']['font']['bold'] == 1) { ?> checked<?php } ?> /><i class="fa fa-bold"></i></label>
														<label class="btn btn-default <?php if (isset($column['style']['font']['italic']) && $column['style']['font']['italic'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_italic; ?>"><input type="checkbox" value="1" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][italic]" <?php if (isset($column['style']['font']['italic']) && $column['style']['font']['italic'] == 1) { ?>checked<?php } ?> /><i class="fa fa-italic"></i></label>
														<label class="btn btn-default <?php if (isset($column['style']['font']['underline']) && $column['style']['font']['underline'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_underline; ?>"><input type="checkbox" value="1" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][underline]" <?php if (isset($column['style']['font']['underline']) && $column['style']['font']['underline'] == 1) { ?>checked<?php } ?> /><i class="fa fa-underline"></i></label>
														<label class="btn btn-default <?php if (isset($column['style']['font']['strike']) && $column['style']['font']['strike'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_strike; ?>"><input type="checkbox" value="1" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][font][strike]" <?php if (isset($column['style']['font']['strike']) && $column['style']['font']['strike'] == 1) { ?>checked<?php } ?> /><i class="fa fa-strikethrough"></i></label>
													</div>

													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_fill; ?>"><i class="fa fa-circle"></i></span>
													<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][fill][color][rgb]" value="<?php echo isset($column['style']['fill']['color']) && isset($column['style']['fill']['color']['rgb']) ? $column['style']['fill']['color']['rgb'] : 'ffffff'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />
													<input type="hidden" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][fill][type]" value="solid" />

													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_style; ?>"><i class="fa fa-square-o"></i></span>
													<select name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][borders][allborders][style]" class="form-control" style="min-width: 80px;">
													  <?php foreach ($border_style_list as $style) { ?>
														<option value="<?php echo $style; ?>" <?php if (isset($column['style']['borders']['allborders']['style']) && $column['style']['borders']['allborders']['style'] == $style) { ?> selected="selected"<?php } ?>><?php echo $style; ?></option>
													  <?php } ?>
													</select>
													<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_color; ?>"><i class="fa fa-square"></i></span>
													<input type="text" name="template[<?php echo $key; ?>][<?php echo $i_row; ?>][columns][<?php echo $i_col; ?>][style][borders][allborders][color][rgb]" value="<?php echo isset($column['style']['borders']['allborders']['color']) && isset($column['style']['borders']['allborders']['color']['rgb']) ? $column['style']['borders']['allborders']['color']['rgb'] : '000000'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />
												  </div>
												<?php } else { ?>
												  <a onclick="toggleStyle('add','<?php echo $key; ?>','<?php echo $i_row; ?>','<?php echo $i_col; ?>');" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $btn_add_style; ?>"><i class="fa fa-plus-circle"></i></a>
												<?php } ?>
											</td>
											<td><a onclick="$(this).closest('.col-table-row').remove(); tooltipRefresh();"  class="btn btn-danger" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>"><i class="fa fa-minus-circle"></i></a></td>
										</tr>
										<?php $i_col++; ?>
										<?php } ?>
									  <?php } ?>
									</tbody>
									<tfoot>
										<tr class="active">
											<td></td>
											<td colspan="7"><a onclick="addRowColumn('<?php echo $key; ?>','<?php echo $i_row; ?>','<?php echo $i_col; ?>');" class="btn btn-primary add-column-row" data-toggle="tooltip" title="<?php echo $button_add_column; ?>"><i class="fa fa-plus"></i></a></td>
										</tr>
									</tfoot>
								  </table>
								</div>
							  </div>
							  </div>
							</div>

							<?php $i_row++; ?>
						  <?php } ?>
						  <?php } ?>
							<div class="panel panel-default add-row-panel">
								<div class="panel-body">
									<a onclick="addRow('<?php echo $key; ?>','<?php echo $i_row; ?>');" class="btn btn-success add-row"><i class="fa fa-plus"></i> <?php echo $button_add_row; ?></a>
								</div>
							</div>
							<?php if ($key == 'order') { ?>
							  <div class="panel panel-default">
								<div class="panel-heading"><a data-toggle="collapse" href="#set-total"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_order_totals_heading; ?></a></div>
								<div id="set-total" class="panel-collapse collapse">
									<div class="panel-body">
									  <div class="form-group">
										<label class="col-sm-3 control-label">
											<p><?php echo $entry_order_totals_template; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_order_totals_template_hel2_excel; ?>" class="fa fa-question-circle text-primary"></i></p>
										</label>
										<div class="col-sm-9">
										  <textarea name="template[total_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['total_tpl']) ? $template['total_tpl'] : "{total_name}: {total_value}\n"; ?></textarea>
										</div>
									  </div>
									</div>
								</div>
							  </div>
							<?php } ?>
							<?php if ($key == 'product') { ?>
							  <div class="panel panel-default">
								<div class="panel-heading"><a data-toggle="collapse" href="#set-option"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_option_heading; ?></a></div>
								<div id="set-option" class="panel-collapse collapse">
									<div class="panel-body">
									  <div class="form-group">
										<label class="col-sm-3 control-label">
											<p><?php echo $entry_option_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_option_tpl_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_option_tpl_help2_excel; ?>" class="fa fa-question-circle text-primary"></i></p>
										</label>
										<div class="col-sm-9">
										  <textarea name="template[option_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['option_tpl']) ? $template['option_tpl'] : "{option_name}: {option_value} [[(остаток: {option_quantity} шт.)]]\n"; ?></textarea>
										</div>
									  </div>
									</div>
								</div>
							  </div>

							  <div class="panel panel-default">
								<div class="panel-heading"><a data-toggle="collapse" href="#set-attribute"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $entry_attribute_heading; ?></a></div>
								<div id="set-attribute" class="panel-collapse collapse">
									<div class="panel-body">
									  <div class="form-group">
										<label class="col-sm-3 control-label">
											<p><?php echo $entry_attribute_group_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help; ?>" class="fa fa-question-circle text-danger"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_group_tpl_help2; ?>" class="fa fa-question-circle text-primary"></i></p>
										</label>
										<div class="col-sm-9">
										  <textarea name="template[attribute_group_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['attribute_group_tpl']) ? $template['attribute_group_tpl'] : "{attribute_group_name}: \n[[{attribute}]]\n"; ?></textarea>
										</div>
									  </div>
									  <div class="form-group">
										<label class="col-sm-3 control-label">
											<p><?php echo $entry_attribute_tpl; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_attribute_tpl_help2_excel; ?>" class="fa fa-question-circle text-primary"></i></p>
										</label>
										<div class="col-sm-9">
										  <textarea name="template[attribute_tpl]" rows="6" class="form-control htmlTextArea"><?php echo isset($template['attribute_tpl']) ? $template['attribute_tpl'] : "{attribute_name}: {attribute_value}; "; ?></textarea>
										</div>
									  </div>
									</div>
								</div>
							  </div>
							<?php } ?>
						</div>

					  </div>
					</div>
				  </div>
				  <?php } ?>
			  </div>
			</div>

			<div class="tab-pane" id="tab-total">
			  <div class="row">
				<div class="col-sm-12">
				  <div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_tpl_excel_orders_products.html"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp; <?php echo $entry_excel_total_info; ?></p>
				  </div>
				</div>
			  </div>

			  <div class="form-group">
				<div class="col-lg-3">
					<label class="control-label"><?php echo $entry_excel_default_store; ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $entry_excel_default_store_info; ?>" class="fa fa-question-circle text-primary"></i></label>
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span>
						<select name="template[default_store_id]" class="form-control" style="min-width: 160px;">
						  <?php foreach ($stores as $store) { ?>
							<option value="<?php echo $store['id']; ?>" <?php if (isset($template['default_store_id']) && $template['default_store_id'] == $store['id']) { ?> selected="selected"<?php } ?>><?php echo $store['text']; ?></option>
						  <?php } ?>
						</select>
					</div>
				</div>
				<div class="col-lg-3">
					<label class="control-label"><?php echo $entry_excel_sheet_name; ?></label>
					<div class="input-group valid-block">
						<span class="input-group-addon"><i class="fa fa-file-excel-o"></i></span>
						<input type="text" name="template[sheet_name]" value="<?php echo isset($template['sheet_name']) ? $template['sheet_name'] : $entry_excel_sheet_name; ?>" class="field-input mustbe form-control" placeholder="<?php echo $entry_excel_sheet_name; ?>" />
					</div>
				</div>
				<div class="col-lg-6">
				  <label class="control-label"><?php echo $entry_excel_default_font; ?></label>
					<div class="input-group">
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font; ?>"><i class="fa fa-font"></i></span>
						<select name="template[default_font][name]" class="form-control" style="min-width: 160px;">
						  <?php foreach ($font_list as $font) { ?>
							<option value="<?php echo $font; ?>" <?php if (isset($template['default_font']['name']) && $template['default_font']['name'] == $font) { ?> selected="selected"<?php } ?>><?php echo $font; ?></option>
						  <?php } ?>
						</select>
						<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_size; ?>"><i class="fa fa-text-height"></i></span>
						<input type="text" name="template[default_font][size]" value="<?php echo isset($template['default_font']['size']) ? $template['default_font']['size'] : 10; ?>" class="field-input numericmustbe form-control" placeholder="10" style="min-width: 40px;" />
					</div>
				</div>
			  </div>

			  <div class="panel-group">
				<div class="panel panel-default">
				  <div class="panel-heading"><b><?php echo $entry_default_cell_set; ?></b></div>
				  <div class="panel-body">

					<div class="table-responsive">
					  <table class="table table-bordered table-striped <?php echo $key; ?>-col-table" id="<?php echo $key; ?>-col-table<?php echo $i_row; ?>">
						<thead>
							<tr>
								<th style="width: 180px;"><?php echo $text_column_type; ?></th>
								<th style="width: "><?php echo $text_column_style; ?></th>
							</tr>
						</thead>
						<tbody>
						  <?php foreach ($default_set_col_list as $set => $title) { ?>
							<tr>
								<td><?php echo $title; ?> <?php if ($set == 'default_set_col_img') { ?>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $text_default_set_col_img_info; ?>" class="fa fa-question-circle text-primary"></i>&nbsp;&nbsp;<i data-toggle="tooltip" title="<?php echo $text_default_set_col_img_info2; ?>" class="fa fa-question-circle text-red"></i><?php } ?>
								</td>
								<td>
								  <div class="input-group">
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font; ?>"><i class="fa fa-font"></i></span>
									<select name="template[<?php echo $set; ?>][style][font][name]" class="form-control" style="min-width: 160px;">
									  <?php foreach ($font_list as $font) { ?>
										<option value="<?php echo $font; ?>" <?php if (isset($template[$set]['style']['font']['name']) && $template[$set]['style']['font']['name'] == $font) { ?> selected="selected"<?php } ?>><?php echo $font; ?></option>
									  <?php } ?>
									</select>
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_size; ?>"><i class="fa fa-text-height"></i></span>
									<input type="text" name="template[<?php echo $set; ?>][style][font][size]" value="<?php echo isset($template[$set]['style']['font']['size']) ? $template[$set]['style']['font']['size'] : 10; ?>" class="field-input numericmustbe form-control" placeholder="10" style="min-width: 40px;" />
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_color; ?>"><i class="fa fa-adjust"></i></span>
									<input type="text" name="template[<?php echo $set; ?>][style][font][color][rgb]" value="<?php echo isset($template[$set]['style']['font']['color']) && isset($template[$set]['style']['font']['color']['rgb']) ? $template[$set]['style']['font']['color']['rgb'] : '000000'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />

									<div class="input-group-btn" data-toggle="buttons">
										<label class="btn btn-default <?php if (isset($template[$set]['style']['font']['bold']) && $template[$set]['style']['font']['bold'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_bold; ?>"><input type="checkbox" value="1" name="template[<?php echo $set; ?>][style][font][bold]" <?php if (isset($template[$set]['style']['font']['bold']) && $template[$set]['style']['font']['bold'] == 1) { ?> checked<?php } ?> /><i class="fa fa-bold"></i></label>
										<label class="btn btn-default <?php if (isset($template[$set]['style']['font']['italic']) && $template[$set]['style']['font']['italic'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_italic; ?>"><input type="checkbox" value="1" name="template[<?php echo $set; ?>][style][font][italic]" <?php if (isset($template[$set]['style']['font']['italic']) && $template[$set]['style']['font']['italic'] == 1) { ?>checked<?php } ?> /><i class="fa fa-italic"></i></label>
										<label class="btn btn-default <?php if (isset($template[$set]['style']['font']['underline']) && $template[$set]['style']['font']['underline'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_underline; ?>"><input type="checkbox" value="1" name="template[<?php echo $set; ?>][style][font][underline]" <?php if (isset($template[$set]['style']['font']['underline']) && $template[$set]['style']['font']['underline'] == 1) { ?>checked<?php } ?> /><i class="fa fa-underline"></i></label>
										<label class="btn btn-default <?php if (isset($template[$set]['style']['font']['strike']) && $template[$set]['style']['font']['strike'] == 1) { ?>active<?php } ?>" data-toggle="tooltip" title="<?php echo $text_style_title_font_strike; ?>"><input type="checkbox" value="1" name="template[<?php echo $set; ?>][style][font][strike]" <?php if (isset($template[$set]['style']['font']['strike']) && $template[$set]['style']['font']['strike'] == 1) { ?>checked<?php } ?> /><i class="fa fa-strikethrough"></i></label>
									</div>

									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_fill; ?>"><i class="fa fa-circle"></i></span>
									<input type="text" name="template[<?php echo $set; ?>][style][fill][color][rgb]" value="<?php echo isset($template[$set]['style']['fill']['color']) && isset($template[$set]['style']['fill']['color']['rgb']) ? $template[$set]['style']['fill']['color']['rgb'] : 'ffffff'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />
									<input type="hidden" name="template[<?php echo $set; ?>][style][fill][type]" value="solid" />

									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_style; ?>"><i class="fa fa-square-o"></i></span>
									<select name="template[<?php echo $set; ?>][style][borders][allborders][style]" class="form-control" style="min-width: 80px;">
									  <?php foreach ($border_style_list as $style) { ?>
										<option value="<?php echo $style; ?>" <?php if (isset($template[$set]['style']['borders']['allborders']['style']) && $template[$set]['style']['borders']['allborders']['style'] == $style) { ?> selected="selected"<?php } ?>><?php echo $style; ?></option>
									  <?php } ?>
									</select>
									<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_color; ?>"><i class="fa fa-square"></i></span>
									<input type="text" name="template[<?php echo $set; ?>][style][borders][allborders][color][rgb]" value="<?php echo isset($template[$set]['style']['borders']['allborders']['color']) && isset($template[$set]['style']['borders']['allborders']['color']['rgb']) ? $template[$set]['style']['borders']['allborders']['color']['rgb'] : '000000'; ?>" class="form-control jscolor {mode:'HVS',position:'right'}" style="min-width: 70px;" />
								  </div>
								</td>
							</tr>
						  <?php } ?>
						</tbody>
					  </table>
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

// preview
$(document).delegate('#export-preview', 'click', function() {
	var order_id = $('#test-order-id').val();
	if (order_id) {
		var url = '<?php echo $preview; ?>';
		url = url.replace(/&amp;/g, "&");
		url += '&order_id=' + order_id;
		window.open(url, '_blank'); return false;
	}
});

--></script>

<script type="text/javascript"><!--

$(document).delegate('.select-col-format', 'change', function() {
	var format = $(this).val();
	var key = $(this).data("key");
	var i_row = $(this).data("i_row");
	var i_col = $(this).data("i_col");
	var target_id = '#format_add_'+key+'_'+i_row+'_'+i_col;

	if (format == 'image') {
		html = '<div class="input-group valid-block">';
		html += '<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_col_img_size; ?>"><i class="fa fa-image"></i></span>';
		html += '<input type="text" name="template['+key+']['+i_row+'][columns]['+i_col+'][img_width]" value="50" class="field-input numeric form-control" />';
		html += '<span class="input-group-addon">x</span>';
		html += '<input type="text" name="template['+key+']['+i_row+'][columns]['+i_col+'][img_height]" value="50" class="field-input numeric form-control"  />';
		html += '</div>';

		$(target_id).html(html);
	} else if (format == 'link') {
		html = '<div class="input-group">';
		html += '<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_col_link_text; ?>"><i class="fa fa-text-width"></i></span>';
		html += '<textarea name="template['+key+']['+i_row+'][columns]['+i_col+'][text]" rows="1" class="htmlTextArea form-control"  style="min-width: 200px;"></textarea>';

		html += '</div>';

		$(target_id).html(html);
	} else {
		$(target_id).html('');
	}
	tooltipRefresh();
	prepareTextArea($(target_id+' .htmlTextArea'));
});

function toggleStyle(action, type, row, col) {
	var target_id = '#style_'+type+'_'+row+'_'+col;

	if (action == 'remove') {
		html = '<a onclick="toggleStyle(\'add\',\''+type+'\',\''+row+'\',\''+col+'\');" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $btn_add_style; ?>"><i class="fa fa-plus-circle"></i></a>';

		$(target_id).html(html);
	} else {
		html = '';
		html += '<div class="input-group">';
		html += ' <div class="input-group-btn"><a onclick="toggleStyle(\'remove\',\''+type+'\',\''+row+'\',\''+col+'\');" class="btn btn-default" data-toggle="tooltip" title="<?php echo $btn_remove_style; ?>"><i class="fa fa-times-circle"></i></a></div>';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font; ?>"><i class="fa fa-font"></i></span>';
		html += ' <select name="template['+type+']['+row+'][columns]['+col+'][style][font][name]" class="form-control" style="min-width: 160px;">';
	  <?php foreach ($font_list as $font) { ?>
		html += '  <option value="<?php echo $font; ?>"><?php echo $font; ?></option>';
	  <?php } ?>
		html += ' </select>';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_size; ?>"><i class="fa fa-text-height"></i></span>';
		html += ' <input type="text" name="template['+type+']['+row+'][columns]['+col+'][style][font][size]" value="10" class="field-input numericmustbe form-control" placeholder="10"  style="min-width: 40px;" />';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_color; ?>"><i class="fa fa-adjust"></i></span>';
		html += ' <input type="text" name="template['+type+']['+row+'][columns]['+col+'][style][font][color][rgb]" value="000000" class="form-control jscolor {mode:\'HVS\',position:\'right\'}" style="min-width: 70px;" />';
		html += ' <div class="input-group-btn" data-toggle="buttons">';
		html += '  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_style_title_font_bold; ?>"><input type="checkbox" value="1" name="template['+type+']['+row+'][columns]['+col+'][style][font][bold]" /><i class="fa fa-bold"></i></label>';
		html += '  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_style_title_font_italic; ?>"><input type="checkbox" value="1" name="template['+type+']['+row+'][columns]['+col+'][style][font][italic]" /><i class="fa fa-italic"></i></label>';
		html += '  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_style_title_font_underline; ?>"><input type="checkbox" value="1" name="template['+type+']['+row+'][columns]['+col+'][style][font][underline]" /><i class="fa fa-underline"></i></label>';
		html += '  <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_style_title_font_strike; ?>"><input type="checkbox" value="1" name="template['+type+']['+row+'][columns]['+col+'][style][font][strike]" /><i class="fa fa-strikethrough"></i></label>';
		html += ' </div>';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_font_fill; ?>"><i class="fa fa-circle"></i></span>';
		html += ' <input type="text" name="template['+type+']['+row+'][columns]['+col+'][style][fill][color][rgb]" value="ffffff" class="form-control jscolor {mode:\'HVS\',position:\'right\'}" style="min-width: 70px;" />';
		html += ' <input type="hidden" name="template['+type+']['+row+'][columns]['+col+'][style][fill][type]" value="solid" />';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_style; ?>"><i class="fa fa-square-o"></i></span>';
		html += ' <select name="template['+type+']['+row+'][columns]['+col+'][style][borders][allborders][style]" class="form-control" style="min-width: 80px;">';
		<?php foreach ($border_style_list as $style) { ?>
		html += '  <option value="<?php echo $style; ?>"><?php echo $style; ?></option>';
		<?php } ?>
		html += ' </select>';
		html += ' <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_style_title_border_color; ?>"><i class="fa fa-square"></i></span>';
		html += ' <input type="text" name="template['+type+']['+row+'][columns]['+col+'][style][borders][allborders][color][rgb]" value="000000" class="form-control jscolor {mode:\'HVS\',position:\'right\'}" style="min-width: 70px;" />';
		html += ' </div>';
		html += '</div>';

		$(target_id).html(html);

		$('.jscolor').each(function() {
			new jscolor($(this)[0]);
		});
	}
	tooltipRefresh();
}

function addRow(type, row){
	var set_id = type+''+row;

	html = '';
	html += '<div class="panel panel-default '+type+'-row-panel">';
	html += ' <div class="panel-heading">';
	html += '  <div class="valid-block">';
	html += '   <div class="input-group">';
	html += '    <span class="input-group-addon">';
	html += '     <i class="fa fa-arrows handle" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"></i>&nbsp;&nbsp; <a data-toggle="collapse" href="#set-'+set_id+'"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_row; ?> '+row+'</a>';
	html += '    </span>';
	html += '    <span class="input-group-addon"><?php echo $text_row_type; ?></span>';
	html += '    <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_row_type_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '    <select name="template['+type+']['+row+'][type]" class="form-control">';
	html += '     <option value="title"><?php echo $text_row_type_title; ?></option>';
	html += '     <option value="data"><?php echo $text_row_type_data; ?></option>';
	html += '    </select>';
	html += '    <span class="input-group-addon"><?php echo $text_row_height; ?></span>';
	html += '    <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $text_row_height_info; ?>"><i class="fa fa-info text-blue"></i></span>';
	html += '    <input type="text" name="template['+type+']['+row+'][height]" value="20" class="field-input numeric form-control" placeholder="20" />';
	html += '   <div class="input-group-btn"><a onclick="$(this).closest(\'.'+type+'-row-panel\').remove(); tooltipRefresh();"  class="btn btn-danger" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>"><i class="fa fa-minus-circle"></i></a></div>';
	html += '   </div>';
	html += '  </div>';
	html += ' </div>';

	html += ' <div id="set-'+set_id+'" class="panel-collapse collapse in">';
	html += ' <div class="panel-body table-responsive">';
	html += '  <table class="table table-bordered table-striped '+type+'-col-table" id="'+type+'-col-table'+row+'">';

	html += '   <thead>';
	html += '    <tr>';
	html += '     <th class="text-center" style="width: 1%;">#</th>';
	html += '     <th style="min-width: 180px;"><?php echo $text_column_format; ?></th>';
	html += '     <th style="min-width: 200px;"><?php echo $text_column_format_param; ?></th>';
	html += '     <th style="min-width: 60px;"><i class="fa fa-clone"></i>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_column_merge; ?>"></i></th>';
	html += '     <th style="min-width: 185px; width: 185px;"><?php echo $text_column_align; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_column_align_info; ?>"></i></th>';
	html += '     <th style="min-width: 400px;"><?php echo $text_column_value; ?>';
	if (type == 'order' || type == 'product') {
		html += '&nbsp;&nbsp;&nbsp;<a onclick="getTableCodes(\''+type+'\');" style="font-weight: normal; cursor: pointer;"><?php echo $text_table_fields_codes; ?></a>';
	}
	html += '     </th>';
	html += '     <th style="width: "><?php echo $text_column_style; ?></th>';
	html += '     <th style="width: 60px;"></th>';
	html += '    </tr>';
	html += '   </thead>';

	html += '   <tbody class="tbody-sortable"></tbody>';
	html += '   <tfoot>';
	html += '    <tr class="active">';
	html += '     <td></td>';
	html += '     <td colspan="7"><a onclick="" class="btn btn-primary add-column-row" data-toggle="tooltip" title="<?php echo $button_add_column; ?>"><i class="fa fa-plus"></i></a></td>';
	html += '    </tr>';
	html += '   </tfoot>';
	html += '  </table>';
	html += ' </div>';
	html += ' </div>';
	html += '</div>';

	$('#'+type+'-row-group .add-row-panel').before(html);
	addRowColumn(type, row, 1); row++;
	$('#'+type+'-row-group a.add-row').attr('onclick', 'addRow(\''+type+'\',\''+row+'\')');
}

function addRowColumn(type, row, col) {
	html = '';
	html += '<tr id="'+type+'-col-table-row'+row+'_'+col+'" class="col-table-row">';
	html += ' <td class="text-center handle handle-col" data-toggle="tooltip" title="<?php echo $text_moove_to_sort; ?>"><i class="fa fa-arrows"></i><br/>'+col+'</td>';
	html += ' <td>';
	html += '  <select name="template['+type+']['+row+'][columns]['+col+'][format]" class="select-col-format form-control" data-key="'+type+'" data-i_row="'+row+'" data-i_col="'+col+'">';
	<?php foreach ($col_format_list as $format => $format_name) { ?>
	html += '   <option value="<?php echo $format; ?>"><?php echo $format_name; ?></option>';
	<?php } ?>
	html += '  </select>';
	html += ' </td>';
	html += ' <td id="format_add_'+type+'_'+row+'_'+col+'"></td>';
	html += ' <td><input type="text" name="template['+type+']['+row+'][columns]['+col+'][merge]" value="1" class="field-input numeric form-control" placeholder="1"/></td>';

	html += ' <td>';
	html += '  <div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_hor_left; ?>"><input type="radio" value="left" name="template['+type+']['+row+'][columns]['+col+'][alignment][horizomtal]" /><i class="fa fa-align-left"></i></label>';
	html += '   <label class="btn btn-default active" data-toggle="tooltip" title="<?php echo $text_align_hor_center; ?>"><input type="radio" value="center" name="template['+type+']['+row+'][columns]['+col+'][alignment][horizomtal]" checked /><i class="fa fa-align-center"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_hor_right; ?>"><input type="radio" value="right" name="template['+type+']['+row+'][columns]['+col+'][alignment][horizomtal]" /><i class="fa fa-align-right"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_hor_justify; ?>"><input type="radio" value="justify" name="template['+type+']['+row+'][columns]['+col+'][alignment][horizomtal]" /><i class="fa fa-align-right"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_hor_distributed; ?>"><input type="radio" value="distributed" name="template['+type+']['+row+'][columns]['+col+'][alignment][horizomtal]" /><i class="fa fa-align-right"></i></label>';
	html += '  </div>';

	html += '  <div class="btn-group btn-group-justified btn-group-xs" data-toggle="buttons">';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_ver_top; ?>"><input type="radio" value="top" name="template['+type+']['+row+'][columns]['+col+'][alignment][vertical]" /><i class="fa fa-hourglass-start"></i></label>';
	html += '   <label class="btn btn-default active" data-toggle="tooltip" title="<?php echo $text_align_ver_center; ?>"><input type="radio" value="center" name="template['+type+']['+row+'][columns]['+col+'][alignment][vertical]" checked /><i class="fa fa-hourglass-o"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_ver_bottom; ?>"><input type="radio" value="bottom" name="template['+type+']['+row+'][columns]['+col+'][alignment][vertical]" /><i class="fa fa-hourglass-end"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_ver_justify; ?>"><input type="radio" value="justify" name="template['+type+']['+row+'][columns]['+col+'][alignment][vertical]" /><i class="fa fa-hourglass-end"></i></label>';
	html += '   <label class="btn btn-default" data-toggle="tooltip" title="<?php echo $text_align_ver_distributed; ?>"><input type="radio" value="distributed" name="template['+type+']['+row+'][columns]['+col+'][alignment][vertical]" /><i class="fa fa-hourglass-end"></i></label>';
	html += '  </div>';
	html += ' </td>';

	html += ' <td><textarea name="template['+type+']['+row+'][columns]['+col+'][value]" rows="1" class="htmlTextArea form-control"></textarea></td>';
	html += ' <td id="style_'+type+'_'+row+'_'+col+'"><a onclick="toggleStyle(\'add\',\''+type+'\',\''+row+'\',\''+col+'\');" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $btn_add_style; ?>"><i class="fa fa-plus-circle"></i></a></td>';
	html += ' <td><a onclick="$(this).closest(\'.col-table-row\').remove(); tooltipRefresh();"  class="btn btn-danger" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>"><i class="fa fa-minus-circle"></i></a></td>';
	html += ' </tr>';

	$('#'+type+'-col-table'+row+' .tbody-sortable').append(html);

	var htmlTextArea = $('#'+type+'-col-table-row'+row+'_'+col+' .htmlTextArea');

	col++;

	$('#'+type+'-col-table'+row+' a.add-column-row').attr('onclick', 'addRowColumn(\''+type+'\',\''+row+'\',\''+col+'\')');

	$('#'+type+'-col-table'+row+' .tbody-sortable .btn').each(function() {
		var btn = $(this), input = btn.find('input');
		if (input.length && input.attr('type') && (input.attr('type') == 'checkbox' || input.attr('type') == 'radio')) {
			$(document).delegate(input, 'change', function() {
				if (btn.hasClass('active')) { input.prop('checked', true); }
				setTimeout(function() { btn.removeClass('focus'); }, 100 );
			});
			input.trigger('change');
		}
	});

	prepareTextArea(htmlTextArea);
	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh();
	prepareSortable();

}

function setTextArea(elem = false) {
	if (elem) {
		var cm = CodeMirror.fromTextArea(elem[0], { height: "auto", mode: "text/html", theme: 'rubyblue', lineNumbers: true, matchBrackets: true, lineWrapping: true, autoRefresh: true })
		.on('change', editor => {
			elem.html(editor.getValue()).trigger('change');
		});
	}
}

function prepareTextArea(elem = false) {
	if (!elem) {
		$('.htmlTextArea').each(function(){ setTextArea($(this)); });
	} else { setTextArea(elem); }
}

function prepareSortable() {
	row_list = 'header order product footer'.split(/\s+/);
	$.each(row_list, function (i, type) {

		var dropElSelector = '#'+type+'-row-group';
		var param = {}; param['group'] = {};
		param['group']['name'] = ''+type+'-rows';
		param['handle'] = '.handle';
		param['draggable'] = '.'+type+'-row-panel';
		param['ghostClass'] = 'sortable-elem';

		sortableStartGroup(dropElSelector, param);

		var dropElSelector = '.'+type+'-col-table > .tbody-sortable';
		var param = {}; param['group'] = {}; param['group'][type] = {};
		param['group'][type]['name'] = ''+type+'-table-rows';
		param['handle'] = '.handle';
		param['draggable'] = 'tr';
		param['ghostClass'] = 'sortable-elem';

		sortableStartGroup(dropElSelector, param);
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

	prepareTextArea();
	prepareSortable();
	controlCheckedInput();
	tooltipRefresh();
	$(function () { window.validation.init({ container: '#form' });});

	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

	$(document).delegate('#form, #form input, #form sellect, #form textarea', 'change', function() {
		if ($('#button_save').hasClass('btn-primary')) {
			$('#button_save').removeClass('btn-primary').addClass('btn-danger');
		}
	});

});

//--></script>

</div>
<?php echo $footer; ?>