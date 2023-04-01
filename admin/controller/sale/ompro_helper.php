<?php
class ControllerSaleOmproHelper extends Controller {

	public function init() {
		if (!is_object($this->ompro)) {
			$this->load->library('ompro/ompro');
		}
		$this->load->language('sale/ompro');
	}

	/* addIndexes */

	public function add_indexes() {
		$this->init();

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
		} else {
			$status = $this->omproapi->add_indexes();

			if ($status) {
				$this->session->data['success'] = 'Индексация прошла успешно!';
			} else {
				$this->session->data['error_warning'] = 'Нет новых индексов для добавления!';
			}
		}

		$this->response->redirect($this->url->link('sale/ompro/admin', $this->strToken(), true));
	}

	public function backup($tables) {
		$output = '';

		foreach ($tables as $table) {
			if (DB_PREFIX) {
				if (strpos($table, DB_PREFIX) === false) {
					$status = false;
				} else {
					$status = true;
				}
			} else {
				$status = true;
			}

			if ($status) {
				$output .= 'TRUNCATE TABLE `' . $table . '`;' . "\n\n";

				$query = $this->db->query("SELECT * FROM `" . $table . "`");

				foreach ($query->rows as $result) {
					$fields = '';

					foreach (array_keys($result) as $value) {
						$fields .= '`' . $value . '`, ';
					}

					$values = '';

					foreach (array_values($result) as $value) {
						$value = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $value);
						$value = str_replace(array("\n", "\r", "\t"), array('\n', '\r', '\t'), $value);
						$value = str_replace('\\', '\\\\',	$value);
						$value = str_replace('\'', '\\\'',	$value);
						$value = str_replace('\\\n', '\n',	$value);
						$value = str_replace('\\\r', '\r',	$value);
						$value = str_replace('\\\t', '\t',	$value);

						$values .= '\'' . $value . '\', ';
					}

					$output .= 'INSERT INTO `' . $table . '` (' . preg_replace('/, $/', '', $fields) . ') VALUES (' . preg_replace('/, $/', '', $values) . ');' . "\n";
				}

				$output .= "\n\n";
			}
		}

		return $output;
	}

    public function allSettingBackup() {
		$this->init();

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link('sale/ompro/admin', $this->strToken(), true));
		} else {
			if (isset($this->request->post['backupall'])) {
				$this->response->addheader('Pragma: public');
				$this->response->addheader('Expires: 0');
				$this->response->addheader('Content-Description: File Transfer');
				$this->response->addheader('Content-Type: application/octet-stream');
				$this->response->addheader('Content-Disposition: attachment; filename="ompro_settings_' . DB_DATABASE . '_' . date('Y-m-d_H-i-s', time()) . '_backup.sql"');
				$this->response->addheader('Content-Transfer-Encoding: binary');
				$this->response->setOutput($this->backup($this->request->post['backupall']));
			} else {
				$this->session->data['error_warning'] = $this->language->get('error_export');
				$this->response->redirect($this->url->link('sale/ompro/admin', $this->strToken(), true));
			}
		}
    }

	public function restore($sql) {
		foreach (explode(";\n", $sql) as $sql) {
			$sql = trim($sql);

			if ($sql) {
				$this->db->query($sql);
			}
		}

		$this->cache->delete('*');
	}

    public function allSettingRestore() {
		$this->init();

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link('sale/ompro/admin', $this->strToken(), true));
		} else {
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				if (isset($this->request->files['importall']) && is_uploaded_file($this->request->files['importall']['tmp_name'])) {
					$content = file_get_contents($this->request->files['importall']['tmp_name']);
				} else {
					$content = false;
				}

				if ($content) {
					$this->restore($content);
					$this->session->data['success'] = $this->language->get('text_restore_success');
					$this->response->redirect($this->url->link('sale/ompro/admin', $this->strToken(), true));
				} else {
					$this->error['warning'] = $this->language->get('error_empty');
				}
			}
		}
    }

    public function settingGroupBackup() {
		if (isset($this->request->get['user_group_id']) && isset($this->request->get['datatype'])) {
			$this->init();
			$user_group_id = $this->request->get['user_group_id'];
			if (!$this->user->hasPermission('modify', 'sale/ompro')) {
				$this->session->data['error_warning'] = $this->language->get('error_permission');
				$this->response->redirect($this->url->link('sale/ompro/settingGroup', $this->strToken() . '&user_group_id=' . $user_group_id, true));
			} else {
				$user_group_id = $this->request->get['user_group_id'];
				$datatype = $this->request->get['datatype'];

				$settings = $this->ompro->getSettingGroup($user_group_id);
				$settings['datatype'] = $datatype;

				$this->response->addheader('Pragma: public');
				$this->response->addheader('Expires: 0');
				$this->response->addheader('Content-Description: File Transfer');
				$this->response->addheader('Content-Type: application/octet-stream');
				$this->response->addheader('Content-Disposition: attachment; filename=' . 'ompro_' .$datatype . '_' . $user_group_id . '_' . date('Y-m-d_H-i-s') . '.settings');
				$this->response->addheader('Content-Transfer-Encoding: binary');
				$this->response->setOutput(serialize($settings));
			}
		}
    }

    public function initSettingBackup() {
		$this->init();
		if (isset($this->request->get['user_group_id'])) {
			$user_group_id = $this->request->get['user_group_id'];
		} else {
			$user_group_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$this->load->language('sale/ompro');
			$this->session->data['error_warning'] = $this->language->get('error_permission');
			$this->response->redirect($this->url->link('sale/ompro/settingGroup', $this->strToken() . '&user_group_id=' . $user_group_id, true));
		} else {
			if ($user_group_id && isset($this->request->get['datatype'])) {
				$this->init();
				$settings = array();
				$settings['datatype'] = $datatype = $this->request->get['datatype'];
				$settings['admin_setting'] = $this->ompro->getAdminSetting();

				$fields_setting = $this->ompro->getAllFields();

				$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "order`");
				$order_fields =  array();
				foreach ($query->rows as $result) {
					$order_fields[$result['Field']] = $result;
				}

				// добавить к настройкам полей параметры полей из БД
				foreach ($fields_setting['order_fields'] as $id => $field_set) {
					if (isset($order_fields[$field_set['key']])) {
						$fields_setting['order_fields'][$id]['dbParam'] = $order_fields[$field_set['key']];
					}
				}

				$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "product`");
				$product_fields =  array();
				foreach ($query->rows as $result) {
					$product_fields[$result['Field']] = $result;
				}

				$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "order_product`");
				$order_product_fields =  array();
				foreach ($query->rows as $result) {
					$order_product_fields[$result['Field']] = $result;
				}

				$manufacturer_fields =  array();
				$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "manufacturer`");
				foreach ($query->rows as $result) {
					$manufacturer_fields[$result['Field']] = $result;
				}

				$op_alias_list = array('op_name', 'op_quantity', 'op_price', 'op_total', 'op_tax', 'op_reward');

				foreach ($fields_setting['product_fields'] as $id => $field_set) {
					if ($field_set['dbTable'] == 'order_product' && in_array($field_set['key'], $op_alias_list)) {
						$field_key = preg_replace("/^op_/", '', $field_set['key']);
					} elseif ($field_set['dbTable'] == 'manufacturer' && ($field_set['key'] == 'm_name' || $field_set['key'] == 'm_image')) {
						$field_key = preg_replace("/^m_/", '', $field_set['key']);
					} else {
						$field_key = $field_set['key'];
					}

					if (isset($product_fields[$field_key])) {
						$fields_setting['product_fields'][$id]['dbParam'] = $product_fields[$field_key];
					} elseif (isset($order_product_fields[$field_key])) {
						$fields_setting['product_fields'][$id]['dbParam'] = $order_product_fields[$field_key];
					} elseif (isset($manufacturer_fields[$field_key])) {
						$fields_setting['product_fields'][$id]['dbParam'] = $manufacturer_fields[$field_key];
					}
				}

				$settings['fields_setting'] = $fields_setting;
				$settings['group_setting'] = $this->ompro->getSettingGroup($user_group_id);

				$template_list = array('block', 'comment', 'excel_orders', 'excel_orders_products', 'filter', 'filter_product', 'history',  'mail', 'option', 'orders', 'pages', 'page_block', 'print_orders', 'print_orders_table', 'print_products_table', 'product', 'sms', 'tlgrm');

				$settings['templates'] = array();
				foreach ($template_list as $type) {
					$settings['templates'][$type] = $this->ompro->getTemplatesAll($type);
				}

				$this->response->addheader('Pragma: public');
				$this->response->addheader('Expires: 0');
				$this->response->addheader('Content-Description: File Transfer');
				$this->response->addheader('Content-Type: application/octet-stream');
				$this->response->addheader('Content-Disposition: attachment; filename=' . 'ompro_' . DB_DATABASE . '_' . $datatype . '_' . $user_group_id . '_' . date('Y-m-d_H-i-s') . '.settings');
				$this->response->addheader('Content-Transfer-Encoding: binary');
				$this->response->setOutput(base64_encode(serialize($settings)));
			}
		}
    }

    public function initSettingRestore() {
		$this->init();

		if (isset($this->request->get['user_group_id'])) {
			$user_group_id = $this->request->get['user_group_id'];
		} else {
			$user_group_id = $this->user->getGroupId();
		}

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$this->session->data['error_warning'] = $this->language->get('error_permission');
		} else {
			if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->files['importall']) && is_uploaded_file($this->request->files['importall']['tmp_name'])) {

				$content = base64_decode(file_get_contents($this->request->files['importall']['tmp_name']));
				if ($content && preg_match('/(a|O|s|b)\:[0-9]*?((\:((\{?(.+)\})|(\"(.+)\"\;)))|(\;))/', $content)) {
					$content = unserialize($content);
				}

				if (is_array($content) && isset($content['datatype']) && $content['datatype'] == 'init_setting') {
					unset($content['datatype']);
					$settings = $content;

					// настройки полей
					if (isset($this->request->get['restore_fields_setting_all']) || isset($this->request->get['restore_fields_setting_new']) && !empty($settings['fields_setting'])) {

						$init_fields_setting = $this->ompro->getAllFields();
						$fields_page_list = array('order_fields','order_as_fields','product_fields','product_as_fields');
						$op_alias_list = array('op_name', 'op_quantity', 'op_price', 'op_total', 'op_tax', 'op_reward');

						// добавляем новые поля в БД с параметрами из импортируемых  настроек
						foreach (array('order_fields','product_fields') as $type) {
							if (isset($settings['fields_setting'][$type])) {
								$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "order`");

								$order_fields =  array();
								foreach ($query->rows as $result) {
									$order_fields[] = $result['Field'];
								}

								$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "product`");
								$product_fields =  array();
								foreach ($query->rows as $result) {
									$product_fields[] = $result['Field'];
								}

								$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "order_product`");
								$order_product_fields =  array();
								foreach ($query->rows as $result) {
									$order_product_fields[] = $result['Field'];
								}

								$manufacturer_fields =  array();
								$query = $this->db->query("DESCRIBE `" . DB_PREFIX . "manufacturer`");
								foreach ($query->rows as $result) {
									$manufacturer_fields[] = $result['Field'];
								}

								foreach ($settings['fields_setting'][$type] as $field_set) {
									if ($field_set['dbTable'] == 'order_product' && in_array($field_set['key'], $op_alias_list)) {
										$field_key = preg_replace("/^op_/", '', $field_set['key']);
									} elseif ($field_set['dbTable'] == 'manufacturer' && ($field_set['key'] == 'm_name' || $field_set['key'] == 'm_image')) {
										$field_key = preg_replace("/^m_/", '', $field_set['key']);
									} else {
										$field_key = $field_set['key'];
									}

									if (isset($field_set['dbParam']) && !empty($field_set['dbParam'])) {
										$need_add = false;

										if (($field_set['dbTable'] == 'order' && !in_array($field_key, $order_fields)) || ($field_set['dbTable'] == 'product' && !in_array($field_key, $product_fields)) || ($field_set['dbTable'] == 'order_product' && !in_array($field_key, $order_product_fields)) || ($field_set['dbTable'] == 'manufacturer' && !in_array($field_key, $manufacturer_fields))) {
											$need_add = true;
										}

										if ($need_add) {
											$param_str = '';
											if (isset($field_set['dbParam']['Type'])) {
												$param_str .= $field_set['dbParam']['Type'];
											}
											if (isset($field_set['dbParam']['Null'])) {
												$param_str .= $field_set['dbParam']['Null'] == 'YES' ? " NULL" : " NOT NULL";
											}
											if (isset($field_set['dbParam']['Default']) && !empty($field_set['dbParam']['Default'])) {
												$param_str .= " DEFAULT '" . $field_set['dbParam']['Default'] ."'";
											}
											if (isset($field_set['dbParam']['Extra']) && !empty($field_set['dbParam']['Extra'])) {
												$param_str .= " " . $field_set['dbParam']['Extra'];
											}
											if (isset($field_set['dbParam']['Key']) && $field_set['dbParam']['Key'] == 'PRI') {
												$param_str .= ", ADD PRIMARY KEY (`" . $field_set['dbParam']['Field'] . "`)";
											}

											if ($param_str) {
												$sql = "ALTER TABLE `" . DB_PREFIX . $field_set['dbTable'] . "` ADD `". $field_set['dbParam']['Field'] ."` $param_str";
												$this->db->query($sql);
											}
										}
									}
								}
							}
						}

						$exclude_keys = array('order_commission_total', 'commission_total', 'affiliate');

						foreach ($fields_page_list as $type) {
							// ключи полей в текущих настройках
							${'init_keys_'. $type} = array();
							if (isset($init_fields_setting[$type])) {
								foreach ($init_fields_setting[$type] as $field) {
									${'init_keys_'. $type}[] = $field['key'];
								}
							}

							if (isset($settings['fields_setting'][$type])) {
								foreach ($settings['fields_setting'][$type] as $id => $field_set) {
									// нет поля в текущих настройках - добавляем настройки из файла настроек
									if (!in_array($field_set['key'], ${'init_keys_'. $type})) {
										$init_fields_setting[$type][] = $field_set;
									} else {
										// поле есть в текущих настройках
										// если импортируем настройки всех полей - заменяем текущие настройки из файла настроек
										if (isset($this->request->get['restore_fields_setting_all'])) {
											foreach ($init_fields_setting[$type] as $id2 => $init_field) {
												if (!in_array($init_field['key'], $exclude_keys) && $field_set['key'] == $init_field['key']) {
													$init_fields_setting[$type][$id2] = $field_set;
													break;
												}
											}
										}
									}

									// Для доп. полей заказа: 'order_commission_total', 'commission_total', 'affiliate' - устанавливаем настройки по умолчанию для текущей версии опенкарта
									$ocVersion = $this->ompro->ocversion;
									if ($type == 'order_as_fields') {
										if ($field_set['key'] == 'order_commission_total') {
											if ($ocVersion >= 300) {
												$field_set['name'] = 'Начислено комиссионных за заказ (ОС3)';
												$field_set['sql'] = "(SELECT SUM(ct.amount) AS total FROM {DB_PREFIX}customer_transaction ct WHERE ct.order_id= o.order_id)";
											} else {
												$field_set['name'] = 'Начислено комиссионных за заказ (ОС 2.1-2.3)';
												$field_set['sql'] = "(SELECT SUM(at.amount) AS total FROM {DB_PREFIX}affiliate_transaction at WHERE at.order_id= o.order_id)";
											}
											$init_fields_setting[$type][$id] = $field_set;
										}

										if ($field_set['key'] == 'commission_total') {
											if ($ocVersion >= 300) {
												$field_set['name'] = 'Общая сумма комиссионных (ОС3)';
												$field_set['sql'] = "(SELECT SUM(ct.amount) AS total FROM {DB_PREFIX}customer_transaction ct WHERE ct.customer_id = o.affiliate_id)";
											} else {
												$field_set['name'] = 'Общая сумма комиссионных (ОС 2.1-2.3)';
												$field_set['sql'] = "(SELECT SUM(at.amount) AS total FROM {DB_PREFIX}affiliate_transaction at WHERE at.affiliate_id = o.affiliate_id)";
											}
											$init_fields_setting[$type][$id] = $field_set;
										}

										if ($field_set['key'] == 'affiliate') {
											if ($ocVersion >= 300) {
												$field_set['name'] = 'Партнер: Имя Фамилия (ОС3)';
												$field_set['sql'] = "(SELECT CONCAT(c.firstname, ' ', c.lastname) FROM {DB_PREFIX}customer c WHERE c.customer_id = o.affiliate_id)";
											} else {
												$field_set['name'] = 'Партнер: Имя Фамилия (ОС 2.1-2.3)';
												$field_set['sql'] = "(SELECT CONCAT(af.firstname, ' ', af.lastname) FROM {DB_PREFIX}affiliate af WHERE af.affiliate_id = o.affiliate_id)";
											}
											$init_fields_setting[$type][$id] = $field_set;
										}
									}

								}
							}

							// Сохраняем
							$this->ompro->editFieldsSetting($type, $init_fields_setting[$type]);
						}
					}

					if (isset($this->request->get['restore_admin_setting']) && !empty($settings['admin_setting'])) {
						$this->ompro->editAdminSetting($settings['admin_setting']);
					}

					if (isset($this->request->get['restore_group_setting']) && !empty($settings['group_setting'])) {
						$this->ompro->editSettingGroup($user_group_id, $settings['group_setting']);
					}

					$this->load->model('localisation/language');
					$system_languages = $this->model_localisation_language->getLanguages();

					$sys_lang_ides =  array();
					foreach ($system_languages as $language) {
						if ($language['status']) {
							$sys_lang_ides[] = $language['language_id'];
						}
					}

					if (isset($this->request->get['restore_tpl_all']) || isset($this->request->get['restore_tpl_new']) && !empty($settings['templates'])) {
						foreach ($settings['templates'] as $type => $templates) {
							$lang_keys =  array();

							if ($type == 'tlgrm') {
								$lang_keys = array('message','total_tpl','product_tpl','attribute_group_tpl','attribute_tpl','option_tpl');
							} elseif ($type == 'mail') {
								$lang_keys = array('total_tpl','subject','message');
							} elseif ($type == 'sms') {
								$lang_keys = array('message');
							} elseif ($type == 'comment') {
								$lang_keys = array('template');
							}

							if ($type == 'tlgrm' || $type == 'mail' || $type == 'sms' || $type == 'comment') {
								foreach ($templates as $tpl_id => $template) {
									foreach ($lang_keys as $lang_key) {
										$first_value = '';
										foreach ($template['template'][$lang_key] as $lang_id => $value) {
											if ($first_value == '') { $first_value = $value; }
											if (!in_array($lang_id, $sys_lang_ides)) {
												unset($template['template'][$lang_key][$lang_id]);
											}
										}
										foreach ($sys_lang_ides as $lang_id2) {
											if (!isset($template['template'][$lang_key][$lang_id2])) {
												$template['template'][$lang_key][$lang_id2] = $first_value;
											}
										}
									}
									$templates[$tpl_id] = $template;
								}
							}
							elseif ($type == 'option') {
								foreach ($templates as $tpl_id => $template) {
									foreach ($template['template']['option_value'] as $ov_id => $option_value) {
										$first_value_description = array();
										foreach ($option_value['option_value_description'] as $ovd_lang_id => $option_value_description) {
											if (!$first_value_description) { $first_value_description = $option_value_description; }
											if (!in_array($ovd_lang_id, $sys_lang_ides)) {
												unset($option_value['option_value_description'][$ovd_lang_id]);
											}
										}

										foreach ($sys_lang_ides as $lang_id) {
											if (!isset($option_value['option_value_description'][$lang_id])) {
												$option_value['option_value_description'][$lang_id] = $first_value_description;
											}
										}

										$template['template']['option_value'][$ov_id] = $option_value;
									}
									$templates[$tpl_id] = $template;
								}
							}
							elseif ($type == 'product') {
								foreach ($templates as $tpl_id => $template) {
									foreach ($template['template']['columns'] as $col_id => $column) {
										$first_name = '';
										foreach ($column['name'] as $lang_id => $value) {
											if ($first_name == '') { $first_name = $value; }
											if (!in_array($lang_id, $sys_lang_ides)) {
												unset($column['name'][$lang_id]);
											}
										}

										$first_data = '';
										foreach ($column['data'] as $lang_id => $value) {
											if ($first_data == '') { $first_data = $value; }
											if (!in_array($lang_id, $sys_lang_ides)) {
												unset($column['data'][$lang_id]);
											}
										}

										foreach ($sys_lang_ides as $lang_id) {
											if (!isset($column['name'][$lang_id])) {
												$column['name'][$lang_id] = $first_name;
											}
											if (!isset($column['data'][$lang_id])) {
												$column['data'][$lang_id] = $first_data;
											}
										}

										$template['template']['columns'][$col_id] = $column;
									}

									$templates[$tpl_id] = $template;
								}
							}

							foreach ($templates as $template) {
								$code = isset($template['code']) ? $template['code'] : '';
								$template_id = $this->ompro->getTemplateIdByCode($type, $code);
								if (!$template_id) {
									$tpl_id = $this->ompro->addTemplate($type, $template['template'], $code);
								} else {
									if (isset($this->request->get['restore_tpl_all'])) {
										$this->ompro->templateEdit($type, $template_id, $template['template']);
									}
								}
							}
						}
					}

					$this->session->data['success'] = $this->language->get('text_success_editor');
				} else {
					$this->session->data['error_warning'] = $this->language->get('error_import_data');
				}
			}
		}

		$this->response->redirect($this->url->link('sale/ompro/settingGroup', $this->strToken() . '&user_group_id=' . $user_group_id, true));
    }

    public function fieldsBackup() {
		$this->init();

		if (isset($this->request->get['type'])) {
			$type = $this->request->get['type'];
			if (!$this->user->hasPermission('modify', 'sale/ompro')) {
				$this->session->data['error_warning'] = $this->language->get('error_permission');
				$this->response->redirect($this->url->link('sale/ompro/fields', $this->strToken() . '&get_page=' . $type, true));
			} else {
				$fields = $res = array();
				if ($type == 'order_fields') {
					$fields = $this->ompro->getOrderFields();
				} elseif ($type == 'order_as_fields') {
					$fields = $this->ompro->getOrderAsFields();
				} elseif ($type == 'order_simple_fields') {
					$fields = $this->ompro->getOrderSimpleFields();
				} elseif ($type == 'product_fields') {
					$fields = $this->ompro->getProductFields();
				} elseif ($type == 'product_as_fields') {
					$fields = $this->ompro->getProductAsFields();
				}

				if ($fields) {
					$res['fields'] = $fields;
					$res['datatype'] = $type;

					$this->response->addheader('Pragma: public');
					$this->response->addheader('Expires: 0');
					$this->response->addheader('Content-Description: File Transfer');
					$this->response->addheader('Content-Type: application/octet-stream');
					$this->response->addheader('Content-Disposition: attachment; filename=' . 'ompro_' . $type . '_'. date('Y-m-d_H-i-s') . '.settings');
					$this->response->addheader('Content-Transfer-Encoding: binary');
					$this->response->setOutput(serialize($res));
				} else {
					$this->session->data['error_warning'] = $this->language->get('error_empty_fields');
					$this->response->redirect($this->url->link('sale/ompro/fields', $this->strToken() . '&get_page=' . $type, true));
				}
			}
		}
    }

	public function getTableCodes() {
		$this->init();

		$json = array();

		if (isset($this->request->get['table'])) {
			$target_table = $this->request->get['table'];
			$type_list = array('orders_table','product_table','history_table','excel_orders','excel_orders_products','print_orders','print_orders_table','print_products_table');

			if ($target_table == 'order') {
				$json['title'] = $this->language->get('heading_order_codes');
				$table = $this->ompro->orderFieldsData('table');
				$json['body'] = trim(html_entity_decode($table.'<div style="padding-left: 10px;">'.$this->language->get('text_order_codes_help').'</div>', ENT_QUOTES, 'UTF-8'));
				$json['footer'] = '';
			} elseif ($target_table == 'product') {
				$json['title'] = $this->language->get('heading_product_codes');
				$table = $this->ompro->productFieldsData('table');
				$json['body'] = trim(html_entity_decode($table.'<div style="padding-left: 10px;">'.$this->language->get('text_product_codes_help').'</div>', ENT_QUOTES, 'UTF-8'));
				$json['footer'] = html_entity_decode('<div style="float: left;"><b class="text-red">{product_count} - </b> доп. переменная для вывода порядкового номера товара в заказе </div>', ENT_QUOTES, 'UTF-8');
			} elseif (in_array($target_table, $type_list)) {
				$footer = ''; $target = $target_table;
				$substr_list = array('orders_table','product_table','history_table');
				if (in_array($target_table, $substr_list)) {
					$target = str_replace('_table', '', $target_table);
				}

				$attach_list = array('excel_orders','excel_orders_products','print_orders','print_orders_table','print_products_table');
				if (in_array($target_table, $attach_list)) {
					$footer .= $this->language->get('text_attach_help');
				}

				$html2pdf_list = array('print_orders','print_orders_table','print_products_table');
				if (in_array($target_table, $html2pdf_list)) {
					$footer .= '<br>'. $this->language->get('text_html2pdf_help');
				}

				$json['title'] = $this->language->get('heading_'.$target.'_table_tpl');
				$table = $this->omproapi->getTableTemplates($target);
				$json['body'] = trim(html_entity_decode($table, ENT_QUOTES, 'UTF-8'));
				$json['footer'] = '<p style="text-align: left;">'.$footer.'</p>';
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function testFilterId() {
		$this->init();
		$json = array();

		if (isset($this->request->get['type']) && isset($this->request->get['template_id']) && isset($this->request->get['filter_id'])) {
			if (!$this->ompro->testFilterId($this->request->get['type'], $this->request->get['template_id'], $this->request->get['filter_id'])) {
				$json['error'] = $this->language->get('text_error_filter_id');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getStatusTemplate() {
		$json = array();
		$template = false;
		if (isset($this->request->get['order_status_id'])) {
			$this->init();

			$order_id = 0;
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			}

			$setting_user_group = $this->ompro->getSettingGroup($this->user->getGroupId());

			if (isset($setting_user_group['comment_templates'])) {
				$comment_templates = $setting_user_group['comment_templates'];
			} else {
				$comment_templates = array();
			}
			$order_status_id = $this->request->get['order_status_id'];

			$language_id = $order_id ? $this->ompro->getOrderFieldValue($order_id, 'language_id') : $this->config->get('config_language_id');

			if (isset($comment_templates[$order_status_id][$language_id])) {
				$template = html_entity_decode($comment_templates[$order_status_id][$language_id], ENT_QUOTES, 'UTF-8');
			}
		}
		$json['template'] = $template;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getCommentTemplate() {
		$json = array();
		$template = '';

		if (isset($this->request->get['template_id'])) {
			$this->init();

			$order_id = 0;
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			}

			$template_id = $this->request->get['template_id'];
			$language_id = $order_id ? $this->ompro->getOrderFieldValue($order_id, 'language_id') : $this->config->get('config_language_id');
			$result = $this->ompro->getTemplateTemplate('comment', $template_id);
			if (isset($result['template'][$language_id])) {
				$template = html_entity_decode($result['template'][$language_id], ENT_QUOTES, 'UTF-8');
			}
		}

		$json['template'] = $template;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/* Orders ACTION */

	public function deleteOrders() {
		$this->init();
		$json = array();
		$orders = array();

		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$orders = array($this->request->get['order_id']);
				$this->ompro->deleteOrder($this->request->get['order_id']);
			} elseif (isset($this->request->post['selected'])) {
				$orders = $this->request->post['selected'];
				foreach ($this->request->post['selected'] as $order_id) {
					$this->ompro->deleteOrder($order_id);
				}
			}

			$json['success'] = $this->language->get('text_success') . implode(', ', $orders);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function createInvoiceNo() {
		$this->init();
		$json = array();

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$invoice_no = $this->ompro->createInvoiceNo($order_id);

		if ($invoice_no) {
			$json['invoice_no'] = $invoice_no;
		} else {
			$json['error'] = $this->language->get('error_action');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addReward() {
		$this->init();

		$json = array();

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$mail_reward_template_id = $this->ompro->getTargetMailTemplate('reward');
		$sms_reward_template_id = $this->ompro->getTargetSmsTemplate('reward');
		$tlgrm_reward_template_id = $this->ompro->getTargetTlgrmTemplate('reward');

		$orders = $this->ompro->getOrders($this->ompro->filterData(), array($order_id));
		$order_info = $orders[0];

		if ($order_info && $order_info['customer_id'] && ($order_info['reward'] > 0)) {
			$reward_total = $this->ompro->getTotalCustomerRewardsByOrderId($order_id);

			if (!$reward_total) {
				$customer_id = $order_info['customer_id'];
				$customer_info = $this->ompro->getCustomer($customer_id);

				if ($customer_info) {
					$description = $this->language->get('text_order_id') . ' #' . $order_id;

					if ($mail_reward_template_id || $sms_reward_template_id || $tlgrm_reward_template_id) {
						$reward_id = $this->ompro->addPoints($customer_id, $description, $order_info['reward'], $order_id);
						if ($reward_id) {
							$all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
							$language_id = isset($order_info['language_id']) ? $order_info['language_id'] : $this->config->get('config_language_id');

							if ($mail_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('mail', $mail_reward_template_id);
								$recipients = array();
								$recipients[] = array(
									'recipient_name' => '', 'email' => $order_info['email']
								);
								$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data);
							}

							if ($sms_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('sms', $sms_reward_template_id);
								$to = $order_info['telephone'];
								$copies = array();
								$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data);
							}

							if ($tlgrm_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_reward_template_id);
								$chat_ids = array();
								if (isset($order_info['telegram_id']) && $order_info['telegram_id']) {
									$chat_ids[] = $order_info['telegram_id'];
									$this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data);
								}
							}

							$json['success'] = $this->language->get('text_reward_added') . $order_id;

						} else {
							$json['error'] = $this->language->get('text_error_reward_add') . $order_id;
						}
					}

					if (!$mail_reward_template_id) {
						$this->omproapi->addReward($customer_info, $customer_id, $description, $order_info['reward'], $order_id);
						$json['success'] = $this->language->get('text_reward_added') . $order_id;
					}

				}
			}
		} else {
			$json['error'] = $this->language->get('text_error_reward_add') . $order_id;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function presentReward() {
		$this->init();
		$json = array();
		$json['error'] = false;
		$success = '';

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
			$customer_info = $this->ompro->getCustomer($customer_id);
			$points = (int)$this->request->post['points'];

			if ($points < 0) {
				$total = (int)$this->ompro->getRewardTotal($customer_id);

				if ($total <= 0) {
					$json['error'] = $this->language->get('text_error_reward_total') . $total;
				} else {
					$diffrent = (int)($total - str_ireplace('-','',$points));
					if ($diffrent <= 0) {
						$points = '-'.$total;
						$success = $this->language->get('text_success_max_remove_reward') . $total;
					} else {
						$success = str_ireplace('-','',$points) . $this->language->get('text_success_remove_reward');
					}
				}
			} else {
				$success = $points . $this->language->get('text_success_add_reward');
			}

			if (!$json['error']) {
				$mail_reward_template_id = $this->ompro->getTargetMailTemplate('reward');
				$sms_reward_template_id = $this->ompro->getTargetSmsTemplate('reward');
				$tlgrm_reward_template_id = $this->ompro->getTargetTlgrmTemplate('reward');

				if ($customer_info) {
					if (isset($this->request->get['order_id'])) {
						$order_id = $this->request->get['order_id'];
					} else {
						$order_id =  0;
					}

					$description = $this->request->post['description'];

					if ($order_id && ($mail_reward_template_id || $sms_reward_template_id || $tlgrm_reward_template_id)) {
						$reward_id = $this->ompro->addPoints($customer_id, $description, $points);

						$all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
						if (!empty($all_orders_data) && !empty($all_orders_data['orders']) && !empty($all_orders_data['orders'][$order_id])) {
							$order_info = $all_orders_data['orders'][$order_id]['order_info'];
						} else {
							$order_info = array();
						}

						if ($reward_id && $order_info) {
							$all_orders_data['orders'][$order_id]['order_info']['order_reward_total'] = $points;

							$language_id = isset($order_info['language_id']) && $order_info['language_id'] ? $order_info['language_id'] : $this->config->get('config_language_id');

							if ($mail_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('mail', $mail_reward_template_id);
								$recipients = array();
								$recipients[] = array(
									'recipient_name' => '', 'email' => $order_info['email']
								);
								$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data);
							}

							if ($sms_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('sms', $sms_reward_template_id);
								$to = $order_info['telephone'];
								$copies = array();
								$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data);
							}

							if ($tlgrm_reward_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_reward_template_id);
								$chat_ids = array();
								if (isset($order_info['telegram_id']) && $order_info['telegram_id']) {
									$chat_ids[] = $order_info['telegram_id'];
									$this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data);
								}
							}

							$json['success'] = $success;

						} else {
							$json['error'] = $this->language->get('text_error_reward_present');
						}
					}

					if (!$mail_reward_template_id) {
						$reward_id = $this->omproapi->addReward($customer_info, $customer_id, $description, $points);
						if ($reward_id) {
							$json['success'] = $success;
						} else {
							$json['error'] = $this->language->get('text_error_reward_present');
						}
					}
				}
			}
		} else {
			$json['error'] = $this->language->get('text_error_reward_present');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeReward() {
		$this->init();
		$json = array();

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$result = $this->ompro->deleteReward($order_id);

		if ($result) {
			$json['success'] = $this->language->get('text_reward_removed') . $order_id;
		} else {
			$json['error'] = $this->language->get('text_error_reward_remove') . $order_id;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addCredit() {
		$this->init();
		$json = array();
		$json['error'] = false;
		$success = '';

		if (isset($this->request->get['customer_id'])) {
			$customer_id = $this->request->get['customer_id'];
			$customer_info = $this->ompro->getCustomer($customer_id);
			$amount = (float)$this->request->post['amount'];

			if ($amount < 0) {
				$total = $this->ompro->getCustomerTransactionTotal($customer_id);

				if ($total <= 0) {
					$json['error'] = $this->language->get('text_error_credit_total') . $this->currency->format($total, $this->config->get('config_currency'));
				} else {
					$diffrent = $total - str_ireplace('-','',$amount);
					if ($diffrent <= 0) {
						$amount = '-'.$total;
						$success = $this->language->get('text_success_max_remove_credit') . $this->currency->format($total, $this->config->get('config_currency'));
					} else {
						$success = $this->language->get('text_success_remove_credit') . $this->currency->format($amount, $this->config->get('config_currency'));
					}
				}
			} else {
				$success = $this->language->get('text_success_add_credit') . $this->currency->format($amount, $this->config->get('config_currency'));
			}

			if (!$json['error']) {
				$transaction_id = $this->omproapi->addCredit($customer_info, $customer_id, $this->request->post['description'], $amount);
				if ($transaction_id) {
					$json['success'] = $success;
				} else {
					$json['error'] = $this->language->get('text_error_credit_present');
				}
			}
		} else {
			$json['error'] = $this->language->get('text_error_credit_present');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function addCommission() {
		$this->init();
		$json = array();

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$mail_transaction_template_id = $this->ompro->getTargetMailTemplate('transaction');
		$sms_transaction_template_id = $this->ompro->getTargetSmsTemplate('transaction');
		$tlgrm_transaction_template_id = $this->ompro->getTargetTlgrmTemplate('transaction');

		$orders = $this->ompro->getOrders($this->ompro->filterData(), array($order_id));
		$order_info = $orders[0];

		if ($order_info) {
			if (!$this->ompro->getTotalTransactionsByOrderId($order_id)) {
				$affiliate_id = $order_info['affiliate_id'];
				$affiliate_info = $this->ompro->getAffiliate($affiliate_id);

				if ($affiliate_info) {
					$description = $this->language->get('text_order_id') . ' #' . $order_id;

					if ($mail_transaction_template_id || $sms_transaction_template_id || $tlgrm_transaction_template_id) {
						$transaction_id = $this->ompro->addComission($affiliate_id, $description, $order_info['commission'], $order_id);
						if ($transaction_id) {
							$all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
							$language_id = isset($order_info['language_id']) ? $order_info['language_id'] : $this->config->get('config_language_id');

							if ($mail_transaction_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('mail', $mail_transaction_template_id);
								$recipients = array();
								$recipients[] = array(
									'recipient_name' => '', 'email' => $affiliate_info['email']
								);
								$this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data);
							}

							if ($sms_transaction_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('sms', $sms_transaction_template_id);
								$to = $affiliate_info['telephone'];
								$copies = array();
								$this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data);
							}

							if ($tlgrm_transaction_template_id) {
								$template_info = $this->ompro->getTemplateTemplate('tlgrm', $tlgrm_transaction_template_id);
								$chat_ids = array();
								if (isset($affiliate_info['telegram_id']) && $affiliate_info['telegram_id']) {
									$chat_ids[] = $affiliate_info['telegram_id'];
									$this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data);
								}
							}

							$json['success'] = $this->language->get('text_commission_added') . $order_id;

						} else {
							$json['error'] = $this->language->get('text_error_commission_add') . $order_id;
						}
					}

					if (!$mail_transaction_template_id) {
						$this->ompro->addTransaction($affiliate_info, $affiliate_id, $description, $order_info['commission'], $order_id);
						$json['success'] = $this->language->get('text_commission_added') . $order_id;
					}
				}
			} else {
				$json['error'] = $this->language->get('text_error_commission_add') . $order_id;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function removeCommission() {
		$this->init();
		$json = array();
		if (!$this->user->hasPermission('modify', 'sale/ompro')) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$orders = $this->ompro->getOrders($this->ompro->filterData(), array($order_id));
			$order_info = $orders[0];

			if ($order_info) {
				$this->ompro->deleteTransaction($order_id);
			}
			$json['success'] = $this->language->get('text_commission_removed') . $order_id;
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function autocomplete() {
		$this->init();
		$json = array();

		if (isset($this->request->get['target'])) {
			if ($this->request->get['target'] == 'customer') {
				$user_group_id = $this->user->getGroupId();
				$setting_user_group = $this->ompro->getSettingGroup($user_group_id);
				$filter_customer_group_id = array();
				if (!empty($setting_user_group['select_orders']) && !empty($setting_user_group['select_orders']['customer_groups'])) {
					$filter_customer_group_id = $setting_user_group['select_orders']['customer_groups'];
				}
				$json = $this->omproapi->autocomplete($filter_customer_group_id);
			} else {
				$json = $this->omproapi->autocomplete();
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function download() {
		$this->init();
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
			$this->load->language('error/not_found');
			$this->document->setTitle($this->language->get('heading_title'));

			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_not_found'] = $this->language->get('text_not_found');

			$strtoken = $this->strToken();

			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_homepage'),
				'href' => $this->url->link('common/dashboard', $strtoken, true)
			);
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('error/not_found', $strtoken, true)
			);

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function upload() {
		$this->init();
		$json = array();

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			$name = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');
			$name = $this->omproapi->translit_cyrillic_to_latina($name);

			if (preg_match('/[а-яА-ЯёЁ№]/', $name) || (utf8_strlen($name) < 3) || (utf8_strlen($name) > 128)) {
				$json['error'] = $this->language->get('error_filename');
			}

			$allowed = array();
			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));
			$filetypes = explode("\n", $extension_allowed);
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($name, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			$allowed = array();
			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));
			$filetypes = explode("\n", $mime_allowed);
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			$content = file_get_contents($this->request->files['file']['tmp_name']);
			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			if (isset($this->request->get['upload_order'])) {
				$file = $order_id . '_' . date("Ymd_") . rand(100,99999) . '_' . $name;
				move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);
				$json['filename'] = $file;
				$json['success'] = $this->language->get('text_upload_success');
			} else {
				$filename = $name . '.' . token(32);
				move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $filename);
				$json['name'] = $name;
				$json['filename'] = $filename;
				$json['code'] = $this->ompro->addUpload($name, $filename);
				$json['success'] = $this->language->get('text_upload_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function strToken() {
		if ($this->ompro->ocversion >= 300) {
			return 'user_token='.$this->session->data['user_token'];
		} else {
			return 'token='.$this->session->data['token'];
		}
	}

	public function executeOmproApiMethod() {
		$this->init();
		$json = array();
		$route = $method = '';
		if (isset($this->request->get['getroute'])) {
			$route = $this->request->get['getroute'];
		}
		if (isset($this->request->get['method'])) {
			$method = $this->request->get['method'];
		}

		if ($route && $method) {
			$json = $this->{$route}->{$method}();
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}