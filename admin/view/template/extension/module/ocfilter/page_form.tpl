<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ocf-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" onclick="$('#form-page').attr('action', '<?php echo $save; ?>').submit();" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <ul class="list-inline pull-right">
          <li><a href="https://ocfilter.com/documentation/4.8/#nav-seo-page-form" target="_blank"><i class="fa fa-fw fa-info-circle"></i> <?php echo $text_documentation; ?></a></li>
          <li><a href="https://ocfilter.com/faq/4.8/" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <?php echo $text_faq; ?></a></li>
        </ul>      
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <script>
        var ocfDOMReady = function(fn) {
          if (document.readyState != 'loading') {
            fn();
          } else {
            document.addEventListener('DOMContentLoaded', fn);
          }
        };
        </script>
      
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-page" class="form-horizontal">       
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-fw fa-asterisk"></i> <?php echo $tab_general; ?></a></li>
            <li><a href="#tab-relation" data-toggle="tab"><i class="fa fa-fw fa-code-fork"></i> <?php echo $tab_relation; ?></a></li>
            <li><a href="#tab-data" data-toggle="tab"><i class="fa fa-fw fa-bars"></i> <?php echo $tab_data; ?></a></li>
            <li><a href="#tab-display" data-toggle="tab"><i class="fa fa-fw fa-link"></i> <?php echo $tab_display; ?></a></li>
          </ul>
          <div class="tab-content">          
            <div class="tab-pane active" id="tab-general">
              <?php include('page_form/tab_general.tpl'); ?>                   
            </div>

            <div class="tab-pane" id="tab-relation">
              <?php include('page_form/tab_relation.tpl'); ?>     
            </div>

            <div class="tab-pane" id="tab-data">
              <?php include('page_form/tab_data.tpl'); ?>        
            </div>
            
            <div class="tab-pane" id="tab-display">
              <?php include('page_form/tab_display.tpl'); ?>    
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>  

<script>

var 

buildMaskOptions = {
  container: '#page-mask-vars-list',
  relationContainer: '#filter-relation-content',
  textDefault: '<?php echo addslashes($text_info_mask_filter); ?>'
},

relationFormOptions = {
  page_id: '<?php echo $page_id; ?>',
  category_id: $('input[name="category_id"]').val(),
  selected: JSON.parse('<?php echo json_encode($ocfilter_filter); ?>'),
  container: '#filter-relation-content',

  onLoad: function() {
    ocfilter.buldMaskVarsList(buildMaskOptions);
    
    $('input[name="dynamic"]:checked').trigger('change');
  },
  
  onSelect: function() {
    ocfilter.buldMaskVarsList(buildMaskOptions);
  }
};

ocfDOMReady(function() {
$(function() { 
  $('#language a:first').tab('show');
  
  $('a[href="#tab-data"]').on('show.bs.tab', function() {
    ocfilter.buldMaskVarsList(buildMaskOptions);
    
    // Destroy CKEDITOR
    if ('undefined' != typeof CKEDITOR) {
      for (var name in CKEDITOR.instances) {
        CKEDITOR.instances[name].destroy();
      }       
    }       
  });  

  $('input[name="dynamic"]').on('change', function(e) { 
    var dynamic = (this.value > 0);
  
    $('#page-mask-vars-list').toggleClass('hidden', !dynamic).next('p').toggleClass('hidden', dynamic);
          
    if (!dynamic) {      
      $('#filter-relation-content').find('input[type="checkbox"][name^="ocfilter_filter"][value="0"]').prop('checked', false).trigger('change').prop('disabled', true).parent().addClass('disabled').removeClass('active');
    } else {
      $('#filter-relation-content').find('input[type="checkbox"][name^="ocfilter_filter"][value="0"]').prop('disabled', false).parent().removeClass('disabled');
    }     
       
    if (dynamic) { 
      $('#input-page-link-code').addClass('disabled').prop('disabled', true).text(''); 
    } else {
      $('#input-page-link-code').removeClass('disabled').prop('disabled', false).text($('#input-page-link-code').attr('data-code')); 
    }    
  }).filter(':checked').trigger('change');
});
});
</script>
</div>
<?php echo $footer; ?>