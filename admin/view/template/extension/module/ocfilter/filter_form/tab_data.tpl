<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_suffix; ?></label>
  <div class="col-sm-3">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
      <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
      <input type="text" name="filter_description[<?php echo $language['language_id']; ?>][suffix]" maxlength="32" value="<?php echo isset($filter_description[$language['language_id']]) ? $filter_description[$language['language_id']]['suffix'] : ''; ?>" class="form-control" />
    </div>
    <?php if (isset($error_suffix[$language['language_id']])) { ?>
    <div class="text-danger"><?php echo $error_suffix[$language['language_id']]; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_suffix; ?></p>
  </div>                
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_description; ?></label>
  <div class="col-sm-3">
    <?php foreach ($languages as $language) { ?>
    <div class="input-group">
      <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></span>
      <textarea name="filter_description[<?php echo $language['language_id']; ?>][description]" rows="2" maxlength="255" class="form-control"><?php echo isset($filter_description[$language['language_id']]) ? $filter_description[$language['language_id']]['description'] : ''; ?></textarea>
    </div>
    <?php if (isset($error_description[$language['language_id']])) { ?>
    <div class="text-danger"><?php echo $error_description[$language['language_id']]; ?></div>
    <?php } ?>
    <?php } ?>
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_description; ?></p>
  </div>      
</div>
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_store; ?></label>
  <div class="col-sm-9">
    <div class="well well-sm" style="height: 100px; overflow: auto;">
      <?php foreach ($stores as $store) { ?>
      <div class="checkbox">
        <label>
          <?php if (in_array($store['store_id'], $filter_store)) { ?>
          <input type="checkbox" name="filter_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" /> <?php echo $store['name']; ?>
          <?php } else { ?>
          <input type="checkbox" name="filter_store[]" value="<?php echo $store['store_id']; ?>" /> <?php echo $store['name']; ?>
          <?php } ?>
        </label>
      </div>
      <?php } ?>
    </div>
  </div>
</div>