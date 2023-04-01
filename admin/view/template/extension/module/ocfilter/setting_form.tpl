<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ocf-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" onclick="$('#form-ocfilter').attr('action', '<?php echo $save; ?>').submit();" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <button type="button" onclick="$('#form-ocfilter').attr('action', '<?php echo $apply; ?>').submit();" data-toggle="tooltip" title="<?php echo $button_apply; ?>" class="btn btn-success"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $heading_title_setting; ?></h1>
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
    <div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <ul class="list-inline pull-right">
          <li><a href="<?php echo $filter_list; ?>" target="_blank"><i class="fa fa-fw fa-code-fork"></i> <?php echo $text_filter_list; ?></a></li>
          <li><a href="<?php echo $filter_page_list; ?>" target="_blank"><i class="fa fa-fw fa-file-text-o"></i> <?php echo $text_filter_page_list; ?></a></li>
          <li><a href="https://ocfilter.com/documentation/4.8/#nav-setting-general" target="_blank"><i class="fa fa-fw fa-info-circle"></i> <?php echo $text_documentation; ?></a></li>
          <li><a href="https://ocfilter.com/faq/4.8/" target="_blank"><i class="fa fa-fw fa-question-circle"></i> <?php echo $text_faq; ?></a></li>
        </ul>
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="" method="post" enctype="multipart/form-data" id="form-ocfilter" class="form-horizontal">
          <div role="tabs">
            <ul class="nav nav-tabs" role="tablist">
              <li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-fw fa-asterisk"></i> <?php echo $tab_general; ?></a></li>
              <li new-feature><a href="#tab-special-filter" data-toggle="tab"><i class="fa fa-fw fa-sliders"></i> <?php echo $tab_special_filter; ?></a></li>
              <li new-feature><a href="#tab-seo" data-toggle="tab"><i class="fa fa-fw fa-globe"></i> <?php echo $tab_seo; ?></a></li>
              <li><a href="#tab-appearance" data-toggle="tab"><i class="fa fa-fw fa-tv"></i> <?php echo $tab_appearance; ?></a></li>
              <li new-feature><a href="#tab-placement" data-toggle="tab"><i class="fa fa-fw fa-arrows-alt"></i> <?php echo $tab_placement; ?></a></li>
              <li><a href="#tab-copy" data-toggle="tab"><i class="fa fa-fw fa-files-o"></i> <?php echo $tab_copy; ?></a></li>
            </ul>

            <div class="tab-content">
              <div id="tab-general" class="tab-pane active">
                <?php include('setting_form/tab_general.tpl'); ?>
              </div>

              <div id="tab-special-filter" class="tab-pane">
                <?php include('setting_form/tab_special_filter.tpl'); ?>
              </div>

              <div id="tab-seo" class="tab-pane">
                <?php include('setting_form/tab_seo.tpl'); ?>
              </div>

              <div id="tab-appearance" class="tab-pane">
                <?php include('setting_form/tab_appearance.tpl'); ?>
              </div>

              <div id="tab-placement" class="tab-pane">
                <?php include('setting_form/tab_placement.tpl'); ?>
              </div>

              <div id="tab-copy" class="tab-pane">
                <?php include('setting_form/tab_copy.tpl'); ?>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div> 
</div>
<?php echo $footer; ?>