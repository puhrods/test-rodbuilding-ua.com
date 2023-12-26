<?php

// Version
define('VERSION', '3.0.2.0');
// Configuration
if (is_file('../admin/config.php')) {
	require_once('../admin/config.php');
} elseif (dirname(__FILE__) . "/../admin/config.php") {
	require_once(dirname(__FILE__) . "/../admin/config.php");
}
require_once(DIR_SYSTEM . 'startup.php');

// Registry
$registry = new Registry();

// Config
$config = new Config();
$registry->set('config', $config);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

$config->set('config_url', HTTP_CATALOG);
$config->set('config_ssl', HTTPS_CATALOG);

// Settings
$query = $db->query("SELECT * FROM " . DB_PREFIX . "setting");
foreach ($query->rows as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], json_decode($setting['value'], true));
	}
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

function log1($message)
{
	file_put_contents(DIR_LOGS . "neoseo_exchange1c.log", date("Y-m-d H:i:s - ") . " " . $message . "\r\n", FILE_APPEND);
}

// Error Handler
function error_handler($errno, $errstr, $errfile, $errline)
{

	if (0 === error_reporting())
		return TRUE;
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}
	log1('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	return TRUE;
}

// Error Handler
set_error_handler('error_handler');

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$registry->set('response', $response);

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// Session
//$session = new Session();
//$registry->set('session', $session);
// Language
$languages = array();

$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language`");

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_language')]['language_id']);

// Language
$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Currency
$registry->set('currency', new Cart\Currency($registry));

// Weight
$registry->set('weight', new Cart\Weight($registry));

// Length
$registry->set('length', new Cart\Length($registry));

// User
$registry->set('user', new Cart\User($registry));


// Event
$event = new Event($registry);
$registry->set('event', $event);

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $value) {
		$event->register($key, new Action($value));
	}
}

// Router
if (isset($request->get['mode']) && $request->get['type'] == 'catalog') {

	switch ($request->get['mode']) {
		case 'checkauth':
			$action = new Action('tool/neoseo_exchange1c/modeCheckauth');
			break;

		case 'init':
			$action = new Action('tool/neoseo_exchange1c/modeCatalogInit');
			break;

		case 'file':
			$action = new Action('tool/neoseo_exchange1c/modeFile');
			break;

		case 'import':
			$action = new Action('tool/neoseo_exchange1c/modeImport');
			break;

		case 'export':
			$action = new Action('tool/neoseo_exchange1c/modeExport');
			break;

		case 'exportall':
			$action = new Action('tool/neoseo_exchange1c/modeExportAll');
			break;

		default:
			echo "success\n";
	}
} else if (isset($request->get['mode']) && $request->get['type'] == 'sale') {

	switch ($request->get['mode']) {
		case 'checkauth':
			$action = new Action('tool/neoseo_exchange1c/modeCheckauth');
			break;

		case 'init':
			$action = new Action('tool/neoseo_exchange1c/modeSaleInit');
			break;

		case 'file':
			$action = new Action('tool/neoseo_exchange1c/modeFile');
			break;

		case 'import':
			$action = new Action('tool/neoseo_exchange1c/modeImport');
			break;

		case 'query':
			$action = new Action('tool/neoseo_exchange1c/modeQueryOrders');
			break;

		case 'success':
			$action = new Action('tool/neoseo_exchange1c/modeSuccessOrders');
			break;

		default:
			echo "success\n";
	}
} else if (isset($request->get['mode']) && ($request->get['type'] == 'pdf' || $request->get['type'] == 'jpg') ) {

	switch ($request->get['mode']) {
		case 'checkauth':
			$action = new Action('tool/neoseo_exchange1c/modeCheckauth');
			break;

		case 'file':
			$action = new Action('tool/neoseo_exchange1c/modeFile');
			break;

		default:
			echo "success\n";
	}

} else {
	if (isset($argv[1])) {
		switch ($argv[1]) {
			case 'import':
				$action = new Action('tool/neoseo_exchange1c/importFile');
				break;
			default:
				echo "success\n";
		}
	}else{
		echo "success\n";
		exit;
	}
}
// Route
$route = new Router($registry);

// Pre Actions
if ($config->has('action_pre_action')) {
	foreach ($config->get('action_pre_action') as $value) {
		$route->addPreAction(new Action($value));
	}
}

if (isset($action)) {
// Dispatch
	$route->dispatch($action, new Action($config->get('action_error')));
}

// Output
$response->output();
?>
