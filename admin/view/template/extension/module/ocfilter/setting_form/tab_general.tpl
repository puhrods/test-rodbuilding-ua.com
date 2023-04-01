<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_license; ?></label>
  <div class="col-sm-5">
    <input type="text" name="license" value="<?php echo $license; ?>" class="form-control" />
    <?php if ($error_license) { ?>
    <p class="text-danger"><?php echo $error_license; ?></p>
    <?php } ?>    
    <p class="help-block"><?php echo $help_license; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label> 
  <div class="col-sm-9">
    <?php $tpl_bool_button('status', $status); ?>
    
    <p class="help-block"><?php echo $help_status; ?></p>
  </div>
</div>

<div class="form-group form-group-popover">
  <label class="col-sm-3 control-label"><?php echo $entry_category_visibility; ?></label>
  <div class="col-sm-9">
    <div class="btn-group-vertical" data-toggle="buttons">
      <?php if ($category_visibility == 'default') { ?>
      <label class="btn btn-default active" data-content="<?php echo $help_category_visibility_default; ?>">
        <input type="radio" name="category_visibility" value="default" checked="checked" /> <?php echo $text_category_visibility_default; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" data-content="<?php echo $help_category_visibility_default; ?>">
        <input type="radio" name="category_visibility" value="default" /> <?php echo $text_category_visibility_default; ?>
      </label>
      <?php } ?>
      <?php if ($category_visibility == 'parent') { ?>
      <label class="btn btn-default active" data-content="<?php echo $help_category_visibility_parent; ?>">
        <input type="radio" name="category_visibility" value="parent" checked="checked" /> <?php echo $text_category_visibility_parent; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" data-content="<?php echo $help_category_visibility_parent; ?>">
        <input type="radio" name="category_visibility" value="parent" /> <?php echo $text_category_visibility_parent; ?>
      </label>
      <?php } ?>
      <?php if ($category_visibility == 'last_level') { ?>
      <label class="btn btn-default active" data-content="<?php echo $help_category_visibility_last_level; ?>" new-feature>
        <input type="radio" name="category_visibility" value="last_level" checked="checked" /> <?php echo $text_category_visibility_last_level; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" data-content="<?php echo $help_category_visibility_last_level; ?>" new-feature>
        <input type="radio" name="category_visibility" value="last_level" /> <?php echo $text_category_visibility_last_level; ?>
      </label>
      <?php } ?>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_hide_categories; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('hide_categories', $hide_categories, 'y/n'); ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_only_instock; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('only_instock', $only_instock, 'y/n'); ?>

    <p class="help-block"><?php echo $help_only_instock; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_search_button; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('search_button', $search_button, 'y/n'); ?>
    
    <p class="help-block"><?php echo $help_search_button; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_cache; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($cache) { ?>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#cache-store').collapse('show')">
        <input type="radio" name="cache" value="1" checked="checked" /> <?php echo $text_enabled; ?>
      </label>
      <label class="btn btn-default" onclick="$('#cache-store').collapse('hide')">
        <input type="radio" name="cache" value="0" /> <?php echo $text_disabled; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default" onclick="$('#cache-store').collapse('show')">
        <input type="radio" name="cache" value="1" /> <?php echo $text_enabled; ?>
      </label>
      <label class="btn btn-default active" data-trigger="onclick" onclick="$('#cache-store').collapse('hide')">
        <input type="radio" name="cache" value="0" checked="checked" /> <?php echo $text_disabled; ?>
      </label>
      <?php } ?>
    </div>
    <p class="help-block"><?php echo $help_cache; ?></p>
  </div>
</div>

<div class="collapse" id="cache-store">
  <div class="form-group">
    <label class="col-sm-3 control-label" for="input-cache-store"><?php echo $entry_cache_store; ?></label>
    <div class="col-sm-4 col-md-3 col-lg-2">
      <select name="cache_store" id="input-cache-store" class="form-control">
        <?php if ($cache_store == 'db') { ?>
        <option value="db" selected="selected"><?php echo $text_cache_db; ?></option>
        <?php } else { ?>
        <option value="db"><?php echo $text_cache_db; ?></option>
        <?php } ?>
        <?php if ($cache_store == 'system') { ?>
        <option value="system" selected="selected"><?php echo $text_cache_system; ?></option>
        <?php } else { ?>
        <option value="system"><?php echo $text_cache_system; ?></option>
        <?php } ?>        
      </select>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label beta-feature"><?php echo $entry_debug; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('debug', $debug); ?>
    <p class="help-block"><?php echo $help_debug; ?></p>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_general_compatibility; ?></h3>

<div class="row">
  <div class="col-sm-offset-3 col-sm-9">
    <h4>Hyper Multi Product Models</h4>
  </div>
</div>

<div class="form-group<?php echo ($use_hpmodel ? '' : ' disabled'); ?>">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_module_hpm_group_products; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('module_hpm_group_products', $module_hpm_group_products, 'y/n'); ?>
    <p class="help-block"><?php echo $help_module_hpm_group_products; ?></p>
  </div>
</div>

<div class="form-group<?php echo ($use_hpmodel ? '' : ' disabled'); ?>">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_module_hpm_group_counter; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('module_hpm_group_counter', $module_hpm_group_counter, 'y/n'); ?>
    <p class="help-block"><?php echo $help_module_hpm_group_counter; ?></p>
  </div>
</div>