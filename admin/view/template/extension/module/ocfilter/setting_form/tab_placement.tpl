<div class="alert alert-info" role="alert"><?php echo $text_placement; ?></div>
<div class="row" id="placement-content">
  <div class="col-sm-3">
    <div class="jumbotron" style="padding: 15px;">
      <ul class="nav nav-pills nav-stacked">
        <li role="presentation"><a href="#" onclick="addModulePlace(); return false;"><i class="fa fa-plus-circle"></i> <?php echo $button_add_placement; ?></a></li>
        <?php $placement_row = 0; ?>
        <?php foreach ($placement_layout as $place) { ?>
        <li role="presentation"><a href="#tab-place-<?php echo $placement_row; ?>" role="tab" data-toggle="tab"><span class="pull-right" data-toggle="tooltip" data-placement="top" title="<?php echo $button_remove_placement; ?>" onclick="removeModulePlace('<?php echo $placement_row; ?>');"><i class="fa fa-lg fa-times-circle"></i></span> <?php echo $place['layout_name']; ?></a></li>
        <?php $placement_row++; ?>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="col-sm-9">
    <div class="tab-content">
      <?php $placement_row = 0; ?>
      <?php foreach ($placement_layout as $place) { ?>
      <div role="tabpanel" class="tab-pane" id="tab-place-<?php echo $placement_row; ?>">
        <div class="form-group">
          <label class="col-sm-3 control-label"><?php echo $entry_placement_layout; ?></label>
          <div class="col-sm-5">
            <select name="placement_layout[<?php echo $placement_row; ?>][layout_id]" class="form-control" data-row="<?php echo $placement_row; ?>">
              <option value=""><?php echo $text_select; ?></option>
              <?php foreach ($layouts as $layout) { ?>
              <?php if ($layout['layout_id'] == $place['layout_id']) { ?>
              <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $layout['layout_id']; ?>" <?php echo $layout['disabled'] ? 'disabled="disabled"' : ''; ?>><?php echo $layout['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label"><?php echo $entry_placement_filter; ?></label>
          <div class="col-sm-9">
            <div class="input-group">
              <input type="text" name="autocomplete_filter[<?php echo $placement_row; ?>]" value="" placeholder="<?php echo $placeholder_autocomplete; ?>" class="form-control" />
              <div class="input-group-addon"><i class="fa fa-bars"></i></div>
            </div>            
            <div class="well well-sm" id="placement-filter-<?php echo $placement_row; ?>" style="height: 150px; overflow: auto;">
              <?php foreach ($place['filters'] as $filter) { ?>
              <div id="placemenet-filter-<?php echo $placement_row; ?>-<?php echo $filter['filter_id']; ?>-<?php echo $filter['source']; ?>">
                <i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> <?php echo $filter['name']; ?>
                <input type="hidden" name="placement_layout[<?php echo $placement_row; ?>][filters][]" value="<?php echo $filter['filter_key']; ?>" />
              </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php $placement_row++; ?>
      <?php } ?>
    </div>
  </div>
</div>
<script id="template-placement-row" type="text/x-handlebars-template">
<div role="tabpanel" class="tab-pane" id="tab-place-{row}">
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_placement_layout; ?></label>
    <div class="col-sm-5">
      <select name="placement_layout[{row}][layout_id]" class="form-control" data-row="{row}">
        <option value=""><?php echo $text_select; ?></option>
        <?php foreach ($layouts as $layout) { ?>
        <option value="<?php echo $layout['layout_id']; ?>" <?php echo $layout['disabled'] ? 'disabled="disabled"' : ''; ?>><?php echo $layout['name']; ?></option>
        <?php } ?>
      </select>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-3 control-label"><?php echo $entry_placement_filter; ?></label>
    <div class="col-sm-9">
      <div class="input-group">
        <input type="text" name="autocomplete_filter[{row}]" value="" placeholder="<?php echo $placeholder_autocomplete; ?>" class="form-control" data-placement-row="{row}" />
        <div class="input-group-addon"><i class="fa fa-bars"></i></div>
      </div>       
      <div class="well well-sm" id="placement-filter-{row}" style="height: 150px; overflow: auto;"></div>
    </div>
  </div>
</div>
</script>
<script>
var

ocfDOMReady = function(fn) {
  if (document.readyState != 'loading') {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
},

placement_row = <?php echo $placement_row; ?>,

addModulePlace = function() {
  $('#placement-content .tab-content').prepend($('#template-placement-row').html().replace(/\{row\}/g, placement_row));

  $('#placement-content .nav.nav-pills').find('> li:eq(0)').after('<li role="presentation"><a href="#tab-place-' + placement_row + '" role="tab" data-toggle="tab"><span class="pull-right" data-toggle="tooltip" data-placement="top" title="<?php echo $button_remove_placement; ?>" onclick="removeModulePlace(\'' + placement_row + '\');"><i class="fa fa-lg fa-times-circle"></i></span> <?php echo $text_new_placement; ?></a></li>');
  $('#placement-content .nav.nav-pills').find('> li:eq(1) > a').tab('show');

  $('a[href="#tab-place-' + placement_row + '"] [data-toggle="tooltip"]').tooltip({ container: 'body', html: true });

  filterAutocomplete(placement_row);

  placement_row++;
},

removeModulePlace = function(placement_row) {
  $('#tab-place-' + placement_row + ', a[href="#tab-place-' + placement_row + '"]').remove();

  let $tabs = $('a[href^="#tab-place-"]');

  if ($tabs.length > 0) {
    $tabs.filter(':first').tab('show');
  }
},

filterAutocomplete = function(placement_row) {
	$('input[name="autocomplete_filter[' + placement_row + ']"]').autocomplete({  
		'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');  
    
			$.ajax({
				url: ocfilter.link('extension/module/ocfilter/filter/autocompleteFilters', 'filter_status=1&filter_name=' +  request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							category: item.category,
							label: item.name,
							value: item.filter_key,
              filter_id: item.filter_id,
              source: item.source,
						}
					}));
				}
			});
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
		},
		'select': function(item) {
			$('input[name="autocomplete_filter[' + placement_row + ']"]').val('');

      $('#placement-filter-' + placement_row).append('<div id="placemenet-filter-' + placement_row + '-' + item['filter_id'] + '-' + item['source'] + '"><i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> ' + item['label'] + '<input type="hidden" name="placement_layout[' + placement_row + '][filters][]" value="' + item['value'] + '" /></div>');
		}
	});
};

ocfDOMReady(function() {
  $(function() {
    $('#placement-content .tab-pane').each(function(index, element) {
       filterAutocomplete(index);
    });

    $('#placement-content').on('change', 'select[name$="[layout_id]"]', function() {
      let $tab = $('a[href="#tab-place-' + $(this).data().row + '"]');

      $tab.html($tab.html().replace($tab.text(), '') + $(this).find('option[value="' + $(this).val() + '"]').text());
    });

    $('#placement-content .nav.nav-pills').find('> li:eq(1) > a').tab('show');
  });
});
</script>