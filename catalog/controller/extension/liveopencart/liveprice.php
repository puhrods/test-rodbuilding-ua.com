<?php
//  Live Price / Живая цена
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru
class ControllerExtensionLiveopencartLivePrice extends Controller {

	public function __construct() {
		call_user_func_array(array(
			'parent',
			'__construct'
		) , func_get_args());

		\liveopencart\ext\liveprice::getInstance($this->registry);
	}

	public function eventViewAnyTemplateBefore(&$route, &$data, &$template) {
		$data['liveprice_settings'] = $this->liveopencart_ext_liveprice->getSettings();
	}

	public function price() {
		
		if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
			$this->response->setOutput(json_encode(array()));
			return;
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			exit;
		}

		if (isset($this->request->get['quantity'])) {
			$quantity  = (int)$this->request->get['quantity'];
		} else {
			$quantity  = 1;
		}

		if (isset($this->request->post['option_oc'])) {
			$options   = $this->request->post['option_oc'];
		} elseif (isset($this->request->post['option'])) {
			$options   = $this->request->post['option'];
		} elseif (isset($this->request->post['option-fast'])) {
			$options   = $this->request->post['option-fast'];
		} elseif (!empty($this->request->post['option2']) && is_array($this->request->post['option2'])) {
			$options   = $this->request->post['option2'];		
		} else {
			$options   = array();
		}
		$options   = $this->liveopencart_ext_liveprice->getCalc()->normalizeArrayOfOptions($options);
		
		if (isset($this->request->post['theme_name'])) {
			$theme_name   = $this->request->post['theme_name'];
			$this->liveopencart_ext_liveprice->getThemeName($theme_name);
		}
		
		$quantity_per_option = !empty($this->request->post['quantity_per_option']) && is_array($this->request->post['quantity_per_option']) ? $this->request->post['quantity_per_option'] : false;

		$ro_qtys   = array();
		if (!empty($this->request->post['ro']) && is_array($this->request->post['ro'])) {
			foreach ($this->request->post['ro'] as $ro_qty) {
				$ro_qty['quantity']           = isset($ro_qty['quantity']) ? (int)$ro_qty['quantity'] : 0;
				if ($ro_qty['quantity']) {
					$ro_qtys[]           = $ro_qty;
				}
			}
		}
		
		$this->load->language('extension/liveopencart/liveprice');

		$lp_prices = $this->getPriceData(array(
			'quantity_per_option'	=> $quantity_per_option,
			'ro_qtys'           	=> $ro_qtys,
			'product_id'           	=> $product_id,
			'options'           	=> $options,
			'quantity'           	=> max($quantity, 1),
		));

		//if ( !empty($this->request->post['quantity_per_option']) && is_array($this->request->post['quantity_per_option']) ) {
		//	// specific calculation for a specific options (quantity is set for each option value)
		//	$quantity_per_options = $this->request->post['quantity_per_option'];
		//	$lp_prices = $this->getProductTotalPriceForQuantityPerOptionWithHtml( $product_id, $options, $quantity_per_options);
		//} else { // standard way
		//	$lp_prices = $this->getProductPriceWithHtml( $product_id, max($quantity, 1), $options, true );
		//}
		// return only required data
		$prices    = array(
			'htmls'	=> $lp_prices['htmls'],
			'ct'	=> $lp_prices['ct']
		);
		if (isset($this->request->get['rnd'])) {
			$prices['rnd']           = $this->request->get['rnd'];
		}
		
		$ov_prices = $this->getFinalPricesForOptionValues($product_id, max($quantity, 1), $options, true);
		if (!empty($ov_prices)) {
		//if (!empty($lp_prices['ov_prices'])) {
			$prices['ov_prices']           = $ov_prices;
			//$prices['ov_prices']           = $lp_prices['ov_prices'];
		}
		
		$qpo_prices = $this->getPricesForQPO($product_id, $options, $quantity_per_option);
		if (!empty($qpo_prices)) {
			$prices['qpo_prices']           = $qpo_prices;
		}
		
		if (!empty($lp_prices['details'])) {
			$prices['details']           = $lp_prices['details'];
		}

		$this->setAllowOriginHeader();

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($prices));

	}

	//
	public function getPriceData($params) {
		
		$multiplied_price     = isset($params['quantity']) ? $params['quantity'] : false;
		if (!empty($params['quantity_per_option'])) {
			// specific calculation for a specific options (quantity is set for each option value)
			$quantity_per_options = $params['quantity_per_option'];
			$lp_prices            = $this->getProductTotalPriceForQuantityPerOptionWithHtml($params['product_id'], $params['options'], $quantity_per_options);
		} else {
			if (!empty($params['ro_qtys'])) {
				$lp_prices            = $this->getProductTotalPriceForROWithHtml($params['product_id'], $params['options'], $params['ro_qtys']);
			}
			if (empty($lp_prices)) { // standard way
				$lp_prices            = $this->getProductPriceWithHtml($params['product_id'], max($params['quantity'], 1) , $params['options'], true, $multiplied_price);
			}
		}
		
		return $lp_prices;
	}

	public function getProductPriceWithHtml($product_id, $current_quantity = 0, $options = array() , $multiplied_price = false) {

		$prices           = $this->liveopencart_ext_liveprice->getCalc()->getProductPrice($product_id, $current_quantity, $options, 0, $multiplied_price);
		
		$simple_prices    = array(
			'price'					=> $prices['f_price_old_opt'],
			'special'				=> (($prices['special'] || $prices['special_opt']) ? $prices['f_special_opt'] : '') ,

			'special_date_start'	=> !empty($prices['special_date_start']) ? $prices['special_date_start'] : '',
			'special_date_end'		=> !empty($prices['special_date_end']) ? $prices['special_date_end'] : '',

			'tax'					=> ($prices['config_tax'] ? $prices['f_price_opt_notax'] : $prices['config_tax']) ,
			'discounts'				=> $prices['discounts'],
			'points'				=> $prices['points'],
			'reward'				=> isset($product_data['reward']) ? $product_data['reward'] : '',
			'minimum'				=> isset($product_data['minimum']) ? $product_data['minimum'] : '',
			'quantity'				=> $current_quantity,

			'price_val'				=> $this->tax->calculate($prices['price_old_opt'], $prices['tax_class_id'], $prices['config_tax']) ,
			'special_val'			=> $this->tax->calculate($prices['special_opt'], $prices['tax_class_id'], $prices['config_tax']) ,

			'product_id'			=> $product_id,
			'weight'				=> $prices['weight'],
			
			//  price taking special into account
			'total'					=> $prices['total'],
			'item'					=> $prices['item'],
			
			//'price_final_item'		=> (($prices['special'] || $prices['special_opt_item']) ? $prices['f_special_opt_item'] : $prices['f_price_old_opt_item']),
			//'tax_final_item'		=> ($prices['config_tax'] ? $prices['f_price_opt_notax_item'] : $prices['config_tax']) , // LP-calc already calculated "price without tax" taking specials into account
		);

		$lowest_price     = $this->liveopencart_ext_liveprice->getCalc()->getLowestPossiblePriceForProductPageIfEnabled($product_id, $simple_prices['price'], $simple_prices['special'], $simple_prices['tax'], $options, $current_quantity);
		if ($lowest_price) {
			$simple_prices['price']			= $lowest_price['f_price'];
			$simple_prices['special']		= $lowest_price['f_special'];
			$simple_prices['tax']			= $lowest_price['f_tax'];
			if (isset($lowest_price['price'])) {
				$simple_prices['price_val']		= $lowest_price['price'];
			}
			if (isset($lowest_price['special'])) {
				$simple_prices['special_val']	= $lowest_price['special'];
			}
		}

		$prices['htmls']	= $this->getPriceHtmls($simple_prices);
		$prices['ct']		= $this->liveopencart_ext_liveprice->getThemeName();

		//$ov_prices        = $this->getFinalPricesForOptionValues($product_id, $current_quantity, $options, $multiplied_price);
		//if ($ov_prices) {
		//	$prices['ov_prices']                  = $ov_prices;
		//}

		return $prices;
		//return array('prices'=>$prices, 'product_data'=>$product_data, 'option_data'=>$option_data);
		
	}

	protected function getFinalPricesForOptionValues($product_id, $current_quantity, $options, $multiplied_price) {

		$final_prices = array();

		$lp_settings  = $this->liveopencart_ext_liveprice->getSettings();

		if ($options) {
			if ( !empty($lp_settings['calc_prices_for_options']) ) {

				$this->load->model('catalog/product');
				$product_options       = $this->model_catalog_product->getProductOptions($product_id);
				$product_options_assoc = array();
				foreach ($product_options as $product_option) {
					$product_options_assoc[$product_option['product_option_id']]	= $product_option;
				}
	
				$current_options       = array();
				foreach ($product_options as $product_option) {
	
					if (!empty($product_option['product_option_value'])) {
						$po_id                 = $product_option['product_option_id'];
						$option_id             = $product_option['option_id'];
						if (in_array($option_id, $lp_settings['calc_prices_for_options'])) {
							$final_prices[$po_id]                       = array();
							foreach ($product_option['product_option_value'] as $pov) {
								$pov_id                			= $pov['product_option_value_id'];
								$current_options[$po_id]		= $pov_id;
								$prices                			= $this->liveopencart_ext_liveprice->getCalc()->getProductPrice($product_id, $current_quantity, $current_options, 0, $multiplied_price);
								$final_prices[$po_id][$pov_id]	= $prices['special'] || $prices['special_opt'] ? $prices['f_special_opt'] : $prices['f_price_old_opt'];
							}
						}
						if (!empty($options[$po_id])) {
							$current_options[$po_id]	= $options[$po_id];
						}
					}
				}
			}
		}

		return $final_prices;
	}
	
	protected function getPricesForQPO($product_id, $options, $quantity_per_option) {

		$final_prices = array();

		$lp_settings  = $this->liveopencart_ext_liveprice->getSettings();
		
		
		if ( !empty($lp_settings['calc_prices_for_qpo']) && !empty($quantity_per_option) ) {
			
			$default_customer_group_id = (int)$this->getDefaultCustomerGroupId();
			$current_customer_group_id = (int)$this->config->get('config_customer_group_id');
			
			$templates     = $this->liveopencart_ext_liveprice->getPriceTemplates();
			
			foreach ( $quantity_per_option as $product_option_id => $product_option_qpo ) {
				
				foreach ( $product_option_qpo as $pov_id => $qpo_qty ) {
					
					$current_qpo_qty = max(1, (int)$qpo_qty);
					
					$current_options = $options;
					$current_options[(int)$product_option_id] = (int)$pov_id;
					
					$prices = $this->liveopencart_ext_liveprice->getCalc()->getProductPriceByParamsArray( [
						'product_id'			=> $product_id,
						'quantity'				=> 1,
						'options'				=> $current_options,
						'without_discounts'		=> true,
						'ignore_cart'			=> true,
						'use_price_cache'		=> true,
						'$use_ro_data_cache'	=> true,
					]);
					
					$price_html_data = [
						'price' 	=> $prices['f_price_old_opt'],
						'special'	=> ($prices['special'] || $prices['special_opt'] ? $prices['f_special_opt'] : ''),
					];
					
					$price_data = [
						'po_id'			=> $product_option_id,
						'pov_id'		=> $pov_id,
						'price'			=> ($prices['special'] || $prices['special_opt'] ? $prices['f_special_opt'] : $prices['f_price_old_opt']),
						'price_html'	=> (isset($templates['qpo_price']) ? $this->liveopencart_ext_liveprice->render($templates['qpo_price'], $price_html_data) : ''),
					];
					
					if ( $current_customer_group_id != $default_customer_group_id ) {
						
						$this->config->set('config_customer_group_id', $default_customer_group_id);
						
						$prices_default_customer = $this->liveopencart_ext_liveprice->getCalc()->getProductPriceByParamsArray( [
							'product_id'			=> $product_id,
							'quantity'				=> 1,
							'options'				=> $current_options,
							'without_discounts'		=> true,
							'ignore_cart'			=> true,
							'use_price_cache'		=> true,
							'use_ro_data_cache'		=> true,
						]);
						
						$price_val_cg_current = round($this->tax->calculate(($prices['special'] || $prices['special_opt'] ? $prices['special_opt'] : $prices['price_old_opt']), $prices['tax_class_id'], $prices['config_tax']), 2);
						$price_val_cg_default = round($this->tax->calculate( ($prices_default_customer['special'] || $prices_default_customer['special_opt'] ? $prices_default_customer['special_opt'] : $prices_default_customer['price_old_opt']), $prices_default_customer['tax_class_id'] , $prices_default_customer['config_tax']), 2);
						
						if ( $price_val_cg_current < $price_val_cg_default ) {
							
							$price_data['price_default'] = $this->language->get('liveprice_price_default_customer_group').' '.($prices_default_customer['special'] || $prices_default_customer['special_opt'] ? $prices_default_customer['f_special_opt'] : $prices_default_customer['f_price_old_opt']);
							$price_data['price_profit'] = $this->language->get('liveprice_price_profit').' '.$this->liveopencart_ext_liveprice->getCalc()->format($price_val_cg_default-$price_val_cg_current);
							//$price_data['price_val_cg_current'] = $price_val_cg_current;
							//$price_data['price_val_cg_default'] = $price_val_cg_default;
						}
						
						$this->config->set('config_customer_group_id', $current_customer_group_id);
					}
					
					$final_prices[] = $price_data;
				}
			}
		}
		

		return $final_prices;
	}
	
	protected function getDefaultCustomerGroupId() {
		
		$this->load->model('setting/setting');
		return $this->model_setting_setting->getSettingValue('config_customer_group_id');
		
	}
	

	protected function getPriceHtmls($prices) {

		$lp_settings = $this->liveopencart_ext_liveprice->getSettings();
		
		$this->load->language('extension/liveopencart/liveprice', 'liveprice_language');
		$data = $this->language->get('liveprice_language')->all();
		
		
		$this->load->language('product/product');
		$data['text_price']			= $this->language->get('text_price');
		$data['text_tax']			= $this->language->get('text_tax');
		$data['text_discount']		= $this->language->get('text_discount');
		$data['text_points']		= $this->language->get('text_points');
		$data['text_reward']		= $this->language->get('text_reward');
		$data['text_stock']			= $this->language->get('text_stock');
		$data['text_minimum']		= sprintf($this->language->get('text_minimum') , $prices['minimum']);
		$data['text_manufacturer']	= $this->language->get('text_manufacturer');

		$data['product_id']			= $prices['product_id'];
		
		$data['price']				= $prices['price'];
		$data['special']			= $prices['special'];
		if (!empty($lp_settings['hide_tax'])) {
			$data['tax']			= '';
		} else {
			$data['tax']			= $prices['tax'];
		}
		
		$data['prices'] 			= $prices;

		$data['quantity']			= $prices['quantity'];
		$data['points']				= $prices['points'];
		$data['discounts']			= $prices['discounts'];
		$data['minimum']			= $prices['minimum'];
		$data['price_val']			= $prices['price_val'];
		$data['special_val']		= $prices['special_val'];
		
		//$data['price_final_item']	= $prices['price_final_item'];
		//$data['tax_final_item']		= $prices['tax_final_item'];
		
		$data['theme_name']			= $this->liveopencart_ext_liveprice->getThemeName();

		$htmls = array();

		$custom_controller_code_file = $this->liveopencart_ext_liveprice->getDirOfCurrentTheme() . 'controller_code.php';
		if (file_exists($custom_controller_code_file)) {
			include ($custom_controller_code_file);
		}
		
		$custom_controller_code_file = $this->liveopencart_ext_liveprice->getDirOfOverrideTheme() . 'controller_code.php';
		if (file_exists($custom_controller_code_file)) {
			include ($custom_controller_code_file);
		}

		$templates     = $this->liveopencart_ext_liveprice->getPriceTemplates();

		foreach ($templates as $template_name => $template_path) {
			if ( $template_name != 'qpo_price' ) {
				$htmls[$template_name]               = $this->liveopencart_ext_liveprice->render($template_path, $data);
			}
		}

		return $htmls;

	}

	public function getProductTotalPriceForQuantityPerOptionWithHtml($p_product_id, $p_options, $p_quantity_per_options) {
		
		//$total_quantity = 0;
		//$total_price_old_opt = 0;
		//$total_special_opt = 0;
		//$total_price_opt = 0;
		//$total_points = 0;
		\liveopencart\ext\qpo::getInstance($this->registry);

		if ($this->liveopencart_ext_qpo->installed()) {

			$quantity_per_options = $this->liveopencart_ext_qpo->normalizeArrayOfQPO($p_quantity_per_options);
			$qpo_all_combinations = $this->liveopencart_ext_qpo->getCombinationsOfOptions($p_product_id, $quantity_per_options, $p_options);

			$prices               = $this->getProductTotalPriceForCombinationsOfOptionsWithHtml($p_product_id, $qpo_all_combinations);

			//$qpo_total_quantity = 0;
			//foreach ($qpo_all_combinations as $qpo_of_options) {
			//	$qpo_total_quantity+= $qpo_of_options['quantity'];
			//}
			//
			//$stored_discounts = array();
			//foreach ( $qpo_all_combinations as $qpo_of_options ) { // get prices for all combinations of options
			//
			//	if ( $qpo_of_options['quantity'] ) {
			//
			//		$quantity = $qpo_of_options['quantity'];
			//
			//		$qpo_total_quantity_except_current = $qpo_total_quantity - $quantity;
			//
			//		$lp_prices = $this->liveopencart_ext_liveprice->getCalc()->getProductPriceByParamsArray( array(
			//			'product_id' =>$p_product_id,
			//			'quantity' => $quantity,
			//			'options' => $qpo_of_options['options'],
			//			'multiplied_price' => true,
			//			'qpo_discount_quantity_addition' => $qpo_total_quantity_except_current,
			//		) );
			//
			//		$total_quantity+= $quantity;
			//		//$current_product_data = $lp_data['product_data'];
			//		$current_tax_class_id = $lp_prices['tax_class_id'];
			//		$current_config_tax = $lp_prices['config_tax'];
			//
			//		$current_price_old_opt = (float)$lp_prices['price_old_opt'];
			//		$current_special_opt = $lp_prices['special'] ? (float)$lp_prices['special_opt'] : 0;
			//		$current_price_opt = (float)$lp_prices['price_opt'];
			//		$current_points = $lp_prices['points'];
			//
			//		$total_price_old_opt+= $quantity*$current_price_old_opt;
			//		$total_special_opt+= $quantity*$current_special_opt;
			//		$total_price_opt+= $quantity*$current_price_opt;
			//		$total_points+= $current_points; // points are already multiplied
			//
			//		if ( count($stored_discounts) == 0 ) {
			//			$stored_discounts[] = $lp_prices['discounts'];
			//		} else {
			//			if ( $stored_discounts[ count($stored_discounts)-1 ] != $lp_prices['discounts'] ) {
			//				$stored_discounts[] = $lp_prices['discounts'];
			//			}
			//		}
			//	}
			//}
			
		}

		if ($prices) {
			return $prices;
		}
		else { // no quantity per options, use standard calculation
			return $this->getProductPriceWithHtml($p_product_id, 1, $p_options, true);
		}

	}

	public function getProductTotalPriceForROWithHtml($product_id, $p_options, $ro_qtys) {

		if (class_exists('\liveopencart\ext\ro')) {
			\liveopencart\ext\ro::getInstance($this->registry);

			//$ro_ids = array();
			//foreach ( $ro_qtys as $ro_qty ) {
			//	if ( !empty($ro_qty['quantity']) ) {
			//		$ro_ids[] = $ro_qty['ro_id'];
			//	}
			//}
			//$ro_data = $this->liveopencart_ext_ro->getROData($product_id,  true, -1, true);
			//$ro_combs = array();
			//if ( $ro_data ) {
			//	foreach ( $ro_data as $ro_dt ) {
			//		foreach ( $ro_dt['ro'] as $ro_comb ) {
			//			if ( in_array($ro_comb['relatedoptions_id'], $ro_ids) ) {
			//				$ro_combs[$ro_comb['relatedoptions_id']] = $ro_comb;
			//			}
			//		}
			//	}
			//}
			foreach ($ro_qtys as & $ro_qty) {
				$ro_qty['options'] = $this->liveopencart_ext_liveprice->getCalc()->normalizeArrayOfOptions($ro_qty['option']) + $p_options;
				unset($ro_qty['option']);
			}
			unset($ro_qty);

			return $this->getProductTotalPriceForCombinationsOfOptionsWithHtml($product_id, $ro_qtys, true);
		}

	}

	protected function getProductTotalPriceForCombinationsOfOptionsWithHtml($product_id, $option_combs, $return_details      = false) {
		
		$total_quantity      = 0;
		$total_price_old_opt = 0;
		$total_special_opt   = 0;
		$total_price_opt     = 0;
		$total_points        = 0;

		$total_quantity      = 0;
		foreach ($option_combs as $option_comb) {
			$total_quantity += $option_comb['quantity'];
		}

		$stored_discounts              = array();
		foreach ($option_combs as &$option_comb) { // get prices for all combinations of options
			if ($option_comb['quantity']) {

				$quantity                      = $option_comb['quantity'];

				$total_quantity_except_current = $total_quantity - $quantity;

				$lp_prices                     = $this->liveopencart_ext_liveprice->getCalc()->getProductPriceByParamsArray(array(
					'product_id' => $product_id,
					'quantity' => $quantity,
					'options' => $option_comb['options'],
					'multiplied_price' => true,
					'discount_quantity_addition' => $total_quantity_except_current,
				));
				
				//$total_quantity += $quantity; // set above the loop
				//$current_product_data = $lp_data['product_data'];
				$current_tax_class_id  = $lp_prices['tax_class_id'];
				$current_config_tax    = $lp_prices['config_tax'];

				$current_price_old_opt = (float)$lp_prices['price_old_opt'];
				$current_special_opt   = $lp_prices['special'] ? (float)$lp_prices['special_opt'] : 0;
				$current_price_opt     = (float)$lp_prices['price_opt'];
				$current_points        = $lp_prices['points'];

				$total_price_old_opt += $quantity * $current_price_old_opt;
				$total_special_opt += $quantity * $current_special_opt;
				$total_price_opt += $quantity * $current_price_opt;
				$total_points += $current_points; // points are already multiplied
				if (count($stored_discounts) == 0) {
					$stored_discounts[] = $lp_prices['discounts'];
				}
				else {
					if ($stored_discounts[count($stored_discounts) - 1] != $lp_prices['discounts']) {
						$stored_discounts[] = $lp_prices['discounts'];
					}
				}

				$option_comb['price_formatted'] = $lp_prices['special'] ? $lp_prices['special_opt'] : $lp_prices['price_old_opt'];
				$option_comb['price_formatted'] = $this->liveopencart_ext_liveprice->getCalc()->format($this->tax->calculate($option_comb['price_formatted'], $current_tax_class_id, $current_config_tax));
				$option_comb['total_formatted'] = $quantity * ($lp_prices['special'] ? $lp_prices['special_opt'] : $lp_prices['price_old_opt']);
				$option_comb['total_formatted'] = $this->liveopencart_ext_liveprice->getCalc()->format($this->tax->calculate($option_comb['total_formatted'], $current_tax_class_id, $current_config_tax));
			}
		}
		unset($option_comb);

		if ($total_quantity) {
			
			$item_price_old_opt 	= round($total_price_old_opt / ( max($total_quantity, 1)), 2);
			$item_special_old_opt 	= round($total_special_opt / ( max($total_quantity, 1)), 2);

			$prices        = array();
			$simple_prices = array(
				'price'					=> $this->liveopencart_ext_liveprice->getCalc()->format($this->tax->calculate($total_price_old_opt, $current_tax_class_id, $current_config_tax)) ,
				'special'				=> $total_special_opt ? $this->liveopencart_ext_liveprice->getCalc()->format($this->tax->calculate($total_special_opt, $current_tax_class_id, $current_config_tax)) : '',

				'tax'					=> $current_config_tax ? $this->liveopencart_ext_liveprice->getCalc()->format($total_price_opt) : $current_config_tax,
				'discounts'				=> (!empty($stored_discounts) && count($stored_discounts) == 1 ? $stored_discounts[0] : array()) ,
				'points'				=> $total_points, // $prices['points']
				'reward'				=> $lp_prices['reward'],
				'minimum'				=> $lp_prices['minimum'],
				'quantity'				=> $total_quantity,

				'special_date_start'	=> !empty($lp_prices['special_date_start']) ? $lp_prices['special_date_start'] : '',
				'special_date_end'		=> !empty($lp_prices['special_date_end']) ? $lp_prices['special_date_end'] : '',

				'price_val'				=> $this->tax->calculate($total_price_old_opt, $current_tax_class_id, $current_config_tax) ,
				'special_val'			=> $this->tax->calculate($total_special_opt, $current_tax_class_id, $current_config_tax) ,

				'product_id'			=> $product_id,
				'weight'				=> $lp_prices['weight'],
				
				//  price taking special into account
				'total'					=> $this->liveopencart_ext_liveprice->getCalc()->generatePriceArray($total_price_old_opt, $total_special_opt, $current_tax_class_id, $current_config_tax),
				'item'					=> $this->liveopencart_ext_liveprice->getCalc()->generatePriceArray($item_price_old_opt, $item_special_old_opt, $current_tax_class_id, $current_config_tax),
				//'price_final_item'		=> $this->liveopencart_ext_liveprice->getCalc()->format( $this->tax->calculate( round(($total_special_opt ? $total_special_opt : $total_price_old_opt) / ( max($total_quantity, 1)), 2), $current_tax_class_id, $current_config_tax)  ),
				//'tax_final_item'		=> $this->liveopencart_ext_liveprice->getCalc()->format( round(($total_special_opt ? $total_special_opt : $total_price_old_opt) / ( max($total_quantity, 1)), 2) ),
			);
			

			if ($return_details) {
				$simple_prices['details']               = $option_combs;
			}

			$prices['htmls']			= $this->getPriceHtmls($simple_prices);
			$prices['ct']				= $this->liveopencart_ext_liveprice->getThemeName();

			if ($return_details) {
				$prices['details']		= array(
					'combs' 			=> $option_combs,
					'price_formatted' 	=> $simple_prices['special'] ? $simple_prices['special'] : $simple_prices['price'],
				);
			}

			return $prices;

		}

	}

	// fix for www and non-www requests
	protected function setAllowOriginHeader() {

		if (!empty($this->request->server['HTTP_ORIGIN'])) {

			if ($this->request->server['HTTPS']) { // the HTTPS propety should be set properly in startup.php
				$server         = $this->config->get('config_ssl');
			}
			else {
				$server         = $this->config->get('config_url');
			}
			$http_origin    = trim($this->request->server['HTTP_ORIGIN'], '/');
			$server         = trim($server, '/');

			if ($server != $http_origin) {
				$url_beginnings = array(
					'http://www.',
					'https://www.',
					'http://',
					'https://'
				);
				foreach ($url_beginnings as $url_beginning) {
					if (substr($server, 0, strlen($url_beginning)) == $url_beginning) {
						$server         = substr($server, strlen($url_beginning));
					}
					if (substr($http_origin, 0, strlen($url_beginning)) == $url_beginning) {
						$http_origin    = substr($http_origin, strlen($url_beginning));
					}
				}
				if ($server == $http_origin) {
					$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
					$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
				}
			}
		}
	}

	// for some themes
	public function eventProductPageViewBefore(&$route, &$data, &$output) {
		$data = $this->liveopencart_ext_liveprice->addProductPageData($data);
	}
	// for some themes
	public function eventProductPageViewAfter(&$route, &$data, &$output) {
		$data = $this->liveopencart_ext_liveprice->addProductPageData($data);
		if (!empty($data['lp_product_page_code'])) {
			$output .= $data['lp_product_page_code'];
		}
	}
	// for some themes 
	public function eventProductListViewBefore(&$route, &$data, &$output) {
		if ( $this->liveopencart_ext_liveprice->versionPRO() && !empty($data['products']) ) {
			foreach ( $data['products'] as &$product ) {
				
				$this->load->model('catalog/product');
				
				$lp_product = $this->model_catalog_product->getProduct($product['product_id']);
				$prices = \liveopencart\ext\liveprice::getInstance($this->registry)->getCalc()->getPriceStartingFrom( $lp_product, $product['price'], $product['special'], $product['tax'] );
				if ( $prices ) {
					$product['price'] = $prices['f_price'];
					if ($prices['f_special']) {
						$product['special'] = $prices['f_special'];
					}
					if ( isset($tax) ) {
						$product['tax'] = $prices['f_tax'];
					}
				}
				
			}
		}	
		
	}

}

