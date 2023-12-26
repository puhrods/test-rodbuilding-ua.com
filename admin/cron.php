<?php
define('VERSION', '3.0.2.0');

if (!ini_get('date.timezone')) {
	date_default_timezone_set('UTC');
}

$fh = fopen(__FILE__, 'r');
if (!flock($fh, LOCK_EX | LOCK_NB)) exit;

require_once(__DIR__ . '/config.php');

if (!isset($_SERVER['HTTP_HOST'])) {
	$_SERVER['HTTP_HOST'] = getenv('HTTP_HOST');
}

$_SERVER['HTTP_HOST'] = HTTP_CATALOG;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_PORT'] = 443;
chdir(DIR_APPLICATION);

// Check if SSL
if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
	$_SERVER['HTTPS'] = true;
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
	$_SERVER['HTTPS'] = true;
} else {
	$_SERVER['HTTPS'] = false;
}

// Modification Override
function modification($filename) {
	if (defined('DIR_CATALOG')) {
		$file = DIR_MODIFICATION . 'admin/' .  substr($filename, strlen(DIR_APPLICATION));
	} elseif (defined('DIR_OPENCART')) {
		$file = DIR_MODIFICATION . 'install/' .  substr($filename, strlen(DIR_APPLICATION));
	} else {
		$file = DIR_MODIFICATION . 'catalog/' . substr($filename, strlen(DIR_APPLICATION));
	}

	if (substr($filename, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
		$file = DIR_MODIFICATION . 'system/' . substr($filename, strlen(DIR_SYSTEM));
	}

	if (is_file($file)) {
		return $file;
	}

	return $filename;
}

function library($class) {
	$file = DIR_SYSTEM . 'library/' . str_replace('\\', '/', strtolower($class)) . '.php';

	if (is_file($file)) {
		include_once(modification($file));

		return true;
	} else {
		return false;
	}
}

spl_autoload_register('library');
spl_autoload_extensions('.php');

// Engine
require_once(modification(DIR_SYSTEM . 'engine/action.php'));
require_once(modification(DIR_SYSTEM . 'engine/controller.php'));
require_once(modification(DIR_SYSTEM . 'engine/event.php'));
require_once(modification(DIR_SYSTEM . 'engine/router.php'));
require_once(modification(DIR_SYSTEM . 'engine/loader.php'));
require_once(modification(DIR_SYSTEM . 'engine/model.php'));
require_once(modification(DIR_SYSTEM . 'engine/registry.php'));
require_once(modification(DIR_SYSTEM . 'engine/proxy.php'));

// Helper
require_once(DIR_SYSTEM . 'helper/general.php');
require_once(DIR_SYSTEM . 'helper/utf8.php');

class Cron {
	private $task = array();
	
	public function __construct() {
		$autor = 'usergio';
	}

	public function __get($name) {
		return $this->registry->get($name);
	}
	
	public function run($registry, $task, $param) {
		$this->registry = $registry;		
		$this->load->controller($task, $param);	

		return;
	}
}

$cron = new Cron;

// Registry
$registry = new Registry();

// Config
$config = new Config();
$config->load('default');
$config->load('admin');
$registry->set('config', $config);


// Log
$log = new Log($config->get('error_filename'));
$registry->set('log', $log);

date_default_timezone_set($config->get('date_timezone'));

function error_handler($message) {
		global $log, $config;

		// error suppressed with @
		if (error_reporting() === 0) {
			return false;
		}	
		if (strlen($message) > 5) {
			$file_er    = "./uploads/cron_errors.tmp";
			$er = @fopen($file_er,'a');
			if (!$er) $er = @fopen($file_er,'w+');
			@fputs($er, $message . " \n");
			@fclose($er);
		}
		return true;
	}			

set_error_handler('error_handler');

// Event
$event = new Event($registry);
$registry->set('event', $event);

// Event Register
if ($config->has('action_event')) {
	foreach ($config->get('action_event') as $key => $value) {
		foreach ($value as $priority => $action) {
			$event->register($key, new Action($action), $priority);
		}
	}
}

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Request
$registry->set('request', new Request());

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Database
if ($config->get('db_autostart')) {
	$registry->set('db', new DB($config->get('db_engine'), $config->get('db_hostname'), $config->get('db_username'), $config->get('db_password'), $config->get('db_database'), $config->get('db_port')));
}

//$sess_path = DIR_APPLICATION . 'sess/';
//ini_set('session.save_path', $sess_path);

// Session
$session = new Session($config->get('session_engine'), $registry);
$registry->set('session', $session);
//$session->start('cronssessionconstant1value');	
//$session->data['token'] = 'token0for0cron0constant100value0';

// Cache
$cache = new Cache('file');
$registry->set('cache', $cache);

// $db
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
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
$languages = array();
$query = $db->query("SELECT * FROM " . DB_PREFIX . "language"); 

foreach ($query->rows as $result) {
	$languages[$result['code']] = $result;
}

$config->set('config_language_id', $languages[$config->get('config_admin_language')]['language_id']);

$language = new Language($languages[$config->get('config_admin_language')]['directory']);
$language->load($languages[$config->get('config_admin_language')]['directory']);	
$registry->set('language', $language); 		

if ($config->has('language_autoload')) {
	foreach ($config->get('language_autoload') as $value) {
		$loader->language($value);
	}
}

// Route
$route = new Router($registry);

/***************************  ERRORS  ***************************************/
    function mess($err, $file) {
		switch ($err) {			
			case 1:	$message = "FTP: invalid login and/or password";
			break;
			case 2:	$message = 'FTP: failed FTP connect';
			break;
			case 3:	$message = 'FTP: file "' . $file . '" not found';
			break;
			case 4:	$message = 'LINK: ivalid file extension';
			break;
			case 5:	$message = 'LINK: can not create file in uploads';
			break;
			case 6:	$message = 'LINK: file "' . $file . '" not found';
			break;
			case 7:	$message = 'MAIL: empty password';
			break;
			case 8:	$message = "MAIL: empty sender&#039;s email";
			break;
			case 9:	$message = "MAIL: attachment not found";
			break;
		}
		if ($err) {
			error_handler($message);
			$file_er    = "./uploads/log_cron.tmp";
			$log = @fopen($file_er,'a');
			if (!$log) $log = @fopen($file_er,'w+');
			@fputs($log, date('Y-m-d H:i') . ' ' .$message ." \n");
			@fclose($log);
			return;
		}	
	}
/************************** tryConvert  *************************************/
	function tryConvert($form_id, $ext) {
	
		$convert = 0;
		$file_source = DIR_APPLICATION . 'uploads/' . $form_id . $ext;
		$file_target = DIR_APPLICATION . 'uploads/decode' . $form_id . $ext;		
		
		if (file_exists($file_source)) {
			$source = @fopen($file_source,'r');
			if ($source) {
				$st = @fgets($source, 4);
				if ((ord($st[0]) == 0xff and ord($st[1]) == 0xfe) or (ord($st[1]) == 0xff and ord($st[0]) == 0xfe)) {
					$target = @fopen($file_target,'w+');
				    $st = utf16_decode(file_get_contents($file_source));
					fwrite($target, $st);
					$convert = 1;
				} elseif (ord($st[0]) == 0xef and ord($st[1]) == 0xbb and ord($st[2]) == 0xbf) {			
					$target = @fopen($file_target,'w+');
					$st = file_get_contents($file_source);
					$st = substr($st, 3);
					fwrite($target, $st);
					$convert = 2;
				}
			}
			@fclose($source);
			@fclose($target);
		}	
		if (file_exists($file_source) and $convert) {
			unlink ($file_source);
			rename($file_target, $file_source);
		}
		
		$convert = 0;
	/*	if (file_exists($file_source)) {
			$source = @fopen($file_source,'r');
			if ($source) {
				if ($ext == '.csv' or $ext == '.xml') {
					$st = file_get_contents($file_source);
					if (substr_count($st, "\r") or substr_count($st, "\n")) {
						$target = @fopen($file_target,'w+');
						$st = str_replace("\n", "\r\n", $st);
						$st = str_replace("\n\n", "\n", $st);
				//		$st = str_replace("\r", "\r\n", $st);
				//		$st = str_replace("\r\r", "\r", $st);
						fwrite($target, $st);
						$convert = 1;
					}
					@fclose($source);
					if ($convert) @fclose($target);
					
					if (file_exists($file_source) and $convert) {
						unlink ($file_source);
						rename($file_target, $file_source);
					}
				}
			}
		}	*/
		return;
	}

/**************************  oups  ******************************************/	
	function oups($local_file) {
		$flag = 0;
		if (!file_exists($local_file) or filesize($local_file) < 600) return 1;
		$fp = @fopen($local_file,'r');
		if (!$fp) return 2;
		$a = fread($fp, 2048);
		if (!$a) $flag = 3;
		if ($a and (substr_count($a, '<form ') or substr_count($a, '<script '))) $flag = 4;
		if ($fp) fclose($fp);
		
		return $flag;
	}
/**************************  FTP  ********************************************/
	function get_ftp($form_id, $file_source, $ftp_server, $user_name, $user_pass, $ext, $go) {
		if (empty($ext)) return 1;		
		$ext = '.' . $ext;	
		$local_file = DIR_APPLICATION . 'uploads/' . $form_id . $ext;		
		if (file_exists($local_file) and $go) return 0;
		
		$port = 21;		
		
		if (!substr_count($ftp_server, '/')) {
			if (substr_count($ftp_server, ':')) {				
				$arr = explode(':', $ftp_server);
				$ftp_server = $arr[0];
				$port = $arr[1];
			}	
				
			if (function_exists('ftp_ssl_connect')) $conn_id = ftp_ssl_connect($ftp_server, $port, 15);
			else $conn_id = ftp_connect($ftp_server, $port, 15);
 
			if ($conn_id == false) return 2;
	
			// login with username and password	
			$login_result = ftp_login($conn_id, $user_name, $user_pass);
			if (!$login_result) return 1;
	
			ftp_pasv($conn_id, true);
			ftp_set_option($conn_id, FTP_TIMEOUT_SEC, 180);
		
			// try to download $server_file and save to $local_file
			if (!ftp_get($conn_id, $local_file, $file_source, FTP_BINARY)) return 3;
	
			ftp_close($conn_id);
		} else {
			$ftp_server = str_replace('ftp://', '', $ftp_server);						
			if (substr($ftp_server, strlen($ftp_server)-1, 1) != '/') $ftp_server = $ftp_server . '/';
				
			$str = "ftp://" .  $user_name . ":" . $user_pass . "@" . $ftp_server . $file_source;		
			$l = file_get_contents($str);
		
			$res = @file_put_contents($local_file, $l);
			if (!$res) return 3;
		}
		if (oups($local_file)) return 3;
				
		tryConvert($form_id, $ext);
		
		return 0;
	}

/*************************** findCsrf ************************************/
	function findCsrf($response, $text) {	
		$p = stripos($response, "type='hidden'", 0);
		if (!$p) $p = stripos($response, 'type="hidden"', 0);
		if ($p) {
			$p = stripos($response, '<input', $p-80);
			if ($p) {		
				$p = stripos($response, $text, $p);
				if (!$p) return '';
				
				$f = 1;
				$pb = stripos($response, "'", $p);
				if (abs($pb-$p) > 10) { $pb = stripos($response, '"', $p); $f = 2;}
				if (abs($pb-$p) < 10) {			
					if ($f == 1) $pe = stripos($response, "'", $pb+1);
					else $pe = stripos($response, '"', $pb+1);
					if ($pe) {
						$n = substr($response, $pb+1, $pe-$pb-1);
						return $n;
					}
				}
			}				
		}
		return '';
	}
	
/*************************** email ******************************************/
	function get_email($e_mail, $form_id, $ext, $sender, $user_pass, $pr_name, $go) {		
		if (empty($user_pass)) return 7;
		if (empty($sender)) return 8;
	
		$ext = '.' . $ext;		
		$load_file = DIR_APPLICATION . 'uploads/' . $form_id . $ext;
		if (file_exists($load_file) and $go) return 0;		
	
		if (file_exists($load_file)) unlink($load_file);
		
		$search = $pr_name;
		if (substr_count($pr_name, '[date(') and substr_count($pr_name, ']')) {
			$posb = strpos($pr_name, '[date(');
			$b = strpos($pr_name, ')', $posb);
			if ($b and $b>$pos) {
				$a = substr($pr_name, $posb+6, $b-$posb-6);	
				$a = str_replace("'", "", $a);
				$a = str_replace('"', '', $a);
				if (!empty($a)) {
					$s = date($a);
					$pose = strpos($pr_name, ')]', $posb) + 1;
					if (substr_count($pr_name, ')-')) {
						$b = strpos($pr_name, ')-');
						$pose = strpos($pr_name, ']', $b);					
						$b = substr($pr_name, $b+2, $pose-$b-2);		
						if ($b and is_numeric($b)) {
							$plus_minus_days = mktime(0, 0, 0, date('m'), date('d')-$b, date('Y'));
							$s = date($a, $plus_minus_days);
						}
					}
					if (substr_count($pr_name, ')+')) {
						$b = strpos($pr_name, ')+');
						$pose = strpos($pr_name, ']', $b);					
						$b = substr($pr_name, $b+2, $pose-$b-2);
						if ($b and is_numeric($b)) {
							$plus_minus_days = mktime(0, 0, 0, date('m'), date('d')+$b, date('Y'));
							$s = date($a, $plus_minus_days);
						}
					}
					$a = substr($pr_name, 0, $posb);
					$b = substr($pr_name, $pose+1);
					$search = $a.$s.$b;
				}
			}
		}
	
		$hostname = '{imap.ukr.net:993/imap/ssl}INBOX';  // you may change socket ukr.net:993
	
		$inbox = imap_open($hostname, $e_mail, $user_pass) or die('Cannot connect to UkrNet: ' . imap_last_error());

		$emails = imap_search($inbox, 'FROM "' . $sender . '"');
	
		if($emails) {
			$count = 1;   
			rsort($emails);   
			foreach($emails as $email_number) {       
				$overview = imap_fetch_overview($inbox,$email_number,0);
				$message = imap_fetchbody($inbox,$email_number,2);       
				$structure = imap_fetchstructure($inbox, $email_number);

				$attachments = array();
        
				if(isset($structure->parts) && count($structure->parts)) {
					for($i = 0; $i < count($structure->parts); $i++) {
						$attachments[$i] = array(
							'is_attachment' => false,
							'filename' => '',
							'name' => '',
							'attachment' => ''
						);

						if($structure->parts[$i]->ifdparameters) {
							foreach($structure->parts[$i]->dparameters as $object) {
								if(strtolower($object->attribute) == 'filename') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['filename'] = $object->value;
								}
							}
						}

						if($structure->parts[$i]->ifparameters) {
							foreach($structure->parts[$i]->parameters as $object) {
								if(strtolower($object->attribute) == 'name') {
									$attachments[$i]['is_attachment'] = true;
									$attachments[$i]['name'] = $object->value;
								}
							}
						}

						if($attachments[$i]['is_attachment']) {
							$attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

							/* 3 = BASE64 encoding */
							if($structure->parts[$i]->encoding == 3) {
								$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
							}
							/* 4 = QUOTED-PRINTABLE encoding */
							elseif($structure->parts[$i]->encoding == 4) {
								$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
							}
						}
					}
				}
    
				foreach($attachments as $attachment) {
					if($attachment['is_attachment'] == 1) {			
						if (empty($search)) {						
							$fp = fopen($load_file, "w+");
							fwrite($fp, $attachment['attachment']);	
						} else {
							if (substr_count($attachment['name'], $search)) {
								$fp = fopen($load_file, "w+");
								fwrite($fp, $attachment['attachment']);		
							}
						}
						fclose($fp);
					}
				}
			}
		}
		imap_close($inbox);	
		return 0;
	}		
	
/***************************  LINK  *****************************************/
	function get_link($url, $form_id, $ext, $user_name, $user_pass, $go) {		
		if (empty($ext)) return 4;
	
		$ext = '.' . $ext;		
		$local_file = DIR_APPLICATION . 'uploads/' . $form_id . $ext;
		if (file_exists($local_file) and $go) return 0;
		
		$url = str_replace('&amp;', '&', $url);
	
		if (file_exists($local_file)) unlink($local_file);
		
		if (!empty($user_name) and !empty($user_pass))
		$cc = '/usr/bin/wget -c -t 1 --user=' . $user_name . ' --password=' . $user_pass . ' -O uploads/' . $form_id . $ext . ' "' . $url . '"';
		else $cc = '/usr/bin/wget -c -t 1 -O uploads/' . $form_id . $ext . ' "' . $url . '"';
		
		$cc = str_replace('&amp;', '&', $cc);
		
		exec($cc);
	
		if (oups($local_file)) {
			
			$agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36";

			$headers = array (
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange,application/json;q=0.9,*;q=0.8;v=b3',
				'cache-control: max-age=0',
				'upgrade-insecure-requests: 1',
				'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.97 Safari/537.36',
				'sec-fetch-user: ?1',				
				'x-compress: null',
				'sec-fetch-site: none',
				'sec-fetch-mode: navigate',
				'Content-Encoding: deflate',
				'Accept-Encoding: deflate',
				
/*************  API  ****************************/
					
		//		'x-request-app: Techno+ Marketplace',
		//		'Authorization: Bearer AIzaSyDswaiwXVyrLNORapOdjpDVFv-GDmQ4vlE',		
		
/************************************************/		
				'Accept-Language: ru-RU,ru,ua,en-US;q=0.8,en;q=0.7'				
			);

								
			$cookie= "";
			
			if (file_exists($local_file)) unlink($local_file);		
			$fp = @fopen($local_file,'w+');
			if (!$fp) return 5;

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_ENCODING, '');
			curl_setopt($ch, CURLOPT_USERAGENT, $agent);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
			curl_setopt($ch, CURLOPT_TIMEOUT, 120);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	//		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	//		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);			
	//		curl_setopt($ch, CURLOPT_COOKIE, "sid=Ad34F12AC347D;pid=afeffad2c4f7a0;pass='frt4'");
			if (!empty($user_name) and !empty($user_pass))
			curl_setopt($ch, CURLOPT_USERPWD, $user_name.":".$user_pass);
			
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
	
			if (oups($local_file) and !empty($user_name) and !empty($user_pass)) {
				if (file_exists($local_file)) unlink($local_file);					
				$fp = @fopen($local_file,'w+');			
				if (!$fp) return 5;
				
				$ch = curl_init($url);				
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_ENCODING, '');
				curl_setopt($ch, CURLOPT_USERAGENT, $agent);			
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);		
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		//		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);		
				
				$response = curl_exec($ch);
	
				if (curl_errno($ch)) return 6;			
				
				$logpass = "&username=" . $user_name . "&password=" . $user_pass;		
				
				$v = '';
				$n = findCsrf($response, "name");
				if (!empty($n)) $v = findCsrf($response, "value");				
				if ($n and $v) $logpass = $n .'='. $v . $logpass;
	
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_ENCODING, '');
				curl_setopt($ch, CURLOPT_USERAGENT, $agent);			
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_POST, 1);			
				curl_setopt($ch, CURLOPT_POSTFIELDS, $logpass);
		//		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		//		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);				
				curl_setopt($ch, CURLOPT_COOKIE, $_SESSION["mycookies"]);
							
				curl_exec($ch);
				curl_close($ch);
				fclose($fp);
			}
			
	/*		if (oups($local_file)) {    //  **********  API  *******************
				if (file_exists($local_file)) unlink($local_file);					
				$fp = @fopen($local_file,'w+');			
				if (!$fp) return 5;
			
				$infotype = 0;
				$data = array("email" => $user_name, "pass" => $user_pass, "infotype" => $infotype);
				$data_string = json_encode($data);
			
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_FILE, $fp);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_TIMEOUT, 120);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'Cache-Control: no-cache', 
					'Content-Length: ' . strlen($data_string))
				);
		//		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
		//		curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);				

				curl_exec($ch);
				curl_close($ch);
				fclose($fp);
			} */
		}

		if (oups($local_file)) return 6;
		
		tryConvert($form_id, $ext);
		return 0;
	}
	
/**************************  END LINK ***************************************/	

	function getNameForm($db, $id) {		
		$query = $db->query("SELECT * FROM " . DB_PREFIX . "suppler WHERE `form_id` = '" . $id . "'");
		
		return $query->rows[0]['name'];
	}	
	
/**************************  UTF-16  *************************************/

	function utf16_decode($str, &$be=null) {
    if (strlen($str) < 2) {
        return $str;
    }
    $c0 = ord($str[0]);
    $c1 = ord($str[1]);
    $start = 0;
    if ($c0 == 0xFE && $c1 == 0xFF) {
        $be = true;
        $start = 2;
    } else if ($c0 == 0xFF && $c1 == 0xFE) {
        $start = 2;
        $be = false;
    }
    if ($be === null) {
        $be = true;
    }
    $len = strlen($str);
    $newstr = '';	
    for ($i = $start; $i < $len; $i += 2) {
        if ($be) {
			if (ord($str[$i]) != 0x04) {
				$a = ord($str[$i+1]);
				if ($a < 128) {
					$val = ord($str[$i]) << 4;
					$val += ord($str[$i+1]);
					$letter = chr($val);
				} else {
					if ($a < 192) $letter = chr(194).$str[$i];
				}
			} else {
				$a = ord($str[$i+1])+1024;
				if ($a >= 1040 and $a <= 1087) $a = $a + 52352;
				if ($a > 1040 and $a <= 1103) $a = $a + 52544;
				if ($a == 1025) $a = 53249;
				if ($a == 1105) $a = 53649;				
				$b = intval($a/256);
				$c = $a - $b*256;
				$letter = chr($b).chr($c);
			}
        } else {
			if (ord($str[$i+1]) != 0x04) {
				$a = ord($str[$i]);
				if ($a < 128) {
					$val = ord($str[$i+1]) << 4;
					$val += ord($str[$i]);
					$letter = chr($val);
				} else {
					if ($a < 192) $letter = chr(194).$str[$i];
				}
			} else {
				$a = ord($str[$i])+1024;
				if ($a >= 1040 and $a <= 1087) $a = $a + 52352;
				if ($a > 1040 and $a <= 1103) $a = $a + 52544;
				if ($a == 1025) $a = 53249;
				if ($a == 1105) $a = 53649;
				$b = intval($a/256);
				$c = $a - $b*256;
				$letter = chr($b).chr($c);
			}	
        }
        $newstr .= ($val == 0x228) ? "\n" : $letter;
    }
    return $newstr;
}
	
/**************************************************************************/

	$query = $db->query("SELECT * FROM " . DB_PREFIX . "suppler_cron ORDER BY `csort`, `nom_id`");
	$all = $query->rows;	
	
	if (empty($query->rows)) exit;

	$time_out = 0;
	$nom_id = 0;
	foreach ($query->rows as $row) {
		if ($row['on_off'] and $row['go']) {
			$time_out = 1;
			$nom_id = $row['nom_id'];
			break;
		}
	}
	
	$form_abort = '';
	$f = 278785;
	
	foreach($query->rows as $row) {
		
		if ($nom_id and $row['nom_id'] != $nom_id) continue;
	
		$param = array();	
		$mk = time();
		if (substr_count($row['port1'], '+')) {
			$a = substr($row['port1'], 1);
			$mk = time() + (int)$a*3600;
		}
		if (substr_count($row['port1'], '-')) {
			$a = substr($row['port1'], 1);
			$mk = time() - (int)$a*3600;
		}
		
		$d = gmmktime(0, 0, 0, gmdate('m'), gmdate('d'), gmdate('Y'));
		$email_d = (int)gmdate('d', $d);
			
		if (!$row['on_off'])  continue;
					
		$period = 240; // pause 240 minutes  = 4 hours

		if (($mk-$row['cron_status'])/60 < $period) continue;	
			
		$h = date('G', $mk);
		$d = date('N', $mk);
		$w = date('j', $mk);
		$w = ceil($w/7);
		
		$t = explode(',', $row['text']);
		$t1 = explode(',', $row['text1']);
		$t2 = explode(',', $row['text2']);
		
		if (!$time_out) {
			if ($row['text'] != '*') {
				$ee = 0;
				for ($u=0; $u<count($t); $u++) {
					if ($h == $t[$u]) { $ee = 1; break; }
				}
				if (!$ee) continue;
			}	
			if ($row['text1'] != '*') {
				$ee = 0;
				for ($u=0; $u<count($t1); $u++) {
					if ($d == $t1[$u]) { $ee = 1; break; }
				}
				if (!$ee) continue;
			}	
			if ($row['text2'] != '*') {
				$ee = 0;
				for ($u=0; $u<count($t2); $u++) {
					if ($w == $t2[$u]) { $ee = 1; break; }
				}
				if (!$ee) continue;
			}
		} else {			
			$sos = false;
			$flag1 = 0;
			$count_old = 0; 
			$count_new = 0;
			$file_sos = "./uploads/sos.tmp";
			if (file_exists($file_sos) and filesize($file_sos)) $sos = @fopen($file_sos,'r+');
			if ($sos) { 
				fseek($sos, 0);
				$mmm = fgets($sos, 100);
				$m = explode(" " , $mmm);
				$count_old = (int)$m[0];
				fclose($sos);
			}

			sleep(4);
		
			if (file_exists($file_sos) and filesize($file_sos)) $sos = @fopen($file_sos,'r+');
			if ($sos) { 
				fseek($sos, 0);
				$mmm = fgets($sos, 100);
				$m = explode(" " , $mmm);
				$count_new = (int)$m[0];
				fclose($sos);
			}
		
			if ($count_old != $count_new) exit;			
		}
		
		$file_inc = DIR_APPLICATION . 'uploads/php/' . $row['form_id'] . '.php';    
		if (file_exists($file_inc) and $row['task'] == 4 and !$time_out) require_once $file_inc;			
		
		$param['form_id'] = $row['form_id'];
		$param['command'] = $row['cmd'];
		$param['ext'] = 'xls';
		if ($row['ext'] == 2) $param['ext'] = 'xml';
		if ($row['ext'] == 3) $param['ext'] = 'csv';
		
		$param['act_find'] = $row['act_find'];	
		$param['act_change'] = $row['act_change'];
		$param['all'] = $row['all0'];
		$param['isno'] = $row['isno'];
		$param['pr_name'] = $row['pr_name'];
		$param['link'] = $row['link'];		
	
		if ($f != $row['form_id']) $form_name = getNameForm($db, $param['form_id']);
			
		if ($form_abort != '' and $row['form_id'] == $form_abort) {
			$file_er    = "./uploads/log_cron.tmp";
			$log = @fopen($file_er,'a');
			if (!$log) $log = @fopen($file_er,'w+');	
			@fputs($log, date('Y-m-d H:i') . " Form= " . $row['form_id'] . " The task= " . $row['task'] . " is missed because the price-list did not download  \n");			
			@fclose($log);		
			continue;
		}	
		
		if ($f != $row['form_id']) {
			$f = $row['form_id'];
			foreach($all as $r) {				
				if ($r['form_id'] == $f and $r['task'] == 4) {
					$pr_name = $r['pr_name'];
					$flag = 0;
					$warn = 0;
					if (!empty($r['link'])) {						
						$par['ext'] = 'xls';
						if ($r['ext'] == 2) $par['ext'] = 'xml';
						if ($r['ext'] == 3) $par['ext'] = 'csv';
						if (!empty($r['ftp_name'])) {
							$r['ftp_name'] = str_replace('&amp;', '&', $r['ftp_name']);
							$r['ftp_name'] = str_replace('&gt;', '>', $r['ftp_name']);
							$r['ftp_name'] = str_replace('&lt;', '<', $r['ftp_name']);
							$r['ftp_name'] = str_replace('&nbsp;', ' ', $r['ftp_name']);
							$r['ftp_name'] = str_replace('&quot;', '"', $r['ftp_name']);
						}
						if (!empty($r['ftp_pass'])) {
							$r['ftp_pass'] = str_replace('&amp;', '&', $r['ftp_pass']);
							$r['ftp_pass'] = str_replace('&gt;', '>', $r['ftp_pass']);
							$r['ftp_pass'] = str_replace('&lt;', '<', $r['ftp_pass']);
							$r['ftp_pass'] = str_replace('&nbsp;', ' ', $r['ftp_pass']);
							$r['ftp_pass'] = str_replace('&quot;', '"', $r['ftp_pass']);
						}
						
						if (filter_var($r['link'], FILTER_VALIDATE_EMAIL)) {
							if ($err = get_email($r['link'], $r['form_id'], $par['ext'], $r['ftp_name'], $r['ftp_pass'], $r['pr_name'], $r['go'])) {
								$flag = 1;
								mess($err, $r['pr_name']);
							}			
							break;
						}						
							
						if ((!empty($r['link']) and !substr_count($r['link'], 'http') and !substr_count($r['link'], 'www') and !empty($r['ftp_name']) and !empty($r['ftp_pass'])) or (substr_count($r['link'], "ftp") and !empty($r['ftp_name']) and !empty($r['ftp_pass']))) {
							$message ='';
							$flag = 1;
							if ($err = get_ftp($r['form_id'], $r['pr_name'], $r['link'], $r['ftp_name'], $r['ftp_pass'], $par['ext'], $r['go'])) {
								$flag = 2;
								mess($err, $r['pr_name']);
							}			
							break;
						}
					
						if (!empty($r['link']) and $flag != 1) {		
							$message ='';
							$flag = 1;
					/*		if (substr_count($r['link'], '[date]')) {
								$dd = date('Y-m-d', $mk);
								$r['link'] = str_replace('[date]', $dd, $r['link']);
							}	*/
													
							if ($err = get_link($r['link'], $r['form_id'], $par['ext'], $r['ftp_name'], $r['ftp_pass'], $r['go'])) {
								$flag = 2;
								mess($err, $r['link']);
							}				
							break;
						}						
					} else {
						$flag = 1;
						if (!empty($r['pr_name'])) {
							$path = DIR_APPLICATION . 'uploads/' . $r['pr_name'];
							if (!file_exists($path)) $flag = 2;
						} else $warn = 1;
						break;
					}					
				}	
			}
		}

		if ($flag == 2) {			
			$form_abort = $f;
			$file_er    = "./uploads/log_cron.tmp";
			$log = @fopen($file_er,'a');
			if (!$log) $log = @fopen($file_er,'w+');	
			@fputs($log, date('Y-m-d H:i') . " Form= " . $form_name . " The task= " . $row['task'] . " is missed because the price-list did not download  \n");			
			@fclose($log);
			
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `cron_status` = '" . $mk . "', `go` = '" . 0 . "'  WHERE `nom_id` = '" . $row['nom_id'] . "'");
			
			if ($row['task'] == 4)
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `errors` = '" . 0 . "', `report` = '" . 0 . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
		
			continue;		
		}		
				
		$task = "catalog/suppler/cronAction";
		if ($row['task'] == 4) $task = "catalog/suppler/cronLoadfile";
		
		if (!empty($param['command']) or ($row['task'] == 4 and $flag == 1)) {
			
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler SET `jopt` = '" . $email_d . "'");
						
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `go` = '" . 1 . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
			
			if ($row['task'] == 4)
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `errors` = '" . 0 . "', `report` = '" . 0 . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
		
			if ($param['command'] == 247) {
				$path = DIR_APPLICATION . 'uploads/' . $pr_name;
				if (file_exists($path)) unlink($path);
			}
		
			$cron->run($registry, $task, $param);
			
			$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `cron_status` = '" . $mk . "', `go` = '" . 0 . "', `save_form` = '" . $mk . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");
			
			$file_er    = "./uploads/log_cron.tmp";
			$log = @fopen($file_er,'a');
			if (!$log) $log = @fopen($file_er,'w+');
			if ($row['task'] == 4) {
				if (!$warn) {
					if (empty($param['pr_name'])) $n = $param['form_id'] . '.' . $param['ext'];
					else $n = $param['pr_name'];
					@fputs($log, date('Y-m-d H:i') . " DONE! Form= " . $form_name . " price-list= " . $n . " \n");
					
					$errors = 0;
					$report = 0;
					$path_err = DIR_APPLICATION . 'uploads/errors.tmp';
					$path_rep = DIR_APPLICATION . 'uploads/report.tmp';
					if (file_exists($path_err)) $errors = sizeof(file($path_err));
					if (file_exists($path_rep)) $report = sizeof(file($path_rep));

					$query = $db->query("UPDATE " . DB_PREFIX . "suppler_cron SET `errors` = '" . $errors . "', `report` = '" . $report . "' WHERE `nom_id` = '" . $row['nom_id'] . "'");	
		
		
					$err_rep_log  = "./uploads/log";
					if (!is_dir($err_rep_log)) @mkdir($err_rep_log, 0755);
					if (is_dir($err_rep_log)) {
						@copy ($path_err, "./uploads/log/errors" . $param['form_id'] . ".tmp");
						@copy ($path_rep, "./uploads/log/report" . $param['form_id'] . ".tmp");
					}
				}	
			} else {
				@fputs($log, date('Y-m-d H:i') . " DONE! Form= " . $form_name . " task= " . $row['task'] . " command= " . $param['command'] . " \n");
			}		
			@fclose($log);
			
			$total_add = 0;
			$total_up = 0;
			$path  = "./uploads/total.tmp"; 
			if (file_exists ($path)) {			
				$total = @fopen($path,'r+');
				$mmm = fgets($total, 100);
				$m = explode(" " , $mmm);	
				if (isset($m[0])) $total_add = (int)$m[0];			
				if (isset($m[1])) $total_up = (int)$m[1];
			}
			if (file_exists($path)) unlink ($path);
		
			$path = "./uploads/sos.tmp";
			if (file_exists($path)) unlink ($path);
				
			$path = "./uploads/schema.tmp";
			if (file_exists($path)) unlink ($path);
			
			$path = "./uploads/decode" . $param['ext'];
			if (file_exists($path)) unlink ($path);
		}
		
		if (!empty($row['mail']) and $row['task'] == 4) {

			$text = 'Price-list not found';
			if (!empty($_SESSION["e-r-r"]) and $flag != 2) {
				$errs = explode(";", $_SESSION["e-r-r"]);
				$err = $errs[0];
				if (!isset($errs[1])) $errs[1] = 0;
				if (!isset($errs[2])) $errs[2] = 0;
				
				$text = 'Host: ' . HTTP_CATALOG . ' TOTAL: Products added ' . $errs[1] . ' Products updated ' . $errs[2];
				if ($row['rtype'] == 5) {
					if ($total_add) $text = 'Host: ' . HTTP_CATALOG . ' TOTAL: Products added ' . $total_add;
					else $text = '';
				}	
			}
			
			$sos = 0;
			$path_err = DIR_APPLICATION . 'uploads/errors.tmp';
			$path_rep = DIR_APPLICATION . 'uploads/report.tmp';
			if (!file_exists($path_rep) and !file_exists($path_err)) $sos = 1;
			if (!file_exists($path_rep) and file_exists($path_err)) $sos = 2;
			if (file_exists($path_err) and file_exists($path_rep)) {	
				$lines_err = sizeof(file($path_err));
				$lines_rep = sizeof(file($path_rep));
				if ($lines_err !=0 and $lines_rep/$lines_err > 5) $sos = 3;
				if ($lines_err !=0 and $lines_rep/$lines_err > 10) $sos = 4;
				if ($lines_err !=0 and $lines_rep/$lines_err > 20) $sos = 5;
				if ($lines_err !=0 and $lines_rep/$lines_err > 50) $sos = 6;
			}
			if (file_exists($path_rep) and !file_exists($path_err)) $sos = 7;
			
			if ($flag == 2) $sos = 1;

			$form = ' Form ' . $form_name;
			
			$sub[0] = 'Hello! Message from Cron. ';
			$sub[1] = 'Good morning! ';
			$sub[2] = 'Hi! ';
			$sub[3] = 'Have a nice day! ';
			$sub[4] = 'Please, read me. ';
			$sub[5] = 'Good news. ';
			if ($sos < 3) $sub[5] = 'The news is not very good. ';
			$sub[6] = 'News. ';
			$sub[7] = 'This is not spam. ';
			$sub[8] = "Hello! I'm not tired of you? ";
			$sub[9] = 'Report. ';
			$sub[10] = 'Would you like to see the report? ';
			$sub[11] = 'Good news. There is a report. ';
			if ($sos < 3) $sub[11] = 'The news is not very good. There is a report. ';
			$sub[12] = 'Report? Here it is. ';
			$i = rand(0, 12);
			$greeting = $sub[$i];
			
			if ($sos == 1) {
				$b[0] = 'Job not done. See System -> Error Log';
				$b[1] = 'Nothing succeeded. See System -> Error Log';
				$b[2] = 'See System -> Error Log';
				$b[3] = 'All very bad. See System -> Error Log';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 2) {
				$b[0] = 'Job not done. Check supplier form settings.';
				$b[1] = 'Nothing succeeded. Check supplier form settings.';
				$b[2] = 'Only errors. Check supplier form settings.';
				$b[3] = 'All very bad. Check supplier form settings.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 3) {
				$b[0] = 'A lot of errors.';
				$b[1] = 'More than 20 percent errors.';
				$b[2] = 'Something went wrong, a lot of errors.';
				$b[3] = 'Too many errors. Check supplier form settings.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 4) {
				$b[0] = 'There are errors.';
				$b[1] = 'More than 10 percent errors.';
				$b[2] = 'Look at the error file.';
				$b[3] = 'Little reason for joy. There are errors.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 5) {
				$b[0] = 'There are some errors.';
				$b[1] = 'More than 5 percent errors.';
				$b[2] = 'Not quite good, best to see the error file.';
				$b[3] = 'Not bad.';
			}
			$i = rand(0, 3);
			$body = $b[$i];
			
			if ($sos == 6) {
				$b[0] = 'Very few errors, but there are.';
				$b[1] = 'Less than 5 percent error.';
				$b[2] = 'Quite perfect.';
			}
			$i = rand(0, 2);
			$body = $b[$i];
			
			if ($sos == 7) {
				$b[0] = 'Great job. No errors';
				$b[1] = 'Perfect. No errors';
				$b[2] = 'No errors.';
			}
			$i = rand(0, 2);
			$body = $b[$i];
					
			$subject = $greeting . $body . $form;
		
			$mail = new Mail();
    
			$mail->setTo($row['mail']);
			$mail->setFrom('your.server');
			$mail->setSender('Plugin "Suppliers"');
			$mail->setSubject($subject);
			$mail->setText($text);
			if ($row['rtype'] == 2) $mail->addAttachment(DIR_APPLICATION . 'uploads/errors.tmp');
			if ($row['rtype'] == 3) $mail->addAttachment(DIR_APPLICATION . 'uploads/report.tmp');
			if ($row['rtype'] == 4) {
				$mail->addAttachment(DIR_APPLICATION . 'uploads/errors.tmp');
				$mail->addAttachment(DIR_APPLICATION . 'uploads/report.tmp');
			}
			
			if (!empty($text)) $mail->send();
		}
		
		$file_inc1 = DIR_APPLICATION . 'uploads/php/' . $row['form_id'] . '_bottom.php'; 
		if ($row['task'] == 10 and file_exists($file_inc1)) require_once $file_inc1;
	}
	
	exit;
?>