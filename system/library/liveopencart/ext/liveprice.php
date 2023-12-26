<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru
namespace liveopencart\ext;

class liveprice extends \liveopencart\lib\v0023\extension {

	protected $extension_code                  	= 'lp3';
	protected $version                         	= '3.2.1b';
	protected $theme_details                   	= '';
	protected $theme_settings                  	= false;
	protected $resource_route_catalog          	= 'view/theme/extension_liveopencart/live_price/';
	protected $resource_subroute_catalog_theme 	= 'theme/';
	protected $theme_template_override_dir		= '_override';
	protected $sub_instances;
	protected $events_catalog  					= array(
		'view/*/*/before'                    	=> 'extension/liveopencart/liveprice/eventViewAnyTemplateBefore', // comp with product listing options
		'view/*/*/*/before'                     => 'extension/liveopencart/liveprice/eventViewAnyTemplateBefore', // comp with product listing options
		
	);
	protected $version_pro;

	// separate init, but not in __construct to have no trouble with endless loop on possible calls for initLibrary from models loaded in the library
	protected function init() {
		if (class_exists('\liveopencart\ext\liveprice_pro')) {
			$this->extension_code = \liveopencart\ext\liveprice_pro::getInstance($this->registry)->getExtensionCode();
		}

		if ($this->installed()) {
			$this->checkTables();

			if ($this->inCustomerSection()) {
				$events = $this->getThemeSetting('catalog_events');
				if (!empty($events)) {
					$this->events_catalog += $events;
				}
			}

			$this->activateExtensionEvents();

		}
		
		$this->version_pro = ($this->getExtensionCode() == 'lppro3');
	}

	public function installed() {
		return $this->getExtensionInstalledStatus('liveprice', 'liveprice_installed');
	}

	public function versionPRO() {
		return $this->version_pro;
		//return ($this->getExtensionCode() == 'lppro3');
	}
	
	
	public function getSQLWhereProductOptionValueByCustomerGroup() {
		$sql = "";
		if ( $this->installedProductOptionValueByCustomerGroup() ) {
			$sql = "
				(
					EXISTS (
						SELECT ROCG.product_option_value_id
						FROM ".DB_PREFIX."product_option_value_group ROCG
						WHERE ROCG.product_option_value_id = POV.product_option_value_id
						  AND ROCG.customer_group_id = ".(int)$this->config->get('config_customer_group_id')."
					)
					OR NOT EXISTS (
						SELECT ROCG.product_option_value_id
						FROM ".DB_PREFIX."product_option_value_group ROCG
						WHERE ROCG.product_option_value_id = POV.product_option_value_id
					)
				)
			";
		}
		return $sql;
	}
	
	public function installedProductOptionValueByCustomerGroup() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			$query = $this->db->query("SELECT * FROM `".DB_PREFIX."modification` WHERE `code` = 'optionvalue-customergroup' AND `status` = 1 ");
			$this->setCacheSimple( __FUNCTION__, $query->num_rows && $this->simple_db->existTable('product_option_value_group') );
		}
		return $this->getCacheSimple(__FUNCTION__);
	}

	public function uninstall() {
		if ($this->versionPRO()) {
			$this->liveopencart_ext_liveprice_pro->uninstall();
		}
	}

	public function getThemeName($custom_theme_name='') {
		
		if (!$this->theme_details || $custom_theme_name) {
			$params = array(
				'themes_shorten'	=> $this->getAdaptedThemes() ,
				'sibling_dir'		=> $sibling_file_name   = $this->getBasicDirOfExtension() . 'theme_sibling/',
			);
			if ($custom_theme_name) {
				$params['custom_theme_name'] = $custom_theme_name;
			}

			$this->theme_details = $this->getOuterLibraryInstanceByName('theme_details', $params);

		}
		return $this->theme_details->getThemeName();
	}

	public function getCalc() {
		return $this->getSubInstance('calculation');
	}
	
	public function getSubTMDCustomerGroupPrice() {
		return $this->getSubInstance('tmd_customer_group_price');
	}
	
	public function getSubPSO() {
		return $this->getSubInstance('pso');
	}

	public function getSubProductOptionQuantityTable() {
		return $this->getSubInstance('product_option_quantity_table');
	}
	public function getSubIO() {
		return $this->getSubInstance('io');
	}
	public function getSubRO() {
		return $this->getSubInstance('ro');
	}
	public function getSuboptprcbycg() {
		return $this->getSubInstance('optprcbycg');
	}

	protected function getSubInstance($sub_instance_name) {
		if (!isset($this->sub_instances[$sub_instance_name])) {
			$sub_instance_class_name = '\\liveopencart\\ext\\liveprice\\' . $sub_instance_name;
			$this->sub_instances[$sub_instance_name] = new $sub_instance_class_name($this->registry);
		}
		return $this->sub_instances[$sub_instance_name];
	}

	public function getBasicDirOfExtension() {
		return DIR_APPLICATION . $this->resource_route_catalog;
	}
	public function getBasicDirOfThemes() {
		return $this->getBasicDirOfExtension() . $this->resource_subroute_catalog_theme;
	}
	public function getResourceRouteCatalog($resource) {
		return $this->resource_route_catalog . $resource;
	}
	public function getResourceRouteCatalogCurrentTheme($resource) {
		return $this->resource_route_catalog . $this->getSubRouteOfCurrentTheme() . $resource;
	}
	public function getResourceRouteCatalogOverrideTheme($resource) {
		return $this->resource_route_catalog . $this->getSubRouteOfOverrideTheme() . $resource;
	}
	protected function getSubRouteOfCurrentTheme() {
		return substr($this->getDirOfCurrentTheme() , strlen($this->getBasicDirOfExtension()));
	}
	protected function getSubRouteOfOverrideTheme() {
		return substr($this->getDirOfOverrideTheme() , strlen($this->getBasicDirOfExtension()));
	}

	public function getDirOfCurrentTheme() {
		$theme_dir = $this->getBasicDirOfThemes() . $this->getThemeName() . '/';
		if (!file_exists($theme_dir) || !is_dir($theme_dir)) {
			$theme_dir = $this->getBasicDirOfThemes() . 'default/';
		}
		return $theme_dir;
	}
	
	public function getDirOfOverrideTheme() {
		$theme_dir = $this->getBasicDirOfThemes() . $this->theme_template_override_dir . '/';
		if (file_exists($theme_dir) && is_dir($theme_dir)) {
			return $theme_dir;
		}
	}

	public function getPriceTemplates() {

		$files     = array();
		$theme_dir = $this->getDirOfCurrentTheme();
		$files     = $this->getPriceTemplatesFromDirectory($theme_dir);
		
		$theme_dir_override = $this->getDirOfOverrideTheme();
		if ( $theme_dir_override ) {
			$files = array_merge($files, $this->getPriceTemplatesFromDirectory($theme_dir_override));
		}

		return $files;
	}

	protected function getPriceTemplatesFromDirectory($dir_theme) {
		$files = array();
		
		foreach (glob($dir_theme . '*.twig') as $file_name) {
			$files[basename($file_name, '.twig') ]       = $this->getTemplateFileRoute($file_name);
		}

		if (!$files) { // check for tpl
			foreach (glob($dir_theme . '*.tpl') as $file_name) {
				$files[basename($file_name, '.tpl') ]       = $this->getTemplateFileRoute($file_name, '.tpl');
			}
		}

		return $files;
	}

	protected function getTemplateFileRoute($file_name, $file_ext = '.twig') {
		$route    = dirname($file_name) . '/' . basename($file_name, $file_ext);
		if (substr($route, 0, strlen(DIR_TEMPLATE)) == DIR_TEMPLATE) {
			$route    = substr($route, strlen(DIR_TEMPLATE));
		}
		return $route;
	}

	public function getPathToMainJS() {
		return $this->getResourceLinkWithVersionCatalog($this->getResourceRouteCatalog('liveopencart.live_price.js'));
	}

	public function getPathToCustomJS() {
		return $this->getResourceLinkWithVersionIfExistsCatalog($this->getResourceRouteCatalogCurrentTheme('code.js'));
	}
	
	public function getPathToOverrideJS() {
		return $this->getResourceLinkWithVersionIfExistsCatalog($this->getResourceRouteCatalogOverrideTheme('code.js'));
	}
	
	public function getPathToMainCSS() { // normally does not exist
		return $this->getResourceLinkWithVersionIfExistsCatalog($this->getResourceRouteCatalog('liveopencart.live_price.css'));
	}

	public function getPathToCustomCSS() {
		return $this->getResourceLinkWithVersionIfExistsCatalog($this->getResourceRouteCatalogCurrentTheme('style.css'));
	}
	
	public function getPathToOverrideCSS() {
		return $this->getResourceLinkWithVersionIfExistsCatalog($this->getResourceRouteCatalogOverrideTheme('style.css'));
	}

	protected function getAdaptedThemes() {

		$dir_of_themes = $this->getBasicDirOfThemes();

		$themes        = glob($dir_of_themes . '*', GLOB_ONLYDIR);
		$themes        = array_map('basename', $themes);

		if (($default_key   = array_search('default', $themes)) !== false) {
			unset($themes[$default_key]);
		}

		usort($themes, function ($a, $b) {
			return strlen($b) - strlen($a);
		});

		return $themes;
	}

	public function getSettings() {
		if ( !$this->hasCacheSimple(__FUNCTION__) ) {
			$old_setting_key   = 'liveprice_settings';
			$new_setting_key   = 'module_liveprice_settings';
			$old_setting_value = $this->config->get($old_setting_key);
			$new_setting_value = $this->config->get($new_setting_key);
			if (!empty($old_setting_value) && empty($new_setting_value)) {
				$settings          = $this->config->get($old_setting_key);
			} else {
				$settings          = $this->config->get($new_setting_key);
			}
	
			$this->setCacheSimple(__FUNCTION__, $settings ? $settings : []);
		}
		return $this->getCacheSimple(__FUNCTION__);
	}

	public function getSetting($key, $default_value = false) {
		$settings = $this->getSettings();
		return isset($settings[$key]) ? $settings[$key] : $default_value;
	}

	public function getThemeSettings() {
		if ($this->theme_settings === false) {
			$file = $this->getDirOfCurrentTheme() . 'code.php';
			$this->theme_settings = file_exists($file) ? include ($file) : array();
		}
		return $this->theme_settings;
	}

	public function getThemeSetting($setting_key) {
		$settings = $this->getThemeSettings();
		if (isset($settings[$setting_key])) {
			return $settings[$setting_key];
		}
	}

	public function setOptionCalculateOnce($option_id, $calculate_once) {
		if ($this->installed()) {
			$this->db->query("UPDATE `" . DB_PREFIX . "option` SET calculate_once = '" . (int)$calculate_once . "' WHERE option_id = '" . (int)$option_id . "' ");
		}
	}

	public function addProductPageData($data) {

		$data = array_merge($data, $this->getProductPageAdditionalData(isset($data['product_id']) ? $data['product_id'] : false));

		return $data;
	}

	public function getProductPageAdditionalData($p_product_id_data = false) {

		$data = ($p_product_id_data && is_array($p_product_id_data)) ? $p_product_id_data : array();

		$data['liveprice_installed']                   = $this->installed();

		if ($data['liveprice_installed']) {

			$lp_product_id     = 0;
			if ($p_product_id_data !== false && !is_array($p_product_id_data)) {
				$lp_product_id     = $p_product_id_data;
			}
			elseif (isset($data['product_id'])) {
				$lp_product_id     = $data['product_id'];
			}
			elseif (isset($this->request->get['product_id'])) {
				$lp_product_id     = $this->request->get['product_id'];
			}
			elseif (isset($this->request->post['product_id'])) {
				$lp_product_id     = $this->request->post['product_id'];
			}
			elseif (isset($this->request->get['pid'])) {
				$lp_product_id     = $this->request->get['pid'];
			}
			elseif (isset($this->request->get['id'])) {
				$lp_product_id     = $this->request->get['id'];
			}
			elseif (isset($this->request->post['id'])) {
				$lp_product_id     = $this->request->post['id'];
			}
			elseif (isset($_GET['product_id'])) { // mijoshop
				$lp_product_id     = (int)$_GET['product_id'];
			}
			elseif (isset($_REQUEST['product_id'])) { // mijoshop
				$lp_product_id     = (int)$_REQUEST['product_id'];
			}
			elseif (isset($this->request->get['prod_id'])) {
				$lp_product_id     = $this->request->get['prod_id'];
			}

			$data['lp_product_id']			= $lp_product_id; // in some cases it is needed even without correct product_id
			$data['lp_theme_name']			= $this->getThemeName( isset($data['liveprice_override_theme_name']) ? $data['liveprice_override_theme_name'] : '' ); 
			$data['liveprice_settings']		= $this->getSettings();

			$data['lp_product_page_code']	= $this->render('extension_liveopencart/live_price/product_page', $data);
			
			if (!empty($data['liveprice_add_scripts_to_data'])) {
				$data['lp_scripts'] = $this->getProductPageScripts();
			}

		}
		return $data;
	}

	public function getProductPageScripts() {

		$scripts = array();

		if ($this->installed()) {
			$liveprice_custom_js = $this->getPathToOverrideJS();
			if ( !$liveprice_custom_js ) {
				$liveprice_custom_js = $this->getPathToCustomJS();
			}
			if ($liveprice_custom_js) {
				$scripts[] = $liveprice_custom_js;
			}
			
			$scripts[] = $this->getPathToMainJS();
		}

		return $scripts;
	}
	
	public function getProductPageStyles() {

		$styles = array();

		if ($this->installed()) {
			$liveprice_custom_css = $this->getPathToOverrideCSS();
			if ( !$liveprice_custom_css ) {
				$liveprice_custom_css = $this->getPathToCustomCSS();
			}	
			if ($liveprice_custom_css) {
				$styles[] = $liveprice_custom_css;
			}
			
			$main_css = $this->getPathToMainCSS();
			if ( $main_css ) {
				$styles[] = $main_css;
			}
		}

		return $styles;
	}

	public function checkTables() {

		// better compatibility
		$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "product_option_value` 'price_prefix' ");
		if ($query->num_rows && strtolower($query->row['Type']) == 'varchar(1)') {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` MODIFY `price_prefix` varchar(2) NOT NULL");
		}
		
		$this->simple_db->addTableColumnIfNotExists('option', 'calculate_once', "tinyint(1) NOT NULL DEFAULT 0 ");

		if ($this->versionPRO()) {
			$this->liveopencart_ext_liveprice_pro->checkTables();
		}
	}

	public function getChangedViewOfDiscounts($discounts, $product_info) {
		if ($this->installed()) {
			$data_discounts = array();
			$mod_settings   = $this->getSettings();
			if (!empty($mod_settings['percent_discount_to_total'])) {
				foreach ($discounts as $discount) {
					if (empty($discount['price_prefix']) || $discount['price_prefix'] == '=') {
						$data_discounts[]                = array(
							'quantity'                => $discount['quantity'],
							'price'                => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')) , $this->session->data['currency'])
						);
					}
					else {
						$data_discounts[]                = array(
							'quantity' => $discount['quantity'],
							'price' => '' . $discount['price_prefix'] . ' ' . (float)$discount['price']
						);
					}
				}
			}
			return $data_discounts;
		}
	}

	protected function getMultiplyingPricePrefixes() {
		return array(
			'*',
			'/',
			'%',
			'+%',
			'-%',
			'=%'
		);
	}

	public function changeOptionPriceFormat($price, $option_value, $product_id = false) {
		$result     = $price;
		if (in_array($option_value['price_prefix'], $this->getMultiplyingPricePrefixes()) && (!isset($option_value['hide']) || !$option_value['hide'])) {
			// special way
			$result     = (float)$option_value['price'];
		}

		return $result;
	}

	protected function getProductIdByProductOptionId($product_option_id) {
		$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product_option WHERE product_option_id = " . (int)$product_option_id . " ");
		if ($query->num_rows) {
			return $query->row['product_id'];
		}
	}

	public function getOptionValuePriceByProductCurrency($option_value, $product_id    = false) {

		$price         = $option_value['price'];

		if (!empty($option_value['price']) && !in_array($option_value['price_prefix'], $this->getMultiplyingPricePrefixes())) {
			if ($product_id == false) {
				$product_id    = $this->getProductIdByProductOptionId($option_value['product_option_id']);
			}
			$product = $this->getCalc()->getProductSimple($product_id);
			if ($product && !empty($product['currency_id'])) {
				$price         = $this->getCalc()->convertFromCurrency($option_value['price'], $product['currency_id']);
			}
		}
		return $price;
	}

	public function render($route, $data) {
		// in some cases the second parameter is theme name, so compatibility with d_twig_manager.xml should be implemented another way
		$ReflectionMethod = new \ReflectionMethod('Template', '__construct');
		$params = array();
		foreach ($ReflectionMethod->getParameters() as $param_reflection) {
			$params[] = $param_reflection->getName();
		}

		if (!empty($params[1]) && $params[1] == 'registry') { // $this->registry is added for compatibility with d_twig_manager.xml
			$template         = new \Template($this->registry->get('config')->get('template_engine') , $this->registry);
		} else { // std
			$template         = new \Template($this->registry->get('config')->get('template_engine'));
		}
		// $this->registry is added for compatibility with d_twig_manager.xml
		//$template = new Template($this->registry->get('config')->get('template_engine'), $this->registry);
		foreach ($data as $key => $value) {
			$template->set($key, $value);
		}

		$classMethod = new \ReflectionMethod($template, 'render');
		$params = array();
		foreach ($classMethod->getParameters() as $param_reflection) {
			$params[] = $param_reflection->getName();
		}
		if (count($params) > 2 && $params[1] == 'registry') { // for some mods ($route, $registry, $cache=false)
			$output = $template->render($route, $this->registry);
		} else { // std
			$output = $template->render($route);
		}
		//$output = $template->render( $route );
		return $output;
	}

}

