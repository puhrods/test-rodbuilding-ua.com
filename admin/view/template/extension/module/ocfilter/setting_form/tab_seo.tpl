<h3 class="jumbotron"><?php echo $nav_seo_page; ?></h3>
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_sitemap; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($sitemap_status) { ?>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#sitemap-link').collapse('show')">
        <input type="radio" name="sitemap_status" value="1" checked="checked" /> <?php echo $text_enabled; ?>
      </label>
      <label class="btn btn-default" onclick="$('#sitemap-link').collapse('hide')">
        <input type="radio" name="sitemap_status" value="0" /> <?php echo $text_disabled; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" onclick="$('#sitemap-link').collapse('show')">
        <input type="radio" name="sitemap_status" value="1" /> <?php echo $text_enabled; ?>
      </label>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#sitemap-link').collapse('hide')">
        <input type="radio" name="sitemap_status" value="0" checked="checked" /> <?php echo $text_disabled; ?>
      </label>
      <?php } ?>
    </div>
  </div>
</div>

<div class="collapse" id="sitemap-link">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_sitemap_link; ?></label>
    <div class="col-sm-9">
      <input type="text" name="sitemap_link" value="<?php echo $sitemap_link; ?>" class="form-control" onclick="$(this).select();" readonly="readonly" />
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_page_category_link_status; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($page_category_link_status) { ?>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-category-link').collapse('show')">
        <input type="radio" name="page_category_link_status" value="1" checked="checked" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default" onclick="$('#page-category-link').collapse('hide')">
        <input type="radio" name="page_category_link_status" value="0" /> <?php echo $text_no; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" onclick="$('#page-category-link').collapse('show')">
        <input type="radio" name="page_category_link_status" value="1" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-category-link').collapse('hide')">
        <input type="radio" name="page_category_link_status" value="0" checked="checked" /> <?php echo $text_no; ?>
      </label>
      <?php } ?>
    </div>
    <p class="help-block"><?php echo $help_page_category_link_status; ?></p>   
  </div>
</div>

<div class="collapse" id="page-category-link">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_page_category_link_position; ?></label>
    <div class="col-sm-9">
      <div class="btn-group" data-toggle="buttons">
        <?php if ($page_category_link_position == 'top') { ?>
        <label class="btn btn-default active">
          <input type="radio" name="page_category_link_position" value="top" checked="checked" /> <i class="fa fa-sort-asc"></i> &nbsp;<?php echo $text_page_category_link_above; ?>
        </label>
        <?php } else { ?>
        <label class="btn btn-default">
          <input type="radio" name="page_category_link_position" value="top" /> <i class="fa fa-sort-asc"></i> &nbsp;<?php echo $text_page_category_link_above; ?>
        </label>
        <?php } ?>
        <?php if ($page_category_link_position == 'bottom') { ?>
        <label class="btn btn-default active">
          <input type="radio" name="page_category_link_position" value="bottom" checked="checked" /> <i class="fa fa-sort-desc"></i> &nbsp;<?php echo $text_page_category_link_under; ?>
        </label>
        <?php } else { ?>
        <label class="btn btn-default">
          <input type="radio" name="page_category_link_position" value="bottom" /> <i class="fa fa-sort-desc"></i> &nbsp;<?php echo $text_page_category_link_under; ?>
        </label>
        <?php } ?>
        <?php if ($page_category_link_position == 'both') { ?>
        <label class="btn btn-default active">
          <input type="radio" name="page_category_link_position" value="both" checked="checked" /> <i class="fa fa-sort"></i> &nbsp;<?php echo $text_page_category_link_both; ?>
        </label>
        <?php } else { ?>
        <label class="btn btn-default">
          <input type="radio" name="page_category_link_position" value="both" /> <i class="fa fa-sort"></i> &nbsp;<?php echo $text_page_category_link_both; ?>
        </label>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_page_module_link_status; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($page_module_link_status) { ?>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-module-link').collapse('show')">
        <input type="radio" name="page_module_link_status" value="1" checked="checked" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default" onclick="$('#page-module-link').collapse('hide')">
        <input type="radio" name="page_module_link_status" value="0" /> <?php echo $text_no; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" onclick="$('#page-module-link').collapse('show')">
        <input type="radio" name="page_module_link_status" value="1" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-module-link').collapse('hide')">
        <input type="radio" name="page_module_link_status" value="0" checked="checked" /> <?php echo $text_no; ?>
      </label>
      <?php } ?>
    </div>
    <p class="help-block"><?php echo $help_page_module_link_status; ?></p>   
  </div>
</div>

<div class="collapse" id="page-module-link">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_page_module_link_title; ?></label>
    <div class="col-sm-9">
      <?php foreach ($languages as $language) { ?>
      <div class="input-group">
        <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
        <input type="text" name="page_module_link_title[<?php echo $language['language_id']; ?>]" value="<?php echo isset($page_module_link_title[$language['language_id']]) ? $page_module_link_title[$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_page_module_link_title; ?>" class="form-control" />
      </div>
      <?php if (isset($error_page_module_link_title[$language['language_id']])) { ?>
      <div class="text-danger"><?php echo $error_page_module_link_title[$language['language_id']]; ?></div>
      <?php } ?>
      <?php } ?>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_page_product_link_status; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($page_product_link_status) { ?>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-product-link').collapse('show')">
        <input type="radio" name="page_product_link_status" value="1" checked="checked" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default" onclick="$('#page-product-link').collapse('hide')">
        <input type="radio" name="page_product_link_status" value="0" /> <?php echo $text_no; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" onclick="$('#page-product-link').collapse('show')">
        <input type="radio" name="page_product_link_status" value="1" /> <?php echo $text_yes; ?>
      </label>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#page-product-link').collapse('hide')">
        <input type="radio" name="page_product_link_status" value="0" checked="checked" /> <?php echo $text_no; ?>
      </label>
      <?php } ?>
    </div>
    <p class="help-block"><?php echo $help_page_product_link_status; ?></p>   
  </div>
</div>

<div class="collapse" id="page-product-link"> 
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_page_product_link_relation_type; ?></label>
    <div class="col-sm-9">
      <div class="btn-group" data-toggle="buttons">
        <?php if ($page_product_link_relation_type == 'complete') { ?>
        <label class="btn btn-default active">
          <input type="radio" name="page_product_link_relation_type" value="complete" checked="checked" /> <i class="fa fa-object-group"></i> &nbsp;<?php echo $text_page_product_link_relation_complete; ?>
        </label>
        <?php } else { ?>
        <label class="btn btn-default">
          <input type="radio" name="page_product_link_relation_type" value="complete" /> <i class="fa fa-object-group"></i> &nbsp;<?php echo $text_page_product_link_relation_complete; ?>
        </label>
        <?php } ?>
        <?php if ($page_product_link_relation_type == 'partial') { ?>
        <label class="btn btn-default active">
          <input type="radio" name="page_product_link_relation_type" value="partial" checked="checked" /> <i class="fa "></i> &nbsp;<?php echo $text_page_product_link_relation_partial; ?>
        </label>
        <?php } else { ?>
        <label class="btn btn-default">
          <input type="radio" name="page_product_link_relation_type" value="partial" /> <i class="fa fa-object-ungroup"></i> &nbsp;<?php echo $text_page_product_link_relation_partial; ?>
        </label>
        <?php } ?>
      </div>
      <p class="help-block"><?php echo $help_page_product_link_relation_type; ?></p>  
    </div>
  </div>  
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_url_suffix; ?></label>
  <div class="col-sm-2">
    <input type="text" name="url_suffix" value="<?php echo $url_suffix; ?>" placeholder="<?php echo $placeholder_url_suffix; ?>" class="form-control" />
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_seo_meta; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_add_meta; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($add_meta == 'filter_value') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="add_meta" value="filter_value" checked="checked" /> <?php echo $text_add_meta_filter_value; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="add_meta" value="filter_value" /> <?php echo $text_add_meta_filter_value; ?>
      </label>
      <?php } ?>
      <?php if ($add_meta == 'value') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="add_meta" value="value" checked="checked" /> <?php echo $text_add_meta_value; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="add_meta" value="value" /> <?php echo $text_add_meta_value; ?>
      </label>
      <?php } ?>
      <?php if (!$add_meta) { ?>
      <label class="btn btn-default active">
        <input type="radio" name="add_meta" value="0" checked="checked" /> <?php echo $text_add_meta_disabled; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="add_meta" value="0" /> <?php echo $text_add_meta_disabled; ?>
      </label>
      <?php } ?>
    </div>
  </div>
</div>

<div class="collapse" id="collapse-meta-filter">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_meta_filter_separator; ?></label>
    <div class="col-sm-2 col-lg-1">
      <input type="text" name="meta_filter_separator" value="<?php echo $meta_filter_separator; ?>" class="form-control">
    </div>
  </div>
</div>

<div class="collapse" id="collapse-meta-value">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_meta_value_separator; ?></label>
    <div class="col-sm-2 col-lg-1">
      <input type="text" name="meta_value_separator" value="<?php echo $meta_value_separator; ?>" class="form-control">
    </div>
  </div>
</div>

<div class="collapse" id="collapse-meta-lowercase">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_meta_lowercase; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('meta_lowercase', $meta_lowercase); ?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label" for="input-add-meta-limit"><?php echo $entry_add_meta_limit; ?></label>
    <div class="col-sm-3 col-lg-2">
      <div class="input-group">
        <input type="number" name="add_meta_limit" min="1" value="<?php echo $add_meta_limit; ?>" class="form-control" id="input-add-meta-limit" />
        <span class="input-group-addon"><?php echo $text_values; ?></span>
      </div>      
    </div>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_seo_misc; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_category_breadcrumb; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('category_breadcrumb', $category_breadcrumb); ?>
    
    <p class="help-block"><?php echo $help_category_breadcrumb; ?></p>  
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_product_breadcrumb; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('product_breadcrumb', $product_breadcrumb); ?>
    
    <p class="help-block"><?php echo $help_product_breadcrumb; ?></p>  
  </div>
</div>

<script type="text/javascript">
$(function() {
  $('input[name="add_meta"]').on('change', function(e) {
    if (!$(this).prop('checked')) {
      return;
    }

    if ($(this).val() == 'filter_value') {
      $('#collapse-meta-filter, #collapse-meta-value, #collapse-meta-lowercase').collapse('show');
    } else if ($(this).val() == 'value') {
      $('#collapse-meta-value, #collapse-meta-lowercase').collapse('show');
      $('#collapse-meta-filter').collapse('hide');
    } else {
      $('#collapse-meta-filter, #collapse-meta-value, #collapse-meta-lowercase').collapse('hide');
    }
  }).filter(':checked').trigger('change');

  $('input[name="indexing_status"]').on('change', function() {
    $('#collapse-indexing').collapse($(this).prop('checked') && $(this).val() == 1 ? 'show' : 'hide');
  }).filter(':checked').trigger('change');
});
</script>