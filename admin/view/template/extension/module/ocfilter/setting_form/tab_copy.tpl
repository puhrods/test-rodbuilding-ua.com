<h3 class="jumbotron"><?php echo $nav_copy_source; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_attribute; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_attribute', $copy_attribute, 'y/n'); ?>
    <p class="help-block" id="text-copy-attribute-total"><i class="fa fa-refresh fa-spin"></i></p>
  </div>
</div>

<div class="collapse" id="collapse-copy-attribute">
  <div class="form-group">
    <label class="col-sm-3 control-label" new-feature><?php echo $entry_copy_group_as_attribute; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('copy_group_as_attribute', $copy_group_as_attribute, 'y/n'); ?>

      <p class="help-block"><?php echo $help_copy_group_as_attribute; ?></p>
    </div>
  </div>
  
  <div class="form-group">
    <label class="col-lg-3 control-label" new-feature><?php echo $entry_copy_attribute_data; ?></label>
    <div class="col-sm-6 col-lg-4" data-group-as-attribute="0">          
      <div class="input-group">
        <input type="text" name="autocomplete_attribute" value="" placeholder="<?php echo $placeholder_copy_attribute_autocomplete; ?>" class="form-control" />
        <label class="input-group-addon">
          <input type="checkbox" name="copy_attribute_id_exclude" value="1" autocomplete="off" <?php echo ($copy_attribute_id_exclude ? 'checked' : ''); ?>> <?php echo $entry_copy_exclude; ?>
        </label>       
        <div class="input-group-btn">
          <button type="button" class="btn btn-default" onclick="getAutoAttributes(this)" data-loading-text="<?php echo $text_loading; ?>"><?php echo $button_auto; ?></button>
        </div>
        <div class="input-group-addon"><i class="fa fa-bars"></i></div>
      </div>            
      <div class="alert alert-success mb-0" id="copy-attribute-id" style="height: 150px; overflow: auto;">
        <?php foreach ($copy_attribute_id as $attribute_id => $name) { ?>
        <div id="copy-attribute-<?php echo $attribute_id; ?>">
          <i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> <?php echo $name; ?>
          <input type="hidden" name="copy_attribute_id[<?php echo $attribute_id; ?>]" value="<?php echo $name; ?>" />
        </div>
        <?php } ?>
      </div>
      <button type="button" class="btn btn-link" onclick="$(this).prev().html(''); buildCopyCode();"><?php echo $button_clear; ?></button>
    </div>
    <div class="col-sm-6 col-lg-4" data-group-as-attribute="1">          
      <div class="input-group">
        <input type="text" name="autocomplete_attribute_group" value="" placeholder="<?php echo $placeholder_copy_attribute_group_autocomplete; ?>" class="form-control" />
        <label class="input-group-addon">
          <input type="checkbox" name="copy_attribute_group_id_exclude" value="1" autocomplete="off" <?php echo ($copy_attribute_group_id_exclude ? 'checked' : ''); ?>> <?php echo $entry_copy_exclude; ?>
        </label>          
        <div class="input-group-addon"><i class="fa fa-bars"></i></div>
      </div>            
      <div class="alert alert-success mb-0" id="copy-attribute-id" style="height: 150px; overflow: auto;">
        <?php foreach ($copy_attribute_group_id as $attribute_group_id => $name) { ?>
        <div id="copy-attribute-<?php echo $attribute_group_id; ?>">
          <i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> <?php echo $name; ?>
          <input type="hidden" name="copy_attribute_group_id[<?php echo $attribute_group_id; ?>]" value="<?php echo $name; ?>" />
        </div>
        <?php } ?>
      </div>
      <button type="button" class="btn btn-link" onclick="$(this).prev().html(''); buildCopyCode();"><?php echo $button_clear; ?></button>
    </div>
    <div class="col-sm-6 col-lg-4">          
      <div class="input-group">
        <input type="text" name="autocomplete_attribute_category" value="" placeholder="<?php echo $placeholder_copy_category_autocomplete; ?>" class="form-control" />
        <label class="input-group-addon">
          <input type="checkbox" name="copy_attribute_category_id_exclude" value="1" autocomplete="off" <?php echo ($copy_attribute_category_id_exclude ? 'checked' : ''); ?>> <?php echo $entry_copy_exclude; ?>
        </label>           
        <div class="input-group-addon"><i class="fa fa-bars"></i></div>
      </div>            
      <div class="alert alert-success mb-0" id="copy-attribute-category-id" style="height: 150px; overflow: auto;">
        <?php foreach ($copy_attribute_category_id as $attribute_category_id => $name) { ?>
        <div id="copy-attribute-category-<?php echo $attribute_category_id; ?>">
          <i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> <?php echo $name; ?>
          <input type="hidden" name="copy_attribute_category_id[<?php echo $attribute_category_id; ?>]" value="<?php echo $name; ?>" />
        </div>
        <?php } ?>
      </div>
      <button type="button" class="btn btn-link" onclick="$(this).prev().html(''); buildCopyCode();"><?php echo $button_clear; ?></button>
    </div>   
    <div class="col-sm-offset-3 col-sm-9">
      <p class="help-block"><?php echo $help_copy_attribute_data; ?></p>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_filter; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_filter', $copy_filter, 'y/n'); ?>
    <p class="help-block" id="text-copy-filter-total"><i class="fa fa-refresh fa-spin"></i></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_option; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_option', $copy_option, 'y/n'); ?>
    <p class="help-block" id="text-copy-option-total"><i class="fa fa-refresh fa-spin"></i></p>
  </div>
</div>

<div class="collapse" id="collapse-copy-option">
  <div class="form-group">
    <label class="col-sm-3 control-label" new-feature><?php echo $entry_copy_option_in_stock; ?></label>
    <div class="col-sm-9">
      <?php $tpl_bool_button('copy_option_in_stock', $copy_option_in_stock, 'y/n'); ?>

      <p class="help-block"><?php echo $help_copy_option_in_stock; ?></p>
    </div>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_copy_filter; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-copy-type"><?php echo $entry_copy_type; ?></label>
  <div class="col-sm-3">
    <select name="copy_type" id="input-copy-type" class="form-control">
      <?php foreach ($types as $type) { ?>
      <?php if ($type == $copy_type) { ?>
      <option value="<?php echo $type; ?>" selected="selected"><?php echo ucfirst($type); ?></option>
      <?php } else { ?>
      <option value="<?php echo $type; ?>"><?php echo ucfirst($type); ?></option>
      <?php } ?>
      <?php } ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_copy_dropdown; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_dropdown', $copy_dropdown, 'y/n'); ?>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" for="input-truncate"><?php echo $entry_copy_status; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_status', $copy_status, 'e/d'); ?>

    <p class="help-block"><?php echo $help_copy_status; ?></p>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_copy_other; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $entry_copy_value_separator; ?></label>
  <div class="col-sm-5">     
    <div class="input-group">
      <div class="input-group-addon">1</div>
      <input name="copy_value_separator[]" type="text" class="form-control" placeholder="<?php echo $placeholder_copy_value_separator; ?>" value="<?php echo (isset($copy_value_separator[0]) ? $copy_value_separator[0] : ''); ?>" />
      <div class="input-group-addon">2</div>
      <input name="copy_value_separator[]" type="text" class="form-control" placeholder="<?php echo $placeholder_copy_value_separator; ?>" value="<?php echo (isset($copy_value_separator[1]) ? $copy_value_separator[1] : ''); ?>" />
      <div class="input-group-addon">3</div>
      <input name="copy_value_separator[]" type="text" class="form-control" placeholder="<?php echo $placeholder_copy_value_separator; ?>" value="<?php echo (isset($copy_value_separator[2]) ? $copy_value_separator[2] : ''); ?>" />      
    </div>    
  </div>
  <div class="col-sm-offset-3 col-sm-9">
    <p class="help-block"><?php echo $help_copy_value_separator; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_clear_filter; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_truncate', $copy_truncate, 'y/n'); ?>
    
    <p class="help-block"><?php echo $help_copy_clear_filter; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_category; ?></label>
  <div class="col-sm-9">
    <?php $tpl_bool_button('copy_category', $copy_category, 'y/n'); ?>

    <p class="help-block"><?php echo $help_copy_category; ?></p>
  </div>
</div>

<h3 class="jumbotron"><?php echo $nav_copy_auto; ?></h3>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $text_copy_auto_code_php; ?></label>
  <div class="col-sm-9">
    <pre id="copy-auto-code-container-php"></pre>
    <p class="help-block"><?php echo $help_copy_auto_code_php; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $text_copy_auto_code_js; ?></label>
  <div class="col-sm-9">
    <pre id="copy-auto-code-container-js">
&lt;script&gt;
<span class="text-muted">// OCFilter copy start</span>
(<b class="text-warning">function</b>() {
  <b class="text-warning">var</b> token = window.location.search.match(<span class="text-warning">/(user_)?token\=(.+)($|&)/</span>);

  <b class="text-warning">if</b> (token[0]) {
    fetch(<span class="text-success">'index.php?route=extension/module/ocfilter/copy&'</span> + token[0]).then(() => {
      console.info(<span class="text-success">'OCFilter Copy finished'</span>);
    }).catch(error => {
      console.warning(<span class="text-success">'OCFilter Copy error '</span> + error);
    });
  }
})();
<span class="text-muted">// OCFilter copy end</span>
&lt;/script&gt;</pre>
    <p class="help-block"><?php echo $help_copy_auto_code_js; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label" new-feature><?php echo $text_copy_auto_cron; ?></label>
  <div class="col-sm-9">
    <div id="cron-manager">
      <div class="input-group input-group">
        <div class="input-group-btn dropdown">
          <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-clock-o"></i> <span class="dropdown-label"><?php echo $text_cron_period; ?></span> <i class="fa fa-angle-down"></i></button>
          <ul class="dropdown-menu" role="menu">
            <li class="dropdown-header"><?php echo $text_cron_select_period; ?></li>
            <li class="divider"></li>

            <li><a role="menuitem" href="#0 * * * *"><?php echo $text_cron_period_01; ?></a></li>
            <li><a role="menuitem" href="#0 */3 * * *"><?php echo $text_cron_period_02; ?></a></li>
            <li><a role="menuitem" href="#0 4 * * *"><?php echo $text_cron_period_03; ?></a></li>
            <li><a role="menuitem" href="#0 4 * * 0"><?php echo $text_cron_period_04; ?></a></li>
            <li><a role="menuitem" href="#0 */5 * * 6,0"><?php echo $text_cron_period_05; ?></a></li>
            <li><a role="menuitem" href="#0 0 1 * *"><?php echo $text_cron_period_06; ?></a></li>
          </ul>
        </div>
        <div class="input-group-addon"><?php echo $text_or; ?></div>
        <input type="text" name="cron_period_manual" value="" class="form-control" placeholder="<?php echo $text_cron_period_manual; ?>" />
      </div><!-- /.input-group -->
    </div><br />
    
    <p><?php echo $text_cron_bin; ?></p>
    <kbd contenteditable="true" onclick="document.execCommand('selectAll', false, null)" style="font-size: 14px;"><span class="cron-period"></span> <?php echo $cron_command_bin; ?> -f <?php echo $cron_command_script; ?></kbd>
    <br /><br />
    <p class="ocf-checkbox"><input type="checkbox" name="copy_cron_wget" value="1" id="input-copy-cron-wget" autocomplete="off" <?php echo ($copy_cron_wget ? 'checked="checked"' : ''); ?>> <label for="input-copy-cron-wget"><?php echo $text_cron_wget; ?></label></p>
    <kbd contenteditable="true" onclick="document.execCommand('selectAll', false, null)" style="font-size: 14px;" class="cron-command-wget"><span class="cron-period"></span> <?php echo $cron_command_wget; ?></kbd>

    <p class="help-block"><?php echo $help_copy_auto_cron; ?></p>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label"><?php echo $entry_copy_now; ?></label>
  <div class="col-sm-3">
    <div class="input-group">
      <div class="input-group-btn">
        <button type="button" class="btn btn-lg btn-primary" id="button-copy-filter" data-loading-text="<?php echo $text_loading; ?>" data-complete-text="<?php echo $text_complete; ?>"><i class="fa fa-copy"></i> <?php echo $button_copy; ?></button>
      </div>
      <label class="input-group-addon">
        <input type="checkbox" name="copy_save_setting" value="1" autocomplete="off"> <?php echo $entry_copy_save_setting; ?>
      </label>
    </div>    
  </div>
  
  <div class="col-sm-offset-3 col-sm-9">
    <pre id="copy-log" style="max-height: 300px; overflow: auto; margin-top: 20px;"></pre>
  </div>
</div>

<script type="text/javascript">

var 

ocfDOMReady = function(fn) {
  if (document.readyState != 'loading') {
    fn();
  } else {
    document.addEventListener('DOMContentLoaded', fn);
  }
},

buildCopyCode = function() {
  var code = '', $control, values = [];

  code += '<span class="text-muted">// OCFilter copy start</span><br>';
  code += '<span class="text-primary">$this</span>->load->controller(\'extension/module/ocfilter/copy\', [<br>';

  $('#tab-copy').find('[name^="copy_"]').filter('input[type="text"], input[type="hidden"], input[type="number"], input[type="checkbox"], input[type="radio"]:checked, select').filter(':not([name$="]"])').each(function(i) {
    $control = $(this);

    if ($control.attr('name') == 'copy_save_setting') {
      return true;
    }

    code += "  <span class='text-success'>'" + $control.attr('name') + "'</span> => ";

    if ($control.is(':checkbox')) {
      code += '<b class="text-warning">' + ($.isNumeric($control.val()) ? ($control.prop('checked') + 0) : ($control.prop('checked') ? $control.val() : '')) + '</b>';
    } else if ($.isNumeric($control.val())) {
      code += '<b class="text-warning">' + $control.val() + '</b>';
    } else {
      code += "<span class='text-success'>'" + $control.val() + "'</span>";
    }

    code += ',';
    
    if ($control.closest('.form-group').find('.control-label').length) {
      code += ' <span class="text-muted">// ' + $control.closest('.form-group').find('.control-label').text() + '</span>';
    }

    code += "<br>";
  });
  
  // Arrays
  values = [];
  
  $('#tab-copy').find('[name^="copy_value_separator["]').each(function() {
    $(this).val() && values.push('<span class="text-success">\'' + $(this).val() + '\'</span>');    
  });

  code += '  <span class="text-success">\'copy_value_separator\'</span> => [' + values.join(', ') + '], <span class="text-muted">// <?php echo addslashes($entry_copy_value_separator); ?></span><br>';   
  
  // Attributes
  values = [];
  
  $('#tab-copy').find('[name^="copy_attribute_id["]').each(function() {
    values.push('<span class="text-success">\'' + $(this).attr('name').match(/\[(\d+)\]/)[1] + '\'</span>');    
  });

  code += '  <span class="text-success">\'copy_attribute_id\'</span> => [' + values.join(', ') + '], <span class="text-muted">// <?php echo addslashes($placeholder_copy_attribute_autocomplete); ?></span><br>';     
  
  // Attribute groups
  values = [];
  
  $('#tab-copy').find('[name^="copy_attribute_group_id["]').each(function() {
    values.push('<span class="text-success">\'' + $(this).attr('name').match(/\[(\d+)\]/)[1] + '\'</span>');    
  });

  code += '  <span class="text-success">\'copy_attribute_group_id\'</span> => [' + values.join(', ') + '], <span class="text-muted">// <?php echo addslashes($placeholder_copy_attribute_group_autocomplete); ?></span><br>';   

  // Categories
  values = [];
  
  $('#tab-copy').find('[name^="copy_attribute_category_id["]').each(function() {
    values.push('<span class="text-success">\'' + $(this).attr('name').match(/\[(\d+)\]/)[1] + '\'</span>');    
  });

  code += '  <span class="text-success">\'copy_attribute_category_id\'</span> => [' + values.join(', ') + '], <span class="text-muted">// <?php echo addslashes($placeholder_copy_category_autocomplete); ?></span><br>';     

  code += ']);<br>';
  code += '<span class="text-muted">// OCFilter copy end</span>';

  $('#copy-auto-code-container-php').html(code);
},

getAutoAttributes = function(btn) {
  $(btn).button('loading');
  
  $.get(ocfilter.link('extension/module/ocfilter/getCopyAttribute', 'exclude=' + ($('input[name="copy_attribute_id_exclude"]').prop('checked') + 0)), {}, function(response) {
    $(btn).button('reset');
    
    for (var i = 0, len = response.length; i < len; i++) {
      $('#copy-attribute-' + response[i].attribute_id).remove();

      $('#copy-attribute-id').append('<div id="copy-attribute-' + response[i].attribute_id + '"><i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> ' + response[i].name + '<input type="hidden" name="copy_attribute_id[' + response[i].attribute_id + ']" value="' + response[i].name + '" /></div>');      
    }
    
    buildCopyCode();
  }, 'json');
};

ocfDOMReady(function() {
$(function() {
  $('input[name="copy_attribute"]').on('change', function() {
    $('#collapse-copy-attribute').collapse($(this).prop('checked') && $(this).val() == 1 ? 'show' : 'hide');
  }).filter(':checked').trigger('change');
  
  $('input[name="copy_option"]').on('change', function() {
    $('#collapse-copy-option').collapse($(this).prop('checked') && $(this).val() == 1 ? 'show' : 'hide');
  }).filter(':checked').trigger('change');  

  $('#tab-copy').find('input, select').on('change', buildCopyCode);

  $('#cron-manager .dropdown-menu a').on('click', function(e) {
    e.preventDefault();

    $('.cron-period').text($(this).attr('href').substring(1));

    $(this).closest('.dropdown-menu').find('li').removeClass('active');
    $(this).parent().addClass('active').closest('.dropdown').find('.dropdown-label').text($(this).text());
  }).filter(':first').trigger('click');

  $('input[name="cron_period_manual"]').on('change', function(e) {
    $('.cron-period').text($(this).val());
  });

  buildCopyCode();

  var timer, copyDone = false, copySuccess = false, copyHash;

  function checkLog() {
    if (copyDone) {
      return;
    }
  
    $.get(ocfilter.link('extension/module/ocfilter/getCopyLog'), {}, function(response) {
      if (response.indexOf(copyHash) !== -1) {
        copyDone = true;
        
        if (!copySuccess) {
          $('#button-copy-filter').button('complete');

          timer = setTimeout(function() {
            $('#button-copy-filter').button('reset');
          }, 7 * 1000);
        }
      }
    
      $('#copy-log').html(response.replace(/\[(.+?)\]/g, '<b class="text-primary">$1</b>')).get(0).scrollTop = 9999;
    
      setTimeout(checkLog, 2000);
    }, 'text');
  }
  
  $('#button-copy-filter').on('click', function(e) {
    copyHash = (new Date()).getTime();
    copyDone = false;
    copySuccess = false;
    
    clearTimeout(timer);

    if ($('#tab-copy input[name="copy_truncate"][value="1"]:checked').length > 0 && !confirm('<?php echo $text_confirm_truncate_copy; ?>')) {
      return false;
    }
    
    checkLog();    

    $('#tab-copy > .alert').remove();

    var $button = $(this).button('loading');

    $.post(ocfilter.link('extension/module/ocfilter/copy', 'copy_hash=' + copyHash), $('#tab-copy').find('[name^="copy_"]').filter('input[type="checkbox"]:checked, input[type="radio"]:checked, input[type="text"], input[type="number"], input[type="hidden"], select'), function(response) {
      if (response['error']) {
        $button.button('reset');

        $('#tab-copy').prepend('<div class="alert alert-danger" role="alert">' + response['error'] + '</div>');
      }

      if (response['success']) {
        $button.button('complete');

        timer = setTimeout(function() {
          $button.button('reset');
        }, 7 * 1000);
        
        copySuccess = true;
      }
    }, 'json');
  });
    
  $.get(ocfilter.link('extension/module/ocfilter/getCopyDataTotals'), {}, function(json) {
    for (var i in json) {
      if (json.hasOwnProperty(i)) {
        $('#' + i.replace(/_/g, '-')).html(json[i]);
      }
    }
  }, 'json');  
  
	$('input[name="autocomplete_attribute"]').autocomplete({  
		'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');  
    
			$.ajax({
				url: ocfilter.link('extension/module/ocfilter/autocompleteAttribute', 'filter_name=' +  request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.name,
              category: item.attribute_group,
							value: item.attribute_id
						}
					}));
				}
			});
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
		},
		'select': function(item) {
			$(this).val('');

      $('#copy-attribute-' + item.value).remove();

      $('#copy-attribute-id').append('<div id="copy-attribute-' + item.value + '"><i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> ' + item.label + '<input type="hidden" name="copy_attribute_id[' + item.value + ']" value="' + item.label + '" /></div>');
      
      buildCopyCode();
		}
	}); 

	$('input[name="autocomplete_attribute_group"]').autocomplete({  
		'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');  
    
			$.ajax({
				url: ocfilter.link('extension/module/ocfilter/autocompleteAttributeGroup', 'filter_name=' +  request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.attribute_id
						}
					}));
				}
			});
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
		},
		'select': function(item) {
			$(this).val('');
      
      $('#copy-attribute-group-' + item.value).remove();

      $('#copy-attribute-group-id').append('<div id="copy-attribute-group-' + item.value + '"><i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> ' + item.label + '<input type="hidden" name="copy_attribute_group_id[' + item.value + ']" value="' + item.label + '" /></div>');
      
      buildCopyCode();
		}
	}); 

	$('input[name="autocomplete_attribute_category"]').autocomplete({  
		'source': function(request, response) {
      var $this = $(this);
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-refresh fa-spin');  
    
			$.ajax({
				url: ocfilter.link('extension/module/ocfilter/autocompleteCategory', 'filter_name=' +  request),
				dataType: 'json',
				success: function(json) {
					response($.map(json, function(item) {
						return {
							label: item.name,
							value: item.category_id
						}
					}));
				}
			});
      
      $this.parent().find('.input-group-addon').find('i').attr('class', 'fa fa-bars');
		},
		'select': function(item) {
			$(this).val('');
      
      $('#copy-attribute-category-' + item.value).remove();

      $('#copy-attribute-category-id').append('<div id="copy-attribute-category-' + item.value + '"><i class="fa fa-fw fa-minus-circle" onclick="$(this).parent().remove();"></i> ' + item.label + '<input type="hidden" name="copy_attribute_category_id[' + item.value + ']" value="' + item.label + '" /></div>');
      
      buildCopyCode();
		}
	});   
  
  $('input[name="copy_group_as_attribute"]').on('change', function(e) {
    $('[data-group-as-attribute]').hide().filter('[data-group-as-attribute="' + $('input[name="copy_group_as_attribute"]:checked').val() + '"]').show();
  }).filter(':checked').trigger('change');
  
  $('input[name$="_exclude"]').on('change', function(e) {
    $(this).closest('.input-group').next().toggleClass('alert-danger', this.checked);
  }).filter(':checked').trigger('change');  
  
  $('#input-copy-cron-wget').on('change', function(e) {
    $('.cron-command-wget').toggleClass('ocf-opacity-50', !this.checked);
  }).trigger('change');   
});
}); // DOM Ready
</script>