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

</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<script type="text/javascript"><!--

if (localStorage.getItem('sidebar-collapse') == 'collapse') {
	$('body').addClass('sidebar-collapse');
} else {
	$('body').removeClass('sidebar-collapse');
}

//--></script>

<header class="main-header">
<a href="#" class="logo" alt="<?php echo $heading_title; ?>" data-toggle="push-menu" role="button">
  <span class="logo-mini" data-toggle="tooltip" data-placement="bottom" title="<?php echo $text_hide_menu; ?>"><i class="fa fa-indent" style="font-size: 14px;" ></i></span>
  <span class="logo-lg"><small class="pull-left"><i class="fa fa-dedent" style="font-size: 14px;" data-toggle="tooltip" data-placement="bottom" title="<?php echo $text_show_menu; ?>"></i><span data-toggle="tooltip" data-placement="bottom" title="<?php echo $heading_title; ?>"><span style="font-size: 12px;">&nbsp;&nbsp;<?php echo 'Order Manager PRO v.'.$version; ?></span></span></small></span>
</a>

<nav class="navbar navbar-static-top">
  <div class="navbar-custom-menu">
	<ul class="nav navbar-nav pull-right">
	  <li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php if($alerts > 0) { ?>  <span class="label label-danger pull-left"><?php echo $alerts; ?></span><?php } ?> <i class="fa fa-bell-o"></i></a>
		<ul class="dropdown-menu dropdown-menu-right alerts-dropdown" style="font-size: 13px;">
			<li class="dropdown-header"><?php echo $text_notifications; ?></li>
			<li class="divider"></li>
			<li class="dropdown-header"><?php echo $text_orders; ?></li>
			<li><a class="quick-filter-trigger" data-filter_id="filter_order_status_id"  data-filter_value="<?php echo $processing_status; ?>" style="display: block; overflow: auto; cursor: pointer;"><span class="label label-warning pull-right"><?php echo $processing_status_total; ?></span><?php echo $text_processing_status; ?></a></li>
			<li><a class="quick-filter-trigger" data-filter_id="filter_order_status_id"  data-filter_value="<?php echo $complete_status; ?>" style="display: block; overflow: auto; cursor: pointer;"><span class="label label-success pull-right"><?php echo $complete_status_total; ?></span><?php echo $text_complete_status; ?></a></li>
			<li><a href="<?php echo $return; ?>" target="_blank"><span class="label label-danger pull-right"><?php echo $return_total; ?></span><?php echo $text_return; ?></a></li>
			<li class="divider"></li>
			<li class="dropdown-header"><?php echo $text_customer; ?></li>
			<li><a href="<?php echo $online; ?>" target="_blank"><span class="label label-success pull-right"><?php echo $online_total; ?></span><?php echo $text_online; ?></a></li>
			<li class="divider"></li>
			<li class="dropdown-header"><?php echo $text_product; ?></li>
			<li><a href="<?php echo $product; ?>" target="_blank"><span class="label label-danger pull-right"><?php echo $product_total; ?></span><?php echo $text_stock; ?></a></li>
			<li><a href="<?php echo $review; ?>" target="_blank"><span class="label label-danger pull-right"><?php echo $review_total; ?></span><?php echo $text_review; ?></a></li>
		</ul>
	  </li>
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
	<?php echo $menu_setting; ?>
	<li class="header"><?php echo $text_header_orders; ?>
		<span class="pull-right-container">
			<span class="label label-warning pull-right" data-toggle="tooltip" title="<?php echo $text_processing_status; ?>"><?php echo $processing_status_total; ?></span>
			<span class="label label-success pull-right" data-toggle="tooltip" title="<?php echo $text_complete_status; ?>"><?php echo $complete_status_total; ?></span>
			<span class="label label-danger pull-right" data-toggle="tooltip" title="<?php echo $text_return; ?>"><?php echo $return_total; ?></span>
		</span>
	</li>
	<li class="treeview <?php echo $order_pages_treeview_active; ?>">
	<?php foreach ($order_pages as $page) { ?>
		<li id="pageid_<?php echo $page['id']; ?>" class="pageid <?php echo $page['active']; ?>">
			<a href="<?php echo $page['href']; ?>"><?php echo $page['icon']; ?><span><?php echo $page['name']; ?></span></a>
		</li>
	<?php } ?>
	</li>
  </ul>
</section>
</aside>
