<?php

require_once(DIR_SYSTEM . "/engine/neoseo_controller.php");

class ControllerToolNeoSeoExchange1c extends NeoSeoController
{

	private $error = array();

	public function __construct($registry)
	{
		parent::__construct($registry);
		$this->_moduleSysName = "neoseo_exchange1c";
		$this->_module_code = "neoseo_exchange1c";
		$this->_logFile = $this->_moduleSysName() . ".log";
		$this->debug = $this->config->get($this->_moduleSysName() . "_debug") == 1;
	}

	public function delTree($dir)
	{
		// Скрытые файлы не будут найдены по обычной маске, поэтому вначале ищем и удаляем их
		$files = glob($dir . '.*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				continue;
			}
			$this->debug("Удаляем файл - $file");
			unlink($file);
		}
		// А теперь обычные
		$files = glob($dir . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				$this->delTree($file);
			} else if (!is_link($file)) {
				$this->debug("Удаляем файл - $file");
				unlink($file);
			}
		}
		if (file_exists($dir)) {
			rmdir($dir);
		}
	}

	public function recurse_copy($src, $dst)
	{
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ( $file = readdir($dir))) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if (is_dir($src . '/' . $file)) {
					$this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	public function delete_products_warehouses()
	{
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_warehouse`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_option_warehouse`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_related_option_warehouse`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_products()
	{
		$this->load->model("catalog/product");
		$query = $this->db->query('SELECT product_id FROM `' . DB_PREFIX . 'product`');
		foreach ($query->rows as $row) {
			$this->model_catalog_product->deleteProduct($row['product_id']);
		}
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_to_1c`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_option_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_1c_products()
	{
		$this->load->model("catalog/product");
		$query = $this->db->query('SELECT product_id FROM `' . DB_PREFIX . 'product_to_1c`');
		foreach ($query->rows as $row) {
			$this->model_catalog_product->deleteProduct($row['product_id']);
		}
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_to_1c`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_option_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_links()
	{
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_to_1c`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'product_option_to_1c`');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'category_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_categories()
	{
		$this->load->model("catalog/category");
		$query = $this->db->query('SELECT category_id FROM `' . DB_PREFIX . 'category`');
		foreach ($query->rows as $row) {
			$this->model_catalog_category->deleteCategory($row['category_id']);
		}
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'category_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_manufacturers()
	{
		$this->load->model("catalog/manufacturer");
		$query = $this->db->query('SELECT manufacturer_id FROM `' . DB_PREFIX . 'manufacturer`');
		foreach ($query->rows as $row) {
			$this->model_catalog_manufacturer->deleteManufacturer($row['manufacturer_id']);
		}
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_attributes()
	{
		$this->load->model("catalog/attribute");
		$query = $this->db->query('SELECT attribute_id FROM `' . DB_PREFIX . 'attribute`');
		foreach ($query->rows as $row) {
			$this->model_catalog_attribute->deleteAttribute($row['attribute_id']);
		}

		$this->load->model("catalog/attribute_group");
		$query = $this->db->query('SELECT attribute_group_id FROM `' . DB_PREFIX . 'attribute_group`');
		foreach ($query->rows as $row) {
			$this->model_catalog_attribute_group->deleteAttributeGroup($row['attribute_group_id']);
		}
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function delete_options()
	{
		$this->load->model("catalog/option");
		$query = $this->db->query('SELECT option_id FROM `' . DB_PREFIX . 'option`');
		foreach ($query->rows as $row) {
			$this->model_catalog_option->deleteOption($row['option_id']);
		}

		if ($this->config->get("neoseo_exchange1c_use_related_options")) {
			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_discount'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_discount");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_option'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_option");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_special'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_special");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_variant'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_variant");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_variant_option'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_variant_option");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_variant_product'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_variant_product");
			}


			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "relatedoptions_to_char'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "relatedoptions_to_char");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_description'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_description");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_detail'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_detail");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_image'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_image");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_image_value'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_image_value");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_kit'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_kit");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_price'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_price");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_to_product'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_to_product");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_pro_value'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_pro_value");
			}

			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "product_option_to_1c'");
			if ($query->num_rows > 0) {
				$this->db->query("TRUNCATE " . DB_PREFIX . "product_option_to_1c");
			}

		}

		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function get_orders()
	{
		$query = $this->db->query('SELECT order_id FROM `' . DB_PREFIX . 'order`');
		$result = array();
		foreach ($query->rows as $row) {
			$result[] = $row['order_id'];
		}
		echo json_encode($result);
	}

	public function import()
	{
		$this->load->language('extension/module/neoseo_exchange1c');
		$this->load->model("tool/neoseo_exchange1c");

		if (empty($this->request->files['filename']['name'])) {
			$this->log("Ошибка! Не указан файл для загрузки");
			return $this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}

		$filename = $this->request->files['filename']['name'];
		$tmpfilename = $this->request->files['filename']['tmp_name'];
		if (!$tmpfilename) {
			$this->log($this->language->get('error_post_size'));
			$this->session->data['warning'] = $this->language->get('error_post_size');
			$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			return;
		}

		$cache = DIR_CACHE . 'exchange1c/';
		if (!is_dir($cache))
			mkdir($cache);

		$zip = new ZipArchive;
		$res = $zip->open($tmpfilename);
		$this->debug("результат открытия архива $tmpfilename: " . $res);
		if ($res === true) {
			$this->debug("Обрабатываем архив: $filename");

			// $this->clearCatalog();
			// Извлекаем файлы
			$zip->extractTo($cache);
			$files = scandir($cache);

			// Обрабатываем файлы
			$found = 0;
			foreach ($files as $file) {
				if (is_file($cache . $file)) {
					$found++;
					$this->debug("Обрабатываем файл $file из архива $filename");
					$this->modeImport($file);
				}
			}

			if (!$found) {
				$this->log($this->language->get('error_empty_archive'));
				$this->session->data['warning'] = $this->language->get('error_empty_archive');
				$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
				return;
			}

			if (is_dir($cache . 'import_files')) {
				if (substr(VERSION, 0, 1) == "1")
					$images = DIR_IMAGE . 'data/import_files/';
				else
					$images = DIR_IMAGE . 'catalog/import_files/';

				if (is_dir($images) && $this->config->get("neoseo_exchange1c_use_tree_delete")) {
					$this->debug("Используем древовидное копирование и удаление $images");
					$this->recurse_copy($cache . 'import_files/', $images);
					$this->delTree($cache . 'import_files/');
				} else {
					rename($cache . 'import_files/', $images);
				}
			}
		} else {

			// Читаем первые 256 байт и определяем файл по сигнатуре, ибо мало ли, какое у него имя
			$buffer = file_get_contents($tmpfilename, 'r');
			if (strpos($buffer, 'ПакетПредложений') || strpos($buffer, 'ИзмененияПакетаПредложений')) {
				$this->debug("Обрабатываем файл товаров: $filename");

				move_uploaded_file($tmpfilename, $cache . 'offers.xml');
				$this->modeImport('offers.xml');
			} else if (strpos($buffer, 'Классификатор')) {
				$this->debug("Обрабатываем файл каталога: $filename");

				move_uploaded_file($tmpfilename, $cache . 'import.xml');
				$this->modeImport('import.xml');
			} else if (strpos($buffer, 'Документ')) {
				$this->debug("Обрабатываем файл заказов: $filename");

				move_uploaded_file($tmpfilename, $cache . 'orders.xml');
				$this->modeImport('orders.xml');
			} else if (strpos($buffer, 'Контрагенты') && strpos($buffer, 'Контрагент')) {
				$this->debug("Обрабатываем файл Контрагентов: $filename");

				move_uploaded_file($tmpfilename, $cache . 'contragents.xml');
				$this->modeImport('contragents.xml');
			} else {
				$this->log("Ошибка! Тип файла не определен: $filename / $buffer");
				$json['error'] = $this->language->get('text_upload_error');
				$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}
		}

		$this->session->data['success'] = $this->language->get('text_upload_success');


		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function export()
	{
		$this->load->model('tool/neoseo_exchange1c');
		$orders = $this->model_tool_neoseo_exchange1c->queryOrders();

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=orders.xml');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($orders));

		if ($this->config->get("neoseo_exchange1c_order_utf8") == 1) {
			//header('Content-Type: text/html; charset=utf-8', true);
			if ($this->config->get("neoseo_exchange1c_order_utf8_bom") == 1) {
				echo chr(239) . chr(187) . chr(191) . $orders;
			} else {
				echo $orders;
			}
		} else {
			//header('Content-Type: text/html; charset=win-1251', true);
			echo @iconv('utf-8', 'cp1251//TRANSLIT', str_replace("encoding=\"utf-8\"", "encoding=\"cp-1251\"", $orders));
		}
	}

	public function export_product()
	{


		$this->load->model('catalog/option');
		$this->load->model('tool/neoseo_exchange1c');
		$options = $this->model_catalog_option->getOptions();

		$tmpArchiveName = tempnam("/tmp", "products_zip");
		$zip = new ZipArchive;
		if ($zip->open($tmpArchiveName) !== TRUE) {
			$this->log("Не удалось создать архив");
			exit;
		}

		foreach ($options as $option) {
			$filenameTmp = tempnam("/tmp", "products_" . $option['name']);
			$filenameArchive = "products_" . $option['name'] . ".xlsx";

			$this->model_tool_neoseo_exchange1c->exportProducts(
					$option['option_id'], $option['name'], $filenameTmp);

			$zip->addFile($filenameTmp, $filenameArchive);
		}

		$filenameTmp = tempnam("/tmp", "products");
		$filenameArchive = "products.xlsx";

		$this->model_tool_neoseo_exchange1c->exportProducts(
				0, "", $filenameTmp);

		$zip->addFile($filenameTmp, $filenameArchive);
		$zip->close();

		header('Content-Type: application/zip');
		header('Content-Disposition: attachment;filename=products.zip');
		header('Cache-Control: max-age=0');
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Content-length: " . filesize($tmpArchiveName));
		readfile($tmpArchiveName);

		exit;
	}

	public function explodeLines($lines)
	{
		$res = array();
		foreach (explode("\n", trim($lines)) as $line) {
			if (trim($line) != "")
				$res[] = trim($line);
		}
		return $res;
	}

	public function modeCheckauth()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);

		// Проверяем включен или нет модуль
		if (!$this->config->get('neoseo_exchange1c_status')) {
			$this->debug("Обращение на выключенный обмен");
			echo "failure\n";
			echo "1c module OFF";
			exit;
		}

		// Разрешен ли IP
		if ($this->config->get('neoseo_exchange1c_ip_list') != '') {
			$ip = $_SERVER['REMOTE_ADDR'];
			$allow_ips = $this->explodeLines($this->config->get('neoseo_exchange1c_ip_list'));

			if (!in_array($ip, $allow_ips)) {
				$this->debug("Обращение с неразрешенного IP адреса: $ip. Список разрешенных IP адресов: " . implode(",", $allow_ips));
				echo "failure\n";
				echo "IP is not allowed";
				exit;
			}
		}

		// Авторизуем
		$login = '';
		$password = '';
		if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
			$login = $_SERVER['PHP_AUTH_USER'];
			$password = $_SERVER['PHP_AUTH_PW'];
		} else if (isset($_SERVER['REMOTE_USER'])) {
			$tmp = explode(" ", $_SERVER['REMOTE_USER']);
			$tmp = explode(":", base64_decode($tmp[1]));
			$login = $tmp[0];
			$password = $tmp[1];
		} else if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
			$tmp = explode(" ", $_SERVER['REDIRECT_REMOTE_USER']);
			$tmp = explode(":", base64_decode($tmp[1]));
			$login = $tmp[0];
			$password = $tmp[1];
		}

		if (!$login) {
			$this->debug("Не указан логин");
			echo "failure\n";
			echo "login required\n";
			echo "Possible solution:\n";
			echo "Add this line to your .htaccess file after RewriteEngine ON\n";
			echo "RewriteRule .*neoseo_exchange1c.php$ - [E=REMOTE_USER:%{HTTP:Authorization},L]\n";
			//var_dump($_SERVER);
			exit;
		}

		if (!$password) {
			$this->debug("Не указан пароль");
			echo "failure\n";
			echo "password required\n";
			exit;
		}

		if ($login != $this->config->get('neoseo_exchange1c_username')) {
			$this->debug("Некорректный логин: '" . $login . "', ожидалось: '" . $this->config->get('neoseo_exchange1c_username') . "'");
			echo "failure\n";
			echo "error login\n";
			exit;
		}


		if ($password != $this->config->get('neoseo_exchange1c_password')) {
			$this->debug("Некорректный пароль: '" . $password . "', ожидалось: '" . $this->config->get('neoseo_exchange1c_password') . "'");
			echo "failure\n";
			echo "error password\n";
			exit;
		}

		$this->log("Авторизация выполнена успешно: " . md5($this->config->get('neoseo_exchange1c_password')));
		echo "success\n";
		echo "key\n";
		echo md5($this->config->get('neoseo_exchange1c_password')) . "\n";
	}

	public function clearCatalog()
	{
		// Очищаем каталоги
		$cache = DIR_CACHE . 'exchange1c/';
		$this->delTree($cache);
		mkdir($cache);
	}

	public function modeCatalogInit()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);

		// Очищаем каталоги
		$this->clearCatalog();

		$limit = 100 * 1024 * 1024;
		if ($this->config->get("neoseo_exchange1c_enable_zip")) {
			$this->log("Требуем каталог в формате zip");
			echo "zip=yes\n";
		} else {
			$this->log("Требуем каталог в виде отдельных файлов");
			echo "zip=no\n";
		}

		echo "file_limit=" . $limit . "\n";
		$this->log("Инициализация каталога завершена");
		//$this->debug("Ограничение на размер файла: $limit байт");
	}

	public function modeSaleInit()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);
		$limit = 100 * 1024 * 1024;

		echo "zip=no\n";
		echo "file_limit=" . $limit . "\n";
		$this->debug("Инициализация продаж завершена");
		//$this->debug("Ограничение на размер файла: $limit байт");
	}

	private function checkUploadFileTree($path, $curDir = null)
	{
		//$this->debug("Подготавливаем каталог для загрузки файлов");

		if (!$curDir)
			$curDir = DIR_CACHE . 'exchange1c/';

		foreach (explode('/', $path) as $name) {

			if (!$name)
				continue;

			if (file_exists($curDir . $name)) {
				if (is_dir($curDir . $name)) {
					$curDir = $curDir . $name . '/';
					continue;
				}

				unlink($curDir . $name);
			}

			if (!mkdir($curDir . $name)) {
				$this->log('Не удалось создать каталог: ' . $curDir . $name);
			}
			$curDir = $curDir . $name . '/';
		}
	}

	public function modeFile()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode'] . "&filename=" . $this->request->get['filename']);

		$type = $this->request->get['type'];


		if (!isset($this->request->cookie['key'])) {
			$this->log("type=" . $type . "&mode=file: не указан сессионный ключ");
			return;
		}

		if ($this->request->cookie['key'] != md5($this->config->get('neoseo_exchange1c_password'))) {
			$this->log("type=" . $type . "&mode=file Неверное значение куки с паролем");
			echo "failure\n";
			echo "Session error";
			return;
		}

		$wait_import_command = $this->config->get("neoseo_exchange1c_wait_import_command");
		$cache = DIR_CACHE . 'exchange1c/';
		if (!is_dir($cache))
			mkdir($cache);

		// Проверяем на наличие имени файла
		if (isset($this->request->get['filename'])) {
			$upload_file = $cache . $this->request->get['filename'];
		} else {
			$this->log("type=" . $type . "&mode=file: не указан файл импорта");
			echo "failure\n";
			echo "ERROR 10: No file name variable";
			return;
		}

		// Проверяем XML или изображения
		$isImage = false;
		if (strpos($this->request->get['filename'], 'import_files') !== false || $type == "pdf") {
			$isImage = true;
			if (substr(VERSION, 0, 1) == "1")
				$cache = DIR_IMAGE . 'data/';
			else
				$cache = DIR_IMAGE . 'catalog/';

			if (strpos($this->request->get['filename'], '.pdf') !== false || $type == "pdf") {
				$this->log("Загрузка файла pdf выполнена успешно");
				$cache = DIR_DOWNLOAD;

				if ($type == "pdf") {
					$upload_file = $cache . $this->request->get['filename'];
					$this->checkUploadFileTree(dirname($this->request->get['filename']), $cache);
				}else{
					$upload_file = $cache . $this->request->get['filename'];
					$upload_file = $cache . basename($upload_file);
					$this->checkUploadFileTree("", $cache);
				}
			} else {
				$this->log("Загрузка файла image выполнена успешно");
				$upload_file = $cache . $this->request->get['filename'];
				$this->checkUploadFileTree(dirname($this->request->get['filename']), $cache);
			}
		}

		// Получаем данные
		$data = file_get_contents("php://input");
		if ($data === false) {
			$this->log("type=catalog&mode=file: нет данных к файлу");
			echo "failure\n";
			echo "No data file\n";
			return;
		}

		// Некоторые криворукие передают тут данные по заказам, но отказываются вызывать дополнительный обработчик
		$isOrders = false;
		if (false !== strpos($data, "<Документ")) {
			$isOrders = true;
			$upload_file = $cache . "orders.xml";
		}

		$fp = fopen($upload_file, "wb");
		if (!$fp) {
			$this->log("type=catalog&mode=file: не получается открыть файл");
			echo "failure\n";
			echo "Can not open file: $upload_file\n";
			return;
		}

		$result = fwrite($fp, $data);
		fclose($fp);

		if ($result === strlen($data)) {
			$this->log("Загрузка файла $upload_file выполнена успешно");
			echo "success\n";

			chmod($upload_file, 0777);
			//echo "success\n";
		} else if ($result > strlen($data)) {
			// http://php.net/manual/ru/mbstring.overload.php
			$this->log("type=catalog&mode=file: Внимание! Обнаружена проблема с функцией strlen, которая вернула количество символов вместо количества байт. Обратитесь к хостеру для замены php на более стабильную версию! Стабильная работа модуля не гарантируется!");
			echo "success\n";

			chmod($upload_file, 0777);
		} else {
			$this->log("type=catalog&mode=file: проблемы с размером файла. Пытались записать " . strlen($data) . ", а записалось " . (int) $result);
			echo "failure\n";
		}

		if ($isImage) {
			// Тут архива быть не может, идем сразу на выход
			return;
		} else if ($isOrders && !$wait_import_command) {
			// Тут архива быть не может, импортируем заказы и сразу на выход
			$this->modeImport("orders.xml");
			return;
		}

		$this->load->model('tool/neoseo_exchange1c');

		// Обрабатываем архив
		$zip = new ZipArchive;
		$res = $zip->open($upload_file);
		$this->debug("результат открытия архива - $res");
		if ($res !== true) {
			//Это был обычный файл
			if ((strpos($upload_file, 'orders') !== false || strpos($upload_file, 'documents') !== false) && !$wait_import_command) {
				$this->model_tool_neoseo_exchange1c->parseOrders($this->request->get['filename']);
			}
			return;
		}

		$this->debug("Обрабатываем архив: $upload_file");

		// Извлекаем файлы
		$zip->extractTo($cache);
		$files = scandir($cache);

		if (is_dir($cache . 'import_files')) {
			$this->log("В архиве найдены изображения. Перемещаем в соответствующие каталоги");

			if (substr(VERSION, 0, 1) == "1")
				$images = DIR_IMAGE . 'data/import_files/';
			else
				$images = DIR_IMAGE . 'catalog/import_files/';
			if (strpos($this->request->get['filename'], '.pdf') !== false) {
				$cache = DIR_DOWNLOAD;
			}
			if (is_dir($images) && $this->config->get("neoseo_exchange1c_use_tree_delete")) {
				$this->debug("Используем древовидное копирование и удаление $images");
				$this->recurse_copy($cache . 'import_files/', $images);
				$this->delTree($cache . 'import_files/');
			} else {
				$this->copyDirectory($cache . 'import_files/', $images);
			}
		}

		// Обрабатываем файлы
		$found = 0;
		foreach ($files as $file) {
			if (is_file($cache . $file)) {
				$found++;
				$this->debug("Обрабатываем файл $file из архива $upload_file");
				if (strpos($file, 'import') !== false) {

					$this->model_tool_neoseo_exchange1c->parseImport($file);
				} else if (strpos($file, 'offers') !== false) {

					$this->model_tool_neoseo_exchange1c->parseOffers($file);
				} else if (strpos($file, 'orders') !== false || strpos($file, 'documents') !== false) {

					$this->model_tool_neoseo_exchange1c->parseOrders($file);
				} else if (strpos($file, 'contragents') !== false) {

					$this->model_tool_neoseo_exchange1c->parseContragents($file);
				}
			}
		}

		if (!$found) {
			$this->log($this->language->get('error_empty_archive'));
			$this->session->data['warning'] = $this->language->get('error_empty_archive');
			$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			return;
		}
	}

	public function copyDirectory( $source, $destination ) {
		if ( is_dir( $source ) ) {
			@mkdir( $destination );
			$directory = dir( $source );
			while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if ( $readdirectory == '.' || $readdirectory == '..' ) {
					continue;
				}
				$PathDir = $source . '/' . $readdirectory;
				if ( is_dir( $PathDir ) ) {
					$this->copyDirectory( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}
				copy( $PathDir, $destination . '/' . $readdirectory );
			}
			$directory->close();
		}else {
			copy( $source, $destination );
		}
	}

	public function modeImport($manual = false)
	{
		$this->load->model("tool/neoseo_exchange1c");
		if($this->config->get($this->_moduleSysName . "_exchange_control")){
			if($this->model_tool_neoseo_exchange1c->getExchangeStatus()){
				$this->log("Обмен уже идет, отменяем текущий");
				echo "failure\n";
				echo "ERROR : The exchange is already in progress";
				return 0;
			}else{
				$this->log("Ставим статус - начат обмен");
				$this->model_tool_neoseo_exchange1c->addExchangeStatus();
			}
		}
		if ($manual) {
			$filename = $manual;
		} else if (isset($this->request->get['filename'])) {
			$filename = $this->request->get['filename'];
			$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode'] . "&filename=" . $filename);
		} else {
			echo "failure\n";
			echo "ERROR 10: No file name variable";
			$this->clearExchangeStatus();
			return 0;
		}

		$cache = DIR_CACHE . 'exchange1c/';
		if (!is_dir($cache))
			mkdir($cache);
		$fullFileName = $cache . $filename;
		if (!file_exists($fullFileName)) {
			$this->log("Файл не найден: $fullFileName");
			echo "failure\n";
			echo "ERROR 10: file not found";
			$this->clearExchangeStatus();
			return 0;
		}

		$this->load->model('tool/neoseo_exchange1c');


		if (strpos($filename, 'import') !== false) {

			if ($this->config->get($this->_moduleSysName() . "_user_answer_for_moy_sklad") == 1) {
				$marker_filename = DIR_CACHE . 'exchange1c/' . 'import_marker.json';
				if (file_exists($marker_filename) && ( time() - filemtime($marker_filename)) < 300) {
					// Если не прошло еще 300 секунд... т.е. импорт еще идет
					$this->log("Обновляем статус из: $marker_filename");
					$status = json_decode(file_get_contents($marker_filename), true);
					if ($status['status'] == 'processing' || $status['status'] == 'success') {
						$this->log("Возвращаем статус из import_marker : " . $status['status']);
						if (!$manual) {
							echo $status['status'] . "\n";
						}
					} else {
						// В файле неожидаемый ответ.
						$this->log("В файле import_marker неожидаемый ответ: " . $status['status']);
					}
				} else {
					$status['status'] = 'processing';
					file_put_contents($marker_filename, json_encode($status));
					$this->log("В файле import_marker помещен ответ: " . $status['status']);
					$this->model_tool_neoseo_exchange1c->parseImport($filename);
					$status['status'] = 'success';
					file_put_contents($marker_filename, json_encode($status));
					$this->log("В файле import_marker помещен ответ: " . $status['status']);
				}
			} else {
				$this->model_tool_neoseo_exchange1c->parseImport($filename);
				if (!$manual) {
					echo "success\n";
				}
			}
		} else if (strpos($filename, 'offers') !== false || strpos($filename, 'rests') !== false || strpos($filename, 'prices') !== false) {
			if ($this->config->get($this->_moduleSysName() . "_user_answer_for_moy_sklad") == 1) {
				$marker_filename = DIR_CACHE . 'exchange1c/' . 'offer_marker.json';
				if (file_exists($marker_filename) && ( time() - filemtime($marker_filename)) < 300) {
					// Если не прошло еще 300 секунд... т.е. импорт еще идет
					$this->log("Обновляем статус из: $marker_filename");
					$status = json_decode(file_get_contents($marker_filename), true);
					if ($status['status'] == 'processing' || $status['status'] == 'success') {
						$this->log("Возвращаем статус из offer_marker : " . $status['status']);
						if (!$manual) {
							echo $status['status'] . "\n";
						}
					} else {
						// В файле неожидаемый ответ.
						$this->log("В файле offer_marker неожидаемый ответ: " . $status['status']);
					}
				} else {
					$status['status'] = 'processing';
					file_put_contents($marker_filename, json_encode($status));
					$this->log("В файле offer_marker помещен ответ: " . $status['status']);
					$this->model_tool_neoseo_exchange1c->parseOffers($filename);
					$status['status'] = 'success';
					file_put_contents($marker_filename, json_encode($status));
					$this->log("В файле offer_marker помещен ответ: " . $status['status']);
				}
			} else {

				$this->model_tool_neoseo_exchange1c->parseOffers($filename);

				if (!$manual) {
					echo "success\n";
				}
			}
		} else if (strpos($filename, 'orders') !== false || strpos($filename, 'documents') !== false) {

			$this->model_tool_neoseo_exchange1c->parseOrders($filename);

			if (!$manual) {
				echo "success\n";
			}
		} else if (strpos($filename, 'contragents') !== false) {

			$this->model_tool_neoseo_exchange1c->parseContragents($filename);

			if (!$manual) {
				echo "success\n";
			}
		} else {
			echo "failure\n";
			echo $filename;
		}

		if ($this->config->get($this->_moduleSysName() . "_update_auto_neoseo_filter_warehouse") == 1) {
			$this->log('Попытка обновление фильтр "Склады"');
			$this->model_tool_neoseo_exchange1c->updateNeoSeoFilterWarehouse();
		}

		if ($this->config->get($this->_moduleSysName() . "_product_update_avaro") == 1) {
			$this->log('Попытка обновление связей "HYPER PRODUCT MODELS (hpmodel_links)"');
			$this->model_tool_neoseo_exchange1c->updateModuleHyperProductModels();
		}

		$this->cache->delete('product');
		$this->clearExchangeStatus();
		return;
	}

	public function importFile()
	{
		$this->load->language('module/neoseo_exchange1c');
		$this->load->model("tool/neoseo_exchange1c");

		$cache = DIR_UPLOAD . 'exchange1c/';
		$files = scandir($cache);

		// Обрабатываем файлы
		$found = 0;
		foreach ($files as $file) {
			if (is_file($cache . $file)) {
				$found++;
				$this->debug("Обрабатываем файл $file ");
				// Обрабатываем архив
				$zip = new ZipArchive;
				$res = $zip->open($cache . $file);
				$this->debug("результат открытия архива - $res");
				if ($res !== true) {
					//Это был обычный файл
					if ((strpos($file, 'orders') !== false || strpos($file, 'documents') !== false)) {
						$this->modeFileImport($cache . $file);
					}
				}else{
					$zip->extractTo($cache . "/unziped/");
					$unziped_files = scandir($cache . "/unziped/");
					foreach ($unziped_files as $unziped_file) {
						if (is_file($cache . "/unziped/" . $unziped_file)) {
							$found++;
							$this->debug("Обрабатываем файл после распаковки $file  $unziped_file");
							$this->modeFileImport($cache . "/unziped/" . $unziped_file);
						}
					}
				}

			}
		}
		$this->log("Обработан файл $file ");
	}

	public function modeFileImport($filename){
		$manual = false;
		if (strpos($filename, 'import') !== false) {
			$this->model_tool_neoseo_exchange1c->parseImport($filename, $cron_job = true);
			if (!$manual) {
				echo "success\n";
			}
		} else if (strpos($filename, 'offers') !== false || strpos($filename, 'rests') !== false || strpos($filename, 'prices') !== false) {
			$this->model_tool_neoseo_exchange1c->parseOffers($filename, $cron_job = true);
			if (!$manual) {
				echo "success\n";
			}
		} else if (strpos($filename, 'orders') !== false || strpos($filename, 'documents') !== false) {
			$this->model_tool_neoseo_exchange1c->parseOrders($filename, $cron_job = true);
			if (!$manual) {
				echo "success\n";
			}
		} else if (strpos($filename, 'contragents') !== false) {
			$this->model_tool_neoseo_exchange1c->parseContragents($filename);
			if (!$manual) {
				echo "success\n";
			}
		} else {
			echo "failure\n";
			echo $filename;
		}
	}

	public function modeExport()
	{
		// Отдаем все товары, у которых нет связи с 1с
		$this->load->model('tool/neoseo_exchange1c');
		$products = $this->model_tool_neoseo_exchange1c->queryProducts();

		//if( $this->config->get("neoseo_exchange1c_order_utf8") == 1 ) {
		header('Content-Type: text/xml; charset=utf-8', true);
		echo $products;
		//} else {
		//	header('Content-Type: text/html; charset=win-1251', true);
		//	echo @iconv('utf-8', 'cp1251//TRANSLIT',  str_replace("encoding=\"utf-8\"","encoding=\"cp-1251\"",$products));
		//}
	}

	public function modeExportAll()
	{
		// Отдаем все товары
		$this->load->model('tool/neoseo_exchange1c');
		$products = $this->model_tool_neoseo_exchange1c->queryAllProducts();

		header('Content-Type: text/xml; charset=utf-8', true);
		echo $products;
	}

	public function modeQueryOrders()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);

		$this->load->model('tool/neoseo_exchange1c');
		$orders = $this->model_tool_neoseo_exchange1c->queryOrders();

		if ($this->config->get("neoseo_exchange1c_order_utf8") == 1) {
			header('Content-Type: text/html; charset=utf-8', true);
			if ($this->config->get("neoseo_exchange1c_order_utf8_bom") == 1) {
				echo chr(239) . chr(187) . chr(191) . $orders;
			} else {
				echo $orders;
			}
		} else {
			header('Content-Type: text/html; charset=win-1251', true);
			echo @iconv('utf-8', 'cp1251//TRANSLIT', str_replace("encoding=\"utf-8\"", "encoding=\"cp-1251\"", $orders));
		}
	}

	public function modeSuccessOrders()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);

		$this->load->model('tool/neoseo_exchange1c');
		$this->load->model('sale/order');
		$orders = $this->model_tool_neoseo_exchange1c->getAllOrderStatusControl();

		if(count($orders)){
			$notify = $this->config->get('neoseo_exchange1c_order_status_notify');
			$comment = trim($this->config->get($this->_moduleSysName . '_order_comment'));
			$new_status = $this->config->get('neoseo_exchange1c_final_order_status');
			$this->debug("Подверждаем количество заказов " . count($orders));

			foreach ($orders as $order){
				$inner_status_ids = unserialize($order['inner_status_ids']);
				$orders_data = unserialize($order['order_data']);
				// если есть список статусов тогда ищем новое соответствие статусу
				if (count($inner_status_ids)) {
					$new_status = $orders_data['order_status_id'];
					if (isset($inner_status_ids[$orders_data['order_status_id']])) {
						$new_status = $inner_status_ids[$orders_data['order_status_id']];
					}
				}

				$this->model_tool_neoseo_exchange1c->addOrderTo1C($orders_data['order_id']);

				if (substr(VERSION, 0, 1) == "2" || substr(VERSION, 0, 1) == "3") {
					$this->model_tool_neoseo_exchange1c->changeOrderHistory($new_status, $orders_data['order_id'], $notify, $comment);
				} else {
					$this->model_sale_order->addOrderHistory($orders_data['order_id'], array(
						'order_status_id' => $new_status,
						'comment' => $comment,
						'notify' => $notify
					));
				}
				$this->model_tool_neoseo_exchange1c->deleteOrderStatusControl($orders_data['order_id']);
			}
		}

		echo "success\n";
	}

	public function changeOrderExport1C()
	{
		$json = array();
		if (!isset($this->request->post['order_id']) && !isset($this->request->post['status'])) {
			$json['success'] = false;
			$this->response->setOutput(json_encode($json));
			return false;
		}
		$order_id = $this->request->post['order_id'];
		$this->load->model('tool/' . $this->_moduleSysName());
		if ($this->request->post['status'] == '0') {
			$this->model_tool_neoseo_exchange1c->addOrderTo1C($order_id);
		} else {
			$this->model_tool_neoseo_exchange1c->deleteOrderTo1C($order_id);
		}
		$json['success'] = true;
		$this->response->setOutput(json_encode($json));
	}

	public function deleteExportListOrders()
	{
		$this->load->language('extension/module/neoseo_exchange1c');
		$this->load->model('tool/' . $this->_moduleSysName());
		$orders = $this->model_tool_neoseo_exchange1c->getAllOrderTo1C();
		if ($orders) {
			foreach ($orders as $order) {
				$this->model_tool_neoseo_exchange1c->deleteOrderTo1C($order['order_id']);
			}
			$response = true;
		} else {
			$response = false;
		}

		if ($response) {
			die(json_encode(array("message" => $this->language->get('text_cleaned'))));
		} else {
			die(json_encode(array("message" => $this->language->get('text_err_cleaned'))));
		}
	}

	public function counterpartiesDelete()
	{
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'customer` WHERE customer_id in (SELECT customer_id FROM `' . DB_PREFIX . 'customer_to_1c`)');
		$this->db->query('DELETE FROM `' . DB_PREFIX . 'address` WHERE customer_id in (SELECT customer_id FROM `' . DB_PREFIX . 'customer_to_1c`)');
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'customer_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function counterpartiesDeleteLinks()
	{
		$this->db->query('TRUNCATE `' . DB_PREFIX . 'customer_to_1c`');
		$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}

	public function modeQueryContragents()
	{
		$this->debug("type=" . $this->request->get['type'] . "&mode=" . $this->request->get['mode']);
		$this->load->model('tool/neoseo_exchange1c');
		$c_data = $this->model_tool_neoseo_exchange1c->queryContragents();

		header('Content-Type: text/xml; charset=utf-8', true);
		echo $c_data;
	}

	public function exportContragents()
	{
		$this->load->model('tool/neoseo_exchange1c');
		$c_data = $this->model_tool_neoseo_exchange1c->queryContragents();

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=contragents.xml');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . strlen($c_data));

		header('Content-Type: text/xml; charset=utf-8', true);
		echo $c_data;
	}

	public function clearExchangeStatus (){
		if ($this->config->get($this->_moduleSysName() . "_exchange_control")) {
			$this->load->model("tool/neoseo_exchange1c");
			$this->model_tool_neoseo_exchange1c->clearExchangeStatus();
			$this->response->redirect($this->url->link('extension/module/neoseo_exchange1c', 'user_token=' . $this->session->data['user_token'], 'SSL'));
		}
	}

}

?>