<?php
namespace ompro;

class omproapi {
	public  $version = '2.0.6';

	public function __construct($registry) {
		$this->registry = $registry;
	//	$this->log->write($_SERVER['HTTP_HOST']);
	}

	public function __get($name) {
		return $this->registry->get($name);
	}


	// OrderHistories
	public function getOrderHistories($order_id, $comment_exist = 0, $log_exist = 0, $file_name_exist = 0, $notify = 2, $notify_sms = 2, $notify_tlgrm = 2, $start = 0, $limit = 10, $order = 'DESC') {

		$sql = "SELECT oh.date_added, oh.user_id, oh.payment_status_id, oh.shipping_status_id, os.name AS order_status, oh.comment, oh.log, CONCAT(u.firstname, ' ', u.lastname) AS user, u.image AS user_image, oh.file_name, oh.notify, oh.notify_sms, oh.notify_tlgrm FROM " . DB_PREFIX . "order_history oh LEFT JOIN " . DB_PREFIX . "order_status os ON (oh.order_status_id = os.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "') LEFT JOIN  " . DB_PREFIX . "user u ON (u.user_id = oh.user_id) WHERE oh.order_id = '" . (int)$order_id . "'";

		if ($notify < 2) {
			$sql .= " AND oh.notify = '" .$notify. "'";
		}

		if ($notify_sms < 2) {
			$sql .= " AND oh.notify_sms = '" .$notify_sms. "'";
		}

		if ($notify_tlgrm < 2) {
			$sql .= " AND oh.notify_tlgrm = '" .$notify_tlgrm. "'";
		}

		if ($comment_exist == 1) {
			$sql .= " AND oh.comment LIKE '_%' ";
		} elseif ($comment_exist == 2) {
			$sql .= " AND oh.comment NOT LIKE '_%' ";
		}

		if ($log_exist == 1) {
			$sql .= " AND oh.log LIKE '_%' ";
		} elseif ($log_exist == 2) {
			$sql .= " AND oh.log NOT LIKE '_%' ";
		}

		if ($file_name_exist == 1) {
			$sql .= " AND oh.file_name LIKE '_%' ";
		} elseif ($file_name_exist == 2) {
			$sql .= " AND oh.file_name NOT LIKE '_%' ";
		}

		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		if ($order == 'DESC') {
			$order = "DESC";
		} else {
			$order = "ASC";
		}

		$sql .= " ORDER BY oh.date_added  " . $order . " LIMIT " . (int)$start . ", " . (int)$limit;
		$query = $this->db->query($sql);

		$payment_status_values = $this->ompro->getOptionValues('MVFJ1613337864', $this->config->get('config_language_id'), false);
		$shipping_status_values = $this->ompro->getOptionValues('JNYR1613337915', $this->config->get('config_language_id'), false);
		$histories = array();
		foreach ($query->rows as $id => $result) {
			$result['payment_status'] = '';
			if ($result['payment_status_id'] && isset($payment_status_values[$result['payment_status_id']])) {
				$result['payment_status'] = $payment_status_values[$result['payment_status_id']]['name'];
			}
			$result['shipping_status'] = '';
			if ($result['shipping_status_id'] && isset($shipping_status_values[$result['shipping_status_id']])) {
				$result['shipping_status'] = $shipping_status_values[$result['shipping_status_id']]['name'];
			}
			$histories[$id] = $result;
		}

		return $histories;
	}

	public function getTotalOrderHistories($order_id, $comment_exist = 0, $log_exist = 0, $file_name_exist = 0, $notify = 2, $notify_sms = 2, $notify_tlgrm = 2) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_history oh WHERE order_id = '" . (int)$order_id . "'";

		if ($notify < 2) {
			$sql .= " AND oh.notify = '" .$notify. "'";
		}

		if ($notify_sms < 2) {
			$sql .= " AND oh.notify_sms = '" .$notify_sms. "'";
		}

		if ($notify_tlgrm < 2) {
			$sql .= " AND oh.notify_tlgrm = '" .$notify_tlgrm. "'";
		}

		if ($comment_exist == 1) {
			$sql .= " AND oh.comment LIKE '_%' ";
		} elseif ($comment_exist == 2) {
			$sql .= " AND oh.comment NOT LIKE '_%' ";
		}

		if ($log_exist == 1) {
			$sql .= " AND oh.log LIKE '_%' ";
		} elseif ($log_exist == 2) {
			$sql .= " AND oh.log NOT LIKE '_%' ";
		}

		if ($file_name_exist == 1) {
			$sql .= " AND oh.file_name LIKE '_%' ";
		} elseif ($file_name_exist == 2) {
			$sql .= " AND oh.file_name NOT LIKE '_%' ";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function addHistoryLog($post, $log_str = '') {
		$table = $post['dbTable'];

		if (isset($post['log']) && $post['log'] && isset($post['order_id']) && $post['order_id'] && ($table == 'order' || $table == 'order_product' || $table == 'order_simple_fields' || $table == 'ocustom')) {
			$order_id = $post['order_id'];

			if (!$log_str && $table !== 'ocustom') {
				$add_str = '';
				if ($table == 'order_product') {
					$add_str = ', где `order_product_id` = ' . $post['pk'];
				}

				if ($table == 'order_product') {
					$column = str_ireplace('op_', '', $post['name']);
				} else {
					$column = $post['name'];
				}

				$value = '';
				if (is_array($post['value'])) {
					$value = implode(',', $post['value']);
				} else {
					$value = $post['value'];
				}

				$log_str = "Изменение в: `" . DB_PREFIX . $table . " > " . $this->db->escape($column) . "`" . $add_str . ". Значение: `" . $this->db->escape($value) ."`";
			}

			$user_id = $this->session->data['user_id'];
			$order_status_id = $this->ompro->getOrderFieldValue($order_id, 'order_status_id');
			$payment_status_id = $this->ompro->getOrderFieldValue($order_id, 'payment_status_id');
			$shipping_status_id = $this->ompro->getOrderFieldValue($order_id, 'shipping_status_id');

			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', user_id = '" . (int)$this->session->data['user_id'] . "', order_status_id = '" . (int)$order_status_id . "', payment_status_id = '" . $payment_status_id . "', shipping_status_id = '" . $shipping_status_id . "', notify = '0', notify_sms = '0', notify_tlgrm = '0', comment = '', log = '" . $this->db->escape($log_str) . "', file_name = '', date_added = NOW()");
		}
	}

	public function addReward($customer_info = null, $customer_id, $description = '', $points = '', $order_id = 0) {
		if ($customer_info) {
			$reward_id = $this->ompro->addPoints($customer_id, $description, $points, $order_id);

			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_reward_received'), $points) . "\n\n";
			$message .= sprintf($this->language->get('text_reward_total'), $this->ompro->getRewardTotal($customer_id));

			if ($this->ompro->ocversion >= 300) {
				$mail = new \Mail($this->config->get('config_mail_engine'));
			} else {
				$mail = new \Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
			}

			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_reward_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();

			return $reward_id;
		}
	}

	public function addCredit($customer_info, $customer_id, $description = '', $amount = '', $order_id = 0) {
		if ($customer_info) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" . (int)$customer_id . "', order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($description) . "', amount = '" . (float)$amount . "', date_added = NOW()");

			$transaction_id = $this->db->getLastId();

			$this->load->model('setting/store');
			$store_info = $this->model_setting_store->getStore($customer_info['store_id']);

			if ($store_info) {
				$store_name = $store_info['name'];
			} else {
				$store_name = $this->config->get('config_name');
			}

			$message  = sprintf($this->language->get('text_transaction_received'), $this->currency->format($amount, $this->config->get('config_currency'))) . "\n\n";
			$message .= sprintf($this->language->get('text_transaction_total'), $this->currency->format($this->ompro->getCustomerTransactionTotal($customer_id), $this->config->get('config_currency')));

			if ($this->ompro->ocversion >= 300) {
				$mail = new \Mail($this->config->get('config_mail_engine'));
			} else {
				$mail = new \Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
			}

			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($customer_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_transaction_subject'), html_entity_decode($store_name, ENT_QUOTES, 'UTF-8')));
			$mail->setText($message);
			$mail->send();

			return $transaction_id;
		}
	}


	// SMS
	public function sendSms($to = '', $copy = '', $message = '') {
       // OCDEV.pro -  Смс уведомления v1.4.8 https://opencartforum.com/files/file/6995-ocdevpro-sms-uvedomleniya-dlya-opencart-2x-3x/

		if ($to && $this->config->get('sms_notify_gatename') && $this->config->get('sms_notify_gate_username')) {
			$phones = preg_replace("/[^0-9]/", '', $to);

			if ($copy) {
				$phones = $to . ',' . $copy;
			}

			if ($phones && $message) {
				$options = [
					'to'       => $phones,
					'from'     => $this->config->get('sms_notify_from'),
					'username' => $this->config->get('sms_notify_gate_username'),
					'password' => $this->config->get('sms_notify_gate_password'),
					'message'  => $message
				];

				$sms = new \OcdSms($this->config->get('sms_notify_gatename'), $options);
				$sms->send();

				return true;
			}
        }

		// gatename List: unisender, alphasms, smsc, atomic, intisSMS, smsaero, smsint, smsru,
		$gatename = $this->config->get('config_sms_gatename');

		if ($this->config->get('config_sms_alert') && $gatename && $to && $message) {
			$options = array(
				'username' 	=> $this->config->get('config_sms_gate_username'),
				'password' 	=> $this->config->get('config_sms_gate_password'),
				'from'     		=> $this->config->get('config_sms_from'),
				'to'       		=> $to,
				'copy'     		=> $copy,
				'message' 	=> $message
			);
			$sms = new \Sms($gatename, $options);
			$sms->send();
			return true;
		}
		return false;
	}

	// Mail
	public function sendMail($sub, $mes, $store_name, $store_email, $recipients, $attachments, $style_body, $style_div)  {

		if ($this->ocversion >= 300) {
			$mail = new \Mail($this->config->get('config_mail_engine'));
		} else {
			$mail = new \Mail();
			$mail->protocol = $this->config->get('config_mail_protocol');
		}

		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		if ($attachments) {
			foreach ($attachments as $filename) {
				$mail->addAttachment( DIR_DOWNLOAD . $filename );
			}
		}

		$mail->setFrom($store_email);
		$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));

		foreach ($recipients as $recipient) {
			$subject = $this->ompro->replaceOneVar('recipient_name', $recipient['recipient_name'], $sub);
			$message = $this->ompro->replaceOneVar('recipient_name', $recipient['recipient_name'], $mes);

			$html  = '<html dir="ltr" lang="en">' . "\n";
			$html .= '  <head>' . "\n";
			$html .= '    <title>' . $subject . '</title>' . "\n";
			$html .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
			$html .= '  </head>' . "\n";
			$html .= '  <body style="'.$style_body.'">' . "\n";
			$html .= '    <div style="'.$style_div.'">' . html_entity_decode($message, ENT_QUOTES, 'UTF-8') . '</div>' . "\n";
			$html .= '  </body>' . "\n";
			$html .= '</html>' . "\n";

			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($html);

			$email = trim($recipient['email']);
			if ($email !== '' && preg_match('/^[^@]+@.*.[a-z]{2,15}$/i', $email)) {
				$mail->setTo($email);
				$mail->send();
			}
		}
	}

	// novaposhta, ukrposhta, justin
	public function getPoshtaMethods() {
		return array('novaposhta', 'ukrposhta', 'justin');
	}

	public function getPoshtaCNListMenu() {
		$extension = $this->ompro->ocversion < 230 ? '' : 'extension/';
		$strtoken = $this->ompro->strtoken;

		$menu = '<ul class="dropdown-menu dropdown-menu-right" data-dropdownmenu="nova_or_ukrposhta_list_dropdownmenu">';
		foreach ($this->getPoshtaMethods() as $code) {
			if ($this->config->get($code.'_status') || $this->config->get('shipping_'.$code.'_status')) {
				$menu .= '<li class="dropdown-header">'.$this->language->get('heading_cn_' . $code).'</li>';
				$menu .= '<li><a href="'.$this->url->link($extension.'shipping/' . $code .'/getCNList', $strtoken, 'SSL').'">Список накладных</a></li>';
				$menu .= '<li role="separator" class="divider"></li>';
			}
		}
		$menu .= '</ul>';
		return $menu;
	}

	public function getPoshtaOrders($orders = array()) {
		$strtoken = $this->ompro->strtoken;
		$poshta_orders = $poshta_settings = array();

		$poshta_methods = $this->getPoshtaMethods();

		foreach ($poshta_methods as $shipping_method) {
			if ($this->config->get($shipping_method . '_status')) {
				$poshta_settings[$shipping_method] = $this->config->get($shipping_method);
			} elseif ($this->config->get('shipping_' . $shipping_method . '_status')) {
				$poshta_settings[$shipping_method] = $this->config->get('shipping_' . $shipping_method);
			} else {
				unset($poshta_methods[$shipping_method]);
			}
		}

		if ($poshta_methods) {
			$extension = $this->ompro->ocversion < 230 ? '' : 'extension/';
			foreach ($orders as $order) {

				$order = $order['order_info'];

				foreach ($poshta_methods as $shipping_method) {
					if (!empty($poshta_settings[$shipping_method]['compatible_shipping_method']) && (empty($order['shipping_code']) || in_array($order['shipping_code'], $poshta_settings[$shipping_method]['compatible_shipping_method']) || in_array(stristr($order['shipping_code'], '.', true), $poshta_settings[$shipping_method]['compatible_shipping_method']))) {
						if ($order[$shipping_method . '_cn_number']) {
							unset($poshta_orders[$order['order_id']]);

							if ($poshta_settings[$shipping_method]['consignment_edit']) {
								if ($poshta_settings[$shipping_method]['consignment_edit_text'][$this->config->get('config_language_id')]) {
									$text = $poshta_settings[$shipping_method]['consignment_edit_text'][$this->config->get('config_language_id')];
								} else {
									$text = $this->language->get('text_cn_edit');
								}

								if ($shipping_method == 'novaposhta') {
									$cn_id = '&cn_ref=' . $order['novaposhta_cn_ref'];
								} elseif ($shipping_method == 'ukrposhta') {
									$cn_id = '&cn_uuid=' . $order['ukrposhta_cn_uuid'];
								} elseif ($shipping_method == 'justin') {
									$cn_id = '&cn_kis=' . $order['justin_cn_kis'];
								} else {
									$cn_id = '';
								}

								$poshta_orders[$order['order_id']][$shipping_method]['edit'] = array(
									'text' => $text,
									'href' => $this->url->link($extension .'shipping/' . $shipping_method . '/getCNForm', 'order_id=' . $order['order_id'] . '&' . $strtoken . $cn_id, 'SSL')
								);
							}

							if ($poshta_settings[$shipping_method]['consignment_delete']) {
								if ($poshta_settings[$shipping_method]['consignment_delete_text'][$this->config->get('config_language_id')]) {
									$text = $poshta_settings[$shipping_method]['consignment_delete_text'][$this->config->get('config_language_id')];
								} else {
									$text = $this->language->get('text_cn_delete');
								}

								$poshta_orders[$order['order_id']][$shipping_method]['delete'] = array(
									'text' => $text,
									'href' => ''
								);
							}

							break;
						} else {
							if ($poshta_settings[$shipping_method]['consignment_create']) {
								if ($poshta_settings[$shipping_method]['consignment_create_text'][$this->config->get('config_language_id')]) {
									$text = $poshta_settings[$shipping_method]['consignment_create_text'][$this->config->get('config_language_id')];
								} else {
									$text = $this->language->get('text_cn_create');
								}

								$poshta_orders[$order['order_id']][$shipping_method]['create'] = array(
									'text' => $text,
									'href' => $this->url->link($extension .'shipping/' . $shipping_method . '/getCNForm', 'order_id=' . $order['order_id'] . '&' . $strtoken, 'SSL')
								);
							}

							if ($poshta_settings[$shipping_method]['consignment_assignment_to_order']) {
								if ($poshta_settings[$shipping_method]['consignment_assignment_to_order_text'][$this->config->get('config_language_id')]) {
									$text = $poshta_settings[$shipping_method]['consignment_assignment_to_order_text'][$this->config->get('config_language_id')];
								} else {
									$text = $this->language->get('text_cn_assignment');
								}

								$poshta_orders[$order['order_id']][$shipping_method]['assignment'] = array(
									'text' => $text,
									'href' => ''
								);
							}
						}
					}
				}
			}
		}

		return $poshta_orders;
	}

	// replace Attributes & Options & Totals
	public function replaceVarsProductAttributes($product_id, $language_id, $attribute_group_tpl, $attribute_tpl) {
		$attribute_groups_tpl = $attributes_tpl = '';
		$attributes = $this->ompro->getProductAttributes($product_id, $language_id);
		if (!empty($attributes)) {
			$pattern_group_name = '/\[\[([^\[\]]*?)\{attribute}(.*?)\]\]/s';
			$srch_attribute = array('{attribute_name}', '{attribute_value}');
			foreach ($attributes as $attribute_group) {
				$attribute_groups_tpl .= str_replace('{attribute_group_name}', $attribute_group['name'], $attribute_group_tpl);
				foreach($attribute_group['attribute'] as $attribute){
					$rplc_attribute = array($attribute['name'], $attribute['text']);
					$attributes_tpl .= str_replace($srch_attribute, $rplc_attribute, $attribute_tpl);
				}

				if (preg_match_all($pattern_group_name, $attribute_group_tpl, $matches, PREG_SET_ORDER)) {
					$attribute_groups_tpl = $this->ompro->replaceMatches($matches, $attribute_groups_tpl, array('[[',']]'), '{attribute}', $attributes_tpl);
				}
			}
		}

		return $attribute_groups_tpl;
	}

	public function replaceVarsProductOrderOptions($order_id, $order_product_id, $option_tpl, $sms, $options = array()) {
		$options_tpl = '';

		if (empty($options)) {
			$options = $this->ompro->getOrderOptions($order_id, $order_product_id);
		}

		$option_data = array();
		if (!empty($options)) {
			foreach ($options as $option) {
				$href = '';
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$upload_info = $this->ompro->getUploadByCode($option['value']);
					if ($upload_info) {
						$href = $this->ompro->catalog .'index.php?route=api/ompro/download&code=' .$upload_info['code'];
						$value = $upload_info['name'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => $value,
					'quantity' => isset($option['quantity']) ? $option['quantity'] : '',
					'type'  => $option['type'],
					'href'  => $href
				);
			}

			if (!empty($option_data)) {
				$srch = array('{option_name}', '{option_value}');
				$pattern = '/\[\[([^\[\]]*?)\{option_quantity}(.*?)\]\]/s';

				foreach ($option_data as $option) {
					if ($option['type'] != 'file') {
						$rplc = array($option['name'], $option['value']);
					} else {
						if ($option['href'] !== '' && !$sms) {
							$rplc = array($option['name'], '<a href="'.$option['href'].'">'.$option['value'].'</a>');
						} else {
							$rplc = array($option['name'], $option['value']);
						}
					}

					$options_tpl .= str_replace($srch, $rplc, $option_tpl);

					preg_match_all($pattern, $options_tpl, $matches, PREG_SET_ORDER);
					if ($matches) {
						$options_tpl = $this->ompro->replaceMatches($matches, $options_tpl, array('[[',']]'), '{option_quantity}', $option['quantity']);
					}
				}
			}
		}

		return $options_tpl;
	}

	public function replaceVarsTotals($order_id, $currency_code, $currency_value, $totals_tpl) {
		$totals = $this->ompro->getOrderTotals($order_id);
		$total_tpl = '';
		$srch = array('{total_name}', '{total_value}');
		foreach ($totals as $total) {
			$rplc = array($total['title'], $this->currency->format($total['value'], $currency_code, $currency_value));
			$res = strip_tags(str_replace('<br>', '__br__', str_replace($srch, $rplc, $totals_tpl)));
			$total_tpl .= str_replace('__br__', '<br>', $res);
		}
		return $total_tpl;
	}


	/* НАСТРОЙКИ */

	// vars need to be in ompro_footer.tpl
	public function action_urls($code = '') {
		$strtoken = $this->ompro->strtoken;

		$action_urls = array();
		// OC
		$action_urls['print_invoice'] = $this->url->link('sale/order/invoice', $strtoken, 'SSL');
		$action_urls['print_shipping'] = $this->url->link('sale/order/shipping', $strtoken, 'SSL');
		$action_urls['order_add'] = $this->url->link('sale/order/add', $strtoken, 'SSL');
		$action_urls['order_info'] = $this->url->link('sale/order/info', $strtoken, 'SSL');
		$action_urls['order_edit'] = $this->url->link('sale/order/edit', $strtoken, 'SSL');
		$action_urls['order_histories_oc'] = $this->url->link('sale/order/history', $strtoken, 'SSL');

		$action_urls['customer_reward_history_oc'] = $this->url->link('customer/customer/reward', $strtoken, 'SSL');
		$action_urls['customer_transaction_history_oc'] = $this->url->link('customer/customer/transaction', $strtoken, 'SSL');

		if ($this->ompro->ocversion < 230) {
			$extension = '';
			$action_urls['dashboard_chart_oc'] = $this->url->link('dashboard/chart', $strtoken, 'SSL');
			$action_urls['dashboard_map_oc'] = $this->url->link('dashboard/map', $strtoken, 'SSL');
		} else {
			$extension = 'extension/';
			$action_urls['dashboard_chart_oc'] = $this->url->link('extension/dashboard/chart/chart', $strtoken, 'SSL');
			$action_urls['dashboard_map_oc'] = $this->url->link('extension/dashboard/map/map', $strtoken, 'SSL');
		}

		// OMPro
		$action_urls['order_delete'] = $this->url->link('sale/ompro_helper/deleteOrders', $strtoken, 'SSL');
		$action_urls['print_orders'] = $this->url->link('sale/ompro/printOrders', $strtoken, 'SSL');
		$action_urls['print_orders_table'] = $this->url->link('sale/ompro/printOrdersTable', $strtoken, 'SSL');
		$action_urls['print_products_table'] = $this->url->link('sale/ompro/printProductsTable', $strtoken, 'SSL');
		$action_urls['excel_orders'] = $this->url->link('sale/ompro/excelOrders', $strtoken, 'SSL');
		$action_urls['excel_orders_products'] = $this->url->link('sale/ompro/excelOrdersProducts', $strtoken, 'SSL');
		$action_urls['order_histories'] = $this->url->link('sale/ompro/orderHistories', $strtoken, 'SSL');

		$action_urls['order_reload'] = $this->url->link('sale/ompro/orderReload', $strtoken, 'SSL');
		$action_urls['order_tpl_view'] = $this->url->link('sale/ompro/orderTplView', $strtoken, 'SSL');
		$action_urls['orders_table_view'] = $this->url->link('sale/ompro/ordersTableView', $strtoken, 'SSL');
		$action_urls['customer_edit_href'] = $this->url->link('customer/customer/edit', $strtoken, 'SSL');
		$action_urls['customer_login_href'] = $this->url->link('customer/customer/login', $strtoken, 'SSL');

		$action_urls['ompro_widget_map'] = $this->url->link('sale/ompro_widget/map', $strtoken, 'SSL');
		$action_urls['ompro_widget_map_ru_mill'] = $this->url->link('sale/ompro_widget/mapRuMill', $strtoken, 'SSL');
		$action_urls['ompro_widget_map_europe_mill'] = $this->url->link('sale/ompro_widget/mapEuropeMill', $strtoken, 'SSL');
		$action_urls['ompro_widget_map_asia_mill'] = $this->url->link('sale/ompro_widget/mapAsiaMill', $strtoken, 'SSL');
		$action_urls['ompro_widget_donut_chart'] = $this->url->link('sale/ompro_widget/donut_chart', $strtoken, 'SSL');

		$action_urls['send_mail'] = $this->url->link('sale/ompro/sendMail', $strtoken, 'SSL');
		$action_urls['send_sms'] = $this->url->link('sale/ompro/sendSms', $strtoken, 'SSL');
		$action_urls['send_tlgrm'] = $this->url->link('sale/ompro/sendTelegram', $strtoken, 'SSL');

		// orderPro
		$action_urls['orderpro_edit'] = $this->url->link('sale/orderpro/edit', $strtoken, 'SSL');
		$action_urls['orderpro_invoice'] = $this->url->link('sale/orderpro/invoice', $strtoken, 'SSL');
		$action_urls['orderpro_get_merge'] = $this->url->link('sale/orderpro/getMerge', $strtoken, 'SSL');
		$action_urls['orderpro_merge_orders'] = $this->url->link('sale/orderpro/MergeOrder', $strtoken, 'SSL');
		$action_urls['orderpro_export_orders'] = $this->url->link('sale/orderpro/export', $strtoken, 'SSL');

		// orderq
		$action_urls['orderq_edit'] = '';
		if ($this->orderq_status()) {
			$action_urls['orderq_edit'] = $this->url->link('sale/orderq', $strtoken, 'SSL');
		}

		if ($code && isset($action_urls[$code])) {
			return $action_urls[$code];
		}

		return $action_urls;
	}

	public function btn_module_statuses($code = '') {
		$mod_statuses = array();

		// orderPro
		$mod_statuses['orderpro_edit_btn'] = false;
		$mod_statuses['orderpro_add_btn'] = false;
		$mod_statuses['orderpro_invoice_btn'] = false;
		$mod_statuses['orderpro_merge_orders_btn'] = false;
		$mod_statuses['orderpro_export_orders_btn'] = false;

		if ($this->orderpro_status()) {
			$mod_statuses['orderpro_edit_btn'] = true;
			$mod_statuses['orderpro_add_btn'] = true;
			$mod_statuses['orderpro_merge_orders_btn'] = true;
			if ($this->config->get('orderpro_invoice_type')) {
				$mod_statuses['orderpro_invoice_btn'] = true;
			}
			if ($this->config->get('orderpro_export_orders')) {
				$mod_statuses['orderpro_export_orders_btn'] = true;
			}
		}

		// novaposhta, ukrposhta, justin
		if ($this->config->get('novaposhta_status') || $this->config->get('shipping_novaposhta_status') || $this->config->get('ukrposhta_status') || $this->config->get('shipping_ukrposhta_status') || $this->config->get('justin_status') || $this->config->get('shipping_justin_status')) {
			$mod_statuses['nova_or_ukrposhta_list_btn'] = true;
		} else {
			$mod_statuses['nova_or_ukrposhta_list_btn'] = false;
		}

		if ($code && isset($mod_statuses[$code.'_btn'])) {
			return $mod_statuses[$code.'_btn'];
		}

		return $mod_statuses;
	}


	// ОБЩИЕ переменные страницы, выводятся в шаблонах страницы ->

	// доп. элементы ряда фильтров для  таблиц заказов ->
	// список ключей, сюда добавлять все filter_id из orderTableFilterRowElemList
	public function orderTableFilterRowElemListFilterIdes() {
		return array(
			'filter_btns_icon',
			'filter_btns_text',
			'filter_btns_icon_text',
		);
	}

	// массив элементов для добавления в массив фильтров при настройке таблицы заказов
	public function orderTableFilterRowElemList() {
		$filter_btns = array();

		$filter_btns[] = array(
			'template_id' => 'filter_btns',
			'code' => '',
			'filter_id' => 'filter_btns_icon',
			'name' => 'Кнопки: Применить и Очистить фильтры (иконки)',
			'description' => '',
		);

		$filter_btns[] = array(
			'template_id' => 'filter_btns',
			'code' => '',
			'filter_id' => 'filter_btns_text',
			'name' => 'Кнопки: Применить и Очистить фильтры (текст)',
			'description' => '',
		);

		$filter_btns[] = array(
			'template_id' => 'filter_btns',
			'code' => '',
			'filter_id' => 'filter_btns_icon_text',
			'name' => 'Кнопки: Применить и Очистить (иконки и текст)',
			'description' => '',
		);

		return $filter_btns;
	}

	// html-код элементов ряда фильтров, возвращается по ключу
	public function orderTableFilterRowElem($key = '') {
		$tpl = '';
		if ($key == 'filter_btns_icon') {
			$tpl =  '<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
							<a class="btn btn-default btn-sm" data-toggle="tooltip" title="Очистить все фильтры" data-btnaction="filter_clear"><i class="fa fa-times-circle"></i></a>
							<a class="btn btn-primary btn-sm" data-toggle="tooltip" title="Применить фильтры" data-btnaction="filter_apply"><i class="fa fa-filter"></i></a>
						</div>';
		}

		if ($key == 'filter_btns_text') {
			$tpl =  '<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
							<a class="btn btn-default btn-sm" title="Очистить все фильтры" data-btnaction="filter_clear">Очистить</a>
							<a class="btn btn-primary btn-sm" title="Применить фильтры" data-btnaction="filter_apply">Применить</a>
						</div>';
		}

		if ($key == 'filter_btns_icon_text') {
			$tpl =  '<div class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
							<a class="btn btn-default btn-sm" data-toggle="tooltip" title="Очистить все фильтры" data-btnaction="filter_clear"><i class="fa fa-times-circle"></i>&nbsp; Очистить</a>
							<a class="btn btn-primary btn-sm" data-toggle="tooltip" title="Применить фильтры" data-btnaction="filter_apply"><i class="fa fa-filter"></i>&nbsp; Применить</a>
						</div>';
		}

		return $tpl;
	}
	// <- доп. элементы ряда фильтров для  таблиц заказов

	// список переменных значений, добавлять из pageValueVars
	public function pageValueVarsList() {
		$prefix = 'pageValueVar_';
		$vars = array(
			'[[{' .$prefix .'config_processing_status}]]' 				=> 'Все ID статусов "В процессе" (через запятую)',
			'[[{' .$prefix .'config_processing_status_total}]]' 	=> 'Общее кол-во заказов "В процессе"',
			'[[{' .$prefix .'config_complete_status}]]'					=> 'Все ID статусов "Завершенные" (через запятую)',
			'[[{' .$prefix .'config_complete_status_total}]]' 		=> 'Общее кол-во заказов "Завершенные"',
			'[[{' .$prefix .'total_orders_status_0}]]' 					=> 'Общее кол-во заказов "Брошенные" (статус 0)',
			'[[{' .$prefix .'total_orders_status_notnull}]]' 			=> 'Общее кол-во всех заказов, кроме брошенных',
		);

		$vars = $this->extendListWithCustom($vars, 'pageValueVarsList');

		return $vars;
	}

	// переменные для вывода на страницу определенных значений, ключи добавлять в pageValueVarsList
	// * переменные использовать в шаблонах Элементов страниц

	public function pageValueVars($key = '', $all_orders_data = array()) {
		if ($key) {
			// (!) список всех переменных данного метода - дополнять какждый раз при добавлении новой переменной
			$key_list = array(
				'config_processing_status',
				'config_processing_status_total',
				'config_complete_status',
				'config_complete_status_total',
				'total_orders_status_0',
				'total_orders_status_notnull',
			);

			if (in_array($key, $key_list)) {

				if ($key == 'config_processing_status') {
					return implode(',', $this->config->get('config_processing_status'));
				}

				if ($key == 'config_processing_status_total') {
					return $this->ompro->getTotalOrdersOC(array('filter_order_status' => implode(',', $this->config->get('config_processing_status'))));
				}

				if ($key == 'config_complete_status') {
					return implode(',', $this->config->get('config_complete_status'));
				}

				if ($key == 'config_complete_status_total') {
					return $this->ompro->getTotalOrdersOC(array('filter_order_status' => implode(',', $this->config->get('config_complete_status'))));
				}

				if ($key == 'total_orders_status_0') {
					return $this->ompro->getTotalOrdersOC(array('filter_order_status' => '0'));
				}

				if ($key == 'total_orders_status_notnull') {
					return $this->ompro->getTotalOrdersOC();
				}

			} else {
				return $this->omproapicustom->pageValueVars($key, $all_orders_data);
			}
		}
	}

	public function getTablePageValueVars() {
		$html = '';
		$vars_list = $this->pageValueVarsList();

		$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
		$html .= '<thead>';
		$html .= ' <tr>';
		$html .= '  <th class="text-left">Переменная</th>';
		$html .= '  <th class="text-left">Значение</th>';
		$html .= ' </tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		foreach ($vars_list as $var => $title) {
			$html .= ' <tr>';
			$html .= '  <td class="text-left">'.$var.'</td>';
			$html .= '  <td class="text-left">'.$title.'</td>';
			$html .= ' </tr>';
		}

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}

	public function getQuickFilterTriggerInfo() {
		$html = '';
		$vars_list = $this->pageValueVarsList();

		$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
		$html .= '<thead>';
		$html .= ' <tr>';
		$html .= '  <th colspan="2">Элементы-Триггеры для быстрой фильтрации заказов (срабатывает любой существующий фильтр из вашего списка (на странице может его не быть) по клику на элемент-триггер). Обязательный класс элемента <span class="text-red">quick-filter-trigger</span> а также аттрибуты  <span class="text-red">data-filter_id</span> и <span class="text-red">data-filter_value</span>.</th>';
		$html .= ' </tr>';
		$html .= ' <tr>';
		$html .= '  <th class="text-left">Пример</th>';
		$html .= '  <th class="text-left">Описание</th>';
		$html .= ' </tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		$html .= ' <tr>';
		$html .= '  <td class="text-left">' . htmlspecialchars('<a class="quick-filter-trigger" data-filter_id="filter_order_status_id"  data-filter_value="1,10">Фильтр по статусам заказа (ID у которых 1 и 10)</a>') . '</td>';
		$html .= '  <td class="text-left">';
		$html .= '  Ссылка для запуска фильтра "filter_order_status_id" со значением "1,10"';
		$html .= '  </td>';
		$html .= ' </tr>';

		$html .= ' <tr>';
		$html .= '  <td class="text-left">' . htmlspecialchars('<button class="btn btn-default quick-filter-trigger" data-filter_id="filter_payment_method" data-filter_value="cod" >Фильтр по коду оплаты "cod"</button>') . '</td>';
		$html .= '  <td class="text-left">';
		$html .= '  Кнопка для запуска фильтра "filter_payment_method" со значением "cod"';
		$html .= '  </td>';
		$html .= ' </tr>';

		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}


	// список переменных Html - элементов для конструктора, добавлять из pageHtmlElemVars
	public function pageHtmlElemVarsList() {
		$vars = array();

		/* блочные элементы - в конструктор загружаются как есть */
		$blocks_el_list = array(
			'total_ordersbystatus_btns_column' => 'Кнопки быстрых фильтров по статусу (с кол-вом заказов, в колонках sm-2)',
		);

		$blocks_el_list = $this->extendListWithCustom($blocks_el_list, 'pageHtmlElemVarsBlockList');

		$vars[] = array('target' => 'api_blocks_el', 'class' => 'blocks-el', 'icon' => 'columns', 'text' => 'API элементы: ряды, колонки, боксы (метод pageHtmlElemVars)', 'vars' => $blocks_el_list);


		/* элементы колонок - для сортировки в конструкторе обворачиваются в
		<div class="row"><div class="col col-sm-12">КОД_ЭЛЕМЕНТА</div></div> */

		$column_el_list = array(
			'total_ordersbystatus_btns_inline' => 'Кнопки быстрых фильтров по статусу (с кол-вом заказов, в один ряд)',
		);

		$column_el_list = $this->extendListWithCustom($column_el_list, 'pageHtmlElemVarsColumnList');

		$vars[] = array('target' => 'api_column_el', 'class' => 'column-el', 'icon' => 'server', 'text' => 'API элементы: эл-ты колонок (метод pageHtmlElemVars)', 'vars' => $column_el_list);


		/* элементы панели инструментов - для сортировки в конструкторе вставляются в бокс
		<div class="row"><div class="col col-sm-12"><div class="box box-default"><div class="box-header with-border"><div class="box-tools pull-right">КОД_ЭЛЕМЕНТА</div></div></div></div></div> */

		$tools_el_list = array(
			'aridiuscallback_btn' => 'HTML-кнопка Заказать звонок (aridiuscallback)',
		);

		$tools_el_list = $this->extendListWithCustom($tools_el_list, 'pageHtmlElemVarsToolsList');

		$vars[] = array('target' => 'api_tools_el', 'class' => 'tools-el', 'icon' => 'ellipsis-h', 'text' => 'API элементы: эл-ты панели инструментов (метод pageHtmlElemVars)', 'vars' => $tools_el_list);


		/* элементы групп кнопок и полей - для сортировки в конструкторе вставляются в
		<div class="row"><div class="col col-sm-12"><div class="btn-group">КОД_ЭЛЕМЕНТА</div></div></div> */
		$btngroup_el_list = array(
		//	'key' => 'name',
		);

		$btngroup_el_list = $this->extendListWithCustom($btngroup_el_list, 'pageHtmlElemVarsBtnGroupList');

		$vars[] = array('target' => 'api_btngroup_el', 'class' => 'btngroup-el', 'icon' => 'object-group', 'text' => 'API элементы: эл-ты групп кнопок и полей (метод pageHtmlElemVars)', 'vars' => $btngroup_el_list);


		return $vars;
	}

	// переменные для вывода на страницу Html-элементов, ключи добавлять в pageHtmlElemVarsList
	public function pageHtmlElemVars($var = '', $all_orders_data = array()) {
		if ($var) {
			$strtoken = $this->ompro->strtoken;

			// (!) список всех переменных данного метода - дополнять какждый раз при добавлении новой переменной
			$vars_list = array(
				'aridiuscallback_btn',
				'total_ordersbystatus_btns_inline',
				'total_ordersbystatus_btns_column',
			);

			if (in_array($var, $vars_list)) {
				// (!) Элемент должен иметь обязательный атрибут "contentvar" с названием переменной: contentvar="'.$var.'"

				if ($var == 'total_ordersbystatus_btns_inline' || $var == 'total_ordersbystatus_btns_column') {
					$setting_user_group = $source = array();
					if ($all_orders_data && isset($all_orders_data['param']) && isset($all_orders_data['param']['setting_user_group'])) {
						$setting_user_group = $all_orders_data['param']['setting_user_group'];
						$source = $setting_user_group['order_statuses'];
					}

					$statuses = $this->getOrderStatuses($setting_user_group);
					$text_color_id = $back_color_id = $margin_left = '';

					if ($var == 'total_ordersbystatus_btns_column') {
						$html = '<div contentvar="'.$var.'" class="row">';
						$block = 'btn-block';
					} else {
						$html = '<div contentvar="'.$var.'">';
						$block = '';
					}

					foreach ($statuses as $status) {
						$total = $this->ompro->getTotalOrdersOC(array('filter_order_status' => ''.$status['id']));

						if ($source) {
							$text_color_id = isset($source[$status['id']]['text_color_id']) && !empty($source[$status['id']]['text_color_id']) ? '#'.$source[$status['id']]['text_color_id'] : '';
							$back_color_id = isset($source[$status['id']]['back_color_id']) && !empty($source[$status['id']]['back_color_id']) ? '#'.$source[$status['id']]['back_color_id'] : '';
						}

						$color_str = $text_color_id ? 'color:' . $text_color_id . '; '  : '';
						$back_color_str = $back_color_id ? 'background-color:' . $back_color_id . ';'  : '';
						$label_bg = $text_color_id ? $text_color_id: 'red';

						if ($var == 'total_ordersbystatus_btns_column') {
							$html .= '<div class="col col-sm-2">';
						}

						$html .= '<a class="btn btn-default quick-filter-trigger '.$block.'" data-filter_id="filter_order_status_id" data-filter_value="'.$status['id'].'" style="margin-bottom: 5px; border-color: #ddd; '. $margin_left . $color_str . $back_color_str.'" data-toggle="tooltip" title="Кликните для фильтрации по данному статусу"><span class="label  pull-left" style="background-color:' . $label_bg . ';" >'.$total.'</span> <i class="fa fa-shopping-cart" style="margin-left: 5px;"></i><span class ="hidden-xs hidden-sm">&nbsp '.$status['text'].'</span></a>';

						if ($var == 'total_ordersbystatus_btns_column') {
							$html .= '</div>';
						} else {
							$margin_left = 'margin-left: 5px; ';
						}
					}

					$html .= '</div>';

					return $html;
				}

				// aridiuscallback_btn
				if ($var == 'aridiuscallback_btn') {
					$qry = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "aridiuscallback'");
					if ($qry->num_rows) {
						$link = $this->url->link('catalog/aridiuscallback', $strtoken, true);
						$this->load->model('catalog/aridiuscallback');
						$calback_total = $this->model_catalog_aridiuscallback->getTotalOrder();

						if (empty($calback_total)) { $calback_total = 0; }

						return '<a contentvar="'.$var.'" href="'.$link.'" target="_blank" class="btn btn-default btn-sm"><span class="label label-danger pull-left">'.$calback_total.'</span> <i class="fa fa-phone fa-lg" style="margin-left: 5px;"></i><span class ="hidden-xs hidden-sm">&nbsp Заказать звонок</span></a>';
					} else {
						return 'Ошибка: aridiuscallback не установлен!';
					}
				}

			} else {
				return $this->omproapicustom->pageHtmlElemVars($var);
			}
		}
	}
	// <- общие переменные страницы


	/* КОНСТРУКТОР */
	///// HTML Элементы страниц ->

	public function groups($tpl_id = 0, $quantity = 3) {
		$groups = array();

		$btn_default = '';
		for ($i = 1; $i <= $quantity; $i++) {
			$btn_default .= '<a class="btn btn-default" title="Checkbox '.$i.'" ><i class="fa fa-external-link"></i> Button '.$i.'</a>';
		}

		$id = 'default_group';
		$groups[$id] = array(
			'id' => $id,
			'title' => 'Группа кнопок',
			'tpl' => '<div class="btn-group btn-group-sm" data-toggle="buttons">' .$btn_default .'</div>'
		);

		$btn_checkbox = '';
		for ($i = 1; $i <= $quantity; $i++) {
			$btn_checkbox .= '<a class="btn btn-primary" title="Checkbox '.$i.'" ><input type="checkbox" /><i class="fa fa-check-square-o"></i> Checkbox '.$i.'</a>';
		}

		$id = 'checkbox_group';
		$groups[$id] = array(
			'id' => $id,
			'title' => 'Группа кнопок-переключателей Checkbox',
			'tpl' => '<div class="btn-group btn-group-sm" data-toggle="buttons">' .$btn_checkbox .'</div>'
		);

		$btn_radio = '';
		for ($i = 1; $i <= $quantity; $i++) {
			$btn_radio .= '<a class="btn btn-success" title="Radio '.$i.'" ><input type="radio"/><i class="fa fa-dot-circle-o"></i> Radio '.$i.'</a>';
		}

		$id = 'radio_group';
		$groups[$id] = array(
			'id' => $id,
			'title' => 'Группа кнопок-переключателей Radio',
			'tpl' => '<div class="btn-group btn-group-sm" data-toggle="buttons">' .$btn_radio .'</div>'
		);

		return $groups;
	}

	public function btngroups($tpl_id = 0, $quantity = 3) {
		$elements = array();

		$groups = $this->groups($tpl_id, $quantity);
		foreach ($groups as $key => $group) {
			$elements[$key] = array(
				'id' => $key,
				'title' => $group['title'],
				'tpl' =>
				   '<div class="box box-default">'
					  .'<div class="box-header with-border">'
						.'<div class="box-tools">'
						  .$group['tpl']
						.'</div>'
					  .'</div>'
				  .'</div>'
			);
		}

		if ($tpl_id) {
			return $elements[$tpl_id];
		} else {
			return $elements;
		}
	}

	// dynamic chnge btn
	public function order_invoice_btn($order_info = array()) {
		if (!$order_info['invoiceno']) {
			return '<button type="button" id="button-invoice[[{order_id}]]" data-loading-text="..." data-orderid="[[{order_id}]]" data-toggle="tooltip" title="'.$this->language->get('entry_invoice_no').'" class="btn btn-success btn-xs create-invoice"><i class="fa fa-cog"></i></button>';
		} else {
			return '<button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-cog"></i></button>';
		}
	}

	public function order_reward_btn($order_info = array()) {
		if ($order_info['customer_id'] && $order_info['reward']) {
			if (!$order_info['order_reward_total']) {
				if ($order_info['reward'] == '0' ) {
					return '<button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i></button>';
				} else {
					return '<button type="button" id="button-reward-add[[{order_id}]]" data-loading-text="..." data-orderid="[[{order_id}]]" data-toggle="tooltip" title="'.$this->language->get('button_reward_add').'" class="btn btn-success btn-xs reward-add"><i class="fa fa-plus-circle"></i></button>';
				}
			} else {
				return '<button id="button-reward-remove[[{order_id}]]" data-loading-text="..." data-orderid="[[{order_id}]]" data-toggle="tooltip" title="'.$this->language->get('button_reward_remove').'" class="btn btn-danger btn-xs reward-remove"><i class="fa fa-minus-circle"></i></button>';
			}
		} else {
			return '<button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i></button>';
		}
	}

	public function order_commission_btn($order_info = array()) {
		if ($order_info['affiliate_id']) {
			if (!$order_info['order_commission_total']) {
				return '<button type="button" id="button-commission-add[[{order_id}]]" data-loading-text="..." data-orderid="[[{order_id}]]" data-toggle="tooltip" title="'.$this->language->get('button_commission_add').'" class="btn btn-success btn-xs commission-add"><i class="fa fa-plus-circle"></i></button>';
			} else {
				return '<button id="button-commission-remove[[{order_id}]]" data-loading-text="..." data-orderid="[[{order_id}]]" data-toggle="tooltip" title="'.$this->language->get('button_commission_remove').'" class="btn btn-danger btn-xs commission-remove"><i class="fa fa-minus-circle"></i></button>';
			}
		} else {
			return '<button disabled="disabled" class="btn btn-success btn-xs"><i class="fa fa-plus-circle"></i></button>';
		}
	}

	public function affiliate_link($affiliate_id) {
		if ($this->ompro->ocversion >= 300) {
			return '(<a href="'.$this->url->link('customer/customer/edit', 'user_token='.$this->session->data['user_token'] . '&customer_id=' . $affiliate_id, 'SSL').'" target="_blank">[[{affiliate}]]</a>)';
		} else {
			return '(<a href="'.$this->url->link('marketing/affiliate/edit', 'token='.$this->session->data['token'] . '&affiliate_id=' . $affiliate_id, 'SSL').'" target="_blank">[[{affiliate}]]</a>)';
		}
	}

	public function btnActionList($exlude_one_order_action = false) {
		$pages = $this->ompro->getPageList();
		$actions = array();

		foreach ($pages as $page) {
			$id = 'pageid_'.$page['template_id'];
			$actions[$id] = array(
				'template_id' => 0,
				'code' => 0,
				'action' => $id,
				'title' => 'Кнопка страницы: ' . $page['name']
			);
		}

		$action_list = array(
			'print_orders' => 'Печать данных заказа',
			'print_orders_table' => 'Печать таблиц Заказов',
			'print_products_table' => 'Печать таблицы Товаров',
			'excel_orders' => 'Экспорт Заказов в Excel',
			'excel_orders_products' => 'Экспорт Товаров в Excel',
			'send_mail' => 'Отправить E-mail',
			'send_sms' => 'Отправить SMS',
			'send_tlgrm' => 'Отправить Telegram уведомление'
		);

		foreach ($action_list as $action => $title) {
			$type = $action;
			if ($type == 'send_mail' || $type == 'send_sms' || $type == 'send_tlgrm') {
				$type = str_replace('send_', '', $type);
			}
			$templates = $this->ompro->getAllTemplatesList($type);
			foreach ($templates as $template) {
				$id = $action.'_'.$template['code'];
				$actions[$id] = array(
					'template_id' => $template['template_id'],
					'code' => $template['code'],
					'action' => $action,
					'title' => 'Кнопка '.$title.': ' . $template['name']
				);
			}
		}

		$one_action_list = array(
			'filter_clear' 						=> 'Кнопка: Очистить все фильтры',
			'filter_apply'						=> 'Кнопка: Применить фильтры',
			'clear_filter_this'				=> 'Кнопка: Очистить фильтр',
			'apply_batch'					=> 'Кнопка: Выполнить пакетную обработку заказов',
			'slide_on_id'					=> 'Кнопка: Свернуть элемент по ID',
			'slide_on_class'				=> 'Кнопка: Свернуть элемент(ы) по классу',

			'widget_collapse'			=> 'Виджет:  Свернуть бокс',
			'widget_remove'				=> 'Виджет:  Удалить бокс',

			'order_add'						=> 'Кнопка: Добавить заказ',
			'order_delete'					=> 'Кнопка: Удалить заказ(ы)',

			'order_info'						=> 'Кнопка: Просмотр заказа Opencart',
			'order_edit'						=> 'Кнопка: Редактор заказа Opencart',
			'order_histories_oc'		=> 'Кнопка: Просмотр истории (Opencart)',
			'order_history_view'		=> 'Кнопка: Просмотр истории',

			'print_shipping' 				=> 'Кнопка: Печать списка доставки (Opencart)',
			'print_invoice'					=> 'Кнопка: Печать счёта (Opencart)',

			'print_orders'					=> 'Кнопка для селектора: Печать данных Заказа',
			'print_orders_table'			=> 'Кнопка для селектора: Печать таблицы Заказов',
			'print_products_table'		=> 'Кнопка для селектора: Печать таблицы Товаров',

			'excel_orders'					=> 'Кнопка для селектора: Экспорт Заказов в Excel',
			'excel_orders_products'	=> 'Кнопка для селектора: Экспорт Товаров в Excel',

			'send_mail'						=> 'Кнопка для селектора: Отправить E-mail',
			'send_sms'						=> 'Кнопка для селектора: Отправить SMS',
			'send_tlgrm'					=> 'Кнопка для селектора: Отправить Telegram уведомление',
		);

		$orderpro_status = $this->orderpro_status();

		if ($orderpro_status) {
			$one_action_list['orderpro_edit'] = 'Кнопка: Редактор заказа OrderPro';
			$one_action_list['orderpro_add'] = 'Кнопка: Добавить заказ OrderPro';
			$one_action_list['orderpro_merge_orders'] = 'Кнопка: Объединить заказы OrderPro';
			$one_action_list['orderpro_export_orders'] = 'Кнопка: Экспорт Заказов в Excel OrderPro';
			if ($this->config->get('orderpro_invoice_type')) {
				$one_action_list['orderpro_invoice'] = 'Кнопка: Печать счёта OrderPro';
			}
		}

		// действия для одного заказа
		$one_order_action_list = array('order_info','order_edit','orderpro_edit','order_histories_oc','order_history_view');

		// orderq
		if ($this->orderq_status()) {
			$one_action_list['orderq_add'] = 'Кнопка: Добавить заказ CustomizedOrderEntry';
			$one_action_list['orderq_edit'] = 'Кнопка: Редактор заказа CustomizedOrderEntry';
			$one_order_action_list[] = 'orderq_edit';
		}

		foreach ($one_action_list as $id => $title) {
			if ($exlude_one_order_action) {
				if (!in_array($id, $one_order_action_list)) {
					$actions[$id] = array(
						'template_id' => 0,
						'code' => 0,
						'action' => $id,
						'title' => $title
					);
				}
			} else {
				$actions[$id] = array(
					'template_id' => 0,
					'code' => 0,
					'action' => $id,
					'title' => $title
				);
			}
		}

		return $actions;
	}

	///// <- Элементы страниц

	// Таблицы переменных и атрибутов элементов
	public function getTableBtnAction() {
		$strtoken = $this->ompro->strtoken;

		$html = '<table class="table-mini full-width" style="margin-bottom: 0;">';
		$html .= '<thead>';
		$html .= ' <tr><th colspan="4">Атрибуты кнопок для присвоения им соотвестствующих действий</th></tr>';
		$html .= ' <tr>';
		$html .= '  <th class="text-left">Атрибуты</th>';
		$html .= '  <th class="text-left">Действие кнопки</th>';

		$html .= '  <th class="text-center text-blue" style="width: 1px;" data-toggle="tooltip" title="ID шаблона в атрибутах">ID</th>';
		$html .= '  <th class="text-center text-blue" style="width: 1px;" data-toggle="tooltip" title="Редактировать шаблон (доступно, если для действия кнопки используется шаблон)">Edit</th>';

		$html .= ' </tr>';
		$html .= '</thead>';
		$html .= '<tbody>';

		$tbtn_actions = $this->btnActionList(true);

		foreach ($tbtn_actions as $tbtn_action) {
			$html .= ' <tr>';
			$tbtn_action_str = 'data-btnaction="'.$tbtn_action['action'].'"';
			if ($tbtn_action['code']) {
				$tbtn_action_str .= ' data-'.$tbtn_action['action'].'_tpl="'.$tbtn_action['code'].'"';
			}
			$html .= '<td class="text-left">'.$tbtn_action_str.'</td>';
			$html .= '<td class="text-left">'.$tbtn_action['title'].'</td>';

			if (preg_match('/pageid_/', $tbtn_action['action'])) {
				$type = 'pages';
				$template_id = str_replace('pageid_', '', $tbtn_action['action']);
			} else {
				$type = $tbtn_action['action'];
				if ($type == 'send_mail' || $type == 'send_sms' || $type == 'send_tlgrm') {
					$type = str_replace('send_', '', $type);
				}
				$template_id = $tbtn_action['template_id'];
			}

			if ($template_id) {
				$html .= '<td class="text-left">'.$template_id.'</td>';
				$html .= '<td class="text-center"><a class="btn btn-primary btn-xs" href="'.$this->url->link('sale/ompro/templateEdit', $strtoken . '&get_page='.$type.'&template_id='.$template_id, 'SSL').'" target="_blank"><i class="fa fa-edit"></i></a></td>';
			} else {
				$html .= '<td></td><td></td>';
			}
			$html .= ' </tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}

	public function getTableBtnActionAdding() {
		return '<table class="table-mini full-width" style="margin-bottom: 0;">'
				.'<thead>'
					.'<tr><th colspan="2">Кнопки с атрибутом `onclick`</th></tr>'
					.'<tr><th>Значение атрибута `onclick`</th><th>Действие кнопки</th></tr>'
				.'</thead>'
				.'</tbody>'
					.'<tr><td>orderReload([[{order_id}]], $(this).closest(\'.table-orders\').attr(\'id\'))</td><td>Обновить заказ</td></tr>'
					.'<tr><td>orderTplView([[{order_id}]], \'код\', \'метод\', `редактирование`, \'источник\')</td><td>Просмотр-копирование таблицы заказа &nbsp;&nbsp;<i class="btn btn-xs fa fa-question-circle text-blue" data-toggle="popover" title="Аргументы orderTplView:"  data-content="1)  [[{order_id}]] - номер заказа, 2) `код` - код таблицы заказов, 3) `метод` - метод просмотра, 3 варианта:  а) #ID(или .class) - id или класс элемента страницы, куда будет вставлена таблица); б) `modal` - просмотр в модальном окне; в) `copytext` - только копировать текст без просмотра шаблона. 4) `редактирование` - значения 1 или 0 - включить быстрое редактирование или нет при просмотре, 5) `источник` - источник копирования - #ID(или .class) элемента таблицы, текст которого будет скопирован в буфер"></i></td></tr>'
					.'<tr><td>ordersTableView(\'код\', \'фильтры\', \'метод\', `лимит`, `редактирование`, \'метод\')</td><td>Просмотр всех заказов покупателя &nbsp;&nbsp; <i class="btn btn-xs fa fa-question-circle text-blue" data-toggle="popover" title="Аргументы ordersTableView:" data-content="1)  `код` - код таблицы заказов, 2) `фильтры` - фильтры отбора заказов (например: \'filter_customer_id={customer_id2}&filter_order_status_id={order_status_id}\'), 3) `лимит` - кол-во заказов на странице (цифра), 4) `редактирование` - значения 1 или 0 - включить быстрое редактирование или нет при просмотре, 5) `метод` - метод просмотра, 2 варианта:  а) #ID(или .class) - id или класс элемента страницы, куда будет вставлена таблица); б) `modal` - просмотр в модальном окне"></i></td></tr>'
				.'</tbody>'
			.'</table>';
	}

	public function getTableBtnActionQuickStatus() {
		$html = '';
		$statuses = $this->getOrderStatuses();

		if ($statuses) {
			$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
			$html .= '<thead>';
			$html .= ' <tr><th colspan="2">Кнопки быстого изменения статуса заказа</th></tr>';
			$html .= ' <tr>';
			$html .= '  <th class="text-left">Пример кода</th>';
			$html .= '  <th class="text-left">Статус</th>';
			$html .= ' </tr>';
			$html .= '</thead>';
			$html .= '<tbody>';

			foreach ($statuses as $status) {
				$html .= ' <tr>';
				$html .= '  <td class="text-left">';
				$html .= htmlspecialchars('<a class="btn btn-danger" data-btnaction="order_quick_status" data-orderid="[[{order_id}]]" data-orderstatusid="'.$status['id'].'">'.$status['text'].'</a>');
				$html .= '  </td>';
				$html .= '  <td class="text-left">'.$status['text'].'</td>';
				$html .= ' </tr>';
			}
			$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
	}

	public function getTableBtnHtmlTemplates($target = '') {
		$html = '';
		$templates = array();
		$strtoken = $this->ompro->strtoken;

		$type = $target;
		if ($target == 'send_mail' || $target == 'send_sms' || $target == 'send_tlgrm') {
			$type = str_replace('send_', '', $target);
		}

		if ($type) {
			$templates = $this->ompro->getAllTemplatesList($type);
		}

		if ($templates) {
			$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
			$html .= '<thead>';
			$html .= ' <tr><th colspan="4">'.$this->language->get('text_'.$target.'_table_tpl').'</th></tr>';
			$html .= ' <tr>';
			$html .= '  <th class="text-left" style="width: 1px;" >ID</th>';
			$html .= '  <th class="text-left">'.$this->language->get('text_tpl_code').'</th>';
			$html .= '  <th style="min-width: 300px;" class="text-left">'.$this->language->get('text_tpl_name').'</th>';
			$html .= '  <th class="text-center" style="width: 1px;" >Edit</th>';
			$html .= ' </tr>';
			$html .= '</thead>';
			$html .= '<tbody>';

			foreach ($templates as $table) {
				$html .= ' <tr>';
				$html .= '  <td class="text-left">'.$table['template_id'].'</td>';
				$html .= '  <td class="text-left">'.$table['code'].'</td>';
				$html .= '  <td class="text-left">'.$table['name'].'</td>';
				$html .= '  <td class="text-center"><a class="btn btn-primary btn-xs" href="'.$this->url->link('sale/ompro/templateEdit', $strtoken . '&get_page='.$type.'&template_id='.$table['template_id'], 'SSL').'" target="_blank"><i class="fa fa-edit"></i></a></td>';
				$html .= ' </tr>';
			}
			$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
	}

	public function getTableBtnOpenWindow() {
		return '<table class="table-mini full-width" style="margin-bottom: 0;">'
				.'<thead>'
					.'<tr><th colspan="2">Кнопки действий с переходом на другую страницу</th></tr>'
					.'<tr><th>Значение атрибута `data-btnaction`</th><th>Действие кнопки</th></tr>'
				.'</thead>'
				.'</tbody>'
					.'<tr><td>order_add</td><td>Добавить заказ Opencart</td></tr>'
					.'<tr><td>order_edit</td><td>Редактор заказа Opencart</td></tr>'
					.'<tr><td>order_info</td><td>Просмотр заказа Opencart</td></tr>'
					.'<tr><td>orderpro_add</td><td>Добавить заказ OrderPro</td></tr>'
					.'<tr><td>orderpro_edit</td><td>Редактор заказа OrderPro</td></tr>'
				.'</tbody>'
			.'</table>';
	}

	public function getTableTemplates($target = 'product') {
		$strtoken = $this->ompro->strtoken;

		$html = '';
		$templates = $this->ompro->getAllTemplatesList($target);

		if ($templates) {
			$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
			$html .= '<thead>';
			$html .= ' <tr><th colspan="5">'.$this->language->get('text_example_'.$target.'_table_tpl').'&nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="'.$this->language->get('text_'.$target.'_table_var_help').'" </i> </th></tr>';
			$html .= ' <tr>';
			$html .= '  <th class="text-left" style="width: 1px;" >ID</th>';
			$html .= '  <th class="text-left">'.$this->language->get('text_tpl_code').'</th>';
			$html .= '  <th style="min-width: 300px;" class="text-left">'.$this->language->get('text_tpl_name').'</th>';
			$html .= '  <th class="text-center" style="width: 1px;" >Edit</th>';
			$html .= ' </tr>';
			$html .= '</thead>';
			$html .= '<tbody>';
			foreach ($templates as $table) {
				$html .= ' <tr>';
				$html .= '  <td class="text-left">'.$table['template_id'].'</td>';
				$html .= '  <td class="text-left">'.$table['code'].'</td>';
				$html .= '  <td class="text-left">'.$table['name'].'</td>';
				$html .= '  <td class="text-center"><a class="btn btn-primary btn-xs" href="'.$this->url->link('sale/ompro/templateEdit', $strtoken . '&get_page='.$target.'&template_id='.$table['template_id'], 'SSL').'" target="_blank"><i class="fa fa-edit"></i></a></td>';
				$html .= ' </tr>';
			}
			$html .= '</tbody>';
			$html .= '</table>';
		}
		return $html;
	}

	public function getTableOptionVars()  {
		$option_vars = array(
			'orderPageSelectOptions' 			=>  'Список Страниц заказов',
			'orderLimitOptions' 					=>  'Кол-во заказов на странице (полный список)',
			'orderLimitShortOptions' 			=>  'Кол-во заказов на странице (короткий список)',
			'printOrdersOptions' 					=>  'Список шаблонов печати данных заказа',
			'printOrdersTableOptions' 			=>  'Список шаблонов печати таблиц Заказов',
			'printProductsTableOptions' 		=>  'Список шаблонов печати таблиц Товаров',
			'excelOrdersOptions' 					=>  'Список шаблонов экспорта Заказов в Excel',
			'excelOrdersProductsOptions' 	=>  'Список шаблонов экспорта Товаров в Excel',
			'sendMailOptions' 						=>  'Список шаблонов писем',
			'sendSmsOptions' 						=>  'Список шаблонов СМС',
			'sendTlgrmOptions' 					=>  'Список шаблонов Телеграм уведомлений',
			'customCommentsOptions' 		=>  'Список шаблонов произвольных Комментариев',
			'batchstatuses' 							=>  'Статусы заказов',
		);

		$html = '';
		$html .= '<table class="table-mini full-width" style="margin-bottom: 0;">';
		$html .= '<thead>';
		$html .= ' <tr><th colspan="2">Атрибуты селекторов для вывода списка значений &nbsp;&nbsp;<i class="fa fa-info-circle text-blue" data-toggle="tooltip" title="Данный способ необходим для вывода изменяющихся значений списка. Добавьте указанный атрибут из таблицы к тегу селектора, чтобы выводить список соответствующих значений (опций)." </i></th></tr>';
		$html .= ' <tr>';
		$html .= '  <th style="min-width: 200px;" class="text-left">Атрибут</th>';
		$html .= '  <th style="min-width: 300px;" class="text-left">Список значений</th>';
		$html .= ' </tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ($option_vars as $key => $text) {
			$html .= ' <tr>';
			$html .= '  <td class="text-left">selectoptions="'.$key.'"</td>';
			$html .= '  <td class="text-left">'.$text.'</td>';
			$html .= ' </tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';

		return $html;
	}

	/* Для  настроек ФИЛЬТРОВ */

	// Типы поля фильтра
	public function getFilterInputTypeList() {
		$types = array();
		$input_types = array('text','select');
		foreach ($input_types as $type) {
			$types[$type] = array('type' => $type, 'name' => $this->language->get('text_filter_input_type_'.$type));
		}
		return $types;
	}

	// Классы валидации поля фильтра
	public function getFilterValidateClassTable() {
		$classes = array();
		$classes[] = array('class' => 'field-input notcyrillics', 'name' => 'кириллические символы недопустимы');
		$classes[] = array('class' => 'field-input digit', 'name' => 'должны быть только цифры');
		$classes[] = array('class' => 'field-input numeric', 'name' => 'должно быть только число');
		$classes[] = array('class' => 'field-input alphanumeric', 'name' => 'должны быть только буквы (латиница, кириллица) и цифры');
		$classes[] = array('class' => 'field-input date', 'name' => 'дата: должна быть в виде: 2021-01-31 или 31.01.2021');
		$classes[] = array('class' => 'field-input datetime', 'name' => 'дата и время: должно быть в виде: 2021-01-31 10:00 или 31.01.2021 10:00');
		$classes[] = array('class' => 'field-input time', 'name' => 'время: должно быть в виде: 10:00 (две пары цифр через двоеточие без пробелов)');
		$classes[] = array('class' => 'field-input digitovercomanotmustbe', 'name' => 'только числа через запятую без пробелов');
		$classes[] = array('class' => 'field-input email', 'name' => 'правильность формата email');

		$html = '<table class="table-mini full-width" style="margin-bottom: 0;">';
		$html .= '<thead>';
		$html .= ' <tr>';
		$html .= '  <th class="text-left">Классы</th>';
		$html .= '  <th class="text-left">На что проверяется вводимое значение</th>';
		$html .= ' </tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		foreach ($classes as $class) {
			$html .= ' <tr>';
			$html .= '  <td class="text-left">'.$class['class'].'</td>';
			$html .= '  <td class="text-left">'.$class['name'].'</td>';
			$html .= ' </tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';

		return $html;


		return $classes;
	}

	// API методы наборов значений для поля фильтра типа  select
	public function getFilterSelectorValuesApiMethodList() {
		$method_list = array(
			'getLimitValues' => 'Заказов на странице',
			'getLimitValuesShort' => 'Заказов на странице (мини список)',
			'getStores' => 'Магазины',
			'getOrderStatuses' => 'Статусы заказа',
			'getPaymentsInstalled' => 'Способы оплаты (без вариантов)',
			'getPayments' => 'Способы оплаты (найденные варианты)',
			'getShippingsInstalled' => 'Способы доставки (без вариантов)',
			'getShippings' => 'Способы доставки (найденные варианты)',
			'getActiveUsers' => 'Пользователи',
			'getManagerUsers' => 'Менеджеры',
			'getCourierUsers' => 'Курьеры',
			'getCusromerGroups' => 'Группы покупателей',
			'getCountries' => 'Все страны',
			'getUserGroupPaymentCountries' => 'Страны Плательщика геозон пользователя',
			'getUserGroupShippingCountries' => 'Страны Получателя геозон пользователя',
			'getYesNo' => 'Да/Нет (1/0)',
			'getProductCategories' => 'Товар: Категории',
			'getManufacturers' => 'Товар: Производители',
			'getSupplers' => 'Товар: Поставщики (модуль АОП)',
			'getCurrencies' => 'Валюты (по ID)',
		);

		$values = array();
		foreach ($method_list as $method => $text) {
			$values[] = array('process_method' => $method, 'text' => $text . ' ('.$method.')');
		}
		return $values;
	}

	// Классы поля фильтра типа select (Тип списка)
	public function getFilterInputSelectClassList() {
		$classes = array();
		$class_list = array('select_default','multiselect_single','multiselect');
		foreach ($class_list as $class) {
			$classes[$class] = array('class' => $class, 'name' => $this->language->get('text_filter_class_'.$class));
		}
		return $classes;
	}

	// Размеры поля фильтра
	public function getFilterSizeList() {
		$sizes = array();
		$size_list = array('sm','default','lg');
		foreach ($size_list as $size) {
			$sizes[] = array('size' => $size, 'text' => $this->language->get('text_filter_size_'.$size));
		}
		return $sizes;
	}

	// Классы для AirDatepicker
	public function getDatepickerClassList() {
		$classes = array();
		$class_list = array('datepicker_default','datepicker_multiple');
		foreach ($class_list as $class) {
			$classes[$class] = array('class' => $class, 'name' => $this->language->get('text_filter_class_'.$class));
		}
		return $classes;
	}

	// Операторы сравнения
	public function getOperatorList() {
		$values = array();
		$operator_list = array('=','>=','<=','>','<','!=','LIKE','NOT LIKE');
		foreach ($operator_list as $operator) {
			$operator = htmlspecialchars($operator, ENT_QUOTES);
			$values[] = array('value' => $operator, 'text' => $operator);
		}
		return $values;
	}

	// Типы обработки данных (Сравнивать значение как)
	public function getProcessAsList() {
		$values = array();
		$type_list = array('string','integer','float','date','time');
		foreach ($type_list as $type) {
			$values[] = array('value' => $type, 'text' => $this->language->get('text_process_as_'.$type) . ' ('.$type.')');
		}
		return $values;
	}

	// Плагины и методы при вводе данных фильтра
	public function getFilterInputHandlerTypeList() {
		$types = array();
		$type_list = array('inputmask','autocomplete','datepicker');
		foreach ($type_list as $type) {
			$types[$type] = array('type' => $type, 'name' => $this->language->get('text_filter_type_'.$type));
		}
		return $types;
	}

	// Autocomplete ->

	public function autocomplete($filter_customer_group_id = array()) {
		$json = array();
		if (isset($this->request->get['target'])) {
			if ($this->request->get['target'] == 'customer') {
				$json = $this->autocompleteCustomer($filter_customer_group_id);
			}
			if ($this->request->get['target'] == 'product') {
				$json = $this->autocompleteProduct();
			}
		}
		return $json;
	}

	// методы autocomplete
	public function autocompleteTargetList() {
		return array(
			'customer' => 'Покупатели (поиск по имени, фамилии)',
			'product' => 'Товары (поиск по названию)'
		);
	}

	public function autocompleteCustomer($filter_customer_group_id = array()) {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 5;
		}

		$filter_data = array(
			'filter_customer_group_id'  => $filter_customer_group_id,
			'filter_name'  => $filter_name,
			'start'        => 0,
			'limit'        => $limit
		);

		$results = $this->getCustomers($filter_data);
		foreach ($results as $result) {
			$json[] = array(
				'name' 	=> strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
			);
		}

		$sort_order = array();
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}
		array_multisort($sort_order, SORT_ASC, $json);

		return $json;
	}

	public function autocompleteProduct() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		return $json;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT CONCAT(c.firstname, ' ', c.lastname) AS name FROM " . DB_PREFIX . "customer c";

		$implode = array();

		if (!empty($data['filter_name']) && trim($data['filter_name']) !== '') {
			$sql .= " WHERE CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		} else {
			$sql .= " WHERE CONCAT(c.firstname, ' ', c.lastname) !=''";
		}

		if (!empty($data['filter_customer_group_id']) && is_array($data['filter_customer_group_id'])) {
			$implode[] = " (c.customer_group_id IN (".implode(", ", $data['filter_customer_group_id'])."))";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	// <- Autocomplete

	// HTMLFilter
	public function createHTMLFilter($setting_user_group = array(), $filter_info, $filter_reload = 0, $filter_value = null, $filter_type = 'filter') {
		$html = '';

		if ($filter_info) {
			$btn_clear_tpl = '';
			if (isset($filter_info['btn_clear_status']) && $filter_info['btn_clear_status']) {
				$btn_clear_class = isset($filter_info['btn_clear_class']) && !empty($filter_info['btn_clear_class']) ? $filter_info['btn_clear_class'] : 'btn btn-default';
				$btn_clear_title = isset($filter_info['btn_clear_title']) && !empty($filter_info['btn_clear_title']) ? $filter_info['btn_clear_title'] : '';
				$btn_clear_text = isset($filter_info['btn_clear_text']) && !empty($filter_info['btn_clear_text']) ? $filter_info['btn_clear_text'] : '';
				$btn_clear_icon = isset($filter_info['btn_clear_icon']) && !empty($filter_info['btn_clear_icon']) ? $filter_info['btn_clear_icon'] : 'fa fa-times-circle';

				$btn_clear_icon_tpl = !empty($btn_clear_icon) ? '<i class="'.$btn_clear_icon.'"></i>' : '';
				$btn_clear_tooltip = isset($filter_info['btn_clear_tooltip']) && $filter_info['btn_clear_tooltip'] == 'tooltip' ? 'data-toggle="tooltip"' : '';

				$btn_clear_tpl .= '<span filterBtnClearGroup class="input-group-btn">';
				$btn_clear_tpl .= '  <span data-btnaction="clear_'.$filter_type.'_this" class="'. $btn_clear_class . '" '. $btn_clear_tooltip . ' title="'.$btn_clear_title.'" target-id="'.$filter_info['filter_id'].'" >';

				if ($filter_info['btn_clear_type'] == 'icon') {
					$btn_clear_tpl .= $btn_clear_icon_tpl;
				} elseif ($filter_info['btn_clear_type'] == 'text') {
					$btn_clear_tpl .= $btn_clear_text;
				} elseif ($filter_info['btn_clear_type'] == 'icontext') {
					$btn_clear_tpl .= $btn_clear_icon_tpl . '&nbsp;&nbsp;' . $btn_clear_text;
				} elseif ($filter_info['btn_clear_type'] == 'texticon') {
					$btn_clear_tpl .= $btn_clear_text . '&nbsp;&nbsp;' . $btn_clear_icon_tpl;
				}

				$btn_clear_tpl .= '  </span>';
				$btn_clear_tpl .= '</span>';
			}

			$btn_apply_tpl = '';
			if (isset($filter_info['btn_apply_status']) && $filter_info['btn_apply_status']) {
				$btn_apply_class = isset($filter_info['btn_apply_class']) && !empty($filter_info['btn_apply_class']) ? $filter_info['btn_apply_class'] : 'btn btn-primary';
				$btn_apply_title = isset($filter_info['btn_apply_title']) && !empty($filter_info['btn_apply_title']) ? $filter_info['btn_apply_title'] : '';
				$btn_apply_text = isset($filter_info['btn_apply_text']) && !empty($filter_info['btn_apply_text']) ? $filter_info['btn_apply_text'] : '';
				$btn_apply_icon = isset($filter_info['btn_apply_icon']) && !empty($filter_info['btn_apply_icon']) ? $filter_info['btn_apply_icon'] : 'fa fa-filter';

				$btn_apply_icon_tpl = !empty($btn_apply_icon) ? '<i class="'.$btn_apply_icon.'"></i>' : '';
				$btn_apply_tooltip = isset($filter_info['btn_apply_tooltip']) && $filter_info['btn_apply_tooltip'] == 'tooltip' ? 'data-toggle="tooltip"' : '';

				$btn_apply_tpl .= '<span filterBtnClearGroup class="input-group-btn">';
				$btn_apply_tpl .= '  <span data-btnaction="filter_apply" class="'. $btn_apply_class . '" '. $btn_apply_tooltip . ' title="'.$btn_apply_title.'" target-id="'.$filter_info['filter_id'].'" >';

				if ($filter_info['btn_apply_type'] == 'icon') {
					$btn_apply_tpl .= $btn_apply_icon_tpl;
				} elseif ($filter_info['btn_apply_type'] == 'text') {
					$btn_apply_tpl .= $btn_apply_text;
				} elseif ($filter_info['btn_apply_type'] == 'icontext') {
					$btn_apply_tpl .= $btn_apply_icon_tpl . '&nbsp;&nbsp;' . $btn_apply_text;
				} elseif ($filter_info['btn_apply_type'] == 'texticon') {
					$btn_apply_tpl .= $btn_apply_text . '&nbsp;&nbsp;' . $btn_apply_icon_tpl;
				}

				$btn_apply_tpl .= '  </span>';
				$btn_apply_tpl .= '</span>';
			}

			$input_group_class = isset($filter_info['input_group_class']) && $filter_info['input_group_class'] !=='' ? $filter_info['input_group_class'] : 'input-group';

			$html .= '<span class="'.$input_group_class.'" filterInputGroup '.$filter_type.'ID="'.$filter_info['filter_id'].'">';

			if (!empty($btn_apply_tpl) && $filter_info['btn_apply_position'] == 'left') {
				$html .= $btn_apply_tpl;
			}

			if (!empty($btn_clear_tpl) && $filter_info['btn_clear_position'] == 'left') {
				$html .= $btn_clear_tpl;
			}

			$id = ' id="'.$filter_info['filter_id'].'" ';
			$filter_main_class = isset($filter_info['filter_main_class']) ? $filter_info['filter_main_class'] : '';
			$prefix = $filter_type == 'filter_product' ? 'filtertypeproduct' : $filter_type;

			if ($filter_info['input_type'] == 'select' && $filter_type == 'filter_product') {
				$name = '';
				if (isset($filter_info['select_class']) && $filter_info['select_class'] === 'multiselect_single') {
					$name = ' name="'.$prefix.'_'.$filter_info['filter_id'].'" ';
				} elseif (isset($filter_info['select_class']) && $filter_info['select_class'] === 'multiselect') {
					$name = ' name="'.$prefix.'_'.$filter_info['filter_id'].'[]" ';
				}
			} else {
				$name = ' name="'.$prefix.'_'.$filter_info['filter_id'].'" ';
			}

			$tooltip = !empty($filter_info['tooltip']) ? ' data-toggle="tooltip" title="'.$filter_info['tooltip'].'" ' : ' ';
			$placeholder = isset($filter_info['placeholder']) && !empty($filter_info['placeholder']) ? ' placeholder="'.$filter_info['placeholder'].'" ' : '';
			$reload = ' filterReload="'.$filter_reload.'" ';

			if ($filter_info['input_type'] == 'text') {
				$add_class = '';

				$handler_class = isset($filter_info['handler_class']) ? $filter_info['handler_class'] : '';
				$datepicker_class = isset($filter_info['datepicker_class']) ? $filter_info['datepicker_class'] : '';

				$add_class .= $handler_class .' '. $datepicker_class;

				$dp_position = isset($filter_info['dp_position']) ? ' data-position="'.$filter_info['dp_position'].'" ' : '';

				if (!empty($filter_info['dpParam'])) {
					$dp_param = base64_encode(json_encode($filter_info['dpParam'], true));
					$dp_param = ' readonly airdatepicker dpParam="'.$dp_param.'" ';
				} else {
					$dp_param = '';
				}

				$autocompleteTarget = isset($filter_info['autocomplete']) ? ' autocompleteTarget="'.$filter_info['autocomplete'].'" ' : '';
				$autocompleteLimit = $autocompleteTarget && isset($filter_info['autocomplete_limit']) ? ' autocompleteLimit="'.$filter_info['autocomplete_limit'].'" ' : '';

				$filter_value = !is_array($filter_value) ? ' value="'.htmlspecialchars(str_replace('"','&quot;', $filter_value)).'"' : '';

				if (isset($filter_info['mask']) && !empty($filter_info['mask'])) {
					$mask = 'data-inputmask="\'mask\': \''.$filter_info['mask'].'\'" data-inputmask-clearIncomplete="true" data-mask';
				} else {
					$mask = '';
				}

				$allclass = trim(str_replace('  ', ' ', ($filter_main_class .' '. $add_class)));
				$all_attr = trim(str_replace('  ', ' ', ($filter_value . $id . $name . $tooltip . $placeholder . $reload . $autocompleteTarget . $autocompleteLimit . $dp_param)));
				$html .= '<input '.$filter_type.'_input="" type="text" '. $all_attr . ' class="'. $allclass . ' form-control" '.$mask.'/>';
			}

			if (isset($filter_info['multiselect'])) {
				$ms_param = base64_encode(json_encode($filter_info['multiselect'], true));
				$ms_param = ' multiselectParam="'.$ms_param.'" ';
			} else {
				$ms_param = '';
			}

			if ($filter_info['input_type'] == 'select') {
				$multiple = '';
				if (isset($filter_info['select_class']) && $filter_info['select_class'] === 'multiselect_single') {
					$multiple = 'multiselect';
				} elseif (isset($filter_info['select_class']) && $filter_info['select_class'] === 'multiselect') {
					$multiple = 'multiselect multiple';
				}

				$allclass = trim(str_replace('  ', ' ', ($filter_main_class)));
				$all_attr = trim(str_replace('  ', ' ', ($id . $name . $tooltip . $reload . $multiple . $ms_param )));
				$html .= '<select '.$filter_type.'_input="" '. $all_attr . ' class="'. $allclass .' form-control">';

				$process_method = isset($filter_info['process_method']) ? $filter_info['process_method'] : '';

				$select_values = array();
				if ($process_method) {
					$select_values = $this->omproapi->{$process_method}($setting_user_group);
				}

				if (isset($filter_info['empty_value_status']) && $filter_info['empty_value_status']) {
					$empty_text = !empty($filter_info['empty_value_text']) ? $filter_info['empty_value_text'] : '';
					$html .= '<option value="">'. $empty_text .'</option>';
				}

				$tmp = explode(',', $filter_value);
				if (isset($tmp[0])) {
					$filter_value = array();
					foreach ($tmp as $val) {
						$filter_value[] = trim($val);
					}
				} else {
					$tmp = explode('-', $filter_value);
					if (isset($tmp[0])) {
						$filter_value = array();
						foreach ($tmp as $val) {
							$filter_value[] = trim($val);
						}
					}
				}

				foreach ($select_values as $value) {
					if (is_array($filter_value)) {
						$selected = in_array($value['id'], $filter_value) ?  'selected="selected"' : '';
					} else {
						$selected = $filter_value == $value['id'] ?  'selected="selected"' : '';
					}

					$html .= '<option value="'. $value['id'] .'" '.$selected.'>'. $value['text'] .'</option>';
				}

				$html .= '</select>';
			}

			if (!empty($btn_clear_tpl) && $filter_info['btn_clear_position'] == 'right') {
				$html .= $btn_clear_tpl;
			}

			if (!empty($btn_apply_tpl) && $filter_info['btn_apply_position'] == 'right') {
				$html .= $btn_apply_tpl;
			}

			$html .= '</span>';
		}

		return $html;
	}

	// Для настроек таблицы заказов

	// Список источников зависимости цвета фона и текста ячейки
	public function getColorSourceList() {
		$sources = array();
		$source_list = array('statuses','payments','shippings','date_shippings');
		foreach ($source_list as $source) {
			$sources[] = array('value' => $source, 'text' => $this->language->get('text_color_source_'.$source));
		}
		return $sources;
	}

	// Для общих настроек

	// Выбор метода для списка способов оплаты
	public function getPaymentsListMethods() {
		$methods = array();
		$methods[] = array('value' => 'getPaymentsInstalled', 'text' => 'Способы оплаты без вариантов (getPaymentsInstalled)');
		$methods[] = array('value' => 'getPayments', 'text' => 'Способы оплаты, найденные варианты (getPayments)');

		return $methods;
	}

	// Выбор метода для списка способов доставки
	public function getShippingsListMethods() {
		$methods = array();
		$methods[] = array('value' => 'getShippingsInstalled', 'text' => 'Способы доставки без вариантов (getShippingsInstalled)');
		$methods[] = array('value' => 'getShippings', 'text' => 'Способы доставки, найденные варианты (getShippings)');

		return $methods;
	}

	// Для настроек шаблона печати таблицы товаров
	// список полей для группировки товаров
	public function getProductGroupByList()  {
		return array(
			'op.order_product_id' 			=>  'op.order_product_id',
			'op.product_id' 					=>  'op.product_id (по умолчанию)',
		);
	}


	// PHPExcel
	public function getFontList() {
		return array('Arial', 'Times New Roman', 'Helvetica', 'Verdana', 'Comic Sans MS');
	}

	public function getCurrencyFormats() {
		$formats = array(
			'cur_ruble'	=> '#,##0.00_-[$ руб.-419]',
			'cur_ruble1'	=> '#,##0_-[$ руб.-419]',
			'cur_uah'	=> '#,##0.00_-[$ грн.-419]',
			'cur_uah1'	=> '#,##0_-[$ грн.-419]',
			'cur_usd'	=> '"$"#,##0.00_-',
			'cur_usd2'	=> '"$ "#,##0.00_-',
			'cur_eur'	=> '[$EUR ]#,##0.00_-',
			'cur_eur2'	=> '[$€ ]#,##0.00_-',
		);

		return $this->extendListWithCustom($formats, 'getCurrencyFormats');
	}

	public function getCellFormatList() {
		$format_list = array(
			'string' => 'Текст (строка)',
			'textarea' => 'Многострочный текст',
			'numeric' => 'Число',
			'image' => 'Изображение',
			'link' => 'Гиперссылка',
			'cur_ruble' => 'Финансы: 1 000,00 руб.',
			'cur_ruble1' => 'Финансы: 1 000 руб.',
			'cur_uah' => 'Финансы: 1 000,00 грн.',
			'cur_uah1' => 'Финансы: 1 000 грн.',
			'cur_usd' => 'Финансы: $1 000,00',
			'cur_usd2' => 'Финансы: $ 1 000,00',
			'cur_eur' => 'Финансы: EUR 1 000,00',
			'cur_eur2' => 'Финансы: € 1 000,00',
		);

		return $this->extendListWithCustom($format_list, 'getCellFormatList');
	}

	public function getDefaultFontName() {
		return 'Arial';
	}

	public function getDefaultFontSize() {
		return 11;
	}


	/* Для настроек СТРАНИЦ и ПОЛЕЙ */

	// список параметров добавляемых полей для настроек полей (Вывод данных)
	public function sqlAddFieldParamList() {
		$params = array();
		$param_list = array("tinyint(1) NOT NULL","tinyint(1) NOT NULL DEFAULT '0'", "tinyint(1) NOT NULL DEFAULT '1'", "tinyint(1) NOT NULL DEFAULT '2'", "tinyint(3) NOT NULL","tinyint(3) NOT NULL DEFAULT '0'","int(11) NOT NULL","int(11) NOT NULL DEFAULT '0'","text NOT NULL","mediumtext NOT NULL","longtext NOT NULL","date NOT NULL","time NOT NULL","datetime NOT NULL","float NOT NULL","float(9,6) NOT NULL","decimal(15,4) NOT NULL","decimal(15,4) NOT NULL DEFAULT '0.0000'","varchar(255) NOT NULL");
		foreach ($param_list as $param) {
			$params[] = array('value' => $param, 'text' => $param);
		}
		return $params;
	}

	// замена переменных в доп. запросах
	public function replaceVarInSql($sql) {
		if (preg_match('/{config_customer_group_id}/', $sql)) {
			$config_customer_group_id = (int)$this->config->get('config_customer_group_id');
			$sql = str_replace('{config_customer_group_id}', $config_customer_group_id, $sql);
		}

		if (preg_match('/{config_language_id}/', $sql)) {
			$config_language_id = (int)$this->config->get('config_language_id');
			$sql = str_replace('{config_language_id}', $config_language_id, $sql);
		}

		if (preg_match('/{DB_PREFIX}/', $sql)) {
			$sql = str_replace('{DB_PREFIX}', DB_PREFIX, $sql);
		}

		return html_entity_decode($sql, ENT_QUOTES, 'UTF-8');
	}


	// Список групп для настройки Таблицы переменных данных Заказа
	public function orderDataGroupList() {
		$data_group = array();
		$data_group[] = array('id' => 'user_info', 'icon' => 'fa fa-user-secret', 'text' => 'Пользователь (ваши данные)');
		$data_group[] = array('id' => 'store_info', 'icon' => 'fa fa-opencart', 'text' => 'Магазин');
		$data_group[] = array('id' => 'customer_info', 'icon' => 'fa fa-user', 'text' => 'Покупатель');
		$data_group[] = array('id' => 'payment_info', 'icon' => 'fa fa-credit-card', 'text' => 'Плательщик');
		$data_group[] = array('id' => 'shipping_info', 'icon' => 'fa fa-truck', 'text' => 'Получатель');
		$data_group[] = array('id' => 'order_info', 'icon' => 'fa fa-shopping-cart', 'text' => 'Заказ');
		$data_group[] = array('id' => 'affiliate_info', 'icon' => 'fa fa-user-plus', 'text' => 'Партнер');
		$data_group[] = array('id' => 'other_info', 'icon' => 'fa fa-info', 'text' => 'Разное');

		if ($this->simpleStatus()) {
			$data_group[] = array('id' => 'simple_custom_info', 'icon' => 'fa fa-dollar', 'text' => 'Кастомные поля модуля Simple');
		}

		return $data_group;
	}

	// Группы для настройки Таблицы переменных данныхТовара
	public function productDataGroupList() {
		$data_group = array();
		$data_group[] = array('id' => 'order_product_info', 'icon' => 'fa fa-cart-arrow-down', 'text' => 'Данные товара в заказе');
		$data_group[] = array('id' => 'stock_product_info', 'icon' => 'fa fa-opencart', 'text' => 'Данные товара в каталоге');
		$data_group[] = array('id' => 'manufacturer_info', 'icon' => 'fa fa-industry', 'text' => 'Производитель');
		$data_group[] = array('id' => 'other_info', 'icon' => 'fa fa-info', 'text' => ' Разное');
		return $data_group;
	}

	// Список полей заказа, исключенных для предобработки - не нужно добавлять поля из excludeEditOrderFieldList
	public function excludeProcessOrderFieldList() {
		$exclude_list = 'order_status, customer_group, reward, payment_country_id, payment_zone_id, shipping_country_id, shipping_zone_id, manager_user_id, courier_user_id, payment_status_id, shipping_status_id, shipping_date, shipping_time_start, shipping_time_end, shipping_cost_fact, order_discount, order_present, order_present_cost, order_custom_file, order_custom_image';

		return $exclude_list = explode(',', str_replace(' ', '', $exclude_list));
	}

	// Список полей заказа, исключенных для редактирования
	public function excludeEditOrderFieldList() {
		$exclude_list = 'order_id, store_id, store_name, store_url, email_not_edited, customer, customer_id, customer_id2, shipping_latitude, shipping_longitude, marketing_id, affiliate_id, tracking, ip, forwarded_ip, user_agent, accept_language, date_added, date_modified, order_commission_total, commission_total, affiliate, order_reward_total, customer_reward_total, subtotal, order_products_count, order_products_quantity, coupon_value, payment_zone_code, shipping_zone_code, invoiceno, shipping_profit, order_cost_total, order_purchase_total, order_calc_totals, order_cost_profit, order_purchase_profit, order_total_with_discount';

		return $exclude_list = explode(',', str_replace(' ', '', $exclude_list));
	}

	// Список полей заказа c "замороженными" настройками
	public function freezeSetOrderFieldList() {
		$freeze_list = 'manager_user_id, courier_user_id, payment_status_id, shipping_status_id, shipping_date, shipping_time_start, shipping_time_end, shipping_cost, shipping_cost_fact, order_discount, order_present, order_present_cost, order_custom_file, order_custom_image, shipping_datetime_start, shipping_datetime_end, telephone_numeric';

		return $freeze_list = explode(',', str_replace(' ', '', $freeze_list));
	}

	// поля товара по умолчанию
	public function defaultOrderProductFieldList() {
		return array('order_product_id', 'order_id', 'product_id', 'name', 'model', 'quantity', 'price', 'total', 'tax', 'reward');
	}

	// исключить повторяющиеся в других таблицах поля товара
	public function excludeOrderProductFieldList() {
		return array('product_id', 'model');
	}

	// исключить поля таблицы product
	public function excludeProductFieldList() {
		return array('noindex');
	}

	// поля производителя по умолчанию
	public function defaultManufacturerFieldList() {
		return array('manufacturer_id','name','image','sort_order');
	}

	// исключить повторяющиеся в других таблицах поля производителя
	public function excludeManufacturerFieldList() {
		return array('manufacturer_id','sort_order','noindex');
	}

	public function orderDataFormatTypeList() {
		$formats = array();
		$format_type_list = array('currency','currency_config','float','num_format_en','num_format_en2','num_format_fr','num_format_fr2','date','method');
		foreach ($format_type_list as $type) {
			$formats[] = array('type' => $type, 'name' => $this->language->get('text_format_type_'.$type));
		}
		return $formats;
	}

	// Список полей данныхТовара, исключенных для редактирования, таблицы:  "product", "order_product", "manufacturer"
	// Название полей должно быть как в настройках на стр. Осн. поля товара
	public function excludeEditProductFieldList() {
		$exclude_list = 'product_id, model, image, viewed, date_added, date_modified, manufacturer_id, m_name, m_image, order_product_id, order_id, op_name, cost_total, purchase_total, cost_total_profit, purchase_total_profit, tax_total, weight_total';

		return $exclude_list = explode(',', str_replace(' ', '', $exclude_list));
	}

	public function excludeProcessProductFieldList() {
		$exclude_list = 'stock_status_id, shipping, tax_class_id, date_available, weight_class_id, length_class_id, subtract, sort_order, status, cost, purchase, op_quantity, op_price, op_total, op_tax, op_reward';

		return $exclude_list = explode(',', str_replace(' ', '', $exclude_list));
	}

	// Список полей данныхТовара c "замороженными" настройками
	public function freezeSetProductFieldList() {
		$freeze_list = 'notes, cost, purchase, cost_total, purchase_total, cost_total_profit, purchase_total_profit, tax_total, weight_total, suppler_id, suppler_name';
		return $freeze_list = explode(',', str_replace(' ', '', $freeze_list));
	}

	public function excludeOpManSumFieldList() {
		$exclude_list = 'order_product_id, order_id, op_name, op_model, op_location, m_name, m_image, notes';

		return $exclude_list = explode(',', str_replace(' ', '', $exclude_list));
	}

	// Способы форматирования данныхТовара
	public function productDataFormatTypeList() {
		$formats = array();
		$format_type_list = array('currency','currency_config','weight','length','volume','float','num_format_en','num_format_en2','num_format_fr','num_format_fr2','date','method');
		foreach ($format_type_list as $type) {
			$formats[] = array('type' => $type, 'name' => $this->language->get('text_format_type_'.$type));
		}
		return $formats;
	}


	/* xEdit Links Редактирование данных таблицы заказов */

	// Редактирование Заказа, Типы полей
	public function xEditInputTypeList() {
		$types = array();
		$types[] = array('id' => 'text', 'name' => 'Текст (text)');
		$types[] = array('id' => 'textarea', 'name' => 'Текстовая областя (textarea)');
		$types[] = array('id' => 'time', 'name' => 'Время (time)');
		$types[] = array('id' => 'datetime', 'name' => 'Дата-время, плагин Air-Datepicker');
		$types[] = array('id' => 'email', 'name' => 'E-mail (email)');
		$types[] = array('id' => 'inputmask', 'name' => 'Текст по маске, плагин inputmask');
		$types[] = array('id' => 'selector_api', 'name' => 'Селектор, значения: API метод');
		$types[] = array('id' => 'selector_option', 'name' => 'Селектор, значения: Опции');
		$types[] = array('id' => 'checklist_api', 'name' => 'Чекбоксы, значения: API метод');
		$types[] = array('id' => 'checklist_option', 'name' => 'Чекбоксы, значения: Опции');
		$types[] = array('id' => 'file', 'name' => 'Файл (file)');
		$types[] = array('id' => 'simple_custom', 'name' => 'Автоопределение типа поля Simple');
		$types[] = array('id' => 'custom_api', 'name' => 'Кастомные API методы редактирования');

		return $types;
	}

	// API методы значений селекторов (для ID типа поля = selector_api)
	public function valuesApiMethodList() {
		$methods = array();
		$methods[] = array('key' => 'getStores', 'name' => 'Магазины');
		$methods[] = array('key' => 'getOrderStatuses', 'name' => 'Статусы заказа');
		$methods[] = array('key' => 'getCusromerGroups', 'name' => 'Группы покупателей');
		$methods[] = array('key' => 'getPaymentsInstalled', 'name' => 'Способы оплаты (без вариантов)');
		$methods[] = array('key' => 'getPayments', 'name' => 'Способы оплаты (найденные варианты)');
		$methods[] = array('key' => 'getShippingsInstalled', 'name' => 'Способы доставки (без вариантов)');
		$methods[] = array('key' => 'getShippings', 'name' => 'Способы доставки (найденные варианты)');
		$methods[] = array('key' => 'getCountries', 'name' => 'Все страны');
		$methods[] = array('key' => 'getUserGroupPaymentCountries', 'name' => 'Страны Плательщика геозон пользователя');
		$methods[] = array('key' => 'getUserGroupShippingCountries', 'name' => 'Страны Получателя геозон пользователя');

		$methods[] = array('key' => 'getActiveUsers', 'name' => 'Все пользователи');
		$methods[] = array('key' => 'getManagerUsers', 'name' => 'Менеджеры');
		$methods[] = array('key' => 'getCourierUsers', 'name' => 'Курьеры');
		$methods[] = array('key' => 'getYesNo', 'name' => 'Да / Нет');
//		$methods[] = array('key' => 'getDownloadsAll', 'name' => 'Загрузки');

		return $methods;
	}


	///// Получение форматированных данных и / или ссылки для открытия формы редактирования X-editable
	// http://vitalets.github.io/x-editable/docs.html#inputs - документация x-Editable

	// форматирование
	public function getFormat($data) {
		$value = $data['val'];
		if ($value !== '' && $data['format_data']) {
			if (isset($data['format_data']['type'])) {
				if ($data['format_data']['type'] == 'date') {
					if (isset($data['format_data']['date_format']) && $data['format_data']['date_format']) {
						$value = $data['val'] > 0 || $data['val'] == '00:00:00' ? date($data['format_data']['date_format'], strtotime($data['val'])) : '';
					}
				}
				elseif ($data['format_data']['type'] == 'currency' && is_numeric($data['val'])) {
					if (isset($data['order_info']) && isset($data['order_info']['currency_code'])) {
						$value = $this->currency->format($data['val'], $data['order_info']['currency_code'], $data['order_info']['currency_value']);
					} else {
						$value = $this->currency->format($data['val'], $this->config->get('config_currency'));
					}
					$value = $this->clearTags($value);
				}
				elseif ($data['format_data']['type'] == 'currency_config' && is_numeric($data['val'])) {
					$value = $this->currency->format($data['val'], $this->config->get('config_currency'));
					$value = $this->clearTags($value);
				}
				elseif ($data['format_data']['type'] == 'float' && is_numeric($data['val'])) {
					$value = (float)$data['val'];
				}
				elseif ($data['format_data']['type'] == 'num_format_en' && is_numeric($data['val'])) {
					$value = number_format($data['val']);
				}
				elseif ($data['format_data']['type'] == 'num_format_en2' && is_numeric($data['val'])) {
					$value = number_format($data['val'], 2, '.', '');
				}
				elseif ($data['format_data']['type'] == 'num_format_fr' && is_numeric($data['val'])) {
					$value = number_format($data['val'], 2, ',', ' ');
				}
				elseif ($data['format_data']['type'] == 'num_format_fr2' && is_numeric($data['val'])) {
					$value = number_format($data['val'], 2, '.', ' ');
				}

				elseif ($data['format_data']['type'] == 'weight' && isset($data['product_info']) && is_numeric($data['val'])) {
					$value = $this->weight->format($data['val'], $data['product_info']['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
				}
				elseif ($data['format_data']['type'] == 'length' && isset($data['product_info']) && is_numeric($data['val'])) {
					$value = $this->length->format($data['val'], $data['product_info']['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point'));
				}
				elseif ($data['format_data']['type'] == 'volume' && isset($data['product_info']) && is_numeric($data['val'])) {
					$value = $this->length->format($data['val'], $data['product_info']['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) . '<sup>3</sup>';
				}
				elseif ($data['format_data']['type'] == 'method' && isset($data['format_data']['process_method'])) {
					$method = $data['format_data']['process_method'];
					if (in_array($method, $this->ompro->custom_methods)) {
						$value = $this->omproapicustom->{$method}($data['val'], $data['order_info']);
					} else {
						$value = $this->{$method}($data['val'], $data['order_info']);
					}
				}
			}
		}
		return $value;
	}

	// xEdit редактирование
	public function xEditPrepareParam($data) {
		$param = array();

		$dbTable = $data['fieldset']['dbTable'];

		if ($dbTable == 'order') {
			$param['object'] = 'order_info';
			$param['pkName'] = 'order_id';
			$param['pk'] = $data['order_info']['order_id'];
		} elseif ($dbTable == 'product') {
			$param['object'] = 'product_info';
			$param['pkName'] = 'product_id';
			$param['pk'] = $data['product_info']['product_id'];
		} elseif ($dbTable == 'order_product') {
			$param['object'] = 'product_info';
			$param['pkName'] = 'order_product_id';
			$param['pk'] = $data['product_info']['order_product_id'];
		}

		return $param;
	}

	public function xEditLinkAutoType($data) {
		if (!$data['to_edit']) {
			return $this->getFormat($data);
		}

		$param = $this->xEditPrepareParam($data);
		$val2text = $this->getFormat($data);

		if ($param) {
			$user_group_id = $data['setting_user_group']['user_group_id'];
			if (isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;

				$dbTable = $data['fieldset']['dbTable'];
				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}

				$order_str = '';
				if ($order_id) { $order_str = 'order_id: \''.$order_id.'\', '; }
				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

				return '<a href="javascript:" xedit="'.$data['fieldset']['eform'].'" data-type="'.$data['fieldset']['eform'].'" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" data-params="{dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$param['pkName'].'\', '.$order_str.' log: \''.$log.'\''.$action_str.'}" data-pk="'.$param['pk'].'" data-placement="top" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'" >'.$val2text.'</a>';

			}
		}
		return $val2text;
	}

	public function xEditLinkFile($data) {
		$val = $data['val'];
		$code = $val2text = '';

		$upload_info = array();
		if ($val) {
			$val2text = utf8_substr($val, 0, utf8_strrpos($val, '.'));
			$upload_info = $this->ompro->getUploadByFilename($val);
			if ($upload_info) {
				$code = $upload_info['code'];
				$fname = $upload_info['name'];
				$href = $this->ompro->catalog.'index.php?route=api/ompro/download&code=' .$code;
				if (!$data['sms']) {
					$val2text = '<a href="'.$href.'" >'.$fname.'</a>';
				} else {
					$val2text = $fname;
				}
			}
		}

		if (!$data['to_edit']) {
			return $val2text;
		} else {
			$param = $this->xEditPrepareParam($data);
			$user_group_id = $data['setting_user_group']['user_group_id'];

			if ($param && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				if (!$val) {
					$val2text = $this->language->get('text_no_file');
				}

				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;

				$dbTable = $data['fieldset']['dbTable'];

				$suf = $data['key'].'_'. $param['pk'];

				$dbTable_str = 'data-dbtable="'.$data['fieldset']['dbTable'].'"';
				$name = ' data-name="'.$data['key'].'"';
				$pk = ' data-pk="'.$param['pk'].'"';
				$pkName = ' data-pkName="'.$param['pkName'].'"';
				$pageReload = ' pageReload="'.$pageReload.'"';

				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}

				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$order_log_str = ' data-order_id="'.$order_id.'" data-log="'.$log.'"';

				$action = isset($data['fieldset']['action']) ? $data['fieldset']['action'] : '';
				$action_str = ' data-action: \''.$action.'\'';

				return ''.
					'<div style="min-width: 200px; width: 100%;">'.
					  '<div class="input-group input-group-sm">'.
						'<span class="input-group-addon form-control text-left" id="name_' .$suf. '" >'.$val2text.'</span>'.
						'<span class="input-group-btn"><button type="button" id="fileupload_' .$suf. '" data-suf="' .$suf. '" class="btn-fileupload btn btn-default" title="'.$this->language->get('text_upload').'" ><i class="fa fa-upload"></i></button></span> '.
						'<span class="input-group-btn"><button type="button" id="filesave_' .$suf. '" ' . $dbTable_str . $name . $pk . $pkName . $pageReload .  $order_log_str  . $action_str . ' data-suf="' .$suf. '" class="filesave btn btn-primary" title="'.$this->language->get('text_save').'" disabled ><i class="fa fa-save"></i></button></span> '.
						'<input type="hidden" id="filename_' .$suf. '" value="' .$val. '" />'.
						'<input type="hidden" id="filecode_' .$suf. '"  value="' .$code. '" />'.
					  '</div>'.
					'</div>';
			}
			return $val2text;
		}
	}

	public function xEditLinkMask($data) {
		if (!$data['to_edit']) {
			return $this->getFormat($data);
		} else {
			$param = $this->xEditPrepareParam($data);
			$user_group_id = $data['setting_user_group']['user_group_id'];

			$val2text = $this->getFormat($data);

			if ($param && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;
				$mask = $data['fieldset']['eparam'];

				$dbTable = $data['fieldset']['dbTable'];
				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}
				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$order_log_str = ', order_id: \''.$order_id.'\', log: \''.$log.'\'';
				$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

				return '<a href="javascript:" xedit="mask" data-type="text" data-mask="'.$mask.'" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" data-params="{dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$param['pkName'].'\''.$order_log_str. $action_str .'}" data-pk="'.$param['pk'].'" data-placement="right" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'" >'.$val2text.'</a>';

			}
			return $val2text;
		}
	}

	public function xEditLinkDateTime($data) {
		if (!$data['to_edit']) {
			return $this->getFormat($data);
		}

		if ($data['fieldset']['dbTable'] == 'order' && ($data['fieldset']['key'] == 'shipping_datetime_start' || $data['fieldset']['key'] == 'shipping_datetime_end') && isset($data['order_info']['shipping_datetime_start']) && isset($data['order_info']['shipping_datetime_end']) && !strtotime($data['order_info']['shipping_datetime_start']) && !strtotime($data['order_info']['shipping_datetime_end'])) {
			$data['to_edit'] = false;
		}

		if (!$data['to_edit']) {
			return $this->getFormat($data);
		} else {
			$param = $this->xEditPrepareParam($data);
			$user_group_id = $data['setting_user_group']['user_group_id'];

			$val2text = $this->getFormat($data);

			if ($param && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;
				$dateFormat = 'data-date-format="dd.mm.yyyy"';
				$timeFormat = '';

				$par = explode('||',$data['fieldset']['eparam']);
				if (isset($par[0])) { $dateFormat = 'data-date-format="'.trim($par[0]).'"'; }
				if (isset($par[1])) { $timeFormat = ' data-time-format="'.trim($par[1]).'"'; }

				$dbTable = $data['fieldset']['dbTable'];
				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}
				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$order_log_str = ', order_id: \''.$order_id.'\', log: \''.$log.'\'';
				$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

				return '<a href="javascript:" xedit="airdatepicker" data-type="text" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" '.$dateFormat . $timeFormat.' data-params="{editType: \'DateTime\', dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$param['pkName'].'\''.$order_log_str . $action_str .'}" data-pk="'.$param['pk'].'" data-placement="bottom" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'">'.$val2text.'</a>';
			}

			return $val2text;
		}
	}

	public function xEditLinkSelector($data) {
		$param = $this->xEditPrepareParam($data);
		$val2text = $data['val'];
		$implode = array(); $val_set = false;

		if ($param) {
			if ($data['fieldset']['eform'] == 'selector_api') {
				$values = $this->{$data['fieldset']['eparam']}($data['setting_user_group']);

				foreach ($values as $option) {
					$format = $this->getFormat(array('val' => $option['text'], ''.$param['object'].'' => $data[$param['object']], 'format_data' => $data['format_data']));
					if ($data['val'] !== '' && $data['val'] == $option['id']) { $val2text = $format; $val_set = true; }
					if ($data['to_edit']) { $implode[] = '{ value: \'' . $option['id'] . '\', text: \'' . $format . '\' }'; }
				}
			}
			elseif ($data['fieldset']['eform'] == 'selector_option') {
				if ($param['object'] == 'order_info') {
					$lang_id = $data['order_info']['language_id'];
				} else {
					$lang_id = $this->config->get('config_language_id');
				}

				$template_id = $data['fieldset']['eparam'];
				if (!is_numeric($template_id)) {
					$template_id = $this->ompro->getTemplateIdByCode('option', $template_id);
				}

				$values = $this->ompro->getOptionValues($template_id, $lang_id);

				foreach ($values as $option) {
					$format = $this->getFormat(array('val' => $option['name'], ''.$param['object'].'' => $data[$param['object']], 'format_data' => $data['format_data']));
					if ($data['val'] !== '' && $data['val'] == $option['option_value_id']) { $val2text = $format; $val_set = true; }
					if ($data['to_edit']) { $implode[] = '{ value: \'' . $option['option_value_id'] . '\', text: \'' . strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')) . '\' }'; }
				}
			}
		}

		if (!$val_set || $data['val'] == '') { $data['val'] = '*'; $val2text = ''; }

		if (!$data['to_edit']) {
			return $val2text;
		} else {
			$user_group_id = $data['setting_user_group']['user_group_id'];

			if ($param && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				if ($implode) { $source = ' data-source="[' . implode(',',$implode) . ']"'; } else { $source = ''; }
				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;
				$dbTable = $data['fieldset']['dbTable'];
				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}
				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$order_log_str = ', order_id: \''.$order_id.'\', log: \''.$log.'\'';
				$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

				return '<a href="javascript:" xedit="select" data-type="select" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" '.$source.' data-params="{dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$param['pkName'].'\''.$order_log_str. $action_str .'}" data-pk="'.$param['pk'].'" data-placement="top" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'">'.$val2text.'</a>';
			}
			return $val2text;
		}
	}

	public function xEditLinkChecklist($data) {
		$param = $this->xEditPrepareParam($data);

		$checked = array();
		if (trim($data['val']) !== '' && preg_match('/,/', $data['val'])) {
			$arr = explode(',',$data['val']);
			foreach ($arr as $v) { if (trim($v) !=='') { $checked[] = $v; } }
		} elseif (trim($data['val']) !== '') { $checked[] = $data['val']; }

		$implode = $checktexts = array();

		if ($param) {
			$values = array();
			if ($data['fieldset']['eform'] == 'checklist_api') {
				$values = $this->{$data['fieldset']['eparam']}($data['setting_user_group']);
				foreach ($values as $option) {
					$format = $this->getFormat(array('val' => $option['text'], ''.$param['object'].'' => $data[$param['object']], 'format_data' => $data['format_data']));

					if (in_array($option['id'], $checked)) { $checktexts[] = $format; }
					if ($data['to_edit']) { $implode[] = '{ value: \'' . $option['id'] . '\', text: \'' . $format . '\' }'; }
				}
			}
			elseif ($data['fieldset']['eform'] == 'checklist_option') {
				if ($param['object'] == 'order_info') {
					$lang_id = $data['order_info']['language_id'];
				} else {
					$lang_id = $this->config->get('config_language_id');
				}

				$template_id = $data['fieldset']['eparam'];
				if (!is_numeric($template_id)) {
					$template_id = $this->ompro->getTemplateIdByCode('option', $template_id);
				}

				$values = $this->ompro->getOptionValues($template_id, $lang_id);

				foreach ($values as $option) {
					$format = $this->getFormat(array('val' => $option['name'], ''.$param['object'].'' => $data[$param['object']], 'format_data' => $data['format_data']));
					if (in_array($option['option_value_id'], $checked)) { $checktexts[] = $format; }
					if ($data['to_edit']) { $implode[] = '{ value: \'' . $option['option_value_id'] . '\', text: \'' . strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')) . '\' }'; }
				}
			}
		}

		if (!empty($checktexts)) { $val2text = implode(', ',$checktexts); } else { $val2text = $data['val'] ; }

		if (!$data['to_edit']) {
			return $val2text;
		} else {
			$user_group_id = $data['setting_user_group']['user_group_id'];

			if ($param && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {
				if ($implode) { $source = ' data-source="[' . implode(',',$implode) . ']"'; } else { $source = ''; }
				$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;

				$dbTable = $data['fieldset']['dbTable'];
				$order_id = 0;
				if ($dbTable == 'order' || $dbTable == 'order_product' || $dbTable == 'order_simple_fields' && isset($data[$param['object']]['order_id'])) {
					$order_id = $data[$param['object']]['order_id'];
				}
				$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
				$order_log_str = ', order_id: \''.$order_id.'\', log: \''.$log.'\'';
				$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

				return '<a href="javascript:" xedit="checklist" data-type="checklist" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" '.$source.' data-params="{dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$param['pkName'].'\''.$order_log_str. $action_str .'}" data-pk="'.$param['pk'].'" data-placement="top" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'">'.$val2text.'</a>';
			}
			return $val2text;
		}
	}


	public function editSchedulerShipping($order_id = 0, $name = '', $value = '') {
		if ($order_id) {
			$query = $this->db->query("SELECT shipping_method, shipping_code, shipping_datetime_start, shipping_datetime_end FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id  ."' LIMIT 1");

			if ($query->num_rows) {
				$shippings = array();

				$scheduler_ships = $this->config->get('scheduler_ships');
				if($scheduler_ships) {
					foreach ($scheduler_ships as $ship_id => $ship) {
						$standart_module = count($ship) > 1 ? false : true;
						$title = html_entity_decode($ship[0]['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
						foreach ($ship as $key => $module) {
							if ($key == 0 && $standart_module) {
								$shippings['scheduler.stand-'.$ship_id] = $title;
							}
							if ($key > 0) {
								$shippings['scheduler.sched-'.$ship_id] = $title;
							}
						}
					}
				}

				if (isset($shippings[$query->row['shipping_code']])) {
					$order_title = $shippings[$query->row['shipping_code']];
					$order_title_date = '('.date('d.m.Y', strtotime($query->row['shipping_datetime_start'])).')';

					if ($name == 'shipping_datetime_start') {
						$start = $value;
						$end = $query->row['shipping_datetime_end'];
						$date_shipping = date('d.m.Y', strtotime($start));
					} else {
						$start = $query->row['shipping_datetime_start'];
						$end = $value;
						$date_shipping = date('d.m.Y', strtotime($end));
					}

					$shipping_datetime_start = date('Y-m-d', strtotime($date_shipping)) . ' ' . date('H:i', strtotime($start));
					$shipping_datetime_end = date('Y-m-d', strtotime($date_shipping)) . ' ' . date('H:i', strtotime($end));

					$shipping_method = $order_title . ' (' . $date_shipping . ') ' . date('H:i', strtotime($shipping_datetime_start)) . ' - ' . date('H:i', strtotime($shipping_datetime_end));

					$this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_method = '" . $shipping_method . "', shipping_datetime_start = '" . $shipping_datetime_start . "', shipping_datetime_end = '" . $shipping_datetime_end . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

					if ($this->db->countAffected() > 0) {
						return true;
					}
				}
			}
		}
		return false;
	}


	/* Simple */

	// Ред. кастомных полей модуля Simple
	// types: 	text, email, tel, password, textarea, date, time, select, select2, checkbox, radio, file

	public function simpleStatus() {
		if ($this->config->get('simple_settings')) {
			return true;
		}
		return false;
	}

	public function xEditLinkSimpleCustom($data) {
		$to_edit = $data['to_edit'];
		$sms = $data['sms'];
		$val = $data['val'];
		$key = $data['key'];
		$order_info = $data['order_info'];
		$fieldset = $data['fieldset'];
		$simple_fields = $data['simple_fields'];
		$langCode = $data['langCode'];
		$format_data = $data['format_data'];

		$name = $val2text = '';

		if (preg_match('/osf_/', $key)) {
			$name = str_replace('osf_', '', $key);
		}

		if (isset($simple_fields[$name])) {
			$simple_field = $simple_fields[$name];
			if ($simple_field['type'] == 'file') {
				$val2text = utf8_substr($val, 0, utf8_strrpos($val, '.'));
				$upload_info = $this->ompro->getUploadByFilename($val);
				if ($upload_info) {
					$code = $upload_info['code'];
					$fname = $upload_info['name'];
					$href = $this->ompro->catalog.'index.php?route=api/ompro/download&code=' .$code;
					if (!$sms) {
						$val2text = '<a href="'.$href.'" >'.$fname.'</a>';
					} else {
						$val2text = $fname;
					}
				}
			}
			else {
				$filter = ''; $all_simple_fields = array();
				if (isset($simple_field['values']['filter']) && $simple_field['values']['filter']) {
					if (isset($order_info['payment_'.$simple_field['values']['filter']])) {
						$filter = $order_info['payment_'.$simple_field['values']['filter']];
					} elseif (isset($order_info['shipping_'.$simple_field['values']['filter']])) {
						$filter = $order_info['shipping_'.$simple_field['values']['filter']];
					} else {
						if (isset($simple_fields[$simple_field['values']['filter']])) {
							$all_simple_fields = $simple_fields;
						} else {
							$simple_settings = @json_decode($this->config->get('simple_settings'), true);
							$all_simple_fields = $simple_settings['fields'];
						}
					}

					if ($all_simple_fields) {
						foreach ($all_simple_fields as $set_field) {
							if ($set_field['id'] == $simple_field['values']['filter'] && isset($set_field['default']['source'])) {
								if ($set_field['default']['source'] == 'saved') {
									$filter = $set_field['default']['saved'];
								} elseif ($set_field['default']['source'] == 'model') {
									$method = $set_field['default']['method'];
									$filter = $this->simpleConnect($method);
								} 	break;
							}
						}
					}
				}

				$values = array();
				$type2values = array('checkbox', 'select', 'select2', 'radio');
				if (in_array($simple_field['type'], $type2values) && !empty($simple_field['values']['source'])) {
					if ($simple_field['values']['source'] == 'saved' && !empty($simple_field['values']['saved'])) {
						$valuesText = !empty($simple_field['values']['saved'][$langCode]) ? $simple_field['values']['saved'][$langCode] : array();
						foreach ($valuesText as $item) {
							$values[$item['id']] = $item['text'];
						}
					}
					elseif ($simple_field['values']['source'] == 'model' && !empty($simple_field['values']['method'])) {
						$method = $simple_field['values']['method'];
						$values = $this->simpleConnect($method, $filter);
					}
				}

				$implode = array(); // for edit

				if (!empty($values) && is_array($values)) {
					$val_set = false; $checked = array();
					if ($simple_field['type'] == 'checkbox') {
						if ($val !== '' && preg_match('/,/', $val)) {
							$arr = explode(',',$val);
							foreach ($arr as $v) { if (trim($v) !=='') { $checked[] = $v; } }
						} elseif ($val !== '') { $checked[] = $val; }
					}

					$checktexts = array();
					foreach ($values as $value => $text) {
						$format = $this->getFormat(array('val' => $text, 'order_info' => $data['order_info'], 'format_data' => $data['format_data']));

						if ($simple_field['type'] == 'checkbox') {
							if (in_array($value, $checked)) { $checktexts[] = $format; $val_set = true; }
							if ($to_edit && !empty($value)) { $implode[] = '{ value: \'' . $value . '\', text: \'' . $format . '\' }'; }// for edit
						} else {
							if ($value !== '' && $value == $val) {
								$val2text = $format; $val_set = true;
							}
							if ($to_edit) { $implode[] = '{ value: \'' . $value . '\', text: \'' . $format . '\' }'; }// for edit
						}
					}

					if ($checktexts) { $val2text = implode(', ',$checktexts); }
					if (!$val_set || $val == '') { $val = '*'; $val2text = ''; }

				} else {
					$val2text = $this->getFormat(array('val' => $val, 'order_info' => $data['order_info'], 'format_data' => $data['format_data']));
				}
			}

			if (!$to_edit) {
				return $val2text;
			} else {
				// for edit
				$title = $simple_field['label'][$langCode];
				$dbTable = 'order_simple_fields';
				$pk = $order_info['order_id'];
				$pageReload = isset($fieldset['reload_onsave']) && $fieldset['reload_onsave'] ? 1 : 0;
				$user_group_id = $data['setting_user_group']['user_group_id'];

				if (isset($fieldset['edit_access']) && in_array($user_group_id, $fieldset['edit_access'])) {
					$xedit = $type = $dateFormat = $mask = '';
					$type2text = array('tel');
					$type2select = array('select', 'select2', 'radio');
					$placement = ' data-placement="top"';

					if (in_array($simple_field['type'], $type2text)) {
						$type = 'text'; $xedit = 'xedit="text"';
					} elseif (in_array($simple_field['type'], $type2select)) {
						$type = 'select';
						$xedit = 'xedit="select"';
					} elseif ($simple_field['type'] == 'date') {
						$type = 'text'; $xedit = 'xedit="airdatepicker"';
						$dateFormat = ' data-date-format="dd.mm.yyyy"';
						$placement = ' data-placement="bottom"';
					} elseif ($simple_field['type'] == 'checkbox') {
						$type = 'checklist'; $xedit = 'xedit="checklist"';
					} elseif ($simple_field['type'] !== 'file') { // AUTO: text, textarea, time, password, email
						$type = $simple_field['type']; $xedit = 'xedit="'.$simple_field['type'].'"';
					}

					if ($type == 'text' && isset($simple_field['mask']['source'])) {
						if ($simple_field['mask']['source'] == 'saved' && $simple_field['mask']['saved']) {
							$xedit = 'xedit="mask"';
							$mask = ' data-mask="'.$simple_field['mask']['saved'].'"';
						} elseif ($simple_field['mask']['source'] == 'model' && $simple_field['mask']['method']) {
							$xedit = 'xedit="mask"';
							$method = $simple_field['mask']['method'];
							$mask = ' data-mask="'.$this->simpleConnect($method, $filter).'"';
						}
					}

					if ($dbTable && $xedit && $type) {
						if ($implode) { $source = ' data-source="[' . implode(',',$implode) . ']"'; } else { $source = ''; }
						$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;

						return '<a href="javascript:" '. $xedit . $mask . $source . $dateFormat . $placement .' data-type="'.$type.'" data-mode="popup" data-emptytext="..." data-name="'.$name.'" data-value="'.$val.'" data-params="{dbTable: \''.$dbTable.'\', pkName: \'order_id\', order_id: \''.$pk.'\', log: \''.$log.'\'}" data-pk="'.$pk.'" data-title="'.$title.'" pageReload="'.$pageReload.'" data-order_id="'.$pk.'">'.$val2text.'</a>';
					}
				}
				return $val2text;
			}
		}
	}

    private function simpleConnect($method = '', $filter = '') {
		if (!$method) { exit; }

		$result = '';

		if (method_exists($this->omproapi, $method)) {
			$result = $this->{$method}($filter);
		} else {
			$curl = curl_init(HTTP_CATALOG.'index.php?route=api/ompro/getSimpleApiCustom&method='.$method.'&filter='.$filter);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$result = json_decode(curl_exec($curl), true);
		}

		$ret = array();
		if (!empty($result)) {
			if (is_array($result)) {
				foreach ($result as $info) {
					$ret[$info['id']] = $info['text'];
				}
			} else {
				return $result;
			}
		}
        return $ret;
    }

	public function getSimpleCustomFieldsSetting() {
		$result = array();

		if ($this->simpleStatus()) {
			$langCode = trim(str_replace('-', '_', strtolower($this->getLanguageCode())), '.');
            $simple_settings = @json_decode($this->config->get('simple_settings'), true);
			if (!empty($simple_settings['fields'])) {
				foreach ($simple_settings['fields'] as $field) {
					if ($field['custom']) {
						if ($field['object'] == 'address') {
							$temp = $field;
							$field['label'][$langCode] = $temp['label'][$langCode] . ' (Оплата)';
							$result['payment_'.$field['id']] = $field;
							$field['label'][$langCode] = $temp['label'][$langCode] . ' (Доставка)';
							$result['shipping_'.$field['id']] = $field;
						} else {
							$result[$field['id']] = $field;
						}
					}
				}
			}
		}

		return $result;
	}

	public function getOrderSimpleCustomFields($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_simple_fields` WHERE order_id = '" . (int)$order_id . "' LIMIT 1");
		if ($query->row) {
			return $query->row; // metadata ???
		}
		return array();
	}

	public function getOrderSimpleFieldValue($order_id, $field) {
		$query = $this->db->query("SELECT $field FROM `" . DB_PREFIX . "order_simple_fields` WHERE order_id = '" . (int)$order_id . "'");
		if (isset($query->row[$field])) {
			return $query->row[$field];
		}
		return null;
	}

	// список полей simple для отображения в таблице переменных
	public function simpleFieldsDataList() {
		$simple_fields = array();
		$langCode = trim(str_replace('-', '_', strtolower($this->getLanguageCode())), '.');

		$fields = $this->getSimpleCustomFieldsSetting();

		if ($fields) {
			foreach ($fields as $key => $field) {
				if (isset($field['label'][$langCode]) && !empty($field['label'][$langCode])) {
					$simple_fields[] = array('key' => 'osf_'.$key, 'code' => $key, 'name' => $field['label'][$langCode]);
				} else {
					$simple_fields[] = array('key' => 'osf_'.$key, 'code' => $key, 'name' => $key);
				}
			}
		}

		return $simple_fields;
	}


	/*** SimpleApiCustom  Methods  ***/
	// * Дублировать ниже все новые методы из файла catalog\model\tool\simpleapicustom.php для ускорения получения данных ???

   public function checkCaptcha($value, $filter) {
        if (isset($this->session->data['captcha']) && $this->session->data['captcha'] != $value) {
            return false;
        }

        return true;
    }

    public function getYesNo($filter = '') {
        return array(
            array(
                'id'   => '1',
                'text' => $this->language->get('text_yes')
            ),
            array(
                'id'   => '0',
                'text' => $this->language->get('text_no')
            )
        );
    }

    public function maskPhoneRu() {
        return '+7 (99) 999-99-99';
    }

    public function getCountries($filter = '') {
        $values = array(
            array(
                'id'   => '',
                'text' => $this->language->get('text_select')
            )
        );

		$country_data = $this->cache->get('country.catalog');

		if (!$country_data)  {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' ORDER BY name ASC");
			$country_data = $query->rows;
			$this->cache->set('country.catalog', $country_data);
		}

		$results = $country_data;

        foreach ($results as $result) {
            $values[] =  array(
                'id'   => $result['country_id'],
                'text' => $result['name']
            );
        }

        if (!$results) {
            $values[] = array(
                'id'   => 0,
                'text' => $this->language->get('text_none')
            );
        }

        return $values;
    }

    public function getZones($countryId) {
        $values = array(
            array(
                'id'   => '',
                'text' => $this->language->get('text_select')
            )
        );

        $this->load->model('localisation/zone');

        $results = $this->model_localisation_zone->getZonesByCountryId($countryId);

        foreach ($results as $result) {
            $values[] = array(
                'id'   => $result['zone_id'],
                'text' => $result['name']
            );
        }

        if (!$results) {
            $values[] = array(
                'id'   => 0,
                'text' => $this->language->get('text_none')
            );
        }

        return $values;
    }
	// <- SimpleApiCustom  Methods


	// Кастомные API методы редактирования: xEditLinkCustom...(ссылка для редактирования) & xEditCustom... (метод ред.) ->

	// -> ShippingCost
	public function xEditLinkCustomOrderShippingCost($data) {
		$pkName = 'order_id';
		$pk = $order_id = $data['order_info']['order_id'];

		$user_group_id = $data['setting_user_group']['user_group_id'];
		$val2text = $this->getFormat($data);

		if ($data['to_edit'] && isset($data['fieldset']['edit_access']) && in_array($user_group_id, $data['fieldset']['edit_access'])) {

			$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;
			$dbTable = $data['fieldset']['dbTable'];

			$order_str = '';
			if ($order_id) { $order_str = 'order_id: \''.$order_id.'\', '; }
			$log = isset($data['fieldset']['log']) && $data['fieldset']['log'] ? 1 : 0;
			$action_str = isset($data['fieldset']['action']) ? ', action: \''.$data['fieldset']['action'].'\'' : '';

			return '<a href="javascript:" xedit="text" data-type="text" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$data['val'].'" data-params="{dbTable: \''.$data['fieldset']['dbTable'].'\', pkName: \''.$pkName.'\', '.$order_str.' log: \''.$log.'\''.$action_str.', xEditCusrom: \'xEditCustomOrderShippingCost\'}" data-pk="'.$pk.'" data-placement="top" data-title="'.$data['fieldset']['name'].'" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'">'.$val2text.'</a>';

		} else {
			return $val2text;
		}
	}

	public function xEditCustomOrderShippingCost() {
		$post = $this->request->post;
		$new_shipping_value = isset($post['value']) && $post['value'] > 0 ? $post['value'] : 0;

		if (isset($post['order_id']) && $post['order_id']) {
			$order_id =  $post['order_id'];
			$totals = $this->ompro->getOrderTotals($order_id);
			$old_shipping_value = $old_total_value = 0;
			foreach ($totals as $total) {
				if ($total['code'] == 'shipping') {
					$old_shipping_value = $total['value'];
				} elseif ($total['code'] == 'total') {
					$old_total_value = $total['value'];
				}
			}

			if ($old_shipping_value !== $new_shipping_value) {
				$shipping_value = $new_shipping_value - $old_shipping_value;
				$total_value = $old_total_value + $shipping_value;

				$this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET `value` = '" . (float)$new_shipping_value . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'shipping'");

				$this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET `value` = '" . (float)$total_value . "' WHERE order_id = '" . (int)$order_id . "' AND code = 'total'");

				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET `total` = '" . (float)$total_value . "' WHERE order_id = '" . (int)$order_id . "'");

				$log_str = 'Измена стоимость доставки, значение: ' . (float)$new_shipping_value;
				$this->addHistoryLog($post, $log_str);

				if (isset($post['action']) && $post['action']) {
					$this->{$post['action']}($post);
				}

				header("HTTP/1.1 200 ok");
			}
		} else {
			header("HTTP/1.1 400 Error: data is not saved!");
		}
	}
	// <- ShippingCost


	// тест
	public function xEditLinkCustomOrderStatus($data) {
		if ($data['to_edit']) {
			$pageReload = isset($data['fieldset']['reload_onsave']) && $data['fieldset']['reload_onsave'] ? 1 : 0;
			$order_status_id = $data['order_info']['order_status_id'];
			$source = '';
			$implode = array();
			$options = $this->getOrderStatuses($data['setting_user_group']);

			foreach ($options as $option) {
				$implode[] = '{value: \''.$option['id'].'\',text:\''.$option['text'].'\'}';
			}

			if ($implode) { $source = '[' . implode(',',$implode) . ']'; }

			$pk = $order_id = $data['order_info']['order_id'];

			return '<a href="javascript:" xedit="select" data-type="select" data-mode="popup" data-emptytext="..." data-name="order_status_id" data-value="'.$order_status_id.'" data-source="'.$source.'" data-params="{dbTable: \'order\', pkName: \'order_id\', xEditCusrom: \'xEditCustomOrderStatus\'}" data-pk="'.$pk.'" data-placement="top" data-title="Статус заказа" pageReload="'.$pageReload.'" data-order_id="'.$order_id.'">'.$data['val'].'</a>';
		} else {
			return $data['val'];
		}
	}

	// тест
	public function xEditLinkOrderFieldsShippingMethod($data) {
		$val2text = $data['val'];

		$shippings = $this->getShippings($data['setting_user_group']);

		$implode = array();
		foreach ($shippings as $shipping) {
			$implode[] = '{ value: \'' . $shipping['id'] . '\', text: \'' . $shipping['text'] . '\' }';
			if ($shipping['id'] == $data['val']) {
				$val2text = $shipping['text'];
			}
		}

		if ($data['to_edit']) {
			$source = '';
			if ($implode) { $source = '[' . implode(',',$implode) . ']'; }
			$value = $data['order_info']['shipping_code'];
			$pk = $order_id = $data['order_info']['order_id'];

			return '<a href="javascript:" xedit="select"  data-type="select" data-mode="popup" data-emptytext="..." data-name="'.$data['key'].'" data-value="'.$value.'" data-source="'.$source.'" data-params="{dbTable: \'order\', pkName: \'order_id\'}" data-pk="'.$pk.'" data-placement="top" data-title="Способ доставки:" pageReload="false" data-order_id="'.$order_id.'" class="xeditable">'.$val2text.'</a>';
		} else {
			return $val2text;
		}
	}

	// <- Кастомные методы редактирования

	///// <- получение форматированных данных


	// Доп. действия (API методы) после редактирования ->

	public function sendNotifyTargetUser($post = array(), $target_type = '') {
		if (isset($post['order_id']) && $target_type) {
			$order_id = $post['order_id'];
			$mail_template_id = $this->ompro->getTargetMailTemplate('target_'.$target_type);
			$sms_template_id = $this->ompro->getTargetSmsTemplate('target_'.$target_type);
			$tlgrm_template_id = $this->ompro->getTargetTlgrmTemplate('target_'.$target_type);

			$all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));

			if (!empty($all_orders_data) && !empty($all_orders_data['param']) &&  !empty($all_orders_data['orders']) && !empty($all_orders_data['orders'][$order_id])) {
				$order_info = $all_orders_data['orders'][$order_id]['order_info'];
			} else {
				$order_info = array();
			}

			if ($order_info && $order_info[$target_type.'_user_id'] && ($mail_template_id || $sms_template_id || $tlgrm_template_id)) {
				$language_id = 0;

				$this->load->model('user/user');
				$user_info = $this->model_user_user->getUser($order_info[$target_type.'_user_id']);

				if ($user_info) {
					if ($mail_template_id) {
						$template_info = $this->ompro->getTemplateTemplate('mail', $mail_template_id);
						$recipients = array();
						$recipients[] = array(
							'recipient_name' => $user_info['firstname'] .' '. $user_info['lastname'], 'email' => $user_info['email']
						);
						$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data, $comment = '', $attachments = array());
					}

					if ($sms_template_id) {
						$template_info = $this->ompro->getTemplateTemplate('sms', $sms_template_id);
						$to = $user_info['telephone'];
						$copies = array();
						$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data, $comment);
					}

					if ($tlgrm_template_id) {
						$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_template_id);
						$chat_ids = array();
						if (isset($user_info['telegram_id']) && $user_info['telegram_id']) {
							$chat_ids[] = $user_info['telegram_id'];
							$this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data, $comment);
						}
					}
				}
			}
		}
	}

	public function xEditActionSendNotifyTargetOrderManager($post = array()) {
		$this->sendNotifyTargetUser($post, 'manager');
	}

	public function xEditActionSendNotifyTargetOrderCourier($post = array()) {
		$this->sendNotifyTargetUser($post, 'courier');
	}

	// Обнулить координаты заказа - если в формате адреса для карты используется поле, которого нет в методе addressFieldList, тогда можно использовать это действие
	public function xEditActionResetShippingLatitudeLongitude($post = array()) {
		if (isset($post['order_id'])) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_latitude='0.000000', shipping_longitude='0.000000' WHERE order_id = '" . (int)$post['order_id'] . "'");
		}
	}

	// при быстром редактировании этих полей будут обнуляться поля координат: shipping_latitude, shipping_longitude в таблице order - нужно для карты, т.е. для обновления координат
	public function addressFieldList() {
		return array('shipping_country','shipping_country_id','shipping_zone','shipping_zone_id','shipping_city','shipping_address_1','shipping_address_2', 'shipping_address_format', 'shipping_custom_field');
	}

	// Обновить группу покупателя в личных данных при редактировании customer_group_id
	public function xEditActionUpdatePersonalDataCustomerGroup($post = array()) {
		if (isset($post['order_id']) && isset($post['name']) && $post['name'] == 'customer_group_id') {
			$customer_id = $this->ompro->getOrderFieldValue($post['order_id'], 'customer_id');
			if ($customer_id) {
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET customer_group_id = '" . (int)$post['value'] . "' WHERE customer_id = '" . (int)$customer_id . "'");
			}
		}
	}

	// <- Доп. действия после редактирования



	// Разное ->

	public function orderpro_status() {
		return isset($this->db->query("SELECT code FROM " . DB_PREFIX . "setting WHERE  `code` = 'orderpro' LIMIT 1")->row['code']) ? true : false;
	}

	public function orderq_status() {
		return file_exists(DIR_SYSTEM . 'config/cartbinder/orderq.php') ? true : false;
	}

	public function scheduler_status() {
		return isset($this->db->query("SELECT code FROM `" . DB_PREFIX . "extension` WHERE `type` = 'shipping' AND `code` = 'scheduler' LIMIT 1")->row['code']) ? true : false;
	}

	public function genAwesomeIcons() {
		$icons = array('glass','music','search','envelope-o','heart','star','star-o','user','film','th-large','th','th-list','check','remove','close','times','search-plus','search-minus','power-off','signal','gear','cog','trash-o','home','file-o','clock-o','road','download','arrow-circle-o-down','arrow-circle-o-up','inbox','play-circle-o','rotate-right','repeat','refresh','list-alt','lock','flag','headphones','volume-off','volume-down','volume-up','qrcode','barcode','tag','tags','book','bookmark','print','camera','font','bold','italic','text-height','text-width','align-left','align-center','align-right','align-justify','list','dedent','outdent','indent','video-camera','photo','image','picture-o','pencil','map-marker','adjust','tint','edit','pencil-square-o','share-square-o','check-square-o','arrows','step-backward','fast-backward','backward','play','pause','stop','forward','fast-forward','step-forward','eject','chevron-left','chevron-right','plus-circle','minus-circle','times-circle','check-circle','question-circle','info-circle','crosshairs','times-circle-o','check-circle-o','ban','arrow-left','arrow-right','arrow-up','arrow-down','mail-forward','share','expand','compress','plus','minus','asterisk','exclamation-circle','gift','leaf','fire','eye','eye-slash','warning','exclamation-triangle','plane','calendar','random','comment','magnet','chevron-up','chevron-down','retweet','shopping-cart','folder','folder-open','arrows-v','arrows-h','bar-chart-o','bar-chart','twitter-square','facebook-square','camera-retro','key','gears','cogs','comments','thumbs-o-up','thumbs-o-down','star-half','heart-o','sign-out','linkedin-square','thumb-tack','external-link','sign-in','trophy','github-square','upload','lemon-o','phone','square-o','bookmark-o','phone-square','twitter','facebook-f','facebook','github','unlock','credit-card','feed','rss','hdd-o','bullhorn','bell','certificate','hand-o-right','hand-o-left','hand-o-up','hand-o-down','arrow-circle-left','arrow-circle-right','arrow-circle-up','arrow-circle-down','globe','wrench','tasks','filter','briefcase','arrows-alt','group','users','chain','link','cloud','flask','cut','scissors','copy','files-o','paperclip','save','floppy-o','square','navicon','reorder','bars','list-ul','list-ol','strikethrough','underline','table','magic','truck','pinterest','pinterest-square','google-plus-square','google-plus','money','caret-down','caret-up','caret-left','caret-right','columns','unsorted','sort','sort-down','sort-desc','sort-up','sort-asc','envelope','linkedin','rotate-left','undo','legal','gavel','dashboard','tachometer','comment-o','comments-o','flash','bolt','sitemap','umbrella','paste','clipboard','lightbulb-o','exchange','cloud-download','cloud-upload','user-md','stethoscope','suitcase','bell-o','coffee','cutlery','file-text-o','building-o','hospital-o','ambulance','medkit','fighter-jet','beer','h-square','plus-square','angle-double-left','angle-double-right','angle-double-up','angle-double-down','angle-left','angle-right','angle-up','angle-down','desktop','laptop','tablet','mobile-phone','mobile','circle-o','quote-left','quote-right','spinner','circle','mail-reply','reply','github-alt','folder-o','folder-open-o','smile-o','frown-o','meh-o','gamepad','keyboard-o','flag-o','flag-checkered','terminal','code','mail-reply-all','reply-all','star-half-empty','star-half-full','star-half-o','location-arrow','crop','code-fork','unlink','chain-broken','question','info','exclamation','superscript','subscript','eraser','puzzle-piece','microphone','microphone-slash','shield','calendar-o','fire-extinguisher','rocket','maxcdn','chevron-circle-left','chevron-circle-right','chevron-circle-up','chevron-circle-down','html5','css3','anchor','unlock-alt','bullseye','ellipsis-h','ellipsis-v','rss-square','play-circle','ticket','minus-square','minus-square-o','level-up','level-down','check-square','pencil-square','external-link-square','share-square','compass','toggle-down','caret-square-o-down','toggle-up','caret-square-o-up','toggle-right','caret-square-o-right','euro','eur','gbp','dollar','usd','rupee','inr','cny','rmb','yen','jpy','ruble','rouble','rub','won','krw','bitcoin','btc','file','file-text','sort-alpha-asc','sort-alpha-desc','sort-amount-asc','sort-amount-desc','sort-numeric-asc','sort-numeric-desc','thumbs-up','thumbs-down','youtube-square','youtube','xing','xing-square','youtube-play','dropbox','stack-overflow','instagram','flickr','adn','bitbucket','bitbucket-square','tumblr','tumblr-square','long-arrow-down','long-arrow-up','long-arrow-left','long-arrow-right','apple','windows','android','linux','dribbble','skype','foursquare','trello','female','male','gittip','gratipay','sun-o','moon-o','archive','bug','vk','weibo','renren','pagelines','stack-exchange','arrow-circle-o-right','arrow-circle-o-left','toggle-left','caret-square-o-left','dot-circle-o','wheelchair','vimeo-square','turkish-lira','try','plus-square-o','space-shuttle','slack','envelope-square','wordpress','openid','institution','bank','university','mortar-board','graduation-cap','yahoo','google','reddit','reddit-square','stumbleupon-circle','stumbleupon','delicious','digg','pied-piper','pied-piper-alt','drupal','joomla','language','fax','building','child','paw','spoon','cube','cubes','behance','behance-square','steam','steam-square','recycle','automobile','car','cab','taxi','tree','spotify','deviantart','soundcloud','database','file-pdf-o','file-word-o','file-excel-o','file-powerpoint-o','file-photo-o','file-picture-o','file-image-o','file-zip-o','file-archive-o','file-sound-o','file-audio-o','file-movie-o','file-video-o','file-code-o','vine','codepen','jsfiddle','life-bouy','life-buoy','life-saver','support','life-ring','circle-o-notch','ra','rebel','ge','empire','git-square','git','y-combinator-square','yc-square','hacker-news','tencent-weibo','qq','wechat','weixin','send','paper-plane','send-o','paper-plane-o','history','circle-thin','header','paragraph','sliders','share-alt','share-alt-square','bomb','soccer-ball-o','futbol-o','tty','binoculars','plug','slideshare','twitch','yelp','newspaper-o','wifi','calculator','paypal','google-wallet','cc-visa','cc-mastercard','cc-discover','cc-amex','cc-paypal','cc-stripe','bell-slash','bell-slash-o','trash','copyright','at','eyedropper','paint-brush','birthday-cake','area-chart','pie-chart','line-chart','lastfm','lastfm-square','toggle-off','toggle-on','bicycle','bus','ioxhost','angellist','cc','shekel','sheqel','ils','meanpath','buysellads','connectdevelop','dashcube','forumbee','leanpub','sellsy','shirtsinbulk','simplybuilt','skyatlas','cart-plus','cart-arrow-down','diamond','ship','user-secret','motorcycle','street-view','heartbeat','venus','mars','mercury','intersex','transgender','transgender-alt','venus-double','mars-double','venus-mars','mars-stroke','mars-stroke-v','mars-stroke-h','neuter','genderless','facebook-official','pinterest-p','whatsapp','server','user-plus','user-times','hotel','bed','viacoin','train','subway','medium','yc','y-combinator','optin-monster','opencart','expeditedssl','battery-4','battery-full','battery-3','battery-three-quarters','battery-2','battery-half','battery-1','battery-quarter','battery-0','battery-empty','mouse-pointer','i-cursor','object-group','object-ungroup','sticky-note','sticky-note-o','cc-jcb','cc-diners-club','clone','balance-scale','hourglass-o','hourglass-1','hourglass-start','hourglass-2','hourglass-half','hourglass-3','hourglass-end','hourglass','hand-grab-o','hand-rock-o','hand-stop-o','hand-paper-o','hand-scissors-o','hand-lizard-o','hand-spock-o','hand-pointer-o','hand-peace-o','trademark','registered','creative-commons','gg','gg-circle','tripadvisor','odnoklassniki','odnoklassniki-square','get-pocket','wikipedia-w','safari','chrome','firefox','opera','internet-explorer','tv','television','contao','500px','amazon','calendar-plus-o','calendar-minus-o','calendar-times-o','calendar-check-o','industry','map-pin','map-signs','map-o','map','commenting','commenting-o','houzz','vimeo','black-tie','fonticons');

		$btn_icons = '<span class="btn btn-icon-source" title="fa"><i class="fa"></i></span> ';

		foreach ($icons as $icon) {
			$btn_icons .= '<span class="btn btn-icon-source" title="'.$icon.'"><i class="fa fa-'.$icon.'"></i></span> ';
		}

		return $btn_icons;
	}

	public function getLanguageCode($language_id = 0) {
		if ($language_id) {
			$query = $this->db->query("SELECT code FROM `" . DB_PREFIX . "language` WHERE language_id = '" . (int)$language_id . "' AND status='1' ");

			if (isset($query->row['code']) && $query->row['code']) {
				return $query->row['code'];
			}
		} elseif ($this->config->get('config_admin_language')) {
			return $this->config->get('config_admin_language');
		}
		return 'ru-RU';
	}

	public function getStringBetween($str,$from,$to) {
		$sub = substr($str, strpos($str,$from)+strlen($from),strlen($str));
		return substr($sub,0,strpos($sub,$to));
	}

	public function translit_cyrillic_to_latina($string, $gost=false) {
		if($gost) {
			$replace = array(
			"А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
			"Е"=>"E","е"=>"e","Ё"=>"E","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z",
			"И"=>"I","и"=>"i", // russian translit
			//"И"=>"Y","и"=>"y", // ukrainian translit
			"Й"=>"I","й"=>"i","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n","О"=>"O","о"=>"o",
			"П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t","У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f",
			"Х"=>"H","х"=>"h","Ц"=>"Tc","ц"=>"tc","Ч"=>"Ch","ч"=>"ch","Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch",
			"Ы"=>"Y","ы"=>"y","Э"=>"E","э"=>"e","Ю"=>"Iu","ю"=>"iu","Я"=>"Ia","я"=>"ia","ъ"=>"","ь"=>"",
			"«"=>"", "»"=>"", "„"=>"", "“"=>"", "“"=>"", "”"=>"", "\•"=>"",
			// ukrainian letters
			"І"=>"I","і"=>"i",
			"Ї"=>"Yi","ї"=>"yi",
			"Є"=>"Ye","є"=>"ye",
			);
		} else {
			$arStrES = array("ае","уе","ое","ые","ие","эе","яе","юе","ёе","ее","ье","ъе","ый","ий");
			$arStrOS = array("аё","уё","оё","ыё","иё","эё","яё","юё","ёё","её","ьё","ъё","ый","ий");
			$arStrRS = array("а$","у$","о$","ы$","и$","э$","я$","ю$","ё$","е$","ь$","ъ$","@","@");

			$replace = array(
			"А"=>"A","а"=>"a","Б"=>"B","б"=>"b","В"=>"V","в"=>"v","Г"=>"G","г"=>"g","Д"=>"D","д"=>"d",
			"Е"=>"Ye","е"=>"e","Ё"=>"Ye","ё"=>"e","Ж"=>"Zh","ж"=>"zh","З"=>"Z","з"=>"z",
			"И"=>"I","и"=>"i", // russian translit
			//"И"=>"Y","и"=>"y", // ukrainian translit
			"Й"=>"Y","й"=>"y","К"=>"K","к"=>"k","Л"=>"L","л"=>"l","М"=>"M","м"=>"m","Н"=>"N","н"=>"n",
			"О"=>"O","о"=>"o","П"=>"P","п"=>"p","Р"=>"R","р"=>"r","С"=>"S","с"=>"s","Т"=>"T","т"=>"t",
			"У"=>"U","у"=>"u","Ф"=>"F","ф"=>"f","Х"=>"H","х"=>"h","Ц"=>"Ts","ц"=>"ts","Ч"=>"Ch","ч"=>"ch",
			"Ш"=>"Sh","ш"=>"sh","Щ"=>"Shch","щ"=>"shch","Ъ"=>"","ъ"=>"","Ы"=>"Y","ы"=>"y","Ь"=>"","ь"=>"",
			"Э"=>"E","э"=>"e","Ю"=>"Yu","ю"=>"yu","Я"=>"Ya","я"=>"ya","@"=>"y","$"=>"ye",
			"«"=>"", "»"=>"", "„"=>"", "“"=>"", "“"=>"", "”"=>"", "\•"=>"",
			// ukrainian letters
			"І"=>"I","і"=>"i",
			"Ї"=>"Yi","ї"=>"yi",
			"Є"=>"Ye","є"=>"ye",
			"№"=>"No.",
			);

			$string = str_replace($arStrES, $arStrRS, $string);
			$string = str_replace($arStrOS, $arStrRS, $string);
		}

		$output = iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));

		return $output;
	}

	public function genPass($number = 8) {
		$arr = array(
			'Q','W','E','R','T','Y','U','I','O','P',
			'A','S','D','F','G','H','J','K','L',
			'Z','X','C','V','B','N','M',
			1,2,3,4,5,6,7,8,9,0
		);

		$pass = "";
		for($i = 0; $i < $number; $i++) {
			$index = rand(0, count($arr) - 1);
			$pass .= $arr[$index];
		}
		return $pass;
	}

	// <- Разное


	///// Наборы значений для селекторов и чекбоксов ->
	// Методы наборов значений могут быть добавлены в списки: getFilterSelectorValuesApiMethodList, valuesApiMethodList

    public function getLimitValues() {
		$values = array();
		$limit_list = array('5', '10', '15', '20', '25', '30', '40', '50', '75', '100', '150', '200');
		foreach ($limit_list as $value) {
			$values[] = array(
				'id'	=> $value,
				'text'	=> $value
			);
		}

		return $values;
    }

    public function getLimitValuesShort() {
		$values = array();
		$limit_list = array('5', '10', '15', '20', '25', '30', '40', '50');
		foreach ($limit_list as $value) {
			$values[] = array(
				'id'	=> $value,
				'text'	=> $value
			);
		}
		return $values;
    }

	public function getStores($setting_user_group = array()) {
		$this->load->model('setting/store');
		$this->load->language('sale/ompro');
		$stores = array();
		$stores[] = array('store_id' => '0', 'name'     => $this->language->get('text_store_default'));
		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$stores[] = array('store_id' => $result['store_id'], 'name' => $result['name']);
		}

		if ($setting_user_group) {
			$set = 'stores';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$user_stores = $setting_user_group['select_orders'][$set];
			} else {
				$user_stores = null;
			}
			if ($user_stores && !empty($stores)) {
				foreach ($stores as $id => $store) {
					if (!isset($user_stores[$store['store_id']])) {
						unset($stores[$id]);
					}
				}
			}
		}

        $values = array();
		foreach ($stores as $store) {
			$values[] = array(
				'id'	=> $store['store_id'],
				'text'	=> $store['name']
			);
		}

		return $values;
	}

	public function getOrderStatuses($setting_user_group = array()) {
		$language_id = $this->config->get('config_language_id');
		$user_group_id = isset($setting_user_group['user_group_id']) ? $setting_user_group['user_group_id'] : 0;

		$values = $this->cache->get('ompro.usergroupid'.$user_group_id.'.statuses.'.$language_id);

		if (!$values) {
			$statuses = $this->db->query("SELECT order_status_id, name FROM " . DB_PREFIX . "order_status WHERE language_id = '" . (int)$language_id . "' ORDER BY order_status_id")->rows;

			if ($setting_user_group) {
				if (!empty($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders']['order_statuses'])) {
					$user_statuses = $setting_user_group['select_orders']['order_statuses'];
				} else {
					$user_statuses = null;
				}

				if ($user_statuses) {
					foreach ($statuses as $id => $status) {
						if (!in_array($status['order_status_id'], $user_statuses)) {
							unset($statuses[$id]);
						}
					}
				}
			}

			$values = array();

			$statuses[] = array('order_status_id' => '0', 'name' => 'Брошенные заказы');

			foreach ($statuses as $status) {
				$values[] = array(
					'id'	=> $status['order_status_id'],
					'text'	=> $status['name']
				);
			}

			$this->cache->set('ompro.usergroupid'.$user_group_id.'.statuses.'.$language_id, $values);
		}

		return $values;
	}


	public function getPaymentsInstalled($setting_user_group = array()) {
		$values = array();
		$results = $this->ompro->getInstalled('payment');
		if ($results) {
			foreach ($results as $code) {
				if ($this->ompro->ocversion < 230) {
					$this->load->language('payment/'.$code);
				} else {
					$this->load->language('extension/payment/'.$code);
				}

				$values[] = array(
					'id'	=> $code,
					'text'	=> $this->clearTags(trim($this->language->get('heading_title')))
				);
			}
		}

		if ($setting_user_group) {
			$set = 'order_payments';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$user_shippings = $setting_user_group['select_orders'][$set];
			} else {
				$user_shippings = null;
			}
			if ($user_shippings) {
				foreach ($values as $key => $payment) {
					if (!in_array($payment['id'], $user_shippings)) {
						unset($values[$key]);
					}
				}
			}
		}

		$values[] = array( 'id'	=> '*', 'text'	=> '- не указан -' );
		return $values;
	}

	public function getShippingsInstalled($setting_user_group = array()) {
		$values = array();
		$results = $this->ompro->getInstalled('shipping');
		if ($results) {
			foreach ($results as $code) {
				if ($this->ompro->ocversion < 230) {
					$this->load->language('shipping/'.$code);
				} else {
					$this->load->language('extension/shipping/'.$code);
				}

				$values[] = array(
					'id'	=> $code,
					'text'	=> $this->clearTags(trim($this->language->get('heading_title')))
				);
			}
		}

		if ($setting_user_group) {
			$set = 'order_shippings';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$user_shippings = $setting_user_group['select_orders'][$set];
			} else {
				$user_shippings = null;
			}
			if ($user_shippings) {
				foreach ($values as $key => $shipping) {
					if (!in_array($shipping['id'], $user_shippings)) {
						unset($values[$key]);
					}
				}
			}
		}

		$values[] = array( 'id'	=> '*', 'text'	=> '- не указан -' );
		return $values;
	}

	public function getPayments($setting_user_group = array()) {
		$payments = array();

		if (!$this->config->get('filterit_shipping')) {
			$results = $this->ompro->getInstalled('payment');

			if ($results) {
				foreach ($results as $code) {
					/* transfer_plus (oplataplus) */
					if ($code == 'transfer_plus') {
						$transfer_plus = $this->config->get('transfer_plus_module');
						if ($transfer_plus) {
							foreach ($transfer_plus as $id => $module) {
								$title = html_entity_decode($module['title'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');
								$payments['transfer_plus.'.$id] = $title;
							}
						}
					} else {
						if ($this->ompro->ocversion < 230) {
							$this->load->language('payment/'.$code);
						} else {
							$this->load->language('extension/payment/'.$code);
						}
						$payments[$code] = $this->language->get('heading_title');
					}
				}
			}
		}
		else {
			$lang = $this->config->get('config_language');
			$filterit_payment = $this->config->get('filterit_payment');
			foreach (array('installed','created') as $type) {
				if (!empty($filterit_payment[$type])) {
					foreach ($filterit_payment[$type] as $id => $module) {
						$title = html_entity_decode($module['title'][$lang], ENT_QUOTES, 'UTF-8');
						$payments[$id] = $title;
					}
				}
			}
		}

		if ($setting_user_group) {
			$set = 'order_payments';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$user_payments = $setting_user_group['select_orders'][$set];
			} else {
				$user_payments = null;
			}
			if ($user_payments) {
				foreach ($payments as $code => $name) {
					if (!in_array($code, $user_payments)) {
						unset($payments[$code]);
					}
				}
			}
		}

		$values = array();
		foreach ($payments as $id => $text) {
			$values[] = array(
				'id'	=> $id,
				'text'	=> $text
			);
		}
		$values[] = array( 'id'	=> '*', 'text'	=> '- не указан -' );

		return $values;
	}

	public function getShippings($setting_user_group = array()) {
		$shippings = array();

		if (!$this->config->get('filterit_shipping')) {
			$results = $this->ompro->getInstalled('shipping');

			if ($results) {
				$language_id = $this->config->get('config_language_id');
				$poshta_methods = $this->getPoshtaMethods();

				foreach ($results as $code) {
					if ($code == 'scheduler') {
						$scheduler_ships = $this->config->get('scheduler_ships');
						if($scheduler_ships) {
							foreach ($scheduler_ships as $ship_id => $ship) {
								$standart_module = count($ship) > 1 ? false : true;
								$title = html_entity_decode($ship[0]['title'][$language_id], ENT_QUOTES, 'UTF-8');
								foreach ($ship as $key => $module) {
									if ($key == 0 && $standart_module) {
										$shippings['scheduler.stand-'.$ship_id] = $title;
									}
									if ($key > 0) {
										$shippings['scheduler.sched-'.$ship_id] = $title;
									}
								}
							}
						}
					}
					elseif (in_array($code, $poshta_methods)) {
						$config = $this->config->get($code);
						if (!empty($config['shipping_methods'])) {
							$methods = $config['shipping_methods'];
							foreach ($methods as $method => $setting) {
								$title = html_entity_decode($setting['name'][$language_id], ENT_QUOTES, 'UTF-8');
								$shippings[$code.'.'.$method] = $title;
							}
						}
					}
					elseif ($code == 'xshippingpro') {
						$xshippingpro_methods = $this->db->query("SELECT * FROM `" . DB_PREFIX . "xshippingpro`")->rows;
						if ($xshippingpro_methods) {
							foreach ($xshippingpro_methods as $method) {
								$tab_id = $method['tab_id'];
								$xshippingpro = $method['method_data'];
								$xshippingpro = @unserialize(@base64_decode($xshippingpro));
								if (!is_array($xshippingpro)) $xshippingpro = array();
								if (!empty($xshippingpro)) {
									$title = html_entity_decode($xshippingpro['name'][$language_id], ENT_QUOTES, 'UTF-8');
									$shippings['xshippingpro.xshippingpro'.$tab_id] = $title;
								}
							}
						}
					}
					elseif ($code == 'dostavkaplus') {
						$dostavkaplus_module = $this->config->get('dostavkaplus_module');
						if ($dostavkaplus_module) {
							foreach ($dostavkaplus_module as $id => $module) {
								$title = html_entity_decode($module['title'][$language_id], ENT_QUOTES, 'UTF-8');
								$shippings['dostavkaplus.sh'.$id] = $title;
							}
						}
					}
					else {
						if ($this->ompro->ocversion < 230) {
							$this->load->language('shipping/'.$code);
						} else {
							$this->load->language('extension/shipping/'.$code);
						}
						$shippings[$code] = $this->language->get('heading_title');
					}
				}
			}
		}
		else {
			$lang = $this->config->get('config_language');
			$filterit_shipping = $this->config->get('filterit_shipping');

			foreach (array('installed','created') as $type) {
				if (!empty($filterit_shipping[$type])) {
					foreach ($filterit_shipping[$type] as $key => $group) {
						$methods = isset($group['methods']) ? $group['methods'] : array();
						if (!empty($methods)) {
							foreach ($methods as $id => $module) {
								$title = html_entity_decode($module['title'][$lang], ENT_QUOTES, 'UTF-8');
								$shippings[$key.'.'.$id] = $title;
							}
						} else {
							$grouptitle = html_entity_decode($group['title'][$lang], ENT_QUOTES, 'UTF-8');
							$shippings[$key.'.'.$key] = $grouptitle;
						}
					}
				}
			}
		}

		if ($setting_user_group) {
			$set = 'order_shippings';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$user_shippings = $setting_user_group['select_orders'][$set];
			} else {
				$user_shippings = null;
			}
			if ($user_shippings) {
				foreach ($shippings as $code => $name) {
					if (!in_array($code, $user_shippings)) {
						unset($shippings[$code]);
					}
				}
			}
		}

		$values = array();
		foreach ($shippings as $id => $text) {
			$values[] = array(
				'id'	=> trim($id),
				'text'	=> $this->clearTags(trim($text))
			);
		}
		$values[] = array( 'id'	=> '*', 'text'	=> '- не указан -' );

		return $values;
	}


    public function getUserGroupPaymentCountries($setting_user_group = array(), $target = 'payment') {
		return $this->getUserGroupCountries($setting_user_group, 'payment');
	}

    public function getUserGroupShippingCountries($setting_user_group = array(), $target = 'payment') {
		return $this->getUserGroupCountries($setting_user_group, 'shipping');
	}

    public function getUserGroupCountries($setting_user_group, $target = 'payment') {
		$user_group_id = isset($setting_user_group['user_group_id']) ? $setting_user_group['user_group_id'] : 0;
		$country_data = $this->cache->get('ompro.usergroupid'.$user_group_id.'.'.$target.'countries');

		if (!$country_data) {
			$set = $target.'_geo_zones';
			if (isset($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders'][$set])) {
				$geo_zones = $setting_user_group['select_orders'][$set];
			} else {
				$geo_zones = array();
			}

			$country_ides = array();
			if ($geo_zones) {
				$query = $this->db->query("SELECT DISTINCT country_id FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id IN (".implode(", ", $geo_zones).")");

				foreach ($query->rows as $result) {
					$country_ides[] = $result['country_id'];
				}
			}

			$country_filter = '';
			if ($country_ides) { $country_filter = "AND country_id IN (".implode(", ", $country_ides).")"; }

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE status = '1' $country_filter ORDER BY name ASC");

			$country_data = $query->rows;
			$this->cache->set('ompro.usergroupid'.$user_group_id.'.'.$target.'countries', $country_data);
		}

		$results = $country_data;
		$values =  array();
        foreach ($results as $result) {
			$values[] =  array(
				'id'   => $result['country_id'],
				'text' => $result['name']
            );
        }
        return $values;
    }

	public function getManagerUsers($setting_user_group = array()) {
		return $this->getTargetGroupUsers('manager');
	}

	public function getCourierUsers($setting_user_group = array()) {
		return $this->getTargetGroupUsers('courier');
	}

	public function getTargetGroupUsers($target = '') {
		$setting_groups = $this->ompro->getSettingGroupsIdesByTarget($target);
		$groups = array();
		foreach ($setting_groups as $group) {
			$groups[] = $group['user_group_id'];
		}

		$result = array();
		if (!empty($groups)) {
			$result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_group_id IN (".implode(", ", $groups).") ")->rows;
		}

		$users = array();
		$text_null = $target !== '' ? '-- Не назначен --' : '-- не выбран --';

		$users[] = array(
			'id' => '0',
			'text' => $text_null
		);

		if (!empty($result)) {
			foreach ($result as $user) {
				$name = $user['firstname'] . ' ' . $user['lastname'] . ' ('.$user['username'].')';
				$users[] = array(
					'id' => $user['user_id'],
					'text' => $name
				);
			}
		}

		return $users;
	}

	public function getActiveUsers() {
		$result = $this->ompro->getActiveUsers();

		$users = array();
		$users[] = array(
			'id' => '0',
			'text' => '-- не выбран --'
		);

		if (!empty($result)) {
			foreach ($result as $user) {
				$name = $user['firstname'] . ' ' . $user['lastname'] . ' ('.$user['username'].')';
				$users[] = array(
					'id' => $user['user_id'],
					'text' => $name
				);
			}
		}

		return $users;
	}

    public function getCusromerGroups($setting_user_group = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "customer_group cg LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$implode = array();

		if (!empty($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders']['customer_groups'])) {
			$sql .= " AND (cg.customer_group_id IN (".implode(", ", $setting_user_group['select_orders']['customer_groups'])."))";
		}

		$sql .= " ORDER BY cgd.name ASC";

		$query = $this->db->query($sql);

		$groups = array();
		foreach ($query->rows as $result) {
			$groups[] = array(
				'id' 	=> $result['customer_group_id'],
				'text' => $result['name'] . $this->clearTags(($result['customer_group_id'] == $this->config->get('config_customer_group_id')) ? $this->language->get('text_default') : null)
			);
		}

		return $groups;
    }

	public function getManufacturers () {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer");
		$manufacturers = array();
		foreach ($query->rows as $result) {
			$manufacturers[] = array(
				'id' 	=> $result['manufacturer_id'],
				'text' => $result['name']
			);
		}
		return $manufacturers;
	}

	public function getProductCategories () {
		$sql = "SELECT cp.category_id AS category_id, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '&nbsp;&nbsp;&gt;&nbsp;&nbsp;') AS name FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id) LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id) WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY cp.category_id";

		$query = $this->db->query($sql);

		$categories = array();
		foreach ($query->rows as $result) {
			$categories[] = array(
				'id' 	=> $result['category_id'],
				'text' => $result['name']
			);
		}

		return $categories;
	}

	public function getCurrencies() {
		$currencies = array();

		$this->load->model('localisation/currency');
		$results = $this->model_localisation_currency->getCurrencies();

		foreach ($results as $result) {
			$currencies[] = array(
				'id' 	=> $result['currency_id'],
				'text'	=> $result['title'] . (($result['code'] == $this->config->get('config_currency')) ? $this->language->get('text_default') : null)
			);
		}

		return $currencies;
	}

/*
	public function getDownloadsAll() { // ???
		$downloads = array();

		$sql = "SELECT * FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);

		$results = array();
		if ($query->num_rows) {
			$results = $query->rows;
		}

		foreach ($results as $result) {
			$downloads[] = array(
				'id' 	=> $result['download_id'],
				'text'	=> $result['name'],
			);
		}

		return $downloads;
	}
 */

	// Для модуля "АОП" Автоматическая обработка прайс-листов
	public function getSupplers() {
		$supplers = array();

		if (file_exists(DIR_APPLICATION . 'model/catalog/suppler.php')) {
			$results = $this->db->query("SELECT suppler_id, name FROM `" . DB_PREFIX . "suppler` GROUP BY suppler_id ")->rows;

			foreach ($results as $suppler) {
				$s_id=$suppler['suppler_id']; if ($s_id<10) {$s_id='0'.$s_id;}
				$supplers[] = array(
					'id' 	=> $s_id,
					'text'	=> $suppler['name']
				);
			}
		}

		return $supplers;
	}

	///// <- Наборы значений


	// для вывода значений в виде таблицы, нумерованного и маркированного списка
	public function nameValueTable($values = array(), $title = '') {
		$html = '';
		if ($values) {
			$html .= '<div class="table-responsive">';
			$html .= '  <table class="table table-bordered">';
			if ($title) {
				$html .= '<thead><tr><td colspan="2">'.$title.'</td></tr></thead>';
			}
			$html .= '    <tbody>';
			foreach ($values as $value) {
				$html .= '<tr>';
				$html .= '  <td>'.$value['name'].'</td>';
				$html .= '  <td>'.$value['value'].'</td>';
				$html .= '</tr>';
			}
			$html .= '</tbody></table></div>';
		}
		return $html;
	}

	public function nameValueOlList($values = array()) {
		$html = '';
		if ($values) {
			$html .= '<ol>';
			foreach ($values as $value) {
				$html .= '<li>'.$value['name'].': '.$value['value'].'</li>';
			}
			$html .= '</ol>';
		}
		return $html;
	}

	public function nameValueUlList($values = array()) {
		$html = '';
		if ($values) {
			$html .= '<ul>';
			foreach ($values as $value) {
				$html .= '<li>'.$value['name'].' - '.$value['value'].'</li>';
			}
			$html .= '</ul>';
		}
		return $html;
	}


	// Методы предварительной Обработки и Формата  данных Заказа и Товара ->
	// Могут быть добавлены в списки: orderDataFormatMethodList, productDataFormatMethodList

	public function nl2br($value = '', $order_info = array(), $product_info = array()) {
		return nl2br($value);
	}

	public function paymentAddressFormat($value = '', $order_info = array(), $product_info = array()) {
		$payment_address = '';

		if (!empty($order_info)) {
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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
				'zone_code' => isset($order_info['payment_zone_code']) ? $order_info['payment_zone_code'] : '',
				'country'   => $order_info['payment_country']
			);

			$payment_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
		}

		return $payment_address;
	}

	public function shippingAddressFormat($value = '', $order_info = array(), $product_info = array()) {
		$shipping_address = '';

		if ($order_info['shipping_address_format']) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
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
			'zone_code' => isset($order_info['shipping_zone_code']) ? $order_info['shipping_zone_code'] : '',
			'country'   => $order_info['shipping_country']
		);

		$shipping_address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		return $shipping_address;
	}

	// Custom Fields Opnacart
	public function getCustomField($order_info = array(), $type = '') {
		$this->load->model('customer/custom_field');
		$custom_fields = array();
		$order_custom_fields = array();
		$location = '';

		if ($type == 'custom_field') {
			$location = 'account';
			$order_custom_fields = json_decode($order_info['custom_field'], true);
		} elseif ($type == 'payment_custom_field') {
			$location = 'address';
			$order_custom_fields = json_decode($order_info['payment_custom_field'], true);
		} elseif ($type == 'shipping_custom_field') {
			$location = 'address';
			$order_custom_fields = json_decode($order_info['shipping_custom_field'], true);
		}

		if ($order_custom_fields && $location) {
			$filter_data = array(
				'sort'  => 'cf.sort_order',
				'order' => 'ASC'
			);

			$custom_fields_all = $this->model_customer_custom_field->getCustomFields($filter_data);

			foreach ($custom_fields_all as $custom_field) {
				if ($custom_field['location'] == $location && isset($order_custom_fields[$custom_field['custom_field_id']])) {
					if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
						$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($order_custom_fields[$custom_field['custom_field_id']]);

						if ($custom_field_value_info) {
							$custom_fields[] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
							);
						}
					}

					if ($custom_field['type'] == 'checkbox' && is_array($order_custom_fields[$custom_field['custom_field_id']])) {
						foreach ($order_custom_fields[$custom_field['custom_field_id']] as $custom_field_value_id) {
							$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($custom_field_value_id);

							if ($custom_field_value_info) {
								$custom_fields[] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name']
								);
							}
						}
					}

					if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
						$custom_fields[] = array(
							'name'  => $custom_field['name'],
							'value' => $order_custom_fields[$custom_field['custom_field_id']]
						);
					}

					if ($custom_field['type'] == 'file') {
						$upload_info = $this->model_tool_upload->getUploadByCode($order_custom_fields[$custom_field['custom_field_id']]);

						if ($upload_info) {
							$custom_fields[] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
							);
						}
					}
				}
			}
		}

		return $custom_fields;
	}

	public function accountCustomFieldTable($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueTable($this->getCustomField($order_info, 'custom_field'), 'Клиент (доп поле)');
	}

	public function accountCustomFieldOlList($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueOlList($this->getCustomField($order_info, 'custom_field'));
	}

	public function accountCustomFieldUlList($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueUlList($this->getCustomField($order_info, 'custom_field'));
	}

	public function paymentCustomFieldTable($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueTable($this->getCustomField($order_info, 'payment_custom_field'), 'Адрес оплаты (доп поле)');
	}

	public function paymentCustomFieldOlList($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueOlList($this->getCustomField($order_info, 'payment_custom_field'));
	}

	public function paymentCustomFieldUlList($value = '', $order_info = array(), $product_info = array()) {
		return  $this->nameValueUlList($this->getCustomField($order_info, 'payment_custom_field'));
	}

	public function shippingCustomFieldTable($value = '', $order_info = array(), $product_info = array()) {
		return $order_info['shipping_method'] ? $this->nameValueTable($this->getCustomField($order_info, 'shipping_custom_field'), 'Адрес доставки (доп поле)') : '';
	}

	public function shippingCustomFieldOlList($value = '', $order_info = array(), $product_info = array()) {
		return $order_info['shipping_method'] ? $this->nameValueOlList($this->getCustomField($order_info, 'shipping_custom_field')) : '';
	}

	public function shippingCustomFieldUlList($value = '', $order_info = array(), $product_info = array()) {
		return $order_info['shipping_method'] ? $this->nameValueUlList($this->getCustomField($order_info, 'shipping_custom_field')) : '';
	}


   	public function clearTags($str = '') {
		if ($str && !is_array($str)) {
			$str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');

			$str = preg_replace('/<\s*script[^>]*>.*?<\s*\/\s*script\s*>/is', ' ', $str);
			$str = preg_replace('/<\s*style[^>]*>.*?<\s*\/\s*style\s*>/is', ' ', $str);
			$str = preg_replace('/<\s*select[^>]*>.*?<\s*\/\s*select\s*>/is', ' ', $str);

			$str = strip_tags($str);
			$str = preg_replace('/(<([^>]+)>)/U', ' ', $str);
			$str = preg_replace('/\s\s+/', ' ', $str);

			$str = htmlspecialchars($str, ENT_COMPAT, 'UTF-8');
		}
		return $str;
  	}

   	public function htmlEntityDecode($str = '') {
		if ($str) {
			return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
		}
  	}

   	public function subString($str, $len = 32) {
		return (utf8_strlen($str) > $len ? utf8_substr($str, 0, $len) . '...' : $str);
	}

   	public function subStr30($str = '') {
		return $this->subString($str, 30);
	}

   	public function subStr40($str = '') {
		return $this->subString($str, 40);
	}

	public function dateFormatShort($value = '', $order_info = array(), $product_info = array()) {
		return $value !=='' && $value > 0 ? date('d.m.Y', strtotime($value)) : '';
	}

	public function dateFormatShortColor($value = '', $order_info = array(), $product_info = array()) {
		$date = $value !=='' && $value > 0 ? date('d.m.Y', strtotime($value)) : '';
		if ($date) {
			$today =  strtotime(date('d.m.Y')); $color = '';
			if ($today == strtotime($date)) { $color = 'green';
			} elseif ($today == strtotime(date('d.m.Y', strtotime($date . '+1 day')))) { $color = 'orange';
			} elseif ($today >= strtotime(date('d.m.Y', strtotime($date . '+2 day')))) { $color = 'red';
			} return '<span style="color: '.$color.';">'.$date.'</span>';
		}

		return $date;
	}

	public function dateFormatMiddle($value = '', $order_info = array(), $product_info = array()) {
		return $value !=='' && $value > 0 ? date('d.m.Y H:i', strtotime($value)) : '';
	}

	public function dateFormatLong($value = '', $order_info = array(), $product_info = array()) {
		return $value !=='' && $value > 0 ? date('d.m.Y H:i:s', strtotime($value)) : '';
	}

	public function timeFormatShort($value = '', $order_info = array(), $product_info = array()) {
		return $value !=='' && $value > 0 ? date('H.i', strtotime($value)) : '';
	}

	public function timeFormatLong($value = '', $order_info = array(), $product_info = array()) {
		return $value !=='' && $value > 0 ? date('H.i:s', strtotime($value)) : '';
	}

	public function colorRed($value = '', $order_info = array(), $product_info = array()) { // пример
		return '<span style="color: red; font-weight: bold;">'.$value.'</span>';
	}

   	public function clearNotDigit($str = '') {
		return preg_replace("/[^0-9]/", '', $str);
	}

   	public function weightFormatConfig($weight = 0) {
		return $this->weight->format($weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
	}

	public function quotDoubleToSingle($value = '') {
		return str_replace(array('&quot;', '"'), '\'', $value);
	}

	public function quotDoubleToOblique($value = '') {
		return str_replace(array('&quot;', '"'), '`', $value);
	}

	public function quotAllToSingle($value = '') {
		return str_replace(array('&quot;', '"', '`'), '\'', $value);
	}

	public function quotAllToOblique($value = '') {
		return str_replace(array('&quot;', '"', '\''), '`', $value);
	}

	public function quotAllRemove($value = '') {
		return str_replace(array('&quot;', '"', '\''), '', $value);
	}

	public function floatValue($value = '') {
		if ($value && is_numeric($value)) {
			return (float)$value;
		}
	}

	public function roundValue($value = '') {
		if ($value && is_numeric($value)) {
			return round($value);
		}
	}

	public function roundValueDecimalOne($value = '') {
		if ($value && is_numeric($value)) {
			return round($value, 1);
		}
	}

	public function roundValueDecimalTwo($value = '') {
		if ($value && is_numeric($value)) {
			return round($value, 2);
		}
	}

	public function oAsEmpty($value = '') {
		if ($value == '0') { $value = ''; } return $value;
	}

	public function convertToConfigCurrency($value = '', $order_info = array(), $product_info = array()) {
		if ($value && $order_info) {
			$config_cur = $this->config->get('config_currency');
			$order_cur = $order_info['currency_code'];

			if ($config_cur !== $order_cur) {
				$value = $this->currency->convert($value, $order_cur, $config_cur);
			} else {
				$value = $this->currency->format($value, $order_cur, $order_info['currency_value'], false);
			}
		}
		return $value;
	}

	// <- Методы предварительной Обработки  и Формата данных Заказа и Товара



	// РАСШИРЯЕМЫЕ методы с помощью library / omproapicustom
	// Доп. данные -->

	public function orderAddingData($order_info = array(), $order_products = array()) {
		$order_data = $order_info;

		$weight = 0;
		foreach ($order_products as $product) {
			if (isset($product['weight_total'])) {
				$weight += $this->weight->convert($product['weight_total'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}
		// новые ключи массива order_data - добавлять в orderAddingDataList
		$order_data['order_products_weight_total'] = $weight;
		$order_data['order_products_weight_total_format'] = $this->weightFormatConfig($weight);

		$order_data['current_date'] = date('d.m.Y');
		$order_data['current_datetime'] = date('d.m.Y H:i:s');

		$order_data['customer_edit_link'] = $order_info['customer_id'] ? '<a href="'.$this->url->link('customer/customer/edit', $this->ompro->strtoken . '&customer_id=' . $order_info['customer_id'], 'SSL').'" data-toggle="tooltip" title="Редактировать данные покупателя" target="_blank">'.$order_info['firstname'].' '.$order_info['lastname'].'</a>' : $order_info['firstname'].' '.$order_info['lastname'];

		$order_data['oc_order_info_link'] = $this->url->link('sale/order/info',  $this->ompro->strtoken . '&order_id=' . $order_info['order_id'], 'SSL'); // ???

		return array_merge($order_data, $this->omproapicustom->orderAddingData($order_info, $order_products));
	}

	public function productAddingData($order_info = array(), $product_info = array(), $template = array()) {
		$strtoken = $this->ompro->strtoken;
		$this->load->model('tool/image');

		$product_adding_data = array();

		$image_width = !empty($template['image_width']) ? $template['image_width'] : 64;
		$image_height = !empty($template['image_height']) ? $template['image_height'] : 64;
		$man_image_width = !empty($template['man_image_width']) ? $template['man_image_width'] : 16;
		$man_image_height = !empty($template['man_image_height']) ? $template['man_image_height'] : 16;

		if (!empty($product_info['image']) && is_file(DIR_IMAGE . $product_info['image'])) {
			$img = $this->model_tool_image->resize($product_info['image'], $image_width, $image_height);
		} else {
			$img = $this->model_tool_image->resize('no_image.png', $image_width, $image_height);
		}

		$product_image = !empty($img) ? '<img src="'.$img.'" alt="'.str_replace(array('"', '&quot;'),'', $product_info['op_name']) .'" />' : '';

		if (!empty($product_info['m_image']) && is_file(DIR_IMAGE . $product_info['m_image'])) {
			$img = $this->model_tool_image->resize($product_info['m_image'], $man_image_width, $man_image_height);
		} else {
			$img = $this->model_tool_image->resize('no_image.png', $man_image_width, $man_image_height);
		}

		$m_image = '<img src="'.$img.'" alt="'.str_replace(array('"', '&quot;'),'', $product_info['m_name']).'"  />';

		$store_url = !empty($order_info['store_url']) ? $order_info['store_url'] : '';
		$download = '';
		if ($store_url) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "product_to_download` WHERE product_id = '" . (int)$product_info['product_id'] . "'");

			if ($query->row['total']) {
				$download = $store_url . 'index.php?route=account/download';
			}
		}

		$discount = isset($product_info['discount']) ? $product_info['discount'] : '';
		$special = isset($product_info['special']) ? $product_info['special'] : '';
		$style_from_discount = $discount ? 'text-decoration: line-through;' : '';
		$style_from_special = $special ? 'color: red; text-decoration: line-through;' : '';

		$server = $this->ompro->server;

		// для отбражения в таблице переменных - ключи массива product_adding_data - добавлять в productAddingDataList
		$product_adding_data = array(
			'discount' => $discount,
			'special' => $special,
			'style_from_discount' => $style_from_discount,
			'style_from_special' => $style_from_special,
			'product_image' => $product_image,
			'product_edit_href' => $server . 'index.php?route=catalog/product/edit&' .$strtoken .'&product_id='.$product_info['product_id'],
			'product_download_href' => $download,
			'product_catalog_href' => $store_url . 'index.php?route=product/product&product_id='.$product_info['product_id'],
			'manufacturer_image_img' => $m_image,
			'manufacturer_edit_href' => $server . 'index.php?route=catalog/manufacturer/edit&'. $strtoken .'&manufacturer_id='.$product_info['manufacturer_id'],
			'manufacturer_catalog_href' => $store_url . 'index.php?route=product/manufacturer/info&manufacturer_id='.$product_info['manufacturer_id']
		);

		return array_merge($product_adding_data, $this->omproapicustom->productAddingData($order_info, $product_info, $template, $strtoken));
	}

	public function userData($user_id) {
		$result = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "user_group` ug WHERE ug.user_group_id = u.user_group_id) AS user_group FROM `" . DB_PREFIX . "user` u WHERE u.user_id = '" . (int)$user_id . "'")->row;

		$user_data = array();
		if ($result) {
			// ключи массива user_data - добавлять в userDataList
			$user_data = array(
				'user_group_id' => $result['user_group_id'],
				'user_login' => $result['username'],
				'user_firstname' => $result['firstname'],
				'user_lastname' => $result['lastname'],
				'user_email' => $result['email'],
				'user_telegram_id' => isset($result['telegram_id']) ? $result['telegram_id'] : '',
				'image' => $result['image']
			);
		}

		return array_merge($user_data, $this->omproapicustom->userData($user_id));
	}

	public function storeData($order_info = array()) {
		$store_data = array();

		if ($order_info) {
			$this->load->model('setting/setting');
			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
			$store_config_url = isset($store_info['config_url']) ? $store_info['config_url'] : $this->ompro->catalog;

			$store_config_meta_title = $store_owner = $store_address = $store_open = $store_comment = '';

			if ($store_info['config_logo'] && is_file(DIR_IMAGE . $store_info['config_logo'])) {
				$store_logo_url = $store_info['config_logo'];
			} else {
				$store_logo_url = '';
			}

			$input_config_keys = array('email', 'telephone', 'fax');
			foreach ($input_config_keys as $key) {
				if ($store_info['config_'.$key]) {
					${'store_'.$key} = $store_info['config_'.$key];
				} else {
					${'store_'.$key} = '';
				}
			}

			$textarea_config_keys = array('owner', 'address', 'open', 'comment');

			if (isset($store_info['config_langdata'])) {
				$langdata = $store_info['config_langdata'];
				$language_id = isset($order_info['language_id']) && $order_info['language_id'] ? $order_info['language_id'] : $this->config->get('config_language_id');
				$store_config_meta_title = isset($langdata[$language_id]['meta_title']) ? html_entity_decode($langdata[$language_id]['meta_title'], ENT_QUOTES, 'UTF-8') : '';
				foreach ($textarea_config_keys as $key) {
					${'store_'.$key} = trim(html_entity_decode(nl2br($langdata[$language_id][$key]), ENT_QUOTES, 'UTF-8'));
				}
			} else {
				foreach ($textarea_config_keys as $key) {
					if ($store_info['config_'.$key]) {
						${'store_'.$key} = trim(html_entity_decode(nl2br($store_info['config_'.$key]), ENT_QUOTES, 'UTF-8'));
					} else {
						${'store_'.$key} = '';
					}
				}
			}

			// эти же ключи - добавлять в storeDataList
			$all_store_data_keys = array('store_config_url', 'store_config_meta_title', 'store_logo_url', 'store_owner', 'store_address', 'store_open', 'store_comment', 'store_email', 'store_telephone', 'store_fax');

			foreach ($all_store_data_keys as $key) {
				$store_data[$key] = ${$key};
			}
		}

		return array_merge($store_data, $this->omproapicustom->storeData($order_info));
	}

	public function extendListWithCustom($arr, $method) {
		if (method_exists($this->omproapicustom, $method)) {
			return array_merge($arr, $this->omproapicustom->{$method}());
		}
	}

	///// расширяемые списки через extendListWithCustom ->

	// Список: API методы предварительной Обработки данных Заказа
	public function orderDataProcessMethodList() {
		$methods = array();
		$methods[] = array('process_method' => 'paymentAddressFormat', 'name' => 'Обработка поля: payment_address_format');
		$methods[] = array('process_method' => 'shippingAddressFormat', 'name' => 'Обработка поля: shipping_address_format');

		$methods[] = array('process_method' => 'accountCustomFieldTable', 'name' => 'Обработка поля: custom_field (вывод таблицы HTML)');
		$methods[] = array('process_method' => 'accountCustomFieldOlList', 'name' => 'Обработка поля: custom_field (вывод  списка OL (html))');
		$methods[] = array('process_method' => 'accountCustomFieldUlList', 'name' => 'Обработка поля: custom_field (вывод  списка UL (html))');

		$methods[] = array('process_method' => 'paymentCustomFieldTable', 'name' => 'Обработка поля: payment_custom_field (вывод таблицы HTML)');
		$methods[] = array('process_method' => 'paymentCustomFieldOlList', 'name' => 'Обработка поля: payment_custom_field (вывод списка OL (html))');
		$methods[] = array('process_method' => 'paymentCustomFieldUlList', 'name' => 'Обработка поля: payment_custom_field (вывод списка UL (html))');

		$methods[] = array('process_method' => 'shippingCustomFieldTable', 'name' => 'Обработка поля: shipping_custom_field (вывод таблицы HTML)');
		$methods[] = array('process_method' => 'shippingCustomFieldOlList', 'name' => 'Обработка поля: shipping_custom_field (вывод списка OL (html))');
		$methods[] = array('process_method' => 'shippingCustomFieldUlList', 'name' => 'Обработка поля: shipping_custom_field (вывод списка UL (html))');

		$methods[] = array('process_method' => 'nl2br', 'name' => 'nl2br (для textarea)');
		$methods[] = array('process_method' => 'clearTags', 'name' => 'Удаление html, script, style');
		$methods[] = array('process_method' => 'htmlEntityDecode', 'name' => 'Обработка html');
		$methods[] = array('process_method' => 'subStr30', 'name' => 'Обрезать строку до 30 знаков...');
		$methods[] = array('process_method' => 'subStr40', 'name' => 'Обрезать строку до 40 знаков...');
		$methods[] = array('process_method' => 'clearNotDigit', 'name' => 'Убрать все символы, кроме цифр');
		$methods[] = array('process_method' => 'quotDoubleToSingle', 'name' => 'Заменить кавычки: двойные - на одинарные');
		$methods[] = array('process_method' => 'quotDoubleToOblique', 'name' => 'Заменить кавычки: двойные - на косые');
		$methods[] = array('process_method' => 'quotAllToSingle', 'name' => 'Заменить кавычки: двойные и косые - на одинарные');
		$methods[] = array('process_method' => 'quotAllToOblique', 'name' => 'Заменить кавычки: двойные и одинарные - на косые');
		$methods[] = array('process_method' => 'quotAllRemove', 'name' => 'Удалить кавычки: двойные и одинарные');
		$methods[] = array('process_method' => 'floatValue', 'name' => 'Вывести как дробное число (float)');
		$methods[] = array('process_method' => 'roundValue', 'name' => 'Округлить до целого (round)');
		$methods[] = array('process_method' => 'roundValueDecimalOne', 'name' => 'Округлить до 1 знака после запятой)');
		$methods[] = array('process_method' => 'roundValueDecimalTwo', 'name' => 'Округлить до 2 знаков после запятой)');
		$methods[] = array('process_method' => 'oAsEmpty', 'name' => 'Если 0 выводить пустую строку');

		$methods[] = array('process_method' => 'convertToConfigCurrency', 'name' => 'Конвертировать в валюту по умолчанию');

		return $this->extendListWithCustom($methods, 'orderDataProcessMethodList');
	}

	// Список: API методы Формата данных Заказа
	public function orderDataFormatMethodList() {
		$methods = array();
		$methods[] = array('process_method' => 'dateFormatShort', 'name' => 'Формат даты d.m.Y (01.01.2020)');
		$methods[] = array('process_method' => 'dateFormatShortColor', 'name' => 'Формат даты d.m.Y (разный цвет для разницы 1,2,3 дня от сегодня)');
		$methods[] = array('process_method' => 'dateFormatMiddle', 'name' => 'Формат даты-времени d.m.Y H:i (01.01.2020 12:00)');
		$methods[] = array('process_method' => 'dateFormatLong', 'name' => 'Формат даты-времени d.m.Y H:i:s (01.01.2020 12:00:00)');
		$methods[] = array('process_method' => 'timeFormatShort', 'name' => 'Формат времени H.i (12:00)');
		$methods[] = array('process_method' => 'timeFormatLong', 'name' => 'Формат времени H.i (12:00:00)');
		$methods[] = array('process_method' => 'weightFormatConfig', 'name' => 'Формат веса в магазине (weight)');

		$methods[] = array('process_method' => 'colorRed', 'name' => 'Красный жирный текст');

		return $this->extendListWithCustom($methods, 'orderDataFormatMethodList');
	}

	// Список: API методы предварительной Обработки данных Товара
	public function productDataProcessMethodList() {
		$methods = array();
		$methods[] = array('process_method' => 'nl2br', 'name' => 'nl2br (для textarea)');
		$methods[] = array('process_method' => 'clearTags', 'name' => 'Удаление html, script, style');
		$methods[] = array('process_method' => 'htmlEntityDecode', 'name' => 'Обработка html');
		$methods[] = array('process_method' => 'subStr20', 'name' => 'Обрезать строку до 20 знаков...');
		$methods[] = array('process_method' => 'subStr30', 'name' => 'Обрезать строку до 30 знаков...');
		$methods[] = array('process_method' => 'subStr40', 'name' => 'Обрезать строку до 40 знаков...');
		$methods[] = array('process_method' => 'clearNotDigit', 'name' => 'Убрать все символы, кроме цифр');
		$methods[] = array('process_method' => 'quotDoubleToSingle', 'name' => 'Заменить кавычки: двойные - на одинарные');
		$methods[] = array('process_method' => 'quotDoubleToOblique', 'name' => 'Заменить кавычки: двойные - на косые');
		$methods[] = array('process_method' => 'quotAllToSingle', 'name' => 'Заменить кавычки: двойные и косые - на одинарные');
		$methods[] = array('process_method' => 'quotAllToOblique', 'name' => 'Заменить кавычки: двойные и одинарные - на косые');
		$methods[] = array('process_method' => 'quotAllRemove', 'name' => 'Удалить кавычки: двойные и одинарные');
		$methods[] = array('process_method' => 'floatValue', 'name' => 'Вывести как дробное число (float)');
		$methods[] = array('process_method' => 'roundValue', 'name' => 'Округлить до целого (round)');
		$methods[] = array('process_method' => 'roundValueDecimalOne', 'name' => 'Округлить до 1 знака после запятой)');
		$methods[] = array('process_method' => 'roundValueDecimalTwo', 'name' => 'Округлить до 2 знаков после запятой)');
		$methods[] = array('process_method' => 'oAsEmpty', 'name' => 'Если 0 выводить пустую строку');

		$methods[] = array('process_method' => 'convertToConfigCurrency', 'name' => 'Конвертировать в валюту по умолчанию');

		return $this->extendListWithCustom($methods, 'productDataProcessMethodList');
	}

	// Список: API методы формата данных Товара
	public function productDataFormatMethodList() {
		$methods = array();
		$methods[] = array('process_method' => 'dateFormatShort', 'name' => 'Формат даты d.m.Y (01.01.2020)');
		$methods[] = array('process_method' => 'dateFormatShortColor', 'name' => 'Формат даты d.m.Y (разный цвет для разницы 1,2,3 дня от сегодня)');
		$methods[] = array('process_method' => 'dateFormatMiddle', 'name' => 'Формат даты-времени d.m.Y H:i (01.01.2020 12:00)');
		$methods[] = array('process_method' => 'dateFormatLong', 'name' => 'Формат даты-времени d.m.Y H:i:s (01.01.2020 12:00:00)');
		$methods[] = array('process_method' => 'timeFormatShort', 'name' => 'Формат времени H.i (12:00)');
		$methods[] = array('process_method' => 'timeFormatLong', 'name' => 'Формат времени H.i (12:00:00)');
		$methods[] = array('process_method' => 'colorRed', 'name' => 'Красный жирный текст');

		return $this->extendListWithCustom($methods, 'productDataFormatMethodList');
	}

	//	Список: кастомные методы быстрого  редактирования
	public function xEditCustomApiMethodList() {
		$methods = array();
		$methods[] = array('key' => 'xEditLinkCustomOrderShippingCost', 'name' => 'Ред. стоимости доставки');
		$methods[] = array('key' => 'xEditLinkCustomOrderStatus', 'name' => 'ТЕСТ: Ред. статуса заказа (таблица: '.DB_PREFIX.'order)');  // не готов
		$methods[] = array('key' => 'xEditLinkOrderFieldsShippingMethod', 'name' => 'ТЕСТ: Ред. способа доставки (таблица: '.DB_PREFIX.'order)');  // не готов

		return $this->extendListWithCustom($methods, 'xEditCustomApiMethodList');
	}

	//	Список:  доп. действия после быстрого редактирования
	public function xEditActionApiMethodList() {
		$methods = array();
		$methods[] = array('action' => 'xEditActionSendNotifyTargetOrderManager', 'name' => 'Уведомить о назначении менеджером заказа');
		$methods[] = array('action' => 'xEditActionSendNotifyTargetOrderCourier', 'name' => 'Уведомить о назначении курьером заказа');
		$methods[] = array('action' => 'xEditActionResetShippingLatitudeLongitude', 'name' => 'Обнулить координаты заказа');

		$methods[] = array('action' => 'xEditActionUpdatePersonalDataCustomerGroup', 'name' => 'Обновить группу покупателя в личных данных (для поля customer_group_id)');

		return $this->extendListWithCustom($methods, 'xEditActionApiMethodList');
	}

	// Доп. данные Заказа: ключи массива orderAddingData
	public function orderAddingDataList() {
		$order_data_list = array();
		$keys = array('order_products_weight_total', 'order_products_weight_total_format', 'current_date', 'current_datetime');

		foreach ($keys as $key) {
			$order_data_list[] = array('data_group_id' => 'order_info', 'key' => $key, 'name' => $this->language->get('text_code_'.$key));
		}

		$order_data_list[] = array('data_group_id' => 'customer_info', 'key' => 'customer_edit_link', 'name' => 'Ссылка редактирования покупателя с именем и фамилией');

		return $this->extendListWithCustom($order_data_list, 'orderAddingDataList');
	}

	// Доп. данные Товара: ключи массива productAddingData
	public function productAddingDataList() {
		$product_data_list = array();
		$keys = array('product_image', 'product_edit_href', 'product_download_href', 'product_catalog_href', 'manufacturer_image_img', 'manufacturer_edit_href', 'manufacturer_catalog_href', 'style_from_discount', 'style_from_special');

		foreach ($keys as $key) {
			$product_data_list[] = array('data_group_id' => 'other_info', 'key' => $key, 'name' => $this->language->get('text_code_'.$key));
		}

		$product_data_list[] = array('data_group_id' => 'other_info', 'key' => 'op_price_with_discount', 'name' => 'Цена товара в заказе с учетом скидки');
		$product_data_list[] = array('data_group_id' => 'other_info', 'key' => 'op_total_with_discount', 'name' => 'Сумма товара в заказе с учетом скидки');
		$product_data_list[] = array('data_group_id' => 'other_info', 'key' => 'total_profit_with_discount', 'name' => 'Сумма прибыли за товар с учетом скидки');

		$product_data_list[] = array('data_group_id' => 'other_info', 'key' => 'special_offer_product_list', 'name' => 'Состав набора спецпредложения (список)');

		return $this->extendListWithCustom($product_data_list, 'productAddingDataList');
	}

	// Доп. данные Пользователя: ключи массива userData
	public function userDataList() {
		$user_data_list = array();
		$keys = array('user_login', 'user_firstname', 'user_lastname', 'user_email', 'user_group_id', 'user_telegram_id', 'image');
		foreach ($keys as $key) {
			$user_data_list[] = array('data_group_id' => 'user_info', 'key' => $key, 'name' => $this->language->get('text_code_'.$key));
		}
		return $this->extendListWithCustom($user_data_list, 'userDataList');
	}

	// Доп. данные Магазина: ключи массива storeData
	public function storeDataList() {
		$store_data_list = array();
		$keys = array('store_logo_url', 'store_owner', 'store_address', 'store_open', 'store_comment', 'store_email', 'store_telephone', 'store_fax');

		foreach ($keys as $key) {
			$store_data_list[] = array('data_group_id' => 'store_info', 'key' => $key, 'name' => $this->language->get('text_code_'.$key));
		}
		return $this->extendListWithCustom($store_data_list, 'storeDataList');
	}

	///// <- расширяемые списки

	// индесация
	// поля в таблицах для добавления индексов
	public function getTableIndexes() {
		// таблица => массив полей
		return array(
			'customer_group' => array('customer_group_id'),
			'customer_group_description' => array('customer_group_id'),
			'customer_reward' => array('customer_id', 'order_id'),
			'order' => array('order_id', 'manager_user_id', 'courier_user_id', 'customer_id', 'customer_group_id', 'payment_code', 'shipping_code', 'total', 'order_status_id', 'date_added', 'date_modified'),
			'order_option' => array('order_option_id', 'order_id', 'order_product_id', 'product_option_value_id'),
			'order_product' => array('order_product_id', 'order_id'),
			'order_status' => array('order_status_id'),
			'order_total' => array('order_id', 'code'),
			'order_history' => array('order_id', 'order_status_id'),
			'product' => array('product_id'),
			'product_option_value' => array('product_option_value_id', 'product_option_id', 'product_id')
		);
	}

	public function add_indexes() {
		$status = false;
		$tables_indexes = $this->getTableIndexes();

		// получаем все индексы в базе
		$query = $this->db->query("SELECT TABLE_NAME, COUNT(1) index_count, GROUP_CONCAT(DISTINCT(index_name) SEPARATOR ',') indexes FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = '". DB_DATABASE . "' AND INDEX_NAME != 'primary' GROUP BY TABLE_NAME ORDER BY COUNT(1) DESC");

		if ($query->num_rows) {
			foreach ($query->rows as $table_info) {
				$table_name = str_replace(DB_PREFIX, '', $table_info['TABLE_NAME']);
				if (isset($tables_indexes[$table_name])) {
					$indexes_2 = explode(',',$table_info['indexes']);
					foreach ($tables_indexes[$table_name] as $index_1) {
						if (!in_array($index_1, $indexes_2)) {
							$this->log->write("Table '$table_name' ADD INDEX '$index_1'");
							$query = $this->db->query("ALTER TABLE `". DB_PREFIX . $table_name ."` ADD INDEX (`".$index_1."`) ");
							$status = true;
						}
					}
					unset($tables_indexes[$table_name]);
				}
			}
		}

		if (!empty($tables_indexes)) {
			$status = true;
			foreach ($tables_indexes as $table => $indexes) {
				foreach ($indexes as $index) {
					$this->log->write("Table '$table' ADD INDEX '$index'");
					$query = $this->db->query("ALTER TABLE `". DB_PREFIX . $table ."` ADD INDEX (`".$index."`) ");
				}
			}
		}

		return $status;
	}
	// <- индесация


	// bundle_expert

	public function orderInfoProductListPrepare($products, $expert_link = false) {
		$this->sortArray($products, 'order_product_id');

		switch ($this->bundle_expert_settings['order_info_format']) {
			case 'one_item':
				$new_products_list = $this->orderEmail_Format_2($products, $expert_link);
				break;

			case 'list':
				$new_products_list = $this->orderEmail_Format_1($products);
				break;

			default:
				$new_products_list = $products;
				break;
		}
		return $new_products_list;
	}

	public function orderEmailProductListPrepare($products, $expert_link = false) {
		$this->sortArray($products, 'order_product_id');

		switch ($this->bundle_expert_settings['order_info_format']) {
			case 'one_item':
				$new_products_list = $this->orderEmail_Format_2($products, $expert_link);
				break;

			case 'list':
				$new_products_list = $this->orderEmail_Format_1($products);
				break;

			default:
				$new_products_list = $products;
				break;
		}

		return $new_products_list;
	}

	private function orderEmail_Format_1($products) {
		$new_products_list = array();
		$product_as_kit_formatted = array();

		foreach ($products as $index => $product) {
			$order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);
			if (!empty($order_product_kit_info)) {
				if (isset($order_product_kit_info['product_as_kit_info'])) {
					$product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];
					$product['name'] = '<b style="font-size: 110%">' . $product['name'] . '</b>';
					if (isset($product['price'])) $product['price'] = '<b style="font-size: 110%">' . $product['price'] . '</b>';
					if (isset($product['total'])) $product['total'] = '<b style="font-size: 110%">' . $product['total'] . '</b>';
					$product['model'] = '<b style="font-size: 110%">' . $product['model'] . '</b>';
					$product['quantity_value'] = $product['quantity'];
					$product['quantity'] = '<b style="font-size: 110%">' . $product['quantity'] . '</b>';
					$product['product_as_kit'] = true;
					$new_products_list[$product_as_kit_unique_id] = $product;
					$product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];
					$product_as_kit_formatted[$product_as_kit_unique_id] = array();
				} else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {
					$product['total'] = '';

					$has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];
					$item_position = $order_product_kit_info['cart_kit_info']['item_position'];

					if (isset($product_as_kit_formatted[$has_product_as_kit_unique_id])) {
						if (!isset($product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']])) {
							$product['quantity'] = $product['quantity'] / $new_products_list[$has_product_as_kit_unique_id]['quantity_value'];
							$new_products_list[] = $product;
							$product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']] = true;
						}
					} else {
						$new_products_list[] = $product;
					}
				} else {
					$new_products_list[] = $product;
				}
			} else {
				$new_products_list[] = $product;
			}
		}
		foreach ($new_products_list as &$product) {
			if (isset($product['product_as_kit'])) {
				$product['weight'] = $this->orderCalculateProductAsKitWeight($product['order_product_id'], true);
			}
		}

		return $new_products_list;
	}

	private function orderEmail_Format_2($products, $expert_link) {
		$new_products_list = array();
		$product_as_kit_formatted = array();

		foreach ($products as $index => $product) {
			$order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);

			if (!empty($order_product_kit_info)) {
				if (isset($order_product_kit_info['product_as_kit_info'])) {
					$product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];
					$product['product_as_kit'] = true;
					$product_as_kit_formatted[$product_as_kit_unique_id] = array();
					$new_products_list[$product_as_kit_unique_id] = $product;
				} else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {
					$has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];
					$item_position = $order_product_kit_info['cart_kit_info']['item_position'];
					if (isset($product_as_kit_formatted[$has_product_as_kit_unique_id])) {
						if (!isset($product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']])) {
							$product['op_quantity'] = $product['op_quantity'] / $new_products_list[$has_product_as_kit_unique_id]['op_quantity'];
							$new_products_list[$has_product_as_kit_unique_id]['option'][] = array(
								'name'  => $expert_link ? '<a href=" ' . $this->ompro->catalog . 'index.php?route=product/product&product_id=' . $product['product_id'] . ' " target="_blank"><b>' . $product['op_name'] . '</b></a>' : $product['op_name'],
								'type'  => '',
								'product_source_product_id' => $product['product_id'],
								'product_source_order_product_id' => $product['order_product_id'],
								'value' => $product['op_quantity'] . ' x ' . $this->currency->format($product['price'], $this->config->get('config_currency'))
							);

							if ($this->bundle_expert->bundle_expert_settings['cart_show_option_in_product_as_kit']) {
								foreach ($product['option'] as $option) {
									$new_products_list[$has_product_as_kit_unique_id]['option'][] = array(
										'name'  => $product['op_name'], // href ???
										'value' => $option['value'],
										'product_source_product_id' => $option['product_source_product_id'],
										'product_source__orderproduct_id' => $option['product_source_order_product_id'],
										'type' => '',
									);
								}
							}

							$product_as_kit_formatted[$has_product_as_kit_unique_id][$item_position][$product['product_id']] = true;
						}
					} else {
						$new_products_list[] = $product;
					}
				} else {
					$new_products_list[] = $product;
				}
			} else {
				$new_products_list[] = $product;
			}
		}

		foreach ($new_products_list as &$product) {
			if (isset($product['product_as_kit'])) {
				$product['weight'] = $this->orderCalculateProductAsKitWeight($product['order_product_id'], true);
			}
		}

		return $new_products_list;
	}

	public function orderCalculateProductAsKitWeight($order_product_id, $format = false)	{
		$this->load->model('catalog/product');
		$weight = 0;
		$product_as_kit_unique_id = '';
		$product_as_kit_info = array();
		$product_as_kit_quantity = 0;

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_product_id = '" . (int)$order_product_id . "'");

		if ($query->rows) {
			$order_id = $query->row['order_id'];
			$products = $this->getOrderProducts($order_id);
			$this->sortArray($products, 'order_product_id');

			foreach ($products as $product) {
				$option_weight = 0;
				$product_info = $this->getProductInfoDefault($product['product_id']);
				$order_product_kit_info = $this->getOrderProductAsKitData($product['order_product_id']);

				if (isset($order_product_kit_info['product_as_kit_info'])) {
					if ($product['order_product_id'] == $order_product_id) {
						$product_as_kit_info = $product_info;
						$product_as_kit_quantity = $product['quantity'];
						$product_as_kit_unique_id = $order_product_kit_info['product_as_kit_info']['product_as_kit_unique_id'];
						$product_weight = $product_info['weight'];
						$options = $this->getOrderOptions($order_id, $product['order_product_id']);

						foreach ($options as $option) {
							$product_option_value_info = $this->getProductOptionValue($product['product_id'], $option['product_option_value_id']);

							if ($product_option_value_info) {
								if ($product_option_value_info['weight_prefix'] == '+') {
									$option_weight += $product_option_value_info['weight'];
								} elseif ($product_option_value_info['weight_prefix'] == '-') {
									$option_weight -= $product_option_value_info['weight'];
								}
							}
						}

						$product_weight += $option_weight;

						$product_weight = $this->weight->convert($product_weight, $product_info['weight_class_id'], $this->config->get('config_weight_class_id'));

						$weight += $product_weight;
					}
				} else if (!empty($order_product_kit_info['cart_kit_info']) && !empty($order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'])) {
					$has_product_as_kit_unique_id = $order_product_kit_info['cart_kit_info']['has_product_as_kit_unique_id'];

					if ($has_product_as_kit_unique_id == $product_as_kit_unique_id) {
						$product_weight = $product_info['weight'];
						$options = $this->getOrderOptions($order_id, $product['order_product_id']);

						foreach ($options as $option) {
							$product_option_value_info = $this->getProductOptionValue($product['product_id'], $option['product_option_value_id']);

							if ($product_option_value_info) {
								if ($product_option_value_info['weight_prefix'] == '+') {
									$option_weight += $product_option_value_info['weight'];
								} elseif ($product_option_value_info['weight_prefix'] == '-') {
									$option_weight -= $product_option_value_info['weight'];
								}
							}
						}

						$product_weight += $option_weight;

						$product_weight = $this->weight->convert($product_weight, $product_info['weight_class_id'], $this->config->get('config_weight_class_id'));

						$weight += $product_weight * $product['quantity'];
					}
				}
			}
		}

		$weight = $weight * $product_as_kit_quantity;

		if ($product_as_kit_info) {
			$weight = $this->weight->convert($weight, $this->config->get('config_weight_class_id'), $product_as_kit_info['weight_class_id']);
		}

		if ($format) {
			$weight = $this->weight->format(
				$weight,
				$product_as_kit_info['weight_class_id'],
				$this->language->get('decimal_point'),
				$this->language->get('thousand_point')
			);
		}

		return $weight;
	}

	public function getProductInfoDefault($product_id) {
		$query = $this->db->query(
			"SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'"
		);

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'stock_status_id'     => $query->row['stock_status_id'],
				'stock_status_result'     => ($query->row['quantity'] <= 0) ? $query->row['stock_status'] : $this->language->get('text_instock'),
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'weight_text'      => $this->weight->format(
					$query->row['weight'],
					$this->config->get('config_weight_class_id'),
					$this->language->get('decimal_point'),
					$this->language->get('thousand_point')
				),
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'length_class'     => $query->row['length_class'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}
	}

	public function getOrderProducts($order_id)	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query(
			"SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'"
		);

		return $query->rows;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query(
			"SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'"
		);

		return $query->row;
	}

	public function getOrderProductAsKitData($order_product_id) {
		$data = array();

		if (!$this->isEnabled()) return array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "bundle_expert_order WHERE order_product_id = '" . (int)$order_product_id . "'");

		if ($query->row) {
			$data['cart_kit_info'] = json_decode($query->row['cart_kit_info'], true);
			$data['product_as_kit_info'] = json_decode($query->row['product_as_kit_info'], true);
		} else {
			$data = array();
		}
		return $data;
	}

	public function isEnabled()	{
		if ($this->isAdminMode()) {
			if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
				$module_status_name = "bundle_expert_status";
			} else {
				$module_status_name = "module_bundle_expert_status";
			}

			if ($this->config->get($module_status_name)) $check = true;
			else $check = false;
		} else {
			if ($this->config->get('bundle_expert_status_for_customer')) $check = true;
			else $check = false;
			if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
				$module_status_name = "bundle_expert_status";
			} else {
				$module_status_name = "module_bundle_expert_status";
			}

			if (!$this->config->get($module_status_name)) $check = false;
		}

		return $check;
	}

	private function isAdminMode() {
		$is_admin = false;
		if (isset($this->request->get['token']) && !empty($this->request->get['token'])) {
			$is_admin = true;
		}
		if (isset($this->request->get['user_token']) && !empty($this->request->get['user_token'])) {
			$is_admin = true;
		}
		if (isset($this->request->get['route']) && strpos($this->request->get['route'], 'api/') === 0) {
			$is_admin = true;
		}
		return $is_admin;
	}

	private function build_sorter($key) {
		return function ($a, $b) use ($key) {
			return ($a[$key] < $b[$key]) ? -1 : 1;
		};
	}

	public function sortArray(&$data, $key) {
		if (!empty($data)) {
			usort($data, $this->build_sorter($key));
		}
	}

	// <- bundle_expert

	public function microtime() {
		return microtime(true);
	}

	public function diffrent($start, $end) {
		return $end-$start;
	}
}