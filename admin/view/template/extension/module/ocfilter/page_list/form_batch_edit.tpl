<form action="<?php echo $edit_batch; ?>" method="post" enctype="multipart/form-data" id="form-edit">
  <div class="row">
    <div class="col-xs-6 col-lg-2">
      <select name="edit_action" class="form-control" id="input-edit-action" data-selected="<?php echo $edit_action; ?>">
        <option value="replace"><?php echo $text_action_replace_text; ?></option>
        <option value="add"><?php echo $text_action_add_text; ?></option>
        <option value="update"><?php echo $text_action_update; ?></option>
        <option value="delete"><?php echo $text_action_delete; ?></option>
      </select>
    </div>
    
    <div class="col-xs-12 col-lg-8" tool-replace tool-add tool-update>
      <div class="hidden-lg mt-2"></div>

      <div class="input-group" tool-replace tool-add>
        <input type="text" name="edit_text_1" value="<?php echo $edit_text_1; ?>" class="form-control" placeholder="<?php echo $placeholder_text; ?>" />    
        <div class="input-group-addon" tool-replace><?php echo $text_replace_on; ?></div>  
        <div class="input-group-btn hidden" tool-add>
          <select name="edit_position" class="btn btn-default" data-selected="<?php echo $edit_position; ?>">
            <option value="prepend"><?php echo $text_add_prepend; ?></option>
            <option value="append"><?php echo $text_add_append; ?></option>
          </select>
        </div>  
        <input type="text" name="edit_text_2" value="<?php echo $edit_text_2; ?>" class="form-control" placeholder="<?php echo $placeholder_text; ?>" tool-replace />
   
        <div class="input-group-addon"><?php echo $text_destination; ?></div>
        <select name="edit_destination" class="form-control" data-selected="<?php echo $edit_destination; ?>">
          <option value="all"><?php echo $text_destination_all; ?></option>
          <option value="name"><?php echo $text_destination_name; ?></option>
          <option value="heading_title"><?php echo $text_destination_heading_title; ?></option>
          <option value="description_top"><?php echo $text_destination_description_top; ?></option>
          <option value="description_bottom"><?php echo $text_destination_description_bottom; ?></option>
          <option value="meta_title"><?php echo $text_destination_meta_title; ?></option>
          <option value="meta_description"><?php echo $text_destination_meta_description; ?></option>
          <option value="meta_keyword"><?php echo $text_destination_meta_keyword; ?></option>
          <option value="keyword"><?php echo $text_destination_seo_url; ?></option>
        </select>                                                        
      </div><!-- /replace/add -->
                    
      <div class="input-group hidden" tool-update>
        <input type="text" name="edit_category_name" value="" class="form-control" placeholder="<?php echo $edit_category_name ? $edit_category_name : $entry_category; ?>" />    
        <input type="hidden" name="edit_category_id" value="<?php echo $edit_category_id; ?>" />    
        <div class="input-group-btn">
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-default" data-toggle="dropdown"><?php echo $entry_edit_status; ?> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu" data-checked="<?php echo $edit_status; ?>">
              <li><label><input type="radio" name="edit_status" value="*" autocomplete="off" checked> <?php echo $text_discard; ?></label></li>
              <li role="separator" class="divider"></li>
              <li><label><input type="radio" name="edit_status" value="1" autocomplete="off"> <?php echo $text_enabled; ?></label></li>
              <li><label><input type="radio" name="edit_status" value="0" autocomplete="off"> <?php echo $text_disabled; ?></label></li>
            </ul>
          </div>                 
        </div> 
        <div class="input-group-addon"><?php echo $text_display; ?></div>    
        <div class="input-group-btn">
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-default" data-toggle="dropdown"><?php echo $entry_edit_display_category; ?> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu" data-checked="<?php echo $edit_category; ?>">
              <li><label><input type="radio" name="edit_category" value="*" autocomplete="off" checked> <?php echo $text_discard; ?></label></li>
              <li role="separator" class="divider"></li>
              <li><label><input type="radio" name="edit_category" value="1" autocomplete="off"> <?php echo $text_enabled; ?></label></li>
              <li><label><input type="radio" name="edit_category" value="0" autocomplete="off"> <?php echo $text_disabled; ?></label></li>
            </ul>
          </div>  
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-default" data-toggle="dropdown"><?php echo $entry_edit_display_product; ?> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu" data-checked="<?php echo $edit_product; ?>">
              <li><label><input type="radio" name="edit_product" value="*" autocomplete="off" checked> <?php echo $text_discard; ?></label></li>
              <li role="separator" class="divider"></li>
              <li><label><input type="radio" name="edit_product" value="1" autocomplete="off"> <?php echo $text_enabled; ?></label></li>
              <li><label><input type="radio" name="edit_product" value="0" autocomplete="off"> <?php echo $text_disabled; ?></label></li>
            </ul>
          </div>  
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-default" data-toggle="dropdown"><?php echo $entry_edit_display_module; ?> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu" data-checked="<?php echo $edit_module; ?>">
              <li><label><input type="radio" name="edit_module" value="*" autocomplete="off" checked> <?php echo $text_discard; ?></label></li>
              <li role="separator" class="divider"></li>
              <li><label><input type="radio" name="edit_module" value="1" autocomplete="off"> <?php echo $text_enabled; ?></label></li>
              <li><label><input type="radio" name="edit_module" value="0" autocomplete="off"> <?php echo $text_disabled; ?></label></li>
            </ul>
          </div>  
          <div class="btn-group dropdown">
            <button type="button" class="btn btn-default" data-toggle="dropdown"><?php echo $entry_edit_display_sitemap; ?> <i class="fa fa-caret-down"></i></button>
            <ul class="dropdown-menu" data-checked="<?php echo $edit_sitemap; ?>">
              <li><label><input type="radio" name="edit_sitemap" value="*" autocomplete="off" checked> <?php echo $text_discard; ?></label></li>
              <li role="separator" class="divider"></li>
              <li><label><input type="radio" name="edit_sitemap" value="1" autocomplete="off"> <?php echo $text_enabled; ?></label></li>
              <li><label><input type="radio" name="edit_sitemap" value="0" autocomplete="off"> <?php echo $text_disabled; ?></label></li>
            </ul>
          </div>                              
        </div>                       
      </div><!-- /edit -->
    </div><!-- /.col -->       
    
    <div class="col-xs-12 col-lg-2">
      <div class="hidden-lg mt-2"></div>
      
      <div class="input-group">                                                         
        <div class="input-group-addon"><?php echo $text_target; ?></div>
        <select name="edit_target" class="form-control" data-selected="<?php echo $edit_target; ?>">
          <option value="all"><?php echo $text_target_all; ?></option>
          <option value="filter"><?php echo $text_target_filter; ?></option>
          <option value="selected"><?php echo $text_target_selected; ?></option>
        </select>                
      </div>
    </div>                  
  </div><!-- /.row -->

  <div class="text-right mt-2">
    <button type="button" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" onclick="submitEditForm(this);"><?php echo $button_edit_pages; ?></button>
  </div>                
</form>

<script>
var

setEditControlVisibled = function() {
  if ($(this).attr('name') == 'edit_action') {
    $('#form-edit').find('[tool-add], [tool-replace], [tool-update]').addClass('hidden').filter('[tool-' + this.value + ']').removeClass('hidden');
  }
  
  if ($(this).attr('name') == 'edit_target') {
    $('#form-list-filter').find('[onclick^="submitEditForm"]').toggleClass('hidden', this.value != 'filter');  
  }  
},

submitEditForm = function(button) {
  $(button).button('loading');
  
  var $form = $('#form-edit'), target = $form.find('select[name="edit_target"]').val();
  
  if (target == 'filter') {
    var html = '<div style="display: none;">';
    
    $('#form-list-filter').find('input, select').filter('[name^="filter_"]').each(function() {
      $filter = $(this);

      if ($filter.val()) {
        if (($filter.is('input') && $filter.val()) || ($filter.is('select') && $filter.val() != '*')) {          
          html += '<input type="hidden" name="' + $filter.attr('name') + '" value="' + $filter.val() + '" />';
        }
      }
    });
    
    html += '</div>';
      
    $form.append(html);
  }
  
  if (target == 'selected') {
    var html = '<div style="display: none;">';
    
    $('#form-list').find('input[name="selected[]"]:checked').each(function() {
      html += '<input type="hidden" name="selected[]" value="' + $(this).val() + '" />';
    });
  
    html += '</div>';
    
    $form.append(html);
  }
  
  $form.submit();
};

ocfDOMReady(function() {
$(function() {
  $('#form-edit').find('select[name="edit_action"], select[name="edit_target"]').on('change', setEditControlVisibled);

  $('input[name="edit_category_name"]').autocomplete({
    'source': function(request, response) {
      var $this = $(this);
      
      $.ajax({
        url: ocfilter.link('catalog/category/autocomplete', { 'filter_name': request }),
        dataType: 'json',
        success: function(json) {   
          json.unshift({
            name: '-- <?php echo addslashes($text_discard); ?> --',
            category_id: '*'
          });   
        
          response($.map(json, function(item) {
            return {
              label: item.name,
              value: item.category_id
            }
          }));       
        }
      });
    },
    'select': function(item) {
      $(this).val('').attr('placeholder', item.label);
      
      $('input[name="edit_category_id"]').val(item.value);
    }
  });

  $('[tool-update]').on('change', '.dropdown-menu input[type="radio"]', function(e) {
    var $this = $(this), $dropdown = $this.closest('.dropdown'), $btn = $dropdown.find('[data-toggle="dropdown"]');
    
    $btn.toggleClass('btn-info', $this.val() == 1).toggleClass('active', $this.val() != '*');
  });
});
});  
</script>