<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_type; ?></label>
  <div class="col-sm-9">
    <div class="btn-group" data-toggle="buttons">
      <?php if ($dynamic) { ?>
      <label class="btn btn-default">
        <input type="radio" name="dynamic" value="0"> <?php echo $text_type_static; ?>  
      </label>                      
      <label class="btn btn-default active">
        <input type="radio" name="dynamic" value="1" checked="checked"> <?php echo $text_type_dynamic; ?>  
      </label>
      <?php } else { ?>              
      <label class="btn btn-default active">
        <input type="radio" name="dynamic" value="0" checked="checked"> <?php echo $text_type_static; ?>  
      </label>         
      <label class="btn btn-default">
        <input type="radio" name="dynamic" value="1"> <?php echo $text_type_dynamic; ?>  
      </label>                         
      <?php } ?>
    </div>
    <p class="help-block"><?php echo $help_type; ?></p>
  </div>
</div> 

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_status; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('status', $status, 'e/d'); ?>
  </div>
</div>  