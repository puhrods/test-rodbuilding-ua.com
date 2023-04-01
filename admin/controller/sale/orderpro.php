<?php 
class ControllerSaleOrderpro extends Controller {
	private $error = array();
	
	public function index() {
		$this->load->language('sale/orderpro');
	
		$this->document->setTitle($this->language->get('heading_title'));
	
		if (!empty($this->request->get['cancel']) && $this->validate()) {
			$this->load->model('sale/orderpro');
			$this->model_sale_orderpro->clearOrder($this->request->get['cancel']);
		}
	
		$return_page = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
	
		$this->response->redirect($this->url->link($return_page, 'user_token=' . $this->session->data['user_token'], 'SSL'));
	}
	
  	public function edit() {
		$this->load->language('sale/orderpro');
	
		$this->document->setTitle($this->language->get('heading_title'));
	
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			exit('PHP v7.1 Required!');
		}
	
		if ($this->config->get('orderpro_install_done') != '379') {
			$this->install();
		}
	
		$this->load->model('sale/orderpro');

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
			$return_page = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
	
			if (!$this->user->hasPermission('modify', 'sale/orderpro') || (!empty($this->request->post['clone']) && !$this->config->get('orderpro_clone_orders'))) {
				$this->session->data['success'] = '<span class="text-danger">' . $this->language->get('error_permission') . '</span>';
				$this->response->redirect($this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
			} else {
				$post_data = $this->request->post;
	
				if (isset($post_data['shipping_method'])) {
					$post_data['shipping_method'] = $this->clearTags($post_data['shipping_method']);
				}
				if (isset($post_data['payment_method'])) {
					$post_data['payment_method'] = $this->clearTags($post_data['payment_method']);
				}
				if (isset($post_data['order_total'])) {
					foreach ($post_data['order_total'] as $i => $order_total) {
						$post_data['order_total'][$i]['title'] = $this->clearTags($order_total['title']);
						$post_data['order_total'][$i]['text'] = $this->clearTags($order_total['text']);
					}
				}
	
				if (utf8_strlen($post_data['firstname']) < 1) {
					$post_data['firstname'] = $this->language->get('text_noname');
				}
	
				if (utf8_strlen($post_data['shipping_firstname']) < 1) {
					$post_data['shipping_firstname'] = $this->language->get('text_noname');
				}
	
				if (utf8_strlen($post_data['payment_firstname']) < 1) {
					$post_data['payment_firstname'] = $post_data['shipping_firstname'];
				}
	
				if (isset($post_data['shipping_custom_field'])) {
					$post_data['payment_custom_field'] = $post_data['shipping_custom_field'];
				}
	
				if (isset($this->request->get['order_id'])) {
					$post_data['order_id'] = $this->request->get['order_id'];
				}
	
				$order_id = $this->model_sale_orderpro->editOrder($post_data);
	
				$this->session->data['success'] = $this->language->get('text_success');
	
				if (isset($this->request->get['reopen'])) {
					$this->response->redirect($this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id . $url, 'SSL'));
				} else {
					$this->response->redirect($this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL'));
				}
			}
		}

		$this->getForm();
  	}
	
  	public function deleteOrder() {
		$json = array();
	
		$this->load->language('sale/orderpro');
	
		if (!empty($this->request->get['order_id']) && $this->validate()) {
			$this->load->model('sale/orderpro');
	
			$this->model_sale_orderpro->deleteOrder($this->request->get['order_id']);
	
			$json['success'] = $this->language->get('text_success');
	
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function getForm() {
		$this->load->language('sale/orderpro');
	
		$data['heading_title'] = $this->language->get('heading_title');
		$data['orderpro_version'] = 'OrderPro v3.7.9';
	
		$data['text_default'] = $this->language->get('text_default');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_loading'] = $this->language->get('text_loading');
		$data['text_upload'] = $this->language->get('text_upload');
		$data['text_download'] = $this->language->get('text_download');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_wait'] = $this->language->get('text_wait');
		$data['text_commission_add'] = $this->language->get('text_commission_add');
		$data['text_commission_remove'] = $this->language->get('text_commission_remove');
		$data['text_discount'] = $this->language->get('text_discount');
		$data['text_account_exist'] = $this->language->get('text_account_exist');
		$data['text_newsletter'] = $this->language->get('text_newsletter');
		$data['text_store'] = $this->language->get('text_store');
		$data['text_currency'] = $this->language->get('text_currency');
		$data['text_language'] = $this->language->get('text_language');
		$data['text_status'] = $this->language->get('text_status');
		$data['text_customer'] = $this->language->get('text_customer');
		$data['text_shipping'] = $this->language->get('text_shipping');
		$data['text_noname'] = $this->language->get('text_noname');
		$data['text_neworder_history'] = $this->language->get('text_neworder_history');
		$data['text_account_custom_field'] = $this->language->get('text_account_custom_field');
		$data['text_payment_custom_field'] = $this->language->get('text_payment_custom_field');
		$data['text_shipping_custom_field'] = $this->language->get('text_shipping_custom_field');
		$data['text_order_options'] = $this->language->get('text_order_options');
		$data['text_alarm_options'] = $this->language->get('text_alarm_options');
	
		$data['column_pid'] = $this->language->get('column_pid');
		$data['column_image'] = $this->language->get('column_image');
		$data['column_product'] = $this->language->get('column_product');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_sku'] = $this->language->get('column_sku');
		$data['column_jan'] = $this->language->get('column_jan');
		$data['column_ean'] = $this->language->get('column_ean');
		$data['column_mpn'] = $this->language->get('column_mpn');
		$data['column_upc'] = $this->language->get('column_upc');
		$data['column_isbn'] = $this->language->get('column_isbn');
		$data['column_location'] = $this->language->get('column_location');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_realquantity'] = $this->language->get('column_realquantity');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_now_price'] = $this->language->get('column_now_price');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_discount'] = $this->language->get('column_discount');
		$data['column_reward'] = $this->language->get('column_reward');
		$data['column_reward_short'] = $this->language->get('column_reward_short');
		$data['column_desc'] = $this->language->get('column_desc');
		$data['column_cost'] = $this->language->get('column_cost');
		$data['column_nocalc'] = $this->language->get('column_nocalc');
		$data['column_sort'] = $this->language->get('column_sort');
	
		$weight_unit = $this->model_sale_orderpro->getWeightUnit($this->config->get('config_language_id'));
		$data['column_weight'] = sprintf($this->language->get('column_weight'), $weight_unit);
	
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_customer'] = $this->language->get('entry_customer');
		$data['entry_address'] = $this->language->get('entry_address');
		$data['entry_firstname'] = $this->language->get('entry_firstname');
		$data['entry_lastname'] = $this->language->get('entry_lastname');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_telephone'] = $this->language->get('entry_telephone');
		$data['entry_fax'] = $this->language->get('entry_fax');
		$data['entry_postcode'] = $this->language->get('entry_postcode');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_zone'] = $this->language->get('entry_zone');
		$data['entry_zone_code'] = $this->language->get('entry_zone_code');
		$data['entry_city'] = $this->language->get('entry_city');
		$data['entry_address_1'] = $this->language->get('entry_address_1');
		$data['entry_address_2'] = $this->language->get('entry_address_2');
		$data['entry_company'] = $this->language->get('entry_company');
		$data['entry_company_id'] = $this->language->get('entry_company_id');
		$data['entry_tax_id'] = $this->language->get('entry_tax_id');
		$data['entry_affiliate'] = $this->language->get('entry_affiliate');
		$data['entry_order_status'] = $this->language->get('entry_order_status');
		$data['entry_comment'] = $this->language->get('entry_comment');
		$data['entry_comment_customer'] = $this->language->get('entry_comment_customer');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_payment'] = $this->language->get('entry_payment');
		$data['entry_now_shipping'] = $this->language->get('entry_now_shipping');
		$data['entry_now_payment'] = $this->language->get('entry_now_payment');
		$data['entry_coupon'] = $this->language->get('entry_coupon');
		$data['entry_voucher'] = $this->language->get('entry_voucher');
		$data['entry_discount'] = $this->language->get('entry_discount');
		$data['entry_correct'] = $this->language->get('entry_correct');
		$data['entry_tax'] = $this->language->get('entry_tax');
		$data['entry_reward'] = $this->language->get('entry_reward');
		$data['entry_reward_use'] = $this->language->get('entry_reward_use');
		$data['entry_reward_recived'] = $this->language->get('entry_reward_recived');
		$data['entry_reward_total'] = $this->language->get('entry_reward_total');
		$data['entry_reward_max'] = $this->language->get('entry_reward_max');
		$data['entry_notify'] = $this->language->get('entry_notify');
		$data['entry_notify_products'] = $this->language->get('entry_notify_products');
		$data['entry_notify_admin'] = $this->language->get('entry_notify_admin');
		$data['error_method_available'] = $this->language->get('error_method_available');
	
		$data['button_upload'] = $this->language->get('button_upload');
		$data['button_invoce'] = $this->language->get('button_invoce');
		$data['button_clone'] = $this->language->get('button_clone');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_apply'] = $this->language->get('button_apply');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_setting'] = $this->language->get('button_setting');
		$data['button_add_product'] = $this->language->get('button_add_product');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_remove_product'] = $this->language->get('button_remove_product');
		$data['button_create_account'] = $this->language->get('button_create_account');
		$data['button_discount'] = $this->language->get('button_discount');
		$data['button_correct'] = $this->language->get('button_correct');
		$data['button_add_history'] = $this->language->get('button_add_history');
		$data['button_recalculate'] = $this->language->get('button_recalculate');
		$data['button_getmethods'] = $this->language->get('button_getmethods');
		$data['button_export'] = $this->language->get('button_export');
	
		$data['button_setting_tooltip'] = $this->language->get('button_setting_tooltip');
		$data['button_invoce_tooltip'] = $this->language->get('button_invoce_tooltip');
		$data['button_clone_tooltip'] = $this->language->get('button_clone_tooltip');
		$data['button_apply_tooltip'] = $this->language->get('button_apply_tooltip');
		$data['button_save_tooltip'] = $this->language->get('button_save_tooltip');
		$data['button_cancel_tooltip'] = $this->language->get('button_cancel_tooltip');
		$data['button_export_tooltip'] = $this->language->get('button_export_tooltip');
	
		$data['help_registered'] = $this->language->get('help_registered');
		$data['help_registered_head'] = $this->language->get('help_registered_head');
		$data['help_order_status_head'] = $this->language->get('help_order_status_head');
		$data['help_order_status'] = $this->language->get('help_order_status');
		$data['help_empty_prices'] = $this->language->get('help_empty_prices');
		$data['help_empty_price'] = $this->language->get('help_empty_price');
		$data['help_empty_discounts'] = $this->language->get('help_empty_discounts');
		$data['help_empty_discount'] = $this->language->get('help_empty_discount');
		$data['help_autocomplite'] = $this->language->get('help_autocomplite');
		$data['help_weight'] = $this->language->get('help_weight');
		$data['help_price'] = $this->language->get('help_price');
		$data['help_now_price'] = $this->language->get('help_now_price');
		$data['help_qty'] = $this->language->get('help_qty');
		$data['help_stock'] = $this->language->get('help_stock');
		$data['help_product_discount'] = $this->language->get('help_product_discount');
		$data['help_nocalc_head'] = $this->language->get('help_nocalc_head');
		$data['help_nocalc'] = $this->language->get('help_nocalc');
		$data['help_coupon_head'] = $this->language->get('help_coupon_head');
		$data['help_coupon'] = $this->language->get('help_coupon');
		$data['help_coupon_use'] = $this->language->get('help_coupon_use');
		$data['help_voucher_head'] = $this->language->get('help_voucher_head');
		$data['help_voucher'] = $this->language->get('help_voucher');
		$data['help_voucher_use'] = $this->language->get('help_voucher_use');
		$data['help_discount_head'] = $this->language->get('help_discount_head');
		$data['help_discount'] = $this->language->get('help_discount');
		$data['help_correct_head'] = $this->language->get('help_correct_head');
		$data['help_correct'] = $this->language->get('help_correct');
		$data['help_recalculate_head'] = $this->language->get('help_recalculate_head');
		$data['help_recalculate'] = $this->language->get('help_recalculate');
		$data['help_recalculate_bad'] = $this->language->get('help_recalculate_bad');
		$data['help_reward_removed_head'] = $this->language->get('help_reward_removed_head');
		$data['help_reward_removed'] = $this->language->get('help_reward_removed');
		$data['help_reward_add_head'] = $this->language->get('help_reward_add_head');
		$data['help_reward_add'] = $this->language->get('help_reward_add');
		$data['help_reward_use_head'] = $this->language->get('help_reward_use_head');
		$data['help_reward_use'] = $this->language->get('help_reward_use');
		$data['help_commission_add_head'] = $this->language->get('help_commission_add_head');
		$data['help_commission_add'] = $this->language->get('help_commission_add');
		$data['help_commission_remove_head'] = $this->language->get('help_commission_remove_head');
		$data['help_commission_remove'] = $this->language->get('help_commission_remove');
		$data['help_notify_products'] = $this->language->get('help_notify_products');
		$data['help_similar'] = $this->language->get('help_similar');
		$data['help_getmethods'] = $this->language->get('help_getmethods');
		$data['help_getmethods_head'] = $this->language->get('help_getmethods_head');
		
		$data['error_order_options'] = $this->language->get('error_order_options');
	
		$data['tab_order'] = $this->language->get('tab_order');
		$data['tab_order_history'] = $this->language->get('tab_order_history');
		$data['tab_total'] = $this->language->get('tab_total');
	
		$data['success_order_history'] = $this->language->get('success_order_history');
	
		$data['user_token'] = $this->session->data['user_token'];
		
		$data['error_info'] = array();
		$data['error_warning'] = array();
		$data['error_error'] = array();
	
		if ($this->user->hasPermission('modify', 'sale/orderpro')) {
			$data['is_admin'] = '1';
		} else {
			$data['is_admin'] = '';
		}
	
		if (isset($this->error['warning'])) {
			$data['error_warning'][] = $this->error['warning'];
		}
	
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
	
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

		$return_page = $this->config->get('orderpro_return_page') ? $this->config->get('orderpro_return_page') : 'sale/order';
	
		$data['breadcrumbs'] = array();
	
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);
	
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link($return_page, 'user_token=' . $this->session->data['user_token'] . $url, 'SSL')
		);
	
		if (!empty($this->request->get['order_id'])) {
			$data['action'] = $this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		}
	
		$data['temp_order_id'] = mt_rand(777777777, 999999999);
	
		$data['setting'] = $this->url->link('sale/orderprose', 'user_token=' . $this->session->data['user_token'] . $url, 'SSL');
		$data['cancel'] = $this->url->link('sale/orderpro', 'user_token=' . $this->session->data['user_token'] . '&cancel=' . $data['temp_order_id'] . $url, 'SSL');
	
		$data['clone_order'] = $this->config->get('orderpro_clone_orders') ? 1 : 0;
		$data['clone'] = '';
	
		if (!empty($this->request->get['order_id']) && $this->config->get('orderpro_export_orders')) {
			$data['export_order'] = $this->url->link('sale/orderpro/export', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'], 'SSL');
		} else {
			$data['export_order'] = '';
		}
	
		if (isset($this->request->get['order_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$order_info = $this->model_sale_orderpro->getOrder($this->request->get['order_id']);
		}
	
		$data['order_id'] = !empty($this->request->get['order_id']) ? $this->request->get['order_id'] : '';
		$data['store_id'] = !empty($order_info) ? $order_info['store_id'] : 0;
	
		$license = $this->model_sale_orderpro->checkLicense();
	
		if (!$license) {
			$data['error_error'][] = $this->language->get('error_license');
		}
	
		$data['error_license'] = $license ? 0 : 1;
	
		if ($this->config->get('orderpro_invoice_type')) {
			$data['invoice'] = $this->url->link('sale/orderpro/invoice', 'user_token=' . $this->session->data['user_token'], 'SSL');
		} else {
			$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'], 'SSL');
		}
	
		$data['show_pid'] = $this->config->get('orderpro_show_pid');
		$data['show_image'] = $this->config->get('orderpro_show_image');
		$data['show_model'] = $this->config->get('orderpro_show_model');
		$data['show_sku'] = $this->config->get('orderpro_show_sku');
		$data['show_ean'] = $this->config->get('orderpro_show_ean');
		$data['show_jan'] = $this->config->get('orderpro_show_jan');
		$data['show_location'] = $this->config->get('orderpro_show_location');
		$data['show_weight'] = $this->config->get('orderpro_show_weight');
		$data['show_mpn'] = $this->config->get('orderpro_show_mpn');
		$data['show_upc'] = $this->config->get('orderpro_show_upc');
		$data['show_isbn'] = $this->config->get('orderpro_show_isbn');
		$data['show_realqty'] = $this->config->get('orderpro_show_realqty');
		$data['show_nowprice'] = $this->config->get('orderpro_show_nowprice');
		$data['show_discount'] = $this->config->get('orderpro_show_discount');
		$data['show_affiliate'] = $this->config->get('orderpro_show_affiliate');
		$data['show_similar'] = $this->config->get('orderpro_show_similar');
	
		$data['orderpro_notabs_mode'] = $this->config->get('orderpro_notabs_mode');
		$data['orderpro_autocomplete_type'] = $this->config->get('orderpro_autocomplete_type');
		$decimal_place = ($this->config->get('orderpro_decimal_place') != '') ? $this->config->get('orderpro_decimal_place') : 2;
	
		$data['admin_language'] = $this->config->get('config_admin_language');
		$data['languages'] = $this->model_sale_orderpro->getLanguages();
	
		if (!empty($order_info)) {
			foreach ($data['languages'] as $lang) {
				if ($lang['language_id'] == $order_info['language_id']) {
					$data['admin_language'] = $lang['code'];
				}
			}
		}
	
		$this->load->model('setting/store');
	
		$data['stores'] = $this->model_setting_store->getStores();
	
		$data['store_url'] = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
	
		$data['similar_customer_id'] = 0;
	
		if (!empty($order_info) && ($order_info['customer_id'])) {
			$data['customer_id'] = $order_info['customer_id'];

			if ($data['show_similar']) {
				$data['similar_customer_id'] = $this->model_sale_orderpro->getTotalOrders(array('filter_customer_id' => $data['customer_id']), $order_info['order_id']);
			}
		} else {
			$data['customer_id'] = '';
		}
	
		$data['reward_status'] = $this->config->get('total_reward_status');
	
		if (!empty($data['customer_id'])) {
			$customer_info = $this->model_sale_orderpro->getCustomer($data['customer_id']);
	
			$customer_group_id = !empty($customer_info['customer_group_id']) ? $customer_info['customer_group_id'] : $this->config->get('config_customer_group_id');
	
			if (!empty($order_info)) {
				$reward_use = $this->model_sale_orderpro->getUsedRewardsByOrderId($order_info['order_id']);
				$data['reward_use'] = $reward_use * -1;
	
				if (!$data['reward_use']) {
					$order_totals = $this->model_sale_orderpro->getOrderTotals($order_info['order_id']);
	
					if ($order_totals) {
						foreach ($order_totals as $total) {
							if ($total['code'] == 'reward') {
								$points = '';
	
								$start = strpos($total['title'], '(') + 1;
								$end = strrpos($total['title'], ')');
	
								if ($start && $end) {
									$points = substr($total['title'], $start, $end - $start);
								}
	
								if ($points) {
									$data['reward_use'] = $points;
								}
	
								break;
							}
						}
					}
				}
			} else {
				$data['reward_use'] = 0;
			}
	
			$reward_total = $this->model_sale_orderpro->getRewardTotal($data['customer_id']);
	
			$data['reward_total'] = $reward_total ? $reward_total : 0;
	
			if (!empty($order_info)) {
				$reward_recived = $this->model_sale_orderpro->getReceivedRewardsByOrderId($order_info['order_id']);
	
				$data['reward_recived'] = $reward_recived ? $reward_recived : 0;
			} else {
				$data['reward_recived'] = 0;
			}
	
			$data['reward_possibly'] = $data['reward_total'] - $data['reward_recived'];
	
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
	
			$data['reward_use'] = 0;
			$data['reward_total'] = 0;
			$data['reward_possibly'] = 0;
			$data['reward_recived'] = 0;
		}
	
		if (!empty($order_info) && ($order_info['reward'])) {
			$data['reward_cart'] = $order_info['reward'];
		} else {
			$data['reward_cart'] = 0;
		}
	
		$data['customer_group_id'] = $customer_group_id;
	
		$data['customer'] = !empty($order_info) ? $order_info['customer'] : '';
	
		if (!empty($order_info) && ($order_info['firstname'] != '')) {
			$data['firstname'] = $order_info['firstname'];
		} else {
			$data['firstname'] = $this->language->get('text_noname');
		}
	
		$data['lastname'] = !empty($order_info) ? $order_info['lastname'] : '';
	
		$data['similar_email'] = 0;
	
		if (!empty($order_info)) {
			$data['email'] = $order_info['email'];
	
			if ($data['show_similar'] && $order_info['email']) {
				$data['similar_email'] = $this->model_sale_orderpro->getTotalOrders(array('filter_email' => $order_info['email']), $order_info['order_id']);
			}
		} else {
			$data['email'] = '';
		}
	
		$data['similar_telephone'] = 0;
	
		if (!empty($order_info)) { 
			$data['telephone'] = $order_info['telephone'];
	
			if ($data['show_similar'] && $order_info['telephone']) {
				$data['similar_telephone'] = $this->model_sale_orderpro->getTotalOrders(array('filter_telephone' => $order_info['telephone']), $order_info['order_id']);
			}
		} else {
			$data['telephone'] = '';
		}
	
		$data['similar_fax'] = 0;
	
		if (!empty($order_info)) { 
			$data['fax'] = $order_info['fax'];
	
			if ($data['show_similar'] && $order_info['fax']) {
				$data['similar_fax'] = $this->model_sale_orderpro->getTotalOrders(array('filter_fax' => $order_info['fax']), $order_info['order_id']);
			}
		} else {
			$data['fax'] = '';
		}
	
		$data['account_custom_field'] = !empty($order_info) ? $order_info['custom_field'] : array();
		$data['payment_custom_field'] = !empty($order_info) ? $order_info['payment_custom_field'] : array();
		$data['shipping_custom_field'] = !empty($order_info) ? $order_info['shipping_custom_field'] : array();
	
		//Custom Fields
		$this->load->model('tool/upload');
	
		$data['custom_fields'] = array();
		$data['account_custom_fields'] = false;
		$data['address_custom_fields'] = false;
	
		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);
	
		$this->load->model('customer/customer');
		$this->load->model('customer/custom_field');
		$custom_fields = $this->model_customer_custom_field->getCustomFields($filter_data);
	
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				$data['account_custom_fields'] = true;
			}
			if ($custom_field['location'] == 'address') {
				$data['address_custom_fields'] = true;
			}
	
			$custom_field_href = '';
			$custom_field_filename = '';
	
			if ($custom_field['type'] == 'file') {
				if (isset($data['account_custom_field'][$custom_field['custom_field_id']]) && !empty($data['account_custom_field'][$custom_field['custom_field_id']])) {
					$upload_info = $this->model_tool_upload->getUploadByCode($data['account_custom_field'][$custom_field['custom_field_id']]);
				} elseif (isset($data['shipping_custom_field'][$custom_field['custom_field_id']]) && !empty($data['shipping_custom_field'][$custom_field['custom_field_id']])) {
					$upload_info = $this->model_tool_upload->getUploadByCode($data['shipping_custom_field'][$custom_field['custom_field_id']]);
				} else {
					$upload_info = $this->model_tool_upload->getUploadByCode($custom_field['value']);
				}
	
				if ($upload_info) {
					$custom_field_href = $this->url->link('sale/orderpro/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], 'SSL');
					$custom_field_filename = $upload_info['name'];
				}
			}
	
			$custom_field_value = $this->model_customer_custom_field->getCustomFieldValues($custom_field['custom_field_id']);
	
			$data['custom_fields'][] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $custom_field_value,
				'name'               => $custom_field['name'],
				'value'              => $custom_field['value'],
				'type'               => $custom_field['type'],
				'location'           => $custom_field['location'],
				'custom_field_href'  => $custom_field_href,
				'custom_field_filename' => $custom_field_filename,
				'sort_order'         => $custom_field['sort_order']
			);
		}
	
		if (!empty($order_info)) {
			$data['addresses'] = $this->model_customer_customer->getAddresses($order_info['customer_id']);
		} else {
			$data['addresses'] = array();
		}
	
		if (!empty($order_info) && ($order_info['invoice_no'])) {
			$data['invoice_no'] = $order_info['invoice_no'];
		} else {
			$data['invoice_no'] = $this->model_sale_orderpro->getInvoiceNo();
		}
	
		if (!empty($order_info) && ($order_info['shipping_firstname'] != '')) {
			$data['shipping_firstname'] = $order_info['shipping_firstname'];
		} else {
			$data['shipping_firstname'] = $this->language->get('text_noname');
		}
	
		$data['shipping_lastname'] = !empty($order_info) ? $order_info['shipping_lastname'] : '';
		$data['shipping_company'] = !empty($order_info) ? $order_info['shipping_company'] : '';
		$data['shipping_address_1'] = !empty($order_info) ? $order_info['shipping_address_1'] : '';
		$data['shipping_address_2'] = !empty($order_info) ? $order_info['shipping_address_2'] : ''; 
		$data['shipping_city'] = !empty($order_info) ? $order_info['shipping_city'] : '';
		$data['shipping_postcode'] = !empty($order_info) ? $order_info['shipping_postcode'] : '';
		$data['shipping_country_id'] = !empty($order_info) ? $order_info['shipping_country_id'] : $this->config->get('config_country_id');
		$data['shipping_zone_id'] = !empty($order_info) ? $order_info['shipping_zone_id'] : $this->config->get('config_zone_id');
	
		if (!empty($order_info) && ($order_info['shipping_method'])) {
			$data['shipping_method'] = $this->clearTags($order_info['shipping_method']);
		} else {
			$data['shipping_method'] = '';
		}
	
		$data['shipping_code'] = !empty($order_info) ? $order_info['shipping_code'] : '';
	
		$data['shipping_methods'] = array();
	
		if (!empty($order_info) && ($order_info['payment_firstname'] != '')) {
			$data['payment_firstname'] = $order_info['payment_firstname'];
		} else {
			$data['payment_firstname'] = $this->language->get('text_noname');
		}
	
		$data['payment_lastname'] = !empty($order_info) ? $order_info['payment_lastname'] : '';
		$data['payment_company'] = !empty($order_info) ? $order_info['payment_company'] : '';
		$data['payment_address_1'] = !empty($order_info) ? $order_info['payment_address_1'] : '';
		$data['payment_address_2'] = !empty($order_info) ? $order_info['payment_address_2'] : '';
		$data['payment_city'] = !empty($order_info) ? $order_info['payment_city'] : '';
		$data['payment_postcode'] = !empty($order_info) ? $order_info['payment_postcode'] : '';
		$data['payment_country_id'] = !empty($order_info) ? $order_info['payment_country_id'] : $this->config->get('config_country_id');
		$data['payment_zone_id'] = !empty($order_info) ? $order_info['payment_zone_id'] : $this->config->get('config_zone_id');
	
		$data['countries'] = $this->model_sale_orderpro->getCountries();
	
		$data['payment_methods'] = array();
	
    	if (!empty($order_info) && ($order_info['payment_method'])) {
			$data['payment_method'] = $this->clearTags($order_info['payment_method']);
		} else {
			$data['payment_method'] = '';
    	}
	
		$data['payment_code'] = !empty($order_info) ? $order_info['payment_code'] : '';
	
		$data['affiliate_id'] = !empty($order_info) ? $order_info['affiliate_id'] : '';
		
		$data['affiliate'] = '';
	
		if (!empty($order_info) && ($order_info['affiliate_id'])) {
			$affiliate_info = $this->model_customer_customer->getAffiliate($order_info['affiliate_id']);
	
			if (!empty($affiliate_info)) {
				$data['affiliate'] = $affiliate_info['firstname'] . ' ' . $affiliate_info['lastname'];
			}
		}
	
		$data['commission'] = !empty($order_info) ? number_format((float)$order_info['commission'], $decimal_place, '.', '') : 0;
	
		$data['order_status_id'] = !empty($order_info) ? $order_info['order_status_id'] : $this->config->get('config_order_status_id');
	
		$data['order_statuses'] = array();
	
		$this->load->model('localisation/order_status');
	
		if (!empty($order_info)) {
			$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		} else {
			$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
	
			foreach ($order_statuses as $order_status) {
				if ($order_status['order_status_id'] == $this->config->get('config_order_status_id')) {
					$data['order_statuses'][] = array(
						'order_status_id' => $order_status['order_status_id'],
						'name'            => $order_status['name']
					);
				}
			}
		}
	
		$data['comment'] = !empty($order_info) ? $order_info['comment'] : '';
	
		if (!empty($order_info)) {
			$data['date_added'] = $order_info['date_added'];
			$data['order_number'] = sprintf($this->language->get('text_order_number'), $order_info['order_id'], date('d.m.Y H:i', strtotime($order_info['date_added'])));
		} else {
			$data['date_added'] = '';
			$data['order_number'] = $data['order_id'];
		}
	
		$data['ip'] = !empty($order_info) ? $order_info['ip'] : '';
	
		$filter_data = array();
		$data['similar_lastname'] = 0;
	
		if ($data['show_similar'] && !empty($order_info)) {
			if ($order_info['lastname']) {
				$filter_data['filter_lastname'] = $order_info['lastname'];
			} elseif ($order_info['payment_lastname']) {
				$filter_data['filter_lastname'] = $order_info['payment_lastname'];
			} elseif ($order_info['shipping_lastname']) {
				$filter_data['filter_lastname'] = $order_info['shipping_lastname'];
			}
			if ($filter_data) {
				$data['similar_lastname'] = $this->model_sale_orderpro->getTotalOrders($filter_data, $order_info['order_id']);
			}
		}
	
		$filter_data = array();
		$data['similar_company'] = 0;
	
		if ($data['show_similar'] && !empty($order_info)) {
			if ($order_info['payment_company']) {
				$filter_data['filter_company'] = $order_info['payment_company'];
			} elseif ($order_info['shipping_company']) {
				$filter_data['filter_company'] = $order_info['shipping_company'];
			}
			if ($filter_data) {
				$data['similar_company'] = $this->model_sale_orderpro->getTotalOrders($filter_data, $order_info['order_id']);
			}
		}
	
		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
	
		if (!empty($order_info)) {
			$data['currency_code'] = $order_info['currency_code'];
			$currency_code = $order_info['currency_code'];
			$currency_value = $order_info['currency_value'];
		} else {
			$data['currency_code'] = $this->config->get('config_currency');
			$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));
			$currency_code = $currency_info['code'];
			$currency_value = $currency_info['value'];
		}
	
		if (!empty($order_info)) {
			$commission_order = $this->currency->format($data['commission'], $currency_code, $currency_value);
			$data['commission_order'] = $this->clearTags($commission_order);
			$data['commission_total'] = $this->model_customer_customer->getTotalTransactionsByOrderId($order_info['order_id']);
		} else {
			$data['commission_order'] = '';
			$data['commission_total'] = '';
		}
	
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
	
		$data['order_products'] = array();
		$old_products = array();
		$weight_cart = 0;
		$items_cart = 0;
		$total_cart = 0;
		$discount_cart = 0;
	
		if (!empty($order_info)) {
			$order_products = $this->model_sale_orderpro->getOrderProducts($order_info['order_id']);			
		} else {
			$order_products = array();
		}
	
		$status_off_warning = false;
		$status_del_warning = false;
	
		foreach ($order_products as $order_product) {
			$option_datas = array();
			$order_option = array();
			$option = array();
			$info_option = '';
			$alarm_options = 0;
	
			$order_options = $this->model_sale_orderpro->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);
	
			foreach ($order_options as $ooption) {
				if ($ooption['type'] == 'file') {
					$oupload_info = $this->model_tool_upload->getUploadByCode($ooption['value']);
					
					if ($oupload_info) {
						$info_option .= $ooption['name'] . ': ' . $oupload_info['name'] . '<br>';
					} else {
						$info_option .= $ooption['name'] . ': ' . $ooption['value'] . '<br>';
					}
				} else {
					$info_option .= $ooption['name'] . ': ' . $ooption['value'] . '<br>';
				}
			}
	
			$product_options = $this->model_catalog_product->getProductOptions($order_product['product_id']);
	
			if ($product_options) {
				foreach ($product_options as $product_option) {
					if (($product_option['type'] == 'select') || ($product_option['type'] == 'radio') || ($product_option['type'] == 'checkbox') || ($product_option['type'] == 'image')) {
						$option_value_data = array();
	
						foreach ($product_option['product_option_value'] as $product_option_value) {
							if ((float)$product_option_value['price']) {
								$oprice = $this->currency->format($product_option_value['price'], $currency_code, $currency_value);
								$oprice = $this->clearTags($oprice);
							} else {
								$oprice = false;
							}
	
							$option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $product_option_value['name'],
								'price'                   => $oprice,
								'price_prefix'            => $product_option_value['price_prefix']
							);
						}
	
						$option_datas[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $option_value_data,
							'required'          => $product_option['required']
						);
	
					} elseif (($product_option['type'] == 'text') || ($product_option['type'] == 'textarea') || ($product_option['type'] == 'time') || ($product_option['type'] == 'date') || ($product_option['type'] == 'datetime')) {
						$option_value = '';
	
						foreach ($order_options as $order_option_data) {
							if ($order_option_data['product_option_id'] == $product_option['product_option_id']) {
								$option_value = $order_option_data['value'];
							}
						}
	
						$option_datas[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $option_value,
							'required'          => $product_option['required']
						);
	
					} elseif ($product_option['type'] == 'file') {
						$option_value = '';
						$option_href = '';
						$option_filename = '';
	
						foreach ($order_options as $order_option_data) {
							if ($order_option_data['product_option_id'] == $product_option['product_option_id']) {
								$upload_info = $this->model_tool_upload->getUploadByCode($order_option_data['value']);
	
								if ($upload_info) {
									$option_value = $upload_info['code'];
									$option_href = $this->url->link('sale/orderpro/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $upload_info['code'], 'SSL');
									$option_filename = $upload_info['name'];
								}
							}
						}
	
						$option_datas[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $option_value,
							'option_filename'   => $option_filename,
							'required'          => $product_option['required'],
							'href'              => $option_href
						);
					}
				}
			}
	
			foreach ($order_options as $order_option_data) {
				$order_option[$order_option_data['product_option_id']][$order_option_data['product_option_value_id']]['product_option_value_id'] = $order_option_data['product_option_value_id'];
				$order_option[$order_option_data['product_option_id']][$order_option_data['product_option_value_id']]['value'] = isset($order_option_data['value']) ? $order_option_data['value'] : '';
			}
	
			$check_option = $order_option;
	
			if ($option_datas) {
				foreach ($option_datas as $option_data) {
					if (($option_data['type'] == 'select') || ($option_data['type'] == 'radio') || ($option_data['type'] == 'checkbox') || ($option_data['type'] == 'image')) {
						$oalarm = 0;
	
						foreach ($option_data['option_value'] as $option_value) {
							if (isset($check_option[$option_data['product_option_id']][$option_value['product_option_value_id']])) {
								if ($option_data['type'] == 'checkbox') {
									unset($check_option[$option_data['product_option_id']][$option_value['product_option_value_id']]);
								} else {
									unset($check_option[$option_data['product_option_id']]);
								}
							}
						}
	
						if (($option_data['type'] != 'checkbox') && !$option_data['required'] && isset($check_option[$option_data['product_option_id']][0])) {
							unset($check_option[$option_data['product_option_id']]);
						}
	
						if (!empty($check_option[$option_data['product_option_id']])) {
							$oalarm = 1;
						} else {
							unset($check_option[$option_data['product_option_id']]);
						}
	
						$option[] = array(
							'product_option_id' => $option_data['product_option_id'],
							'option_id'         => $option_data['option_id'],
							'name'              => $option_data['name'],
							'type'              => $option_data['type'],
							'option_value'      => $option_data['option_value'],
							'required'          => $option_data['required'],
							'alarm'             => $oalarm
						);
	
					} elseif (($option_data['type'] == 'text') || ($option_data['type'] == 'textarea') || ($option_data['type'] == 'time') || ($option_data['type'] == 'date') || ($option_data['type'] == 'datetime')) {
						$oalarm = 0;
	
						if (isset($check_option[$option_data['product_option_id']][0]['value'])) {
							if ($check_option[$option_data['product_option_id']][0]['value'] == $option_data['option_value']) {
								unset($check_option[$option_data['product_option_id']]);
							} else {
								$oalarm = 1;
							}
						}
	
						$option[] = array(
							'product_option_id' => $option_data['product_option_id'],
							'option_id'         => $option_data['option_id'],
							'name'              => $option_data['name'],
							'type'              => $option_data['type'],
							'option_value'      => $option_data['option_value'],
							'required'          => $option_data['required'],
							'alarm'             => $oalarm
						);
	
					} elseif ($option_data['type'] == 'file') {
						$oalarm = 0;
	
						if (isset($check_option[$option_data['product_option_id']][0]['value'])) {
							if ($check_option[$option_data['product_option_id']][0]['value'] == $option_data['option_value']) {
								unset($check_option[$option_data['product_option_id']]);
							} else {
								$oalarm = 1;
							}
						}
	
						$option[] = array(
							'product_option_id' => $option_data['product_option_id'],
							'option_id'         => $option_data['option_id'],
							'name'              => $option_data['name'],
							'type'              => $option_data['type'],
							'option_value'      => $option_data['option_value'],
							'option_filename'   => $option_data['option_filename'],
							'required'          => $option_data['required'],
							'href'              => $option_data['href'],
							'alarm'             => $oalarm
						);
					}
				}
			}
	
			if (!empty($check_option)) {
				$alarm_options = 1;
			}
	
			$realquantity = 0;
			$now_price = 0;
			$now_special = 0;
			$now_points = 0;
			$now_status = 33;
			$now_image = '';
			$now_popap = false;
			$now_href = '';
	
			$product_info = $this->model_catalog_product->getProduct($order_product['product_id'], $customer_group_id);
	
			if ($product_info) {
				$realquantity = $product_info['quantity'];
				$now_price = number_format((float)$product_info['price'], $decimal_place, '.', '');
				$now_special = number_format((float)$product_info['special'], $decimal_place, '.', '');
				$now_points = $product_info['points'];
				$now_status = $product_info['status'];
				$now_href = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $this->url->link('product/product', 'product_id=' . $product_info['product_id'], 'SSL'));
	
				if ($product_info['image'] && is_file(DIR_IMAGE . $product_info['image'])) {
					$now_image = $this->model_tool_image->resize($product_info['image'], 45, 45);
					$now_popap = HTTPS_CATALOG . 'image/' . $product_info['image'];
				}
			}
	
			if (!$now_image) {
				$now_image = $this->model_tool_image->resize('placeholder.png', 45, 45);
			}
	
			if ($now_status == 0) {
				$status_off_warning = true;
			}
	
			if ($now_status == 33) {
				$status_del_warning = true;
			}
	
			$now_discount = 0;
			$now_discount_qty = 0;
	
			$product_discounts = $this->model_catalog_product->getOrderProductDiscounts($order_product['product_id'], $customer_group_id);
	
			if ($product_discounts) {
				foreach ($product_discounts as $product_discount) {
					$now_discount = number_format((float)$product_discount['price'], $decimal_place, '.', '');
					$now_discount_qty = $product_discount['quantity'];
				}
			}
	
			if (isset($order_product['discount_amount']) && ($order_product['discount_amount'] > 0)) {
				$discount_amount = number_format((float)$order_product['discount_amount'], $decimal_place, '.', '');
	
				if ($order_product['discount_type'] == 'S') {
					$discount_cart += ($discount_amount * (float)$order_product['quantity']);
				}
				if ($order_product['discount_type'] == 'P') {
					$discount_cart += (($order_product['price'] * (float)$order_product['quantity']) - (float)$order_product['total']);
				}
			} else {
				$discount_amount = 0;
			}
	
			$weight_cart += (float)$order_product['weight'];
			$items_cart += (float)$order_product['quantity'];
			$total_cart += (float)$order_product['total'];
	
			$data['order_products'][] = array(
				'order_product_id' => (int)$order_product['order_product_id'],
				'product_id'       => (int)$order_product['product_id'],
				'name'             => $order_product['name'],
				'model'            => $order_product['model'],
				'sku'              => $order_product['sku'],
				'upc'              => $order_product['upc'],
				'ean'              => $order_product['ean'],
				'jan'              => $order_product['jan'],
				'isbn'             => $order_product['isbn'],
				'mpn'              => $order_product['mpn'],
				'location'         => $order_product['location'],
				'weight'           => $order_product['weight'],
				'option'           => $option,
				'order_option'     => $order_option,
				'info_option'      => $info_option,
				'alarm_options'    => $alarm_options,
				'quantity'         => (float)$order_product['quantity'],
				'realquantity'     => $realquantity,
				'price'            => number_format((float)$order_product['price'], $decimal_place, '.', ''),
				'now_price'        => $now_price,
				'now_special'      => $now_special,
				'now_discount'     => $now_discount,
				'now_discount_qty' => $now_discount_qty,
				'now_points'       => $now_points,
				'discount_amount'  => $discount_amount,
				'discount_type'    => $order_product['discount_type'],
				'total'            => number_format((float)$order_product['total'], $decimal_place, '.', ''),
				'tax'              => number_format((float)$order_product['tax'], $decimal_place, '.', ''),
				'reward'           => $order_product['reward'],
				'href'             => $now_href,
				'image'            => $now_image,
				'popap'            => $now_popap,
				'status'           => $now_status
			);
	
			$old_products[] = array(
				'order_product_id' => (int)$order_product['order_product_id'],
				'product_id'       => (int)$order_product['product_id'],
				'order_option'     => $order_option,
				'quantity'         => (float)$order_product['quantity']
			);
		}
	
		$data['old_products'] = urlencode(json_encode($old_products));
	
		$data['weight_cart'] = $weight_cart;
		$data['items_cart'] = $items_cart;
		$data['total_cart'] = number_format($total_cart, $decimal_place, '.', '');
		$data['discount_cart'] = number_format($discount_cart, $decimal_place, '.', '');
	
		$products_vars = count($data['order_products'], COUNT_RECURSIVE);
		$input_vars = ini_get('max_input_vars');
	
		$posible_vars = $products_vars + 100;
	
		if (($input_vars - $posible_vars) < 0) {
			$data['error_warning'][] = sprintf($this->language->get('error_input_vars'), $input_vars, $posible_vars);
		}
	
		if ($status_off_warning) {
			$data['error_info'][] = $this->language->get('error_off_product');
		}
	
		if ($status_del_warning) {
			$data['error_error'][] = $this->language->get('error_del_product');
		}
	
		$data['status_del_warning'] = $status_del_warning ?: 0;
	
		if (!empty($order_info)) {
			$order_totals = $this->model_sale_orderpro->getOrderTotals($order_info['order_id']);
		} else {
			$order_totals = array();
		}
	
		$data['coupon_status'] = $this->config->get('total_coupon_status');
		$data['coupon_code'] = '';
	
		if (!empty($order_info)) {
			$order_coupon = $this->model_sale_orderpro->getCouponByOrderId($order_info['order_id']);
	
			if ($order_coupon) {
				$coupon_code = $this->model_sale_orderpro->getCodeCouponById($order_coupon['coupon_id']);
	
				if ($coupon_code) {
					$data['coupon_code'] = $coupon_code;
				}
			} else {
				if (!empty($order_totals)) {
					foreach ($order_totals as $total) {
						if ($total['code'] == 'coupon') {
							$coupon_code = '';
	
							$start = strpos($total['title'], '(') + 1;
							$end = strrpos($total['title'], ')');
	
							if ($start && $end) {
								$coupon_code = substr($total['title'], $start, $end - $start);
							}
	
							if ($coupon_code) {
								$data['coupon_code'] = $coupon_code;
							}
	
							break;
						}
					}
				}
			}
		}
	
		$data['voucher_status'] = $this->config->get('total_voucher_status');
		$data['voucher_code'] = '';
	
		if (!empty($order_info)) {
			$order_voucher = $this->model_sale_orderpro->getVoucherByOrderId($order_info['order_id']);
	
			if ($order_voucher) {
				$voucher_code = $this->model_sale_orderpro->getCodeVoucherById($order_voucher['voucher_id']);
	
				if ($voucher_code) {
					$data['voucher_code'] = $voucher_code;
				}
			} else {
				if (!empty($order_totals)) {
					foreach ($order_totals as $total) {
						if ($total['code'] == 'voucher') {
							$voucher_code = '';
	
							$start = strpos($total['title'], '(') + 1;
							$end = strrpos($total['title'], ')');
	
							if ($start && $end) {
								$voucher_code = substr($total['title'], $start, $end - $start);
							}
	
							if ($voucher_code) {
								$data['voucher_code'] = $voucher_code;
							}
	
							break;
						}
					}
				}
			}
		}
	
		$data['order_totals'] = array();
	
		if (!empty($order_totals)) {
			foreach ($order_totals as $total) {
				$total_text = $this->currency->format((float)$total['value'], $currency_code, $currency_value);
				$total_value = $this->clearTags((float)$total['value']);
	
				$data['order_totals'][] = array(
					'order_total_id' => $total['order_total_id'],
					'code'       => $total['code'],
					'title'      => $this->clearTags($total['title']),
					'text'       => $this->clearTags($total_text),
					'value'      => number_format($total_value, $decimal_place, '.', ''),
					'sort_order' => $total['sort_order']
				);
			}
		}
	
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
	
		$this->document->addStyle('view/stylesheet/orderproe.css');
		$this->document->addStyle('view/javascript/jquery/colorbox/colorbox.css');
		$this->document->addScript('view/javascript/jquery/colorbox/jquery.colorbox-min.js');
	
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
	
		$this->response->setOutput($this->load->view('sale/orderpro/order_form', $data));
	}
	
  	public function setting() {
		$this->response->redirect($this->url->link('sale/orderprose', 'user_token=' . $this->session->data['user_token'], 'SSL'));
  	}
	
	public function export() {
		if (!$this->user->hasPermission('modify', 'sale/orderpro') || !$this->config->get('orderpro_export_orders')) {
			exit('Error: You do not have permission!');
		}
	
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			exit('Error! PHP v7.1 Required for this operation!');
		}
	
		if ($this->config->get('orderpro_install_done') != '379') {
			$this->install();
		}
	
		if (is_file(DIR_SYSTEM . 'library/PHPExcel.php')) {
			$this->load->model('sale/orderproex');
	
			$orders = array();
	
			if (isset($this->request->post['selected'])) {
				$orders = $this->request->post['selected'];
			}
			if (isset($this->request->get['order_id'])) {
				$orders[] = $this->request->get['order_id'];
			}
	
			if ($orders) {
				$filename = $this->model_sale_orderproex->getExport($orders);
	
				if ($filename) {
					$file = DIR_DOWNLOAD . $filename;
	
					if (!headers_sent()) {
						if (is_file($file)) {
							header('Content-Type: application/octet-stream');
							header('Content-Description: File Transfer');
							header('Content-Disposition: attachment; filename="' . $filename . '"');
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: public');
							header('Content-Length: ' . filesize($file));
							readfile($file, 'rb');
							unlink($file);
							exit;
						} else {
							exit('Error: File not found: ' . $file . '!');
						}
					} else {
						exit('Error: Headers already sent out!');
					}
				} else {
					exit('Error: Operation failed!');
				}
			} else {
				exit('Error: Orders not found!');
			}
		} else {
			exit('Error: "system/library/PHPExcel.php" not found! Please install "fix_phpExcel" from "fixed" folder.');
		}
	}
	
  	private function install() {
		$this->load->model('sale/orderpro');
		$this->model_sale_orderpro->installOrderpro();
	
		return true;
	}
	
   	protected function validate() {
		if (!$this->user->hasPermission('modify', 'sale/orderpro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			$this->error['warning'] = 'Error! PHP v7.1 Required for this operation!';
		}
	
		return !$this->error;
  	}
	
  	protected function validateNewcustomer($data = array()) {
		$error = array();
	
		$this->load->language('sale/orderpro');
	
		if (!$this->user->hasPermission('modify', 'sale/orderpro')) {
			$error['permission'] = $this->language->get('error_permission');
		}
	
		if (!isset($data['firstname']) || (utf8_strlen($data['firstname']) < 1) || (utf8_strlen($data['firstname']) > 32)) {
			$error['firstname'] = $this->language->get('error_firstname');
		}
	
		$this->load->model('sale/orderproh');
	
		if (!isset($data['email']) || !$this->model_sale_orderproh->validateEmail($data['email'])) {
			$error['email'] = $this->language->get('error_email');
		} else {
			$customer_info = $this->model_sale_orderpro->getCustomerByEmail($data['email']);
	
			if ($customer_info) {
				$error['exists'] = $this->language->get('error_exists');
			}
		}
	
		if (isset($data['address'])) {
			foreach ($data['address'] as $key => $value) {
				if ((utf8_strlen($value['firstname']) < 1) || (utf8_strlen($value['firstname']) > 32)) {
					$error['address_firstname'][$key] = $this->language->get('error_firstname');
				}
			}
		}
	
		if ($error) {
			$error['warning'] = $this->language->get('error_warning');
		}
		
		return $error;
  	}
	
	public function country() {
		$json = array();
	
		if (isset($this->request->get['country_id'])) {
			$this->load->model('localisation/country');
	
			$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);
	
			if ($country_info) {
				$this->load->model('localisation/zone');
	
				$json = array(
					'country_id'        => $country_info['country_id'],
					'name'              => $country_info['name'],
					'iso_code_2'        => $country_info['iso_code_2'],
					'iso_code_3'        => $country_info['iso_code_3'],
					'address_format'    => $country_info['address_format'],
					'postcode_required' => $country_info['postcode_required'],
					'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
					'status'            => $country_info['status']		
				);
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function couponAutocomplete() {
		$json = array();
	
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('sale/orderpro');
	
			$results = $this->model_sale_orderpro->couponAutocomplete(array('filter_name' => $this->request->get['filter_name']));
	
			foreach ($results as $result) {
				$json[] = array(
					'name'  => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8') . ' (' . $result['code'] . ')',
					'code'  => $result['code']
				);
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function voucherAutocomplete() {
		$json = array();
	
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('sale/orderpro');
	
			$results = $this->model_sale_orderpro->voucherAutocomplete(array('filter_name' => $this->request->get['filter_name']));
	
			foreach ($results as $result) {
				$json[] = array(
					'name'  => utf8_substr(html_entity_decode($result['message'], ENT_QUOTES, 'UTF-8'), 0, 25) . '...' . '(' . $result['code'] . ')',
					'code'  => $result['code']
				);
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function customerAutocomplete() {
		$json = array();
	
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
			$filter_email = $this->request->get['filter_name'];
	
			$this->load->model('sale/orderpro');
			$this->load->model('customer/customer');
	
			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_email' => $filter_email,
				'filter_status' => '1'
			);
	
			$results = $this->model_sale_orderpro->getCustomers($filter_data);
	
			foreach ($results as $result) {
				$customer_group_id = !empty($result['customer_group_id']) ? $result['customer_group_id'] : $this->config->get('config_customer_group_id');
	
				$custom_field = json_decode($result['custom_field'], true);
				$address = $this->model_customer_customer->getAddresses($result['customer_id']);
	
				$json[] = array(
					'customer_id'         => $result['customer_id'],
					'customer_group_id' => $customer_group_id,
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'customer_group'    => $result['customer_group'],
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'fax'               => $result['fax'],
					'custom_field'      => $custom_field,
					'address'           => $address
				);
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function productAutocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_pid']) || isset($this->request->get['filter_name']) || isset($this->request->get['filter_model']) || isset($this->request->get['filter_sku']) || isset($this->request->get['filter_upc']) || isset($this->request->get['filter_ean']) || isset($this->request->get['filter_jan']) || isset($this->request->get['filter_isbn']) || isset($this->request->get['filter_mpn']) || isset($this->request->get['filter_location'])) {
			$this->load->model('catalog/product');
			
			$filter_pid = isset($this->request->get['filter_pid']) ? $this->request->get['filter_pid'] : '';
			$filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : '';
			$filter_model = isset($this->request->get['filter_model']) ? $this->request->get['filter_model'] : '';
			$filter_sku = isset($this->request->get['filter_sku']) ? $this->request->get['filter_sku'] : '';
			$filter_upc = isset($this->request->get['filter_upc']) ? $this->request->get['filter_upc'] : '';
			$filter_ean = isset($this->request->get['filter_ean']) ? $this->request->get['filter_ean'] : '';
			$filter_jan = isset($this->request->get['filter_jan']) ? $this->request->get['filter_jan'] : '';
			$filter_isbn = isset($this->request->get['filter_isbn']) ? $this->request->get['filter_isbn'] : '';
			$filter_mpn = isset($this->request->get['filter_mpn']) ? $this->request->get['filter_mpn'] : '';
			$filter_location = isset($this->request->get['filter_location']) ? $this->request->get['filter_location'] : '';
			
			$limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 15;	

			$data = array(
				'filter_pid'   => $filter_pid,
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'filter_sku'   => $filter_sku,
				'filter_upc'   => $filter_upc,
				'filter_ean'   => $filter_ean,
				'filter_jan'   => $filter_jan,
				'filter_isbn'  => $filter_isbn,
				'filter_mpn'   => $filter_mpn,
				'filter_location' => $filter_location,
				'filter_status'   => '1',
				'start'        => 0,
				'limit'        => $limit
			);
			
			$results = $this->model_catalog_product->getCatalogProducts($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => strip_tags(html_entity_decode($result['model'], ENT_QUOTES, 'UTF-8')),
					'sku'        => strip_tags(html_entity_decode($result['sku'], ENT_QUOTES, 'UTF-8')),
					'upc'        => strip_tags(html_entity_decode($result['upc'], ENT_QUOTES, 'UTF-8')),
					'ean'        => strip_tags(html_entity_decode($result['ean'], ENT_QUOTES, 'UTF-8')),
					'jan'        => strip_tags(html_entity_decode($result['jan'], ENT_QUOTES, 'UTF-8')),
					'isbn'       => strip_tags(html_entity_decode($result['isbn'], ENT_QUOTES, 'UTF-8')),
					'mpn'        => strip_tags(html_entity_decode($result['mpn'], ENT_QUOTES, 'UTF-8')),
					'location'   => strip_tags(html_entity_decode($result['location'], ENT_QUOTES, 'UTF-8'))
				);	
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getToProduct() {
		$json = array();
	
		if (!empty($this->request->get['product_id'])) {
			$this->load->model('catalog/product');
	
			if (!empty($this->request->get['customer_group_id'])) {
				$customer_group_id = $this->request->get['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
	
			$decimal_place = ($this->config->get('orderpro_decimal_place') != '') ? $this->config->get('orderpro_decimal_place') : 2;
	
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id'], $customer_group_id);
	
			if ($product_info) {
				$option_data = array();
	
				$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);	
	
				foreach ($product_options as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						$option_value_data = array();
	
						foreach ($product_option['product_option_value'] as $product_option_value) {
							if ((float)$product_option_value['price']) {
								$ovprice = $this->currency->format($product_option_value['price'], $this->config->get('config_currency'));
								$ovprice = $this->clearTags($ovprice);
							} else {
								$ovprice = false;
							}
	
							$option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $product_option_value['name'],
								'price'                   => $ovprice,
								'price_prefix'            => $product_option_value['price_prefix']
							);
						}
	
						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $product_option['name'],
							'type'                 => $product_option['type'],
							'required'             => $product_option['required']
						);
	
					} else {
						$option_data[] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'value'             => $product_option['value'],
							'required'          => $product_option['required']
						);				
					}
				}
	
				$discount = 0;
				$discount_qty = 0;
	
				$product_discounts = $this->model_catalog_product->getOrderProductDiscounts($product_info['product_id'], $customer_group_id);
	
				if ($product_discounts) {
					foreach ($product_discounts as $product_discount) {
						$discount = number_format((float)$product_discount['price'], $decimal_place, '.', '');
						$discount_qty = $product_discount['quantity'];
					}
				}
	
				$special = 0;
				$reward = 0;
	
				if ((float)$product_info['special']) {
					$special = $product_info['special'];
				}
				if ((float)$product_info['reward']) {
					$reward = $product_info['reward'];
				}
	
				$this->load->model('tool/image');
	
				if ($product_info['image'] && is_file(DIR_IMAGE . $product_info['image'])) {
					$image = $this->model_tool_image->resize($product_info['image'], 45, 45);
					$popap = HTTPS_CATALOG . 'image/' . $product_info['image'];
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 45, 45);
					$popap = false;
				}
	
				$json['product_id'] = $product_info['product_id'];
				$json['name'] = strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8'));
				$json['model'] = strip_tags(html_entity_decode($product_info['model'], ENT_QUOTES, 'UTF-8'));
				$json['sku'] = strip_tags(html_entity_decode($product_info['sku'], ENT_QUOTES, 'UTF-8'));
				$json['upc'] = strip_tags(html_entity_decode($product_info['upc'], ENT_QUOTES, 'UTF-8'));
				$json['ean'] = strip_tags(html_entity_decode($product_info['ean'], ENT_QUOTES, 'UTF-8'));
				$json['jan'] = strip_tags(html_entity_decode($product_info['jan'], ENT_QUOTES, 'UTF-8'));
				$json['isbn'] = strip_tags(html_entity_decode($product_info['isbn'], ENT_QUOTES, 'UTF-8'));
				$json['mpn'] = strip_tags(html_entity_decode($product_info['mpn'], ENT_QUOTES, 'UTF-8'));
				$json['location'] = strip_tags(html_entity_decode($product_info['location'], ENT_QUOTES, 'UTF-8'));
				$json['quantity'] = $product_info['quantity'];
				$json['points'] = $product_info['points'];
				$json['reward'] = $reward;
				$json['special'] = number_format((float)$special, $decimal_place, '.', '');
				$json['discount'] = $discount;
				$json['discount_qty'] = $discount_qty;
				$json['option'] = $option_data;
				$json['image'] = $image;
				$json['popap'] = $popap;
				$json['href'] = str_replace(HTTPS_SERVER, HTTPS_CATALOG, $this->url->link('product/product', 'product_id=' . $product_info['product_id'], 'SSL'));
				$json['price'] = number_format((float)$product_info['price'], $decimal_place, '.', '');
				$json['weight'] = $this->weight->convert($product_info['weight'], $product_info['weight_class_id'], $this->config->get('config_weight_class_id'));
				$json['status'] = $product_info['status'];
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function createCustomer() {
		$json = array();
	
		$this->load->model('sale/orderpro');
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$data = $this->request->post;
			
			$validate = $this->validateNewcustomer($data);
			
			if ($validate) {
				$json['error'] = $validate;
			} else {
				$data['password'] = $this->model_sale_orderpro->setCustomerPassword();
				$data['confirm'] = $data['password'];
				$data['affiliate'] = '0';
				$data['status'] = '1';
				$data['safe'] = '0';
				$data['newsletter'] = '1';
		
				if (isset($data['payment_country_id']) && ($data['payment_country_id'] != '')) {
					$customer_country_id = $data['payment_country_id'];
				} else {
					$customer_country_id = $this->config->get('config_country_id');
				}
		
				if (isset($data['payment_zone_id']) && ($data['payment_zone_id'] != '')) {
					$customer_zone_id = $data['payment_zone_id'];
				} else {
					$customer_zone_id = $this->config->get('config_zone_id');
				}
		
				if (isset($data['custom_field'])) {
					$data['custom_field'] = $data['custom_field'];
				} else {
					$data['custom_field'] = '';
				}
	
				if (isset($data['shipping_custom_field'])) {
					$shipping_custom_field = $data['shipping_custom_field'];
				} else {
					$shipping_custom_field = '';
				}
		
				$data['address'][] = array(
					'firstname'   => $data['payment_firstname'],
					'lastname'    => $data['payment_lastname'],
					'company'     => $data['payment_company'],
					'address_1'   => $data['payment_address_1'],
					'address_2'   => $data['payment_address_2'],
					'city'        => $data['payment_city'],
					'postcode'    => $data['payment_postcode'],
					'country_id'  => $customer_country_id,
					'zone_id'     => $customer_zone_id,
					'custom_field' => $shipping_custom_field,
					'default'     => '1'
				);
		
				$this->load->model('customer/customer');
				$this->model_customer_customer->addCustomer($data);
	
				$new_customer = $this->model_sale_orderpro->getCustomerByEmail($data['email']);
				$customer_id = $new_customer['customer_id'];
		
				if (!empty($data['order_id'])) {
					$this->model_sale_orderpro->addCustomerToOrder($customer_id, $data['order_id']);
				}
		
				$this->load->model('setting/setting');
				$store_info = $this->model_setting_setting->getSetting('config', $data['store_id']);
		
				if ($store_info) {
					$data['store_name'] = $store_info['config_name'];
					$data['store_address'] = $store_info['config_address'];
					$data['store_email'] = $store_info['config_email'];
					$data['store_telephone'] = $store_info['config_telephone'];
					$data['store_fax'] = $store_info['config_fax'];
					$logo = $store_info['config_logo'];
				} else {
					$data['store_name'] = $this->config->get('config_name');
					$data['store_address'] = $this->config->get('config_address');
					$data['store_email'] = $this->config->get('config_email');
					$data['store_telephone'] = $this->config->get('config_telephone');
					$data['store_fax'] = $this->config->get('config_fax');
					$logo = $this->config->get('config_logo');
				}
	
				$language_directory = 'en-gb';
				$language_id = 1;
		
				$languages = $this->model_sale_orderpro->getLanguages();
		
				foreach ($languages as $lang) {
					if ($lang['code'] == $data['language']) {
						$language_directory = $lang['code'];
						$language_id = $lang['language_id'];
					}
				}
		
				$language = new Language($language_directory);
				$language->load($language_directory);
				$language->load('sale/orderpro');
		
				$data['store_url'] = $this->config->get('config_secure') ? HTTPS_CATALOG : HTTP_CATALOG;
	
				if ($language_id) {
					$langdata = $this->model_setting_setting->getSettingValue('config_langdata', $data['store_id']);
	
					if ($langdata) {
						$store_infos = json_decode($langdata, true);
	
						if (!empty($store_infos[$language_id]['address'])) {
							$data['store_address'] = $store_infos[$language_id]['address'];
						}
						if (!empty($store_infos[$language_id]['name'])) {
							$data['store_name'] = $store_infos[$language_id]['name'];
						}
					}
				}
	
				$data['store_address'] = str_ireplace('<br />', ', ', nl2br($data['store_address']));
	
				$json['customer_id'] = $customer_id;
				$json['success'] = $language->get('success_add_account');
	
				$subject = sprintf($language->get('text_subject_account'), $data['store_name']);
	
				if ($logo && is_file(DIR_IMAGE . $logo)) {
					$this->load->model('tool/image');
	
					list($width_logo, $height_logo) = getimagesize(DIR_IMAGE . $logo);
	
					if (($width_logo > 420) || ($height_logo > 100)) {
						$data['logo'] = $this->model_tool_image->resize($logo, 420, 100);
					} else {
						$data['logo'] = HTTP_CATALOG . 'image/' . $logo;
					}
	
				} else {
					$data['logo'] = false;
				}
	
				$data['title'] = sprintf($language->get('text_subject_account'), $data['store_name']);
				$data['text_message'] = $language->get('text_message_account');
				$data['text_address'] = $language->get('text_address');
				$data['text_telephone'] = $language->get('text_telephone');
				$data['text_email'] = $language->get('text_email');
				$data['text_fax'] = $language->get('text_fax');
				$data['text_url'] = $language->get('text_url');
				$data['text_login'] = $language->get('text_login');
				$data['text_password'] = $language->get('text_password');
				$data['text_login_url'] = $language->get('text_login_url');
				$data['login_url'] = $data['store_url'] . 'index.php?route=account/login';
				$data['text_account'] = $language->get('text_account');
	
				$html = $this->load->view('mail/orderpro/newcustomer', $data);
	
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
				$mail->setTo($data['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender(html_entity_decode($data['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function customerAddress() {
		$json = array();
	
		if (!empty($this->request->get['address_id'])) {
			$this->load->model('customer/customer');
			$json = $this->model_customer_customer->getAddress($this->request->get['address_id']);
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function removeReward() {
		$this->load->language('sale/orderpro');
	
		$json = array();
	
		if ($this->user->hasPermission('modify', 'sale/orderpro')) {
			if (!empty($this->request->get['order_id'])) {
				$this->load->model('sale/orderpro');
	
				$order_info = $this->model_sale_orderpro->getOrder($this->request->get['order_id']);
	
				if ($order_info && $order_info['customer_id']) {
					$this->model_sale_orderpro->deleteOrderRewardReceived($this->request->get['order_id']);
	
					$json['success'] = $this->language->get('success_reward_removed');
				} else {
					$json['error'] = $this->language->get('error_action');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  	}
	
	public function addReward() {
		$this->load->language('sale/orderpro');
	
		$json = array();
	
		if ($this->user->hasPermission('modify', 'sale/orderpro')) {
			if (!empty($this->request->get['order_id'])) {
				$this->load->model('sale/orderpro');
				$this->load->model('sale/orderproh');
	
				if (!empty($this->request->post['customer_id']) && ((float)$this->request->post['reward_cart'] > 0)) {
					$reward_exist = $this->model_sale_orderpro->getReceivedRewardsByOrderId($this->request->get['order_id']);
	
					$this->model_sale_orderpro->deleteOrderRewardReceived($this->request->get['order_id']);
					$json['success'] = $this->model_sale_orderproh->updateReward($this->request->post['customer_id'], $this->language->get('text_order_id') . $this->request->get['order_id'], (float)$this->request->post['reward_cart'], $this->request->get['order_id'], (float)$reward_exist);
				} else {
					$json['error'] = $this->language->get('error_action');
				}
	
				$json['reward_total'] = $this->model_sale_orderpro->getReceivedRewardsByOrderId($this->request->get['order_id']);
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addCommission() {
		$this->load->language('sale/orderpro');
	
		$json = array();
    
		if ($this->user->hasPermission('modify', 'sale/orderpro')) {
			if (isset($this->request->get['order_id']) && !empty($this->request->get['order_id'])) {
				$this->load->model('sale/orderpro');
	
				$order_info = $this->model_sale_orderpro->getOrder($this->request->get['order_id']);
	
				if ($order_info && $order_info['affiliate_id']) {
					$this->load->model('customer/customer');
	
					$affiliate_total = $this->model_customer_customer->getTotalTransactionsByOrderId($this->request->get['order_id']);
	
					if (!$affiliate_total) {
						$this->model_customer_customer->addTransaction($order_info['affiliate_id'], $this->language->get('text_order_id') . $this->request->get['order_id'], $order_info['commission'], $this->request->get['order_id']);
	
						$json['success'] = $this->language->get('success_commission_added');
					} else {
						$json['error'] = $this->language->get('error_action'); 
					}
				} else {
					$json['error'] = $this->language->get('error_action');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  	}
	
	public function removeCommission() {
		$this->load->language('sale/orderpro');
	
		$json = array(); 
    
		if ($this->user->hasPermission('modify', 'sale/orderpro')) {
			if (isset($this->request->get['order_id']) && !empty($this->request->get['order_id'])) {
				$this->load->model('sale/orderpro');
	
				$order_info = $this->model_sale_orderpro->getOrder($this->request->get['order_id']);
	
				if ($order_info && $order_info['affiliate_id']) {
					$this->load->model('customer/customer');
	
					$this->model_customer_customer->deleteTransaction($this->request->get['order_id']);
	
					$json['success'] = $this->language->get('success_commission_removed');
				} else {
					$json['error'] = $this->language->get('error_action');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  	}
	
	public function history() {
		$this->load->language('sale/orderpro');
	
		$data['error'] = '';
		$data['success'] = '';
	
		$this->load->model('sale/orderproh');
	
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'sale/orderpro')) { 
				$data['error'] = $this->language->get('error_permission');
			}
	
			if (!$data['error']) {
				if (!empty($this->request->post['history_order_status_id'])) {
					$this->request->post['order_status_id'] = $this->request->post['history_order_status_id'];
				}
				$this->request->post['comment'] = !empty($this->request->post['comment']) ? $this->request->post['comment'] : '';
	
				if (!empty($this->request->post['admin_comment'])) {
					$this->request->post['comment'] = $this->request->post['admin_comment'];
				}
	
				$order_history_id = $this->model_sale_orderproh->addOrderHistory($this->request->get['order_id'], $this->request->post);
	
				$data['success'] = $this->language->get('success_order_history');
			}
		}
	
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_date'] = $this->language->get('column_date');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_notify'] = $this->language->get('column_notify');
		$data['column_comment'] = $this->language->get('column_comment');
	
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
	
		$limit = 10;
	
		$data['histories'] = array();
	
		$results = $this->model_sale_orderproh->getOrderHistories($this->request->get['order_id'], ($page - 1) * $limit, $limit);
	
		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date('d.m.Y H:i', strtotime($result['date_added']))
			);
		}
	
		$history_total = $this->model_sale_orderproh->getTotalOrderHistories($this->request->get['order_id']);
	
		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/orderpro/history', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . '&page={page}', 'SSL');
	
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit), $history_total, ceil($history_total / $limit));
	
		$this->response->setOutput($this->load->view('sale/orderpro/order_history', $data));
  	}
	
	public function download() {
		if (!empty($this->request->get['code'])) {
			$this->load->model('tool/upload');
	
			$code = $this->request->get['code'];
	
			$upload_info = $this->model_tool_upload->getUploadByCode($code);
	
			if ($upload_info) {
				$file = DIR_UPLOAD . $upload_info['filename'];
				$filename = $upload_info['name'];
	
				if (!headers_sent()) {
					if (is_file($file)) {
						header('Content-Type: application/octet-stream');
						header('Content-Description: File Transfer');
						header('Content-Disposition: attachment; filename="' . $filename . '"');
						header('Content-Transfer-Encoding: binary');
						header('Expires: 0');
						header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						header('Pragma: public');
						header('Content-Length: ' . filesize($file));
						readfile($file, 'rb');
						exit;
					} else {
						exit('Ошибка: Не удалось найти файл: ' . $file . '!');
					}
				} else {
					exit('Error: Headers already sent out!');
				}
			}
		}
	}
	
	public function upload() {
		$this->load->language('sale/orderpro');
	
		$json = array();
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->user->hasPermission('modify', 'sale/orderpro')) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				$filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
	
				if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 128)) {
					$json['error'] = $this->language->get('error_filename');
				}
	
				$allowed = array();
	
				$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
	
				$filetypes = explode("\n", $extension_allowed);
	
				foreach ($filetypes as $filetype) {
					$allowed[] = trim(utf8_strtolower($filetype));
				}
	
				if (!in_array(substr(strrchr(utf8_strtolower($filename), '.'), 1), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}
	
				$allowed = array();
	
				$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
	
				$mimes = explode("\n", $mime_allowed);
	
				foreach ($mimes as $mime) {
					$allowed[] = trim($mime);
				}
	
				if (!in_array($this->request->files['file']['type'], $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}
	
				$content = file_get_contents($this->request->files['file']['tmp_name']);
	
				if (preg_match('/\<\?php/i', $content)) {
					$json['error'] = $this->language->get('error_filetype');
				}
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
	
			if (!$json) {
				if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
					$file = $filename . '.' . md5(mt_rand());
	
					move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);
	
					$this->load->model('tool/upload');
	
					$json['code'] = $this->model_tool_upload->addUpload($filename, $file);
	
					$json['filename'] = $filename;
					$json['href'] = $this->url->link('sale/orderpro/download', 'user_token=' . $this->session->data['user_token'] . '&code=' . $json['code'], 'SSL');
					$json['success'] = $this->language->get('success_upload_file');
	
				} else {
					$json['error'] = $this->language->get('error_upload');
				}
			}
		} else {
			$json['error'] = $this->language->get('error_permission');
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  	}
	
  	public function invoice() {
		$this->load->language('sale/orderpro');
	
		$data['title'] = $this->language->get('text_invoice_title');
	
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['base'] = HTTPS_SERVER;
		} else {
			$data['base'] = HTTP_SERVER;
		}
	
		if ($this->config->get('orderpro_install_done') != '379') {
			$this->install();
		}
	
		$data['direction'] = $this->language->get('direction');
		$data['language'] = $this->language->get('code');
	
		$data['show_pid'] = $this->config->get('orderpro_invoice_pid');
		$data['show_image'] = $this->config->get('orderpro_invoice_image');
		$data['show_model'] = $this->config->get('orderpro_invoice_model');
		$data['show_sku'] = $this->config->get('orderpro_invoice_sku');
		$data['show_upc'] = $this->config->get('orderpro_invoice_upc');
		$data['show_ean'] = $this->config->get('orderpro_invoice_ean');
		$data['show_jan'] = $this->config->get('orderpro_invoice_jan');
		$data['show_isbn'] = $this->config->get('orderpro_invoice_isbn');
		$data['show_mpn'] = $this->config->get('orderpro_invoice_mpn');
		$data['show_location'] = $this->config->get('orderpro_invoice_location');
		$data['show_weight'] = $this->config->get('orderpro_invoice_weight');
	
		$this->load->model('sale/orderpro');
		$this->load->model('setting/setting');
	
		$this->load->model('tool/image');
	
		$data['orders'] = array();
	
		$orders = array();
	
		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
	
		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_orderpro->getOrder($order_id);
	
			if ($order_info) {
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_directory']);
				$language->load('sale/orderpro');
	
				$text['text_invoice'] = $language->get('text_invoice');
				$text['text_order'] = $language->get('text_order');
				$text['text_order_id'] = $language->get('text_order_id');
	
				$text['text_date_added'] = $language->get('text_date_added');
				$text['text_email'] = $language->get('text_email');
				$text['text_telephone'] = $language->get('text_telephone');
				$text['text_fax'] = $language->get('text_fax');
				$text['text_url'] = $language->get('text_url');
				$text['text_to'] = $language->get('text_to');
				$text['text_ship_to'] = $language->get('text_ship_to');
				$text['text_address'] = $language->get('text_address');
				$text['text_company'] = $language->get('text_company');
				$text['text_comment_customer'] = $language->get('text_comment_customer');
				$text['text_payment_method'] = $language->get('text_payment_method');
				$text['text_shipping_method'] = $language->get('text_shipping_method');
	
				$text['column_pid'] = $language->get('column_pid');
				$text['column_image'] = $language->get('column_image');
				$text['column_product'] = $language->get('column_product');
				$text['column_model'] = $language->get('column_model');
				$text['column_sku'] = $language->get('column_sku');
				$text['column_upc'] = $language->get('column_upc');
				$text['column_ean'] = $language->get('column_ean');
				$text['column_jan'] = $language->get('column_jan');
				$text['column_isbn'] = $language->get('column_isbn');
				$text['column_mpn'] = $language->get('column_mpn');
				$text['column_location'] = $language->get('column_location');
				$text['column_quantity'] = $language->get('column_quantity');
				$text['column_price'] = $language->get('column_price');
				$text['column_total'] = $language->get('column_total');
				$text['column_comment'] = $language->get('column_comment');
	
				if ($order_info['language_id']) {
					$language_id = $order_info['language_id'];
				} else {
					$language_id = $this->config->get('config_language_id');
				}
	
				$weight_unit = $this->model_sale_orderpro->getWeightUnit($language_id);
				$text['column_weight'] = sprintf($language->get('column_weight'), $weight_unit);
	
				$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
	
				if ($store_info) {
					$store_address = $store_info['config_address'];
					$store_email = $store_info['config_email'];
					$store_telephone = $store_info['config_telephone'];
					$store_fax = $store_info['config_fax'];
					$logo = $store_info['config_logo'];
				} else {
					$store_address = $this->config->get('config_address');
					$store_email = $this->config->get('config_email');
					$store_telephone = $this->config->get('config_telephone');
					$store_fax = $this->config->get('config_fax');
					$logo = $this->config->get('config_logo');
				}
	
				if ($order_info['language_id']) {
					$langdata = $this->model_setting_setting->getSettingValue('config_langdata', $order_info['store_id']);
	
					if ($langdata) {
						$store_infos = json_decode($langdata, true);
	
						if (!empty($store_infos[$order_info['language_id']]['address'])) {
							$store_address = $store_infos[$order_info['language_id']]['address'];
						}
					}
				}
	
				$store_address = str_ireplace('<br />', ', ', nl2br($store_address));
	
				if ($logo && is_file(DIR_IMAGE . $logo)) {
					list($width_logo, $height_logo) = getimagesize(DIR_IMAGE . $logo);
			
					if (($width_logo > 420) || ($height_logo > 100)) {
						$store_logo = $this->model_tool_image->resize($logo, 420, 100);
					} else {
						$store_logo = HTTP_CATALOG . 'image/' . $logo;
					}
	
				} else {
					$store_logo = false;
				}
	
				if ($order_info['invoice_no']) {
					$invoice_no = $order_info['invoice_prefix'] . $order_info['invoice_no'];
				} else {
					$invoice_no = '';
				}
	
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{postcode} {country}' . "\n" . '{zone}' . "\n" . '{city}' . "\n" . '{address_1} {address_2}';
				}
	
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
	
				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);
	
				$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	
				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}';
				}
	
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
	
				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);
	
				$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	
				$product_data = array();
	
				$products = $this->model_sale_orderpro->getOrderProducts($order_id);
	
				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_sale_orderpro->getOrderOptions($order_id, $product['order_product_id']);
	
					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						}
	
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $value
						);								
					}
	
					$image = $this->model_sale_orderpro->getProductImage($product['product_id']);
	
					if ($image && is_file(DIR_IMAGE . $image)) {
						$img = $this->model_tool_image->resize($image, 60, 60);
					} else {
						$img = $this->model_tool_image->resize('placeholder.png', 60, 60);
					}
	
					$product_data[] = array(
						'product_id' => $product['product_id'],
						'img'      => $img,
						'name'     => $product['name'],
						'model'    => $product['model'],
						'sku'      => $product['sku'],
						'upc'      => $product['upc'],
						'ean'      => $product['ean'],
						'jan'      => $product['jan'],
						'isbn'     => $product['isbn'],
						'mpn'      => $product['mpn'],
						'location' => $product['location'],
						'weight'   => $product['weight'],
						'option'   => $option_data,
						'quantity' => $product['quantity'],
						'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
						'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
	
				$voucher_data = array();
	
				$vouchers = $this->model_sale_orderpro->getOrderVouchers($order_id);
	
				foreach ($vouchers as $voucher) {
					$voucher_data[] = array(
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])			
					);
				}
	
				$total_data = array();
	
				$totals_data = $this->model_sale_orderpro->getOrderTotals($order_id);
	
				foreach ($totals_data as $order_total) {
					$total_data[] = array(
						'title' => $this->clearTags($order_total['title']),
						'text'  => $this->currency->format($order_total['value'], $order_info['currency_code'], $order_info['currency_value'])			
					);
				}
	
				$data['orders'][] = array(
					'order_id'	         => $order_id,
					'text'	             => $text,
					'invoice_no'         => $invoice_no,
					'date_added'         => date('d.m.Y H:i', strtotime($order_info['date_added'])),
					'store_name'         => $order_info['store_name'],
					'store_url'          => rtrim($order_info['store_url'], '/'),
					'store_address'      => $store_address,
					'store_email'        => $store_email,
					'store_telephone'    => $store_telephone,
					'store_fax'          => $store_fax,
					'store_logo'         => $store_logo,
					'email'              => $order_info['email'],
					'telephone'          => $order_info['telephone'],
					'shipping_address'   => $shipping_address,
					'shipping_method'    => $order_info['shipping_method'],
					'payment_address'    => $payment_address,
					'company'            => $order_info['payment_company'],
					'payment_method'     => $order_info['payment_method'],
					'product'            => $product_data,
					'voucher'            => $voucher_data,
					'total'              => $total_data,
					'comment'            => nl2br($order_info['comment'])
				);
			}
		}
	
		$this->response->setOutput($this->load->view('sale/orderpro/order_invoice', $data));
	}
	
	public function clearArr(&$value, $key) {
		$value = $this->clearTags($value);
	}
	
   	public function clearTags($str) {
		if (is_array($str)) {
			array_walk($str, array('ControllerSaleOrderpro', 'clearArr'));
		} else {
			$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	
			$str = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', ' ', $str);
			$str = preg_replace('/<\s*style[^>]*>.*?<\s*\/\s*style\s*>/is', ' ', $str);
			$str = preg_replace('/<\s*select[^>]*>.*?<\s*\/\s*select\s*>/is', ' ', $str);
	
			$str = strip_tags($str);
			$str = preg_replace('/(<([^>]+)>)/U', ' ', $str);
			$str = preg_replace('/\s\s+/', ' ', $str);
	
			$str = htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
		}
		return $str;
  	}
	
	public function getMerge() {
		$this->language->load('sale/orderpro');
	
		$json = array();
	
		if (!$this->user->hasPermission('modify', 'sale/orderpro') || !$this->config->get('orderpro_merge_orders')) {
			$json['error'] = $this->language->get('error_permission');
		}
	
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			$json['error'] = 'Error! PHP v7.1 Required for this operation!';
		}
	
		if (!$json) {
			if ($this->config->get('orderpro_install_done') != '379') {
				$this->install();
			}
	
			if (!empty($this->request->post['selected']) && (count($this->request->post['selected']) >= 2)) {
				$data['orders'] = $this->request->post['selected'];
	
				$this->load->model('localisation/order_status');
	
				$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
	
				$data['text_merge_order'] = $this->language->get('text_merge_order');
				$data['text_merge_addnew'] = $this->language->get('text_merge_addnew');
				$data['text_merge_inold'] = $this->language->get('text_merge_inold');
				$data['text_old_nothing'] = $this->language->get('text_old_nothing');
				$data['text_old_status'] = $this->language->get('text_old_status');
				$data['text_old_delete'] = $this->language->get('text_old_delete');
	
				$data['entry_merge_mode'] = $this->language->get('entry_merge_mode');
				$data['entry_order_status'] = $this->language->get('entry_order_status');
				$data['entry_main_order'] = $this->language->get('entry_main_order');
				$data['entry_old_order'] = $this->language->get('entry_old_order');
	
				$data['help_main_order'] = $this->language->get('help_main_order');
	
				$data['button_merge'] = $this->language->get('button_merge');
				$data['button_cancel'] = $this->language->get('button_cancel');
	
				$json['html'] = $this->load->view('sale/orderpro/order_merge', $data);
	
			} else {
				$json['error'] = $this->language->get('error_countmerge');
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function MergeOrder() {
		$this->load->language('sale/orderpro');
	
		$json = array();
		$status = false;
	
		if (!$this->user->hasPermission('modify', 'sale/orderpro') || !$this->config->get('orderpro_merge_orders')) {
			$json['error'] = $this->language->get('error_permission');
		}
	
		if (version_compare(phpversion(), '7.1.0', '<') == true) {
			$json['error'] = 'Error! PHP v7.1 Required for this operation!';
		}
	
		if (!$json) {
			$this->load->model('sale/orderpro');
	
			$orders = $this->request->post['orders'];
			$main_order = $this->request->post['main_order'];
			$merge_mode = $this->request->post['merge_mode'];
			$old_order_action = $this->request->post['old_order'];
			$old_order_status = $this->request->post['old_status_id'];
			$new_order_id = 0;
	
			if ($merge_mode == 2) {
				$all_orders = $orders;
				$main_key = array_search($main_order, $all_orders);
				unset($all_orders[$main_key]);
				$old_orders = $all_orders;
			} else {
				$old_orders = $orders;
			}
	
			$old_orders_data = array();
	
			foreach ($old_orders as $order_id) {
				$order_products = array();
	
				$products = $this->model_sale_orderpro->getOrderProducts($order_id);
	
				foreach ($products as $order_product) {
					$options = $this->model_sale_orderpro->getOrderOptions($order_id, $order_product['order_product_id']);
	
					$order_products[$order_product['order_product_id']] = array(
						'product' => $order_product,
						'options' => $options
					);
				}
	
				$old_orders_data[$order_id] = $order_products;
			}
	
			if ($merge_mode == 2) {
				$status = $this->model_sale_orderpro->addProductsToOrder($main_order, $old_orders_data);
			} else {
				$new_order_id = $this->model_sale_orderpro->getCopyOrder($main_order);
				
				if ($new_order_id) {
					$status = $this->model_sale_orderpro->addProductsToOrder($new_order_id, $old_orders_data);
				}
			}
	
			if ($status) {
				if ($old_order_action == 2) {
					foreach ($old_orders as $order_id) {
						$this->model_sale_orderpro->deleteOrder($order_id);
					}
				}
	
				if ($old_order_action == 1) {
					$hdata = array(
						'order_status_id' => $old_order_status,
						'notify'          => 0,
						'comment'         => ''
					);
	
					$this->load->model('sale/orderproh');
	
					foreach ($old_orders as $order_id) {
						$this->model_sale_orderproh->addOrderHistory($order_id, $hdata);
					}
				}
	
				if ($new_order_id) {
					$edit_order = $this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $new_order_id, 'SSL');
				} else {
					$edit_order = $this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $main_order, 'SSL');
				}
	
				$json['success'] = sprintf($this->language->get('success_merge'), $edit_order);
	
			} else {
				$json['error'] = $this->language->get('error_action');
			}
		}
	
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getsimilar() {
		$this->load->language('sale/orderpro');
	
		$data['column_order_id'] = $this->language->get('column_order_id');
		$data['column_customer'] = $this->language->get('column_customer');
		$data['column_status'] = $this->language->get('column_ostatus');
		$data['column_total'] = $this->language->get('column_total');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');
	
		$data['text_similar_order'] = $this->language->get('text_similar_order');
		$data['text_no_results'] = $this->language->get('text_no_results');
	
		$data['button_view'] = $this->language->get('button_view');
		$data['button_edit'] = $this->language->get('button_edit');
	
		$data['orders'] = array();
	
		if (!empty($this->request->get['order_id']) && !empty($this->request->get['pole'])) {
			$this->load->model('sale/orderpro');
	
			$order_info = $this->model_sale_orderpro->getOrder($this->request->get['order_id']);
	
			$filter_data = array();
	
			$data['text_similar_order'] = sprintf($this->language->get('text_similar_order'), $this->request->get['pole']);
	
			if ($order_info) {
				if ($this->request->get['pole'] == 'customer_id') {
					$filter_data['filter_customer_id'] = $order_info['customer_id'];
				}
				if ($this->request->get['pole'] == 'lastname') {
					if ($order_info['lastname']) {
						$filter_data['filter_lastname'] = $order_info['lastname'];
					} elseif ($order_info['payment_lastname']) {
						$filter_data['filter_lastname'] = $order_info['payment_lastname'];
					} elseif ($order_info['shipping_lastname']) {
						$filter_data['filter_lastname'] = $order_info['shipping_lastname'];
					}
				}
				if ($this->request->get['pole'] == 'email') {
					$filter_data['filter_email'] = $order_info['email'];
				}
				if ($this->request->get['pole'] == 'telephone') {
					$filter_data['filter_telephone'] = $order_info['telephone'];
				}
				if ($this->request->get['pole'] == 'fax') {
					$filter_data['filter_fax'] = $order_info['fax'];
				}
				if ($this->request->get['pole'] == 'company') {
					$filter_data['filter_company'] = $order_info['payment_company'] ? $order_info['payment_company'] : $order_info['shipping_company'];
				}
			}
	
			if ($filter_data) {
				$results = $this->model_sale_orderpro->getOrders($filter_data);
	
				foreach ($results as $result) {
					if ($result['order_id'] != $this->request->get['order_id']) {
						$data['orders'][] = array(
							'order_id'      => $result['order_id'],
							'customer'      => $result['customer'],
							'status'        => $result['status'],
							'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
							'date_added'    => date('d.m.Y H:i', strtotime($result['date_added'])),
							'date_modified' => date('d.m.Y H:i', strtotime($result['date_modified'])),
							'view'          => $this->url->link('sale/order/info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'], 'SSL'),
							'edit'          => $this->url->link('sale/orderpro/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'], 'SSL')
						);
					}
				}
			}
		}
	
		$this->response->setOutput($this->load->view('sale/orderpro/order_similar', $data));
	}
}
/*
@author Aleksandr Shcherbakov
@e-mail: shchs@ya.ru
@link   https://opencart-group.ru/modules/orderpro/
*/
?>