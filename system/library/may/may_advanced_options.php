<?php

namespace May;

class May_Advanced_Options {
	private $registry;

    public function __construct($registry) {
		$this->registry = $registry;
    }

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	public function getExtensionConfig($override = array()) {
		return array(
			'option_select_effect_in_list' => $this->config->get('may_advanced_options_option_select_effect_in_list'),
			'show_option_price' => $this->config->get('may_advanced_options_show_option_price'),
			'select_first_option' => $this->config->get('may_advanced_options_select_first_option'),
			'swatches' => $this->config->get('may_advanced_options_swatches'),
			'show_out_of_stock_option' => $this->config->get('may_advanced_options_show_out_of_stock_option'),
			'swatch_image' => isset($override['swatch_image']) ? $override['swatch_image'] : $this->config->get('may_advanced_options_swatch_image'),

			'hide_pp_sku' => $this->config->get('may_advanced_options_hide_pp_sku'),
			'hide_pp_upc' => $this->config->get('may_advanced_options_hide_pp_upc'),
			'hide_pp_ean' => $this->config->get('may_advanced_options_hide_pp_ean'),
			'hide_pp_jan' => $this->config->get('may_advanced_options_hide_pp_jan'),
			'hide_pp_isbn' => $this->config->get('may_advanced_options_hide_pp_isbn'),
			'hide_pp_mpn' => $this->config->get('may_advanced_options_hide_pp_mpn'),
			'hide_pp_location' => $this->config->get('may_advanced_options_hide_pp_location'),
			'hide_pp_dimension' => $this->config->get('may_advanced_options_hide_pp_dimension'),
	
			'swatch_style_shape' => $this->config->get('may_advanced_options_swatch_style_shape'),
			'swatch_style_size_width' => $this->config->get('may_advanced_options_swatch_style_size_width'),
			'swatch_style_size_height' => $this->config->get('may_advanced_options_swatch_style_size_height'),
			'swatch_style_size_radius' => $this->config->get('may_advanced_options_swatch_style_size_radius'),
			'swatch_style_selected' => $this->config->get('may_advanced_options_swatch_style_selected'),
			'swatch_style_out_of_stock' => $this->config->get('may_advanced_options_swatch_style_out_of_stock'),
			'swatch_style_border_width' => $this->config->get('may_advanced_options_swatch_style_border_width'),
			'swatch_style_border_color_selected' => $this->config->get('may_advanced_options_swatch_style_border_color_selected'),
			'swatch_style_border_color_default' => $this->config->get('may_advanced_options_swatch_style_border_color_default'),
			'swatch_style_space_padding' => $this->config->get('may_advanced_options_swatch_style_space_padding'),
	
			'swatch_css' => $this->config->get('may_advanced_options_swatch_css'),
			'theme' => ($this->config->get('config_theme') == 'default') ? $this->config->get('theme_default_directory') : $this->config->get('config_theme'),
		);
	}

	public function getCurrencyInfo($currency = '') {
		if (empty($currency)) {
			$currency = $this->session->data['currency'];
		}
		
		$symbol_left = $this->currency->getSymbolLeft($currency);
		$symbol_right = $this->currency->getSymbolRight($currency);
		
		return array(
			'code' => $currency,
			'symbol_position' => (trim($symbol_left) !== "") ? "left" : "right",
			'symbol' => (trim($symbol_left) !== "") ? $symbol_left : $symbol_right,
			'decimal_place' => $this->currency->getDecimalPlace($currency),
			'decimal_point' => $this->language->get('decimal_point'),
			'thousand_point' => $this->language->get('thousand_point')
		);
	}

    public function getProductAdvancedOptionConfig($product_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "may_advanced_option_product_config` WHERE product_id = '" . (int)$product_id . "'");

        return $query->row;
    }

    public function getProductAdvancedOptionValues($product_id, $is_admin = false) {
        $query = $this->db->query("SELECT maopv.*, ss.name AS stock_status FROM `" . DB_PREFIX . "may_advanced_option_product_value` AS maopv LEFT JOIN `" . DB_PREFIX . "stock_status` AS ss ON maopv.stock_status_id = ss.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "' WHERE maopv.product_id = '" . (int)$product_id . "' ORDER BY maopv.product_option_id ASC, maopv.option_value_id ASC");

        $values = array();
		$product = $this->db->query("SELECT price, points, weight FROM `" . DB_PREFIX . "product` WHERE product_id = '" . (int)$product_id . "'")->row;
        foreach ($query->rows as $row) {

            if (!isset($values[$row['product_option_id']])) {
                $values[$row['product_option_id']] = array(
                    'combination_id' => $row['combination_id'],
                    'values' => array()
                );
            }

            $images = json_decode($row['image'], true);
            $row['image'] = (is_array($images)) ? $images : array();

			$row['dimension_l'] = (float)$row['dimension_l'] ? $row['dimension_l'] : '';
			$row['dimension_w'] = (float)$row['dimension_w'] ? $row['dimension_w'] : '';
			$row['dimension_h'] = (float)$row['dimension_h'] ? $row['dimension_h'] : '';

			if (!$is_admin) {

				if ($row['price_prefix'] == 'as' && (float)$row['price']) {
					if ((float)$product['price'] > (float)$row['price']) {
						$row['price'] = (float)$product['price'] - (float)$row['price'];
						$row['price_prefix'] = '-';
					} else {
						$row['price'] = (float)$row['price'] - (float)$product['price'];
						$row['price_prefix'] = '+';
					}
				}

				if ($row['point_prefix'] == 'as' && (int)$row['point']) {
					if ((int)$product['points'] > (int)$row['point']) {
						$row['point'] = (int)$product['points'] - (int)$row['point'];
						$row['point_prefix'] = '-';
					} else {
						$row['point'] = (int)$row['point'] - (int)$product['points'];
						$row['point_prefix'] = '+';
					}
				}

				if ($row['weight_prefix'] == 'as' && (float)$row['weight']) {
					if ((float)$product['weight'] > (float)$row['weight']) {
						$row['weight'] = (float)$product['weight'] - (float)$row['weight'];
						$row['weight_prefix'] = '-';
					} else {
						$row['weight'] = (float)$row['weight'] - (float)$product['weight'];
						$row['weight_prefix'] = '+';
					}
				}
			}
	
			$row['points'] = $row['point'];
			$row['points_prefix'] = $row['point_prefix'];

            $values[$row['product_option_id']]['values'][$row['option_value_id']] = $row;
        }
        //$this->log->write(print_r($values,1));
        return $values;
    }

    public function getSelectedProductAdvancedOptionValues($product_option_value_ids) {
		if (!is_array($product_option_value_ids)) {
			$product_option_value_ids = array($product_option_value_ids);
		}

        $query = $this->db->query("SELECT pov.product_option_value_id, maopv.* FROM " . DB_PREFIX . "product_option_value as pov LEFT JOIN " . DB_PREFIX . "may_advanced_option_product_value as maopv ON pov.product_option_id = maopv.product_option_id AND pov.option_value_id = maopv.option_value_id WHERE pov.product_option_value_id IN (" . implode(',', $product_option_value_ids) . ") ORDER BY pov.product_option_value_id DESC");

        $data = array();
        foreach ($query->rows as $row) {
            if (!isset($data['model']) && !empty($row['model'])) {
				$data['model'] = $row['model'];
			}
			if (!isset($data['sku']) && !empty($row['sku'])) {
				$data['sku'] = $row['sku'];
			}
			if (!isset($data['upc']) && !empty($row['upc'])) {
				$data['upc'] = $row['upc'];
			}
			if (!isset($data['ean']) && !empty($row['ean'])) {
				$data['ean'] = $row['ean'];
			}
			if (!isset($data['jan']) && !empty($row['jan'])) {
				$data['jan'] = $row['jan'];
			}
			if (!isset($data['isbn']) && !empty($row['isbn'])) {
				$data['isbn'] = $row['isbn'];
			}
			if (!isset($data['mpn']) && !empty($row['mpn'])) {
				$data['mpn'] = $row['mpn'];
			}
			if (!isset($data['location']) && !empty($row['location'])) {
				$data['location'] = $row['location'];
			}
			if (!isset($data['dimension_l']) && !$row['dimension_l']) {
				$data['dimension_l'] = $row['dimension_l'];
			}
			if (!isset($data['dimension_w']) && !$row['dimension_w']) {
				$data['dimension_w'] = $row['dimension_w'];
			}
			if (!isset($data['dimension_h']) && !$row['dimension_h']) {
				$data['dimension_h'] = $row['dimension_h'];
            }
            if (!isset($data['image'])) {
                $images = json_decode($row['image'], true);
                if (is_array($images) && count($images)) {
                    $data['image'] = $images;
                }
            }
			if (!isset($data['price']) && (float)$row['price'] && !isset($data['price_prefix']) && !empty($row['price_prefix'])) {
				$data['price'] = $row['price'];
				$data['price_prefix'] = $row['price_prefix'];
            }
			if (!isset($data['point']) && !$row['point'] && !isset($data['point_prefix']) && !empty($row['point_prefix'])) {
				$data['point'] = $row['point'];
				$data['point_prefix'] = $row['point_prefix'];
            }
			if (!isset($data['weight']) && !$row['weight'] && !isset($data['weight_prefix']) && !empty($row['weight_prefix'])) {
				$data['weight'] = $row['weight'];
				$data['weight_prefix'] = $row['weight_prefix'];
            }
			if (!isset($data['quantity']) && !$row['quantity']) {
				$data['quantity'] = $row['quantity'];
            }
			if (!isset($data['hide']) && !$row['hide']) {
				$data['hide'] = $row['hide'];
            }
        }

        return $data;
    }

	public function loadAdvancedOptions($data, $is_admin = false) {
		if (!isset($data['product_id']) || !$data['product_id']) {
			return $data;
		}

		$currency = $is_admin ? $this->config->get('config_currency') : $this->session->data['currency'];

		$advanced_option_config = $this->getProductAdvancedOptionConfig($data['product_id']);
		$advanced_option_values = $this->getProductAdvancedOptionValues($data['product_id'], $is_admin);

		if (!count($advanced_option_config) || !count($advanced_option_values)) {
			return $data;
		}

		$show_out_of_stock_option_config = $this->config->get('may_advanced_options_show_out_of_stock_option');

		$data['swatch_image'] = $advanced_option_config['swatch_image'];

		$this->load->model('tool/image');

		$may_advanced_options = array();
		$may_advanced_options_prefix = array();

		foreach ($data['options'] as $option_key => $option) {
			if (
				($option['type'] != 'select' && $option['type'] != 'radio') || 
				$option['value'] != 'may_advanced_option' || 
				!isset($advanced_option_values[$option['product_option_id']])
			) {
				continue;
			}

			$values = $advanced_option_values[$option['product_option_id']]['values'];
			foreach ($option['product_option_value'] as $option_value_key => &$option_value) {
				
				if (
					(isset($values[$option_value['option_value_id']]) && $values[$option_value['option_value_id']]['hide']) ||
					(!$show_out_of_stock_option_config && $advanced_option_config['subtract'] && $values[$option_value['option_value_id']]['quantity'] <= 0)
				) {
					unset($option['product_option_value'][$option_value_key]);
					continue;
				}

				if (count(array_filter($values[$option_value['option_value_id']]['image']))) {
					$option_value['product_images'] = array();
					foreach ($values[$option_value['option_value_id']]['image'] as $image) {
						$option_value['product_images'][] = array(
							'origin' => $image,
							'popup' => $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
							'thumb' => $this->model_tool_image->resize($image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
						);
					}
				}

				$option_value['base_price'] = $this->currency->format($values[$option_value['option_value_id']]['price'], $currency);
				$option_value['subtract'] = $advanced_option_config['subtract'];

				unset($values[$option_value['option_value_id']]['image']);
				unset($values[$option_value['option_value_id']]['price']);

				$option_value = array_merge($option_value, $values[$option_value['option_value_id']]);
			}

			$option['advanced_option_id'] = $advanced_option_values[$option['product_option_id']]['combination_id'];
			$option['init_disable'] = 1;

			$may_advanced_options[] = $option;
			unset($data['options'][$option_key]);
		}

		uasort($may_advanced_options, function($a, $b) {
			return ($a['product_option_id'] < $b['product_option_id']) ? -1 : 1;
		});

		if (count($may_advanced_options)) {
			$may_advanced_options = array_values($may_advanced_options);

			$init_visible_options = array();
			foreach ($may_advanced_options as $index => $option) {
				if (!in_array($option['option_id'], $init_visible_options)) {
					$may_advanced_options[$index]['init_visible'] = 1;
					$init_visible_options[] = $option['option_id'];
				} else {
					$may_advanced_options[$index]['init_visible'] = 0;
				}
			}

			$may_advanced_options[0]['init_disable'] = 0;
		}

		$data['may_advanced_options'] = $may_advanced_options;

		return $data;		
	}

	public function adjustTag(&$data) {
		if (isset($data['tag'])) {
			$tags = explode(':::', $data['tag']);
			$data['tag'] = $tags[0];
		}
	}

	public function adjustTags(&$data) {
		foreach ($data as &$datum) {
			$this->adjustTag($datum);
		}
	}
}
