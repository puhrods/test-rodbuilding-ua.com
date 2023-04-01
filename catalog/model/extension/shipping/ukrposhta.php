<?php
class ModelShippingUkrPoshta extends Model {
    private $settings;

	public function __construct($registry) {
     	parent::__construct($registry);
     	
     	require_once(DIR_SYSTEM . 'helper/ukrposhta.php');

     	$registry->set('ukrposhta', new Ukrposhta($registry));

        if (version_compare(VERSION, '3', '>=')) {
            $this->settings = $this->config->get('shipping_ukrposhta');
        } else {
            $this->settings = $this->config->get('ukrposhta');
        }
    }
    
	function getQuote($address) {
        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->language('extension/shipping/ukrposhta');
        } else {
            $this->load->language('shipping/ukrposhta');
        }

		$quote_data = array();
        $url        = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;
		$products 	= $this->cart->getProducts();
        $departure 	= $this->ukrposhta->getDeparture($products);
		$total 		= $this->getTotal($products);

        if ($this->currency->getValue('UAH') != 1) {
            $total *= $this->currency->getValue('UAH');
        }

        if (empty($address['postcode'])) {
            if (!empty($this->session->data['shipping_address']['postcode'])) {
                $address['postcode'] = $this->session->data['shipping_address']['postcode'];
            } elseif (!isset($address['postcode'])) {
                $address['postcode'] = '';
            }
        }

		if (is_array($this->settings['shipping_methods'])) {
		    foreach ($this->settings['shipping_methods'] as $code => $method) {
				if (!$method['status']) {
					continue;
				}
				
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$method['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				
				if ($method['geo_zone_id'] && !$query->num_rows) {
					$status = false;
				} elseif ($total < $method['minimum_order_amount']) {
					$status = false;
				} elseif ($method['maximum_order_amount'] && $total > $method['maximum_order_amount']) {
					$status = false;
				} else {
					$status = true;
				}
				
				if ($status) {
					$cost       = 0;
					$period     = 0;
					$img        = '';
					$code_parts = explode('_', $code);

                    if ($method['name'][$this->config->get('config_language_id')]) {
                        $description = $method['name'][$this->config->get('config_language_id')];
                    } else {
                        $description = $this->language->get('text_description_' . $code);
                    }

					// Cost	
					if ($method['cost'] && (!$method['free_shipping'] || $total < $method['free_shipping'])) {
                        if ($method['api_calculation'] && $address['postcode'] && $departure['weight']) {
                            if ($this->settings['sender_address_pick_up']) {
                                $service_type = 'D2';
                            } else {
                                $service_type = 'W2';
                            }

                            $cost_data = array (
                                'validate'      => true,
                                'addressFrom'   => array(
                                    'postcode' => $this->settings['sender_postcode']
                                ),
                                'addressTo'     => array(
                                    'postcode' => $address['postcode']
                                ),
                                'type'		    => strtoupper($code_parts[0]),
                                'deliveryType'	=> $service_type . strtoupper($code_parts[1]),
                                'weight'		=> ($departure['weight'] * 1000),
                                'length'		=> $departure['length'],
                                'declaredPrice' => $total
                            );

                            if ($this->settings['recommended_letter']) {
                                $cost_data['recommended'] = true;
                            }

                            if ($this->settings['sms_message']) {
                                $cost_data['sms'] = true;
                            }

                            if ($this->settings['tariffs'][$code_parts[0]]['discount']) {
                                $cost_data['discounts'][] = array(
                                    'rate'        => $this->settings['tariffs'][$code_parts[0]]['discount'],
                                    'description' => $code_parts[0] . ' ' . $this->settings['tariffs'][$code_parts[0]]['discount'] . '%'
                                );
                            }

                            $result = $this->ukrposhta->getDeliveryPrice($cost_data);

                            $cost = isset($result['deliveryPrice']) ? $result['deliveryPrice'] : 0;
                        }

						if ($method['tariff_calculation'] && !$cost) {
							$cost = $this->tariffCalculation($code_parts[0], $code_parts[1], $address['zone_id'], $departure['weight'], $total);
						}
									
						if ($cost && $this->currency->getValue('UAH') && $this->currency->getValue('UAH') != 1) {
							$cost /= $this->currency->getValue('UAH');
						}
					}

					// Period
					if ($method['delivery_period'] && $address['zone_id']) {
						$period = $this->getDeliveryPeriod($code_parts[0], $address['zone_id']);
					}

                    if ($period) {
                        $text_period = $this->language->get('text_period') . $this->plural_tool($period, array($this->language->get('text_day_1'), $this->language->get('text_day_2'), $this->language->get('text_day_3')));
                    } else {
                        $text_period = '';
                    }
					
					// Image
					if ($this->settings['image']) {
						if ($this->settings['image_output_place'] == 'img_key') {
							$img = $url . 'image/' . $this->settings['image'];
						}
					}
					
					// Text
					if ($cost) {
						$text = $this->currency->format($this->tax->calculate($cost, $method['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} elseif ($method['free_shipping'] && $total >= $method['free_shipping']) {
						$text = $method['free_cost_text'][$this->config->get('config_language_id')];
					} else {
						$text = '';
					}

					$quote_data[$code] = array(
						'code'			=> 'ukrposhta.' . $code,
						'title'			=> $description,
						'img'			=> $img,
						'cost'			=> $cost,
						'tax_class_id'	=> $method['tax_class_id'],
						'text'			=> $text,
						'text_period'	=> $text_period
					);
				}
			}	
		}
		
		if ($this->settings['image'] && $this->settings['image_output_place'] == 'title') {
			$title = '<img src="' . $url . 'image/' . $this->settings['image'] . '" width="36" height="36" border="0" style="display:inline-block;margin:3px;">'. $this->language->get('text_title');
		} else {
			$title = $this->language->get('text_title');
		}

        if ($quote_data) {
            return array(
                'code'       => 'ukrposhta',
                'title'      => $title,
                'quote'      => $quote_data,
                'sort_order' => $this->config->get('ukrposhta_sort_order'),
                'error'      => false
            );
        }
	}

    private function getTotal($products) {
        $total  = 0;
        $totals = array();
        $taxes  = $this->cart->getTaxes();

        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        foreach ($products as $product) {
            $total += $product['total'];
        }

        if (isset($this->session->data['coupon'])) {
            if (version_compare(VERSION, '2.3', '>=')) {
                $this->load->model('extension/total/coupon');

                $this->model_extension_total_coupon->getTotal($total_data);
            } elseif (version_compare(VERSION, '2.2', '>=')) {
                $this->load->model('total/coupon');

                $this->model_total_coupon->getTotal($total_data);
            } else {
                $this->load->model('total/coupon');

                $this->model_total_coupon->getTotal($totals, $total, $taxes);
            }
        }

        if (isset($this->session->data['voucher'])) {
            if (version_compare(VERSION, '2.3', '>=')) {
                $this->load->model('extension/total/voucher');

                $this->model_extension_total_voucher->getTotal($total_data);
            } elseif (version_compare(VERSION, '2.2', '>=')) {
                $this->load->model('total/voucher');

                $this->model_total_voucher->getTotal($total_data);
            } else {
                $this->load->model('total/voucher');

                $this->model_total_voucher->getTotal($totals, $total, $taxes);
            }
        }

        if (isset($this->session->data['card'])) {
            if (version_compare(VERSION, '2.3', '>=')) {
                $this->load->model('extension/total/membership_card');

                $this->model_extension_total_membership_card->getTotal($total_data);
            } elseif (version_compare(VERSION, '2.2', '>=')) {
                $this->load->model('total/membership_card');

                $this->model_total_membership_card->getTotal($total_data);
            } else {
                $this->load->model('total/membership_card');

                $this->model_total_membership_card->getTotal($totals, $total, $taxes);
            }
        }

        return $total;
    }
	
	private function tariffCalculation($delivery_type, $service_type, $zone_id, $weight, $total) {
	    $cost = 30;

        if ($zone_id == $this->settings['sender_region']) {
            $tariff_zone = 'region';
        } else {
            $tariff_zone = 'Ukraine';
        }

        foreach($this->settings['tariffs'][$delivery_type] as $v) {
            if (is_array($v)) {
                if ($weight <= $v['weight']) {
                    $cost = (double)$v[$tariff_zone];

                    if($this->settings['sender_address_pick_up']) {
                        $cost += (double)$v['overpay_doors_pickup'];
                    }

                    if ($service_type == 'd') {
                        $cost += (double)$v['overpay_doors_delivery'];
                    }

                    break;
                }
            }
        }

        if ($this->settings['tariffs'][$delivery_type]['declared_cost_commission'] && $total > $this->settings['tariffs'][$delivery_type]['declared_cost_bottom_line']) {
            $cost += $total * (double)$this->settings['tariffs'][$delivery_type]['declared_cost_commission'] / 100;
        }

        if ($this->settings['tariffs'][$delivery_type]['declared_cost_minimum_commission'] && $total > $this->settings['tariffs'][$delivery_type]['declared_cost_bottom_line']) {
            $cost += (double)$this->settings['tariffs'][$delivery_type]['declared_cost_minimum_commission'];
        }

        if ($this->settings['tariffs'][$delivery_type]['discount']) {
            $cost -= $cost * (double)$this->settings['tariffs'][$delivery_type]['discount'] / 100;
        }

		return round($cost, 2);
	}

	private function getDeliveryPeriod($delivery_type, $zone_id) {
	    if ($zone_id == $this->settings['sender_region']) {
            $period = $this->settings['tariffs'][$delivery_type]['region_delivery_period'];
        } else {
            $period = $this->settings['tariffs'][$delivery_type]['ukraine_delivery_period'];
        }

	    return $period;
    }
	
	protected function plural_tool($number, $text) {
		$cases = array (2, 0, 1, 1, 1, 2);
		
		return $number . ' ' . $text[(($number % 100) > 4 && ($number % 100) < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}

class ModelExtensionShippingUkrPoshta extends ModelShippingUkrPoshta {

}