<?php 
class ModelSaleOrderproex extends Model {
	public function getExport($orders = array()) {
		$export_data = array();
		$export_setting = array();
		$file_name = false;
	
		$orderpro_export = $this->config->get('orderpro_export') ? $this->config->get('orderpro_export') : array();
		$one_list = $this->config->get('orderpro_export_orders_one') ? 1 : 0;
	
		foreach ($orderpro_export as $key => $value) {
			if (!empty($value['status'])) {
				$export_setting[$key] = $value;
			}
	
			if ($key == 'products') {
				$product_setting = array();
	
				foreach ($value as $pkey => $pvalue) {
					if (!empty($pvalue['status'])) {
						$product_setting[$pkey] = $pvalue;
					}
				}
	
				if ($product_setting) {
					$export_setting['products'] = $product_setting;
				}
			}
		}
	
		if ($export_setting) {
			if ($orders) {
				sort($orders);
	
				foreach ($orders as $order_id) {
					$order_info = $this->getOrder($order_id);
	
					if ($order_info) {
						$export_data[] = $order_info;
					}
				}
			}
	
			if ($export_data && is_file(DIR_SYSTEM . "library/PHPExcel.php")) {
				require_once(DIR_SYSTEM . "library/PHPExcel.php");
				require_once(DIR_SYSTEM . "library/PHPExcel/Writer/Excel5.php");
	
				$xls = new PHPExcel();
	
				foreach ($export_data as $okey => $order_data) {
					$order_id = $order_data['order_id'];
	
					$order_columns = array();
					$order_column = array();
	
					if ($order_data) {
						foreach ($order_data as $key => $value) {
							if ($key != 'products') {
								if (array_key_exists($key, $export_setting)) {
									$order_column[] = array(
										'name'       => $export_setting[$key]['name'],
										'sort_order' => $export_setting[$key]['sort_order'],
										'value'      => $value
									);
								}
							}
						}
					}
	
					if (!empty($export_setting['products']) && $order_data['products']) {
						foreach ($order_data['products'] as $pkey => $product) {
							$product_column = array();
	
							foreach ($product as $key => $value) {
								if (array_key_exists($key, $export_setting['products'])) {
									$product_column[] = array(
										'name'       => $export_setting['products'][$key]['name'],
										'sort_order' => $export_setting['products'][$key]['sort_order'],
										'value'      => $value
									);
								}
							}
	
							$merge_columns = array_merge($order_column, $product_column);
	
							if ($merge_columns) {
								$sort_order = array();
	
								foreach ($merge_columns as $key => $value) {
									$sort_order[$key] = $value['sort_order'];
								}
	
								array_multisort($sort_order, SORT_ASC, $merge_columns);
	
								$order_columns[] = $merge_columns;
							}
						}
					} else {
						if ($order_column) {
							$sort_order = array();
	
							foreach ($order_column as $key => $value) {
								$sort_order[$key] = $value['sort_order'];
							}
	
							array_multisort($sort_order, SORT_ASC, $order_column);
	
							$order_columns[] = $order_column;
						}
					}
	
					if ($order_columns) {
						if (!$one_list && $okey) {
							$xls->createSheet($okey);
						}
	
						if (!$one_list || ($one_list && !$okey)) {
							$xls->setActiveSheetIndex($okey);
							$sheet = $xls->getActiveSheet();
	
							if (!$one_list) {
								$sheet_name = 'Order_' . $order_id;
							} else {
								$sheet_name = 'Orders';
							}
	
							$sheet->setTitle($sheet_name);
	
							$line = 1;
	
							$header_styles = array(
								'borders' => array(
									'allborders' => array(
										'style'  => PHPExcel_Style_Border::BORDER_THIN
									)
								),
								'fill'   => array(
									'type'   => PHPExcel_Style_Fill::FILL_SOLID,
									'color'  => array(
										'rgb' => 'EEEEEE'
									)
								),
								'font'  => array(
									'bold' => true
								),
								'alignment' => array(
									'horizontal'   => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
									'vertical'     => PHPExcel_Style_Alignment::VERTICAL_CENTER
								)
							);
	
							foreach ($order_columns[0] as $key => $value) {
								$sheet->setCellValueByColumnAndRow($key, $line, $value['name']);
								$sheet->getStyleByColumnAndRow($key, $line)->applyFromArray($header_styles);
								$sheet->getColumnDimensionByColumn($key)->setAutoSize(true);
							}
	
							$sheet->getRowDimension(1)->setRowHeight(22);
						}
	
						if (!$one_list || ($one_list && !$okey)) {
							$line = 2;
						}
	
						foreach ($order_columns as $rows) {
							foreach ($rows as $key => $value) {
								$sheet->setCellValueByColumnAndRow($key, $line, $value['value']);
							}
							$line++;
						}
					}
				}
	
				$file_name = 'Export_' . date('Y-m-d-Hi', time()) . '.xls';
	
				$objWriter = new PHPExcel_Writer_Excel5($xls);
				$objWriter->save(DIR_DOWNLOAD . $file_name);
			}
		}
	
		return $file_name;
	}
	
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT *, CONCAT(o.lastname, ' ', o.firstname) AS customer, CONCAT(o.payment_lastname, ' ', o.payment_firstname) AS payment_customer, CONCAT(o.shipping_lastname, ' ', o.shipping_firstname) AS shipping_customer, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
	
		if ($order_query->num_rows) {
			$reward = 0;
			$products = array();
	
			$order_product_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_product` WHERE order_id = '" . (int)$order_id . "'");
	
			foreach ($order_product_query->rows as $product) {
				$reward += $product['reward'];
	
				$options = '';
	
				$product_options_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_option` WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
	
				if ($product_options_query->num_rows) {
					foreach ($product_options_query->rows as $option) {
						if ($option['type'] == 'file') {
							$oupload_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "upload` WHERE `code` = '" . $this->db->escape($option['value']) . "'");
	
							if ($oupload_query->num_rows) {
								$options .= $option['name'] . ': ' . $oupload_query->row['name'] . ', ';
							} else {
								$options .= $option['name'] . ': ' . $option['value'] . ', ';
							}
						} else {
							$options .= $option['name'] . ': ' . $option['value'] . ', ';
						}
					}
				}
	
				$product['options'] = rtrim($this->clearTags($options), ', ');
	
				$product['name'] = $this->clearTags($product['name']);
				$product['model'] = $this->clearTags($product['model']);
				$product['sku'] = $this->clearTags($product['sku']);
				$product['ean'] = $this->clearTags($product['ean']);
				$product['upc'] = $this->clearTags($product['upc']);
				$product['mpn'] = $this->clearTags($product['mpn']);
				$product['isbn'] = $this->clearTags($product['isbn']);
				$product['jan'] = $this->clearTags($product['jan']);
				$product['location'] = $this->clearTags($product['location']);
	
				$products[] = $product;
			}
	
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");
	
			if ($country_query->num_rows) {
				$payment_iso_code_2 = $country_query->row['iso_code_2'];
				$payment_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$payment_iso_code_2 = '';
				$payment_iso_code_3 = '';
			}
	
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");
	
			if ($zone_query->num_rows) {
				$payment_zone_code = $zone_query->row['code'];
			} else {
				$payment_zone_code = '';
			}
	
			$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");
	
			if ($country_query->num_rows) {
				$shipping_iso_code_2 = $country_query->row['iso_code_2'];
				$shipping_iso_code_3 = $country_query->row['iso_code_3'];
			} else {
				$shipping_iso_code_2 = '';
				$shipping_iso_code_3 = '';
			}
	
			$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");
	
			if ($zone_query->num_rows) {
				$shipping_zone_code = $zone_query->row['code'];
			} else {
				$shipping_zone_code = '';
			}
	
			$customer_group = '';
	
			if ($order_query->row['customer_group_id']) {
				$customer_group_query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "customer_group_description` WHERE customer_group_id = '" . (int)$order_query->row['customer_group_id'] . "' AND language_id = '" . (int)$order_query->row['language_id'] . "'");
				
				if ($customer_group_query->num_rows) {
					$customer_group = $customer_group_query->row['name'];
				}
			}
	
			if ($order_query->row['affiliate_id']) {
				$affiliate_id = $order_query->row['affiliate_id'];
			} else {
				$affiliate_id = 0;
			}
	
			$this->load->model('customer/customer');
	
			$affiliate_info = $this->model_customer_customer->getAffiliate($affiliate_id);
	
			if ($affiliate_info) {
				$affiliate_firstname = $affiliate_info['firstname'];
				$affiliate_lastname = $affiliate_info['lastname'];
	
				if ($affiliate_info['lastname'] && $affiliate_info['firstname']) {
					$affiliate = $affiliate_info['lastname'] . ' ' . $affiliate_info['firstname'];
				} elseif ($affiliate_info['lastname']) {
					$affiliate = $affiliate_info['lastname'];
				} elseif ($affiliate_info['firstname']) {
					$affiliate = $affiliate_info['firstname'];
				} else {
					$affiliate = '';
				}
			} else {
				$affiliate_firstname = '';
				$affiliate_lastname = '';
				$affiliate = '';
			}
	
			$this->load->model('localisation/language');
	
			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);
	
			if ($language_info) {
				$language_code = $language_info['code'];
				$language_directory = $language_info['code'];
			} else {
				$language_code = 'en-gb';
				$language_directory = 'en-gb';
			}
	
			$custom_field = json_decode($order_query->row['custom_field'], true);
			$payment_custom_field = json_decode($order_query->row['payment_custom_field'], true);
			$shipping_custom_field = json_decode($order_query->row['shipping_custom_field'], true);
	
			return array(
				'order_id'                => $order_query->row['order_id'],
				'invoice_no'              => $order_query->row['invoice_no'],
				'invoice_prefix'          => $order_query->row['invoice_prefix'],
				'store_id'                => $order_query->row['store_id'],
				'store_name'              => $this->clearTags($order_query->row['store_name']),
				'store_url'               => $order_query->row['store_url'],
				'customer_id'             => $order_query->row['customer_id'],
				'customer'                => $this->clearTags($order_query->row['customer']),
				'customer_group_id'       => $order_query->row['customer_group_id'],
				'customer_group'          => $this->clearTags($customer_group),
				'firstname'               => $this->clearTags($order_query->row['firstname']),
				'lastname'                => $this->clearTags($order_query->row['lastname']),
				'telephone'               => $order_query->row['telephone'],
				'fax'                     => $order_query->row['fax'],
				'email'                   => $order_query->row['email'],
				'custom_field'            => $custom_field,
				'payment_firstname'       => $this->clearTags($order_query->row['payment_firstname']),
				'payment_lastname'        => $this->clearTags($order_query->row['payment_lastname']),
				'payment_customer'        => $this->clearTags($order_query->row['payment_customer']),
				'payment_company'         => $this->clearTags($order_query->row['payment_company']),
				'payment_address_1'       => $this->clearTags($order_query->row['payment_address_1']),
				'payment_address_2'       => $this->clearTags($order_query->row['payment_address_2']),
				'payment_postcode'        => $order_query->row['payment_postcode'],
				'payment_city'            => $this->clearTags($order_query->row['payment_city']),
				'payment_zone_id'         => $order_query->row['payment_zone_id'],
				'payment_zone'            => $this->clearTags($order_query->row['payment_zone']),
				'payment_zone_code'       => $payment_zone_code,
				'payment_country_id'      => $order_query->row['payment_country_id'],
				'payment_country'         => $this->clearTags($order_query->row['payment_country']),
				'payment_iso_code_2'      => $payment_iso_code_2,
				'payment_iso_code_3'      => $payment_iso_code_3,
				'payment_address_format'  => $order_query->row['payment_address_format'],
				'payment_custom_field'    => $payment_custom_field,
				'payment_method'          => $this->clearTags($order_query->row['payment_method']),
				'payment_code'            => $order_query->row['payment_code'],				
				'shipping_firstname'      => $this->clearTags($order_query->row['shipping_firstname']),
				'shipping_lastname'       => $this->clearTags($order_query->row['shipping_lastname']),
				'shipping_customer'       => $this->clearTags($order_query->row['shipping_customer']),
				'shipping_company'        => $this->clearTags($order_query->row['shipping_company']),
				'shipping_address_1'      => $this->clearTags($order_query->row['shipping_address_1']),
				'shipping_address_2'      => $this->clearTags($order_query->row['shipping_address_2']),
				'shipping_postcode'       => $order_query->row['shipping_postcode'],
				'shipping_city'           => $this->clearTags($order_query->row['shipping_city']),
				'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
				'shipping_zone'           => $this->clearTags($order_query->row['shipping_zone']),
				'shipping_zone_code'      => $shipping_zone_code,
				'shipping_country_id'     => $order_query->row['shipping_country_id'],
				'shipping_country'        => $this->clearTags($order_query->row['shipping_country']),
				'shipping_iso_code_2'     => $shipping_iso_code_2,
				'shipping_iso_code_3'     => $shipping_iso_code_3,
				'shipping_address_format' => $order_query->row['shipping_address_format'],
				'shipping_custom_field'   => $shipping_custom_field,
				'shipping_method'         => $this->clearTags($order_query->row['shipping_method']),
				'shipping_code'           => $order_query->row['shipping_code'],
				'comment'                 => $this->clearTags($order_query->row['comment']),
				'total'                   => $order_query->row['total'],
				'reward'                  => $reward,
				'products'                => $products,
				'order_status_id'         => $order_query->row['order_status_id'],
				'order_status'            => $this->clearTags($order_query->row['order_status']),
				'affiliate_id'            => $order_query->row['affiliate_id'],
				'affiliate_firstname'     => $this->clearTags($affiliate_firstname),
				'affiliate_lastname'      => $this->clearTags($affiliate_lastname),
				'affiliate'               => $this->clearTags($affiliate),
				'commission'              => $order_query->row['commission'],
				'marketing_id'            => $order_query->row['marketing_id'],
				'tracking'                => $order_query->row['tracking'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'language_directory'      => $language_directory,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'ip'                      => $order_query->row['ip'],
				'forwarded_ip'            => $order_query->row['forwarded_ip'], 
				'user_agent'              => $this->clearTags($order_query->row['user_agent']),	
				'accept_language'         => $order_query->row['accept_language'],					
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			);
	
		} else {
			return false;
		}
	}
	
   	public function clearTags($str) {
		$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

		$str = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', ' ', $str);
		$str = preg_replace('/<\s*style[^>]*>.*?<\s*\/\s*style\s*>/is', ' ', $str);
		$str = preg_replace('/<\s*select[^>]*>.*?<\s*\/\s*select\s*>/is', ' ', $str);

		$str = strip_tags($str);
		$str = preg_replace('/(<([^>]+)>)/U', ' ', $str);
		$str = preg_replace('/\s\s+/', ' ', $str);

		return $str;
  	}
}
?>