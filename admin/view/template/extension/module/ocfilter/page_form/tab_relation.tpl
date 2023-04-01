<div class="form-group required">
  <label class="col-sm-3 control-label" for="input-category"><?php echo $entry_category; ?></label>
  <div class="col-sm-9">
    <input type="text" name="category_name" value="" placeholder="<?php echo $category_name ? $category_name : $entry_category; ?>" id="input-category" class="form-control" required />
    <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
    <?php if ($error_category) { ?>
    <div class="text-danger"><?php echo $error_category; ?></div>
    <?php } ?>
  </div>
</div>

<div class="form-group required">
  <label class="col-md-3 control-label sticky-md-top">
    <?php echo $entry_filter; ?>
    <div class="help-block text-left"><?php echo $help_ocfilter_filter; ?></div>
  </label>
  <div class="col-md-9">              
    <?php if ($error_filter) { ?>
    <div class="text-danger"><?php echo $error_filter; ?></div>
    <?php } ?>     
    <div class="well well-sm" id="filter-relation-content"></div>                 
  </div>
</div>
<script> 
ocfDOMReady(function() {
$(function() {
  $('input[name="category_name"]').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: ocfilter.link('catalog/category/autocomplete', 'filter_name=' + request),
        dataType: 'json',
        success: function(json) {
          json.unshift({
            category_id: 0,
            name: '<?php echo $text_none; ?>'
          });

          response($.map(json, function(item) {
            return {
              label: item['name'],
              value: item['category_id']
            }
          }));
        }
      });
    },
    'select': function(item) {
      $('input[name="category_name"]').val('').attr('placeholder', item['label']);
      
      $('input[name="category_id"]').val(item['value']).trigger('change');
      
      ocfilter.getRelationForm($.extend({}, relationFormOptions, { category_id: item['value'] }));
    }
  });
    
  // Get filters relation form
  ocfilter.getRelationForm(relationFormOptions);    
});
});
</script>