<?php

namespace liveopencart\ext\liveprice;

class pso extends \liveopencart\lib\v0023\sub_library {
  
	use \liveopencart\lib\v0023\traits\cache;
	use \liveopencart\lib\v0023\traits\installed;
	
	public function installed() {
		return $this->getExtensionInstalledStatus('option_size', 'pso_installed', function(){
			return (method_exists( '\Cart', 'getAllProductOption') || method_exists( '\Cart\Cart', 'getAllProductOption') || method_exists( $this->cart, 'getAllProductOption') );
		});
	}
	
	public function getProductSizeOptionDetails($product_id) {
	  
		if ($this->installed()) {
		  
			$result = array('product_width'=>0, 'product_height'=>0, 'option_size_id'=>0);
			
			$min_width = 0;
			$max_width = 0;
			$min_height = 0;
			$max_height = 0;
			$cost_per_square = 0;
			$option_size_id = 0;
			$product_option_size_id = 0;
			
			$product_options =  $this->cart->getAllProductOption($product_id);
			if(!empty($product_options)) {
				foreach($product_options as $product_option) {
					if ($product_option['min_width'] != '' && $product_option['max_width'] != '' && $product_option['cost_per_square'] != '') {
						$min_width = $product_option['min_width'];
						$max_width = $product_option['max_width'];
						$min_height = $product_option['min_height'];
						$max_height = $product_option['max_height'];
						$cost_per_square = $product_option['cost_per_square'];
						$product_option_size_id = $product_option['product_option_id'];
						$result['option_size_id'] = $product_option['option_id']; 
						break;
					}
				}
			}
			if($cost_per_square > 0 && $product_option_size_id > 0) {
				$result['product_width'] = isset($options[$product_option_size_id]['width']) ? $options[$product_option_size_id]['width'] : 0;
				$result['product_height'] = isset($options[$product_option_size_id]['height']) ? $options[$product_option_size_id]['height'] : 0; 
			}
			
			return $result;
		}
	}
	
	public function getOptionDataItemIfAny($option_value, $product_option_id, $options_types, $pso_details) {
	  
		if ($options_types[$product_option_id]['type'] == 'size' && $pso_details) {
			
			$width = (float)$option_value['width'];
			$height = (float)$option_value['height'];
			
			$extra_price = $width * $height * $options_types[$product_option_id]['cost_per_square'];
			
			if($extra_price < $options_types[$product_option_id]['min_price']) {
				$extra_price = $options_types[$product_option_id]['min_price'];
			}
			
			//$option_price += $extra_price;
			
			if ($this->config->has('pso_dimension_order')) {
				$pso_dimension_order = $this->config->get('pso_dimension_order');
			} else {
				$pso_dimension_order = 0; //default: height then width
			}
			
			return array(
				'product_option_id'       => $product_option_id,
				'product_option_value_id' => '',
				'option_id'               => $options_types[$product_option_id]['option_id'],
				'option_value_id'         => '',
				'name'                    => $options_types[$product_option_id]['name'],
				'value'           		    => $pso_dimension_order == 1 ? ($width . 'x' . $height) : ($height . 'x' . $width),
				'type'                    => $options_types[$product_option_id]['type'],
				'quantity'                => '',
				'subtract'                => '',
				'price'                   => $extra_price,
				'price_prefix'            => '',
				'points'                  => '',
				'points_prefix'           => '',								
				'weight'                  => '',
				'weight_prefix'           => '',
			);
		}
	}
	
	public function updatePOVPriceBySize($product_id, $product_option_id, $calc_multiplier, $price, $pso_details) {
		if ($pso_details && $this->cart->checkScaleWithSize($product_id, $product_option_id, $pso_details['option_size_id'])) {
			$price = $calc_multiplier * $price * $pso_details['product_width'] * $pso_details['product_height'];
		}
		return $price;
	}
	
	protected function ProductHasSizeOption($options, $options_types) {
		foreach ($options as $product_option_id => $option_value) {
			if (!isset($options_types[$product_option_id])) {
				continue;
			}
			
			if ($options_types[$product_option_id]['type'] == 'size') {
				return true;
			}
		}
		return false;
	}
	
	public function updateProductPriceInBeginning($product_option_ids, $options, $options_types, $price) {
	  
		if ( $product_option_ids && $this->installed() ) {
			if ( $this->ProductHasSizeOption($options, $options_types) && $this->config->has('pso_exclude_product_price_in_total') && $this->config->get('pso_exclude_product_price_in_total') ) {
				$price = 0;
			}
		}
		return $price;
	}
  
}