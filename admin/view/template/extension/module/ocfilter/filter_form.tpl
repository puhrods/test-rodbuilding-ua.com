<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ocf-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" onclick="submitForm('<?php echo $save; ?>');" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <button type="button" onclick="submitForm('<?php echo $apply; ?>');" data-toggle="tooltip" title="<?php echo $button_apply; ?>" class="btn btn-info"><i class="fa fa-save"></i> + <i class="fa fa-undo"></i></button>
        <button type="button" onclick="submitForm('<?php echo $apply_add; ?>');" data-toggle="tooltip" title="<?php echo $button_apply_add; ?>" class="btn btn-success"><i class="fa fa-save"></i> + <i class="fa fa-file-o"></i></button>

        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>    
    <div class="panel panel-default">
      <div class="panel-heading">
        <ul class="list-inline pull-right">
          <li><a href="https://ocfilter.com/documentation/4.8/#nav-filter-form" target="_blank"><i class="fa fa-fw fa-info-circle"></i> <?php echo $text_documentation; ?></a></li>
          <li><a href="https://ocfilter.com/faq/4.8/" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <?php echo $text_faq; ?></a></li>
        </ul>      
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>    
      <div class="panel-body">     
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
          <li><a href="#tab-other" data-toggle="tab"><?php echo $tab_other; ?></a></li>
          <li><a href="#tab-values" data-toggle="tab"><?php echo $tab_values; ?></a></li>
        </ul>
        <form id="form-filter" action="" method="post" enctype="multipart/form-data" class="form-horizontal">
          <div class="tab-content"> 
            <div class="tab-pane active" id="tab-general">
              <?php include('filter_form/tab_general.tpl'); ?>     
            </div>

            <div id="tab-other" class="tab-pane">
              <?php include('filter_form/tab_data.tpl'); ?>     
            </div>
          </div>  
          <input type="hidden" name="filter_value" value="" id="input-filter-value" />
        </form>
        <div class="tab-content"> 
          <div id="tab-values" class="tab-pane">
            <?php include('filter_form/tab_values.tpl'); ?>  
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
var submitForm = function(action) {
  var values = [], value = [], description = {}, $item;
  
  $('#filter-value-list > li').each(function() {
    $item = $(this);
    
    value = [
      $item.find('input[name$="[value_id]"]').val(),
      $item.find('input[name$="[image]"]').val(),
      $item.find('input[name$="[color]"]').val(),
      $item.find('input[name$="[sort_order]"]').val(),
      $item.find('input[name$="[keyword]"]').val()
    ];
    
    description = {};
    
    $item.find('input[name$="[name]"]').each(function() {
      description[$(this).attr('name').match(/\[(\d+)\]\[name\]/)[1]] = $(this).val();
    });
    
    value.push(description);
    
    values.push(value);
  });      

  $('#input-filter-value').val(JSON.stringify(values));

  values = [];
  
  $('#form-filter').attr('action', action).submit();
};

$(function() {
  if (window.location.hash && window.location.hash.substring(0, 10) == '#value_id=') {   
    $('a[href="#tab-values"]').tab('show');
    
    setTimeout(function() {
      var value_id = window.location.hash.substring(10), $input = $('input[value="' + value_id + '"]');
      
      if ($input.length > 0) {     
        $('html, body').stop().animate({ scrollTop: $input.closest('li').addClass('list-group-item-danger').offset().top - 20 }, 700, 'swing');
      }      
      
      window.location.hash = '';
    }, 200);   
  }
});  
</script>
<?php echo $footer; ?>