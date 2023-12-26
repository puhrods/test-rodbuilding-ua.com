<?php
class ControllerModuleUkrPoshtaCron extends Controller {
    private $settings;

    public function __construct($registry) {
        parent::__construct($registry);

        require_once(DIR_SYSTEM . 'helper/ukrposhta.php');

        $registry->set('ukrposhta', new UkrPoshta($registry));

        if (version_compare(VERSION, '3', '>=')) {
            $this->settings = $this->config->get('shipping_ukrposhta');
        } else {
            $this->settings = $this->config->get('ukrposhta');
        }
    }
    
    public function update() {
        if (isset($this->request->get['type'], $this->request->get['key']) && $this->request->get['key'] == $this->settings['key_cron']) {
            $this->ukrposhta->update($this->request->get['type']);
        }
    }

    public function departuresTracking() {
        if (!empty($this->request->get['key']) && $this->request->get['key'] == $this->settings['key_cron']) {
            if (version_compare(VERSION, '2.3', '>=')) {
                $this->load->model('extension/module/ukrposhta_cron');

                $model_name = 'model_extension_module_ukrposhta_cron';
            } else {
                $this->load->model('module/ukrposhta_cron');

                $model_name = 'model_module_ukrposhta_cron';
            }

            /* Caching orders for tracking if there are more than $limit */
            $limit 	= 100;

            $data = $this->cache->get('ukrposhta_tracking_orders');

            if ($data) {
                $orders = array_splice($data, 0, $limit);

                if ($data) {
                    $this->cache->set('ukrposhta_tracking_orders', $data);
                } else {
                    $this->cache->delete('ukrposhta_tracking_orders');
                }
            } else {
                $result = $this->$model_name->getOrders();

                if ($result->num_rows > $limit) {
                    $data = $result->rows;

                    $orders = array_splice($data, 0, $limit);

                    $this->cache->set('ukrposhta_tracking_orders', $data);
                } else {
                    $orders = $result->rows;
                }
            }

            if ($orders) {
                $cn_numbers = array();

                $find_cn = array(
                    '{barcode}',
                    '{step}',
                    '{date}',
                    '{index}',
                    '{name}',
                    '{event}',
                    '{eventName}',
                    '{country}',
                    '{eventReason}',
                    '{eventReason_id}',
                    '{mailType}',
                    '{indexOrder}'
                );

                $find_order = array(
                    '{order_id}',
                    '{invoice_no}',
                    '{invoice_prefix}',
                    '{store_name}',
                    '{store_url}',
                    '{customer}',
                    '{firstname}',
                    '{lastname}',
                    '{email}',
                    '{telephone}',
                    '{fax}',
                    '{payment_firstname}',
                    '{payment_lastname}',
                    '{payment_company}',
                    '{payment_address_1}',
                    '{payment_address_2}',
                    '{payment_postcode}',
                    '{payment_city}',
                    '{payment_zone}',
                    '{payment_zone_id}',
                    '{payment_country}',
                    '{shipping_firstname}',
                    '{shipping_lastname}',
                    '{shipping_company}',
                    '{shipping_address_1}',
                    '{shipping_address_2}',
                    '{shipping_postcode}',
                    '{shipping_city}',
                    '{shipping_zone}',
                    '{shipping_zone_id}',
                    '{shipping_country}',
                    '{comment}',
                    '{total}',
                    '{commission}',
                    '{date_added}',
                    '{date_modified}'
                );

                $find_product = array(
                    '{name}',
                    '{model}',
                    '{option}',
                    '{sku}',
                    '{ean}',
                    '{upc}',
                    '{jan}',
                    '{isbn}',
                    '{mpn}',
                    '{quantity}'
                );

                foreach ($orders as $i => $order) {
                    $replace_order[$order['order_id']] = array(
                        '{order_id}'           => $order['order_id'],
                        '{invoice_no}'         => $order['invoice_no'],
                        '{invoice_prefix}'     => $order['invoice_prefix'],
                        '{store_name}'         => $order['store_name'],
                        '{store_url}'          => $order['store_url'],
                        '{customer}'           => $order['customer'],
                        '{firstname}'          => $order['firstname'],
                        '{lastname}'           => $order['lastname'],
                        '{email}'              => $order['email'],
                        '{telephone}'          => $order['telephone'],
                        '{fax}'                => isset($order['fax']) ? $order['fax'] : '',
                        '{payment_firstname}'  => $order['payment_firstname'],
                        '{payment_lastname}'   => $order['payment_lastname'],
                        '{payment_company}'    => $order['payment_company'],
                        '{payment_address_1}'  => $order['payment_address_1'],
                        '{payment_address_2}'  => $order['payment_address_2'],
                        '{payment_postcode}'   => $order['payment_postcode'],
                        '{payment_city}'       => $order['payment_city'],
                        '{payment_zone}'       => $order['payment_zone'],
                        '{payment_zone_id}'    => $order['payment_zone_id'],
                        '{payment_country}'    => $order['payment_country'],
                        '{shipping_firstname}' => $order['shipping_firstname'],
                        '{shipping_lastname}'  => $order['shipping_lastname'],
                        '{shipping_company}'   => $order['shipping_company'],
                        '{shipping_address_1}' => $order['shipping_address_1'],
                        '{shipping_address_2}' => $order['shipping_address_2'],
                        '{shipping_postcode}'  => $order['shipping_postcode'],
                        '{shipping_city}'      => $order['shipping_city'],
                        '{shipping_zone}'      => $order['shipping_zone'],
                        '{shipping_zone_id}'   => $order['shipping_zone_id'],
                        '{shipping_country}'   => $order['shipping_country'],
                        '{comment}'            => $order['comment'],
                        '{total}'              => $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']),
                        '{commission}'         => $order['commission'],
                        '{date_added}'         => $order['date_added'],
                        '{date_modified}'      => $order['date_modified']
                    );

                    foreach ($this->$model_name->getSimpleFields($order['order_id']) as $k => $v) {
                        if (!in_array('{' . $k . '}', $find_order)) {
                            $find_order[] = '{' . $k . '}';
                            $replace_order[$order['order_id']][$k] = $v;
                        }
                    }

                    $cn_numbers[] = $order['ukrposhta_cn_number'];

                    $orders[$order['ukrposhta_cn_number']] = $order;

                    unset($orders[$i]);
                }

                if ($this->settings['debugging_mode']) {
                    $this->log->write('Ukrposhta API tracking orders:');
                    $this->log->write($orders);
                }

                $documents = $this->ukrposhta->tracking($cn_numbers);

                if ($documents) {
                    $this->load->model('checkout/order');
                    $this->load->model('localisation/language');
                    $this->load->model('setting/setting');

                    if ($this->settings['debugging_mode']) {
                        $this->log->write('Ukrposhta API documents:');
                        $this->log->write($documents);
                    }

                    if (version_compare(VERSION, '2.2', '>=')) {
                        $language_directory = 'code';
                    } else {
                        $language_directory = 'directory';
                    }

                    foreach($documents as $document) {
                        foreach ($this->settings['settings_tracking_statuses'] as $s_t_s) {
                            if ($s_t_s['ukrposhta_status'] == $document['event'] && $s_t_s['store_status'] != $orders[$document['barcode']]['order_status_id'] && (!$s_t_s['implementation_delay']['value'] || strtotime($document['date']) < strtotime('- ' . $s_t_s['implementation_delay']['value'] . ' ' . $s_t_s['implementation_delay']['type']))) {
                                $replace_cn = array();

                                foreach ($find_cn as $m) {
                                    $k = str_replace(array('{', '}'), '', $m);

                                    $replace_cn[$k] = (isset($document[$k])) ? $document[$k] : '';
                                }

                                // E-mail
                                $email_message = '';

                                if ($s_t_s['email'][$orders[$document['barcode']]['language_id']]) {
                                    $email_template = explode('|', $s_t_s['email'][$orders[$document['barcode']]['language_id']]);

                                    if (!empty($email_template[0])) {
                                        $email_message = str_replace($find_order, $replace_order[$orders[$document['barcode']]['order_id']], $email_template[0]);
                                        $email_message = str_replace($find_cn, $replace_cn, $email_message);
                                    }

                                    if (!empty($email_template[1])) {
                                        $products = $this->$model_name->getOrderProducts($orders[$document['barcode']]['order_id']);

                                        foreach ($products as $k => $product) {
                                            $replace_product = array(
                                                'name'     => $product['name'],
                                                'model'    => $product['model'],
                                                'option'   => '',
                                                'sku'      => $product['sku'],
                                                'ean'      => $product['ean'],
                                                'upc'      => $product['upc'],
                                                'jan'      => $product['jan'],
                                                'isbn'     => $product['isbn'],
                                                'mpn'      => $product['mpn'],
                                                'quantity' => $product['quantity']
                                            );

                                            if ($product['option']) {
                                                foreach ($product['option'] as $option) {
                                                    $replace_product['option'] = $option['name'] . ': ' . $option['value'];
                                                }
                                            }

                                            $email_message .= trim(str_replace($find_product, $replace_product, $email_template[1]));
                                        }
                                    }
                                }

                                // SMS
                                $sms_message = '';

                                if ($s_t_s['sms'][$orders[$document['barcode']]['language_id']]) {
                                    $sms_template = explode('|', $s_t_s['sms'][$orders[$document['barcode']]['language_id']]);

                                    if (!empty($sms_template[0])) {
                                        $sms_message = str_replace($find_order, $replace_order[$orders[$document['barcode']]['order_id']], $sms_template[0]);
                                        $sms_message = str_replace($find_cn, $replace_cn, $sms_message);
                                    }

                                    if (!empty($sms_template[1])) {
                                        $products = $this->$model_name->getProducts($orders[$document['barcode']]['order_id']);

                                        foreach ($products as $k => $product) {
                                            $replace_product = array(
                                                'name'     => $product['name'],
                                                'model'    => $product['model'],
                                                'option'   => '',
                                                'sku'      => $product['sku'],
                                                'ean'      => $product['ean'],
                                                'upc'      => $product['upc'],
                                                'jan'      => $product['jan'],
                                                'isbn'     => $product['isbn'],
                                                'mpn'      => $product['mpn'],
                                                'quantity' => $product['quantity']
                                            );

                                            if ($product['option']) {
                                                foreach ($product['option'] as $option) {
                                                    $replace_product['option'] .= $option['name'] . ': ' . $option['value'];
                                                }
                                            }

                                            $sms_message .= trim(str_replace($find_product, $replace_product, $sms_template[1]));
                                        }
                                    }
                                }

                                // Add order history
                                if (isset($s_t_s['customer_notification_default'])) {
                                    $notify = true;
                                } else {
                                    $notify = false;
                                }

                                if (version_compare(VERSION, '2', '>=')) {
                                    $this->model_checkout_order->addOrderHistory($orders[$document['barcode']]['order_id'], $s_t_s['store_status'], $sms_message, $notify);
                                } else {
                                    $this->model_checkout_order->update($orders[$document['barcode']]['order_id'], $s_t_s['store_status'], $sms_message, $notify);
                                }

                                if ($this->settings['debugging_mode']) {
                                    $this->log->write('Ukrposhta API in order #' . $orders[$document['barcode']]['order_id'] . ' changed its status to #' . $s_t_s['store_status']);
                                }

                                $language = new Language($orders[$document['barcode']][$language_directory]);
                                $language->load($orders[$document['barcode']][$language_directory]);

                                if (version_compare(VERSION, '3', '>=')) {
                                    $language->load('mail/order_edit');

                                    $subject = sprintf($language->get('text_subject'), html_entity_decode($orders[$document['barcode']]['store_name'], ENT_QUOTES, 'UTF-8'), $orders[$document['barcode']]['order_id']);

                                    $mail = new Mail($this->config->get('config_mail_engine'));
                                    $mail->parameter = $this->config->get('config_mail_parameter');
                                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                                    $from = $this->model_setting_setting->getSettingValue('config_email', $orders[$document['barcode']]['store_id']);

                                    if (!$from) {
                                        $from = $this->config->get('config_email');
                                    }
                                } elseif (version_compare(VERSION, '2', '>=')) {
                                    $language->load('mail/order');

                                    $subject = sprintf($language->get('text_update_subject'), html_entity_decode($orders[$document['barcode']]['store_name'], ENT_QUOTES, 'UTF-8'), $orders[$document['barcode']]['order_id']);

                                    $mail = new Mail();
                                    $mail->protocol = $this->config->get('config_mail_protocol');
                                    $mail->parameter = $this->config->get('config_mail_parameter');
                                    $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
                                    $mail->smtp_username = $this->config->get('config_mail_smtp_username');
                                    $mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
                                    $mail->smtp_port = $this->config->get('config_mail_smtp_port');
                                    $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

                                    $from = $this->config->get('config_email');
                                } else {
                                    $language->load('mail/order');

                                    $subject = sprintf($language->get('text_update_subject'), html_entity_decode($orders[$document['barcode']]['store_name'], ENT_QUOTES, 'UTF-8'), $orders[$document['barcode']]['order_id']);

                                    $mail = new Mail();
                                    $mail->protocol = $this->config->get('config_mail_protocol');
                                    $mail->parameter = $this->config->get('config_mail_parameter');
                                    $mail->hostname = $this->config->get('config_smtp_host');
                                    $mail->username = $this->config->get('config_smtp_username');
                                    $mail->password = $this->config->get('config_smtp_password');
                                    $mail->port = $this->config->get('config_smtp_port');
                                    $mail->timeout = $this->config->get('config_smtp_timeout');

                                    $from = $this->config->get('config_email');
                                }

                                // Customer notification
                                if (isset($s_t_s['customer_notification']) && filter_var($orders[$document['barcode']]['email'], FILTER_VALIDATE_EMAIL) && $email_message) {
                                    $mail->setTo($orders[$document['barcode']]['email']);
                                    $mail->setFrom($from);
                                    $mail->setSender(html_entity_decode($orders[$document['barcode']]['store_name'], ENT_QUOTES, 'UTF-8'));
                                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                                    $mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
                                    $mail->send();
                                }

                                // Admin notification
                                if (isset($s_t_s['admin_notification']) && filter_var($this->config->get('config_email'), FILTER_VALIDATE_EMAIL) && $email_message) {
                                    $mail->setTo($from);
                                    $mail->setFrom($from);
                                    $mail->setSender(html_entity_decode($orders[$document['barcode']]['store_name'], ENT_QUOTES, 'UTF-8'));
                                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
                                    $mail->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
                                    $mail->send();

                                    // Send to additional alert emails
                                    if (version_compare(VERSION, '2.3', '>=')) {
                                        $emails = explode(',', $this->config->get('config_alert_email'));
                                    } else {
                                        $emails = explode(',', $this->config->get('config_mail_alert'));
                                    }

                                    foreach ($emails as $email) {
                                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                            $mail->setTo($email);
                                            $mail->send();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

class ControllerExtensionModuleUkrPoshtaCron extends ControllerModuleUkrPoshtaCron {

}