<form action="<?php echo $add_batch; ?>" method="post" enctype="multipart/form-data" id="form-add">
  <div class="row">
    <div class="col-sm-6">
      <div class="form-group">
        <label class="control-label"><?php echo $entry_status; ?></label>
        <div>
          <?php $tpl_bool_button('add_status', $add_status, 'e/d'); ?>
        </div>        
      </div>                 

      <div class="form-group required">
        <label class="control-label" for="input-add-category"><?php echo $entry_category; ?></label>
        <div>
          <div class="input-group">
            <input type="text" name="add_category_name" value="" placeholder="<?php echo ($add_category_name ? $add_category_name : $entry_category); ?>" id="input-add-category" class="form-control" />
            <input type="hidden" name="add_category_id" value="<?php echo $add_category_id; ?>" />                
            <div class="input-group-addon"><i class="fa fa-bars"></i></div>
          </div>   
          <?php if ($error_add_category) { ?>
          <div class="text-danger"><?php echo $error_add_category; ?></div>
          <?php } ?>              
        </div>
      </div>
      
      <div class="form-group required">
        <label class="control-label" for="input-add-filter"><?php echo $entry_filter; ?></label>
        <div>
          <input type="text" name="add_filter_name" value="" placeholder="<?php echo $entry_filter; ?>" list="filter-datalist" id="input-add-filter" class="form-control" autocomplete="off" <?php echo ($add_category_id ? '' : 'readonly'); ?> />
          <div class="well well-sm bg-white m-0 p-0">
            <div class="container-fluid">
              <div class="form-horizontal" id="add-filter-list">
                <div class="text-center text-muted p-4"><?php echo $text_select_categpry; ?></div>
              </div>
            </div>                          
          </div> 
          <?php if ($error_add_filter) { ?>
          <div class="text-danger"><?php echo $error_add_filter; ?></div>
          <?php } ?>            
        </div>
      </div>                  
      
      <div class="form-group row">
        <div class="col-sm-6">
          <label class="control-label"><?php echo $entry_display_module; ?></label>
          <div>
            <?php $tpl_bool_button('add_module', $add_module, 'y/n'); ?>
          </div>
        </div>
        <div class="col-sm-6">
          <label class="control-label"><?php echo $entry_display_category; ?></label>
          <div>
            <?php $tpl_bool_button('add_category', $add_category, 'y/n'); ?>
          </div>
        </div>
      </div>
      <div class="form-group row">
        <div class="col-sm-6">
          <label class="control-label"><?php echo $entry_display_product; ?></label>
          <div>
            <?php $tpl_bool_button('add_product', $add_product, 'y/n'); ?>
          </div>
        </div>
        <div class="col-sm-6">
          <label class="control-label"><?php echo $entry_display_sitemap; ?></label>
          <div>
            <?php $tpl_bool_button('add_sitemap', $add_sitemap, 'y/n'); ?>
          </div>
        </div>
      </div>  

      <div class="form-group pb-0">
        <label class="control-label text-primary cursor-pointer" data-toggle="collapse" data-target="#collapse-add-store-layout" aria-expanded="false"><u><?php echo $entry_store; ?>, <?php echo $entry_layout; ?></u> <i class="fa fa-angle-down"></i></label>
        <?php if (end($add_page_layout)) { ?>
        <?php $class = 'collapse in'; ?>
        <?php } else { ?>
        <?php $class = 'collapse'; ?>
        <?php } ?>
        <div class="<?php echo $class; ?>" id="collapse-add-store-layout">
          <div class="well well-sm bg-white m-0" style="max-height: 150px; overflow: auto;">
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
                    <?php if (isset($add_page_store[$store['store_id']])) { ?>
                    <label><input type="checkbox" name="add_page_store[]" value="<?php echo $store['store_id']; ?>" checked="checked" /> <?php echo $store['name']; ?></label>
                    <?php } else { ?>
                    <label><input type="checkbox" name="add_page_store[]" value="<?php echo $store['store_id']; ?>" /> <?php echo $store['name']; ?></label>
                    <?php } ?>                    
                  </td>
                  <td class="text-left">
                    <select name="add_page_layout[<?php echo $store['store_id']; ?>]" class="form-control">
                      <option value=""></option>
                      <?php foreach ($layouts as $layout) { ?>
                      <?php if (isset($add_page_layout[$store['store_id']]) && $add_page_layout[$store['store_id']] == $layout['layout_id']) { ?>
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
      </div><!-- /.form-group -->              
    </div>
    <div class="col-sm-6">
      <div class="bg-white p-4 mt-4">   
        <?php if ($multilang_keyword) { ?>
        <label class="control-label" data-toggle="popover" data-placement="top" data-content="<?php echo $help_add_keyword; ?>"><?php echo $entry_keyword; ?> <i class="fa fa-question-circle"></i></label>

        <?php foreach ($stores as $store) { ?>
        <div class="form-group">
          <label class="control-label"><?php echo $store['name']; ?></label>
          <?php foreach ($languages as $language) { ?>
          <div class="input-group">
            <div class="input-group-addon"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /></div>
            <input type="text" name="add_keyword[<?php echo $store['store_id']; ?>][<?php echo $language['language_id']; ?>]" value="<?php echo $add_keyword[$store['store_id']][$language['language_id']] ? $add_keyword[$store['store_id']][$language['language_id']] : ''; ?>" placeholder="<?php echo $entry_keyword; ?>" class="form-control" />
          </div>              
          <?php if ($error_add_keyword[$store['store_id']][$language['language_id']]) { ?>
          <div class="text-danger"><?php echo $error_add_keyword[$store['store_id']][$language['language_id']]; ?></div>
          <?php } ?>
          <?php } ?>
        </div> 
        <?php } ?>
        
        <?php } else { ?>
        
        <div class="form-group">
          <label class="control-label" for="input-add-keyword" data-toggle="popover" data-placement="top" data-content="<?php echo $help_add_keyword; ?>"><?php echo $entry_keyword; ?> <i class="fa fa-question-circle"></i></label>
          <div class="">
            <input type="text" name="add_keyword" value="<?php echo $add_keyword; ?>" placeholder="<?php echo $entry_keyword; ?>" id="input-add-keyword" class="form-control" />
            <?php if ($error_add_keyword) { ?>
            <div class="text-danger"><?php echo $error_add_keyword; ?></div>
            <?php } ?>                                 
          </div>
        </div>  
        
        <?php } ?>
      
        <ul class="nav nav-tabs" id="add-language">
          <?php foreach ($languages as $language) { ?>
          <li><a href="#tab-add-language-<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
          <?php } ?>
        </ul>
        <div class="tab-content">                      
          <?php foreach ($languages as $language) { ?>
          <div class="tab-pane" id="tab-add-language-<?php echo $language['language_id']; ?>">
            <div class="form-group">
              <label class="control-label" for="input-add-name-<?php echo $language['language_id']; ?>" data-toggle="popover" data-placement="top" data-content="<?php echo $help_name; ?>"><?php echo $entry_name; ?> <i class="fa fa-question-circle"></i></label>
              <input type="text" name="add_page_description[<?php echo $language['language_id']; ?>][name]" value="<?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['name'] : ''); ?>" placeholder="<?php echo $entry_name; ?>" id="input-add-name-<?php echo $language['language_id']; ?>" class="form-control" />
              <?php if (isset($error_name[$language['language_id']])) { ?>
              <div class="text-danger"><?php echo $error_name[$language['language_id']]; ?></div>
              <?php } ?>                            
            </div>
            <div class="form-group required">
              <label class="control-label" for="input-add-heading-title-<?php echo $language['language_id']; ?>" data-toggle="popover" data-placement="top" data-content="<?php echo $help_heading_title; ?>"><?php echo $entry_heading_title; ?> <i class="fa fa-question-circle"></i></label>
              <input type="text" name="add_page_description[<?php echo $language['language_id']; ?>][heading_title]" value="<?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['heading_title'] : ''); ?>" placeholder="<?php echo $entry_heading_title; ?>" id="input-add-heading-title-<?php echo $language['language_id']; ?>" class="form-control" />
              <?php if (isset($error_heading_title[$language['language_id']])) { ?>
              <div class="text-danger"><?php echo $error_heading_title[$language['language_id']]; ?></div>
              <?php } ?>                               
            </div>
            <div class="form-group required">
              <label class="control-label" for="input-add-meta-title-<?php echo $language['language_id']; ?>" data-toggle="popover" data-placement="top" data-content="<?php echo $help_meta_title; ?>"><?php echo $entry_meta_title; ?> <i class="fa fa-question-circle"></i></label>
              <input type="text" name="add_page_description[<?php echo $language['language_id']; ?>][meta_title]" value="<?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['meta_title'] : ''); ?>" placeholder="<?php echo $entry_meta_title; ?>" id="input-add-meta-title-<?php echo $language['language_id']; ?>" class="form-control" />                          
              <?php if (isset($error_meta_title[$language['language_id']])) { ?>
              <div class="text-danger"><?php echo $error_meta_title[$language['language_id']]; ?></div>
              <?php } ?>                               
            </div>
            <div class="form-group">
              <label class="control-label text-primary cursor-pointer" data-toggle="collapse" data-target="#collapse-add-description-top-<?php echo $language['language_id']; ?>" aria-expanded="false"><u><?php echo $entry_description_top; ?></u> <i class="fa fa-angle-down"></i></label>
              <?php if (!empty($add_page_description[$language['language_id']]['description_top'])) { ?>
              <?php $class = 'collapse in'; ?>
              <?php } else { ?>
              <?php $class = 'collapse'; ?>
              <?php } ?>              
              <div class="<?php echo $class; ?>" id="collapse-add-description-top-<?php echo $language['language_id']; ?>">
                <textarea name="add_page_description[<?php echo $language['language_id']; ?>][description_top]" placeholder="<?php echo $entry_description_top; ?>" id="input-add-description-top-<?php echo $language['language_id']; ?>" class="form-control"><?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['description_top'] : ''); ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label text-primary cursor-pointer" data-toggle="collapse" data-target="#collapse-add-description-bottom-<?php echo $language['language_id']; ?>" aria-expanded="false"><u><?php echo $entry_description_bottom; ?></u> <i class="fa fa-angle-down"></i></label>
              <?php if (!empty($add_page_description[$language['language_id']]['description_bottom'])) { ?>
              <?php $class = 'collapse in'; ?>
              <?php } else { ?>
              <?php $class = 'collapse'; ?>
              <?php } ?>              
              <div class="<?php echo $class; ?>" id="collapse-add-description-bottom-<?php echo $language['language_id']; ?>">
                <textarea name="add_page_description[<?php echo $language['language_id']; ?>][description_bottom]" placeholder="<?php echo $entry_description_bottom; ?>" id="input-add-description-bottom-<?php echo $language['language_id']; ?>" class="form-control"><?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['description_bottom'] : ''); ?></textarea>
              </div>
            </div>                  
            <div class="form-group">
              <label class="control-label text-primary cursor-pointer" for="input-add-meta-description-<?php echo $language['language_id']; ?>" data-toggle="collapse" data-target="#collapse-add-meta-description-<?php echo $language['language_id']; ?>" aria-expanded="false"><u><?php echo $entry_meta_description; ?></u> <i class="fa fa-angle-down"></i></label>
              <?php if (!empty($add_page_description[$language['language_id']]['meta_description'])) { ?>
              <?php $class = 'collapse in'; ?>
              <?php } else { ?>
              <?php $class = 'collapse'; ?>
              <?php } ?>              
              <div class="<?php echo $class; ?>" id="collapse-add-meta-description-<?php echo $language['language_id']; ?>">
                <textarea name="add_page_description[<?php echo $language['language_id']; ?>][meta_description]" rows="2" placeholder="<?php echo $entry_meta_description; ?>" id="input-add-meta-description-<?php echo $language['language_id']; ?>" class="form-control"><?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['meta_description'] : ''); ?></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label text-primary cursor-pointer" for="input-add-meta-keyword-<?php echo $language['language_id']; ?>" data-toggle="collapse" data-target="#collapse-add-meta-keyword-<?php echo $language['language_id']; ?>" aria-expanded="false"><u><?php echo $entry_meta_keyword; ?></u> <i class="fa fa-angle-down"></i></label>
              <?php if (!empty($add_page_description[$language['language_id']]['meta_keyword'])) { ?>
              <?php $class = 'collapse in'; ?>
              <?php } else { ?>
              <?php $class = 'collapse'; ?>
              <?php } ?>              
              <div class="<?php echo $class; ?>" id="collapse-add-meta-keyword-<?php echo $language['language_id']; ?>">
                <textarea name="add_page_description[<?php echo $language['language_id']; ?>][meta_keyword]" rows="2" placeholder="<?php echo $entry_meta_keyword; ?>" id="input-add-meta-keyword-<?php echo $language['language_id']; ?>" class="form-control"><?php echo (isset($add_page_description[$language['language_id']]) ? $add_page_description[$language['language_id']]['meta_keyword'] : ''); ?></textarea>
              </div>
            </div>                  
          </div>             
          <?php } ?>  
               
          <div class="form-group pb-0">
            <label class="control-label"><?php echo $text_mask_vars; ?></label>
            <div>
              <ul class="list-unstyled mb-0" id="page-mask-vars-list">
                <li class="text-muted"><?php echo $text_info_mask_filter_select; ?></li>
              </ul>                       
            </div>
          </div>                        
        </div><!-- /.tab-content -->                    
      </div>
    </div>                  
  </div><!-- /.row -->  
  <hr />
  <div class="row">
    <div class="col-sm-offset-6 col-sm-6">
      <button type="submit" class="btn btn-primary" data-loading-text="<?php echo $text_loading; ?>" onclick="$(this).button('loading');"><?php echo $button_add_pages; ?></button>
    </div>                
  </div>  
</form>

<script>
ocfDOMReady(function() {
$(function() {  
  function initForm() {
    $('#add-language a:first').tab('show');
    
    var editorOptions = ocfilter.getSummernoteOptions();
    
    $('textarea[name*="[description_"]').each(function() {
      editorOptions.placeholder = $(this).attr('placeholder');
      editorOptions.height = 140;
      
      $(this).summernote(editorOptions);
    });

    $('input[name="add_category_name"]').autocomplete({
      'source': function(request, response) {
        var $this = $(this);
        
        $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');
        
        $.ajax({
          url: ocfilter.link('catalog/category/autocomplete', { 'filter_name': request }),
          dataType: 'json',
          success: function(json) {                 
            response($.map(json, function(item) {
              return {
                label: item.name,
                value: item.category_id
              }
            }));
            
            $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
          }
        });
      },
      'select': function(item) {
        $(this).val('').attr('placeholder', item.label);
        
        $('input[name="add_category_id"]').val(item.value).trigger('change');
      }
    });  
         
    // Filter values relation form container
    $('body').append('<div style="display: none; position: absolute; top: -99999px; left: -99999px;" id="filter-relation-content"></div>');            
     
    // Get filters relation form     
    var relationFormOptions = {
      page_id: 0,
      category_id: 0,    
      ignore_slide: true,
      allow_group: true,
      container: '#filter-relation-content',
      eventContainer: '#add-filter-list',
      selected: JSON.parse('<?php echo json_encode($add_ocfilter_filter); ?>'),
      
      onLoad: function() {        
        $('#filter-datalist').remove();        
        
        var html = '<datalist id="filter-datalist">';
        
        $('#filter-relation-content .form-group').each(function() {
          html += '<option value="' + $(this).find('.control-label').text() + '" data-key="' + $(this).attr('data-ocfilter-filter-key') +'">';
        });
        
        html += '</datalist>';
               
        $('body').append(html);
        
        $('#add-filter-list').html('<div class="text-center text-muted p-4 text-select-filter"><?php echo $text_select_filter; ?></div>');
        
        $('input[name="add_filter_name"]').prop('readonly', false);
        
        // Add selected
        $('#filter-relation-content .ocf-form-group-selected').each(function() {
          addFilter($(this).attr('data-ocfilter-filter-key'));
        });
        
        ocfilter.buldMaskVarsList(buildMaskOptions);
      },
      
      onSelect: function() {
        ocfilter.buldMaskVarsList(buildMaskOptions);
      }
    };
      
    var buildMaskOptions = {
      container: '#page-mask-vars-list',
      relationContainer: '#add-filter-list',
      textDefault: '<?php echo $text_info_mask_filter; ?>'
    };
      
    function addFilter(filter_key) {
      var filterSelector = '[data-ocfilter-filter-key="' + filter_key + '"]', $filterNewItem;
        
      $('#add-filter-list').find('.text-select-filter').remove();
      
      $('#add-filter-list').find(filterSelector).remove();
    
      $('#add-filter-list').prepend($(filterSelector).get(0).outerHTML);
      
      $filterNewItem = $('#add-filter-list').find(filterSelector);
      
      if ($filterNewItem.hasClass('ocf-form-group-slider')) {
        $filterNewItem.find('input[name^="ocfilter_filter"][value="0"]').closest('[class^="col-"]').remove();
        $filterNewItem.find('.input-group').replaceWith('<p class="form-control-static"><?php echo $text_slider_not_available; ?></p>');
      }
      
      $filterNewItem.find('.control-label').prepend('<i class="fa fa-times-circle text-danger cursor-pointer pull-left mt-02 remove-relation-filter"></i>');     

      $('#filter-datalist').find('option[data-key="' + filter_key + '"]').remove();         
    }      
      
    $('input[name="add_category_id"]').on('change', function() {         
      ocfilter.getRelationForm($.extend({}, relationFormOptions, { category_id: this.value }));
    }).filter(':not([value=""])').trigger('change');
    
    // Filters Selectbox
    $('input[name="add_filter_name"]').on('input', function(e) {
      var keyword = $.trim($(this).val());

      if (!e.originalEvent.inputType) {
        $(this).val('');
      }    

      var $option = $('#filter-datalist').find('option').filter(function() {
        return this.value.toLowerCase() === keyword.toLowerCase();
      });

      if ($option.length) {
        addFilter($option.attr('data-key'));       

        ocfilter.buldMaskVarsList(buildMaskOptions);        
      }
    });
    
    $('#add-filter-list').on('change', 'input[name^="ocfilter_filter"]', function() {
      ocfilter.buldMaskVarsList(buildMaskOptions);
    }).on('click', '.remove-relation-filter', function() {
      var $formGroup = $(this).parents('.form-group:first');
      
      $('#filter-datalist').prepend('<option value="' + $.trim($formGroup.find('.control-label').text()) + '" data-key="' + $formGroup.attr('data-ocfilter-filter-key') + '">');
      
      $formGroup.remove();
      
      if ($('#add-filter-list').find('[data-ocfilter-filter-key]').length < 1) {
        $('#add-filter-list').html('<div class="text-center text-muted p-4 text-select-filter"><?php echo $text_select_filter; ?></div>');
      }
      
      ocfilter.buldMaskVarsList(buildMaskOptions);
    });    
  }

  if ($('#collapse-add').hasClass('in')) {
    initForm();
  } else {
    $('#collapse-add').one('show.bs.collapse', initForm);
  } 
});
}); // DOM Ready
</script> 