<div class="form-group required">
  <label class="col-sm-3 control-label"><?php echo $entry_name; ?></label>
  <div class="col-sm-9">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
      <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
      <input type="text" name="filter_description[<?php echo $language['language_id']; ?>][name]" maxlength="128" value="<?php echo isset($filter_description[$language['language_id']]) ? $filter_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
    </div>
    <?php if (isset($error_name[$language['language_id']])) { ?>
    <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_category; ?></label>
  <div class="col-sm-9">
    <div class="input-group">                  
      <input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
      <div class="input-group-btn">
        <button type="button" class="btn btn-primary" data-ocf="category"><?php echo $button_show_all; ?> <i class="fa fa-flip-horizontal fa-object-ungroup"></i></button>
      </div>      
      <div class="input-group-addon">                    
        <i class="fa fa-bars"></i>
      </div>
    </div>
    <div id="filter-category" class="well well-sm" style="height: 150px; overflow: auto;">
      <?php foreach ($filter_category as $category_id => $_name) { ?>
      <div id="filter-category-<?php echo $category_id; ?>"><i class="fa fa-minus-circle"></i> <?php echo $_name; ?>
        <input type="hidden" name="filter_category[<?php echo $category_id; ?>]" value="<?php echo $_name; ?>" />
      </div>
      <?php } ?>
    </div>
  </div>
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_type; ?></label>
  <div class="col-sm-3">
    <select name="type" class="form-control" onchange="$('#collapse-type-text').collapse(this.value == 'checkbox' || this.value == 'radio' ? 'show' : 'hide');">
      <?php foreach ($type_items as $key => $item) { ?>
      <?php if ($type == $key) { ?>
      <option value="<?php echo $key; ?>" selected="selected"><?php echo $item; ?></option>
      <?php } else { ?>
      <option value="<?php echo $key; ?>"><?php echo $item; ?></option>
      <?php } ?>
      <?php } ?>
    </select>
  </div>
</div>
<?php if ($type == 'checkbox' || $type == 'radio') { ?>
<?php $class = 'collapse in'; ?>
<?php } else { ?>
<?php $class = 'collapse'; ?>
<?php } ?>
<div class="<?php echo $class; ?>" id="collapse-type-text">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_dropdown; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('dropdown', $dropdown, 'y/n'); ?>
    </div>
  </div>                 
  
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_is_image; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('image', $image, 'y/n'); ?>
    </div>
  </div>                

  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_is_color; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('color', $color, 'y/n'); ?>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
  <div class="col-sm-3">
    <input type="number" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('status', $status, 'e/d'); ?>
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
};

ocfDOMReady(function() {
$(function() {
  // Category
  $('input[name="category"]').autocomplete({     
    'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
     
      $.ajax({
        url: ocfilter.link('catalog/category/autocomplete', 'filter_name=' +  request),
        dataType: 'json',
        success: function(json) {
          $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
        
          json.unshift({
            name: '{{ text_all }}',
            category_id: 0
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
      $('#filter-category-' + item.value).remove();

      if (item.value < 1) {
        $('#filter-category').html('');
      } else if ($('#filter-category-0').length) {
        $('#filter-category-0').remove();
      }

      $('#filter-category').append('<div id="filter-category-' + item.value + '"><i class="fa fa-minus-circle"></i> ' + item.label + '<input type="hidden" name="filter_category[' + item.value + ']" value="' + item.label + '" /></div>');

      $('input[name="category"]').val('');
    }
  });

  $('#filter-category').on('click', '.fa-minus-circle', function() {
    $(this).parent().remove();
  });  
  
  $('[data-ocf="category"]').on('click', function(e) {
    $('#modal-category-list').remove();

    $.get(ocfilter.link('extension/module/ocfilter/filter/modalCategory', 'filter_key={{ filter_key }}&target=filter-category'), {}, function(response) {
      if (response) {
        $('body').append(response);

        $('#modal-category-list').modal('show');
      }
    }, 'html');
  });  
});  
}); // DOM Ready
</script>