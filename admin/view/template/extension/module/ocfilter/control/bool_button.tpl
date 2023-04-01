<div class="btn-group" data-toggle="buttons">
  <?php if ($true) { ?>
  <label class="btn btn-default active">
    <input type="radio" name="<?php echo $name; ?>" value="1" checked="checked" /> <?php echo $text_true; ?>
  </label>
  <label class="btn btn-default">
    <input type="radio" name="<?php echo $name; ?>" value="0" /> <?php echo $text_false; ?>
  </label>
  <?php } else { ?>
  <label class="btn btn-default">
    <input type="radio" name="<?php echo $name; ?>" value="1" /> <?php echo $text_true; ?>
  </label>
  <label class="btn btn-default active">
    <input type="radio" name="<?php echo $name; ?>" value="0" checked="checked" /> <?php echo $text_false; ?>
  </label>
  <?php } ?>
</div>