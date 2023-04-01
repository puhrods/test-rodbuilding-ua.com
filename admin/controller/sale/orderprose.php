<?php 
class ControllerSaleOrderprose extends Controller {
	private $error = array();
	
  	public function index() {
		$this->load->language('sale/orderprose');

		$this->document->setTitle($this->language->get('heading_title'));
		
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			exit('PHP v7.1 Required!');
		}
	
		$this->load->model('sale/orderpro');
	
		$return_page = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
	
		$url = '';
	
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
	
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
	
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
	
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
	
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'sale/orderprose')) {
				$this->session->data['success'] = '<span class="text-danger">' . $this->language->get('error_permission') . '</span>';
				$this->response->redirect($this->url->link($return_page, 'user_token=' . $this->session->data['user_token'], 'SSL'));
			} else {
				$this->load->model('setting/setting');
	
				$this->model_setting_setting->editSetting('orderpro', $this->request->post);
	
				$this->session->data['success'] = $this->language->get('success_save_setting');
	
				$this->response->redirect($this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			}
		}
	
		$this->getSettingForm();
  	}
	
	protected function getSettingForm() {
		$this->load->language('sale/orderprose');
	
		$data['heading_title'] = $this->language->get('heading_title');
		$data['orderpro_version'] = 'OrderPro v3.7.9';
	
		$data['text_heading'] = $this->language->get('text_heading');
		$data['text_name_placeholder'] = $this->language->get('text_name_placeholder');
		$data['text_sort_placeholder'] = $this->language->get('text_sort_placeholder');
	
		$data['entry_license'] = $this->language->get('entry_license');
		$data['entry_show_pid'] = $this->language->get('entry_show_pid');
		$data['entry_show_image'] = $this->language->get('entry_show_image');
		$data['entry_show_model'] = $this->language->get('entry_show_model');
		$data['entry_show_sku'] = $this->language->get('entry_show_sku');
		$data['entry_show_ean'] = $this->language->get('entry_show_ean');
		$data['entry_show_jan'] = $this->language->get('entry_show_jan');
		$data['entry_show_location'] = $this->language->get('entry_show_location');
		$data['entry_show_weight'] = $this->language->get('entry_show_weight');
		$data['entry_show_mpn'] = $this->language->get('entry_show_mpn');
		$data['entry_show_upc'] = $this->language->get('entry_show_upc');
		$data['entry_show_isbn'] = $this->language->get('entry_show_isbn');
		$data['entry_show_realqty'] = $this->language->get('entry_show_realqty');
		$data['entry_show_nowprice'] = $this->language->get('entry_show_nowprice');
		$data['entry_show_discount'] = $this->language->get('entry_show_discount');
		$data['entry_invoice_type'] = $this->language->get('entry_invoice_type');
		$data['entry_autocomplete_type'] = $this->language->get('entry_autocomplete_type');
		$data['entry_tabs_mode'] = $this->language->get('entry_tabs_mode');
		$data['entry_neworder_notify'] = $this->language->get('entry_neworder_notify');
		$data['entry_neworder_notify_admin'] = $this->language->get('entry_neworder_notify_admin');
		$data['entry_show_affiliate'] = $this->language->get('entry_show_affiliate');
		$data['entry_auto_addreward'] = $this->language->get('entry_auto_addreward');
		$data['entry_show_similar'] = $this->language->get('entry_show_similar');
		$data['entry_decimal_place'] = $this->language->get('entry_decimal_place');
		$data['entry_return_page'] = $this->language->get('entry_return_page');
		$data['entry_merge_orders'] = $this->language->get('entry_merge_orders');
		$data['entry_clone_orders'] = $this->language->get('entry_clone_orders');
	
		$data['entry_export_orders'] = $this->language->get('entry_export_orders');
		$data['entry_export_orders_one'] = $this->language->get('entry_export_orders_one');
		$data['entry_export_order_id'] = $this->language->get('entry_export_order_id');
		$data['entry_export_invoice_no'] = $this->language->get('entry_export_invoice_no');
		$data['entry_export_store_id'] = $this->language->get('entry_export_store_id');
		$data['entry_export_store_name'] = $this->language->get('entry_export_store_name');
		$data['entry_export_store_url'] = $this->language->get('entry_export_store_url');
		$data['entry_export_customer_id'] = $this->language->get('entry_export_customer_id');
		$data['entry_export_customer_group'] = $this->language->get('entry_export_customer_group');
		$data['entry_export_firstname'] = $this->language->get('entry_export_firstname');
		$data['entry_export_lastname'] = $this->language->get('entry_export_lastname');
		$data['entry_export_customer'] = $this->language->get('entry_export_customer');
		$data['entry_export_email'] = $this->language->get('entry_export_email');
		$data['entry_export_telephone'] = $this->language->get('entry_export_telephone');
		$data['entry_export_fax'] = $this->language->get('entry_export_fax');
		$data['entry_export_comment'] = $this->language->get('entry_export_comment');
		$data['entry_export_total'] = $this->language->get('entry_export_total');
		$data['entry_export_order_status'] = $this->language->get('entry_export_order_status');
		$data['entry_export_affiliate_id'] = $this->language->get('entry_export_affiliate_id');
		$data['entry_export_affiliate'] = $this->language->get('entry_export_affiliate');
		$data['entry_export_commission'] = $this->language->get('entry_export_commission');
		$data['entry_export_marketing_id'] = $this->language->get('entry_export_marketing_id');
		$data['entry_export_tracking'] = $this->language->get('entry_export_tracking');
		$data['entry_export_language'] = $this->language->get('entry_export_language');
		$data['entry_export_currency_code'] = $this->language->get('entry_export_currency_code');
		$data['entry_export_currency_value'] = $this->language->get('entry_export_currency_value');
		$data['entry_export_date_added'] = $this->language->get('entry_export_date_added');
		$data['entry_export_date_modified'] = $this->language->get('entry_export_date_modified');
		$data['entry_export_payment_firstname'] = $this->language->get('entry_export_payment_firstname');
		$data['entry_export_payment_lastname'] = $this->language->get('entry_export_payment_lastname');
		$data['entry_export_payment_customer'] = $this->language->get('entry_export_payment_customer');
		$data['entry_export_payment_company'] = $this->language->get('entry_export_payment_company');
		$data['entry_export_payment_country'] = $this->language->get('entry_export_payment_country');
		$data['entry_export_payment_zone'] = $this->language->get('entry_export_payment_zone');
		$data['entry_export_payment_city'] = $this->language->get('entry_export_payment_city');
		$data['entry_export_payment_address_1'] = $this->language->get('entry_export_payment_address_1');
		$data['entry_export_payment_address_2'] = $this->language->get('entry_export_payment_address_2');
		$data['entry_export_payment_postcode'] = $this->language->get('entry_export_payment_postcode');
		$data['entry_export_payment_method'] = $this->language->get('entry_export_payment_method');
		$data['entry_export_payment_code'] = $this->language->get('entry_export_payment_code');
		$data['entry_export_shipping_firstname'] = $this->language->get('entry_export_shipping_firstname');
		$data['entry_export_shipping_lastname'] = $this->language->get('entry_export_shipping_lastname');
		$data['entry_export_shipping_customer'] = $this->language->get('entry_export_shipping_customer');
		$data['entry_export_shipping_company'] = $this->language->get('entry_export_shipping_company');
		$data['entry_export_shipping_country'] = $this->language->get('entry_export_shipping_country');
		$data['entry_export_shipping_zone'] = $this->language->get('entry_export_shipping_zone');
		$data['entry_export_shipping_city'] = $this->language->get('entry_export_shipping_city');
		$data['entry_export_shipping_address_1'] = $this->language->get('entry_export_shipping_address_1');
		$data['entry_export_shipping_address_2'] = $this->language->get('entry_export_shipping_address_2');
		$data['entry_export_shipping_postcode'] = $this->language->get('entry_export_shipping_postcode');
		$data['entry_export_shipping_method'] = $this->language->get('entry_export_shipping_method');
		$data['entry_export_shipping_code'] = $this->language->get('entry_export_shipping_code');
		$data['entry_export_product_id'] = $this->language->get('entry_export_product_id');
		$data['entry_export_product_name'] = $this->language->get('entry_export_product_name');
		$data['entry_export_product_model'] = $this->language->get('entry_export_product_model');
		$data['entry_export_product_sku'] = $this->language->get('entry_export_product_sku');
		$data['entry_export_product_ean'] = $this->language->get('entry_export_product_ean');
		$data['entry_export_product_upc'] = $this->language->get('entry_export_product_upc');
		$data['entry_export_product_isbn'] = $this->language->get('entry_export_product_isbn');
		$data['entry_export_product_mpn'] = $this->language->get('entry_export_product_mpn');
		$data['entry_export_product_jan'] = $this->language->get('entry_export_product_jan');
		$data['entry_export_product_location'] = $this->language->get('entry_export_product_location');
		$data['entry_export_product_weight'] = $this->language->get('entry_export_product_weight');
		$data['entry_export_product_options'] = $this->language->get('entry_export_product_options');
		$data['entry_export_product_quantity'] = $this->language->get('entry_export_product_quantity');
		$data['entry_export_product_price'] = $this->language->get('entry_export_product_price');
		$data['entry_export_product_total'] = $this->language->get('entry_export_product_total');
		$data['entry_export_product_tax'] = $this->language->get('entry_export_product_tax');
		$data['entry_export_product_reward'] = $this->language->get('entry_export_product_reward');
	
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
	
		$data['column_setting_order'] = $this->language->get('column_setting_order');
		$data['column_setting_invoice'] = $this->language->get('column_setting_invoice');
		$data['column_setting_mail'] = $this->language->get('column_setting_mail');
		$data['column_setting_export'] = $this->language->get('column_setting_export');
	
		$data['user_token'] = $this->session->data['user_token'];
	
		$license = $this->model_sale_orderpro->checkLicense();
	
		if (!$license) {
			$data['error_license'] = $this->language->get('error_license');
		} else {
			$data['error_license'] = false;
		}
	
		$return_page = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
	
		$url = '';
	
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
	
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
	
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
	
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
	
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
	
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
	
		$data['breadcrumbs'] = array();
	
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
	
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_orders'),
			'href' => $this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);
	
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('sale/orderprose', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);
	
		$data['action'] = $this->url->link('sale/orderprose', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['cancel'] = $this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
	
		$data['orderpro_license'] = $this->config->get('orderpro_license');
		$data['orderpro_show_pid'] = $this->config->get('orderpro_show_pid');
		$data['orderpro_show_image'] = $this->config->get('orderpro_show_image');
		$data['orderpro_show_model'] = $this->config->get('orderpro_show_model');
		$data['orderpro_show_sku'] = $this->config->get('orderpro_show_sku');
		$data['orderpro_show_mpn'] = $this->config->get('orderpro_show_mpn');
		$data['orderpro_show_upc'] = $this->config->get('orderpro_show_upc');
		$data['orderpro_show_isbn'] = $this->config->get('orderpro_show_isbn');
		$data['orderpro_show_ean'] = $this->config->get('orderpro_show_ean');
		$data['orderpro_show_jan'] = $this->config->get('orderpro_show_jan');
		$data['orderpro_show_location'] = $this->config->get('orderpro_show_location');
		$data['orderpro_show_weight'] = $this->config->get('orderpro_show_weight');
		$data['orderpro_show_realqty'] = $this->config->get('orderpro_show_realqty');
		$data['orderpro_show_nowprice'] = $this->config->get('orderpro_show_nowprice');
		$data['orderpro_show_discount'] = $this->config->get('orderpro_show_discount');
		$data['orderpro_autocomplete_type'] = $this->config->get('orderpro_autocomplete_type');
		$data['orderpro_neworder_notify'] = $this->config->get('orderpro_neworder_notify');
		$data['orderpro_neworder_notify_admin'] = $this->config->get('orderpro_neworder_notify_admin');
		$data['orderpro_notabs_mode'] = $this->config->get('orderpro_notabs_mode');
		$data['orderpro_invoice_type'] = $this->config->get('orderpro_invoice_type');
		$data['orderpro_show_affiliate'] = $this->config->get('orderpro_show_affiliate');
		$data['orderpro_show_similar'] = $this->config->get('orderpro_show_similar');
		$data['orderpro_auto_addreward'] = $this->config->get('orderpro_auto_addreward');
		$data['orderpro_decimal_place'] = $this->config->get('orderpro_decimal_place');
		$data['orderpro_return_page'] = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
		$data['orderpro_clone_orders'] = $this->config->get('orderpro_clone_orders');
		$data['orderpro_merge_orders'] = $this->config->get('orderpro_merge_orders');
	
		//Invoice
		$data['orderpro_invoice_pid'] = $this->config->get('orderpro_invoice_pid');
		$data['orderpro_invoice_image'] = $this->config->get('orderpro_invoice_image');
		$data['orderpro_invoice_model'] = $this->config->get('orderpro_invoice_model');
		$data['orderpro_invoice_sku'] = $this->config->get('orderpro_invoice_sku');
		$data['orderpro_invoice_mpn'] = $this->config->get('orderpro_invoice_mpn');
		$data['orderpro_invoice_upc'] = $this->config->get('orderpro_invoice_upc');
		$data['orderpro_invoice_isbn'] = $this->config->get('orderpro_invoice_isbn');
		$data['orderpro_invoice_ean'] = $this->config->get('orderpro_invoice_ean');
		$data['orderpro_invoice_jan'] = $this->config->get('orderpro_invoice_jan');
		$data['orderpro_invoice_location'] = $this->config->get('orderpro_invoice_location');
		$data['orderpro_invoice_weight'] = $this->config->get('orderpro_invoice_weight');
	
		//Neworder mail
		$data['orderpro_neworder_pid'] = $this->config->get('orderpro_neworder_pid');
		$data['orderpro_neworder_image'] = $this->config->get('orderpro_neworder_image');
		$data['orderpro_neworder_model'] = $this->config->get('orderpro_neworder_model');
		$data['orderpro_neworder_sku'] = $this->config->get('orderpro_neworder_sku');
		$data['orderpro_neworder_mpn'] = $this->config->get('orderpro_neworder_mpn');
		$data['orderpro_neworder_upc'] = $this->config->get('orderpro_neworder_upc');
		$data['orderpro_neworder_isbn'] = $this->config->get('orderpro_neworder_isbn');
		$data['orderpro_neworder_ean'] = $this->config->get('orderpro_neworder_ean');
		$data['orderpro_neworder_jan'] = $this->config->get('orderpro_neworder_jan');
		$data['orderpro_neworder_location'] = $this->config->get('orderpro_neworder_location');
		$data['orderpro_neworder_weight'] = $this->config->get('orderpro_neworder_weight');
	
		//Export orders
		$data['orderpro_export'] = $this->config->get('orderpro_export') ? $this->config->get('orderpro_export') : array();
	
		$data['orderpro_export_orders'] = $this->config->get('orderpro_export_orders');
		$data['orderpro_export_orders_one'] = $this->config->get('orderpro_export_orders_one');
	
		$sys_patch = str_replace('/system/', '', DIR_SYSTEM);
	
		$data['bom_files'] = $this->checkBom($sys_patch);
	
		if ($data['bom_files']) {
			$data['error_bom'] = $this->language->get('error_bom');
		} else {
			$data['error_bom'] = '';
		}
	
		$this->document->addStyle('view/stylesheet/orderproe.css');
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
	
		$this->response->setOutput($this->load->view('sale/orderpro/order_setting', $data));
	}

	private function checkBom($path) {
		$sys_patch = str_replace('/system/', '/', DIR_SYSTEM);
	
		if ($dir = @opendir($path)) {
			while($file = readdir($dir)) {
				if (($file == '.') || ($file == '..')) {
					continue;
				}
	
				$file_path = $path . '/' . $file;
	
				if (is_dir($file_path))  {
					$rbooms = $this->checkBom($file_path);
	
					if ($rbooms) {
						if (!empty($booms)) {
							$booms = array_merge($booms, $rbooms);
						} else {
							$booms = $rbooms;
						}
					}
				}
	
				if (is_file($file_path) && strstr($file_path, '.php')) {
					$f = fopen($file_path, 'r');
					$t = fread($f, 3);
	
					if ($t == "\xEF\xBB\xBF") {
						$booms[] = str_replace($sys_patch, '', $file_path);
					}
					fclose($f);
				}
			}
			closedir($dir);
		}
	
		return (!empty($booms)) ? $booms : array();
	}
}
/*
@author Aleksandr Shcherbakov
@e-mail: shchs@ya.ru
@link   https://opencart-group.ru/modules/orderpro/
*/
?>
