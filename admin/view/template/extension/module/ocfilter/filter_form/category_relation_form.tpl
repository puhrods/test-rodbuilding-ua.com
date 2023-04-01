<div class="modal fade" id="modal-category-list" tabindex="-1" role="dialog" aria-labelledby="modal-category-list" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="input-group">
          <label class="input-group-addon cursor-pointer">
            <input type="checkbox" id="input-check-all-category" /> <?php echo $text_select_all; ?>
          </label>
          <div class="input-group-btn" data-toggle="buttons">
            <label class="btn btn-primary">
              <input type="checkbox" name="level[]" value="0" /> 1
            </label>
            <label class="btn btn-primary">
              <input type="checkbox" name="level[]" value="1" /> 2
            </label>
            <label class="btn btn-primary">
              <input type="checkbox" name="level[]" value="2" /> 3
            </label>
            <label class="btn btn-primary">
              <input type="checkbox" name="level[]" value=">2" /> > 3
            </label>
          </div>
          <input type="text" name="search_category" value="" class="form-control" placeholder="<?php echo $placeholder_category_search; ?>" />
          <div class="input-group-btn">
            <button type="button" class="btn btn-default" data-toggle="cancel-search"><i class="fa fa-search"></i></button>
          </div>
        </div>
      </div>
      <div class="modal-body">
        <?php if ($categories) { ?>
        <div class="list-group modal-category-list">
          <?php foreach ($categories as $key => $category) { ?>
          <?php $next_level = isset($categories[$key + 1]) ? $categories[$key + 1]['level'] : 0; ?>

          <?php if ($category['level'] < 1) { ?>
          <?php $class = 'list-group-item list-group-item-info'; ?>
          <?php } else { ?>
          <?php $class = 'list-group-item'; ?>
          <?php } ?>

          <?php $class .= ' level-' . $category['level']; ?>

          <?php if ($category['level'] > 2) { ?>
          <?php $class .= ' level-more-than-2'; ?>
          <?php } ?>

          <?php if ($next_level > $category['level']) { ?>
          <?php $class .= ' has-child'; ?>
          <?php } ?>

          <label class="<?php echo $class; ?>" data-label="<?php echo $category['path_name']; ?>">
            <input type="checkbox" name="category_id[]" value="<?php echo $category['category_id']; ?>" <?php echo (in_array($category['category_id'], $selected) ? 'checked' : ''); ?> />&nbsp;&nbsp;<span class="ocf-category-name"><?php echo $category['name']; ?></span>
            <?php if ($next_level > $category['level']) { ?>
            <span class="pull-right">
              <i class="fa fa-level-down"></i>
              <input type="checkbox" name="select_sublevel" value="<?php echo $category['level']; ?>" />
            </span>          
            <?php } ?>
          </label>
          <?php } ?>
        </div>
        <?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
        <button type="button" class="btn btn-primary" data-save="category"><?php echo $button_save; ?></button>
      </div>
    </div>
  </div>
<script>
// Search categories
// onkeyup | onchange
var searchCategories = function(e) {
  var
    $search = $(this),
    $categoryBody = $search.data().categoryBody,
    $categoryItems = $search.data().categoryItems,
    $btnCancel = $search.data().btnCancel,
    keyword = $search.val();

  if (!$categoryItems || $categoryItems.length < 1) {
    return;
  }

  $btnCancel.find('.fa').attr('class', 'fa fa-search');
  $categoryBody.removeClass('hidden');
  $categoryItems.removeClass('hidden').find('.ocf-found-match').contents().unwrap();

  if (keyword) {
    var $found = $categoryItems.filter(function() {
      return ($(this).find('.ocf-category-name:icontains(\'' + keyword + '\')').length > 0);
    });

    if ($found.length > 0) {
      var $categoryText;

      $found.each(function(i) {
        $categoryText = $(this).find('.ocf-category-name');

        $categoryText.html($categoryText.html().replace(new RegExp(keyword, 'gi'), function(match) {
          return '<b class="ocf-found-match">' + match + '</b>';
        }));
      });

      $categoryItems.not($found).addClass('hidden');
    } else {
      $categoryBody.addClass('hidden');
    }

    $btnCancel.find('.fa').attr('class', 'fa fa-times text-danger');
  }
};

$.expr[':'].icontains = function(a, i, m) {
  return $(a).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
};

var $modal = $('#modal-category-list'),
    $categoryItems = $modal.find('.list-group-item');

$('[name="search_category"]').one('focus', function(e) {
  var $element = $(this);

  $element.on('keyup change', searchCategories).data({
    'categoryBody': $modal.find('.modal-category-list'),
    'categoryItems': $categoryItems,
    'btnCancel': $('[data-toggle="cancel-search"]')
  });
});

$('[data-toggle="cancel-search"]').on('click', function(e) {
  e.stopPropagation();

  $(this).closest('.input-group').find('[name="search_category"]').val('').trigger('change').trigger('click').focus();
});

$('#input-check-all-category').on('change', function(e) {
  $('.modal-category-list').find('input[name="category_id[]"]:visible').prop('checked', this.checked);
});

$('input[name="level[]"]').on('change', function(e) {
  $categoryItems.toggleClass('hidden', !!$('input[name="level[]"]:checked').length);

  $('input[name="level[]"]:checked').each(function(i) {
    if ($(this).val() != '>2') {
      $categoryItems.filter('.level-' + $(this).val()).removeClass('hidden');
    } else {
      $categoryItems.filter(function() {
        return /level-[3-9]/.test($(this).attr('class'));
      }).removeClass('hidden');
    }
  });
});

$modal.find('input[name="select_sublevel"]').on('change', function(e) {
  var 
    startIndex = $(this).closest('label').index(), 
    endIndex = $categoryItems.filter(':gt(' + startIndex + ')').filter('.level-' + this.value).filter(':first').index();
       
  if (endIndex > 0) {
    $categoryItems.filter(':gt(' + (startIndex - 1) + '):lt(' + (endIndex - startIndex) + ')').find('input[name="category_id[]"]').prop('checked', this.checked);
  } else {
    $categoryItems.filter(':gt(' + (startIndex - 1) + ')').find('input[name="category_id[]"]').prop('checked', this.checked);  
  }
});

var $target = $('#<?php echo $target; ?>');

$('[data-save="category"]').on('click', function(e) {
  $modal.modal('hide').on('hidden.bs.modal', function(e) {
    $target.find('[id*="category-"]').remove();

    $modal.find('input[name="category_id[]"]:checked').each(function(i) {
      var $item = $(this).parent();

      $target.append('<div id="filter-category-' + $(this).val() + '"><i class="fa fa-fw fa-minus-circle"></i> ' + $item.data().label + '<input type="hidden" name="filter_category[' + $(this).val() + ']" value="' + $item.data().label + '" /></div>');
    });

    $modal.remove();
  });
});
</script>
</div>