<?php
class ControllerSaleOmproHeader extends Controller {

	public function index() {
		$data['title'] = $this->document->getTitle();
		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}
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
		$data['pages_orders'] = $this->url->link('sale/ompro/orders', $strtoken.'&pageid='.$this->getFirstPageID(), true);

		//	tpl_data_list
		$data['pages'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=pages', true);
		$data['orders'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=orders', true);
		$data['product_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=product', true);
		$data['history_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=history', true);
		$data['filter_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=filter', true);
		$data['filter_product_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=filter_product', true);
		$data['option_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=option', true);

		// tpl_notify_list
		$data['mail_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=mail', true);
		$data['sms_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=sms', true);
		$data['tlgrm_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=tlgrm', true);
		$data['comment_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=comment', true);

		// tpl_export_list
		$data['print_orders_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=print_orders', true);
		$data['print_orders_table_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=print_orders_table', true);
		$data['print_products_table_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=print_products_table', true);

		$data['excel_orders_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=excel_orders', true);
		$data['excel_orders_products_list'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=excel_orders_products', true);


		$data['block_product'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=product', true);
		$data['block_pptrigger'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=pptrigger', true);
		$data['block_order'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=order', true);
		$data['block_mail'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=mail', true);
		$data['block_print_orders_table'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=print_orders_table', true);
		$data['block_print_products_table'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=block&block_target=print_products_table', true);

		$data['page_block_blocks_el'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=page_block&block_target=blocks_el', true);
		$data['page_block_column_el'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=page_block&block_target=column_el', true);
		$data['page_block_tools_el'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=page_block&block_target=tools_el', true);
		$data['page_block_btngroup_el'] = $this->url->link('sale/ompro/templateList', $strtoken . '&get_page=page_block&block_target=btngroup_el', true);

		$data['admin'] = $this->url->link('sale/ompro/admin', $strtoken, true);

		$data['order_fields'] = $this->url->link('sale/ompro/fields', $strtoken . '&get_page=order_fields', true);
		$data['order_as_fields'] = $this->url->link('sale/ompro/fields', $strtoken . '&get_page=order_as_fields', true);

		$data['simple_status'] = $this->omproapi->simpleStatus();

		if ($data['simple_status']) {
			$fields_page_list = array('order_fields','order_as_fields','order_simple_fields','product_fields','product_as_fields');
			$data['order_simple_fields'] = $this->url->link('sale/ompro/fields', $strtoken . '&get_page=order_simple_fields', true);
		} else {
			$fields_page_list = array('order_fields','order_as_fields','product_fields','product_as_fields');
		}

		$data['product_fields'] = $this->url->link('sale/ompro/fields', $strtoken . '&get_page=product_fields', true);
		$data['product_as_fields'] = $this->url->link('sale/ompro/fields', $strtoken . '&get_page=product_as_fields', true);

		if (isset($this->session->data['omanager_page'])) {
			$session_page = $this->session->data['omanager_page'];
			unset($this->session->data['omanager_page']);
		} else {
			$session_page = '';
		}

		$page_list = array('admin','fields');
		foreach ($page_list as $value) {
			if ($session_page == $value) {
				$data[$value.'_active'] = 'active';
			} else {
				$data[$value.'_active'] = '';
			}
		}

		$user_id = $this->session->data['user_id'];

		if (isset($this->request->get['get_page'])) {
			$get_page = $this->request->get['get_page'];
		} else {
			$get_page = '';
		}

		$data['tpl_data_list_treeview_active'] = '';
		$tpl_list = array('pages', 'orders', 'product', 'history', 'filter', 'filter_product', 'option');
		foreach ($tpl_list as $tpl) {
			if ($get_page == $tpl) {
				$data[$tpl.'_active'] = 'active';
				$data['tpl_data_list_treeview_active'] = 'active';
			} else {
				$data[$tpl.'_active'] = '';
			}
		}

		$data['tpl_notify_list_treeview_active'] = '';
		$tpl_list = array('mail','sms','tlgrm', 'comment');
		foreach ($tpl_list as $tpl) {
			if ($get_page == $tpl) {
				$data[$tpl.'_active'] = 'active';
				$data['tpl_notify_list_treeview_active'] = 'active';
			} else {
				$data[$tpl.'_active'] = '';
			}
		}

		$data['tpl_export_list_treeview_active'] = '';
		$tpl_list = array('print_orders', 'print_orders_table', 'print_products_table', 'excel_orders', 'excel_orders_products');
		foreach ($tpl_list as $tpl) {
			if ($get_page == $tpl) {
				$data[$tpl.'_active'] = 'active';
				$data['tpl_export_list_treeview_active'] = 'active';
			} else {
				$data[$tpl.'_active'] = '';
			}
		}

		if (isset($this->request->get['block_target'])) {
			$block_target = $this->request->get['block_target'];
		} else {
			$block_target = '';
		}

		$data['block_list_treeview_active'] = '';
		if ($get_page == 'block') {
			$data['block_list_treeview_active'] = 'active';
		}

		$block_list = array('product','pptrigger','order','mail','print_orders_table','print_products_table');
		foreach ($block_list as $value) {
			if ($block_target == $value) {
				$data['block_'.$value.'_active'] = 'active';
			} else {
				$data['block_'.$value.'_active'] = '';
			}
		}

		$data['page_block_list_treeview_active'] = '';
		if ($get_page == 'page_block') {
			$data['page_block_list_treeview_active'] = 'active';
		}

		$page_block_list = array('blocks_el','column_el','tools_el','btngroup_el');
		foreach ($page_block_list as $value) {
			if ($block_target == $value) {
				$data['page_block_'.$value.'_active'] = 'active';
			} else {
				$data['page_block_'.$value.'_active'] = '';
			}

			$text_page = $this->language->get('text_page_block_'.$value.'_list');
			$data['text_page_block_'.$value.'_list'] = (utf8_strlen($text_page) > 24 ? utf8_substr($text_page, 0, 24) . '...' : $text_page);
		}

		$data['fields_treeview_active'] = '';

		foreach ($fields_page_list as $page) {
			if ($get_page == $page) {
				$data[$page.'_active'] = 'active';
				$data['fields_treeview_active'] = 'active';
			} else {
				$data[$page.'_active'] = '';
			}
		}

		$this->load->model('user/user_group');
		$user_groups = $this->model_user_user_group->getUserGroups();

		if (isset($this->request->get['user_group_id'])) {
			$user_group_id = $this->request->get['user_group_id'];
		} else {
			$user_group_id = 0;
		}

		$data['group_treeview_active'] = '';
		$data['user_groups'] = array();
		foreach ($user_groups as $user_group) {
			$href = $this->url->link( 'sale/ompro/settingGroup', $strtoken.'&user_group_id='.$user_group['user_group_id'], true );
			$active = '';
			if ($user_group_id == $user_group['user_group_id']) {
				$active = $data['group_treeview_active'] = 'active';
			}
			$data['user_groups'][] = array(
				'user_group_id' => $user_group['user_group_id'],
				'name' => (utf8_strlen($user_group['name']) > 24 ? utf8_substr($user_group['name'], 0, 24) . '...' : $user_group['name']),
				'href' => $href,
				'active' => $active
			);
		}

		if (!isset($this->request->get[$token_index]) || !isset($token) || ($this->request->get[$token_index] != $token)) {
			$data['logged'] = '';
			$data['home'] = $this->url->link('common/dashboard', '', true);
		} else {
			$data['logged'] = true;
			$data['home'] = $this->url->link('common/dashboard', $strtoken, true);
			$data['logout'] = $this->url->link('common/logout', $strtoken, true);

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
		$data['ocversion'] = $ocversion;
		$ending = $ocversion >= 230 ? '' : '.tpl';
		return $this->load->view('sale/ompro/ompro_header_setting'.$ending, $data);
	}

	public function getFirstPageID() {
		$order_pages = $this->ompro->getGroupPageList($this->user->getGroupId());
		if ($order_pages) {
			foreach ($order_pages as $page) {
				return $page['template_id'];
			}
		}
		return 0;
	}

}