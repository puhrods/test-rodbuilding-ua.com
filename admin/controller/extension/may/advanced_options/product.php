<?php
class ControllerExtensionMayAdvancedOptionsProduct extends Controller {
	public function getCombineOptionsModal() {

		$this->load->language('extension/may/advanced_options');

		$this->load->model('catalog/option');


		$product_options = $this->request->post;

		$data['mode'] = count($product_options) ? 'edit' : 'new';

		$selected = array(
			'options' => array(),
			'values' => array(),
			'combinations' => array()
		);

		foreach ($product_options as $product_option) {
			$selected['options'][] = $product_option['option_id'];

			$info = explode(':::', $product_option['value']);
			$info = explode('-', isset($info[1]) ? $info[1] : '');
			foreach ($product_option['product_option_value'] as $option_value) {
				$selected['values'][] = $option_value['option_value_id'];

				if (count($info) > 1) {
					$parent_option_value_id = end($info);
					$selected['combinations'][] = $parent_option_value_id . '-' . $option_value['option_value_id'];
				}
			}
		}

		foreach ($selected as &$tmp) {
			$tmp = array_values(array_unique($tmp));
		}

		$data['selected'] = $selected;

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
				$option_values[] = array(
					'option_value_id' => $option_value['option_value_id'],
					'name'       	  => $option_value["name"]
				);
			}

			$data['options'][] = array(
				'option_id'  => $result['option_id'],
				'name'       => $result['name'],
				'values' => $option_values,
			);
		}

        $data['_'] = $this->language->get('advanced_options');

		$data['may_advanced_options_config'] = array(
			'swatch_image' => $this->config->get('may_advanced_options_swatch_image'),
			'show_first_option_in_list' => $this->config->get('may_advanced_options_show_first_option_in_list'),
		);

		$this->response->setOutput($this->load->view('extension/may/advanced_options/product/modal', $data));
	}

	public function getCombinedOptions() {
		$product_id = $this->request->get['product_id'];

		$this->load->model('catalog/product');
		$this->load->library('may/may_advanced_options');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		$data = array(
			'product_id' => $product_id,
			'options' => $this->model_catalog_product->getProductOptions($product_id),
			'currency' => $this->may_advanced_options->getCurrencyInfo($this->config->get('config_currency')),
			'weight' => $this->weight->format((float)$product_info['weight'], $product_info['weight_class_id']),
			'weight_value' => (float)$product_info['weight'],
			'weight_unit' => $this->weight->getUnit($product_info['weight_class_id']),
			'dimension_l' => (float)$product_info['length'],
			'dimension_w' => (float)$product_info['width'],
			'dimension_h' => (float)$product_info['height'],
			'length_unit' => $this->length->getUnit($product_info['length_class_id']),
		);

		foreach ($data['options'] as &$option) {
			foreach ($option['product_option_value'] as $option_value_index => &$option_value) {
				$product_option_value = $this->model_catalog_product->getProductOptionValue($product_id, $option_value['product_option_value_id']);

				if ($product_option_value['subtract'] && $product_option_value['quantity'] <= 0) {
					unset($option['product_option_value'][$option_value_index]);
				} else {
					$option_value['name'] = $product_option_value['name'];
				}
			}
		}

		$tmp = $this->config->get('config_theme');
		$this->config->set('config_theme', 'default');
		$data = $this->may_advanced_options->loadAdvancedOptions($data, true);
		$this->config->set('config_theme', $tmp);
		
		if (isset($data['may_advanced_options'])) {
			$data['may_advanced_options_config'] = $this->may_advanced_options->getExtensionConfig();
			$data['may_advanced_options_template'] = array(
				'hidden' => $this->load->view('extension/may/advanced_options/common/hidden', $data),
				'options' => $this->load->view('extension/may/advanced_options/common/options', $data),
				'js_helper' => $this->load->view('extension/may/advanced_options/common/js_helper', $data),
				'js_plugin' => $this->load->view('extension/may/advanced_options/common/js_plugin', $data),
				'style' => $this->load->view('extension/may/advanced_options/common/style', $data),
			);

			$this->response->setOutput(implode('', $data['may_advanced_options_template']));
		}		
	}
}
