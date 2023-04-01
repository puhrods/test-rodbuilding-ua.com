<!--
@author	Konstantin Kornelyuk
@link https://opencartforum.com/user/28448-brest001/?tab=idm
-->
<?php echo $header; ?>
<div class="content-wrapper" id="content-omanager">

<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/bootstrap-checkbox.min.js"></script>
<script type="text/javascript" src="view/javascript/ompro/bootstrap-checkbox/i18n/ru.js"></script>

<section class="content-header">
  <h1><?php echo $heading_global_title; ?></h1>
  <ol class="breadcrumb">
	<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<?php echo $breadcrumb; ?>
	<?php } ?>
  </ol>
</section>

<section class="content">
<form action="" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
  <div class="box box-default">
	<div class="box-header with-border">
		<div class="row">
		  <div class="col-sm-6 box-tools pull-right text-right">
			<a id="add_indexes" href="<?php echo $add_indexes; ?>" data-toggle="tooltip" title="Индексация таблиц БД: будут добавлены рекомендуемые индексы в таблицы базы данных. Данная операция выполняется один раз!" class="btn btn-default"><i class="fa fa-database"></i></a>
			<a id="button_save" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;&nbsp;<?php echo $button_save; ?></a>
			<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
		  </div>
		</div>
	</div>

	<div class="box-body">
		<div class="nav-tabs-custom">
		  <ul class="nav nav-tabs">
			<li><a href="#ttab_notify_target" role="tab" data-toggle="tab"><i class="fa fa-envelope"></i>&nbsp;&nbsp;<?php echo $text_tab_notify_target; ?></a></li>
			<li><a href="#tab_backup" data-toggle="tab"><i class="fa fa-database"></i>&nbsp;&nbsp;<?php echo $text_tab_backup; ?></a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane" id="ttab_notify_target">

			  <div class="panel panel-default">
				<div class="panel-body">
				  <div id="alert_log_sql" class="callout callout-default <?php if (!$log_sql) { ?>hidden-default<?php } ?>">
					<p><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_alert_log_sql; ?></p>
				  </div>
				  <div class="radio" style="padding-top: 0; padding-bottom: 0; min-height: auto;">
					<label><?php echo $text_log_sql; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_log_sql_info; ?>"></i></label>&nbsp;&nbsp;
					<label>
					  <input type="radio" name="log_sql" value="1" <?php if ($log_sql) { ?>checked<?php } ?> />
					  <?php echo $text_yes; ?>
					</label>
					<label>&nbsp;&nbsp;
					  <input type="radio" name="log_sql" value="0" <?php if (!$log_sql) { ?>checked<?php } ?> />
					  <?php echo $text_no; ?>
					</label>
				  </div>
				</div>
			  </div>

			  <div class="callout callout-default">
				<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_global&item=item_001"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;  <?php echo $text_tab_notify_target_info; ?></p>
			  </div>

			  <div class="form-group">
				<div class="col-sm-12">
				  <label class="control-label"><?php echo $entry_groupdefault; ?></label>
				  <div class="input-group">
					<span class="input-group-addon"><i class="fa fa-users"></i></span>
					<span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_groupdefault_info; ?>"><i class="fa fa-info text-blue"></i></span>
					<select name="settings[groupdefault]" class="form-control">
					<?php foreach ($user_groups as $user_group) { ?>
						<option value="<?php echo $user_group['user_group_id']; ?>" <?php if (isset($settings['groupdefault']) && $settings['groupdefault'] == $user_group['user_group_id']) { ?>selected="selected"<?php } ?>><?php echo $user_group['name']; ?></option>
					<?php } ?>
					</select>
				  </div>
				</div>
			  </div>

			   <div class="row">
				<div class="col-sm-12">
				  <div class="panel panel-default">
					<div class="panel-heading"><a data-toggle="collapse" href="#all-info"><i class="fa fa-sort text-blue"></i>&nbsp;&nbsp; <?php echo $text_tlgrm_setting_title; ?></a></div>
					<div id="all-info" class="panel-collapse collapse in">
					  <div class="panel-body">
						<div class="form-group">
							<div class="col-sm-12 valid-block">
								<div class="input-group">
								  <span class="input-group-addon" ><i class="fa fa-location-arrow"></i>&nbsp;&nbsp; <?php echo $entry_tlgrm_bot_token; ?></span>
								  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_tlgrm_bot_token_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
								  <input type="text"  name="settings[tlgrm_bot_token]"  value="<?php  echo $settings['tlgrm_bot_token']; ?>" class="field-input notcyrillics form-control" placeholder="token"/>
								  <span class="input-group-addon" ><?php echo $entry_tlgrm_admin_ides; ?></span>
								  <span class="input-group-addon" data-toggle="tooltip" title="<?php echo $entry_tlgrm_admin_ides_help; ?>">&nbsp;<i class="fa fa-info text-blue"></i>&nbsp;</span>
								  <input type="text"  name="settings[tlgrm_admin_ides]"  value="<?php  echo $settings['tlgrm_admin_ides']; ?>" class="field-input notcyrillics form-control" placeholder="123456789"/>
								</div>
							</div>
						</div>
						<div class="callout callout-default">
							<p><b><?php echo $text_tlgrm_setting_info; ?></b>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tlgrm_setting_info1; ?>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tlgrm_setting_info2; ?>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tlgrm_setting_info3; ?>
							<br><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tlgrm_setting_info4; ?>
							</p>
						</div>
					  </div>
					</div>
				  </div>
				</div>

				<div class="col-sm-12">
				  <div class="callout callout-default">
					<p><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_notify_target_help; ?></p>
				  </div>
				</div>

				<div class="col-sm-12">
				  <div class="table-responsive">
					<table class="table-mini table-hover full-width">
					  <thead>
						  <tr>
							<th style="width: 400px;"><?php echo $text_notify_target; ?></th>
							<th><?php echo $text_notify_mail_template; ?></th>
							<th><?php echo $text_notify_sms_template; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_notify_sms_template_help; ?>"></i></th>
							<th><?php echo $text_notify_tlgrm_template; ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $text_notify_tlgrm_template_help; ?>"></i></th>
						  </tr>
					  </thead>
					  <tbody>
						<?php foreach ($notify_target as $target) { ?>
						  <tr>
							<td><b><?php echo $target['name']; ?></b>
								<?php if ($target['key'] == 'new_order_manager') { ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $notify_new_order_manager_help; ?>"></i><?php } ?>
								<?php if ($target['key'] == 'new_order_courier') { ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $notify_new_order_courier_help; ?>"></i><?php } ?>
								<?php if ($target['key'] == 'target_manager') { ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $notify_target_manager_help; ?>"></i><?php } ?>
								<?php if ($target['key'] == 'target_courier') { ?>&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="<?php echo $notify_target_courier_help; ?>"></i><?php } ?>
							</td>
							<td>
							  <div class="input-group">
								<select name="settings[target_mail_template][<?php echo $target['key']; ?>]" class="select-notify form-control" data-notifytype="mail">
								  <?php if ($target['key'] == 'new_order_manager' || $target['key'] == 'target_manager' || $target['key'] == 'new_order_courier' || $target['key'] == 'target_courier') { ?>
									<option value="0"><?php echo $text_not_notify_mail; ?></option>
								  <?php } else { ?>
									<option value="0"><?php echo $text_mail_system_default; ?></option>
								  <?php } ?>
								  <?php foreach ($mail_templates as $template) { ?>
									<option value="<?php echo $template['template_id']; ?>" <?php if (isset($settings['target_mail_template'][$target['key']]) && $settings['target_mail_template'][$target['key']] == $template['template_id']) { ?> selected="selected"<?php } ?>><?php echo $template['template_id'] . ' - '. $template['name']; ?></option>
								  <?php } ?>
								</select>
								<div class="input-group-btn">
									<a href="" data-toggle="tooltip" title="<?php echo $button_template_edit; ?>" class="btn btn-primary" target="_blank"><i class="fa fa-edit"></i></a>
								</div>
							  </div>
							</td>
							<td>
							  <div class="input-group">
								<select name="settings[target_sms_template][<?php echo $target['key']; ?>]" class="select-notify form-control" data-notifytype="sms">
								  <?php if ($target['key'] == 'new_order_admin') { ?>
									<option value="0"><?php echo $text_system_notify_sms; ?></option>
								  <?php } else { ?>
									<option value="0"><?php echo $text_not_notify_sms; ?></option>
								  <?php } ?>
								  <?php foreach ($sms_templates as $template) { ?>
									<option value="<?php echo $template['template_id']; ?>" <?php if (isset($settings['target_sms_template'][$target['key']]) && $settings['target_sms_template'][$target['key']] == $template['template_id']) { ?> selected="selected"<?php } ?>><?php echo $template['template_id'] . ' - '. $template['name']; ?></option>
								  <?php } ?>
								</select>
								<div class="input-group-btn">
									<a href="" data-toggle="tooltip" title="<?php echo $button_template_edit; ?>" class="btn btn-primary" target="_blank"><i class="fa fa-edit"></i></a>
								</div>
							  </div>
							</td>
							<td>
							  <div class="input-group">
							  <?php if ($target['key'] == 'new_order_admin' || ($target['key'] == 'transaction' && $ocversion < 300) || $target['key'] == 'new_order_manager' || $target['key'] == 'target_manager' || $target['key'] == 'new_order_courier' || $target['key'] == 'target_courier') { ?>
								<select name="settings[target_tlgrm_template][<?php echo $target['key']; ?>]" class="select-notify form-control" data-notifytype="tlgrm">
								  <option value="0"><?php echo $text_not_notify_tlgrm; ?></option>
								  <?php foreach ($tlgrm_templates as $template) { ?>
									<option value="<?php echo $template['template_id']; ?>" <?php if (isset($settings['target_tlgrm_template'][$target['key']]) && $settings['target_tlgrm_template'][$target['key']] == $template['template_id']) { ?> selected="selected"<?php } ?>><?php echo $template['template_id'] . ' - '. $template['name']; ?></option>
								  <?php } ?>
								</select>
								<div class="input-group-btn">
									<a href="" data-toggle="tooltip" title="<?php echo $button_template_edit; ?>" class="btn btn-primary" target="_blank"><i class="fa fa-edit"></i></a>
								</div>
							  </div>
							  <?php } ?>
							</td>
						  </tr>
						<?php } ?>
					  </tbody>
					</table>
				  </div>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab_backup">
				<div class="callout callout-default">
					<p><a class="btn btn-info btn-xs" style="text-decoration: none;" href="http://brest001.ru/ompro_doc/ompro_setting.html#tab=tab_global&item=item_002"  target="_blank" data-toggle="tooltip" title="<?php echo $text_doc_link; ?>" ><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>&nbsp;&nbsp;   <?php echo $text_tab_backup_admin_all_help; ?>
					<br/><i class="fa fa-exclamation-triangle text-red"></i> <?php echo $text_tab_backup_admin_all_help2; ?>
					<br/><i class="fa fa-exclamation-triangle text-blue"></i> <?php echo $text_tab_backup_admin_all_help3; ?>
					</p>
				</div>
				<div class="panel panel-default">
				  <div class="panel-body">
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_restore_from_file; ?></label>
						<div class="col-sm-10">
							<table class="restore-table">
							  <tr>
								<td>
								  <input class="btn btn-default" type="file" name="importall"/>
								  <input name="restoreall" type="hidden" value="0" id="restoreall" />
								</td>
								<td>
								  <a class="btn btn-primary button-restoreall" disabled="disabled"><i class="fa fa-upload"></i>&nbsp;&nbsp;<?php echo $button_restore; ?></a>
								</td>
							  </tr>
							</table>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_save_to_file; ?></label>
						<div class="col-sm-10">
						  <a id="button-backupall" class="btn btn-primary"><i class="fa fa-download"></i>&nbsp;&nbsp;<?php echo $button_save ?></a>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
						  <div class="well well-sm" style="height: 420px; overflow: auto;">
							<a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $text_unselect_all; ?></a>
							<?php foreach ($ompro_tables as $table) { ?>
							<div class="checkbox">
							  <label>
								<input type="checkbox" name="backupall[]" value="<?php echo $table; ?>" checked="checked" />
								<?php echo $table; ?></label>
							</div>
							<?php } ?>
							<br/>
						    <a onclick="$(this).parent().find(':checkbox').prop('checked', true);"><?php echo $text_select_all; ?></a> / <a onclick="$(this).parent().find(':checkbox').prop('checked', false);"><?php echo $text_unselect_all; ?></a>
						  </div>
						</div>
					</div>
				  </div>
				</div>
			</div>
		  </div>
		</div>
	</div>
  </div>
</form>
</section>

<script type="text/javascript"><!--

var has_permission = '<?php echo $has_permission; ?>';
var token = getURLVar('token');
var strtoken = token ? '&token='+ token : '&user_token=' +getURLVar('user_token');
$('.select-notify').each(function() {
	var this_ = $(this);
	this_.bind('change', function() {
		var notifytype = this_.data('notifytype');
		var template_id = this_.val();
		var href = 'index.php?route=sale/ompro/templateEdit' + strtoken + '&get_page=' + notifytype +  '&template_id=' + template_id;
		this_.next().find('a').attr('href', href);
	});
	this_.trigger('change');
});

$('input[name=\'importall\']').bind('change', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		$('input[name=\'importall\']').val('');
		return false;
	}
	var imp = $('input[name=\'importall\']').val();
	if (imp) {
		$('.button-restoreall').attr('disabled', false);
	} else {
		$('.button-restoreall').attr('disabled', true);
	}
});

$('#button-backupall').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var url = '<?php echo $all_backup; ?>';
	url = url.replace(/&amp;/g, "&");
	$('#form').attr('action', url).submit();
});

$('.button-restoreall').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var imp = $('input[name=\'importall\']').val();
	if (!imp) { return false; }
	$('#restoreall').val(1);
	var url = '<?php echo $all_restore; ?>';
	url = url.replace(/&amp;/g, "&");
	$('#form').attr('action', url).submit();
});

$('#button_save').bind('click', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	} else if (!window.validation.isValid({ container: '#form' })) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p><?php echo $text_error_form; ?></p>', '');
		return false;
	} else {
		var url = '<?php echo $action; ?>';
		url = url.replace(/&amp;/g, "&");
		$('#form').attr('action', url).submit();
	}
});

$('.checkboxpicker').checkboxpicker();

$(document).delegate('#form', 'change', function() {
	if ($('#button_save').hasClass('btn-primary')) {
		$('#button_save').removeClass('btn-primary').addClass('btn-danger');
	}
});

$(document).ready(function(){
	$('.nav-tabs').each(function(){
		$(this).find('a:first').tab('show');
	});

	var error = '<?php echo $error_warning; ?>';
	var warning = '<?php echo $warning; ?>';
	var success = '<?php echo $success; ?>';
	if (error) {
		modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+error+'</p>', '');
	}
	if (warning) {
		modalAlert('modal-warning', '<?php echo $text_alert_warning; ?>', '<p>'+warning+'</p>', '');
	}
	if (success) {
		toastr.success(success, '<?php echo $text_alert_success; ?>');
	}

	iCheckStart();
	$(function () { window.validation.init({ container: '#form' });});
	tooltipRefresh();
});

$('input[name="log_sql"]').on('change', function() {
	if (!has_permission) {
		toastr.warning('<?php echo $error_permission; ?>', '<?php echo $text_alert_warning; ?>');
		return false;
	}
	var log_sql = $(this).val() == 1 ? 1 : 0;
	$.ajax({
		url: 'index.php?route=sale/ompro/editAdminLogSql&<?php echo $strtoken; ?>&log_sql=' + log_sql,
		dataType: 'json',
		beforeSend: function() {
			$('body').append('<div class="text-loading" style="position: fixed; top: 50%; left: 50%; opacity: 0.5;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only"><?php echo $text_loading; ?></span></div>');
		},
		success: function(json) {
			$('.text-loading').remove();
			if (json['error']) {
				modalAlert('modal-danger', '<?php echo $text_alert_error; ?>', '<p>'+json['error']+'</p>', '');
			}
			if (json['success']) {
				toastr.success(json['success'], '<?php echo $text_alert_success; ?>');
				if (log_sql) { $('#alert_log_sql').show('slow'); } else { $('#alert_log_sql').hide('slow'); }

			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('#add_indexes').bind('click', function(e) {
	if (!confirm("Будут добавлены рекомендуемые индексы в таблицы базы данных. Список добавляемых индексов см. в разжеле FAQ на сайте разработчика. Данная операция выполняется один (!) раз и в большинстве случаев значительно ускоряет обработку запросов, а значит и загрузку страниц заказов. Вы уверены, что хотите выполнить индексацию?!")) {
		e.preventDefault();
		return false;
	}
});

//--></script>

</div>
<?php echo $footer; ?>