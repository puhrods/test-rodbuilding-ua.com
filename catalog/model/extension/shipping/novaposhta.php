<?php
class ModelShippingNovaPoshta extends Model {
    private $extension = 'novaposhta';
    private $settings;

	public function __construct($registry) {
     	parent::__construct($registry);
     	
     	require_once(DIR_SYSTEM . 'helper/novaposhta.php');

     	$registry->set('novaposhta', new NovaPoshta($registry));

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
        $departure 	= $this->novaposhta->getDeparture($products);
        $totals 	= $this->getTotals();
        $sub_total  = $totals['total'];

        if (!empty($this->settings['calculate_declared_cost_commision'])) {
            if ($this->settings['declared_cost_default'] && isset($this->session->data['payment_method']['code']) && !in_array($this->session->data['payment_method']['code'], $this->settings['payment_cod'])) {
                $declared_cost = $this->settings['declared_cost_default'];
            } else {
                $declared_cost = $this->novaposhta->getDeclaredCost($totals['totals']);
            }
        } else {
            $declared_cost = 0;
        }

        if (isset($this->settings['calculate_cod']) && ($this->settings['calculate_cod'] == 'all_payment_methods' || ($this->settings['calculate_cod'] == 'enabled' && isset($this->session->data['payment_method']['code']) && in_array($this->session->data['payment_method']['code'], $this->settings['payment_cod'])))) {
            $cod = round($sub_total);
        } else {
            $cod = 0;
        }

        foreach ($address as $k => &$v) {
            if (empty($v) && !empty($this->session->data['shipping_address'][$k])) {
                $v = $this->session->data['shipping_address'][$k];
            }
        }

        if (!empty($address['zone'])) {
            $recipient_region = $this->novaposhta->getRegionRef($address['zone']);
        } else {
            $recipient_region = '';
        }

        if (!empty($address['city'])) {
            $recipient_city = $this->novaposhta->getCityRef(trim($address['city']), $recipient_region);
        } else {
            $recipient_city = '';
        }

        if (!empty($address['address_1'])) {
            $recipient_department = $this->novaposhta->getDepartmentRef(trim($address['address_1']), $recipient_city);
        } else {
            $recipient_department = '';
        }

        if ($this->settings['calculate_volume']) {
            $volume_weight = $departure['volume'] * 250;
        } else {
            $volume_weight = 0;
        }

        if ($this->settings['autodetection_departure_type']) {
            $departure_type = $this->novaposhta->getDepartureType($departure);
        } elseif (!empty($this->settings['departure_type'])) {
            $departure_type = $this->settings['departure_type'];
        } else {
            $departure_type = 'Parcel';
        }

        if ($this->settings['seats_amount']) {
            $seats = $this->settings['seats_amount'];
        } else {
		    $seats = $this->novaposhta->getDepartureSeats($products);
        }

        if ($seats == 1) {
            $options_seat[0] = array(
                'weight'           => $departure['weight'],
                'volumetricLength' => $departure['length'],
                'volumetricWidth'  => $departure['width'],
                'volumetricHeight' => $departure['height'],
                'volumetricVolume' => max(round($departure['length'] * $departure['width'] * $departure['height'] / 1000000, 4), 0.0004)
            );

            if ($this->settings['manual_processing']) {
                $options_seat[0]['specialCargo'] = 1;
            }

            if ($this->settings['pack'] && !empty($this->settings['pack_type'])) {
                if ($this->settings['autodetection_pack_type']) {
                    $pack_type = $this->novaposhta->getPackType($departure);
                } else {
                    $pack_type = $this->settings['pack_type'][0];
                }

                $options_seat[0]['packRef'] = $pack_type;
            }
        } else {
            foreach ($departure['parcels'] as $i => $parcel) {
                $options_seat[$i] = array(
                    'weight'           => $parcel['weight'],
                    'volumetricLength' => $parcel['length'],
                    'volumetricWidth'  => $parcel['width'],
                    'volumetricHeight' => $parcel['height'],
                    'volumetricVolume' => max(round($parcel['length'] * $parcel['width'] * $parcel['height'] / 1000000, 4), 0.0004)
                );

                if ($this->settings['manual_processing']) {
                    $options_seat[$i]['specialCargo'] = 1;
                }

                if ($this->settings['pack'] && !empty($this->settings['pack_type'])) {
                    if ($this->settings['autodetection_pack_type']) {
                        $pack_type = $this->novaposhta->getPackType($parcel);
                    } else {
                        $pack_type = $this->settings['pack_type'][0];
                    }

                    $options_seat[$i]['packRef'] = $pack_type;
                }
            }
        }

        $this->session->data['shippingdata']['cart_weight'] = $departure['weight'];

        foreach ((array)$this->settings['shipping_methods'] as $code => $method) {
            if (empty($method['status'])) {
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
                $cost   = 0;
                $period = 0;
                $img    = '';

                if ($method['name'][$this->config->get('config_language_id')]) {
                    $description = $method['name'][$this->config->get('config_language_id')];
                } else {
                    $description = $this->language->get('text_description_' . $code);
                }

                if ($code == 'department' || $code == 'poshtomat') {
                    $service_type = $this->settings['sender_address_type'] . 'Warehouse';
                } else {
                    $service_type = $this->settings['sender_address_type'] . ucfirst($code);
                }

                /* Cost */
                if ($method['cost'] && (!$method['free_shipping'] || $sub_total < $method['free_shipping'])) {
                    if ($method['api_calculation'] && $recipient_city && $departure['weight']) {
                        $properties_cost = array (
                            'Sender'		   => $this->settings['sender'],
                            'CitySender'	   => $this->settings['sender_city'],
                            'CityRecipient'    => $recipient_city,
                            'RecipientAddress' => $recipient_department,
                            'ServiceType'      => $service_type,
                            'CargoType'        => $departure_type,
                            'Weight'		   => $departure['weight'],
                            'VolumeWeight'	   => $volume_weight,
                            'SeatsAmount'      => $seats,
                            'OptionsSeat'      => $options_seat,
                            'Cost'			   => $declared_cost,
                            'DateTime' 		   => date('d.m.Y')
                        );

                        if ($this->settings['sender_address_pick_up']) {
                            $properties_cost['SenderAddress'] = $this->settings['sender_address'];
                        } else {
                            $properties_cost['SenderAddress'] = $this->settings['sender_department'];
                        }

                        if ($cod) {
                            $properties_cost['RedeliveryCalculate'] = array(
                                'CargoType' => 'Money',
                                'Amount' => $cod
                            );
                        }
								
                        $cost = $this->novaposhta->getDocumentPrice($properties_cost);
                    }

                    if ($method['tariff_calculation'] && !$cost) {
                        $cost = $this->tariffCalculation($recipient_region, $recipient_city, $recipient_department, $departure['weight'], $volume_weight, $declared_cost, $cod, $code, $service_type, lcfirst($departure_type), $totals);
                    }
									
                    /* Currency correcting */
                    if ($cost && $this->currency->getValue('UAH') && $this->currency->getValue('UAH') != 1) {
                        $cost /= $this->currency->getValue('UAH');
                    }
                }

                /* Period */
                if ($method['delivery_period']) {
                    if ($recipient_city) {
                        $properties_period = array (
                            'CitySender'	=> $this->settings['sender_city'],
                            'CityRecipient'	=> $recipient_city,
                            'ServiceType'	=> $service_type,
                            'CargoType'     => $departure_type,
                            'DateTime' 		=> date('d.m.Y')
                        );

                        $period = $this->novaposhta->getDocumentDeliveryDate($properties_period);
                    }

                    if (!$period) {
                        $period = $this->getDeliveryPeriod($recipient_region, $recipient_city, $recipient_department, lcfirst($departure_type));
                    }
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
					
                /* Period text */
                if ($period) {
                    $text_period = $this->language->get('text_period') . $this->plural_tool((int)$period, array($this->language->get('text_day_1'), $this->language->get('text_day_2'), $this->language->get('text_day_3')));
                } else {
                    $text_period = '';
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

	private function tariffCalculation($region, $city, $department, $weight, $volume_weight, $declared_cost, $cod, $delivery_type, $service_type, $departure_type, $totals) {
        if (!in_array($departure_type, array('parcel'))) {
            $departure_type = 'parcel';
        }

	    if ($service_type == 'DoorsDoors') {
            $cost = 90;
        } elseif ($service_type == 'WarehouseDoors' || $service_type == 'DoorsWarehouse') {
            $cost = 70;
        } else {
            $cost = 50;
        }

        if ($this->settings['sender_address_pick_up']) {
            $sender_address = $this->settings['sender_address'];
        } else {
            $sender_address = $this->settings['sender_department'];
        }

        if ($department && $department == $sender_address && $delivery_type == 'department') {
            $tariff_zone  = 'department';
        } elseif ($city && $city == $this->settings['sender_city']) {
            $tariff_zone  = 'city';
        } elseif ($region && $region == $this->settings['sender_region']) {
            $tariff_zone  = 'region';
        } else {
            $tariff_zone  = 'Ukraine';
        }

		if ($volume_weight > $weight) {
			$weight = $volume_weight;
		}

        foreach($this->settings['tariffs'][$departure_type] as $v) {
            if (is_array($v)) {
                if ($weight <= $v['weight']) {
                    $cost = (double)$v[$tariff_zone];

                    if ($service_type == 'DoorsWarehouse' || $service_type == 'DoorsDoors') {
                        $cost += (double)$v['overpay_doors_pickup'];
                    }

                    if ($service_type == 'WarehouseDoors' || $service_type == 'DoorsDoors') {
                        $cost += (double)$v['overpay_doors_delivery'];
                    }

                    break;
                }
            }
        }

		if ($this->settings['tariffs'][$departure_type]['declared_cost_commission'] && $declared_cost > $this->settings['tariffs'][$departure_type]['declared_cost_commission_bottom']) {
			$cost += $declared_cost * (double)$this->settings['tariffs'][$departure_type]['declared_cost_commission'] / 100;
		} elseif ($declared_cost && $this->settings['tariffs'][$departure_type]['declared_cost_minimum_commission'] && $declared_cost <= $this->settings['tariffs'][$departure_type]['declared_cost_commission_bottom']) {
            $cost += $this->settings['tariffs'][$departure_type]['declared_cost_minimum_commission'];
        }

        if ($this->settings['tariffs'][$departure_type]['discount']) {
            $cost -= $cost * (double)$this->settings['tariffs'][$departure_type]['discount'] / 100;
        }

        if ($cod) {
            $cod_payment = 0;

            foreach((array)$this->settings['tariffs']['cod'] as $v) {
                $base = 0;

                if ($v['delivery_type'] == $delivery_type) {
                    foreach ($totals['totals'] as $total) {
                        if (in_array($total['code'], $v['calculation_base'])) {
                            $base += $total['value'];
                        }
                    }
                }

                if ($base && (!$v['tariff_limit'] || $base <= $v['tariff_limit'])) {
                    if ($v['percent']) {
                        $cod_payment += $base * (double)$v['percent'] / 100;
                    }

                    if ($v['fixed_amount']) {
                        $cod_payment += (double)$v['fixed_amount'];
                    }

                    if ($v['minimum_payment'] > $cod_payment) {
                        $cod_payment = $v['minimum_payment'];
                    }

                    $cost += $cod_payment;

                    break;
                }
            }
        }

		return round($cost);
	}

	private function getDeliveryPeriod($region, $city, $department, $departure_type) {
        if ($this->settings['sender_address_pick_up']) {
            $sender_address = $this->settings['sender_address'];
        } else {
            $sender_address = $this->settings['sender_department'];
        }

        if ($department && $department == $sender_address) {
            $period = $this->settings['tariffs'][$departure_type]['department_delivery_period'];
        } elseif ($city && $city == $this->settings['sender_city']) {
            $period = $this->settings['tariffs'][$departure_type]['city_delivery_period'];
        } elseif ($region && $region == $this->settings['sender_region']) {
            $period = $this->settings['tariffs'][$departure_type]['region_delivery_period'];
        } else {
            $period = $this->settings['tariffs'][$departure_type]['ukraine_delivery_period'];
        }

	    return $period;
    }
	
	protected function plural_tool($number, $text) {
		$cases = array (2, 0, 1, 1, 1, 2);
		
		return $number . ' ' . $text[(($number % 100) > 4 && ($number % 100) < 20) ? 2 : $cases[min($number % 10, 5)]];
    }
}

class ModelExtensionShippingNovaPoshta extends ModelShippingNovaPoshta {

}