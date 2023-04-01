<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_display_module; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('module', $module, 'y/n'); ?>
  </div>
</div>  
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_display_category; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('category', $category, 'y/n'); ?>
  </div>
</div>  
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_display_product; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('product', $product, 'y/n'); ?>
  </div>
</div>  
<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_display_sitemap; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('sitemap', $sitemap, 'y/n'); ?>
  </div>
</div>      

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_store; ?>, <?php echo $entry_layout; ?></label>
  <div class="col-sm-5">
    <table class="table m-0">
      <thead>
        <tr>
          <td class="text-left"><?php echo $entry_store; ?></td>
          <td class="text-left"><?php echo $entry_layout; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($stores as $store) { ?>
        <tr>
          <td class="text-left">
            <?php if (isset($page_store[$store['store_id']])) { ?>
            <label><input type="checkbox" name="page_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" /> <?php echo $store['name']; ?></label>
            <?php } else { ?>
            <label><input type="checkbox" name="page_store[]" value="<?php echo $store['store_id']; ?>" /> <?php echo $store['name']; ?></label>
            <?php } ?>                    
          </td>
          <td class="text-left">
            <select name="page_layout[<?php echo $store['store_id']; ?>]" class="form-control">
              <option value=""></option>
              <?php foreach ($layouts as $layout) { ?>
              <?php if (isset($page_layout[$store['store_id']]) && $page_layout[$store['store_id']] == $layout['layout_id']) { ?>
              <option value="<?php echo $layout['layout_id']; ?>" selected="selected"><?php echo $layout['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $layout['layout_id']; ?>"><?php echo $layout['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>  
  </div>
</div>                
<div class="form-group">
  <label class="col-sm-3 control-label" for="input-page-link-code"><?php echo $entry_display_code; ?></label>
  <div class="col-sm-9">   
    <textarea class="form-control" onclick="$(this).select();" rows="1" id="input-page-link-code" placeholder="<?php echo $placeholder_display_code; ?>" data-code="&lt;a href=&quot;&lt;?php echo $ocfilter-&gt;page(<?php echo $page_id; ?>)-&gt;href; ?&gt;&quot;&gt;&lt;?php echo $ocfilter-&gt;page(<?php echo $page_id; ?>)-&gt;name; ?&gt;&lt;/a&gt;" readonly></textarea>
  </div>
</div> 