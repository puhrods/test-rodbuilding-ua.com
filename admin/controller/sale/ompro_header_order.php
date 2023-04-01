<?php
class ControllerSaleOmproHeaderOrder extends Controller {

	public function index() {
		$data['title'] = $this->document->getTitle();

		$data['base'] = $this->ompro->server;
		$data['ocversion'] = $ocversion = $this->ompro->ocversion;

		$data['code'] = $this->language->get('code');
		$data['lang'] = $this->language->get('lang');
		$data['direction'] = $this->language->get('direction');

		if ($ocversion >= 300) {
			$data['strtoken'] = $strtoken = 'user_token='.$this->session->data['user_token'];
			$token_index = 'user_token';
			$token = $this->session->data['user_token'];
		} else {
			$data['strtoken'] = $strtoken = 'token='.$this->session->data['token'];
			$token_index = 'token';
			$token = $this->session->data['token'];
		}

		$data = array_merge($data, $this->load->language('sale/ompro'));

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$data['menu_setting'] = '';
		} else {
			$data['menu_setting'] = '<li class="accent"><a href="'.$this->url->link('sale/ompro/admin', $strtoken, true).'"><i class="fa fa-cog"></i><span>'.$this->language->get('text_setting').'</span></a></li>';
		}

		$user_group_id = $this->user->getGroupId();
		$order_pages = $this->ompro->getGroupPageList($user_group_id);

		if (isset($this->request->get['pageid']) && $this->request->get['pageid']) {
			$session_order_page = $this->request->get['pageid'];
		} else {
			$session_order_page = '';
		}

		$data['order_pages_treeview_active'] = '';
		$data['order_pages'] = array();
		if ($order_pages) {
			foreach ($order_pages as $page) {
				$href = $this->url->link( 'sale/ompro/orders', $strtoken.'&pageid='.$page['template_id'], true );
				$active = '';
				if ($session_order_page == $page['template_id']) {
					$active = $data['order_pages_treeview_active'] = 'active';
				}
				$data['order_pages'][] = array(
					'id' => $page['template_id'],
					'name' => (utf8_strlen($page['name']) > 24 ? utf8_substr($page['name'], 0, 24) . '...' : $page['name']),
					'href' => $href,
					'icon' => $page['icon'] !=='' ? '<i class="fa '.$page['icon'].'"></i>' : '<i class="fa fa-file-text-o"></i>',
					'active' => $active
				);
			}
		}

		if (!isset($this->request->get[$token_index]) || !isset($token) || ($this->request->get[$token_index] != $token)) {
			$data['logged'] = '';
			$data['home'] = $this->url->link('common/dashboard', '', true);
		} else {
			$data['logged'] = true;
			$data['home'] = $this->url->link('common/dashboard', $strtoken, true);
			$data['logout'] = $this->url->link('common/logout', $strtoken, true);

			// Orders
			$this->load->model('sale/order');

			// Processing Orders
			$data['processing_status_total'] = $this->model_sale_order->getTotalOrders(array('filter_order_status' => implode(',', $this->config->get('config_processing_status'))));
			$data['processing_status'] = implode(',', $this->config->get('config_processing_status'));

			// Complete Orders
			$data['complete_status_total'] = $this->model_sale_order->getTotalOrders(array('filter_order_status' => implode(',', $this->config->get('config_complete_status'))));
			$data['complete_status'] = implode(',', $this->config->get('config_complete_status'));

			// Returns
			$this->load->model('sale/return');
			$data['return_total'] = $return_total = $this->model_sale_return->getTotalReturns(array('filter_return_status_id' => $this->config->get('config_return_status_id')));
			$data['return'] = $this->url->link('sale/return', $strtoken, true);

			// Customers
			if ($ocversion >= 300) {
				$this->load->model('report/online');
				$data['online_total'] = $this->model_report_online->getTotalOnline();
				$data['online'] = $this->url->link('report/online', $strtoken, true);
			} else {
				$this->load->model('report/customer');
				$data['online_total'] = $this->model_report_customer->getTotalCustomersOnline();
				$data['online'] = $this->url->link('report/customer_online', $strtoken, true);
			}

			// Products
			$this->load->model('catalog/product');
			$data['product_total'] = $product_total = $this->model_catalog_product->getTotalProducts(array('filter_quantity' => 0));
			$data['product'] = $this->url->link('catalog/product', $strtoken . '&filter_quantity=0', true);

			// Reviews
			$this->load->model('catalog/review');
			$data['review_total'] = $review_total = $this->model_catalog_review->getTotalReviews(array('filter_status' => 0));
			$data['review'] = $this->url->link('catalog/review', $strtoken . '&filter_status=0', true);

			$data['alerts'] = $product_total + $review_total + $return_total;

			$data['stores'] = array();
			$data['stores'][] = array(
				'name' => $this->config->get('config_name'),
				'href' => $this->ompro->catalog
			);
			$this->load->model('setting/store');
			$results = $this->model_setting_store->getStores();
			foreach ($results as $result) {
				$data['stores'][] = array(
					'name' => $result['name'],
					'href' => $result['url']
				);
			}

			$this->load->model('tool/image');
			$this->load->model('user/user');
			$user_id = $this->session->data['user_id'];
			$user_info = $this->model_user_user->getUser($user_id);

			if ($user_info) {
				$data['firstname'] = $user_info['firstname'];
				$data['lastname'] = $user_info['lastname'];
				$data['username']  = $user_info['username'];
				$data['user_group'] = $user_info['user_group'];

				if (is_file(DIR_IMAGE . $user_info['image'])) {
					$data['image'] = $this->model_tool_image->resize($user_info['image'], 45, 45);
				} else {
					$data['image'] = '';
				}
			} else {
				$data['firstname'] = '';
				$data['lastname'] = '';
				$data['username'] = '';
				$data['user_group'] = '';
				$data['image'] = '';
			}
		}

		$data['version'] = $this->omproapi->version;
		$ending = $ocversion >= 230 ? '' : '.tpl';
		return $this->load->view('sale/ompro/ompro_header_order'.$ending, $data);
	}
}