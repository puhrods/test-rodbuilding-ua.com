<div class="well" id="form-list-filter">  
  <div class="form-group">
    <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
    <div class="input-group">
      <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
      <?php if ($filter_name) { ?>
      <div class="input-group-btn">                    
        <button type="button" class="btn btn-danger" onclick="$('[name=\'filter_name\']').val(''); filterList();"><i class="fa fa-times"></i></button>                    
      </div>
      <?php } else { ?>
      <div class="input-group-addon">                    
        <i class="fa fa-bars"></i>
      </div>
      <?php } ?>
    </div>               
  </div>
  <div class="form-group">
    <label class="control-label" for="input-filter-category"><?php echo $entry_category; ?></label>
    <div class="input-group">
      <input type="text" name="filter_category" value="" placeholder="<?php echo ($filter_category ? $filter_category : $entry_category); ?>" id="input-filter-category" class="form-control" />
      <input type="hidden" name="filter_category_id" value="<?php echo $filter_category_id; ?>" />                 
      <?php if ($filter_category_id) { ?>
      <div class="input-group-btn">                    
        <button type="button" class="btn btn-danger" onclick="$('[name=\'filter_category_id\'],[name=\'filter_category\']').val(''); filterList();"><i class="fa fa-times"></i></button>                
      </div>
      <?php } else { ?>
      <div class="input-group-addon">                    
        <i class="fa fa-bars"></i>
      </div>
      <?php } ?>
    </div>                    
  </div> 
  <div class="form-group">
    <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
    <div class="input-group">                
      <select name="filter_status" id="input-status" class="form-control">
        <option value="*"></option>
        <?php if ($filter_status) { ?>
        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
        <?php } else { ?>
        <option value="1"><?php echo $text_enabled; ?></option>
        <?php } ?>
        <?php if (!is_null($filter_status) && !$filter_status) { ?>
        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
        <?php } else { ?>
        <option value="0"><?php echo $text_disabled; ?></option>
        <?php } ?>
      </select>
      <?php if (!is_null($filter_status)) { ?>
      <div class="input-group-btn"> 
        <button type="button" class="btn btn-danger" onclick="$('[name=\'filter_status\']').val('*'); filterList();"><i class="fa fa-times"></i></button>
      </div>
      <?php } else { ?>
      <div class="input-group-addon">
        <i class="fa fa-asterisk"></i>
      </div> 
      <?php } ?>
    </div>
  </div>
  <div class="form-group text-right">                
    <button type="button" onclick="filterList(this);" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
    <button type="button" onclick="submitEditForm(this);" class="btn btn-primary hidden" data-loading-text="<?php echo $text_loading; ?>"><?php echo $button_edit_pages; ?></button>
  </div>
<script>

var 

filterList = function(button) {
  button && $(button).button('loading');

  var url = ocfilter.link('extension/module/ocfilter/page'), $filter;

  $('#form-list-filter').find('input, select').filter('[name^="filter_"]').each(function() {
    $filter = $(this);

    if ($filter.val()) {
      if (($filter.is('input') && $filter.val()) || ($filter.is('select') && $filter.val() != '*')) {
        url += '&' + $filter.attr('name') + '=' + encodeURIComponent($filter.val());
      }
    }
  });

  window.location = url;
};

ocfDOMReady(function() {
$(function() {
  $('#form-list-filter').find('input').on('keydown', function(e) {
    (e.which == 13) && filterList();
  });  
  
  $('#form-list-filter input[name="filter_name"]').autocomplete({
    'placement': 'right',
    'source': function(request, response) {    
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
    
      $.ajax({
        url: ocfilter.link('extension/module/ocfilter/page/autocomplete', { 'filter_name': request }),
        dataType: 'json',
        success: function(json) {                  
          response($.map(json, function(item) {
            return {
              label: item.name,
              value: item.page_id,
              category: item.category
            }
          }));  

          $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
        }
      });
    },
    'select': function(item) {
      $(this).val(item.label);
    }
  });

  $('#form-list-filter input[name="filter_category"]').autocomplete({
    'placement': 'right',
    'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
      
      $.ajax({
        url: ocfilter.link('catalog/category/autocomplete', { 'filter_name': request }),
        dataType: 'json',
        success: function(json) {                 
          response($.map(json, function(item) {
            return {
              label: item.name,
              value: item.category_id
            }
          }));
          
          $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');       
        }
      });
    },
    'select': function(item) {
      $(this).val('').attr('placeholder', item.label);
      
      $('#form-list-filter input[name="filter_category_id"]').val(item.value);
    }
  });  
});
});
</script>
</div>