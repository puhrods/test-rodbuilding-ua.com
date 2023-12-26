<?php
class ControllerExtensionMayAdvancedOptions extends Controller {
	protected function getProductAdvancedOptionsReady(&$data) {
		$this->load->library('may/may_advanced_options');
		$data = $this->may_advanced_options->loadAdvancedOptions($data);
		$data['currency'] = $this->may_advanced_options->getCurrencyInfo();

		if (!isset($data['may_advanced_options_config'])) {
			$data['may_advanced_options_config'] = $this->may_advanced_options->getExtensionConfig($data);
		}

		$this->load->language('extension/may/advanced_options');
		$data['may_advanced_options_language'] = array(
			'text_sku' => $this->language->get('text_sku'),
			'text_upc' => $this->language->get('text_upc'),
			'text_ean' => $this->language->get('text_ean'),
			'text_jan' => $this->language->get('text_jan'),
			'text_isbn' => $this->language->get('text_isbn'),
			'text_mpn' => $this->language->get('text_mpn'),
			'text_location' => $this->language->get('text_location'),
			'text_weight' => $this->language->get('text_weight'),
			'text_dimension' => $this->language->get('text_dimension'),
			'text_instock' => $this->language->get('text_instock'),
			'error_option_stock' => $this->language->get('error_option_stock'),
		);

		$data['config_stock_display'] = $this->config->get('config_stock_display');

		if (in_array($data['may_advanced_options_config']['theme'], [
			'journal3', 
			'basel', 
			'wokiee', 
			'zeexo', 
			'so-emarket',
			'so-revo',
			'so-claue',
			'so-myshop',
			'so-bestshop',
			'so-shoppystore',
			'BurnEngine',
			'fastor',
			'yoga',
			'oct_ultrastore',
			'oct_feelmart',
			'oct_remarket',
			'mahardhi',
			'cyberstore',
			'furelife',
		])) {
			$view_template = 'product/' . strtolower($data['may_advanced_options_config']['theme']);
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_truemart') !== false) {
			$view_template = 'product/truemart';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_mimosa') !== false) {
			$view_template = 'product/mimosa';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_pander') !== false) {
			$view_template = 'product/pander';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_sinrato') !== false) {
			$view_template = 'product/sinrato';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_gicor') !== false) {
			$view_template = 'product/gicor';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_debaco') !== false) {
			$view_template = 'product/debaco';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_drama') !== false) {
			$view_template = 'product/drama';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_madina') !== false) {
			$view_template = 'product/madina';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'kenza') !== false) {
			$view_template = 'product/kenza';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'zemez') !== false) {
			$view_template = 'product/zemez';
		} else {
			$view_template = 'product';
		}
		
		$data['view_template'] = $view_template;

		if ($product_info = $this->cache->get('may_advanced_option_product_' . $data['product_id'])) {
			if (!isset($data['weight'])) {
				$data['weight'] = $this->weight->format((float)$product_info['weight'], $product_info['weight_class_id']);
			}
			$data['weight_value'] = (float)$product_info['weight'];
			$data['weight_unit'] = $this->weight->getUnit($product_info['weight_class_id']);

			$data['dimension_l'] = (float)$product_info['length'];
			$data['dimension_w'] = (float)$product_info['width'];
			$data['dimension_h'] = (float)$product_info['height'];
			$data['length_unit'] = $this->length->getUnit($product_info['length_class_id']);

			$this->cache->delete('may_advanced_option_product_' . $data['product_id']);
		}

		if (isset($data['may_advanced_options']) && !empty($data['may_advanced_options'])) {
			$may_advanced_options = $data['may_advanced_options'];
			$preselected_option_name = str_replace(' ', '_', strtolower($may_advanced_options[0]['name']));
			if (isset($this->request->get[$preselected_option_name])) {
				$preselected_option_value = $this->request->get[$preselected_option_name];
				foreach ($may_advanced_options[0]['product_option_value'] as $option_value) {
					if ($preselected_option_value == strtolower($option_value['name'])) {
						$data['preselected_option_value_id'] = $option_value['option_value_id'];
						break;
					}
				}
			}
		}

		$data['may_advanced_options_template'] = array(
			'hidden' => $this->load->view('extension/may/advanced_options/product/common/hidden', $data),
			'js_helper' => $this->load->view('extension/may/advanced_options/product/common/js_helper', $data),
			'js_plugin' => $this->load->view('extension/may/advanced_options/product/common/js_plugin', $data),
			'options' => $this->load->view('extension/may/advanced_options/product/common/options', $data),
			'style' => $this->load->view('extension/may/advanced_options/product/common/style', $data),
		);
	}

	public function vProductProductBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['options'])) {
			return;
		}

		$this->getProductAdvancedOptionsReady($data);

		$this->vProductCategoryBefore($route, $data, false, array(
			'width' =>  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'),
			'height' =>  $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height')
		));
		$data['footer'] = $this->load->view('extension/may/advanced_options/product/' . $data['view_template'], $data) . $data['footer'];
	}

	public function vProductQuickviewBefore(&$route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['options'])) {
			return;
		}

		$this->getProductAdvancedOptionsReady($data);
	}

	public function vProductQuickviewAfter(&$route, &$data, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['is_quick_view'] = true;

		$output .= $this->load->view('extension/may/advanced_options/product/' . $data['view_template'], $data);
	}

	public function vProductCyberGalleryBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$product_option_value_ids = array();
		if (isset($this->request->post['option'])) {
			foreach ($this->request->post['option'] as $option_key => $option_value) {
				if (is_numeric($option_key) && $option_value != -1 && isset($this->request->post['may_advanced_option_' . $option_key])) {
					$product_option_value_ids[] = $option_value;
				}
			}
		}

		if (!count($product_option_value_ids)) {
			return;
		}		

		if (!$data['slickToGo']) {
			$data['slickToGo'] = $this->request->get['slickToGo'];
		}

		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($data['product_id']);

		$this->load->library('may/may_advanced_options');
		$option_value_data = $this->may_advanced_options->getSelectedProductAdvancedOptionValues($product_option_value_ids);

		if ($option_value_data) {
			$data['model'] = $option_value_data['model'];
			$data['sku'] = $option_value_data['sku'];

			if (count($option_value_data['image'])) {
				$this->load->model('tool/image');

				$data['popup'] = $this->model_tool_image->resize($option_value_data['image'][0], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				$data['thumb'] = $this->model_tool_image->resize($option_value_data['image'][0], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
				$data['thumb_min'] = $this->model_tool_image->resize($option_value_data['image'][0], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));

				$data['gallery_images'] = array();

				foreach ($option_value_data['image'] as $image_index => $image) {
					if (!$image_index) {
						continue;
					}

					$data['gallery_images'][] = array(
						'popup' => $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
						'thumb' => $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'))
					);
				}
			}

			$price = $product_info['price'];
			$special = false;

			if ($option_value_data['price_prefix'] == 'as' && (float)$option_value_data['price']) {
				$price = $option_value_data['price'];
			} else if ($option_value_data['price_prefix'] == '-') {
				$price -= $option_value_data['price'];
			} else {
				$price += $option_value_data['price'];
			}

			$data['price'] = $this->currency->format($this->tax->calculate($price, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

			if ($product_info['special']) {
				$special = $product_info['special'] + ($price - $product_info['price']);
				$data['special'] = $this->currency->format($this->tax->calculate($special, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				if ($this->config->get('config_tax')) {
					$data['tax'] = $this->currency->format($special, $this->session->data['currency']);
				} else {
					$data['tax'] = false;
				}
			} else {
				$data['special'] = false;
				if ($this->config->get('config_tax')) {
					$data['tax'] = $this->currency->format($price, $this->session->data['currency']);
				} else {
					$data['tax'] = false;
				}
			}
		}
	}

	public function vOctModulePopupCartBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['products'])) {
			return;
		}

		foreach ($data['products'] as &$product) {
			$product['cart_id'] = $product['key'];
		}

		$this->vCommonCartBefore($route, $data);
	}

	public function vCommonCartBefore($route, &$data) {

		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['products'])) {
			return;
		}

		if (isset($this->session->data['may_advanced_options_config'])) {
			$may_advanced_options_config = $this->session->data['may_advanced_options_config'];
		} else {
			$may_advanced_options_config = array(
				'hide_cc_sku' => $this->config->get('may_advanced_options_hide_cc_sku'),
				'hide_cc_upc' => $this->config->get('may_advanced_options_hide_cc_upc'),
				'hide_cc_ean' => $this->config->get('may_advanced_options_hide_cc_ean'),
				'hide_cc_jan' => $this->config->get('may_advanced_options_hide_cc_jan'),
				'hide_cc_isbn' => $this->config->get('may_advanced_options_hide_cc_isbn'),
				'hide_cc_mpn' => $this->config->get('may_advanced_options_hide_cc_mpn'),
				'hide_cc_location' => $this->config->get('may_advanced_options_hide_cc_location'),
				'hide_cc_dimension' => $this->config->get('may_advanced_options_hide_cc_dimension'),
			);	
		}

		$this->load->model('extension/may/advanced_options');
		$cart_product_data = $this->model_extension_may_advanced_options->getCartProductVariableData();
		$cart_product_models = $cart_product_data['models'];
		$cart_product_skus = $cart_product_data['skus'];
		$cart_product_upcs = $cart_product_data['upcs'];
		$cart_product_eans = $cart_product_data['eans'];
		$cart_product_jans = $cart_product_data['jans'];
		$cart_product_isbns = $cart_product_data['isbns'];
		$cart_product_mpns = $cart_product_data['mpns'];
		$cart_product_locations = $cart_product_data['locations'];
		$cart_product_dimension_ls = $cart_product_data['dimension_ls'];
		$cart_product_dimension_ws = $cart_product_data['dimension_ws'];
		$cart_product_dimension_hs = $cart_product_data['dimension_hs'];
		$cart_product_length_units = $cart_product_data['length_units'];
		$cart_product_weights = $cart_product_data['weights'];
		$cart_product_weight_units = $cart_product_data['weight_units'];
		$cart_product_images = $cart_product_data['images'];

		$this->load->model('tool/image');
		$this->load->language('extension/may/advanced_options');

		$data['column_model'] = $this->language->get('text_product_details');

		$show_attribute_label = $this->config->get('may_advanced_options_show_attribute_label');

		foreach ($data['products'] as $key => $product) {
			$details = [];
			if (isset($cart_product_models[$product['cart_id']]) && !empty($cart_product_models[$product['cart_id']])) {
				$details[] = ($show_attribute_label ? $this->language->get('text_model') . ': ' : '') . $cart_product_models[$product['cart_id']];
			} else {
				$details[] = ($show_attribute_label ? $this->language->get('text_model') . ': ' : '') . $data['products'][$key]['model'];
			}
			if (isset($cart_product_skus[$product['cart_id']]) && !empty($cart_product_skus[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_sku']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_sku') . ': ' : '') . $cart_product_skus[$product['cart_id']];
			}
			if (isset($cart_product_upcs[$product['cart_id']]) && !empty($cart_product_upcs[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_upc']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_upc') . ': ' : '') . $cart_product_upcs[$product['cart_id']];
			}
			if (isset($cart_product_eans[$product['cart_id']]) && !empty($cart_product_eans[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_ean']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_ean') . ': ' : '') . $cart_product_eans[$product['cart_id']];
			}
			if (isset($cart_product_jans[$product['cart_id']]) && !empty($cart_product_jans[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_jan']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_jan') . ': ' : '') . $cart_product_jans[$product['cart_id']];
			}
			if (isset($cart_product_isbns[$product['cart_id']]) && !empty($cart_product_isbns[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_isbn']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_isbn') . ': ' : '') . $cart_product_isbns[$product['cart_id']];
			}
			if (isset($cart_product_mpns[$product['cart_id']]) && !empty($cart_product_mpns[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_mpn']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_mpn') . ': ' : '') . $cart_product_mpns[$product['cart_id']];
			}
			if (isset($cart_product_locations[$product['cart_id']]) && !empty($cart_product_locations[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_location']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_location') . ': ' : '') . $cart_product_locations[$product['cart_id']];
			}
			if (isset($cart_product_dimension_ls[$product['cart_id']]) && !empty($cart_product_dimension_ls[$product['cart_id']]) && !$may_advanced_options_config['hide_cc_dimension']) {
				$length_unit = isset($cart_product_length_units[$product['cart_id']]) ? $cart_product_length_units[$product['cart_id']] : '';

				$dimension = $cart_product_dimension_ls[$product['cart_id']] . $length_unit;

				if (isset($cart_product_dimension_ws[$product['cart_id']]) && !empty($cart_product_dimension_ws[$product['cart_id']])) {
					$dimension .= ' x ' . $cart_product_dimension_ws[$product['cart_id']] . $length_unit;
				}
				if (isset($cart_product_dimension_hs[$product['cart_id']]) && !empty($cart_product_dimension_hs[$product['cart_id']])) {
					$dimension .= ' x ' . $cart_product_dimension_hs[$product['cart_id']] . $length_unit;
				}
				$details[] = ($show_attribute_label ? $this->language->get('text_dimension') . ': ' : '') . $dimension;
			}
			if (isset($cart_product_weights[$product['cart_id']]) && !empty($cart_product_weights[$product['cart_id']]) && $cart_product_weights[$product['cart_id']] > 0) {
				$details[] = ($show_attribute_label ? $this->language->get('text_weight') . ': ' : '') . $cart_product_weights[$product['cart_id']] . (isset($cart_product_weight_units[$product['cart_id']]) ? $cart_product_weight_units[$product['cart_id']] : "");
			}

			$data['products'][$key]['model'] = implode('<br/>', $details);

			if (isset($cart_product_images[$product['cart_id']])) {
				$data['products'][$key]['thumb'] = $this->model_tool_image->resize($cart_product_images[$product['cart_id']], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height'));
				$data['products'][$key]['thumb2x'] = $this->model_tool_image->resize($cart_product_images[$product['cart_id']], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_width') * 2, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_cart_height') * 2);
			}
		}
	}

	public function vCheckoutCartBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->vCommonCartBefore($route, $data);
	}

	public function mCheckoutOrderAddOrderBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->library('may/may_advanced_options');
		$this->load->model('catalog/product');
		$this->load->language('extension/may/advanced_options');

		$may_advanced_options_config = array(
			'hide_oi_sku' => $this->config->get('may_advanced_options_hide_oi_sku'),
			'hide_oi_upc' => $this->config->get('may_advanced_options_hide_oi_upc'),
			'hide_oi_ean' => $this->config->get('may_advanced_options_hide_oi_ean'),
			'hide_oi_jan' => $this->config->get('may_advanced_options_hide_oi_jan'),
			'hide_oi_isbn' => $this->config->get('may_advanced_options_hide_oi_isbn'),
			'hide_oi_mpn' => $this->config->get('may_advanced_options_hide_oi_mpn'),
			'hide_oi_location' => $this->config->get('may_advanced_options_hide_oi_location'),
			'hide_oi_dimension' => $this->config->get('may_advanced_options_hide_oi_dimension'),
		);	

		foreach ($args[0]['products'] as $key => $product) {
			$product_option_value_ids = array();
			foreach ($product['option'] as $option) {
				if (($option['type'] == 'select' || $option['type'] == 'radio') && $option['value'] != '') {
					$product_option_value_ids[] = $option['product_option_value_id'];
				}
			}

			$option_value_data = array();
			if ($product_option_value_ids) {
				$option_value_data = $this->may_advanced_options->getSelectedProductAdvancedOptionValues($product_option_value_ids);
			}

			$show_attribute_label = $this->config->get('may_advanced_options_show_attribute_label');

			$details = array();
			if (isset($option_value_data['model']) && !empty($option_value_data['model'])) {
				$details[] = ($show_attribute_label ? $this->language->get('text_model') . ': ' : '') . $option_value_data['model'];
			} else {
				$details[] = ($show_attribute_label ? $this->language->get('text_model') . ': ' : '') . $args[0]['products'][$key]['model'];
			}
			if (isset($option_value_data['sku']) && !empty($option_value_data['sku']) && !$may_advanced_options_config['hide_oi_sku']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_sku') . ': ' : '') . $option_value_data['sku'];
			}
			if (isset($option_value_data['upc']) && !empty($option_value_data['upc']) && !$may_advanced_options_config['hide_oi_upc']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_upc') . ': ' : '') . $option_value_data['upc'];
			}
			if (isset($option_value_data['ean']) && !empty($option_value_data['ean']) && !$may_advanced_options_config['hide_oi_ean']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_ean') . ': ' : '') . $option_value_data['ean'];
			}
			if (isset($option_value_data['jan']) && !empty($option_value_data['jan']) && !$may_advanced_options_config['hide_oi_jan']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_jan') . ': ' : '') . $option_value_data['jan'];
			}
			if (isset($option_value_data['isbn']) && !empty($option_value_data['isbn']) && !$may_advanced_options_config['hide_oi_isbn']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_isbn') . ': ' : '') . $option_value_data['isbn'];
			}
			if (isset($option_value_data['mpn']) && !empty($option_value_data['mpn']) && !$may_advanced_options_config['hide_oi_mpn']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_mpn') . ': ' : '') . $option_value_data['mpn'];
			}
			if (isset($option_value_data['location']) && !empty($option_value_data['location']) && !$may_advanced_options_config['hide_oi_location']) {
				$details[] = ($show_attribute_label ? $this->language->get('text_location') . ': ' : '') . $option_value_data['location'];
			}

			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
			$length_unit = $this->length->getUnit($product_info['length_class_id']);

			if (isset($option_value_data['dimension_l']) && !empty($option_value_data['dimension_l']) && !$may_advanced_options_config['hide_oi_dimension']) {
				$dimension = $option_value_data['dimension_l'] . $length_unit;
				if (isset($option_value_data['dimension_w']) && !empty($option_value_data['dimension_w'])) {
					$dimension .= ' x ' . $option_value_data['dimension_w'] . $length_unit;
				}
				if (isset($option_value_data['dimension_h']) && !empty($option_value_data['dimension_h'])) {
					$dimension .= ' x ' . $option_value_data['dimension_h'] . $length_unit;
				}

				$details[] = ($show_attribute_label ? $this->language->get('text_dimension') . ': ' : '') . $dimension;
			}

			$args[0]['products'][$key]['model'] = implode('<br/>', $details);
		}
	}

	public function mCheckoutOrderEditOrderBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data = array($args[1]);
		$this->mCheckoutOrderAddOrderBefore($route, $data);
		$args[1] = $data[0];
	}

	public function mJournal3OrderSaveBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data = array($args[1]);
		$this->mCheckoutOrderAddOrderBefore($route, $data);
		$args[1] = $data[0];
	}

	public function vCheckoutConfirmBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->vCommonCartBefore($route, $data);
	}

	public function mCatalogProductGetProductAfter(&$route, &$args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->library('may/may_advanced_options');
		$this->may_advanced_options->adjustTag($output);

		if (isset($this->request->get['route']) && $this->request->get['route'] == 'checkout/cart/add' && isset($this->request->post['option']) && !empty($this->request->post['option']['image'])) {
			$output['image'] = $this->request->post['option']['image'];
		}

		if (!$this->cache->get('may_advanced_option_product_' . $args[0])) {
			$this->cache->set('may_advanced_option_product_' . $args[0], $output);
		}
	}

	public function mCheckoutOrderAddOrderHistoryAfter(&$route, &$args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->model('checkout/order');
		$this->load->model('extension/may/advanced_options');

		$order_products = $this->model_checkout_order->getOrderProducts($args[0]);
		foreach ($order_products as $order_product) {
			$order_options = $this->model_checkout_order->getOrderOptions($args[0], $order_product['order_product_id']);
			$this->model_extension_may_advanced_options->updateOrderProductAdvancedOptions($order_product, $order_options);
		}
	}

	public function vDQuickCheckoutCartBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$json = json_decode($data['json'], true);
		$data['products'] = $json['products'];
		foreach ($data['products'] as &$product) {
			$product['cart_id'] = $product['key'];
		}

		$this->vCommonCartBefore($route, $data);

		$json['products'] = $data['products'];
		unset($data['products']);
		$data['json'] = json_encode($json);
	}

	public function cExtensionDQuickCheckoutCartPrepareAfter($route, &$args, &$json) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['products'] = $json['products'];
		foreach ($data['products'] as &$product) {
			$product['cart_id'] = $product['key'];
		}

		$this->vCommonCartBefore($route, $data);

		$json['products'] = $data['products'];
		unset($data['products']);
	}

	public function mExtensionDQuickCheckoutOrderUpdateOrderBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$tmp = $args[0];
		$args[0] = $args[1];
		
		$this->mCheckoutOrderAddOrderBefore($route, $args);

		$args[1] = $args[0];
		$args[0] = $tmp;
	}

	public function vProductCategoryBefore($route, &$data, $code = false, $setting = array()) {
		if (!$this->config->get('may_advanced_options_status') ||
			!$this->config->get('may_advanced_options_swatches') ||
			!$this->config->get('may_advanced_options_show_first_option_in_list')
		) {
			return;
		}

		if (!isset($data['products']) || !is_array($data['products'])) {
			return;
		}

		if (!empty($code)) {
			$main_route = isset($this->request->get['route']) ? $this->request->get['route'] : 'common/home';

			$this->load->model('design/layout');
			$layout_id = $this->model_design_layout->getLayout($main_route);

			if ($layout_id) {
				$this->load->model('extension/may/advanced_options');
				$module = $this->model_extension_may_advanced_options->getLayoutModuleByCode($layout_id, $code);
				if ($module) {
					$module_id = str_replace($code . '.', '', $module['code']);
					$this->load->model('setting/module');
					$setting = $this->model_setting_module->getModule($module_id);
				}
			}
		}

		$product_image_width = isset($setting['width']) ? $setting['width'] : $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width');
		$product_image_height = isset($setting['height']) ? $setting['height'] : $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height');

		$this->load->model('tool/image');
		$this->load->model('extension/may/advanced_options');

		if (!($products = $this->cache->get('may_advanced_option_product_list'))) {
			$products = array();
		}

		foreach ($data['products'] as $product) {
			if (isset($products[$product['href']])) {
				continue;
			}

			$values = $this->model_extension_may_advanced_options->getProductFirstOptionValues($product['product_id']);
			if (count($values)) {
				foreach ($values as &$value) {
					$images = array();
					foreach ($value['image'] as $index => $image) {
						$new_image = $this->model_tool_image->resize($image, $product_image_width, $product_image_height);
						$new_image2x = (isset($product['thumb2x'])) ? $this->model_tool_image->resize($image, $product_image_width * 2, $product_image_height * 2) : $new_image;

						$images[] = array(
							'origin' => $image,
							'popup' => $new_image,
							'thumb' => $new_image,
							'popup2x' => $new_image2x,
							'thumb2x' => $new_image2x
						);

						if ($index) {
							break;
						}
					}

					$value['image'] = $images;

					if (!empty($value['swatch_image'])) {
						$value['swatch_image'] = $this->model_tool_image->resize($value['swatch_image'], $product_image_width, $product_image_height);
					}

					$value['base_price'] = $this->currency->format($value['price'], $this->session->data['currency']);
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$value['price'] = $this->currency->format($this->tax->calculate((float)$value['price'], $value['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
					}
				}

				$product['option_name'] = $values[0]['option_name'];
				$product['option'] = $values;
			}

			//$product['weight_unit'] = $this->weight->getUnit($product['weight_class_id']);
			//$product['length_unit'] = $this->length->getUnit($product['length_class_id']);		

			$products[$product['href']] = $product;
		}

		$this->cache->set('may_advanced_option_product_list', $products);

		if ($route == 'product/product' && !isset($data['may_advanced_options'])) {
			$route .= '/related';
		}

		if (isset($data['footer'])) {
			$data['footer'] = $this->getCategoryAdvancedOptionsOutput($route) . $data['footer'];
		}
	}

	public function vProductFeaturedBefore($route, &$data) {
		$this->vProductCategoryBefore($route, $data, 'featured');
	}

	public function vProductLatestBefore($route, &$data) {
		$this->vProductCategoryBefore($route, $data, 'latest');
	}

	public function vProductBestsellerBefore($route, &$data) {
		$this->vProductCategoryBefore($route, $data, 'bestseller');
	}

	public function vExtensionModuleOCProductBefore($route, &$data) {
		$this->vProductCategoryBefore($route, $data, 'occmsblock');
	}

	public function vExtensionModuleOCTabProductsBefore($route, &$data) {
		$data['products'] = array();
		if (isset($data['octabs'])) {
			foreach ($data['octabs'] as $octabs) {
				$data['products'] = array_merge($data['products'], $octabs['products']);
			}
		}
		$this->vProductCategoryBefore($route, $data, 'occmsblock');
	}

	public function vPlazaModulePtProductsBefore($route, &$data) {
		$data['products'] = array();
		if (isset($data['tabs'])) {
			foreach ($data['tabs'] as $tab) {
				$data['products'] = array_merge($data['products'], $tab['products']);
			}
		}
		$this->vProductCategoryBefore($route, $data, 'ptstaticblock');
	}

	public function vCommonFooterAfter(&$route, &$args, &$output) {
		if ($this->cache->get('may_advanced_option_product_list') &&
			!(isset($this->request->get['route']) && in_array($this->request->get['route'], array(
				'product/category', 
				'product/search', 
				'product/special', 
				'product/manufacturer_info', 
				'product/product',
				'plaza/filter/category',
				'product/catalog',
			)))
		) {
			$output = $this->getCategoryAdvancedOptionsOutput() . $output;
		}
	}

	public function vAjaxCategoryAfter(&$route, &$args, &$output) {
		if ($this->cache->get('may_advanced_option_product_list')) {
			$output = $this->getCategoryAdvancedOptionsOutput() . $output;
		}
	}

	protected function getCategoryAdvancedOptionsOutput($route = '') {
		$data = array();

		$data['products'] = $this->cache->get('may_advanced_option_product_list');
			
		$this->load->library('may/may_advanced_options');
		$data['currency'] = $this->may_advanced_options->getCurrencyInfo();

		if (!isset($data['may_advanced_options_config'])) {			
			$data['may_advanced_options_config'] = $this->may_advanced_options->getExtensionConfig();
		}

		if (in_array($data['may_advanced_options_config']['theme'], [
			'journal3', 
			'wokiee', 
			'basel', 
			'zeexo',
			'so-emarket',
			'so-revo',
			'so-claue',
			'so-myshop',
			'so-bestshop',
			'so-shoppystore',
			'BurnEngine',
			'fastor',
			'yoga',
			'oct_ultrastore',
			'oct_feelmart',
			'oct_remarket',
			'mahardhi',
			'cyberstore',
			'furelife',
		])) {
			$view_template = 'category/' . strtolower($data['may_advanced_options_config']['theme']);
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_pander') !== false) {
			$view_template = 'category/pander';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_truemart') !== false) {
			$view_template = 'category/truemart';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_mimosa') !== false) {
			$view_template = 'category/mimosa';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_sinrato') !== false) {
			$view_template = 'category/sinrato';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_gicor') !== false) {
			$view_template = 'category/gicor';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_debaco') !== false) {
			$view_template = 'category/debaco';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_drama') !== false) {
			$view_template = 'category/drama';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'tt_madina') !== false) {
			$view_template = 'category/madina';
		} else if (strpos($data['may_advanced_options_config']['theme'], 'kenza') !== false) {
			$view_template = 'category/kenza';
		} else {
			$view_template = 'category';
		}

		$data['route'] = $route;

		$this->cache->delete('may_advanced_option_product_list');

		$data['may_advanced_options_template'] = array(
			'js_helper' => $this->load->view('extension/may/advanced_options/product/common/js_helper', $data),
			'options_in_list' => $this->load->view('extension/may/advanced_options/product/common/options_in_list', $data),
			'style' => $this->load->view('extension/may/advanced_options/product/common/style', $data),
		);

		return $this->load->view('extension/may/advanced_options/product/' . $view_template, $data);
	}

	public function cJournal3NotificationCartBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$args['product_info']['model'] = !empty($this->request->post['option']['model']) ? $this->request->post['option']['model'] : $args['product_info']['model'];
		$args['product_info']['sku'] = !empty($this->request->post['option']['sku']) ? $this->request->post['option']['sku'] : $args['product_info']['sku'];
		$args['product_info']['upc'] = !empty($this->request->post['option']['upc']) ? $this->request->post['option']['upc'] : $args['product_info']['upc'];
		$args['product_info']['ean'] = !empty($this->request->post['option']['ean']) ? $this->request->post['option']['ean'] : $args['product_info']['ean'];
		$args['product_info']['jan'] = !empty($this->request->post['option']['jan']) ? $this->request->post['option']['jan'] : $args['product_info']['jan'];
		$args['product_info']['mpn'] = !empty($this->request->post['option']['mpn']) ? $this->request->post['option']['mpn'] : $args['product_info']['mpn'];
		$args['product_info']['location'] = !empty($this->request->post['option']['location']) ? $this->request->post['option']['location'] : $args['product_info']['location'];
		$args['product_info']['image'] = !empty($this->request->post['option']['image']) ? $this->request->post['option']['image'] : $args['product_info']['image'];
		$args['product_info']['weight'] = !empty($this->request->post['option']['weight']) ? $this->request->post['option']['weight'] : $args['product_info']['weight'];
		$args['product_info']['length'] = !empty($this->request->post['option']['dimension_l']) ? $this->request->post['option']['dimension_l'] : $args['product_info']['length'];
		$args['product_info']['width'] = !empty($this->request->post['option']['dimension_w']) ? $this->request->post['option']['dimension_w'] : $args['product_info']['width'];
		$args['product_info']['height'] = !empty($this->request->post['option']['dimension_h']) ? $this->request->post['option']['dimension_h'] : $args['product_info']['height'];
	}

	public function vJournal3CheckoutCartBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->load->language('extension/may/advanced_options');

		$args['column_model'] = $this->language->get('text_product_details');
	}

	public function vJournal3CheckoutCheckoutBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$this->vCommonCartBefore($route, $args['checkout_data']);
	}

	public function vJournal3ModuleProductsBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['products'] = array();
		if (isset($data['items'])) {
			foreach ($data['items'] as $item) {
				$data['products'] = array_merge($data['products'], $item['products']);
			}
		}

		$this->vProductCategoryBefore($route, $data, 'j3module');
		unset($data['products']);
	}

	public function mDispatchJsonBefore(&$route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (isset($this->request->get['route'])) {
			switch ($this->request->get['route']) {
				case 'journal3/checkout/cart_update':
				case 'journal3/checkout/cart_delete':
				case 'journal3/checkout/save':
					$this->vCommonCartBefore($route, $args[0]);
					break;
				case 'extension/basel/basel_features/add_to_cart':
					$this->load->model('tool/image');
					if (isset($this->request->post['option']['image']))	{
						$args[0]['image'] = $this->model_tool_image->resize($this->request->post['option']['image'], $this->config->get('theme_default_image_cart_width'), $this->config->get('theme_default_image_cart_height'));
					}
					break;
			}
		}
	}

	public function cSoconfigCartAddBefore($route, &$args) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (isset($this->request->post['option']) && isset($this->request->post['option']['image']) && !empty($this->request->post['option']['image'])) {
			$this->cache->set('may_advanced_option_soconfig_cart_add_image', $this->request->post['option']['image']);
		}
	}

	public function vSoconfigQuickcartBefore($route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['product'])) {
			return;
		}

		if ($soconfig_cart_Add_image = $this->cache->get('may_advanced_option_soconfig_cart_add_image')) {
			$this->load->model('tool/image');
			$data['product']['thumb'] = $this->model_tool_image->resize($soconfig_cart_Add_image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));

			$this->cache->delete('may_advanced_option_soconfig_cart_add_image');
		}
	}

	public function mSoonepagecheckoutGetProductsAfter($route, &$args, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['products'] = $output;

		$this->vCommonCartBefore($route, $data);

		$output = $data['products'];
		unset($data['products']);
	}

	public function vZemezAjaxQuickviewBefore(&$route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		if (!isset($data['product']['options'])) {
			return;
		}

		$this->getProductAdvancedOptionsReady($data['product']);
	}

	public function vZemezAjaxQuickviewAfter(&$route, &$data, &$output) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['product']['is_quick_view'] = true;

		$output .= $this->load->view('extension/may/advanced_options/product/' . $data['product']['view_template'], $data['product']);
	}

	public function vZemezSingleCategoryProductBefore(&$route, &$data) {
		if (!$this->config->get('may_advanced_options_status')) {
			return;
		}

		$data['products'] = array_merge($data['special_products'], $data['bestseller_products'], $data['latest_products'], $data['featured_products']);

		$this->vProductCategoryBefore($route, $data);
	}
}
