<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ocf-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
				<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add_page; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="popover" data-trigger="click" data-placement="bottom" data-html="true" data-content="<?php echo $text_confirm_delete_page; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>
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
      <script>
      var ocfDOMReady = function(fn) {
        if (document.readyState != 'loading') {
          fn();
        } else {
          document.addEventListener('DOMContentLoaded', fn);
        }
      };
      </script>
        
      <div class="panel-heading">
        <ul class="list-inline pull-right">
          <li><a href="https://ocfilter.com/documentation/4.8/#nav-seo-page-list" target="_blank"><i class="fa fa-fw fa-info-circle"></i> <?php echo $text_documentation; ?></a></li>
          <li><a href="https://ocfilter.com/faq/4.8/" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <?php echo $text_faq; ?></a></li>
        </ul>
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>     
      <div class="panel-body">
        <div class="well">  
          <div class="form-group mt-0 pt-0">
            <label class="control-label" for="input-edit-action"><?php echo $text_batch_edit; ?></label>
            <div class="mt-2">
              <?php include('page_list/form_batch_edit.tpl'); ?>  
            </div>                       
          </div>
          
          <div class="form-group mb-0 pb-0">
            <label class="control-label text-primary cursor-pointer" data-toggle="collapse" data-target="#collapse-add" aria-expanded="false"><u><?php echo $text_batch_add; ?></u> <i class="fa fa-angle-down"></i></label>
            <?php if (!empty($show_form)) { ?>
            <?php $class = 'collapse in'; ?>
            <?php } else { ?>
            <?php $class = 'collapse'; ?>
            <?php } ?>
            <div class="<?php echo $class; ?>" id="collapse-add">
              <?php include('page_list/form_batch_add.tpl'); ?>                
            </div>
          </div>          
        </div>
            
        <div class="row">
          <div class="col-sm-4 col-lg-3 col-sm-push-8 col-lg-push-9"> 
            <?php include('page_list/form_list_filter.tpl'); ?>
          </div>        
          <div class="col-sm-8 col-lg-9 col-sm-pull-4 col-lg-pull-3">          
            <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-list">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr class="active">
                      <td style="width: 1px;" class="text-center">
                        <input type="checkbox" onclick="$('input[name=\'selected[]\']').prop('checked', this.checked);" />
                      </td>
                      <td><?php echo $column_name; ?> </td>                     
                      <td><?php echo $column_category; ?></td>
                      <td><?php echo $column_view; ?></td>
                      <td><?php echo $column_status; ?></td>
                      <td style="width: 1px;"></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($pages as $page) { ?>
                    <?php if ($page['status']) { ?>
                    <?php $class = ''; ?>
                    <?php } else { ?>
                    <?php $class = 'active'; ?>
                    <?php } ?>
                    <tr class="<?php echo $class; ?>">
                      <?php if ($page['dynamic']) { ?>
                      <?php $class = 'text-center info'; ?>
                      <?php } else { ?>
                      <?php $class = 'text-center active'; ?>
                      <?php } ?>                    
                      <td class="<?php echo $class; ?>">
                        <?php if ($page['selected']) { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $page['page_id']; ?>" checked="checked" />
                        <?php } else { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $page['page_id']; ?>" />
                        <?php } ?>
                      </td>
                      <td>
                        <?php if ($page['dynamic_id']) { ?>
                        <div style="display: flex; align-items: center;">
                          <i class="fa fa-flip-horizontal fa-level-up"></i>
                          <input type="text" name="name" value="<?php echo $page['name']; ?>" class="form-control ocf-edit" for="<?php echo $page['page_id']; ?>" />
                        </div>                        
                        <?php } else { ?>
                        <input type="text" name="name" value="<?php echo $page['name']; ?>" class="form-control ocf-edit" for="<?php echo $page['page_id']; ?>" />
                        <?php } ?>                        
                      </td>
                      <td><?php echo $page['category_name']; ?></td>
                      <td>
                        <?php if ($page['category']) { ?>
                        <i class="fa fa-df fa-fw fa-th" data-toggle="tooltip" data-placement="top" title="<?php echo $entry_display_category; ?>"></i>
                        <?php } ?>
                        <?php if ($page['product']) { ?>
                        <i class="fa fa-df fa-fw fa-tags" data-toggle="tooltip" data-placement="top" title="<?php echo $entry_display_product; ?>"></i>
                        <?php } ?>
                        <?php if ($page['module']) { ?>
                        <i class="fa fa-df fa-fw fa-sliders" data-toggle="tooltip" data-placement="top" title="<?php echo $entry_display_module; ?>"></i>
                        <?php } ?>
                        <?php if ($page['sitemap']) { ?>
                        <i class="fa fa-df fa-fw fa-sitemap" data-toggle="tooltip" data-placement="top" title="<?php echo $entry_display_sitemap; ?>"></i>
                        <?php } ?>                        
                      </td>
                      <td>
                        <div class="btn-group" data-toggle="buttons">
                          <?php if ($page['status']) { ?>
                          <label class="btn btn-sm btn-default btn-info active">
                            <input type="checkbox" name="status" value="1" class="ocf-edit" for="<?php echo $page['page_id']; ?>" checked="checked" /> <i class="fa fa-lg fa-check-square fa-square-o"></i>
                          </label>
                          <?php } else { ?>
                          <label class="btn btn-sm btn-default">
                            <input type="checkbox" name="status" value="1" class="ocf-edit" for="<?php echo $page['page_id']; ?>" /> <i class="fa fa-lg fa-square-o"></i>
                          </label>
                          <?php } ?>
                        </div>
                      </td>
                      <td class="text-right">
                        <div class="text-nowrap">
                          <?php if (!$page['dynamic']) { ?>
                          <a href="<?php echo $page['show']; ?>" data-toggle="tooltip" title="<?php echo $button_show; ?>" class="btn btn-sm btn-default" target="_blank"><i class="fa fa-external-link-square"></i></a>
                          <?php } else { ?>
                          <button type="button" class="btn btn-sm btn-default disabled" disabled><i class="fa fa-external-link-square"></i></button>
                          <?php } ?>                          
                          <a href="<?php echo $page['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>
                        </div>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                  <tfoot>
                    <tr class="active">
                      <td></td>
                      <td colspan="5">
                        <?php if ($pages) { ?>
                        <div class="">
                          <ul class="list-inline mb-0">
                            <li><span class="label label-info" style="padding: 0 5px;">&nbsp;</span> <?php echo $text_dynamic; ?></li>
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
ocfDOMReady(function() {
$(function() {
  $('#form-list .ocf-edit').on('change', function() {
    var $this = $(this), field = encodeURIComponent($this.attr('name')), value = ($this.attr('type') == 'checkbox' ? (this.checked + 0) : encodeURIComponent(this.value));

    $this.fadeTo(250, .3);

    $.post(ocfilter.link('extension/module/ocfilter/page/editImmediately', 'page_id=' + $this.attr('for')), { field: field, value: value }, function(json) {
      if (json.status === true) {
        $this.fadeTo(250, 1).css('border-color', '#2390b0');

        if ($this.attr('type') == 'checkbox') {
          $this.parent().toggleClass('btn-info', $this.prop('checked')).find('.fa').toggleClass('fa-check-square', $this.prop('checked'));

          field == 'status' && $this.closest('tr').toggleClass('active', !$this.prop('checked'));
        }
      } else {
        $this.fadeTo(250, 1).css('border-color', '#f24545');
      }
    }, 'json');
  });
});
});
</script>
</div>
<?php echo $footer; ?>