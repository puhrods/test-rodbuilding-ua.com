<div class="well" id="form-list-filter">      
  <div class="form-group">
    <label class="control-label" for="input-name"><?php echo $entry_filter_name; ?></label>
    <div class="input-group">
      <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_filter_name; ?>" id="input-name" class="form-control" />
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
    <label class="control-label" for="input-forum-group"><?php echo $entry_category; ?></label>
    <div class="input-group">
      <input type="text" name="filter_category" value="" placeholder="<?php echo ($filter_category ? $filter_category : $entry_category); ?>" id="input-forum-group" class="form-control" />
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
    <label class="control-label" for="input-type"><?php echo $entry_type; ?></label>
    <div class="input-group">    
      <select name="filter_type" id="input-type" class="form-control">
        <option value="*"></option>
        <?php if ($filter_type == 'checkbox') { ?>
        <option value="checkbox" selected="selected"><?php echo $text_checkbox; ?></option>
        <?php } else { ?>
        <option value="checkbox"><?php echo $text_checkbox; ?></option>
        <?php } ?>
        <?php if ($filter_type == 'radio') { ?>
        <option value="radio" selected="selected"><?php echo $text_radio; ?></option>
        <?php } else { ?>
        <option value="radio"><?php echo $text_radio; ?></option>
        <?php } ?>
        <?php if ($filter_type == 'slide') { ?>
        <option value="slide" selected="selected"><?php echo $text_slide; ?></option>
        <?php } else { ?>
        <option value="slide"><?php echo $text_slide; ?></option>
        <?php } ?>
        <?php if ($filter_type == 'slide_dual') { ?>
        <option value="slide_dual" selected="selected"><?php echo $text_slide_dual; ?></option>
        <?php } else { ?>
        <option value="slide_dual"><?php echo $text_slide_dual; ?></option>
        <?php } ?>                  
      </select>
      <?php if ($filter_type) { ?>
      <div class="input-group-btn"> 
        <button type="button" class="btn btn-danger" onclick="$('[name=\'filter_type\']').val('*'); filterList();"><i class="fa fa-times"></i></button>
      </div>
      <?php } else { ?>
      <div class="input-group-addon">
        <i class="fa fa-asterisk"></i>
      </div> 
      <?php } ?>
    </div>
  </div>
  <div class="form-group">
    <label class="control-label" for="input-source"><?php echo $entry_source; ?></label>
    <div class="input-group">    
      <select name="filter_source" id="input-type" class="form-control">
        <option value="*"></option>
        <?php if ($filter_source == 'default') { ?>
        <option value="default" selected="selected"><?php echo $text_source_default; ?></option>
        <?php } else { ?>
        <option value="default"><?php echo $text_source_default; ?></option>
        <?php } ?>
        <?php if ($filter_source == 'attribute') { ?>
        <option value="attribute" selected="selected"><?php echo $text_source_attribute; ?></option>
        <?php } else { ?>
        <option value="attribute"><?php echo $text_source_attribute; ?></option>
        <?php } ?>
        <?php if ($filter_source == 'filter') { ?>
        <option value="filter" selected="selected"><?php echo $text_source_filter; ?></option>
        <?php } else { ?>
        <option value="filter"><?php echo $text_source_filter; ?></option>
        <?php } ?>
        <?php if ($filter_source == 'option') { ?>
        <option value="option" selected="selected"><?php echo $text_source_option; ?></option>
        <?php } else { ?>
        <option value="option"><?php echo $text_source_option; ?></option>
        <?php } ?>                  
      </select>
      <?php if ($filter_source) { ?>
      <div class="input-group-btn"> 
        <button type="button" class="btn btn-danger" onclick="$('[name=\'filter_source\']').val('*'); filterList();"><i class="fa fa-times"></i></button>
      </div>
      <?php } else { ?>
      <div class="input-group-addon">
        <i class="fa fa-asterisk"></i>
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
    <button type="button" onclick="filterList();" class="btn btn-primary"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
  </div>
</div>
<script>

var

ocfDOMReady = function(fn) {
  if (document.readyState != 'loading') {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
},

filterList = function(button) {
  button && $(button).button('loading');

  var url = ocfilter.link('extension/module/ocfilter/filter'), $filter;

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

  $('input[name="filter_name"]').autocomplete({
    'placement': 'right',  
    'source': function(request, response) {    
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
    
      $.ajax({
        url: ocfilter.link('extension/module/ocfilter/filter/autocompleteFilters', 'filter_name=' + request),
        dataType: 'json',
        success: function(json) {        
          response($.map(json, function(item) {
            return {
              label: item.name,
              value: item.filter_id,
              category: item.category
            }
          }));
          
          $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
        }
      });
    },
    'select': function(item) {
      $('input[name="filter_name"]').val(item.label);
    }
  });

  $('input[name="filter_category"]').autocomplete({
    'placement': 'right',
    'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
      
      $.ajax({
        url: ocfilter.link('catalog/category/autocomplete', 'filter_name=' + request),
        dataType: 'json',
        success: function(json) {
          json.unshift({
            name: '<?php echo $text_without_category; ?>',
            category_id: -1
          });
          
          json.unshift({
            name: '<?php echo $text_all; ?>',
            category_id: 0
          });          

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
      $('input[name="filter_category"]').val('').attr('placeholder', item.label);
      $('input[name="filter_category_id"]').val(item.value);
    }
  });
});
}); // DOM Ready
</script>