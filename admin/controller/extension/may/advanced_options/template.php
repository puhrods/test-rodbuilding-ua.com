<?php
class ControllerExtensionMayAdvancedOptionsTemplate extends Controller {

	private $error = array();
    private $_ = array();

	public function index() {
		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->document->setTitle($this->_['Combined Options']);

		$this->load->model('extension/may/advanced_options/template');

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->document->setTitle($this->_['Combined Options']);

		$this->load->model('extension/may/advanced_options/template');

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->document->setTitle($this->_['Combined Options']);

		$this->load->model('extension/may/advanced_options/template');

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->document->setTitle($this->_['Combined Options']);

		$this->load->model('extension/may/advanced_options/template');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $option_id) {
				$this->model_extension_may_advanced_options_template->deleteOption($option_id);
			}

			$this->session->data['success'] = $this->_['Success: You have modified combined options!'];

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	public function save() {
		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->load->model('extension/may/advanced_options/template');

		if (isset($this->request->post['option_values']) && 
			//isset($this->request->post['option_children']) && 
			isset($this->request->post['advanced_option_name']) && 
			$this->validateForm()) {

			$data = array();

			foreach ($this->request->post['option_values'] as $option_id => $option_values) {
				foreach ($option_values as $option_value) {
					if (!isset($data[$option_id])) {
						$data[$option_id] = array();
					}
					if (isset($this->request->post['option_children'][$option_id])) {
						$data[$option_id][$option_value] = $this->request->post['option_children'][$option_id][$option_value];
					} else {
						$data[$option_id][$option_value] = array();
					}
				}
			}

			$this->request->post['option_values'] = $data;

			if (!isset($this->request->get['option_id'])) {
				$this->model_extension_may_advanced_options_template->addOption($this->request->post);
			} else {
				$this->model_extension_may_advanced_options_template->editOption($this->request->get['option_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->_['Success: You have modified combined options!'];
		}
	}

	protected function getList() {
		$this->load->model('catalog/option');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'option_name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

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
			'text' => $this->_['Home'],
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->_['Combined Options'],
			'href' => $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/may/advanced_options/template/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/may/advanced_options/template/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['options'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$option_total = $this->model_extension_may_advanced_options_template->getTotalOptions();

		$results = $this->model_extension_may_advanced_options_template->getOptions($filter_data);

		foreach ($results as $result) {
			$children = array();
			foreach (explode(",", $result['children']) as $child_id) {
				$child_names = $this->model_catalog_option->getOptionDescriptions($child_id);
				if (isset($child_names[(int)$this->config->get('config_language_id')])) {
					$children[] = $child_names[(int)$this->config->get('config_language_id')]['name'];
				} else if (count($child_names) > 0) {
					$children[] = $child_names[0]['name'];
				}
			}

			$data['options'][] = array(
				'option_id'  => $result['option_id'],
				'option_name' => $result['option_name'],
				'options' => implode(", ", $children),
				'swatch_image' => $result['swatch_image'],
				'show_first_option_in_list' => $result['show_first_option_in_list'],
				'sort_order' => $result['sort_order'],
				'edit'       => $this->url->link('extension/may/advanced_options/template/edit', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $result['option_id'] . $url, true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . '&sort=option_name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $option_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->_['Showing %d to %d of %d (%d Pages)'], ($option_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($option_total - $this->config->get('config_limit_admin'))) ? $option_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $option_total, ceil($option_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $data['_'] = $this->_;

		$this->response->setOutput($this->load->view('extension/may/advanced_options/template/list', $data));
	}

	protected function getForm() {
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

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
			'text' => $this->_['Home'],
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->_['Combined Options'],
			'href' => $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['option_id'])) {
			$data['action'] = $this->url->link('extension/may/advanced_options/template/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/may/advanced_options/template/edit', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $this->request->get['option_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['option_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$data['option'] = $this->model_extension_may_advanced_options_template->getOption($this->request->get['option_id']);
			if (isset($data['option']['content'])) {
				$content = json_decode($data['option']['content'], true);
				$combinations = array();
				foreach ($content as $option_id => $option_values) {
					$content[$option_id] = array_keys($option_values);
					foreach ($option_values as $option_value => $option_value_children) {
						foreach ($option_value_children as $option_value_child) {
							$combinations[] = $option_value . "-" . $option_value_child;
						}
					}
				}
				$data['option']['content'] = $content;
				$data['option']['combinations'] = $combinations;
			}
		}

		$data['may_advanced_options_config'] = array(
			'swatch_image' => $this->config->get('may_advanced_options_swatch_image'),
			'show_first_option_in_list' => $this->config->get('may_advanced_options_show_first_option_in_list'),
		);

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['url_select_options_form'] = $this->url->link('extension/may/advanced_options/template/getSelectOptionsForm', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['url_option_values_form'] = $this->url->link('extension/may/advanced_options/template/getOptionValuesForm', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['url_option_children_form'] = $this->url->link('extension/may/advanced_options/template/getOptionChildrenForm', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['option_id'])) {
			$data['url_save'] = $this->url->link('extension/may/advanced_options/template/save', 'user_token=' . $this->session->data['user_token'] . '&option_id=' . $this->request->get['option_id'] . $url, true);
		} else {
			$data['url_save'] = $this->url->link('extension/may/advanced_options/template/save', 'user_token=' . $this->session->data['user_token'] . $url, true);
		}

        $data['_'] = $this->_;

		$this->response->setOutput($this->load->view('extension/may/advanced_options/template/form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/may/advanced_options/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/may/advanced_options/template')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/may/advanced_options/template');

			$this->load->model('catalog/option');

			$this->load->model('tool/image');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 10
			);

			$advanced_options = $this->model_extension_may_advanced_options_template->getOptions($filter_data);

			foreach ($advanced_options as $advanced_option) {
				$children = explode(",", $advanced_option['children']);
				$content = json_decode($advanced_option['content'], true);

				$options = array();

				foreach ($children as $option_id) {
					$option = $this->model_catalog_option->getOption($option_id);

					$option_value_data = array();

					$option_values = $this->model_catalog_option->getOptionValues($option['option_id']);

					foreach ($option_values as $option_value) {

						if (!in_array($option_value['option_value_id'], array_keys($content[$option['option_id']]))) {
							continue;
						}


						if (is_file(DIR_IMAGE . $option_value['image'])) {
							$image = $this->model_tool_image->resize($option_value['image'], 50, 50);
						} else {
							$image = $this->model_tool_image->resize('no_image.png', 50, 50);
						}

						$option_value_data[] = array(
							'option_value_id' => $option_value['option_value_id'],
							'name'            => strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8')),
							'image'           => $image,
							'children'		  => $content[$option['option_id']][$option_value['option_value_id']]
						);
					}

					$options[] = array(
						'option_id'    => $option['option_id'],
						'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')),
						'option_value' => $option_value_data
					);
				}

				$json[] = array(
					'value'				 => $advanced_option['option_id'],
					'name' 				 => $advanced_option['option_name'],
					'options'			 => $options,
					'swatch_image'		 => is_null($advanced_option['swatch_image']) ? 0 : $advanced_option['swatch_image'],
					'show_first_option_in_list' => is_null($advanced_option['show_first_option_in_list']) ? 0 : $advanced_option['show_first_option_in_list']
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getSelectOptionsForm() {

		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->load->model('catalog/option');

		$data['options'] = array();

		$filter_data = array(
			'sort'  => 'o.sort_order',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $this->model_catalog_option->getTotalOptions()
		);

		$results = $this->model_catalog_option->getOptions($filter_data);

		foreach ($results as $result) {
			if ($result['type'] != 'radio' && $result['type'] != 'select') {
				continue;
			}

			$option_values = array();

			foreach ($this->model_catalog_option->getOptionValues($result['option_id']) as $option_value) {
				$option_values[] = $option_value["name"];
			}

			$data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'       => $result['name'],
				'values' => implode(", ", $option_values),
			);
		}

		if (isset($this->request->post['option_children'])) {
			$data['selected'] = explode(",", $this->request->post['option_children']);
		} else {
			$data['selected'] = array();
		}

        $data['_'] = $this->_;

		$this->response->setOutput($this->load->view('extension/may/advanced_options/template/form/select_options', $data));		
	}

	public function getOptionValuesForm() {

		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->load->model('catalog/option');


		$selected_options = array();

		if (isset($this->request->post['option_values']) &&
			$this->request->post['option_values'] != "") {
			$option_info = explode(":", $this->request->post['option_values']);
			$data['selected'] = explode(",", $option_info[1]);

			$selected_options = explode(",", $option_info[0]);
		} else {
			$data['selected'] = array();

			if (isset($this->request->post['selected'])) {
				$selected_options = $this->request->post['selected'];
			}
		}

		$data['options'] = array();

		foreach ($selected_options as $selected_option) {

			$result =  $this->model_catalog_option->getOption($selected_option);

			if (count($result) == 0) {
				continue;
			}

			$data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'       => $result['name'],
				'values' => $this->model_catalog_option->getOptionValues($result['option_id']),
			);
		}

        $data['_'] = $this->_;

		$this->response->setOutput($this->load->view('extension/may/advanced_options/template/form/option_values', $data));		
	}

	public function getOptionChildrenForm() {

		$this->load->language('extension/may/advanced_options');
        $this->_ = $this->language->get('advanced_options');

		$this->load->model('catalog/option');

		if (isset($this->request->post['option_combinations'])) {
			$data['combinations'] = explode(',', $this->request->post['option_combinations']);
		}

		$option_values = array();

		if (isset($this->request->post['option_values'])) {
			$option_values = $this->request->post['option_values'];
		}

		$data['options'] = array();

		foreach ($option_values as $option_id => $option_value) {

			$result =  $this->model_catalog_option->getOption($option_id);

			if (count($result) == 0) {
				continue;
			}

			$values = array();
			foreach ($this->model_catalog_option->getOptionValues($result['option_id']) as $child) {
				if (in_array($child['option_value_id'], $option_value)) {
					$values[] = $child;
				}
			}


			if (count($data['options']) > 0) {
				$data['options'][count($data['options']) - 1]['cname'] = $result['name'];
				$data['options'][count($data['options']) - 1]['children'] = $values;
			}

			$data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'       => $result['name'],
				'values' => $values,
				'cname' => '',
				'children' => array()
			);
		}

        $data['_'] = $this->_;

		$this->response->setOutput($this->load->view('extension/may/advanced_options/template/form/option_children', $data));		
	}

}
