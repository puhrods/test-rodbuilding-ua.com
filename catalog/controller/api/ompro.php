<?php
class ControllerApiOMPro extends Controller {
	public function history() {
		$this->load->language('api/ompro');
		$json = $error_order_not_found = $success_change_status = array();

		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}

		$order_status_id = 0;
		if (isset($this->request->post['order_status_id'])) {
			$order_status_id = $this->request->post['order_status_id'];
		}

		if (isset($this->request->post['selected'])) {
			$comment = '';
			if (isset($this->request->post['comment'])) {
				$comment = urldecode($this->request->post['comment']);
			}

			$this->request->post['ompro_history'] = true;
			$this->load->model('checkout/order');

			unset($this->session->data['ompro_data']);

			foreach (array('mail', 'sms', 'tlgrm') as $notify) {
				unset($this->session->data[$notify.'_history_template_info']);
			}

			if ($this->ompro->ocversion < 300) {
				$this->ompro->requestNotifyTarget();
			}

			foreach ($this->request->post['selected'] as $order_id) {
				$result = $this->model_checkout_order->addOrderHistory($order_id, $order_status_id, $comment, $this->request->post['notify_mail'], $this->request->post['override']);

				if ($result === false) {
					$error_order_not_found[] = $order_id;
				} else {
					$success_change_status[] = $order_id;
				}
			}
		}

		$result_error = '';
		if ($error_order_not_found) {
			$result_error .= '<div>' . $this->language->get('error_not_found') . implode(', ', $error_order_not_found) . '</div>';
		}

		$json['error'] = $result_error;

		$result_success = '';
		if ($success_change_status) {
			$result_success .= '<div>' . $this->language->get('text_success') . implode(', ', $success_change_status) . '</div>';
		}

		$json['success'] = $result_success;

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// model/checkout/order/addOrderHistory/before
	public function notifyTarget() {
		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}

		$this->ompro->requestNotifyTarget();
	}

	// model/checkout/order/addOrderHistory/after
	public function notify(&$route, &$args) {
		if (isset($args[0])) { $order_id = $args[0]; } else { $order_id = 0; }
		if (isset($args[1])) { $order_status_id = $args[1]; } else { $order_status_id = 0; }
		if (isset($args[2])) { $comment = $args[2]; } else { $comment = ''; }
		if (isset($args[3])) { $notify = $args[3]; } else { $notify = ''; }

		$ompro_data = array();
		if (isset($this->session->data['ompro_data'])) {
			$ompro_data = $this->session->data['ompro_data'];
		}

		// mail_target sms_target tlgrm_target user_id attachments notify_sms notify_tlgrm
		foreach ($ompro_data as $k => $v) { ${$k} = $v; }

		$old_order_status_id = 0;
		if (isset($this->session->data['ompro_old_order_status_id'])) {
			$old_order_status_id = $this->session->data['ompro_old_order_status_id'];
		}

		$all_orders_data = $this->ompro->getOrdersDataAll(array($order_id), $to_edit = false, $expert_link = false);

		if (!empty($all_orders_data) && !empty($all_orders_data['orders']) && !empty($all_orders_data['orders'][$order_id])) {
			$order_info = $all_orders_data['orders'][$order_id]['order_info'];
			$order_language_id = isset($order_info['language_id']) ? $order_info['language_id'] : $this->config->get('config_language_id');
			$language_id = $this->config->get('config_language_id');

			// If order status is 0 then becomes greater than 0 send main html email
			// НОВЫЙ ЗАКАЗ

			if (!$old_order_status_id && $order_status_id) {
				if ($mail_target['new_order_customer']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('mail', $mail_target['new_order_customer']);
					$recipients = array();
					$recipients[] = array(
						'recipient_name' => '', 'email' => $order_info['email']
					);
					$this->ompro->sendMail($order_id, $template_info, $order_language_id, $recipients, $all_orders_data, $comment2, $attachments);
					$system_customer_mail = false;
				}

				if ($sms_target['new_order_customer']) {
					$template_info = $this->ompro->getTemplateTemplate('sms', $sms_target['new_order_customer']);
					$to = $order_info['telephone'];
					$copies = array();
					$this->ompro->sendSms($order_id, $template_info, $order_language_id, $to, $copies, $all_orders_data, $comment);
				}

				if ($tlgrm_target['new_order_customer']) {
					$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_target['new_order_customer']);
					$chat_ids = array();
					if (isset($order_info['telegram_id']) && $order_info['telegram_id']) {
						$chat_ids[] = $order_info['telegram_id'];
						$this->ompro->sendToTelegram($order_id, $template_info,  $chat_ids, $all_orders_data, $comment);
					}
				}

				if ($mail_target['new_order_admin']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('mail', $mail_target['new_order_admin']);
					$emails = explode(',', $this->config->get('config_alert_email'));
					$emails[] = $this->config->get('config_email');
					$recipients = array();
					foreach ($emails as $email) {
						$email = trim($email);
						if ($email !== '' && preg_match('/^[^@]+@.*.[a-z]{2,15}$/i', $email)) {
							$recipients[] = array(
								'recipient_name' => '', 'email' => $email
							);
						}
					}
					$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data, $comment2, $attachments = array());
					$system_admin_mail = false;
				}

				if ($sms_target['new_order_admin']) {
					$template_info = $this->ompro->getTemplateTemplate('sms', $sms_target['new_order_admin']);
					$to = $this->config->get('config_sms_to');
					$copies = $this->config->get('config_sms_copy');
					$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data, $comment);
					$system_admin_sms = false;
				}

				if ($tlgrm_target['new_order_admin']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_target['new_order_admin']);
					$chat_ids = array();
					$chat_ids = explode(',', $this->ompro->getTelegramAdminIdes());
					if (!empty($chat_ids)) {
						$this->ompro->sendToTelegram($order_id, $template_info,  $chat_ids, $all_orders_data, $comment2);
					}
				}

				$managers = array();
				if ($mail_target['new_order_manager'] || $sms_target['new_order_manager'] || $tlgrm_target['new_order_manager']) {
					$managers = $this->ompro->getTargetUsers($order_id, $all_orders_data, 'manager');
				}

				if ($mail_target['new_order_manager']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('mail', $mail_target['new_order_manager']);
					$recipients = array();
					foreach ($managers as $manager) {
						$recipients[] = array(
							'recipient_name' => $manager['firstname'] .' '. $manager['lastname'], 'email' => $manager['email']
						);
					}

					$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data, $comment2, $attachments);
				}

				if ($sms_target['new_order_manager']) {
					$template_info = $this->ompro->getTemplateTemplate('sms', $sms_target['new_order_manager']);
					$to = ''; $copies = array();
					foreach ($managers as $manager) {
						if (isset($manager['telephone']) && $manager['telephone'] !== '') {
							if ($to == '') {
								$to = $manager['telephone'];
							} else {
								$copies[] = $manager['telephone'];
							}
						}
					}

					$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data, $comment);
				}

				if ($tlgrm_target['new_order_manager']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_target['new_order_manager']);
					$chat_ids = array();
					foreach ($managers as $manager) {
						if (isset($manager['telegram_id']) && $manager['telegram_id'] !== '') {
							$chat_ids[] = $manager['telegram_id'];
						}
					}
					if (!empty($chat_ids)) {
						$this->ompro->sendToTelegram($order_id, $template_info,  $chat_ids, $all_orders_data, $comment2);
					}
				}

				$couriers = array();
				if ($mail_target['new_order_courier'] || $sms_target['new_order_courier'] || $tlgrm_target['new_order_courier']) {
					$couriers = $this->ompro->getTargetUsers($order_id, $all_orders_data, 'courier');
				}

				if ($mail_target['new_order_courier']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('mail', $mail_target['new_order_courier']);
					$recipients = array();
					foreach ($couriers as $courier) {
						$recipients[] = array(
							'recipient_name' => $courier['firstname'] .' '. $courier['lastname'], 'email' => $courier['email']
						);
					}

					$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data, $comment2, $attachments);
				}

				if ($sms_target['new_order_courier']) {
					$template_info = $this->ompro->getTemplateTemplate('sms', $sms_target['new_order_courier']);
					$to = ''; $copies = array();
					foreach ($couriers as $courier) {
						if (isset($courier['telephone']) && $courier['telephone'] !== '') {
							if ($to == '') {
								$to = $courier['telephone'];
							} else {
								$copies[] = $courier['telephone'];
							}
						}
					}

					$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data, $comment);
				}

				if ($tlgrm_target['new_order_courier']) {
					$comment2 = nl2br($comment);
					$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_target['new_order_courier']);
					$chat_ids = array();
					foreach ($couriers as $courier) {
						if (isset($courier['telegram_id']) && $courier['telegram_id'] !== '') {
							$chat_ids[] = $courier['telegram_id'];
						}
					}
					if (!empty($chat_ids)) {
						$this->ompro->sendToTelegram($order_id, $template_info,  $chat_ids, $all_orders_data, $comment2);
					}
				}
			}


			// If order status is not 0 then send update text email
			// ОБНОВЛЕНИЕ СТАТУСА

			if ($old_order_status_id && $order_status_id && ($notify && $mail_target['history']) || ($notify_sms && $sms_target['history']) || ($notify_tlgrm && $tlgrm_target['history'])) {
				if ($notify && $mail_target['history']) {
					if (!isset($this->session->data['mail_history_template_info'])) {
						$this->session->data['mail_history_template_info'] = $this->ompro->getTemplateTemplate('mail', $mail_target['history']);
					}
					$recipients = array();
					$recipients[] = array(
						'recipient_name' => '', 'email' => $order_info['email']
					);
					$this->ompro->sendMail($order_id, $this->session->data['mail_history_template_info'], $order_language_id, $recipients, $all_orders_data, $comment, $attachments);
					$system_status_mail = false;
				}

				if ($notify_sms && $sms_target['history']) {
					if (!isset($this->session->data['sms_history_template_info'])) {
						$this->session->data['sms_history_template_info'] = $this->ompro->getTemplateTemplate('sms', $sms_target['history']);
					}
					$to = $order_info['telephone'];
					$copies = array();
					$this->ompro->sendSms($order_id, $this->session->data['sms_history_template_info'], $order_language_id, $to, $copies, $all_orders_data, $comment);
				}

				if ($notify_tlgrm && $tlgrm_target['history']) {
					if (!isset($this->session->data['tlgrm_history_template_info'])) {
						$this->session->data['tlgrm_history_template_info'] = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_target['history']);
					}
					$chat_ids = array();
					if (isset($order_info['telegram_id']) && $order_info['telegram_id']) {
						$chat_ids[] = $order_info['telegram_id'];
						$this->ompro->sendToTelegram($order_id, $this->session->data['tlgrm_history_template_info'],  $chat_ids, $all_orders_data, $comment);
					}
				}
			}
		} else {
			return false;
		}
	}

	public function getSimpleApiCustom($method = '', $filter = '') {
		$this->load->model('tool/simpleapicustom');
		$json = array();

		if (!$method) {
			$method = isset($this->request->get['method']) ? trim($this->request->get['method']) : '';
		}

		if (!$filter) {
			$filter = isset($this->request->get['filter']) ? trim($this->request->get['filter']) : '';
		}

        if (!$method) {  exit; } else {
			$json = $this->model_tool_simpleapicustom->{$method}($filter);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function download() {
		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}

		$upload_info = array();

		if (isset($this->request->get['code'])) {
			$upload_info = $this->ompro->getUploadByCode($this->request->get['code']);
		} elseif (isset($this->request->get['filename'])) {
			$upload_info = $this->ompro->getUploadByFilename($this->request->get['filename']);
		} elseif (isset($this->request->get['history_file_name'])) {
			$upload_info['filename'] = $this->request->get['history_file_name'];
			$upload_info['name'] = $this->request->get['history_file_name'];
		}

		if ($upload_info) {
			if (isset($this->request->get['history_file_name'])) {
				$file = DIR_DOWNLOAD . $upload_info['filename'];
			} else {
				$file = DIR_UPLOAD . $upload_info['filename'];
			}

			$mask = basename($upload_info['name']);

			if (!headers_sent()) {
				if (is_file($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			exit('Error: Could not find file upload information!');
		}
	}
}