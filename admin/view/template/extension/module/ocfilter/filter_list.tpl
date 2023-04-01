<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ocf-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
				<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add_filter; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="popover" data-trigger="click" data-placement="bottom" data-html="true" data-content="<?php echo $text_confirm_delete_filter; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
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
          <li><a href="https://ocfilter.com/documentation/4.8/#nav-filter-list" target="_blank"><i class="fa fa-fw fa-info-circle"></i> <?php echo $text_documentation; ?></a></li>
          <li><a href="https://ocfilter.com/faq/4.8/" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <?php echo $text_faq; ?></a></li>
        </ul>
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>       
      <div class="panel-body">
        <div class="row">
          <div class="col-lg-3 col-lg-push-9"> 
            <?php include('filter_list/form_list_filter.tpl'); ?>
          </div>        
          <div class="col-lg-9 col-lg-pull-3"> 
            <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-list">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr class="active">
                      <td style="width: 1px;" class="text-center">
                        <input type="checkbox" onclick="$('input[name=\'selected[]\']').prop('checked', this.checked);" />
                      </td>
                      <td class="<?php echo ($sort == 'ofd.name' ? 'info' : ''); ?>" style="min-width: 250px;">                        
                        <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?> <i class="fa fa-sort-<?php echo ($order == 'DESC' ? 'asc' : 'desc'); ?>"></i></a>
                        <?php if ($sort == 'ofd.name') { ?>
                        <a href="<?php echo $reset_sort; ?>" class="text-danger pull-right"><i class="fa fa-times-circle"></i></a>                     
                        <?php } ?>                           
                      </td>                     
                      <td class="text-right<?php echo ($sort == 'total_values' ? ' info' : ''); ?>" colspan="2">                        
                        <a href="<?php echo $sort_total_values; ?>"><?php echo $column_values; ?> <i class="fa fa-sort-<?php echo ($order == 'DESC' ? 'asc' : 'desc'); ?>"></i></a>                        
                        <?php if ($sort == 'total_values') { ?>
                        <a href="<?php echo $reset_sort; ?>" class="text-danger"><i class="fa fa-times-circle"></i></a>                
                        <?php } ?>                        
                      </td>
                      <td class="<?php echo ($sort == 'numeric' ? 'info' : ''); ?>">
                        <?php echo $column_type; ?> 
                        <div class="pull-right">
                          <a href="<?php echo $sort_numeric; ?>"><?php echo $column_numeric; ?> <i class="fa fa-sort-<?php echo ($order == 'DESC' ? 'asc' : 'desc'); ?>"></i></a>
                          <?php if ($sort == 'numeric') { ?>
                          <a href="<?php echo $reset_sort; ?>" class="text-danger"><i class="fa fa-times-circle"></i></a>
                          <?php } ?>
                        </div> 
                      </td>
                      <td class="text-right text-nowrap<?php echo ($sort == 'of.sort_order' ? ' info' : ''); ?>">
                        <a href="<?php echo $sort_order; ?>"><?php echo $column_sort_order; ?> <i class="fa fa-sort-<?php echo ($order == 'DESC' ? 'asc' : 'desc'); ?>"></i></a>
                        <?php if ($sort == 'of.sort_order') { ?>
                        <a href="<?php echo $reset_sort; ?>" class="text-danger"><i class="fa fa-times-circle"></i></a>
                        <?php } ?>                          
                      </td>
                      <td><?php echo $column_status; ?></td>
                      <td style="width: 1px;"></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($filters as $filter) { ?>
                    <?php if ($filter['status']) { ?>
                    <?php $class = ''; ?>
                    <?php } else { ?>
                    <?php $class = 'active'; ?>
                    <?php } ?>
                    <tr class="<?php echo $class; ?>">
                      <?php if ($filter['source'] == 'attribute') { ?>
                      <?php $class = 'warning'; ?>
                      <?php } else if ($filter['source'] == 'filter') { ?>
                      <?php $class = 'success'; ?>
                      <?php } else if ($filter['source'] == 'option') { ?>
                      <?php $class = 'info'; ?>            
                      <?php } else { ?>
                      <?php $class = 'bg-white'; ?>
                      <?php } ?>
                    
                      <td class="text-center <?php echo $class; ?>">
                        <?php if ($filter['selected']) { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $filter['filter_key']; ?>" checked="checked" />
                        <?php } else { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $filter['filter_key']; ?>" />
                        <?php } ?>
                      </td>
                      <td>
                        <input type="text" name="name" value="<?php echo $filter['name']; ?>" class="form-control ocf-edit" for="<?php echo $filter['filter_key']; ?>" />
                      </td>
                      <td class="text-right">
                        <?php foreach ($filter['values'] as $value) { ?>
                        <span class="label label-ocf-value"><?php echo $value; ?></span>
                        <?php } ?>                   
                      </td>
                      <td class="text-right">
                        <?php if ($filter['total_values'] > 0) { ?>
                        <?php if (($filter['total_values'] - 5) > 0) { ?>
                        <span class="text-nowrap">+ <b><?php echo ($filter['total_values'] - 5); ?></b></span>
                        <?php } else { ?>
                        <b><?php echo $filter['total_values']; ?></b>
                        <?php } ?>
                        <?php } else { ?>
                        <span class="text-muted">0</span>
                        <?php } ?>
                      </td>
                      <td>
                        <select class="form-control input-sm ocf-edit" name="type" for="<?php echo $filter['filter_key']; ?>">
                          <?php if ($filter['type'] == 'checkbox') { ?>
                          <option value="checkbox" selected="selected"><?php echo $text_checkbox; ?></option>
                          <?php } else { ?>
                          <option value="checkbox"><?php echo $text_checkbox; ?></option>
                          <?php } ?>
                          <?php if ($filter['type'] == 'radio') { ?>
                          <option value="radio" selected="selected"><?php echo $text_radio; ?></option>
                          <?php } else { ?>
                          <option value="radio"><?php echo $text_radio; ?></option>
                          <?php } ?>
                          <?php if ($filter['type'] == 'slide') { ?>
                          <option value="slide" selected="selected"><?php echo $text_slide; ?></option>
                          <?php } else { ?>
                          <option value="slide"><?php echo $text_slide; ?></option>
                          <?php } ?>
                          <?php if ($filter['type'] == 'slide_dual') { ?>
                          <option value="slide_dual" selected="selected"><?php echo $text_slide_dual; ?></option>
                          <?php } else { ?>
                          <option value="slide_dual"><?php echo $text_slide_dual; ?></option>
                          <?php } ?>                             
                        </select>
                      </td>
                      <td class="text-right">
                        <input type="number" name="sort_order" value="<?php echo $filter['sort_order']; ?>" class="form-control ocf-edit" for="<?php echo $filter['filter_key']; ?>" style="text-align: right; max-width: 80px;" />
                      </td>
                      <td>
                        <div class="btn-group" data-toggle="buttons">
                          <?php if ($filter['status']) { ?>
                          <label class="btn btn-sm btn-default btn-info active">
                            <input type="checkbox" name="status" value="1" class="ocf-edit" for="<?php echo $filter['filter_key']; ?>" checked="checked" /> <i class="fa fa-lg fa-check-square fa-square-o"></i>
                          </label>
                          <?php } else { ?>
                          <label class="btn btn-sm btn-default">
                            <input type="checkbox" name="status" value="1" class="ocf-edit" for="<?php echo $filter['filter_key']; ?>" /> <i class="fa fa-lg fa-square-o"></i>
                          </label>
                          <?php } ?>
                        </div>
                      </td>
                      <td class="text-right">
                        <a href="<?php echo $filter['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                      </td>
                    </tr>
                    <?php } ?> 
                  </tbody>
                  <tfoot>
                    <tr class="active">
                      <td></td>
                      <td colspan="7">
                        <?php if ($filters) { ?>
                        <div class="">
                          <ul class="list-inline mb-0">
                            <li><span class="label bg-white border" style="padding: 0 5px;">&nbsp;</span> <?php echo $text_source_default; ?></li>
                            <li><span class="label label-warning" style="padding: 0 5px;">&nbsp;</span> <?php echo $text_source_attribute; ?></li>
                            <li><span class="label label-success" style="padding: 0 5px;">&nbsp;</span> <?php echo $text_source_filter; ?></li>
                            <li><span class="label label-primary" style="padding: 0 5px;">&nbsp;</span> <?php echo $text_source_option; ?></li>
                          </ul>
                        </div>   
                        <?php } else { ?>
                        <div class="text-center"><?php echo $text_no_results; ?></div>
                        <?php } ?>                            
                      </td>
                    </tr>
                  </tfoot>                   
                </table>
              </div>
            </form>
            <div class="row">
              <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
              <div class="col-sm-6 text-right"><?php echo $results; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<script>
$(function() {
  $('#form-list .ocf-edit').on('change', function() {
    var $this = $(this), field = encodeURIComponent($this.attr('name')), value = ($this.attr('type') == 'checkbox' ? (this.checked + 0) : this.value);

    $this.addClass('ocf-loading');

    $.post(ocfilter.link('extension/module/ocfilter/filter/editImmediately', 'filter_key=' + $this.attr('for')), { field: field, value: value }, function(json) {
      if (json.status === true) {
        $this.removeClass('ocf-loading').css('border-color', '#2390b0');

        if ($this.attr('type') == 'checkbox') {
          $this.parent().toggleClass('btn-info', $this.prop('checked')).find('.fa').toggleClass('fa-check-square', $this.prop('checked'));

          field == 'status' && $this.closest('tr').toggleClass('active', !$this.prop('checked'));
        }
      } else {
        $this.removeClass('ocf-loading').css('border-color', '#f24545');
      }
    }, 'json');
  });  
});  
</script>  
</div>
<?php echo $footer; ?>