<?php
class ModelShippingUkrPoshta extends Model {
    private $extension = 'ukrposhta';
    private $settings;

	public function __construct($registry) {
     	parent::__construct($registry);
     	
     	require_once(DIR_SYSTEM . 'helper/ukrposhta.php');

     	$registry->set('ukrposhta', new Ukrposhta($registry));

        if (version_compare(VERSION, '3', '>=')) {
            $this->settings = $this->config->get('shipping_' . $this->extension);
        } else {
            $this->settings = $this->config->get($this->extension);
        }
    }
    
	function getQuote($address) {
        if (version_compare(VERSION, '2.3', '>=')) {
            $this->load->language('extension/shipping/' . $this->extension);
        } else {
            $this->load->language('shipping/' . $this->extension);
        }

		$quote_data = array();
        $url        = $this->config->get('config_secure') ? HTTPS_SERVER : HTTP_SERVER;
		$products 	= $this->cart->getProducts();
        $departure 	= $this->ukrposhta->getDeparture($products);
        $totals 	= $this->getTotals();
        $sub_total  = $totals['total'];

        foreach ($address as $k => &$v) {
            if (empty($v) && !empty($this->session->data['shipping_address'][$k])) {
                $v = $this->session->data['shipping_address'][$k];
            }
        }

        if (!empty($address['zone'])) {
            $recipient_region = $this->ukrposhta->getRegionID($address['zone']);
        } else {
            $recipient_region = '';
        }

        if (!empty($address['city'])) {
            $recipient_city = $this->ukrposhta->getCityID(trim($address['city']), $recipient_region);
        } else {
            $recipient_city = '';
        }

        if (!empty($address['address_1'])) {
            $recipient_department = $this->ukrposhta->getDepartmentID(trim($address['address_1']), $recipient_city);
        } else {
            $recipient_department = '';
        }

        if (!isset($address['postcode'])) {
            $address['postcode'] = '';
        }

        if (empty($address['postcode']) && $recipient_department) {
            $address['postcode'] = $this->ukrposhta->getDepartmentIndex($recipient_department);
        }

		if (is_array($this->settings['shipping_methods'])) {
		    foreach ($this->settings['shipping_methods'] as $code => $method) {
				if (!$method['status']) {
					continue;
				}
				
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$method['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
				
				if ($method['geo_zone_id'] && !$query->num_rows) {
					$status = false;
				} elseif ($sub_total < $method['minimum_order_amount']) {
					$status = false;
				} elseif ($method['maximum_order_amount'] && $sub_total > $method['maximum_order_amount']) {
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

					/* Cost	*/
					if ($method['cost'] && (!$method['free_shipping'] || $sub_total < $method['free_shipping'])) {
                        if ($method['api_calculation'] && $address['postcode'] && $departure['weight']) {
                            if ($this->settings['sender_address_pick_up']) {
                                $delivery_type = 'D';
                            } else {
                                $delivery_type = 'W';
                            }

                            if ($code_parts[1] == 'department') {
                                $delivery_type .= '2W';
                            } elseif ($code_parts[1] == 'doors') {
                                $delivery_type .= '2D';
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
                                'deliveryType'	=> $delivery_type,
                                'weight'		=> ($departure['weight'] * 1000),
                                'length'		=> $departure['length'],
                                'declaredPrice' => $sub_total
                            );

                            if ($this->settings['delivery_notification']) {
                                $cost_data['withDeliveryNotification'] = true;
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

                            if (isset($result['deliveryPrice'])) {
                                $cost = $result['deliveryPrice'];
                            } else {
                                $cost = 0;
                            }
                        }

						if ($method['tariff_calculation'] && !$cost) {
							$cost = $this->tariffCalculation($recipient_region, $address['postcode'], $departure['weight'], $sub_total, $code_parts[0], $code_parts[1]);
						}

                        /* Currency correcting */
						if ($cost && $this->currency->getValue('UAH') && $this->currency->getValue('UAH') != 1) {
							$cost /= $this->currency->getValue('UAH');
						}
					}

					/* Period */
					if ($method['delivery_period']) {
						$period = $this->getDeliveryPeriod($recipient_region, $address['postcode'], $code_parts[0], $code_parts[1]);
					}

                    if ($period) {
                        $text_period = $this->language->get('text_period') . $this->plural_tool((int)$period, array($this->language->get('text_day_1'), $this->language->get('text_day_2'), $this->language->get('text_day_3')));
                    } else {
                        $text_period = '';
                    }
					
					/* Image */
					if ($this->settings['image']) {
						if ($this->settings['image_output_place'] == 'img_key') {
							$img = $url . 'image/' . $this->settings['image'];
						}
					}
					
					/* Text */
					if ($cost) {
						$text = $this->currency->format($this->tax->calculate($cost, $method['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} elseif ($method['free_shipping'] && $sub_total >= $method['free_shipping']) {
						$text = $method['free_cost_text'][$this->config->get('config_language_id')];
					} else {
						$text = '';
					}

					$quote_data[$code] = array(
						'code'			=> $this->extension . '.' . $code,
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
            $shipping = array(
                'code'       => $this->extension,
                'title'      => $title,
                'quote'      => $quote_data,
                'sort_order' => '',
                'error'      => false
            );

            if (version_compare(VERSION, '3', '>=')) {
                $shipping['sort_order'] = $this->config->get('shipping_' . $this->extension . '_sort_order');
            } else {
                $shipping['sort_order'] = $this->config->get($this->extension . '_sort_order');
            }

            return $shipping;
        }
	}

    private  function getTotals() {
        $extensions = array();
        $total      = 0;
        $totals     = array();
        $taxes      = $this->cart->getTaxes();

        $total_data = array(
            'totals' => &$totals,
            'taxes'  => &$taxes,
            'total'  => &$total
        );

        if (version_compare(VERSION, '2', '<') || version_compare(VERSION, '3', '>=')) {
            $this->load->model('setting/extension');

            $result = $this->model_setting_extension->getExtensions('total');
        } else {
            $this->load->model('extension/extension');

            $result = $this->model_extension_extension->getExtensions('total');
        }

        foreach ($result as $k => $v) {
            if (version_compare(VERSION, '3', '>=')) {
                if ($this->config->get('total_' . $v['code'] . '_status')) {
                    $extensions[$this->config->get('total_' . $v['code'] . '_sort_order')] = $v;
                }
            } else {
                if ($this->config->get($v['code'] . '_status')) {
                    $extensions[$this->config->get($v['code'] . '_sort_order')] = $v;
                }
            }
        }

        ksort($extensions);

        foreach ($extensions as $v) {
            if ($v['code'] == 'shipping') {
                continue;
            }

            if (version_compare(VERSION, '2.3', '>=')) {
                $this->load->model('extension/total/' . $v['code']);

                $this->{'model_extension_total_' . $v['code']}->getTotal($total_data);
            } elseif (version_compare(VERSION, '2.2', '>=')) {
                $this->load->model('total/' . $v['code']);

                $this->{'model_total_' . $v['code']}->getTotal($total_data);
            } else {
                $this->load->model('total/' . $v['code']);

                $this->{'model_total_' . $v['code']}->getTotal($totals, $total, $taxes);
            }
        }

        if ($this->currency->getValue('UAH') != 1) {
            $total_data['total'] *= $this->currency->getValue('UAH');

            foreach ($total_data['totals'] as $k => $v) {
                $total_data['totals'][$k]['value'] *= $this->currency->getValue('UAH');
            }
        }

        return $total_data;
    }
	

    private function tariffCalculation($region, $postcode, $weight, $declared_cost, $delivery_type, $service_type) {
        if (!in_array($delivery_type, array('express', 'standard'))) {
            $delivery_type = 'express';
        }

	    /* The minimum cost as a parcel is 1 kg */
	    if ($delivery_type == 'express') {
            $cost = 40;
        } else {
            $cost = 35;
        }

        if ($postcode && $postcode == $this->settings['sender_postcode'] && $service_type == 'department') {
            $tariff_zone  = 'department';
        } elseif ($region && $region == $this->settings['sender_region']) {
            $tariff_zone  = 'region';
        } else {
            $tariff_zone  = 'Ukraine';
        }

        foreach($this->settings['tariffs'][$delivery_type] as $v) {
            if (is_array($v)) {
                if ($weight <= $v['weight']) {
                    $cost = (double)$v[$tariff_zone];

                    if($this->settings['sender_address_pick_up']) {
                        $cost += (double)$v['overpay_doors_pickup'];
                    }

                    if ($service_type == 'doors') {
                        $cost += (double)$v['overpay_doors_delivery'];
                    }

                    break;
                }
            }
        }

        if ($this->settings['tariffs'][$delivery_type]['declared_cost_commission'] && $declared_cost > $this->settings['tariffs'][$delivery_type]['declared_cost_commission_bottom']) {
            $cost += $declared_cost * (double)$this->settings['tariffs'][$delivery_type]['declared_cost_commission'] / 100;
        } elseif ($declared_cost && $this->settings['tariffs'][$delivery_type]['declared_cost_minimum_commission'] && $declared_cost <= $this->settings['tariffs'][$delivery_type]['declared_cost_commission_bottom']) {
            $cost += $this->settings['tariffs'][$delivery_type]['declared_cost_minimum_commission'];
        }

        if ($this->settings['tariffs'][$delivery_type]['discount']) {
            $cost -= $cost * (double)$this->settings['tariffs'][$delivery_type]['discount'] / 100;
        }

		return round($cost);
	}

	private function getDeliveryPeriod($region, $postcode, $delivery_type, $service_type) {
        if ($postcode && $postcode == $this->settings['sender_postcode'] && $service_type == 'department') {
            $period = $this->settings['tariffs'][$delivery_type]['department_delivery_period'];
        } elseif ($region && $region == $this->settings['sender_region']) {
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