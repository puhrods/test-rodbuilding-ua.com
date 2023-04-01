<h3 class="jumbotron"><?php echo $nav_appearance_module; ?></h3>
<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_theme; ?></label>
  <div class="col-sm-3">
    <select name="theme" class="form-control">
      <?php if ($theme == 'light') { ?>
      <option value="light" selected="selected"><?php echo $text_theme_light; ?></option>
      <?php } else { ?>
      <option value="light"><?php echo $text_theme_light; ?></option>
      <?php } ?>
      <?php if ($theme == 'light-block') { ?>
      <option value="light-block" selected="selected"><?php echo $text_theme_light_block; ?></option>
      <?php } else { ?>
      <option value="light-block"><?php echo $text_theme_light_block; ?></option>
      <?php } ?>      
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_module_heading_title; ?></label>
  <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
      <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
      <input type="text" name="module_heading_title[<?php echo $language['language_id']; ?>]" value="<?php echo isset($module_heading_title[$language['language_id']]) ? $module_heading_title[$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_module_heading_title; ?>" class="form-control" />
    </div>
    <?php if (isset($error_module_heading_title[$language['language_id']])) { ?>
    <div class="text-danger"><?php echo $error_module_heading_title[$language['language_id']]; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_mobile_button_text; ?></label>
  <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
      <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
      <input type="text" name="mobile_button_text[<?php echo $language['language_id']; ?>]" value="<?php echo isset($mobile_button_text[$language['language_id']]) ? $mobile_button_text[$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_mobile_button_text; ?>" class="form-control" />
    </div>
    <?php if (isset($error_mobile_button_text[$language['language_id']])) { ?>
    <div class="text-danger"><?php echo $error_mobile_button_text[$language['language_id']]; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_mobile_button_position; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($mobile_button_position == 'fixed') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="mobile_button_position" value="fixed" checked="checked" /> <?php echo $text_mobile_button_position_fixed; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="mobile_button_position" value="fixed" /> <?php echo $text_mobile_button_position_fixed; ?>
      </label>      
      <?php } ?>
      
      <?php if ($mobile_button_position == 'static') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="mobile_button_position" value="static" checked="checked" /> <?php echo $text_mobile_button_position_static; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="mobile_button_position" value="static" /> <?php echo $text_mobile_button_position_static; ?>
      </label>      
      <?php } ?>
      
      <?php if ($mobile_button_position == 'both') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="mobile_button_position" value="both" checked="checked" /> <?php echo $text_mobile_button_position_both; ?>
      </label>
      <?php } else { ?>
      <label class="btn btn-default">
        <input type="radio" name="mobile_button_position" value="both" /> <?php echo $text_mobile_button_position_both; ?>
      </label>      
      <?php } ?>      
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-mobile-max-width" new-feature><?php echo $entry_mobile_max_width; ?></label>
  <div class="col-sm-3">
    <div class="input-group">
      <input type="number" name="mobile_max_width" min="0" value="<?php echo $mobile_max_width; ?>" class="form-control" id="input-mobile-max-width" />
      <span class="input-group-addon">px</span>
    </div>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_mobile_max_width; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_mobile_placement; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($mobile_placement == 'left') { ?>
      <label class="btn btn-default active">
        <input type="radio" name="mobile_placement" value="left" checked="checked" /> <i class="fa fa-step-backward"></i> <?php echo $text_mobile_placement_left; ?>
      </label>
      <label class="btn btn-default">
        <input type="radio" name="mobile_placement" value="right" /> <?php echo $text_mobile_placement_right; ?> <i class="fa fa-step-forward"></i>
      </label>      
      <?php } ?>
      <?php if ($mobile_placement == 'right') { ?>
      <label class="btn btn-default">
        <input type="radio" name="mobile_placement" value="left" /> <i class="fa fa-step-backward"></i> <?php echo $text_mobile_placement_left; ?>
      </label>
      <label class="btn btn-default active">
        <input type="radio" name="mobile_placement" value="right" checked="checked" /> <?php echo $text_mobile_placement_right; ?> <i class="fa fa-step-forward"></i>
      </label>
      <?php } ?>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_mobile_remember_state; ?></label>
  <div class="col-sm-5">
    <?php $tpl_bool_button('mobile_remember_state', $mobile_remember_state); ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_mobile_remember_state; ?></p>
  </div>  
</div>

<h3 class="jumbotron"><?php echo $nav_appearance_filter; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-show-filters-limit"><?php echo $entry_show_first_limit; ?></label>
  <div class="col-sm-3">
    <div class="input-group">
      <input type="number" name="show_filters_limit" min="0" value="<?php echo $show_filters_limit; ?>" class="form-control" id="input-show-filters-limit" />
      <span class="input-group-addon"><?php echo $text_filters; ?></span>
    </div>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_show_filters_limit; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_hidden_filters_lazy_load; ?></label>
  <div class="col-sm-5">
    <?php $tpl_bool_button('hidden_filters_lazy_load', $hidden_filters_lazy_load); ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_hidden_filters_lazy_load; ?></p>
  </div>  
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_hide_single_value; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('hide_single_value', $hide_single_value, 'y/n'); ?>

    <p class="help-block"><?php echo $help_hide_single_value; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_slider_input; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('slider_input', $slider_input); ?>
    
    <p class="help-block"><?php echo $help_slider_input; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_slider_pips; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('slider_pips', $slider_pips); ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_show_selected; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('show_selected', $show_selected, 'y/n'); ?>
    
    <p class="help-block"><?php echo $help_show_selected; ?></p>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_appearance_filter_value; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_show_counter; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('show_counter', $show_counter, 'y/n'); ?>

    <p class="help-block"><?php echo $help_show_counter; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-show-values-limit"><?php echo $entry_show_first_limit; ?></label>
  <div class="col-sm-3">
    <div class="input-group">
      <input type="number" name="show_values_limit" min="0" value="<?php echo $show_values_limit; ?>" class="form-control" id="input-show-values-limit" />
      <span class="input-group-addon"><?php echo $text_values; ?></span>
    </div>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_show_values_limit; ?></p>
  </div>    
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_hidden_values_lazy_load; ?></label>
  <div class="col-sm-5">
    <?php $tpl_bool_button('hidden_values_lazy_load', $hidden_values_lazy_load); ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_hidden_values_lazy_load; ?></p>
  </div>   
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_hide_empty_values; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('hide_empty_values', $hide_empty_values, 'y/n'); ?>

    <p class="help-block"><?php echo $help_hide_empty_values; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label beta-feature" new-feature><?php echo $entry_values_auto_column; ?></label>
  <div class="col-sm-5">
    <?php $tpl_bool_button('values_auto_column', $values_auto_column, 'y/n'); ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_values_auto_column; ?></p>
  </div>    
</div>