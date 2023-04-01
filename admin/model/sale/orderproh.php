<?php 
class ModelSaleOrderproh extends Model {
	public function addOrderHistory($order_id, $hdata = array()) {
		$order_history_id = 0;
		if ($hdata) {
			$this->load->model('sale/orderpro');
			$order_info = $this->model_sale_orderpro->getOrder($order_id);
	
			if (!empty($hdata['order_new'])) {
				$order_new = true;
			} else {
				$order_new = false;
			}
	
			if ($order_info) {
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_directory']);
				$language->load('sale/orderpro');
	
				$new_status_id = $hdata['order_status_id'];
	
				if (!in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && in_array($new_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
					$this->model_sale_orderpro->substractFromStockOrderId($order_id);
					$this->cache->delete('product');
	
					$order_totals = $this->model_sale_orderpro->getOrderTotals($order_id);
	
					//Coupon
					$order_coupon = $this->model_sale_orderpro->getCouponByOrderId($order_id);
	
					if (!$order_coupon) {
						$coupon_code = '';
						$coupon_value = 0;
	
						foreach ($order_totals as $total) {
							if ($total['code'] == 'coupon') {
								$coupon_value = $total['value'];
	
								$start = strpos($total['title'], '(') + 1;
								$end = strrpos($total['title'], ')');
	
								if ($start && $end) {
									$coupon_code = substr($total['title'], $start, $end - $start);
								}
								break;
							}
						}
	
						if ($coupon_code) {
							$coupon_info = $this->model_sale_orderpro->getCouponByCode($coupon_code);
	
							if ($coupon_info) {
								$coupon_data = array(
									'coupon_id'   => $coupon_info['coupon_id'],
									'customer_id' => $order_info['customer_id'],
									'value'       => $coupon_value
								);
	
								$this->model_sale_orderpro->setCouponToOrder($order_id, $coupon_data);
							}
						}
					}
	
					//Voucher
					$order_voucher = $this->model_sale_orderpro->getVoucherByOrderId($order_id);
	
					if (!$order_voucher) {
						$voucher_code = '';
						$voucher_value = 0;
	
						foreach ($order_totals as $total) {
							if ($total['code'] == 'voucher') {
								$voucher_value = $total['value'];
	
								$start = strpos($total['title'], '(') + 1;
								$end = strrpos($total['title'], ')');
	
								if ($start && $end) {
									$voucher_code = substr($total['title'], $start, $end - $start);
								}
								break;
							}
						}
	
						if ($voucher_code) {
							$voucher_info = $this->model_sale_orderpro->getVoucherByCode($voucher_code);
	
							if ($voucher_info) {
								$voucher_data = array(
									'voucher_id'  => $voucher_info['voucher_id'],
									'value'       => $voucher_value
								);
	
								$this->model_sale_orderpro->setVoucherToOrder($order_id, $voucher_data);
							}
						}
					}
	
					//Reward
					$reward_use = abs($this->model_sale_orderpro->getUsedRewardsByOrderId($order_id));
	
					if (!(float)$reward_use) {
						$points = '';
	
						foreach ($order_totals as $total) {
							if ($total['code'] == 'reward') {
								$start = strpos($total['title'], '(') + 1;
								$end = strrpos($total['title'], ')');
	
								if ($start && $end) {
									$points = substr($total['title'], $start, $end - $start);
								}
								break;
							}
						}
	
						if ($points) {
							$description = $language->get('text_order_id') . $order_id;
							
							$points_data = array(
								'customer_id' => $order_info['customer_id'],
								'points'      => $points,
								'description' => $description
							);
	
							$this->model_sale_orderpro->setRewardUsedToOrder($order_id, $points_data);
						}
					}
				}
	
				if (!in_array($order_info['order_status_id'], $this->config->get('config_complete_status')) && in_array($new_status_id, $this->config->get('config_complete_status'))) {
					$description = $language->get('text_order_id') . $order_id;
	
					if ($order_info['affiliate_id'] && $this->config->get('config_affiliate_auto')) {
						$this->load->model('customer/customer');
	
						$affiliate_total = $this->model_customer_customer->getTotalTransactionsByOrderId($order_id);
	
						if (!$affiliate_total) {
							$this->model_customer_customer->addTransaction($order_info['affiliate_id'], $description, $order_info['commission'], $order_id);
						} else {
							$this->model_customer_customer->deleteTransactionByOrderId($order_id);
							$this->model_customer_customer->addTransaction($order_info['affiliate_id'], $description, $order_info['commission'], $order_id);
						}
					}
	
					if ($order_info['customer_id'] && $this->config->get('orderpro_auto_addreward')) {
						$reward_exist = $this->model_sale_orderpro->getReceivedRewardsByOrderId($order_id);
	
						$this->model_sale_orderpro->deleteOrderRewardReceived($order_id);
						$this->updateReward($order_info['customer_id'], $description, (float)$order_info['reward'], $order_id, (float)$reward_exist);
					}
	
					$this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '1' WHERE order_id = '" . (int)$order_id . "'");
	
					$this->load->model('sale/voucher');
					$results = $this->model_sale_orderpro->getOrderVouchers($order_id);
	
					foreach ($results as $result) {
						$this->model_sale_voucher->sendVoucher($result['voucher_id']);
					}
				}
	
				if (in_array($order_info['order_status_id'], $this->config->get('config_complete_status')) && !in_array($new_status_id, $this->config->get('config_complete_status')) && in_array($new_status_id, $this->config->get('config_processing_status'))) {
					if ($order_info['affiliate_id']) {
						$this->load->model('customer/customer');
						$this->model_customer_customer->deleteTransactionByOrderId($order_id);
					}
	
					$this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '0' WHERE order_id = '" . (int)$order_id . "'");
					$this->model_sale_orderpro->deleteOrderRewardReceived($order_id);
				}
	
				if (in_array($order_info['order_status_id'], array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status'))) && !in_array($new_status_id, array_merge($this->config->get('config_processing_status'), $this->config->get('config_complete_status')))) {
					$this->model_sale_orderpro->addToStockOrderId($order_id);
					$this->cache->delete('product');
	
					if ($order_info['affiliate_id']) {
						$this->load->model('customer/customer');
						$this->model_customer_customer->deleteTransactionByOrderId($order_id);
					}
	
					$this->db->query("UPDATE " . DB_PREFIX . "voucher SET status = '0' WHERE order_id = '" . (int)$order_id . "'");
	
					$this->model_sale_orderpro->deleteOrderCoupon($order_id);
					$this->model_sale_orderpro->deleteOrderVoucher($order_id);
	
					$this->model_sale_orderpro->deleteOrderRewardReceived($order_id);
					$this->model_sale_orderpro->deleteOrderRewardUsed($order_id);
				}
	
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$new_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$new_status_id . "', notify = '" . (!empty($hdata['notify']) ? 1 : 0) . "', comment = '" . $this->db->escape(strip_tags($hdata['comment'])) . "', date_added = NOW()");
				$order_history_id = $this->db->getLastId();
	
				if (!$order_new) {
					if (!empty($hdata['notify']) || !empty($hdata['notify_admin'])) {
						$data = array();
	
						$data['text_address'] = $language->get('text_address');
						$data['text_email'] = $language->get('text_email');
						$data['text_telephone'] = $language->get('text_telephone');
						$data['text_fax'] = $language->get('text_fax');
						$data['text_url'] = $language->get('text_url');
	
						$data['title'] = sprintf($language->get('text_update_subject'), $order_info['store_name'], $order_id);
	
						$this->load->model('tool/image');
	
						$this->load->model('setting/setting');
						$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
	
						if ($store_info) {
							$data['store_address'] = $store_info['config_address'];
							$data['store_email'] = $store_info['config_email'];
							$data['store_telephone'] = $store_info['config_telephone'];
							$data['store_fax'] = $store_info['config_fax'];
							$logo = $store_info['config_logo'];
						} else {
							$data['store_address'] = $this->config->get('config_address');
							$data['store_email'] = $this->config->get('config_email');
							$data['store_telephone'] = $this->config->get('config_telephone');
							$data['store_fax'] = $this->config->get('config_fax');
							$logo = $this->config->get('config_logo');
						}
	
						if ($logo && is_file(DIR_IMAGE . $logo)) {
							list($width_logo, $height_logo) = getimagesize(DIR_IMAGE . $logo);
	
							if (($width_logo > 420) || ($height_logo > 100)) {
								$data['logo'] = $this->model_tool_image->resize($logo, 420, 100);
							} else {
								$data['logo'] = HTTP_CATALOG . 'image/' . $logo;
							}
	
						} else {
							$data['logo'] = false;
						}
	
						if ($order_info['language_id']) {
							$langdata = $this->model_setting_setting->getSettingValue('config_langdata', $order_info['store_id']);
	
							if ($langdata) {
								$store_infos = json_decode($langdata, true);
	
								if (!empty($store_infos[$order_info['language_id']]['address'])) {
									$data['store_address'] = $store_infos[$order_info['language_id']]['address'];
								}
							}
						}
	
						$data['store_address'] = str_ireplace('<br />', ', ', nl2br($data['store_address']));
	
						$data['store_name'] = $order_info['store_name'];
						$data['store_url'] = $order_info['store_url'];
	
						$subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
	
						$data['text_order_id'] = $language->get('text_update_order_id');
						$data['order_id'] = $order_id;
	
						$data['text_date_added'] = $language->get('text_update_date_added');
						$data['date_added'] = date('j.m.Y H:i', strtotime($order_info['date_added']));
						$data['text_date_modified'] = $language->get('text_update_date_modified');
						$data['date_modified'] = date('j.m.Y H:i', strtotime($order_info['date_modified']));
	
						$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$hdata['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
						if ($order_status_query->num_rows) {
							$data['text_order_status'] = $language->get('text_update_order_status');
							$data['order_status'] = $order_status_query->row['name'];
						} else {
							$data['order_status'] = '';
						}
	
						if ($order_info['customer_id']) {
							$data['text_link'] = $language->get('text_update_link');
							$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
							$data['text_account'] = $language->get('text_account');
						} else {
							$data['link'] = '';
						}
	
						if (!empty($hdata['comment'])) {
							$data['text_admin_comment'] = $language->get('text_update_comment');
							$data['admin_comment'] = $hdata['comment'];
						} else {
							$data['admin_comment'] = '';
						}
	
						if ($order_info['comment']) {
							$data['text_order_comment'] = $language->get('text_comment_customer');
							$data['comment'] = nl2br($order_info['comment']);
						} else {
							$data['comment'] = '';
						}
	
						$data['download'] = '';
	
						$data['text_footer'] = $language->get('text_update_footer');
	
						$data['products'] = array();
						$products = array();
	
						if (!empty($hdata['notify_products']) || !empty($hdata['notify_admin_products'])) {
							$order_products = $this->model_sale_orderpro->getOrderProducts($order_id);
	
							$download_status = false;
	
							foreach ($order_products as $order_product) {
								$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");
								if ($product_download_query->row['total']) {
									$download_status = true;
								}
							}
	
							$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$new_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
							if ($order_status_query->num_rows) {
								$order_status = $order_status_query->row['name'];
							} else {
								$order_status = '';
							}
	
							$data['column_pid'] = $language->get('column_pid');
							$data['column_image'] = $language->get('column_image');
							$data['column_product'] = $language->get('column_product');
							$data['column_model'] = $language->get('column_model');
							$data['column_sku'] = $language->get('column_sku');
							$data['column_upc'] = $language->get('column_upc');
							$data['column_ean'] = $language->get('column_ean');
							$data['column_jan'] = $language->get('column_jan');
							$data['column_isbn'] = $language->get('column_isbn');
							$data['column_mpn'] = $language->get('column_mpn');
							$data['column_location'] = $language->get('column_location');
							$data['column_quantity'] = $language->get('column_quantity');
							$data['column_price'] = $language->get('column_price');
							$data['column_total'] = $language->get('column_total');
							$data['column_comment'] = $language->get('column_comment');
	
							$weight_unit = $this->model_sale_orderpro->getWeightUnit($order_info['language_id']);
							$data['column_weight'] = sprintf($language->get('column_weight'), $weight_unit);
	
							if ($download_status) {
								$data['text_download'] = $language->get('text_update_download');
								$data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
							}
	
							$data['show_pid'] = $this->config->get('orderpro_neworder_pid');
							$data['show_image'] = $this->config->get('orderpro_neworder_image');
							$data['show_model'] = $this->config->get('orderpro_neworder_model');
							$data['show_sku'] = $this->config->get('orderpro_neworder_sku');
							$data['show_upc'] = $this->config->get('orderpro_neworder_upc');
							$data['show_ean'] = $this->config->get('orderpro_neworder_ean');
							$data['show_jan'] = $this->config->get('orderpro_neworder_jan');
							$data['show_isbn'] = $this->config->get('orderpro_neworder_isbn');
							$data['show_mpn'] = $this->config->get('orderpro_neworder_mpn');
							$data['show_location'] = $this->config->get('orderpro_neworder_location');
							$data['show_weight'] = $this->config->get('orderpro_neworder_weight');
	
							$this->load->model('tool/upload');
	
							foreach ($order_products as $product) {
								$option_data = array();
	
								$order_options = $this->model_sale_orderpro->getOrderOptions($order_id, $product['order_product_id']);
	
								foreach ($order_options as $option) {
									if ($option['type'] != 'file') {
										$value = $option['value'];
									} else {
										$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
	
										if ($upload_info) {
											$value = $upload_info['name'];
										} else {
											$value = '';
										}
									}
	
									$option_data[] = array(
										'name'  => $option['name'],
										'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
									);
								}
	
								$image = $this->model_sale_orderpro->getProductImage($product['product_id']);
	
								if ($image && is_file(DIR_IMAGE . $image)) {
									$img = $this->model_tool_image->resize($image, 60, 60);
								} else {
									$img = $this->model_tool_image->resize('placeholder.png', 60, 60);
								}
	
								$products[] = array(
									'product_id' => $product['product_id'],
									'img'      => $img,
									'name'     => $product['name'],
									'model'    => $product['model'],
									'sku'      => $product['sku'],
									'mpn'      => $product['mpn'],
									'upc'      => $product['upc'],
									'jan'      => $product['jan'],
									'isbn'     => $product['isbn'],
									'ean'      => $product['ean'],
									'location' => $product['location'],
									'weight'   => $product['weight'],
									'option'   => $option_data,
									'quantity' => $product['quantity'],
									'pprice'   => $product['price'],
									'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
									'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
								);
							}
	
							$data['vouchers'] = array();
							$order_vouchers = $this->model_sale_orderpro->getOrderVouchers($order_id);
	
							foreach ($order_vouchers as $voucher) {
								$data['vouchers'][] = array(
									'description' => $voucher['description'],
									'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
								);
							}
	
							$data['totals'] = array();
							$order_totals = $this->model_sale_orderpro->getOrderTotals($order_id);
	
							foreach ($order_totals as $total) {
								$data['totals'][] = array(
									'title' => $total['title'],
									'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
								);
							}
						}
	
						$mail = new Mail($this->config->get('config_mail_engine'));
						$mail->parameter = $this->config->get('config_mail_parameter');
						$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
						$mail->smtp_username = $this->config->get('config_mail_smtp_username');
						$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
						$mail->smtp_port = $this->config->get('config_mail_smtp_port');
						$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
						$mail->setFrom($this->config->get('config_email'));
						$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
						$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
	
						if (!empty($hdata['notify']) && $this->validateEmail($order_info['email'])) {
							if (!empty($hdata['notify_products'])) {
								$data['products'] = $products;
							} else {
								$data['products'] = array();
							}
	
							$html = $this->load->view('mail/orderpro/updateorder', $data);
							$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
							$mail->setTo($order_info['email']);
							$mail->send();
						}
	
						if (!empty($hdata['notify_admin'])) {
							if (!empty($hdata['notify_admin_products'])) {
								$data['products'] = $products;
							} else {
								$data['products'] = array();
							}
	
							$data['link'] = '';
							$data['download'] = '';
							$data['text_footer'] = '';
	
							$html = $this->load->view('mail/orderpro/updateorder', $data);
							$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
							$mail->setTo($this->config->get('config_email'));
							$mail->send();
						}
					}
				} else {
					if (!empty($hdata['neworder_notify']) || !empty($hdata['neworder_notify_admin'])) {
						$this->sendNewOrderNotify($order_id, $hdata);
					}
				}
			}
		}
		return $order_history_id;
	}
	
	public function sendNewOrderNotify($order_id, $hdata) {
		$this->load->model('sale/orderpro');
		$order_info = $this->model_sale_orderpro->getOrder($order_id);
	
		if ($order_info) {
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_directory']);
			$language->load('sale/orderpro');
	
			$order_products = $this->model_sale_orderpro->getOrderProducts($order_id);
	
			$download_status = false;
	
			foreach ($order_products as $order_product) {
				$product_download_query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$order_product['product_id'] . "'");
				if ($product_download_query->row['total']) {
					$download_status = true;
				}
			}
	
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_info['order_status_id'] . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
	
			if ($order_status_query->num_rows) {
				$order_status = $order_status_query->row['name'];
			} else {
				$order_status = '';
			}
	
			$subject = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
	
			$data = array();
			$data['title'] = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);
	
			$data['text_greeting'] = sprintf($language->get('text_new_greeting'), $order_info['store_name']);
			$data['text_link'] = $language->get('text_new_link');
			$data['text_download'] = $language->get('text_new_download');
			$data['text_order'] = $language->get('text_order');
			$data['text_order_id'] = $language->get('text_order_id');
			$data['text_date_added'] = $language->get('text_date_added');
			$data['text_email'] = $language->get('text_email');
			$data['text_telephone'] = $language->get('text_telephone');
			$data['text_fax'] = $language->get('text_fax');
			$data['text_url'] = $language->get('text_url');
			$data['text_to'] = $language->get('text_to');
			$data['text_ship_to'] = $language->get('text_ship_to');
			$data['text_address'] = $language->get('text_address');
			$data['text_company'] = $language->get('text_company');
			$data['text_comment_customer'] = $language->get('text_comment_customer');
			$data['text_payment_method'] = $language->get('text_payment_method');
			$data['text_shipping_method'] = $language->get('text_shipping_method');
			$data['text_footer'] = $language->get('text_new_footer');
	
			$data['column_pid'] = $language->get('column_pid');
			$data['column_image'] = $language->get('column_image');
			$data['column_product'] = $language->get('column_product');
			$data['column_model'] = $language->get('column_model');
			$data['column_sku'] = $language->get('column_sku');
			$data['column_upc'] = $language->get('column_upc');
			$data['column_ean'] = $language->get('column_ean');
			$data['column_jan'] = $language->get('column_jan');
			$data['column_isbn'] = $language->get('column_isbn');
			$data['column_mpn'] = $language->get('column_mpn');
			$data['column_location'] = $language->get('column_location');
			$data['column_quantity'] = $language->get('column_quantity');
			$data['column_price'] = $language->get('column_price');
			$data['column_total'] = $language->get('column_total');
			$data['column_comment'] = $language->get('column_comment');
	
			$weight_unit = $this->model_sale_orderpro->getWeightUnit($order_info['language_id']);
			$data['column_weight'] = sprintf($language->get('column_weight'), $weight_unit);
	
			$this->load->model('tool/image');
	
			$this->load->model('setting/setting');
			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
	
			if ($store_info) {
				$data['store_address'] = $store_info['config_address'];
				$data['store_email'] = $store_info['config_email'];
				$data['store_telephone'] = $store_info['config_telephone'];
				$data['store_fax'] = $store_info['config_fax'];
				$logo = $store_info['config_logo'];
			} else {
				$data['store_address'] = $this->config->get('config_address');
				$data['store_email'] = $this->config->get('config_email');
				$data['store_telephone'] = $this->config->get('config_telephone');
				$data['store_fax'] = $this->config->get('config_fax');
				$logo = $this->config->get('config_logo');
			}
	
			if ($logo && is_file(DIR_IMAGE . $logo)) {
				list($width_logo, $height_logo) = getimagesize(DIR_IMAGE . $logo);
	
				if (($width_logo > 420) || ($height_logo > 100)) {
					$data['logo'] = $this->model_tool_image->resize($logo, 420, 100);
				} else {
					$data['logo'] = HTTP_CATALOG . 'image/' . $logo;
				}
	
			} else {
				$data['logo'] = false;
			}
	
			if ($order_info['language_id']) {
				$langdata = $this->model_setting_setting->getSettingValue('config_langdata', $order_info['store_id']);
	
				if ($langdata) {
					$store_infos = json_decode($langdata, true);
	
					if (!empty($store_infos[$order_info['language_id']]['address'])) {
						$data['store_address'] = $store_infos[$order_info['language_id']]['address'];
					}
				}
			}
	
			$data['store_address'] = str_ireplace('<br />', ', ', nl2br($data['store_address']));
	
			$data['store_name'] = $order_info['store_name'];
			$data['store_url'] = $order_info['store_url'];
			$data['customer_id'] = $order_info['customer_id'];
	
			if ($data['customer_id']) {
				$data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
			} else {
				$data['link'] = '';
			}
	
			if ($download_status) {
				$data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
			} else {
				$data['download'] = '';
			}
	
			$data['order_id'] = $order_id;
			$data['date_added'] = date('j.m.Y H:i', strtotime($order_info['date_added']));
			$data['payment_method'] = $order_info['payment_method'];
			$data['shipping_method'] = $order_info['shipping_method'];
			$data['email'] = $order_info['email'];
			$data['telephone'] = $order_info['telephone'];
	
			$data['order_status'] = $order_status;
	
			$data['show_pid'] = $this->config->get('orderpro_neworder_pid');
			$data['show_image'] = $this->config->get('orderpro_neworder_image');
			$data['show_model'] = $this->config->get('orderpro_neworder_model');
			$data['show_sku'] = $this->config->get('orderpro_neworder_sku');
			$data['show_upc'] = $this->config->get('orderpro_neworder_upc');
			$data['show_ean'] = $this->config->get('orderpro_neworder_ean');
			$data['show_jan'] = $this->config->get('orderpro_neworder_jan');
			$data['show_isbn'] = $this->config->get('orderpro_neworder_isbn');
			$data['show_mpn'] = $this->config->get('orderpro_neworder_mpn');
			$data['show_location'] = $this->config->get('orderpro_neworder_location');
			$data['show_weight'] = $this->config->get('orderpro_neworder_weight');
	
			if ($order_info['comment']) {
				$data['comment'] = nl2br($order_info['comment']);
			} else {
				$data['comment'] = '';
			}
	
			if ($order_info['payment_company']) {
				$data['company'] = $order_info['payment_company'];
			} elseif ($order_info['shipping_company']) {
				$data['company'] = $order_info['shipping_company'];
			} else {
				$data['company'] = '';
			}
	
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}';
			}
	
			$find = array('{firstname}','{lastname}','{company}','{address_1}','{address_2}','{city}','{postcode}','{zone}','{zone_code}','{country}');
	
			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);
	
			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	
			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{postcode} {country}' . "\n" . '{zone}' . "\n" . '{city}' . "\n" . '{address_1} {address_2}';
			}
	
			$find = array('{firstname}','{lastname}','{company}','{address_1}','{address_2}','{city}','{postcode}','{zone}','{zone_code}','{country}');
	
			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);
	
			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
	
			$this->load->model('tool/upload');
	
			$data['products'] = array();
	
			foreach ($order_products as $product) {
				$option_data = array();
	
				$order_options = $this->model_sale_orderpro->getOrderOptions($order_id, $product['order_product_id']);
	
				foreach ($order_options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}
	
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}
	
				$image = $this->model_sale_orderpro->getProductImage($product['product_id']);
	
				if ($image && is_file(DIR_IMAGE . $image)) {
					$img = $this->model_tool_image->resize($image, 60, 60);
				} else {
					$img = $this->model_tool_image->resize('placeholder.png', 60, 60);
				}
	
				$data['products'][] = array(
					'product_id' => $product['product_id'],
					'img'      => $img,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'sku'      => $product['sku'],
					'mpn'      => $product['mpn'],
					'upc'      => $product['upc'],
					'jan'      => $product['jan'],
					'isbn'     => $product['isbn'],
					'ean'      => $product['ean'],
					'location' => $product['location'],
					'weight'   => $product['weight'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'pprice'   => $product['price'],
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
	
			$data['vouchers'] = array();
			$order_vouchers = $this->model_sale_orderpro->getOrderVouchers($order_id);
	
			foreach ($order_vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
	
			$data['totals'] = array();
			$order_totals = $this->model_sale_orderpro->getOrderTotals($order_id);
	
			foreach ($order_totals as $total) {
				$data['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
	
			$html = $this->load->view('mail/orderpro/neworder', $data);
	
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
	
			if (!empty($hdata['neworder_notify']) && $this->validateEmail($order_info['email'])) {
				$mail->setTo($order_info['email']);
				$mail->send();
			}
	
			if (!empty($hdata['neworder_notify_admin'])) {
				$subject = sprintf($language->get('text_new_received_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
	
				$data['text_greeting'] = $language->get('text_new_received');
				$data['text_footer'] = '';
				$data['download'] = '';
				$data['link'] = '';
	
				$html = $this->load->view('mail/orderpro/neworder', $data);
	
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
				$mail->setTo($this->config->get('config_email'));
				$mail->send();
			}
		}
	}
	
	public function getOrderHistories($order_id, $start = 0, $limit = 0) {
		if ($order_id) {
			if ($start < 0) {
				$start = 0;
			}
	
			if ($limit < 1) {
				$limit = 10;
			}
	
			$query = $this->db->query("SELECT oh.date_added, os.name AS status, oh.comment, oh.notify FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id WHERE oh.order_id = '" . (int)$order_id . "' AND os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY oh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
	
			return $query->rows;
		} else {
			return array();
		}
	}
	
	public function getTotalOrderHistories($order_id) {
		$total = 0;
		if ($order_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
			$total = $query->row['total'];
		}
		return $total;
	}
	
	public function updateReward($customer_id, $description = '', $points, $order_id, $reward_exist = 0) {
		$this->load->model('sale/orderpro');
		$customer_info = $this->model_sale_orderpro->getCustomer($customer_id);
	
		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_reward SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', points = '" . (float)$points . "', description = '" . $this->db->escape($description) . "', date_added = NOW()");
	
			$language_directory = false;
			$store_name = $this->config->get('config_name');
	
			$order_info = $this->model_sale_orderpro->getOrder($order_id);
	
			if ($order_info) {
				$store_name = $order_info['store_name'];
				$language_directory = $order_info['language_directory'];
			}
	
			if (!$language_directory) {
				$languages = $this->model_sale_orderpro->getLanguages();
	
				foreach ($languages as $lang) {
					if ($lang['language_id'] == $this->config->get('config_language_id')) {
						$language_directory = $lang['code'];
					}
				}
			}
	
			if (!$language_directory) {
				$language_directory = 'en-gb';
			}
	
			$language = new Language($language_directory);
			$language->load($language_directory);
			$language->load('sale/orderpro');
	
			if ($points != $reward_exist) {
				if ($reward_exist < $points) {
					$points = $points - $reward_exist;
					$message = sprintf($language->get('text_reward_received'), $points) . "\n\n";
				}
				if ($reward_exist > $points) {
					$points = $reward_exist - $points;
					$message  = sprintf($language->get('text_reward_revoked'), $points) . "\n\n";
				}
	
				$new_total = $this->model_sale_orderpro->getRewardTotal($customer_id);
				$message .= sprintf($language->get('text_reward_total'), $new_total);
	
				if ($this->validateEmail($customer_info['email'])) {
					$mail = new Mail($this->config->get('config_mail_engine'));
					$mail->parameter = $this->config->get('config_mail_parameter');
					$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
					$mail->smtp_username = $this->config->get('config_mail_smtp_username');
					$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
					$mail->smtp_port = $this->config->get('config_mail_smtp_port');
					$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
					$mail->setTo($customer_info['email']);
					$mail->setFrom($this->config->get('config_email'));
					$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
					$mail->setSubject(html_entity_decode(sprintf($language->get('text_reward_subject'), $store_name), ENT_QUOTES, 'UTF-8'));
					$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
					$mail->send();
				}
			}
	
			return $language->get('success_reward_updated');
		}
	}
	
	public function validateEmail($email) {
		$pattern = '/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,15}$/i';
	
		if ($pattern) {
			if (preg_match($pattern, $email)) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}
/*
@author Aleksandr Shcherbakov
@e-mail: shchs@ya.ru
@link   https://opencart-group.ru/modules/orderpro/
*/
?>