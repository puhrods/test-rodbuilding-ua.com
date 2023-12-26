<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru
class ControllerExtensionModuleLivePrice extends liveopencart\lib\v0023\ControllerAdminExtension {

	protected $extension_code = 'liveprice'; // for paths and urls
	public function __construct() {
		call_user_func_array(array(
			'parent',
			'__construct'
		) , func_get_args());

		\liveopencart\ext\liveprice::getInstance($this->registry);
		$this->load->model('extension/module/liveprice');
	}

	public function index() {

		$this->load->language('catalog/product');
		$this->loadLanguage();

		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$this->document->addScript($this->liveopencart_ext_liveprice->getResourceLinkWithVersion('view/javascript/liveopencart/live_price/liveprice_pro.js'));
		}

		$data['extension_code']      = $this->liveopencart_ext_liveprice->getExtensionCode();
		$data['module_version']      = $this->liveopencart_ext_liveprice->getCurrentVersion();

		$data['liveprice_texts']      = $this->language->all();
		$data['liveprice_texts_json']      = $this->jsonEncodeToUTF8($data['liveprice_texts']); // non UTF symbols can cause issues (which cannot be avoided on the twig side)
		$data['liveprice_lowest_price_update_url']      = htmlspecialchars_decode($this->getLinkWithToken($this->getRouteExtension('', 'updateLowestPrices')));
		$data['liveprice_get_product_ids_url']      = htmlspecialchars_decode($this->getLinkWithToken($this->getRouteExtension('', 'getProductIds')));

		$data = array_merge($data, $this->getLinks());

		$this->document->setTitle($this->language->get('module_name'));

		$this->load->model('setting/setting');

		if ($this->existSettingsToSave()) {
			// remove old setting
			$this->load->model('setting/setting');
			$this->model_setting_setting->deleteSetting('liveprice');

			$post_to_save = $this->request->post;
			if ($this->liveopencart_ext_liveprice->versionPRO()) {
				$post_to_save = $this->liveopencart_ext_liveprice_pro->saveDBSettings($post_to_save);
			}
			$this->saveSettingsStandard($data, $post_to_save);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		else {
			$data['error_warning'] = '';
		}

		$data['config_admin_language'] = $this->config->get('config_admin_language');
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		$this->load->model('customer/customer_group');
		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		$data['version_pro'] = $this->liveopencart_ext_liveprice->versionPRO();

		$data['settings'] = $this->liveopencart_ext_liveprice->getSettings();
		$data['settings'] = array_merge($data['settings'], $this->getPageExtraData($data['settings']));

		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$data['liveprice_pro_settings'] = $this->loadView($data, '', 'liveprice_pro_settings');
			$data['liveprice_pro_tab_content'] = $this->loadView($data, '', 'liveprice_pro_tab_content');
		}

		$this->setOutputStandard($data, '', 'liveprice');
	}

	protected function getPageExtraData($settings) {

		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$settings = array_merge($settings, $this->liveopencart_ext_liveprice_pro->readDBSettings());
		}

		$this->load->model('catalog/category');
		$categories       = $this->model_catalog_category->getCategories();
		$categories_names = array();
		foreach ($categories as $category) {
			$categories_names[$category['category_id']]                  = $category['name'];
		}

		$this->load->model('catalog/manufacturer');
		$manufacturers       = $this->model_catalog_manufacturer->getManufacturers();
		$manufacturers_names = array();
		foreach ($manufacturers as $manufacturer) {
			$manufacturers_names[$manufacturer['manufacturer_id']]                     = $manufacturer['name'];
		}

		if (isset($settings['discounts'])) {
			foreach ($settings['discounts'] as & $discount) {
				if (isset($discount['category_id']) && isset($categories_names[$discount['category_id']])) {
					$discount['category']                     = $categories_names[$discount['category_id']];
				}
				if (isset($discount['manufacturer_id']) && isset($manufacturers_names[$discount['manufacturer_id']])) {
					$discount['manufacturer']                     = $manufacturers_names[$discount['manufacturer_id']];
				}
			}
			unset($discount);
		}

		if (isset($settings['specials'])) {
			foreach ($settings['specials'] as & $special) {
				if (isset($special['category_id']) && isset($categories_names[$special['category_id']])) {
					$special['category'] = $categories_names[$special['category_id']];
				}
				if (isset($special['manufacturer_id']) && isset($manufacturers_names[$special['manufacturer_id']])) {
					$special['manufacturer'] = $manufacturers_names[$special['manufacturer_id']];
				}
			}
			unset($special);
		}

		$categories = $this->model_catalog_category->getCategories(array(
			'sort' => 'name'
		));

		$this->load->model('catalog/product');

		if (!empty($settings['discount_quantity_customize']) && !empty($settings['dqc'])) {
			foreach ($settings['dqc'] as & $dqc) {
				$new_dqc = array(
					'discount_quantity'         => $dqc['discount_quantity'],
					'categories'         => array() ,
					'manufacturers'         => array() ,
					'products'         => array()
				);
				if (!empty($dqc['categories'])) {
					foreach ($categories as $category) {
						if (in_array($category['category_id'], $dqc['categories'])) {
							$new_dqc['categories'][]         = $category;
						}
					}
				}
				if (!empty($dqc['manufacturers'])) {
					foreach ($manufacturers as $manufacturer) {
						if (in_array($manufacturer['manufacturer_id'], $dqc['manufacturers'])) {
							$new_dqc['manufacturers'][]         = $manufacturer;
						}
					}
				}
				if (!empty($dqc['products'])) {
					foreach ($dqc['products'] as $product_id) {
						$product = $this->model_catalog_product->getProduct($product_id);
						if ($product) {
							$new_dqc['products'][]         = $product;
						}
					}
				}
				$dqc     = $new_dqc;
			}
			unset($dqc);

		}

		return $settings;

	}

	public function getProductIds() {

		$json = array();

		$json['product_ids']      = $this->liveopencart_ext_liveprice_pro->getProductIds();

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function updateLowestPrices() {
		$json        = array();

		$product_ids = array();
		if (!empty($this->request->get['pids'])) {
			$product_ids = $this->request->get['pids'];
		}

		if (!empty($product_ids)) {

			$json['result']             = $this->liveopencart_ext_liveprice_pro->updateProductsMinimumPrices($product_ids);

			$json['success']             = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function install() {

		$settings = array(
			'status' => 1, // for standard opencart module status
			
		);
		$this->setSettings($settings);

		$this->liveopencart_ext_liveprice->checkTables();
	}

	public function uninstall() {
		$this->liveopencart_ext_liveprice->uninstall();
	}

	public function eventModelCatalogProductAddProductAfter(&$route, &$args, &$output) {
		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$this->liveopencart_ext_liveprice_pro->updateProductMinimumPrices($output);
		}
	}

	public function eventModelCatalogProductEditProductAfter(&$route, &$args, &$output) {
		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$this->liveopencart_ext_liveprice_pro->updateProductMinimumPrices($args[0]);
		}
	}

	public function eventModelCatalogProductDeleteProductAfter(&$route, &$args, &$output) {
		if ($this->liveopencart_ext_liveprice->versionPRO()) {
			$this->liveopencart_ext_liveprice_pro->updateProductMinimumPrices($args[0]);
		}
	}

}

