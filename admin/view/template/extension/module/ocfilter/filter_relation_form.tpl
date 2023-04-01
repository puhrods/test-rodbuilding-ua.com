<div class="container-fluid">
  <div class="form-horizontal">
    <?php if ($filters) { ?>   
    <?php foreach ($filters as $filter) { ?>
    <?php $class = 'form-group ocf-form-group-condensed'; ?>
    
    <?php if (!$filter['status']) { ?>
    <?php $class .= ' ocf-form-group-inactive'; ?>
    <?php } ?>
    
    <?php if ($filter['selected']) { ?>
    <?php $class .= ' ocf-form-group-selected'; ?>
    <?php } ?>   

    <?php if ($filter['type'] == 'slide' || $filter['type'] == 'slide_dual') { ?>
    <?php $class .= ' ocf-form-group-slider'; ?>
    <?php } ?> 

    <?php if ($filter['values_autocomplete']) { ?>
    <?php $class .= ' ocf-form-group-autocomplete'; ?>
    <?php } ?>
    
    <?php $class .= ' ocf-form-group-source-' . $filter['source_name']; ?>
    
    <div class="<?php echo $class; ?>" data-ocfilter-filter-key="<?php echo $filter['filter_key']; ?>" data-total-values="<?php echo $filter['total_values']; ?>">
      <?php if ($page) { ?>
      <?php $class = 'col-xs-6 col-lg-4 control-label'; ?>
      <?php } else { ?>
      <?php $class = 'col-xs-6 col-lg-5 control-label'; ?>
      <?php } ?>
      <label class="<?php echo $class; ?>"><?php echo $filter['name']; ?></label>
      
      <?php if ($page) { ?>
      <div class="col-xs-6 col-lg-2">
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
          <?php if ($filter['selected_all']) { ?>
          <label class="btn btn-default active" data-toggle="popover" data-trigger="hover" data-container="body" data-placement="top" data-content="<?php echo $help_all; ?>"><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="0" checked="checked" autocomplete="off" /> <?php echo $entry_all; ?></label>
          <?php } else { ?>
          <label class="btn btn-default" data-toggle="popover" data-trigger="hover" data-container="body" data-placement="top" data-content="<?php echo $help_all; ?>"><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="0" autocomplete="off" /> <?php echo $entry_all; ?></label>
          <?php } ?>  
          <?php if ($allow_group && !($filter['type'] == 'slide' || $filter['type'] == 'slide_dual')) { ?>
          <?php if ($filter['group']) { ?>
          <label class="btn btn-default active" data-toggle="popover" data-trigger="hover" data-container="body" data-placement="top" data-content="<?php echo $help_group; ?>"><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="group" checked="checked" autocomplete="off" /> <?php echo $entry_group; ?></label>
          <?php } else { ?>
          <label class="btn btn-default" data-toggle="popover" data-trigger="hover" data-container="body" data-placement="top" data-content="<?php echo $help_group; ?>"><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="group" autocomplete="off" /> <?php echo $entry_group; ?></label>
          <?php } ?>            
          <?php } ?>
        </div>     
      </div>
      <?php } ?>

      <?php if ($page) { ?>
      <?php $class = 'col-xs-12 col-lg-6'; ?>
      <?php } else { ?>
      <?php $class = 'col-xs-6 col-lg-7'; ?>
      <?php } ?>
      <div class="<?php echo $class; ?>">
        <div class="hidden-lg mt-2"></div>
        <?php if ($filter['type'] == 'slide' || $filter['type'] == 'slide_dual') { ?>
        <div class="input-group">
          <div class="input-group-prepend ocf-relative">
            <input type="number" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][min]" value="<?php echo $filter['min']; ?>" class="ocf-input-slide-value-min form-control<?php echo ($filter['selected_all'] ? ' disabled' : ''); ?>" <?php echo (($filter['selected_all'] || (strlen($filter['min']) < 1)) ? 'disabled="disabled"' : ''); ?> />
            <div class="ocf-input-placeholder"></div>
          </div>            
          <span class="input-group-addon">&mdash;</span>
          <div class="input-group-prepend ocf-relative">          
            <input type="number" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][max]" value="<?php echo $filter['max']; ?>" class="ocf-input-slide-value-max form-control<?php echo ($filter['selected_all'] ? ' disabled' : ''); ?>" <?php echo (($filter['selected_all'] || (strlen($filter['min']) < 1)) ? 'disabled="disabled"' : ''); ?> />
            <div class="ocf-input-placeholder"></div>
          </div>
          <?php if ($filter['suffix']) { ?>
          <span class="input-group-addon"><?php echo $filter['suffix']; ?></span>
          <?php } ?>
        </div>
        <?php } else { ?>
        <?php if ($filter['values_autocomplete']) { ?>
        
        <div class="input-group">
          <input type="text" name="filter_value_name" value="" placeholder="<?php echo $entry_value_name; ?>" class="form-control" data-filter-key="<?php echo $filter['filter_key']; ?>" data-target="#value-relation-<?php echo str_replace('.', '-', $filter['filter_key']); ?>" <?php echo ($filter['selected_all'] ? 'disabled="disabled"' : ''); ?> />
          <div class="input-group-addon" data-toggle="tooltip" data-placement="top" title="<?php echo $text_values_autocomplete; ?>">                    
            <i class="fa fa-question-circle"></i>
          </div>
        </div> 

        <div class="label-ocf-list<?php echo ($filter['selected_all'] ? ' disabled' : ''); ?>" id="value-relation-<?php echo str_replace('.', '-', $filter['filter_key']); ?>">
          <?php foreach ($filter['values_selected'] as $value) { ?>         
          <span class="label label-ocf-value remove-autocomplete-value" title="<?php echo $value['name']; ?>"><input type="hidden" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="<?php echo $value['value_id']; ?>" /> <span><?php echo $value['name']; ?></span> <i class="fa fa-times-circle"></i></span>
          <?php } ?>
        </div>
        
        <?php } else if ($filter['values']) { ?>  
        
        <div class="dropdown ocf-product-values-dropdown">
          <?php $selecteds = array_column($filter['values_selected'], 'name'); ?>
          
          <button type="button" class="btn btn-light dropdown-toggle<?php echo ($filter['selected_all'] ? ' disabled' : ''); ?>" data-toggle="dropdown">
            <?php if ($selecteds) { ?>
            <span class="dropdown-label label-selected" data-default="<?php echo $text_select_product_value; ?>"><span class="label label-ocf-value"><?php echo implode('</span><span class="label label-ocf-value">', $selecteds); ?></span></span> 
            <?php } else { ?>
            <span class="dropdown-label" data-default="<?php echo $text_select_product_value; ?>"><?php echo $text_select_product_value; ?></span> 
            <?php } ?>        
            <span class="fa fa-caret-down"></span>
          </button>                 

          <ul class="dropdown-menu ocf-filter-dm">
            <?php foreach ($filter['values'] as $value) { ?>
            <?php if ($value['selected']) { ?>
            <li class="active"><label><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="<?php echo $value['value_id']; ?>" checked="checked" autocomplete="off" /> <span><?php echo $value['name']; ?><?php echo $filter['suffix']; ?></span></label></li>
            <?php } else { ?>
            <li><label><input type="checkbox" name="ocfilter_filter[<?php echo $filter['filter_key']; ?>][]" value="<?php echo $value['value_id']; ?>" autocomplete="off" /> <span><?php echo $value['name']; ?><?php echo $filter['suffix']; ?></span></label></li>
            <?php } ?>
            <?php } ?>
          </ul>
        </div>
        
        <?php } else { ?>
        <a href="<?php echo $filter['href']; ?>#tab-values" target="_blank"><?php echo $text_add_filter_values; ?></a>
        <?php } ?>
        <?php } ?>
      </div>      
    </div><!-- /.form-group -->
    <?php } ?>
    <?php } else { ?>
    <?php echo $text_filters_not_found; ?>
    <?php } ?>
  </div>
</div>