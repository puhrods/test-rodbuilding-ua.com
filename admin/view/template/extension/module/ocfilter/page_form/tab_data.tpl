<div class="row">          
  <div class="col-md-9">              
    <?php if ($multilang_keyword) { ?>
    <div class="row">
      <label class="col-sm-3 control-label"><?php echo $entry_keyword; ?></label>
    </div>
    
    <?php foreach ($stores as $store) { ?>
    <div class="form-group">
      <label class="col-sm-3 control-label"><?php echo $store['name']; ?></label>
      <div class="col-sm-9">
        <?php foreach ($languages as $language) { ?>
        <div class="input-group">
          <div class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></div>
          <input type="text" name="keyword[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]" value="<?php echo $keyword[$store['store_id']][$language['language_id']] ? $keyword[$store['store_id']][$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_keyword; ?>" class="form-control" />
        </div>           
        <?php if ($error_keyword[$store['store_id']][$language['language_id']]) { ?>
        <div class="text-danger"><?php echo $error_keyword[$store['store_id']][$language['language_id']]; ?></div>
        <?php } ?>
        <?php } ?>
      </div>
    </div> 
    <?php } ?>  
    <div class="row">
      <div class="col-sm-offset-3 col-sm-9 help-block"><?php echo $help_keyword; ?></div>
    </div>  
    <?php } else { ?>  
    <div class="form-group">
      <label class="col-sm-3 control-label" for="input-keyword"><?php echo $entry_keyword; ?></label>
      <div class="col-sm-9">
        <input type="text" name="keyword" value="<?php echo $keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-keyword" class="form-control" />
        <?php if ($error_keyword) { ?>
        <div class="text-danger"><?php echo $error_keyword; ?></div>
        <?php } ?>
        <p class="help-block"><?php echo $help_keyword; ?></p>
      </div>
    </div>      
    <?php } ?>
      
    <ul class="nav nav-tabs" id="language">
      <?php foreach ($languages as $language) { ?>
      <li><a href="#tab-language-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
      <?php } ?>
    </ul>
    <div class="tab-content">       
      <?php foreach ($languages as $language) { ?>
      <div class="tab-pane" id="tab-language-<?php echo $language['language_id']; ?>">
        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-name-<?php echo $language['language_id']; ?>"><?php echo $entry_name; ?></label>
          <div class="col-sm-9">
            <input type="text" name="page_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['name'] : ''; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name-<?php echo $language['language_id']; ?>" class="form-control" />
            <?php if (isset($error_name[$language['language_id']])) { ?>
            <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
            <?php } ?>
            <p class="help-block"><?php echo $help_name; ?></p>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-heading-title-<?php echo $language['language_id']; ?>"><?php echo $entry_heading_title; ?></label>
          <div class="col-sm-9">
            <input type="text" name="page_description[<?php echo $language['language_id']; ?>][heading_title]" value="<?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['heading_title'] : ''; ?>" placeholder="<?php echo $entry_heading_title; ?>" id="input-heading-title-<?php echo $language['language_id']; ?>" class="form-control" />
            <?php if (isset($error_heading_title[$language['language_id']])) { ?>
            <div class="text-danger"><?php echo $error_heading_title[$language['language_id']]; ?></div>
            <?php } ?>
            <p class="help-block"><?php echo $help_heading_title; ?></p>
          </div>
        </div>
        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-meta-title<?php echo $language['language_id']; ?>"><?php echo $entry_meta_title; ?></label>
          <div class="col-sm-9">
            <input type="text" name="page_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['meta_title'] : ''; ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-meta-title-<?php echo $language['language_id']; ?>" class="form-control" />
            <?php if (isset($error_meta_title[$language['language_id']])) { ?>
            <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
            <?php } ?>
            <p class="help-block"><?php echo $help_meta_title; ?></p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-description-<?php echo $language['language_id']; ?>"><?php echo $entry_description_top; ?></label>
          <div class="col-sm-9">
            <textarea name="page_description[<?php echo $language['language_id']; ?>][description_top]" placeholder="<?php echo $entry_description_top; ?>" id="input-description-top-<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['description_top'] : ''; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-description-<?php echo $language['language_id']; ?>"><?php echo $entry_description_bottom; ?></label>
          <div class="col-sm-9">
            <textarea name="page_description[<?php echo $language['language_id']; ?>][description_bottom]" placeholder="<?php echo $entry_description_bottom; ?>" id="input-description-bottom-<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['description_bottom'] : ''; ?></textarea>
          </div>
        </div>                  
        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-meta-description-<?php echo $language['language_id']; ?>"><?php echo $entry_meta_description; ?></label>
          <div class="col-sm-9">
            <textarea name="page_description[<?php echo $language['language_id']; ?>][meta_description]" rows="3" placeholder="<?php echo $entry_meta_description; ?>" id="input-meta-description-<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['meta_description'] : ''; ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-meta-keyword-<?php echo $language['language_id']; ?>"><?php echo $entry_meta_keyword; ?></label>
          <div class="col-sm-9">
            <textarea name="page_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="3" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-meta-keyword-<?php echo $language['language_id']; ?>" class="form-control"><?php echo isset($page_description[$language['language_id']]) ? $page_description[$language['language_id']]['meta_keyword'] : ''; ?></textarea>
          </div>
        </div>                  
      </div>             
      <?php } ?>       
    </div>
  </div>
  <div class="col-md-3 sticky-md-top">
    <div class="well">
      <?php echo $text_info_mask; ?>
      <hr />
      <ul class="media-list lead" id="page-mask-vars-list"></ul>
      <p><?php echo $text_info_mask_static; ?></p>
    </div>
  </div>    
</div><!-- /.row -->   
<script>
ocfDOMReady(function() {
$(function() {
  var options = ocfilter.getSummernoteOptions();

  $('textarea[name*="[description_"]').each(function() {
    options.placeholder = $(this).attr('placeholder');
    
    $(this).summernote(options);
  });   
});
});
</script> 