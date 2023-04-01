<button type="button" onclick="addFilterValue(1);" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $button_add_value; ?></button>
<hr />
<ul class="list-group" id="filter-value-list">
  <?php $filter_value_row = 0; ?>
  <?php foreach ($filter_value as $key => $value) { ?>
  <li class="list-group-item">
    <input type="hidden" name="filter_value[<?php echo $filter_value_row; ?>][value_id]" value="<?php echo $value['value_id']; ?>" />
    <input type="hidden" name="filter_value[<?php echo $filter_value_row; ?>][image]" value="<?php echo $value['image']; ?>" id="value-image-<?php echo $value['value_id']; ?>" />
    <input type="hidden" name="filter_value[<?php echo $filter_value_row; ?>][color]" value="<?php echo $value['color']; ?>" />

    <div class="row">
      <div class="col-sm-1">
        <input type="number" name="filter_value[<?php echo $filter_value_row; ?>][sort_order]" value="<?php echo $value['sort_order']; ?>" class="form-control" placeholder="<?php echo $entry_sort_order; ?>" />
      </div>
      <div class="col-sm-9">
        <?php foreach ($languages as $language) { ?>
        <div class="input-group">
          <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
          <input type="text" class="form-control" name="filter_value[<?php echo $filter_value_row; ?>][description][<?php echo $language['language_id']; ?>][name]" value="<?php echo (isset($value['description'][$language['language_id']]) ? $value['description'][$language['language_id']]['name'] : ''); ?>" />
        </div>                  
        <?php } ?>    
        <?php if (isset($error_filter_value[$key]['name'])) { ?>
        <div class="text-danger"><?php echo $error_filter_value[$key]['name']; ?></div>
        <?php } ?>                             
      </div>
      <div class="col-sm-2 text-right">
        <div class="btn-group" role="group">
          <button type="button" class="btn btn-default color-handler<?php echo (!$color ? ' disabled' : ''); ?>" title="<?php echo $text_select_color; ?>"<?php echo ($value['color'] ? ' style="background: #' . $value['color'] . ';"' : ''); ?>><i class="fa fa-fw fa-eyedropper"></i></button>
          <button type="button" class="btn btn-default image-handler<?php echo (!$image ? ' disabled' : '') . ($value['image'] ? ' inserted' : ''); ?>" title="<?php echo $text_browse_image; ?>" id="value-image-thumb-<?php echo $value['value_id']; ?>"><img src="<?php echo $value['thumb']; ?>" alt="" /><i class="fa fa-fw fa-picture-o"></i></button>
          <button type="button" class="btn btn-default" onclick="removeFilterValue(this);"><i class="fa fa-fw fa-times text-danger"></i></button>
        </div>
      </div>                                        
    </div>                            
  </li>
  <?php $filter_value_row++; ?>
  <?php } ?>
</ul>
<hr />
<button type="button" onclick="addFilterValue(0);" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo $button_add_value; ?></button>

<script id="template-filter-row" type="text/x-handlebars-template">
<li class="list-group-item list-group-item-info">
  <input type="hidden" name="filter_value[{row}][value_id]" value="" />
  <input type="hidden" name="filter_value[{row}][image]" value="" id="value-image-add-{row}" />
  <input type="hidden" name="filter_value[{row}][color]" value="" />

  <div class="row">
    <div class="col-sm-1">
      <input type="number" name="filter_value[{row}][sort_order]" value="" class="form-control" placeholder="<?php echo $entry_sort_order; ?>" />
    </div>
    <div class="col-sm-9">
      <?php foreach ($languages as $language) { ?>
      <div class="input-group">
        <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
        <input type="text" class="form-control" name="filter_value[{row}][description][<?php echo $language['language_id']; ?>][name]" value="" />
      </div>                 
      <?php } ?>                  
    </div>
    <div class="col-sm-2 text-right">
      <div class="btn-group" role="group">
        <button type="button" class="btn btn-default color-handler{color}" title="<?php echo $text_select_color; ?>"><i class="fa fa-fw fa-eyedropper"></i></button>
        <button type="button" class="btn btn-default image-handler{image}" title="<?php echo $text_browse_image; ?>" id="value-image-add-thumb-{row}"><img src="<?php echo $no_image; ?>" alt="" /><i class="fa fa-fw fa-picture-o"></i></button>
        <button type="button" class="btn btn-default" onclick="removeFilterValue(this);"><i class="fa fa-fw fa-times text-danger"></i></button>
      </div>
    </div>                                        
  </div> 
</li>
</script>
<script>
var
  colorboxHTML = '',
  colors = [
    'f00', 'ff0', '0f0', '0ff', '00f', 'f0f', 'fff', 
    'ebebeb', 'e1e1e1', 'd7d7d7', 'cccccc', 'c2c2c2', 'b7b7b7', 'acacac', 'a0a0a0', '959595', 'ee1d24', 'fff100', '00a650', '00aeef', '2f3192', 
    'ed008c', '898989', '7d7d7d', '707070', '626262', '555', '464646', '363636', '262626', '111', '000', 'f7977a', 'fbad82', 'fdc68c', 'fff799', 
    'c6df9c', 'a4d49d', '81ca9d', '7bcdc9', '6ccff7', '7ca6d8', '8293ca', '8881be', 'a286bd', 'bc8cbf', 'f49bc1', 'f5999d', 'f16c4d', 'f68e54', 
    'fbaf5a', 'fff467', 'acd372', '7dc473', '39b778', '16bcb4', '00bff3', '438ccb', '5573b7', '5e5ca7', '855fa8', 'a763a9', 'ef6ea8', 'f16d7e', 
    'ee1d24', 'f16522', 'f7941d', 'fff100', '8fc63d', '37b44a', '00a650', '00a99e', '00aeef', '0072bc', '0054a5', '2f3192', '652c91', '91278f', 
    'ed008c', 'ee105a', '9d0a0f', 'a1410d', 'a36209', 'aba000', '588528', '197b30', '007236', '00736a', '0076a4', '004a80', '003370', '1d1363', 
    '450e61', '62055f', '9e005c', '9d0039', '790000', '7b3000', '7c4900', '827a00', '3e6617', '045f20', '005824', '005951', '005b7e', '003562', 
    '002056', '0c004b', '30004a', '4b0048', '7a0045', '7a0026'
  ];

colorboxHTML += '<div class="ocf-colorbox">';

for (var i = 0; i < colors.length; i++) {
  colorboxHTML += '<a href="#' + colors[i] + '" style="background-color: #' + colors[i] + ';"></a>';
}

colorboxHTML += '</div>';

var 

filterValueRow = <?php echo $filter_value_row; ?>,

addFilterValue = function(before) {
  var 
    html = '',
    color = ($('input[name="color"][value="1"]').prop('checked') ? '' : ' disabled'), 
    image = ($('input[name="image"][value="1"]').prop('checked') ? '' : ' disabled'); 
  
  html = $('#template-filter-row').html().replace(/{row}/g, filterValueRow).replace(/{color}/g, color).replace(/{image}/g, image);
  
  if (before) {
    $('#filter-value-list').prepend(html);
  } else {
    $('#filter-value-list').append(html);
  }
  
  filterValueRow++;
},

removeFilterValue = function(btn) {
  $(btn).closest('.list-group-item').remove();
},

showColorBox = function($btn) {
  if ($btn.hasClass('disabled')) {
    return;
  }
  
  function showPopover() {
    $btn.popover({
      html: true,
      placement: 'left',
      container: $btn.closest('li.list-group-item'),
      trigger: 'manual',
      content: colorboxHTML,
    }).popover('show');  
  }

  if ($('.popover').length) {
    $('[aria-describedby="' + $('.popover').attr('id') + '"]').popover('hide').one('hidden.bs.popover', showPopover);
  } else {
    showPopover();
  }
},

setValueColor = function($color) {
  var color = $color.attr('href').substr(1), $valueRow = $color.closest('li.list-group-item');

  $valueRow.find('input[name$="[color]"]').val(color);
  $valueRow.find('.color-handler').css('background', '#' + color);

  return false;
},

showImageExplorer = function($btn) {
  if ($btn.hasClass('disabled')) {
    return;
  }

  function showPopover() { 
    $btn.popover({
      html: true,
      placement: 'left',
      container: $valueRow,
      trigger: 'manual',
      content: function() {
        return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
      }
    }).popover('show');
  }
  
  if ($('.popover').length) {
    $('[aria-describedby="' + $('.popover').attr('id') + '"]').popover('hide').one('hidden.bs.popover', showPopover);
  } else {
    showPopover();
  }

  var $valueRow = $btn.closest('li.list-group-item');

  $(document).off('.image-manager').on('click.image-manager', '#button-image', function() {
    $('#modal-image').remove();

    $.ajax({
      url: ocfilter.link('common/filemanager', { target: $valueRow.find('input[name$="[image]"]').attr('id'), thumb: $btn.attr('id') }),
      dataType: 'html',
      beforeSend: function() {
        $('#button-image').prop('disabled', true).find('i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
      },
      complete: function() {
        $('#button-image').prop('disabled', false).find('i').replaceWith('<i class="fa fa-pencil"></i>');
      },
      success: function(html) {
        $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

        $('#modal-image').modal('show');
      }
    });

    $btn.popover('hide', function() {
      $('.popover').remove();
    });
  }).on('click.image-manager', '#button-clear', function() {
    $valueRow.find('input[name$="[image]"]').val('');

    $btn.popover('hide', function() {
      $('.popover').remove();
    }).find('img').attr('src', '<?php echo $no_image; ?>');
  });
};

$(function() {
  $('#filter-value-list').on('click', function(e) {
    if ($(e.target).closest('button.color-handler').length) {
      return showColorBox($(e.target).closest('button.color-handler'));
    }
  
    if ($(e.target).closest('button.image-handler').length) {
      return showImageExplorer($(e.target).closest('button.image-handler'));
    }
    
    if ($(e.target).parent('.ocf-colorbox').length) {
      e.preventDefault();
    
      return setValueColor($(e.target));
    }    
  });

  $('input[name="color"]').on('change', function() {
    $('.color-handler').toggleClass('disabled', !($(this).val() && $(this).prop('checked')));
  });

  $('input[name="image"]').on('change', function() {
    $('.image-handler').toggleClass('disabled', !($(this).val() && $(this).prop('checked')));
  });
});
</script>