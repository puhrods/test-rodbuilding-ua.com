<div class="input-group ocf-sort-order" data-toggle="buttons">
  <div class="input-group-btn">
    <?php if ($sort_order == 'begin') { ?>
    <label class="btn btn-default">
      <input type="radio" name="<?php echo $name; ?>" value="begin" checked /> <?php echo $text_begin; ?>
    </label>
    <?php } else { ?>
    <label class="btn btn-default">
      <input type="radio" name="<?php echo $name; ?>" value="begin" /> <?php echo $text_begin; ?>
    </label>          
    <?php } ?>
  </div>
  <div class="input-group-prepend ocf-relative">
    <?php if (is_numeric($sort_order)) { ?>
    <input type="number" name="<?php echo $name; ?>" value="<?php echo $sort_order; ?>" class="form-control" />
    <?php } else { ?>
    <input type="number" name="<?php echo $name; ?>" value="" class="form-control" disabled />
    <?php } ?>   
    <div class="ocf-input-placeholder"></div>
  </div>
  <div class="input-group-btn">
    <?php if ($sort_order == 'after') { ?>
    <label class="btn btn-default">
      <input type="radio" name="<?php echo $name; ?>" value="after" checked /> <?php echo $text_after; ?>
    </label>
    <?php } else { ?>
    <label class="btn btn-default">
      <input type="radio" name="<?php echo $name; ?>" value="after" /> <?php echo $text_after; ?>
    </label>
    <?php } ?>
  </div>
</div>  
<p class="help-block"><?php echo $help_sort_order; ?></p>