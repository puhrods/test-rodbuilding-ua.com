<?php
class ControllerExtensionMayAdvancedOptions extends Controller {
	public function mUserUserGroupEditUserGroupBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (count($args) < 2) {
			return;
		}

		$args[1]['permission']['access'][] = 'extension/may';
		$args[1]['permission']['modify'][] = 'extension/may';
	}

	public function mCatalogProductAddProductAfter($route, $args, $product_id) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!$product_id || !isset($args[0])) {
			return;
		}

		$this->load->model('extension/may/advanced_options/product');
		$this->model_extension_may_advanced_options_product->setProductAdvancedOptions($product_id, $args[0]);
	}

	public function mCatalogProductEditProductAfter($route, $args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (count($args) < 2 || !$args[0]) {
			return;
		}

		$this->load->model('extension/may/advanced_options/product');
		$this->model_extension_may_advanced_options_product->setProductAdvancedOptions($args[0], $args[1]);
	}

	public function mCatalogProductDeleteProductAfter($route, $args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!$args[0]) {
			return;
		}

		$this->load->model('extension/may/advanced_options/product');
		$this->model_extension_may_advanced_options_product->deleteProductAdvancedOptions($args[0]);
	}

	public function mCatalogProductCopyProductAfter($route, $args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!$args[0]) {
			return;
		}

		$this->load->model('catalog/product');
		$source = $this->model_catalog_product->getProduct($args[0]);
		$options = $this->model_catalog_product->getProductOptions($args[0]);

		$this->load->model('extension/may/advanced_options/product');
		$this->model_extension_may_advanced_options_product->copyProductAdvancedOptions($source, $options);
	}

	public function mCatalogProductGetProductAfter($route, $args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->library('may/may_advanced_options');
		$this->may_advanced_options->adjustTag($output);
	}

	public function mCatalogProductGetProductDescriptionsAfter($route, $args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->library('may/may_advanced_options');
		$this->may_advanced_options->adjustTags($output);
	}

	public function mCatalogProductGetProductsAfter($route, $args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->library('may/may_advanced_options');
		$this->may_advanced_options->adjustTags($output);
	}

	public function mCatalogOptionDeleteOptionAfter($route, $args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->model('extension/may/advanced_options/template');
		$this->model_extension_may_advanced_options_template->deleteOptionByChild($args[0]);
	}

	public function mCatalogOptionDeleteOptionBefore($route, $args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (count($args) < 2 || !$args[0]) {
			return;
		}

		$child_option_value_ids_new = array();
		foreach ($args[1]['option_value'] as $child_option_value_new) {
			$child_option_value_ids_new[] = $child_option_value_new['option_value_id'];
		}

		$this->load->model('catalog/option');

		$child_option_values_old = $this->model_catalog_option->getOptionValues($args[0]);
		$child_option_value_ids_old = array();
		foreach ($child_option_values_old as $child_option_value_old) {
			if (!in_array($child_option_value_old['option_value_id'], $child_option_value_ids_new)) {
				$child_option_value_ids_old[] = $child_option_value_old['option_value_id'];
			}
		}

		if (!count($child_option_value_ids_old)) {
			return;
		}

		$this->load->model('extension/may/advanced_options/template');

		$options = $this->model_extension_may_advanced_options_template->getOptionsByChild($args[0]);
		foreach ($options as $option) {
			$this->model_extension_may_advanced_options_template->deleteChildOptionValues($option, $child_option_value_ids_old);
		}

		$this->model_extension_may_advanced_options_template->deleteChildOptionValues(['option_id' => 0], $child_option_value_ids_old);
	}

	public function vCommonColumnLeftBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!$this->user->hasPermission('access', 'extension/may/advanced_options/template')) {
			return;
		}

		foreach ($data['menus'] as $menu_key => $menu) {
			if ($menu['id'] != 'menu-catalog') {
				continue;
			}

			$new_catalog = array();
			foreach ($menu['children'] as $submenu) {
				$new_catalog[] = $submenu;
				if (isset($submenu['href']) && $submenu['href'] == $this->url->link('catalog/option', 'user_token=' . $this->session->data['user_token'], true)) {
					$this->load->language('extension/may/advanced_options');
					$_ = $this->language->get('advanced_options');
					$new_catalog[] = array(
						'name'	   => $_['Combined Options'],
						'href'     => $this->url->link('extension/may/advanced_options/template', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()		
					);
				}
			}

			$data['menus'][$menu_key]['children'] = $new_catalog;
			break;
		}
	}

	public function vCatalogProductFormBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status') ||
			(isset($this->request->get['type']) && $this->request->get['type'] != 'variable')) {
			return;
		}

		$this->load->model('tool/image');
		$this->load->library('may/may_advanced_options');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			uasort($data['product_options'], function($a, $b) {
				return ($a['product_option_id'] < $b['product_option_id']) ? -1 : 1;
			});
		}

		$may_advanced_options = array();

		$product_advanced_option_config = array();
		$product_advanced_option_values = array();

		if (isset($this->request->get['product_id'])) {
			$product_advanced_option_config = $this->may_advanced_options->getProductAdvancedOptionConfig($this->request->get['product_id']);
			$product_advanced_option_values = $this->may_advanced_options->getProductAdvancedOptionValues($this->request->get['product_id'], true);
		}

		if (count($product_advanced_option_config) && count($product_advanced_option_values)) {
			$option_row = 0;
			$root_option_combination_id = "";

			foreach ($data['product_options'] as $product_option_key => $product_option) {
				if ($product_option['value'] != 'may_advanced_option' || !isset($product_advanced_option_values[$product_option['product_option_id']])) {
					$option_row ++;
					continue;
				}

				$product_advanced_option_value = $product_advanced_option_values[$product_option['product_option_id']];

				if ($root_option_combination_id == '' || strpos($product_advanced_option_value['combination_id'], $root_option_combination_id . '-') !== 0) {
					$root_option_combination_id = $product_advanced_option_value['combination_id'];
					$may_advanced_options[] = array(
						'name' => $product_advanced_option_config['option_name'],
						'type' => 'may_advanced_option',
						'required' => $product_option['required'],
						'subtract' => $product_advanced_option_config['subtract'],
						'after' => $option_row - 1,
						'options' => array()
					);
				}

				$product_option['value'] = $product_advanced_option_config['option_name'] . ':::' . $product_advanced_option_value['combination_id'];

				if (count($product_advanced_option_value['values'])) {
					foreach ($product_option['product_option_value'] as $index => $option_value) {
						if (!isset($product_advanced_option_value['values'][$option_value['option_value_id']])) {
							continue;
						}

						$product_option['product_option_value'][$index] = array_merge($product_option['product_option_value'][$index], $product_advanced_option_value['values'][$option_value['option_value_id']]);

						$option_value_images = array();
						foreach ($product_advanced_option_value['values'][$option_value['option_value_id']]['image'] as $option_value_image) {
							if (!is_file(DIR_IMAGE . $option_value_image)) {
								continue;
							}

							$option_value_images[] = array(
								'image' => $option_value_image,
								'thumb' => $this->model_tool_image->resize($option_value_image, 100, 100)
							);
						}
						$option_value_images[] = array('thumb' => 'no_image.png', 'image' => '');
						$product_option['product_option_value'][$index]['image'] = $option_value_images;
					}

					$product_option['swatch_image'] = isset($product_advanced_option_config['swatch_image']) ? $product_advanced_option_config['swatch_image'] :  $this->config->get('may_advanced_options_swatch_image');
					$product_option['show_first_option_in_list'] = isset($product_advanced_option_config['show_first_option_in_list']) ? $product_advanced_option_config['show_first_option_in_list'] :  $this->config->get('may_advanced_options_show_first_option_in_list');

					$may_advanced_options[count($may_advanced_options) - 1]['options'][(string)$product_advanced_option_value['combination_id']] = $product_option;
				}

				unset($data['product_options'][$product_option_key]);
			}

			foreach ($may_advanced_options as $may_option_key => $may_option) {
				$may_option_count = 0;
				$map_option_index = array();
				$map_option_index_no = 0;
				foreach ($may_option['options'] as $may_option_item_key => $may_option_item) {
					$may_option_count += count($may_option_item['product_option_value']);
					$map_option_index[$may_option_item_key] = $map_option_index_no ++;
				}

				$may_option_value_tree = array();
				$may_option_depth = 0;
				while (count($may_option_value_tree) < $may_option_count) {
					if ($may_option_depth == 0) {
						$may_option_item = current($may_option['options']);
						$may_option_item_key = key($may_option['options']);
						foreach ($may_option_item['product_option_value'] as $may_option_item_value) {
							$option_value_name = "";
							foreach ($data['option_values'][$may_option_item['option_id']] as $option_value) {
								if ($option_value['option_value_id'] == $may_option_item_value['option_value_id']) {
									$option_value_name = $option_value['name'];
								}
							}

							$may_option_value_tree[] = array(
								'key' => $may_option_item_key,
								'name' => $option_value_name,
								'depth' => $may_option_depth,
								'value' => $may_option_item_value,
								'index' => $map_option_index[$may_option_item_key],
								'parent_sibling_index' => array(),
								'is_last_sibling' => 0,
								'tooltip' => array($may_option_item['name'] . ' : ' . $option_value_name),
								'row_key' => array(strtolower($option_value_name))
							);
						}

						$may_option_value_tree[count($may_option_value_tree) - 1]['is_last_sibling'] = 1;
					} else {
						$may_option_value_tree_temp = array();
						foreach ($may_option_value_tree as $tree_item) {
							$may_option_value_tree_temp[] = $tree_item;

							if ($tree_item['depth'] != $may_option_depth - 1) {
								continue;
							}

							$may_option_item_key = $tree_item['key'] . '-' . $tree_item['value']['option_value_id'];
							if (!array_key_exists($may_option_item_key, $may_option['options'])) {
								continue;
							}

							$may_option_item = $may_option['options'][$may_option_item_key];
							foreach ($may_option_item['product_option_value'] as $may_option_item_value) {
								$option_value_name = "";
								foreach ($data['option_values'][$may_option_item['option_id']] as $option_value) {
									if ($option_value['option_value_id'] == $may_option_item_value['option_value_id']) {
										$option_value_name = $option_value['name'];
									}
								}
								$may_option_value_tree_temp[] = array(
									'key' => $may_option_item_key,
									'name' => $option_value_name,
									'depth' => $may_option_depth,
									'value' => $may_option_item_value,
									'index' => $map_option_index[$may_option_item_key],
									'parent_sibling_index' => array_merge($tree_item['parent_sibling_index'], array($tree_item['is_last_sibling'])),
									'is_last_sibling' => 0,
									'tooltip' => array_merge($tree_item['tooltip'], array($may_option_item['name'] . ' : ' . $option_value_name)),
									'row_key' => array_merge($tree_item['row_key'], array(strtolower($option_value_name)))
								);
							}

							$may_option_value_tree_temp[count($may_option_value_tree_temp) - 1]['is_last_sibling'] = 1;
						}

						$may_option_value_tree = $may_option_value_tree_temp;
					}

					$may_option_depth ++;
				}

				$may_advanced_options[$may_option_key]['option_value_tree'] = $may_option_value_tree;
				$may_advanced_options[$may_option_key]['option_value_tree_depth'] = $may_option_depth;
			}

			foreach ($may_advanced_options as $may_option_key => $may_option) {
				$options = array();
				$index = 10000;

				foreach ($may_option['options'] as $option_key => $option) {
					$option['name'] = $may_option['name'];
					$option['type'] = 'may_advanced_option';
					$option_new = $option;
					$option_new['product_option_value'] = array();
					foreach ($option['product_option_value'] as $product_option_value) {
						foreach ($may_option['option_value_tree'] as $tree_index => $tree_item) {
							if ($tree_item['key'] === $option_key && 
								$product_option_value['product_option_value_id'] === $tree_item['value']['product_option_value_id']) {
								$option_new['product_option_value'][$tree_index] = $product_option_value;
							}
						}
					}
					$options[$index ++] = $option_new;
				}

				$may_advanced_options[$may_option_key]['options'] = $options;
			}
		}

		$data['may_advanced_options'] = $may_advanced_options;

		$data['may_advanced_options_config'] = array(
			'attribute_model' => $this->config->get('may_advanced_options_attribute_model'),
			'attribute_sku' => $this->config->get('may_advanced_options_attribute_sku'),
			'attribute_upc' => $this->config->get('may_advanced_options_attribute_upc'),
			'attribute_ean' => $this->config->get('may_advanced_options_attribute_ean'),
			'attribute_jan' => $this->config->get('may_advanced_options_attribute_jan'),
			'attribute_isbn' => $this->config->get('may_advanced_options_attribute_isbn'),
			'attribute_mpn' => $this->config->get('may_advanced_options_attribute_mpn'),
			'attribute_location' => $this->config->get('may_advanced_options_attribute_location'),
			'attribute_dimension' => $this->config->get('may_advanced_options_attribute_dimension'),
			'attribute_quantity' => $this->config->get('may_advanced_options_attribute_quantity'),
			'attribute_stock_status' => $this->config->get('may_advanced_options_attribute_stock_status'),
			'attribute_price' => $this->config->get('may_advanced_options_attribute_price'),
			'attribute_point' => $this->config->get('may_advanced_options_attribute_point'),
			'attribute_weight' => $this->config->get('may_advanced_options_attribute_weight'),	
		);

		$this->load->language('extension/may/advanced_options');
		$data['_'] = $this->language->get('advanced_options');

		$data['url_combine_options_modal'] = $this->url->link('extension/may/advanced_options/product/getCombineOptionsModal', 'user_token=' . $this->session->data['user_token'], true);

		$data['footer'] = $this->load->view('extension/may/advanced_options/product/form', $data) . $data['footer'];
	}

	public function vSaleOrderFormBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['footer'] = $this->load->view('extension/may/advanced_options/sale/order_form', $data) . $data['footer'];
	}

	public function vSaleOrderInfoBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->language('extension/may/advanced_options');
		$_ = $this->language->get('advanced_options');
		$data['column_model'] = $_['Reference'];
	}

	public function vSaleOrderInvoiceBefore($route, &$data) {
		$this->vSaleOrderInfoBefore($route, $data);
	}

	public function vSaleOrderShippingBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->model('sale/order');
		$this->load->library('may/may_advanced_options');

		foreach ($data['orders'] as $index => &$order) {
			$products = $this->model_sale_order->getOrderProducts($order['order_id']);

			foreach ($products as $index => $product) {
				$options = $this->model_sale_order->getOrderOptions($order['order_id'], $product['order_product_id']);
				$product_option_value_ids = [];
				foreach ($options as $option) {
					$product_option_value_ids[] = $option['product_option_value_id'];
				}

				if ($product_option_value_ids) {
					$order_product_option_data = $this->may_advanced_options->getSelectedProductAdvancedOptionValues($product_option_value_ids);

					if (isset($order_product_option_data['model']) && !empty($order_product_option_data['model'])) {
						$order['product'][$index]['model'] = $order_product_option_data['model'];
					}
					if (isset($order_product_option_data['sku']) && !empty($order_product_option_data['sku'])) {
						$order['product'][$index]['sku'] = $order_product_option_data['sku'];
					}
					if (isset($order_product_option_data['upc']) && !empty($order_product_option_data['upc'])) {
						$order['product'][$index]['upc'] = $order_product_option_data['upc'];
					}
					if (isset($order_product_option_data['ean']) && !empty($order_product_option_data['ean'])) {
						$order['product'][$index]['ean'] = $order_product_option_data['ean'];
					}
					if (isset($order_product_option_data['jan']) && !empty($order_product_option_data['jan'])) {
						$order['product'][$index]['jan'] = $order_product_option_data['jan'];
					}
					if (isset($order_product_option_data['isbn']) && !empty($order_product_option_data['isbn'])) {
						$order['product'][$index]['isbn'] = $order_product_option_data['isbn'];
					}
					if (isset($order_product_option_data['mpn']) && !empty($order_product_option_data['mpn'])) {
						$order['product'][$index]['mpn'] = $order_product_option_data['mpn'];
					}
					if (isset($order_product_option_data['location']) && !empty($order_product_option_data['location'])) {
						$order['product'][$index]['location'] = $order_product_option_data['location'];
					}
				}
			}
		}
	}
}
