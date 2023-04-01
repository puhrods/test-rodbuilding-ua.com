<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" />

<!-- Bootstrap 3.3.7 css -->
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/bootstrap/bootstrap.min.css" />

<!-- AdminLTE -->
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/AdminLTE.css" />
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/skin-blue.css" />
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/pace.min.css" />
<link rel="stylesheet" href="view/javascript/ompro/AdminLTE/jquery-ui.min.css" type="text/css"/>

<!-- ompro -->
<link rel="stylesheet" type="text/css" href="view/javascript/ompro/ompro.css?<?php echo $version; ?>" />

<script src="view/javascript/ompro/AdminLTE/jquery.min.js"></script>
<script src="view/javascript/ompro/AdminLTE/jquery-ui.min.js"></script>
<script src="view/javascript/ompro/AdminLTE/jquery.knob.min.js"></script>
<script>
  window.paceOptions = {
    ajax: {
      trackMethods: ["GET", "POST"]
    }
  };
</script>
<script src="view/javascript/ompro/AdminLTE/pace.min.js"></script>
<script src="view/javascript/ompro/AdminLTE/adminlte.js"></script>
<script src="view/javascript/ompro/AdminLTE/jquery.inputmask.js"></script>

<!-- Bootstrap 3.3.7 js -->
<script src="view/javascript/ompro/bootstrap/bootstrap.min.js"></script>
<!-- airdatepicker -->
<link type="text/css" rel="stylesheet" href="view/javascript/ompro/airdatepicker.min.css" />
<script type="text/javascript" src="view/javascript/ompro/airdatepicker.min.js"></script>
<script type="text/javascript" src="view/javascript/ompro/airdatepicker.en.js"></script>
<!-- multiselect -->
<link rel="stylesheet" href="view/javascript/ompro/bootstrap-multiselect.css" type="text/css"/>
<script type="text/javascript" src="view/javascript/ompro/bootstrap-multiselect.js"></script>

<style type="text/css" title="ompro">.CodeMirror { height: auto !Important; }</style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<script type="text/javascript"><!--

if (localStorage.getItem('sidebar-collapse') == 'collapse') {
	$('body').addClass('sidebar-collapse');
} else {
	$('body').removeClass('sidebar-collapse');
}

function getURLVar(key) {
	var value = [];
	var query = document.location.search.split('?');
	if (query[1]) {
		var part = query[1].split('&');
		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}
		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

//--></script>

<header class="main-header">
<a href="#" class="logo" alt="<?php echo $heading_title; ?>" data-toggle="push-menu" role="button">
  <span class="logo-mini" data-toggle="tooltip" data-placement="bottom" title="<?php echo $text_hide_menu; ?>"><i class="fa fa-indent" style="font-size: 14px;" ></i></span>
  <span class="logo-lg"><small class="pull-left"><i class="fa fa-dedent" style="font-size: 14px;" data-toggle="tooltip" data-placement="bottom" title="<?php echo $text_show_menu; ?>"></i><span data-toggle="tooltip" data-placement="bottom" title="<?php echo $heading_title; ?>"><span style="font-size: 12px;">&nbsp;&nbsp;<?php echo 'Order Manager PRO v.'.$version; ?></span></span></small></span>
</a>

<nav class="navbar navbar-static-top">
  <ul class="nav navbar-nav pull-left">
	<li>
		<a href="http://brest001.ru/ompro_doc/ompro_capability.html" target="_blank"><i class="fa fa-question-circle"></i>&nbsp;&nbsp; <?php echo $text_help; ?></a>
	</li>
  </ul>
  <div class="navbar-custom-menu">
	<ul class="nav navbar-nav pull-right">
	  <li class="dropdown notifications-menu">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $text_stores; ?>&nbsp;&nbsp;<i class="fa fa-cart-arrow-down"></i></a>
		<ul class="dropdown-menu">
		  <li class="header"><?php echo $text_stores; ?></li>
		  <li>
			<ul class="menu">
			<?php foreach ($stores as $store) { ?>
			  <li><a href="<?php echo $store['href']; ?>" target="_blank"><i class="fa fa-shopping-cart text-aqua"></i> <?php echo $store['name']; ?></a></li>
			<?php } ?>
			</ul>
		  </li>
		</ul>
	  </li>
	  <li>
		<a href="<?php echo $home; ?>"><?php echo $text_homepage; ?>&nbsp;&nbsp;<i class="fa fa-dashboard"></i></a>
	  </li>
	  <li>
		<a href="<?php echo $logout; ?>"><?php echo $text_logout; ?>&nbsp;&nbsp;<i class="fa fa-sign-out"></i></a>
	  </li>
	</ul>
  </div>
</nav>
</header>

<aside class="main-sidebar">
<section class="sidebar">
  <div class="user-panel">
  <?php if ($image) { ?>
	<div class="pull-left image">
	  <img src="<?php echo $image; ?>" class="img-circle" alt="<?php echo $firstname; ?> <?php echo $lastname; ?>" title="<?php echo $username; ?>">
	</div>
  <?php } else { ?>
	<div class="pull-left image text-info">
	  <i class="fa fa-opencart"></i>
	</div>
  <?php } ?>
	<div class="pull-left info">
	  <p><?php echo $firstname; ?> <?php echo $lastname; ?></p>
	  <?php if ($logged) { ?>
	  <a><i class="fa fa-circle text-success"></i> OnLine</a>
	  <?php } else { ?>
	  <a><i class="fa fa-circle text-danger"></i> OffLine</a>
	  <?php } ?>
	</div>
  </div>
  <ul class="sidebar-menu" data-widget="tree">
	<li class="accent"><a href="<?php echo $pages_orders; ?>"><i class="fa fa-shopping-cart"></i> <span><?php echo $text_order_manager; ?></span></a></li>

	<li class="header"><?php echo $text_header_settings; ?></li>

	<li class="<?php echo $admin_active; ?>"><a href="<?php echo $admin; ?>"><i class="fa fa-cog"></i><span><?php echo $text_global_setting; ?></span></a></li>

	<li class="treeview <?php echo $group_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-users"></i> <span><?php echo $text_group_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<?php foreach ($user_groups as $user_group) { ?>
			<li class="<?php echo $user_group['active']; ?>">
				<a href="<?php echo $user_group['href']; ?>"><i class="fa fa-user"></i> <span><?php echo $user_group['name']; ?></span></a>
			</li>
		<?php } ?>
	  </ul>
	</li>

	<li class="treeview <?php echo $fields_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-warning"></i> <span><?php echo $text_fields_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $order_fields_active; ?>"><a href="<?php echo $order_fields; ?>" ><i class="fa fa-shopping-cart"></i><span><?php echo $text_order_fields; ?></span></a></li>
		<li class="<?php echo $order_as_fields_active; ?>"><a href="<?php echo $order_as_fields; ?>"><i class="fa fa-cart-plus"></i><span><?php echo $text_order_as_fields; ?></span></a></li>
		<?php if ($simple_status) { ?>
			<li class="<?php echo $order_simple_fields_active; ?>"><a href="<?php echo $order_simple_fields; ?>"><i class="fa fa-cart-plus"></i><span><?php echo $text_order_simple_fields; ?></span></a></li>
		<?php } ?>
		<li class="<?php echo $product_fields_active; ?>"><a href="<?php echo $product_fields; ?>"><i class="fa fa-tag"></i><span><?php echo $text_product_fields; ?></span></a></li>
		<li class="<?php echo $product_as_fields_active; ?>"><a href="<?php echo $product_as_fields; ?>"><i class="fa fa-tags"></i><span><?php echo $text_product_as_fields; ?></span></a></li>
	  </ul>
	</li>

	<li class="header"><?php echo $text_header_setting_tpl; ?></li>

	<li class="treeview <?php echo $tpl_data_list_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-file-text-o"></i> <span><?php echo $text_tpl_data_list_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $pages_active; ?>"><a href="<?php echo $pages; ?>"><i class="fa fa-file-text"></i><span><?php echo $text_pages; ?></span></a></li>
		<li class="<?php echo $orders_active; ?>"><a href="<?php echo $orders; ?>"><i class="fa fa-table"></i><span><?php echo $text_template_orders; ?></span></a></li>
		<li class="<?php echo $product_active; ?>"><a href="<?php echo $product_list; ?>" ><i class="fa fa-th-list"></i><span><?php echo $text_template_product_list; ?></span></a></li>
		<li class="<?php echo $history_active; ?>"><a href="<?php echo $history_list; ?>"><i class="fa fa-history"></i><span><?php echo $text_template_history_list; ?></span></a></li>
		<li class="<?php echo $filter_active; ?>"><a href="<?php echo $filter_list; ?>"><i class="fa fa-filter"></i><span><?php echo $text_template_filter_list; ?></span></a></li>
		<li class="<?php echo $filter_product_active; ?>"><a href="<?php echo $filter_product_list; ?>"><i class="fa fa-filter"></i><span><?php echo $text_template_filter_product_list; ?></span></a></li>
		<li class="<?php echo $option_active; ?>"><a href="<?php echo $option_list; ?>"><i class="fa fa-list-ol"></i><span><?php echo $text_template_options_list; ?></span></a></li>
	  </ul>
	</li>

	<li class="treeview <?php echo $tpl_notify_list_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-mail-forward"></i> <span><?php echo $text_tpl_notify_list_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $mail_active; ?>"><a href="<?php echo $mail_list; ?>"><i class="fa fa-envelope-o"></i><span><?php echo $text_template_mail_list; ?></span></a></li>
		<li class="<?php echo $sms_active; ?>"><a href="<?php echo $sms_list; ?>"><i class="fa fa-mobile"></i><span><?php echo $text_template_sms_list; ?></span></a></li>
		<li class="<?php echo $tlgrm_active; ?>"><a href="<?php echo $tlgrm_list; ?>"><i class="fa fa-location-arrow"></i><span><?php echo $text_template_tlgrm_list; ?></span></a></li>
		<li class="<?php echo $comment_active; ?>"><a href="<?php echo $comment_list; ?>"><i class="fa fa-comments-o"></i><span><?php echo $text_template_comment_list; ?></span></a></li>
	  </ul>
	</li>

	<li class="treeview <?php echo $tpl_export_list_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-upload"></i> <span><?php echo $text_tpl_export_list_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $print_orders_active; ?>"><a href="<?php echo $print_orders_list; ?>"><i class="fa fa-print"></i><span><?php echo $text_template_print_orders_list; ?></span></a></li>
		<li class="<?php echo $print_orders_table_active; ?>"><a href="<?php echo $print_orders_table_list; ?>"><i class="fa fa-print"></i><span><?php echo $text_template_print_orders_table_list; ?></span></a></li>
		<li class="<?php echo $print_products_table_active; ?>"><a href="<?php echo $print_products_table_list; ?>"><i class="fa fa-print"></i><span><?php echo $text_template_print_products_table_list; ?></span></a></li>
		<li class="<?php echo $excel_orders_active; ?>"><a href="<?php echo $excel_orders_list; ?>"><i class="fa fa-file-excel-o"></i><span><?php echo $text_template_excel_orders_list; ?></span></a></li>
		<li class="<?php echo $excel_orders_products_active; ?>"><a href="<?php echo $excel_orders_products_list; ?>"><i class="fa fa-file-excel-o"></i><span><?php echo $text_template_excel_orders_products_list; ?></span></a></li>
	  </ul>
	</li>

	<li class="treeview <?php echo $page_block_list_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-th"></i> <span><?php echo $text_page_block_list_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $page_block_blocks_el_active; ?>"><a href="<?php echo $page_block_blocks_el; ?>" ><i class="fa fa-th-large"></i><span><?php echo $text_page_block_blocks_el_list; ?></span></a></li>
		<li class="<?php echo $page_block_column_el_active; ?>"><a href="<?php echo $page_block_column_el; ?>" ><i class="fa fa-th-list"></i><span><?php echo $text_page_block_column_el_list; ?></span></a></li>
		<li class="<?php echo $page_block_tools_el_active; ?>"><a href="<?php echo $page_block_tools_el; ?>"><i class="fa fa-table"></i><span><?php echo $text_page_block_tools_el_list; ?></span></a></li>
		<li class="<?php echo $page_block_btngroup_el_active; ?>"><a href="<?php echo $page_block_btngroup_el; ?>"><i class="fa fa-envelope-o"></i><span><?php echo $text_page_block_btngroup_el_list; ?></span></a></li>
	  </ul>
	</li>

	<li class="treeview <?php echo $block_list_treeview_active; ?>">
	  <a href="#">
		<i class="fa fa-th-large"></i> <span><?php echo $text_block_list_treeview; ?></span>
		<span class="pull-right-container">
		  <i class="fa fa-angle-left pull-right"></i>
		</span>
	  </a>
	  <ul class="treeview-menu">
		<li class="<?php echo $block_product_active; ?>"><a href="<?php echo $block_product; ?>" ><i class="fa fa-th-list"></i><span><?php echo $text_block_product_list; ?></span></a></li>
		<li class="<?php echo $block_pptrigger_active; ?>"><a href="<?php echo $block_pptrigger; ?>" ><i class="fa fa-th-list"></i><span><?php echo $text_block_pptrigger_list; ?></span></a></li>
		<li class="<?php echo $block_order_active; ?>"><a href="<?php echo $block_order; ?>"><i class="fa fa-table"></i><span><?php echo $text_block_orders; ?></span></a></li>
		<li class="<?php echo $block_mail_active; ?>"><a href="<?php echo $block_mail; ?>"><i class="fa fa-envelope-o"></i><span><?php echo $text_block_mail_list; ?></span></a></li>
		<li class="<?php echo $block_print_orders_table_active; ?>"><a href="<?php echo $block_print_orders_table; ?>"><i class="fa fa-print"></i><span><?php echo $text_block_print_orders_table_list; ?></span></a></li>
		<li class="<?php echo $block_print_products_table_active; ?>"><a href="<?php echo $block_print_products_table; ?>"><i class="fa fa-print"></i><span><?php echo $text_block_print_products_table_list; ?></span></a></li>
	  </ul>
	</li>
  </ul>
</section>
</aside>

<script type="text/javascript"><!--

// https://github.com/SortableJS/Sortable

function sortableDestroy(dropElSelector = '') {
	if (dropElSelector) {
		$(dropElSelector).each(function(){
			sort = Sortable.get($(this)[0]);
			if (sort) { sort.destroy(); }
		});
	}
}

function sortableStartGroup(dropElSelector = '', param = '') {
	if (dropElSelector) {
		$(dropElSelector).each(function(){
			var el = $(this)[0];

			if (Sortable.get(el)) {
				sortableDestroy($(this));
			}

			if (!Sortable.get(el)) {
				if (param) {
					Sortable.create(el, {
						group: param['group'] ? param['group'] : '',
						handle: param['handle'] ? param['handle'] : '',
						filter: param['filter'] ? param['filter'] : null,
						draggable: param['draggable'] ? param['draggable'] : '>*',
						ghostClass: param['ghostClass'] ? param['ghostClass'] : 'sortable-ghost',
						chosenClass: param['chosenClass'] ? param['chosenClass'] : 'sortable-chosen',
						dragClass: param['dragClass'] ? param['dragClass'] : 'sortable-drag',
						animation: param['animation'] ? param['animation'] : 150,
						onChange: function(evt) {
							if ($('.omanager-content').attr('id') == 'editor') {
								$('#form-editor').trigger('change');
							}
						}
					});
				} else {
					return Sortable.create(el);
				}
			}
		});
	}
}

//--></script>