<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$is_cli = substr(strtolower(php_sapi_name()), 0, 3) == 'cli' || empty($_SERVER['REMOTE_ADDR']);

$DIR_ADMIN = rtrim(preg_replace('/(\/|\\\)controller(\/|\\\).+?$/i', '', dirname(__FILE__)), '/') . '/';

// Configuration
if (is_file($DIR_ADMIN . 'config.php')) {
	require_once($DIR_ADMIN . 'config.php');
} else {
  exit('Config is not found' . PHP_EOL);
}

if (!defined('VERSION')) {
  if (is_file(DIR_SYSTEM . 'engine/router.php')) {
    define('VERSION', '3.0');
  } else if (is_file(DIR_SYSTEM . 'framework.php')) {
    define('VERSION', '2.3');
  } else {
    define('VERSION', '2.1');
  }
}

function library($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once($file);

		return true;
	} else {
		return false;
	}
}

spl_autoload_register('library');
spl_autoload_extensions('.php');

function load($file) {
	if (is_file($file)) {
		include_once($file);
	}
}

load(DIR_SYSTEM . 'engine/event.php');
load(DIR_SYSTEM . 'engine/loader.php');
load(DIR_SYSTEM . 'engine/model.php');
load(DIR_SYSTEM . 'engine/registry.php');
load(DIR_SYSTEM . 'engine/proxy.php');

// Helper
load(DIR_SYSTEM . 'helper/general.php');
load(DIR_SYSTEM . 'helper/utf8.php');
load(DIR_SYSTEM . 'helper/json.php');

// Registry
$registry = new Registry();

// Config
$config = new Config();

if (is_file(DIR_CONFIG . 'default.php')) {
  $config->load('default');
}

if (is_file(DIR_CONFIG . 'admin.php')) {
  $config->load('admin');
}

$registry->set('config', $config);

$event = new Event($registry);
$registry->set('event', $event);

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Database
$db = new DB($config->get('db_engine') ? $config->get('db_engine') : $config->get('db_type'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port'));
  
$registry->set('db', $db);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '0'");

foreach ($query->rows as $setting) {
  if (!$setting['serialized']) {
    $config->set($setting['key'], $setting['value']);
  } else {
    $config->set($setting['key'], json_decode($setting['value'], true));
  }
}

// Language
$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE code = '" . $db->escape($config->get('config_admin_language')) . "'");

if ($query->num_rows) {
  $config->set('config_language_id', $query->row['language_id']);
}

// Cache
$registry->set('cache', new Cache($config->get('cache_engine') ? $config->get('cache_engine') : $config->get('cache_type'), $config->get('cache_expire')));

$file = DIR_SYSTEM . 'library/ocfilter/init.php';

if (file_exists($file)) {
  include_once($file);

  initOCFilter($registry);
}

$ocfilter = $registry->get('ocfilter');

if (!$ocfilter) {
  exit('OCFilter is not installed' . PHP_EOL);
}

if (!$ocfilter->config('status')) {
  exit('OCFilter is disable by setting' . PHP_EOL);
}

$loader->model('extension/module/ocfilter/filter');

if (!$ocfilter->config('copy_cron_wget') && !$is_cli) {
	exit('CLI Only' . PHP_EOL);
}

$registry->get('model_extension_module_ocfilter_filter')->copyFilters([
  'copy_attribute' => $ocfilter->config('copy_attribute'),
  'copy_group_as_attribute' => $ocfilter->config('copy_group_as_attribute'),
  'copy_attribute_id_exclude' => $ocfilter->config('copy_attribute_id_exclude'),
  'copy_attribute_id' => is_null($ocfilter->config('copy_attribute_id')) ? [] : $ocfilter->config('copy_attribute_id'),  
  'copy_attribute_group_id_exclude' => $ocfilter->config('copy_attribute_group_id_exclude'),
  'copy_attribute_group_id' => is_null($ocfilter->config('copy_attribute_group_id')) ? [] : $ocfilter->config('copy_attribute_group_id'),  
  'copy_attribute_category_id_exclude' => $ocfilter->config('copy_attribute_category_id_exclude'),
  'copy_attribute_category_id' => is_null($ocfilter->config('copy_attribute_category_id')) ? [] : $ocfilter->config('copy_attribute_category_id'),  
  'copy_filter' => $ocfilter->config('copy_filter'),
  'copy_option' => $ocfilter->config('copy_option'),
  'copy_option_in_stock' => $ocfilter->config('copy_option_in_stock'),
  'copy_type' => $ocfilter->config('copy_type'),
  'copy_dropdown' => $ocfilter->config('copy_dropdown'),
  'copy_status' => $ocfilter->config('copy_status'),
  'copy_truncate' => $ocfilter->config('copy_truncate'),
  'copy_category' => $ocfilter->config('copy_category'),
  'copy_value_separator' => $ocfilter->config('copy_value_separator'),
]);