<?php

class ControllerSaleOMPro extends Controller
{
    private $error = array();
    public $log_write = false;
    public $errors = NULL;
    public $license_key = "11111";
    public $remote_port = NULL;
    public $remote_timeout = NULL;
    public $local_key_storage = NULL;
    public $read_query = NULL;
    public $update_query = NULL;
    public $local_key_path = NULL;
    public $local_key_name = NULL;
    public $local_key_transport_order = NULL;
    public $local_key_grace_period = NULL;
    public $local_key_last = NULL;
    public $validate_download_access = NULL;
    public $release_date = NULL;
    public $key_data = NULL;
    public $status_messages = NULL;
    public $valid_for_product_tiers = NULL;
    public $index_route = "index.php?route=sale/ompro";
    public $mod_path = "sale/ompro";
    public $server = "";
    public $server_http = "";
    public $api_server = "";
    public $product_code = "ompro";
    public $secret_key = "11111";
    public $use_localhost = false;
    public $sale_resourse = NULL;
    public $sale_order_id = NULL;
    public $sale_test_domain = NULL;
    public $sale_email = NULL;
    public $ocversion = NULL;
    public static $custom_methods = array();
    public function protect()
    {
        $this->errors = false;
        $this->remote_port = 80;
        $this->remote_timeout = 20;
        $this->valid_local_key_types = array("spbas");
        $this->local_key_type = "spbas";
        $this->local_key_storage = "database";
        $this->local_key_grace_period = 0;
        $this->local_key_last = 0;
        $this->read_query = "SELECT `value` as local_key FROM `" . DB_PREFIX . "licenses_km` WHERE `key`='local_key' AND `p_code`= '" . $this->product_code . "' ";
        $this->update_query = "UPDATE `" . DB_PREFIX . "licenses_km` SET `value`= '{local_key}' WHERE `key`='local_key' AND `p_code`= '" . $this->product_code . "'";
        $this->local_key_path = "./";
        $this->local_key_name = "license.txt";
        $this->local_key_transport_order = "scf";
        $this->validate_download_access = false;
        $this->release_date = false;
        $this->valid_for_product_tiers = false;
        $this->load->language("common/footer");
        $this->ocversion = sprintf($this->language->get("text_version"), VERSION);
        $this->key_data = array("custom_fields" => array(), "download_access_expires" => 0, "license_expires" => 0, "local_key_expires" => 0, "status" => "Invalid");
        $this->load->language($this->mod_path);
        $keys = array("active", "suspended", "expired", "pending", "download_access_expired", "missing_license_key", "unknown_local_key_type", "could_not_obtain_local_key", "maximum_grace_period_expired", "local_key_tampering", "local_key_invalid_for_location", "missing_license_file", "license_file_not_writable", "invalid_local_key_storage", "could_not_save_local_key", "license_key_string_mismatch", "local_key_not_read");
        $this->status_messages = array();
        foreach ($keys as $key) {
            $this->status_messages[$key] = $this->language->get("message_" . $key);
        }
        $this->localization = array("active" => "This license is active.", "suspended" => "Error: This license has been suspended.");
    }
    public function validateLkey()
    {
        $this->protect();
        if ($this->use_localhost && $this->getIpLocal() && $this->is_windows() && !file_exists((string) $this->local_key_path . $this->local_key_name)) {
            return true;
        }
        if (!$this->license_key) {
            return $this->errors = $this->status_messages["missing_license_key"];
        }
        if (!in_array(strtolower($this->local_key_type), $this->valid_local_key_types)) {
            return $this->errors = $this->status_messages["unknown_local_key_type"];
        }
        switch ($this->local_key_storage) {
            case "database":
                $local_key = $this->db_read_local_key();
                break;
            case "filesystem":
                $local_key = $this->read_local_key();
                break;
            default:
                return $this->errors = $this->status_messages["missing_license_key"];
        }
        if ($this->errors) {
            return $this->errors;
        }
        return $this->validate_local_key($local_key);
    }
    public function decode_key($local_key)
    {
        return base64_decode(str_replace("\n", "", urldecode($local_key)));
    }
    public function split_key($local_key, $token = "{license}")
    {
        return explode($token, $local_key);
    }
    public function get_massiv($local_key, $token = "|")
    {
        return explode($token, $local_key);
    }
    public function validate_access($key, $valid_accesses)
    {
        return in_array($key, (array) $valid_accesses);
    }
    public function wildcard_ip($key)
    {
        $octets = explode(".", $key);
        array_pop($octets);
        $ip_range[] = implode(".", $octets) . ".*";
        array_pop($octets);
        $ip_range[] = implode(".", $octets) . ".*";
        array_pop($octets);
        $ip_range[] = implode(".", $octets) . ".*";
        return $ip_range;
    }
    public function wildcard_domain($key)
    {
        return "*." . str_replace("www.", "", $key);
    }
    public function wildcard_server_hostname($key)
    {
        $hostname = explode(".", $key);
        unset($hostname[0]);
        $hostname = !isset($hostname[1]) ? array($key) : $hostname;
        return "*." . implode(".", $hostname);
    }
    public function extract_access_set($instances, $enforce)
    {
        foreach ($instances as $key => $instance) {
            if ($key != $enforce) {
                continue;
            }
            return $instance;
        }
        return array();
    }
    public function validate_local_key($local_key)
    {
        $local_key_src = $this->decode_key($local_key);
        $parts = $this->split_key($local_key_src);
        if (!isset($parts[1]) || md5($parts[0] . $this->secret_key) != $parts[1]) {
            return $this->errors = $this->status_messages["local_key_tampering"];
        }
        $parts = $this->get_massiv($local_key_src);
        $key_data = unserialize($parts[1]);
        $instance = $key_data["instance"];
        unset($key_data["instance"]);
        $enforce = $key_data["enforce"];
        unset($key_data["enforce"]);
        $this->key_data = $key_data;
        if ((string) $key_data["license_key_string"] != (string) $this->license_key) {
            return $this->errors = $this->status_messages["license_key_string_mismatch"];
        }
        if ((string) $key_data["check_period"] < time()) {
            $this->clear_cache_local_key(true);
            return $this->validateLkey();
        }
        $conflicts = array();
        $access_details = $this->access_details();
        foreach ((array) $enforce as $key) {
            $valid_accesses = $this->extract_access_set($instance, $key);
            if (!$this->validate_access($access_details[$key], $valid_accesses)) {
                $conflicts[$key] = true;
                if (in_array($key, array("ip", "server_ip"))) {
                    foreach ($this->wildcard_ip($access_details[$key]) as $ip) {
                        if ($this->validate_access($ip, $valid_accesses)) {
                            unset($conflicts[$key]);
                            break;
                        }
                    }
                } else {
                    if (in_array($key, array("domain"))) {
                        if ($this->validate_access($this->wildcard_domain($access_details[$key]), $valid_accesses)) {
                            unset($conflicts[$key]);
                        }
                    } else {
                        if (in_array($key, array("server_hostname")) && $this->validate_access($this->wildcard_server_hostname($access_details[$key]), $valid_accesses)) {
                            unset($conflicts[$key]);
                        }
                    }
                }
            }
        }
        if (!empty($conflicts)) {
            return $this->errors = $this->status_messages["local_key_invalid_for_location"];
        }
    }
    public function db_read_local_key()
    {
        $query = $this->db->query($this->read_query);
        if ($query->num_rows == 0) {
            return $this->errors = $this->status_messages["local_key_not_read"];
        }
        $result = $query->row;
        if (!$result["local_key"]) {
            $result["local_key"] = $this->fetch_new_local_key();
            if ($this->errors) {
                return $this->errors;
            }
            $this->db_write_local_key($result["local_key"]);
        }
        return $this->local_key_last = $result["local_key"];
    }
    public function db_write_local_key($local_key)
    {
        $this->db->query(str_replace("{local_key}", $local_key, $this->update_query));
        if ($this->db->countAffected() == 0) {
            return $this->errors = $this->status_messages["local_key_not_read"];
        }
        return true;
    }
    public function read_local_key()
    {
        if (!file_exists($path = (string) $this->local_key_path . $this->local_key_name)) {
            return $this->errors = $this->status_messages["missing_license_file"] . $path;
        }
        if (!is_writable($path)) {
            return $this->errors = $this->status_messages["license_file_not_writable"] . $path;
        }
        if (!($local_key = @file_get_contents($path))) {
            $local_key = $this->fetch_new_local_key();
            if ($this->errors) {
                return $this->errors;
            }
            $this->write_local_key(urldecode($local_key), $path);
        }
        return $this->local_key_last = $local_key;
    }
    public function clear_cache_local_key($clear = false)
    {
        switch (strtolower($this->local_key_storage)) {
            case "database":
                $this->db_write_local_key("");
                break;
            case "filesystem":
                $this->write_local_key("", (string) $this->local_key_path . $this->local_key_name);
                break;
            default:
                return $this->errors = $this->status_messages["invalid_local_key_storage"];
        }
    }
    public function write_local_key($local_key, $path)
    {
        return true;
    }
    public function fetch_new_local_key($need_new_license = true)
    {
        return true;
    }
    public function build_querystring($array)
    {
        $buffer = "";
        foreach ((array) $array as $key => $value) {
            if ($buffer) {
                $buffer .= "&";
            }
            $buffer .= (string) $key . "=" . $value;
        }
        return $buffer;
    }
    public function access_details()
    {
        $access_details = array();
        $access_details["domain"] = $_SERVER["HTTP_HOST"];
        $access_details["ip"] = $_SERVER["REMOTE_ADDR"];
        $access_details["directory"] = $this->path_translated();
        $access_details["server_hostname"] = $_SERVER["SERVER_NAME"];
        $access_details["server_hostname"] = $access_details["server_hostname"] ? $access_details["server_hostname"] : "Unknown";
        $access_details["server_ip"] = @gethostbyname($access_details["server_hostname"]);
        $access_details["server_ip"] = $access_details["server_ip"] ? $access_details["server_ip"] : "Unknown";
        foreach ($access_details as $key => $value) {
            $access_details[$key] = $access_details[$key] ? $access_details[$key] : "Unknown";
        }
        if ($this->valid_for_product_tiers) {
            $access_details["valid_for_product_tiers"] = $this->valid_for_product_tiers;
        }
        return $access_details;
    }
    public function path_translated()
    {
        $option = array("PATH_TRANSLATED", "ORIG_PATH_TRANSLATED", "SCRIPT_FILENAME", "DOCUMENT_ROOT", "APPL_PHYSICAL_PATH");
        foreach ($option as $key) {
            if (!isset($_SERVER[$key]) || strlen(trim($_SERVER[$key])) <= 0) {
                continue;
            }
            if ($this->is_windows() && strpos($_SERVER[$key], "\\")) {
                return @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], "\\"));
            }
            return @substr($_SERVER[$key], 0, @strrpos($_SERVER[$key], "/"));
        }
        return false;
    }
    public function server_addr()
    {
        $options = array("SERVER_ADDR", "LOCAL_ADDR");
        foreach ($options as $key) {
            if (isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }
        return false;
    }
    public function use_fsockopen($url, $querystring)
    {
        if (!function_exists("fsockopen")) {
            return false;
        }
        $url = parse_url($url);
        $fp = @fsockopen($url["host"], $this->remote_port, $errno, $errstr, $this->remote_timeout);
        if (!$fp) {
            return false;
        }
        $header = "POST " . $url["path"] . " HTTP/1.0\r\n";
        $header .= "Host: " . $url["host"] . "\r\n";
        $header .= "Content-type: application/x-www-form-urlencoded\r\n";
        $header .= "User-Agent: Superliver (http://superliver.ru)\r\n";
        $header .= "Content-length: " . @strlen($querystring) . "\r\n";
        $header .= "Connection: close\r\n\r\n";
        $header .= $querystring;
        $result = false;
        fputs($fp, $header);
        while (!feof($fp)) {
            $result .= fgets($fp, 1024);
        }
        fclose($fp);
        if (strpos($result, "200") === false) {
            return false;
        }
        $result = explode("\r\n\r\n", $result, 2);
        if (!$result[1]) {
            return false;
        }
        return $result[1];
    }
    public function use_curl($url, $querystring)
    {
        if (!function_exists("curl_init")) {
            return false;
        }
        $curl = curl_init();
        $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
        $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: ";
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, "Superliver (http://superliver.ru)");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->remote_timeout);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->remote_timeout);
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        if ((int) $info["http_code"] != 200) {
            return false;
        }
        return $result;
    }
    public function use_fopen($url, $querystring)
    {
        if (!function_exists("file_get_contents")) {
            return false;
        }
        return @file_get_contents((string) $url . "?" . $querystring);
    }
    public function is_windows()
    {
        return strtolower(substr(PHP_OS, 0, 3)) == "win";
    }
    public function getIpLocal()
    {
        if ($this->server_addr() === "127.0.0.1") {
            return true;
        }
        return false;
    }
    public function checkServer()
    {
        return true;
    }
    public function checkLicense()
    {
        return true;
    }
    public function requestLicense()
    {
        $this->init();
        $this->load->language("sale/ompro");
        $json = array();
        if (!$this->user->hasPermission("modify", $this->mod_path)) {
            $json["error"] = $this->language->get("error_permission");
        } else {
            if (isset($this->request->post["sale_resourse"])) {
                $this->sale_resourse = $this->request->post["sale_resourse"];
            }
            if (isset($this->request->post["order_id"])) {
                $this->sale_order_id = $this->request->post["order_id"];
            }
            if (isset($this->request->post["test_domain"])) {
                $this->sale_test_domain = $this->request->post["test_domain"];
            }
            if (isset($this->request->post["order_id"])) {
                $this->sale_email = $this->request->post["email"];
            }
            $this->protect();
            $result = $this->fetch_new_local_key(true);
            if ($this->errors) {
                $json["error"] = $this->errors;
            } else {
                $json["error"] = false;
            }
            if (!$json["error"] && $result) {
                $json["license_key"] = base64_decode($result);
                $json["success"] = $this->language->get("text_success_get_license");
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function activate()
    {
        $this->init();
        $this->load->language("common/footer");
        $this->ocversion = sprintf($this->language->get("text_version"), VERSION);
        $json = array();
        if (!$this->user->hasPermission("modify", $this->mod_path)) {
            $json["error"] = $this->language->get("error_permission");
        } else {
            if (isset($this->request->post["license_key"])) {
                $this->db->query("UPDATE `" . DB_PREFIX . "licenses_km` SET `value`='',`license_key`='' WHERE `key`= 'local_key' AND `p_code`= '" . $this->product_code . "'");
                $license_key = trim($this->request->post["license_key"]);
                $this->license_key = $license_key;
                $this->validateLkey();
                $json["error"] = false;
                if ($this->errors) {
                    $json["error"] = $this->errors;
                } else {
                    $this->db->query("UPDATE `" . DB_PREFIX . "licenses_km` set `license_key`='" . $license_key . "' WHERE `key`= 'local_key' AND `p_code`= '" . $this->product_code . "' ");
                    if ($this->db->countAffected() == 0) {
                        $json["error"] = $this->status_messages["local_key_not_read"];
                    }
                    if (!$json["error"]) {
                        $json["success"] = $this->language->get("text_success_license");
                    }
                }
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function license()
    {
        $data = $this->init();
        $data["heading_title"] = $this->language->get("heading_license") . '+';
        $this->document->setTitle($this->language->get("heading_license"));
        $data["strtoken"] = $strtoken = $this->strToken();
        if (isset($this->error["warning"])) {
            $data["error_warning"] = $this->error["warning"];
        } else {
            if (isset($this->session->data["warning"])) {
                $data["error_warning"] = $this->session->data["warning"];
                unset($this->session->data["warning"]);
            } else {
                $data["error_warning"] = "";
            }
        }
        if (isset($this->session->data["success"])) {
            $data["success"] = $this->session->data["success"];
            unset($this->session->data["success"]);
        } else {
            $data["success"] = "";
        }
        $data["breadcrumbs"] = array();
        $data["breadcrumbs"][] = "<li class=\" \"><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
        $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/license", $strtoken, true) . "\" >" . $data["heading_license"] . "</a></li>";
        $query = $this->db->query("SELECT `license_key` FROM `" . DB_PREFIX . "licenses_km` WHERE `key`= 'local_key' AND `p_code`= '" . $this->product_code . "'");
        $this->license_key = isset($query->row["license_key"]) ? $query->row["license_key"] : "";
        $data["license_key"] = $this->license_key;
        $data["btn_activate_attr"] = "";
        if ($data["license_key"]) {
            $data["btn_activate_attr"] = "disabled";
        }
        $access_details = $this->access_details();
        $data["domain"] = $access_details["domain"];
        $data["index_route"] = $this->index_route;
        $data["sale_resourses"] = explode(",", $data["text_sale_resourse_array"]);
        $data["config_email"] = $this->config->get("config_email") ? $this->config->get("config_email") : "";
        $data["header"] = $this->load->controller("sale/ompro_header");
        $data["footer"] = $this->load->controller("sale/ompro_footer");
        $data["ocversion"] = $this->ompro->ocversion;
        $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
        $this->response->setOutput($this->load->view("sale/ompro/ompro_license" . $ending, $data));
    }
    public function init()
    {
        if (!is_object($this->ompro)) {
            $this->load->library("ompro/ompro");
            $this->custom_methods = $this->ompro->custom_methods;
        }
        return $this->load->language("sale/ompro");
    }
    public function orders()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $this->log_write = $this->ompro->getAdminLogSql();
        if ($this->log_write) {
            $this->log->write("-- Method orders START --");
        }
        if (!$this->checkLicense()) {
            if ($this->log_write) {
                $this->log->write("* checkLicense false -> redirect to license");
            }
            $this->response->redirect($this->url->link($this->mod_path . "/license", $strtoken, true));
        } else {
            if (isset($this->request->get["pageid"])) {
                $pageid = $this->request->get["pageid"];
            } else {
                $this->request->get["pageid"] = $this->ompro->getFirstPageID();
                $pageid = $this->request->get["pageid"];
                if ($this->log_write) {
                    $this->log->write("getFirstPageID = " . $pageid);
                }
            }
            $this->document->setTitle($this->language->get("heading_title"));
            $data["heading_title"] = $this->language->get("heading_title") . '+';
            if (isset($this->error["warning"])) {
                $data["error_warning"] = $this->error["warning"];
            } else {
                $data["error_warning"] = "";
            }
            if (isset($this->session->data["success"])) {
                $data["success"] = $this->session->data["success"];
                unset($this->session->data["success"]);
            } else {
                $data["success"] = "";
            }
            $data["pageid"] = $pageid;
            $data["breadcrumbs"] = array();
            $data["breadcrumbs"][] = "<li class=\" \"><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
            $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/orders", $strtoken . "&pageid=" . $pageid, true) . "\" >" . $data["heading_title"] . "</a></li>";
            $data["header"] = $this->load->controller("sale/ompro_header_order");
            $data["footer"] = $this->load->controller("sale/ompro_footer");
            $data["ocversion"] = $this->ompro->ocversion;
            $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
            if ($this->log_write) {
                $this->log->write("-- Method orders END --");
            }
            $this->response->setOutput($this->load->view("sale/ompro/ompro_orders" . $ending, $data));
        }
    }
    public function content()
    {
        $data = $this->init();
        $this->log_write = $this->ompro->getAdminLogSql();
        if ($this->log_write) {
            $this->log->write("-- Method content START --");
        }
        $json = $order_ides = $filter_data = $setting_page = array();
        $json["content"] = "";
        $json["pageid"] = 0;
        $this->request->get["get_first_pageid"] = true;
        $to_edit = $expert_link = $this->request->get["get_first_pageid"];
        $all_orders_data = $this->ompro->getOrdersDataAll($order_ides, $to_edit, $expert_link);
        if (!empty($all_orders_data["param"]["filter_data"])) {
            $filter_data = $all_orders_data["param"]["filter_data"];
            $json["pageid"] = $filter_data["pageid"];
        }
        if (!empty($all_orders_data["param"]["setting_page"])) {
            $setting_page = $all_orders_data["param"]["setting_page"];
        }
        if (!empty($setting_page) && !empty($filter_data)) {
            $html = html_entity_decode($setting_page["constructor"], ENT_QUOTES, "UTF-8");
            $map_data = array();
            $total = 0;
            if (preg_match("/{pagination}/", $html) || preg_match("/{pagination_results}/", $html)) {
                $total = $this->ompro->getTotalOrders($filter_data);
            }
            $max_page = ceil($total / $filter_data["limit"]);
            if ($max_page < $filter_data["page"]) {
                $filter_data["page"] = $max_page;
                $filter_data["start"] = ($max_page - 1) * $filter_data["limit"];
            }
            $all_orders_data["param"]["filter_data"] = $filter_data;
            $all_orders_data["total"] = $total;
            if (preg_match("/id=\"order-map\"/", $html)) {
                $apikey = "";
                if (isset($setting_page["ymap_apikey"]) && $setting_page["ymap_apikey"]) {
                    $apikey = $setting_page["ymap_apikey"];
                }
                $map_data["apikey"] = $apikey;
                $map_data["height_map"] = isset($setting_page["height_map"]) ? $setting_page["height_map"] : 600;
                $map_data["shop_on_map"] = isset($setting_page["shop_on_map"]) ? $setting_page["shop_on_map"] : "";
                $map_data["map_zoom"] = isset($setting_page["map_zoom"]) ? $setting_page["map_zoom"] : 14;
                $map_data["map_center"] = isset($setting_page["map_center"]) ? $setting_page["map_center"] : 1;
                $map_data["shop_name"] = isset($setting_page["shop_name"]) && $setting_page["shop_name"] ? $setting_page["shop_name"] : $this->config->get("config_name");
                $map_data["map_orders"] = $this->getMapsJData($all_orders_data);
                if ($this->log_write) {
                    $this->log->write("- getMapsJData --");
                    $this->log->write($map_data["map_orders"]);
                    $this->log->write("");
                }
                $shop_adress = isset($setting_page["shop_adress"]) ? $setting_page["shop_adress"] : $this->config->get("config_adress");
                $shop_coords = $this->geocode($shop_adress, $apikey);
                if (!empty($shop_coords)) {
                    $map_data["shop_coords"] = $shop_coords;
                } else {
                    $map_data["shop_coords"] = array("55.754728", "37.6194445");
                }
                $map_data["shop_ballooncontent"] = "<b>" . $map_data["shop_name"] . "</b><br/>" . $shop_adress;
            }
            $css = "";
            if (isset($setting_page["css"]) && !empty($setting_page["css"])) {
                $css = "<style type=\"text/css\" title=\"ompro\">" . str_replace(array("\r\n", "\r", "\n"), "", preg_replace("/[\\s]{2,}/", " ", trim($setting_page["css"]))) . "</style>";
            }
            $script = "";
            if (isset($setting_page["script"]) && !empty($setting_page["script"])) {
                $script = "<script type=\"text/javascript\"><!--" . str_replace(array("\r\n", "\r", "\n"), "", preg_replace("/[\\s]{2,}/", " ", trim($setting_page["script"]))) . "--></script>";
            }
            $constructor = isset($this->request->get["constructor"]) ? true : false;
            $json["css_script"] = "";
            if ($constructor) {
                $json["css_script"] = $css . $script;
            } else {
                $html .= $css . $script;
            }
            $json["map_data"] = $map_data;
            $json["filter_data"] = $filter_data;
            $json["content"] = $this->constructHTML($html, $all_orders_data);
        } else {
            $json["content"] = "";
            if ($this->log_write) {
                $this->log->write("** setting_page = empty!");
            }
        }
        if ($this->log_write) {
            $this->log->write("-- Method content END --");
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function admin()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $data["has_permission"] = false;
        if ($this->user->hasPermission("modify", "sale/ompro")) {
            $data["has_permission"] = true;
        } else {
            $this->response->redirect($this->url->link("sale/ompro/orders", $strtoken, true));
        }
        $this->document->setTitle($this->language->get("heading_global_title"));
        if (!$this->checkLicense()) {
            $this->response->redirect($this->url->link($this->mod_path . "/license", $strtoken, true));
        } else {
            $data["log_sql"] = $this->log_write = $this->ompro->getAdminLogSql();
            $this->session->data["omanager_page"] = "admin";
            $data["cancel"] = $this->url->link("sale/ompro/orders", $strtoken, true);
            $data["action"] = $this->url->link("sale/ompro/admin", $strtoken, true);
            $data["add_indexes"] = $this->url->link("sale/ompro_helper/add_indexes", $strtoken, true);
            $data["all_backup"] = $this->url->link("sale/ompro_helper/allSettingBackup", $strtoken, true);
            $data["all_restore"] = $this->url->link("sale/ompro_helper/allSettingRestore", $strtoken, true);
            $data["warning"] = "";
            if (version_compare(PHP_VERSION, "5.6.0", ">=")) {
                if (!file_exists(DIR_SYSTEM . "library/ompro/mpdf/vendor/autoload.php")) {
                    $data["warning"] = sprintf($this->language->get("text_mpdf_warning"), "MPDF v.8.0");
                }
            } else {
                if (!file_exists(DIR_SYSTEM . "library/ompro/mpdf6/mpdf.php")) {
                    $data["warning"] = sprintf($this->language->get("text_mpdf_warning"), "mPDF v.6.1");
                }
            }
            if ($this->request->server["REQUEST_METHOD"] == "POST" && !empty($this->request->post["settings"]) && $this->validate()) {
                $this->ompro->editAdminSetting($this->request->post["settings"]);
                $this->session->data["success"] = $this->language->get("text_success_editor");
                $this->response->redirect($this->url->link("sale/ompro/admin", $strtoken, true));
            }
            $data["breadcrumbs"] = array();
            $data["breadcrumbs"][] = "<li><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
            $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/admin", $strtoken, true) . "\" >" . $this->language->get("heading_global_title") . "</a></li>";
            if (isset($this->error["warning"])) {
                $data["error_warning"] = $this->error["warning"];
            } else {
                if (isset($this->session->data["error_warning"])) {
                    $data["error_warning"] = $this->session->data["error_warning"];
                    unset($this->session->data["error_warning"]);
                } else {
                    $data["error_warning"] = "";
                }
            }
            if (isset($this->session->data["success"])) {
                $data["success"] = $this->session->data["success"];
                unset($this->session->data["success"]);
            } else {
                $data["success"] = "";
            }
            $this->load->model("user/user_group");
            $user_groups = $this->model_user_user_group->getUserGroups();
            $data["user_groups"] = array();
            foreach ($user_groups as $user_group) {
                $data["user_groups"][] = array("user_group_id" => $user_group["user_group_id"], "name" => $user_group["name"]);
            }
            $notify_targets = array("new_order_admin", "new_order_customer", "history", "reward", "transaction", "new_order_manager", "new_order_courier", "target_manager", "target_courier");
            $data["notify_target"] = array();
            foreach ($notify_targets as $val) {
                $data["notify_target"][$val] = array("key" => $val, "name" => $this->language->get("notify_" . $val));
            }
            $types = array("mail", "sms", "tlgrm");
            foreach ($types as $type) {
                $data[$type . "_templates"] = $this->ompro->getAllTemplatesListByType($type);
            }
            $data["settings"] = $this->ompro->getAdminSetting();
            $data["settings"]["tlgrm_bot_token"] = isset($data["settings"]["tlgrm_bot_token"]) ? $data["settings"]["tlgrm_bot_token"] : "";
            $data["settings"]["tlgrm_admin_ides"] = isset($data["settings"]["tlgrm_admin_ides"]) ? $data["settings"]["tlgrm_admin_ides"] : "";
            $ompro_tables_list = array("ompro_admin_setting", "ompro_fields_added", "ompro_fields_setting", "ompro_group_setting", "ompro_tpl_block", "ompro_tpl_comment", "ompro_tpl_excel_orders", "ompro_tpl_excel_orders_products", "ompro_tpl_filter", "ompro_tpl_filter_product", "ompro_tpl_history", "ompro_tpl_mail", "ompro_tpl_option", "ompro_tpl_orders", "ompro_tpl_pages", "ompro_tpl_page_block", "ompro_tpl_print_orders", "ompro_tpl_print_orders_table", "ompro_tpl_print_products_table", "ompro_tpl_product", "ompro_tpl_sms", "ompro_tpl_tlgrm");
            $data["ompro_tables"] = array();
            foreach ($ompro_tables_list as $table) {
                $data["ompro_tables"][] = DB_PREFIX . $table;
            }
            $data["header"] = $this->load->controller("sale/ompro_header");
            $data["footer"] = $this->load->controller("sale/ompro_footer");
            $data["ocversion"] = $this->ompro->ocversion;
            $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
            $this->response->setOutput($this->load->view("sale/ompro/ompro_admin" . $ending, $data));
        }
    }
    public function settingGroup()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $data["has_permission"] = false;
        if ($this->user->hasPermission("modify", "sale/ompro")) {
            $data["has_permission"] = true;
        } else {
            $this->response->redirect($this->url->link("sale/ompro/orders", $strtoken, true));
        }
        if (!$this->checkLicense()) {
            $this->response->redirect($this->url->link($this->mod_path . "/license", $strtoken, true));
        } else {
            if (isset($this->request->get["user_group_id"]) && $this->request->get["user_group_id"]) {
                $data["user_group_id"] = $user_group_id = $this->request->get["user_group_id"];
            } else {
                $data["user_group_id"] = $user_group_id = 0;
            }
            $this->defaultSettings($this->user->getGroupId());
            $this->document->setTitle($this->language->get("heading_setting_group"));
            if ($user_group_id) {
                $data["heading_title"] = $this->language->get("heading_setting_group");
                $data["user_group_name"] = $this->ompro->getUserGroupName($user_group_id);
                $data["action"] = $this->url->link("sale/ompro/settingGroup", $strtoken . "&user_group_id=" . $user_group_id, true);
                $data["default_setting"] = $this->url->link("sale/ompro/defaultSettings", $strtoken . "&user_group_id=" . $user_group_id, true);
                $data["cancel"] = $this->url->link("sale/ompro/orders", $strtoken, true);
                $datatype = "settingGroup";
                $data["backup_group_link"] = $this->url->link("sale/ompro_helper/settingGroupBackup", $strtoken . "&datatype=" . $datatype . "&user_group_id=" . $user_group_id, "SSL");
                if ($this->request->server["REQUEST_METHOD"] == "POST" && isset($this->request->get["user_group_id"]) && $this->validate()) {
                    if (isset($this->request->files["import"]) && is_uploaded_file($this->request->files["import"]["tmp_name"])) {
                        $content = file_get_contents($this->request->files["import"]["tmp_name"]);
                        if ($content && preg_match("/(a|O|s|b)\\:[0-9]*?((\\:((\\{?(.+)\\})|(\\\"(.+)\\\"\\;)))|(\\;))/", $content)) {
                            $content = unserialize($content);
                        }
                        if (is_array($content) && isset($content["datatype"]) && $content["datatype"] == $datatype) {
                            unset($content["datatype"]);
                            $this->request->post["settings"] = $content;
                        } else {
                            $this->request->post["settings"] = array();
                            $this->error["warning"] = $this->language->get("error_import_data");
                        }
                    }
                    if (!empty($this->request->post["settings"])) {
                        $this->ompro->editSettingGroup($user_group_id, $this->request->post["settings"]);
                        $this->session->data["success"] = $this->language->get("text_success_editor");
                        $this->response->redirect($this->url->link("sale/ompro/settingGroup", $strtoken . "&user_group_id=" . $user_group_id, true));
                    }
                }
                $data["backup_default"] = $this->url->link("sale/ompro_helper/initSettingBackup", $strtoken . "&user_group_id=" . $user_group_id . "&datatype=init_setting", "SSL");
                $data["restore_default"] = $this->url->link("sale/ompro_helper/initSettingRestore", $strtoken . "&user_group_id=" . $user_group_id, true);
                if (isset($this->error["warning"])) {
                    $data["error_warning"] = $this->error["warning"];
                } else {
                    if (isset($this->session->data["error_warning"])) {
                        $data["error_warning"] = $this->session->data["error_warning"];
                        unset($this->session->data["error_warning"]);
                    } else {
                        $data["error_warning"] = "";
                    }
                }
                if (isset($this->session->data["success"])) {
                    $data["success"] = $this->session->data["success"];
                    unset($this->session->data["success"]);
                } else {
                    $data["success"] = "";
                }
                $data["breadcrumbs"] = array();
                $data["breadcrumbs"][] = "<li class=\" \"><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
                $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/settingGroup", $strtoken . "&user_group_id=" . $user_group_id, true) . "\" >" . $data["heading_title"] . "</a></li>";
                $this->load->model("localisation/language");
                $data["languages"] = $this->model_localisation_language->getLanguages();
                $data["stores"] = $this->omproapi->getStores();
                $data["customer_groups"] = $this->omproapi->getCusromerGroups();
                $data["order_statuses"] = $this->omproapi->getOrderStatuses();
                $payments_list = $this->ompro->getSettingGroupPaymentsList();
                $data["order_payments"] = $this->omproapi->{$payments_list}();
                $shippings_list = $this->ompro->getSettingGroupShippingsList();
                $data["order_shippings"] = $this->omproapi->{$shippings_list}();
                $this->load->model("localisation/geo_zone");
                $results = $this->model_localisation_geo_zone->getGeoZones();
                $all_geo_zones = array();
                foreach ($results as $result) {
                    $all_geo_zones[] = array("id" => $result["geo_zone_id"], "text" => $result["name"]);
                }
                $data["shipping_geo_zones"] = $all_geo_zones;
                $data["payment_geo_zones"] = $data["shipping_geo_zones"];
                $user_group_param_list = array("stores", "customer_groups", "payment_geo_zones", "shipping_geo_zones", "order_statuses", "order_payments", "order_shippings");
                $data["user_group_params"] = array();
                foreach ($user_group_param_list as $param) {
                    $data["user_group_params"][] = array("id" => $param, "title" => $data["entry_" . $param], "help" => $data["entry_" . $param . "_help"], "data" => $data[$param]);
                }
                $settings = $this->ompro->getSettingGroup($user_group_id);
                $settings["payments_list"] = isset($settings["payments_list"]) && $settings["payments_list"] ? $settings["payments_list"] : "getPayments";
                $settings["shippings_list"] = isset($settings["shippings_list"]) && $settings["shippings_list"] ? $settings["shippings_list"] : "getShippings";
                $data["payment_methods_list"] = array();
                foreach ($this->omproapi->getPaymentsListMethods() as $method) {
                    if ($settings["payments_list"] == $method["value"]) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $data["payment_methods_list"][] = array("value" => $method["value"], "text" => $method["text"], "selected" => $selected);
                }
                $data["shipping_methods_list"] = array();
                foreach ($this->omproapi->getShippingsListMethods() as $method) {
                    if ($settings["shippings_list"] == $method["value"]) {
                        $selected = "selected";
                    } else {
                        $selected = "";
                    }
                    $data["shipping_methods_list"][] = array("value" => $method["value"], "text" => $method["text"], "selected" => $selected);
                }
                $user_group_targets = array("manager", "courier", "notarget");
                $data["user_group_targets"] = array();
                foreach ($user_group_targets as $target) {
                    $data["user_group_targets"][] = array("value" => $target, "text" => $data["text_target_user_group_" . $target]);
                }
                if (!isset($settings["group_target"])) {
                    $settings["group_target"] = "notarget";
                }
                if (isset($settings["pages"]) && is_array($settings["pages"])) {
                    foreach ($settings["pages"] as $id => $code) {
                        if (is_numeric($code)) {
                            $code = $this->ompro->getTemplateCode("pages", $code);
                        }
                        $settings["pages"][$id] = $code;
                    }
                }
                $setting_pages = $this->ompro->getPageList();
                $order_pages = array();
                foreach ($setting_pages as $page) {
                    $checked = "";
                    if (isset($settings["pages"]) && is_array($settings["pages"]) && in_array($page["code"], $settings["pages"])) {
                        $checked = "checked=\"checked\"";
                    }
                    $order_pages[$page["code"]] = array("template_id" => $page["template_id"], "code" => $page["code"], "name" => $page["name"], "description" => $page["description"], "checked" => $checked);
                }
                $data["order_pages"] = array();
                if (!empty($settings["pages"])) {
                    foreach ($settings["pages"] as $code) {
                        if (isset($order_pages[$code])) {
                            $data["order_pages"][] = $order_pages[$code];
                            unset($order_pages[$code]);
                        }
                    }
                }
                $data["order_pages"] = array_merge($data["order_pages"], $order_pages);
                $access_actions = array("change_status", "create_invoiceno", "edit_reward", "edit_comission");
                $data["access_actions"] = array();
                foreach ($access_actions as $action) {
                    $checked = "";
                    if (isset($settings["access_actions"]) && is_array($settings["access_actions"]) && in_array($action, $settings["access_actions"])) {
                        $checked = "checked=\"checked\"";
                    }
                    $data["access_actions"][] = array("action" => $action, "name" => $this->language->get("access_" . $action), "checked" => $checked);
                }
                $data["order_statuses"] = $this->omproapi->getOrderStatuses();
                foreach ($data["order_statuses"] as $status) {
                    foreach ($data["languages"] as $language) {
                        if (!empty($settings["comment_templates"])) {
                            if (!isset($settings["comment_templates"][$status["id"]][$language["language_id"]])) {
                                $settings["comment_templates"][$status["id"]][$language["language_id"]] = "";
                            }
                        } else {
                            $settings["comment_templates"][$status["id"]][$language["language_id"]] = "";
                        }
                    }
                    if (!isset($settings["order_statuses"][$status["id"]]["text_color_id"])) {
                        $settings["order_statuses"][$status["id"]]["text_color_id"] = "595959";
                    }
                    if (!isset($settings["order_statuses"][$status["id"]]["back_color_id"])) {
                        $settings["order_statuses"][$status["id"]]["back_color_id"] = "ffffff";
                    }
                }
                foreach ($data["order_payments"] as $payment) {
                    if (!isset($settings["order_payments"][$payment["id"]]["text_color_id"])) {
                        $settings["order_payments"][$payment["id"]]["text_color_id"] = "595959";
                    }
                    if (!isset($settings["order_payments"][$payment["id"]]["back_color_id"])) {
                        $settings["order_payments"][$payment["id"]]["back_color_id"] = "ffffff";
                    }
                }
                foreach ($data["order_shippings"] as $shipping) {
                    if (!isset($settings["order_shippings"][$shipping["id"]]["text_color_id"])) {
                        $settings["order_shippings"][$shipping["id"]]["text_color_id"] = "595959";
                    }
                    if (!isset($settings["order_shippings"][$shipping["id"]]["back_color_id"])) {
                        $settings["order_shippings"][$shipping["id"]]["back_color_id"] = "ffffff";
                    }
                }
                $data["scheduler_status"] = $scheduler_status = $this->omproapi->scheduler_status();
                $data["days_to_ship_dates"] = array();
                if ($scheduler_status && isset($settings["days_to_ship"]) && !empty($settings["days_to_ship"])) {
                    foreach ($settings["days_to_ship"] as $days_id => $colors) {
                        if (empty($colors["text_color_id"])) {
                            $settings["days_to_ship"][$days_id]["text_color_id"] = "595959";
                        }
                        if (empty($colors["back_color_id"])) {
                            $settings["days_to_ship"][$days_id]["back_color_id"] = "ffffff";
                        }
                        $settings["days_to_ship_dates"][$days_id] = date("d.m.Y", time() + $colors["days"] * 86400);
                    }
                }
                $data["settings"] = $settings;
                $filters_default = array();
                if (isset($settings["filters_default"])) {
                    $filters_default = $settings["filters_default"];
                }
                $setting_user_group = $settings;
                $data["filters_default"] = array();
                if (!empty($filters_default)) {
                    foreach ($filters_default as $filter_default) {
                        $filter_info = $this->ompro->getFilterTemplateByFilterId("filter", $filter_default["filter_id"]);
                        if ($filter_info) {
                            $html = $this->omproapi->createHTMLFilter($setting_user_group, $filter_info, $filter_reload = 0, $filter_default["default_val"], "filter");
                            $data["filters_default"][] = array("filter_id" => $filter_default["filter_id"], "default_val" => $filter_default["default_val"], "html" => $html);
                        }
                    }
                }
                $data["order_format_list"] = $this->omproapi->orderDataFormatTypeList();
                $data["product_format_list"] = $this->omproapi->productDataFormatTypeList();
                $data["order_format_method_list"] = $this->omproapi->orderDataFormatMethodList();
                $data["product_format_method_list"] = $this->omproapi->productDataFormatMethodList();
                $data["filters"] = $this->ompro->getAllTemplatesList("filter");
                $data["order_codes"] = $this->ompro->orderFieldsData("codes");
                $data["order_formats"] = array();
                $all_order_fields = $this->ompro->getOrderFieldsAll();
                foreach ($all_order_fields as $field) {
                    if (isset($settings["order_formats"][$field["key"]])) {
                        $data["order_formats"][$field["key"]] = $settings["order_formats"][$field["key"]];
                    }
                }
                $data["product_codes"] = $this->ompro->productFieldsData("codes");
                $data["product_formats"] = array();
                $all_product_fields = $this->ompro->getProductFieldsAll();
                foreach ($all_product_fields as $field) {
                    if (isset($settings["product_formats"][$field["key"]])) {
                        $data["product_formats"][$field["key"]] = $settings["product_formats"][$field["key"]];
                    }
                }
            }
            $data["header"] = $this->load->controller("sale/ompro_header");
            $data["footer"] = $this->load->controller("sale/ompro_footer");
            $data["ocversion"] = $this->ompro->ocversion;
            $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
            $this->response->setOutput($this->load->view("sale/ompro/ompro_group_setting" . $ending, $data));
        }
    }
    public function defaultSettings($current_user_group_id = 0)
    {
        $this->init();
        if (isset($this->request->get["user_group_id"])) {
            $request_user_group_id = $this->request->get["user_group_id"];
        } else {
            $request_user_group_id = 0;
        }
        if ($current_user_group_id && $current_user_group_id == $request_user_group_id && !$this->ompro->checkSettingGroup($current_user_group_id)) {
            $user_group_id = $current_user_group_id;
        } else {
            if ($current_user_group_id && $current_user_group_id !== $request_user_group_id && !$this->ompro->checkSettingGroup($request_user_group_id) && $this->validate()) {
                $user_group_id = $request_user_group_id;
            } else {
                if (!$current_user_group_id && $request_user_group_id && $this->validate()) {
                    $user_group_id = $request_user_group_id;
                } else {
                    $user_group_id = 0;
                }
            }
        }
        if ($user_group_id) {
            $this->load->model("user/user_group");
            $this->model_user_user_group->addPermission($user_group_id, "access", "sale/ompro_helper");
            $this->model_user_user_group->addPermission($user_group_id, "access", "sale/ompro_widget");
            if (230 <= $this->ompro->ocversion) {
                $widget_list = array("activity", "chart", "customer", "online", "order", "recent", "sale");
                foreach ($widget_list as $widget) {
                    $route = "extension/dashboard/" . $widget;
                    if (!$this->user->hasPermission("access", $route)) {
                        $this->model_user_user_group->addPermission($user_group_id, "access", $route);
                    }
                }
            }
            $file = DIR_SYSTEM . "library/ompro/ompro_setting_group_default.settings";
            if (file_exists($file)) {
                $content = file_get_contents($file);
                if ($content && preg_match("/(a|O|s|b)\\:[0-9]*?((\\:((\\{?(.+)\\})|(\\\"(.+)\\\"\\;)))|(\\;))/", $content)) {
                    $content = unserialize($content);
                }
                if (is_array($content) && isset($content["datatype"]) && $content["datatype"] == "settingGroup") {
                    unset($content["datatype"]);
                    $this->ompro->editSettingGroup($user_group_id, $content);
                    $this->session->data["success"] = $this->language->get("text_success_default");
                    $this->response->redirect($this->url->link("sale/ompro/settingGroup", $this->strToken() . "&user_group_id=" . $user_group_id, true));
                    return NULL;
                }
            }
        }
    }
    public function fields()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $data["has_permission"] = false;
        if ($this->user->hasPermission("modify", "sale/ompro")) {
            $data["has_permission"] = true;
        } else {
            $this->response->redirect($this->url->link("sale/ompro/orders", $strtoken, true));
        }
        if (!$this->checkLicense()) {
            $this->response->redirect($this->url->link($this->mod_path . "/license", $strtoken, true));
        } else {
            if (isset($this->request->get["get_page"])) {
                $data["page"] = $page = $this->request->get["get_page"];
            } else {
                $data["page"] = $page = "order_fields";
            }
            $data["action"] = $this->url->link("sale/ompro/fields", $strtoken . "&get_page=" . $page, true);
            $data["cancel"] = $this->url->link("sale/ompro/orders", $strtoken, true);
            $data["log_sql"] = $this->ompro->getAdminLogSql();
            $data["backup_link"] = $this->url->link("sale/ompro_helper/fieldsBackup", $strtoken . "&type=" . $page, true);
            if ($this->request->server["REQUEST_METHOD"] == "POST" && $this->validate()) {
                if (isset($this->request->files["import"]) && is_uploaded_file($this->request->files["import"]["tmp_name"])) {
                    $content = file_get_contents($this->request->files["import"]["tmp_name"]);
                    if ($content && preg_match("/(a|O|s|b)\\:[0-9]*?((\\:((\\{?(.+)\\})|(\\\"(.+)\\\"\\;)))|(\\;))/", $content)) {
                        $content = unserialize($content);
                    }
                    if (is_array($content) && isset($content["fields"]) && isset($content["datatype"]) && $content["datatype"] == $page) {
                        $this->request->post["fields"] = $content["fields"];
                    } else {
                        $this->request->post["fields"] = array();
                        $this->error["warning"] = $this->language->get("error_import_data");
                    }
                }
                if (!empty($this->request->post["fields"])) {
                    $this->ompro->editFieldsSetting($page, $this->request->post["fields"]);
                    $this->session->data["success"] = $this->language->get("text_success_editor");
                    $this->response->redirect($data["action"]);
                }
            }
            if (isset($this->error["warning"])) {
                $data["error_warning"] = $this->error["warning"];
            } else {
                if (isset($this->session->data["error_warning"])) {
                    $data["error_warning"] = $this->session->data["error_warning"];
                    unset($this->session->data["error_warning"]);
                } else {
                    $data["error_warning"] = "";
                }
            }
            if (isset($this->session->data["success"])) {
                $data["success"] = $this->session->data["success"];
                unset($this->session->data["success"]);
            } else {
                $data["success"] = "";
            }
            $order_fields_page_list = array("order_fields", "order_as_fields", "order_simple_fields");
            $product_fields_page_list = array("product_fields", "product_as_fields");
            $fields = $check_fields = $check_keys = array();
            $data["boxtitle"] = "";
            $data["subtitle"] = $data["boxtitle"];
            $data["title"] = $data["subtitle"];
            if (in_array($page, $order_fields_page_list)) {
                $data["title"] = $data["heading_fields_title_order_fields"];
                $data["subtitle"] = $data["heading_fields_subtitle_" . $page];
                $data["boxtitle"] = $data["heading_fields_boxtitle_" . $page];
                $data["text_order_sql"] = $this->language->get("text_order_sql1");
                $data["simple_status"] = $this->omproapi->simpleStatus();
                if ($data["simple_status"]) {
                    $data["text_order_sql"] = $this->language->get("text_order_sql2");
                }
            } else {
                if (in_array($page, $product_fields_page_list)) {
                    $data["title"] = $data["heading_fields_title_product_fields"];
                    $data["subtitle"] = $data["heading_fields_subtitle_" . $page];
                    $data["boxtitle"] = $data["heading_fields_boxtitle_" . $page];
                }
            }
            $this->document->setTitle($data["title"]);
            $data["breadcrumbs"] = array();
            $data["breadcrumbs"][] = "<li><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
            $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/fields", $strtoken . "&get_page=" . $page, true) . "\" >" . $data["title"] . "</a></li>";
            $data["header"] = $this->load->controller("sale/ompro_header");
            $data["footer"] = $this->load->controller("sale/ompro_footer");
            $data["ocversion"] = $this->ompro->ocversion;
            $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
            $this->load->model("user/user_group");
            $user_groups = $this->model_user_user_group->getUserGroups();
            $data["user_groups"] = array();
            foreach ($user_groups as $user_group) {
                $data["user_groups"][] = array("user_group_id" => $user_group["user_group_id"], "name" => $user_group["name"]);
            }
            $fields_added = array();
            if ($page == "order_fields") {
                $data["help_item"] = "item_11";
                $data["sql_field_param_list"] = $this->omproapi->sqlAddFieldParamList();
                $data["db_prefix"] = DB_PREFIX;
                $fields = $this->ompro->getOrderFields();
                $fields_added = $this->ompro->getTableFieldsAdded("'order'");
                $setting_order_keys = array();
                foreach ($fields as $field) {
                    if (isset($field["key"]) && $field["key"]) {
                        $setting_order_keys[] = $field["key"];
                    }
                }
                $dbTable = "order";
                $exist_order_fields = $this->ompro->getColumnsFromTable($dbTable);
                $exist_order_keys = array();
                foreach ($exist_order_fields as $key) {
                    $exist_order_keys[] = $key;
                    if (!in_array($key, $setting_order_keys)) {
                        $fields[] = array("data_group_id" => "other_info", "dbTable" => $dbTable, "key" => $key, "name" => $key);
                    }
                }
                foreach ($fields as $key => $field) {
                    if (isset($field["key"]) && $field["key"]) {
                        if (!in_array($field["key"], $exist_order_keys)) {
                            unset($fields[$key]);
                        }
                    } else {
                        unset($fields[$key]);
                    }
                }
                $check_fields = array_merge($this->ompro->getOrderAsFields(), $this->ompro->getOrderSimpleFields());
            } else {
                if ($page == "order_as_fields") {
                    $data["help_item"] = "item_12";
                    $fields = $this->ompro->getOrderAsFields();
                    $check_fields = array_merge($this->ompro->getOrderFields(), $this->ompro->getOrderSimpleFields());
                } else {
                    if ($page == "order_simple_fields") {
                        $data["help_item"] = "item_13";
                        $fields = $this->ompro->getOrderSimpleFields();
                        $check_fields = array_merge($this->ompro->getOrderFields(), $this->ompro->getOrderAsFields());
                        $setting_simple_keys = array();
                        foreach ($fields as $field) {
                            if (isset($field["key"]) && $field["key"]) {
                                $setting_simple_keys[] = $field["key"];
                            }
                        }
                        $dbTable = "order_simple_fields";
                        $exist_order_simple_fields = $this->omproapi->simpleFieldsDataList();
                        $exist_order_simple_keys = array();
                        foreach ($exist_order_simple_fields as $field) {
                            $exist_order_simple_keys[] = $field["key"];
                            if (!in_array($field["key"], $setting_simple_keys)) {
                                $fields[] = array("data_group_id" => "simple_custom_info", "dbTable" => $dbTable, "key" => $field["key"], "name" => $field["name"]);
                            }
                        }
                        foreach ($fields as $key => $field) {
                            if (isset($field["key"]) && $field["key"]) {
                                if (!in_array($field["key"], $exist_order_simple_keys)) {
                                    unset($fields[$key]);
                                }
                            } else {
                                unset($fields[$key]);
                            }
                        }
                        foreach ($exist_order_simple_fields as $field) {
                            foreach ($fields as $k => $f) {
                                if ($field["key"] == $f["key"] && $field["name"] !== $f["name"]) {
                                    $fields[$k]["name"] = $field["name"];
                                }
                            }
                        }
                    } else {
                        if ($page == "product_fields") {
                            $data["help_item"] = "item_14";
                            $data["sql_field_param_list"] = $this->omproapi->sqlAddFieldParamList();
                            $data["db_prefix"] = DB_PREFIX;
                            $product_fields_data = $this->ompro->productFieldsSettingData();
                            $fields = $product_fields_data["fields"];
                            $fields_added = $this->ompro->getTableFieldsAdded("'product', 'order_product'");
                            $check_fields = $this->ompro->getProductAsFields();
                        } else {
                            if ($page == "product_as_fields") {
                                $data["help_item"] = "item_15";
                                $fields = $this->ompro->getProductAsFields();
                                $check_fields = $this->ompro->getProductFields();
                            }
                        }
                    }
                }
            }
            foreach ($check_fields as $field) {
                $check_keys[] = $field["key"];
            }
            $data["values_api_list"] = $values_api_list = $this->omproapi->valuesApiMethodList();
            $data["values_option_list"] = $values_option_list = $this->ompro->getAllOptionsList();
            $data["eform_list"] = $eform_list = $this->omproapi->xEditInputTypeList();
            $data["ecustom_method_list"] = $ecustom_method_list = $this->omproapi->xEditCustomApiMethodList();
            $data["action_method_list"] = $action_method_list = $this->omproapi->xEditActionApiMethodList();
            $data["check_keys"] = implode(" ", $check_keys);
            $data["freeze_set_list"] = array();
            $data["exclude_process_list"] = $data["freeze_set_list"];
            $data["exclude_edit_list"] = $data["exclude_process_list"];
            if (in_array($page, $order_fields_page_list)) {
                $data["order_process_method_list"] = $process_method_list = $this->omproapi->orderDataProcessMethodList();
                $data["order_data_group_list"] = $this->omproapi->orderDataGroupList();
                $data["exclude_edit_list"] = $this->omproapi->excludeEditOrderFieldList();
                $data["exclude_process_list"] = $this->omproapi->excludeProcessOrderFieldList();
                $data["freeze_set_list"] = $freeze_set_list = $this->omproapi->freezeSetOrderFieldList();
                $data["fields"] = $this->prepareFieldsSettingData($data["order_data_group_list"], $fields, $process_method_list, $eform_list, $values_api_list, $values_option_list, $ecustom_method_list, $user_groups, $fields_added, $action_method_list, $freeze_set_list);
            } else {
                if (in_array($page, $product_fields_page_list)) {
                    $data["product_process_method_list"] = $this->omproapi->productDataProcessMethodList();
                    $data["product_data_group_list"] = $this->omproapi->productDataGroupList();
                    $data["exclude_edit_list"] = $this->omproapi->excludeEditProductFieldList();
                    $data["exclude_process_list"] = $this->omproapi->excludeProcessProductFieldList();
                    $data["exclude_op_m_sum_list"] = $this->omproapi->excludeOpManSumFieldList();
                    $data["freeze_set_list"] = $freeze_set_list = $this->omproapi->freezeSetProductFieldList();
                    $data["fields"] = $this->prepareFieldsSettingData($data["product_data_group_list"], $fields, $data["product_process_method_list"], $eform_list, $values_api_list, $values_option_list, $ecustom_method_list, $user_groups, $fields_added, $action_method_list, $freeze_set_list);
                }
            }
            if (in_array($page, $order_fields_page_list)) {
                $this->response->setOutput($this->load->view("sale/ompro/ompro_order_fields" . $ending, $data));
            } else {
                if (in_array($page, $product_fields_page_list)) {
                    $this->response->setOutput($this->load->view("sale/ompro/ompro_product_fields" . $ending, $data));
                }
            }
        }
    }
    public function prepareFieldsSettingData($group_list, $fields, $process_method_list = array(), $eform_list = array(), $values_api_list = array(), $values_option_list = array(), $ecustom_method_list = array(), $user_groups = array(), $fields_added = array(), $action_method_list = array(), $freeze_set_list = array())
    {
        $result = array();
        foreach ($fields as $field) {
            $dbTable = isset($field["dbTable"]) && $field["dbTable"] ? $field["dbTable"] : "";
            $key = isset($field["key"]) && $field["key"] ? $field["key"] : "";
            $name = isset($field["name"]) && $field["name"] ? $field["name"] : "";
            $sql = isset($field["sql"]) && $field["sql"] ? $field["sql"] : "";
            $eform = isset($field["eform"]) && $field["eform"] ? $field["eform"] : "";
            $reload_onsave = isset($field["reload_onsave"]) && $field["reload_onsave"] ? 1 : 0;
            $log = isset($field["log"]) && $field["log"] ? 1 : 0;
            $sum = isset($field["sum"]) && $field["sum"] ? 1 : 0;
            $incl = isset($field["incl"]) && $field["incl"] ? 1 : 0;
            $status = isset($field["status"]) && $field["status"] ? 1 : 0;
            $disabled = "";
            if (in_array($field["key"], $freeze_set_list)) {
                $disabled = "disabled";
            }
            $btn_drop = false;
            if (in_array($field["key"], $fields_added)) {
                $btn_drop = true;
            }
            $action_methods = array();
            foreach ($action_method_list as $method) {
                if (isset($field["action"]) && $field["action"] == $method["action"]) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                $action_methods[] = array("action" => $method["action"], "name" => $method["name"], "selected" => $selected);
            }
            $process_methods = array();
            foreach ($process_method_list as $method) {
                if (isset($field["process_method"]) && $field["process_method"] == $method["process_method"]) {
                    $selected = "selected";
                } else {
                    $selected = "";
                }
                $process_methods[] = array("process_method" => $method["process_method"], "name" => $method["name"], "selected" => $selected);
            }
            $simple_field = false;
            if ($dbTable == "order_simple_fields") {
                $simple_field = true;
            }
            $eforms = array();
            if (!$simple_field) {
                if ($dbTable == "ocustom" || $dbTable == "pcustom") {
                    foreach ($eform_list as $form) {
                        if ($form["id"] == "custom_api") {
                            if (isset($field["eform"]) && $field["eform"] == $form["id"]) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            $eforms[] = array("eform" => $form["id"], "name" => $form["name"], "selected" => $selected);
                            break;
                        }
                    }
                } else {
                    foreach ($eform_list as $form) {
                        if ($form["id"] !== "simple_custom") {
                            if (isset($field["eform"]) && $field["eform"] == $form["id"]) {
                                $selected = "selected";
                            } else {
                                $selected = "";
                            }
                            $eforms[] = array("eform" => $form["id"], "name" => $form["name"], "selected" => $selected);
                        }
                    }
                }
            } else {
                foreach ($eform_list as $form) {
                    if ($form["id"] == "simple_custom" || $form["id"] == "custom_api") {
                        if (isset($field["eform"]) && $field["eform"] == $form["id"]) {
                            $selected = "selected";
                        } else {
                            $selected = "";
                        }
                        $eforms[] = array("eform" => $form["id"], "name" => $form["name"], "selected" => $selected);
                    }
                }
            }
            $eparam_title = "";
            $eparam_plholder = "";
            $api_valuelist = $option_valuelist = $ecustom_api = array();
            if (isset($field["eform"]) && !$simple_field) {
                if ($field["eform"] == "datetime") {
                    $eparam_title = $this->language->get("text_format_datetime_info");
                    $eparam_plholder = "dd.mm.yyyy||hh:ii";
                } else {
                    if ($field["eform"] == "inputmask") {
                        $eparam_title = $this->language->get("text_filter_info_title_inputmask");
                        $eparam_plholder = "+375 (99) 999-99-99";
                    } else {
                        if ($field["eform"] == "selector_api" || $field["eform"] == "checklist_api") {
                            foreach ($values_api_list as $value) {
                                if (isset($field["eparam"]) && $field["eparam"] == $value["key"]) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                $api_valuelist[] = array("key" => $value["key"], "name" => $value["name"], "selected" => $selected);
                            }
                        } else {
                            if ($field["eform"] == "selector_option" || $field["eform"] == "checklist_option") {
                                foreach ($values_option_list as $value) {
                                    if (is_numeric($field["eparam"])) {
                                        $field["eparam"] = $this->ompro->getTemplateCode("option", $field["eparam"]);
                                    }
                                    if (isset($field["eparam"]) && $field["eparam"] == $value["key"]) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                                    $option_valuelist[] = array("key" => $value["key"], "name" => $value["name"], "selected" => $selected);
                                }
                            } else {
                                if ($field["eform"] == "custom_api") {
                                    foreach ($ecustom_method_list as $method) {
                                        if (isset($field["eparam"]) && $field["eparam"] == $method["key"]) {
                                            $selected = "selected";
                                        } else {
                                            $selected = "";
                                        }
                                        $ecustom_api[] = array("key" => $method["key"], "name" => $method["name"], "selected" => $selected);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $data_group = array();
            foreach ($group_list as $group) {
                if (isset($field["data_group_id"]) && $field["data_group_id"] == $group["id"]) {
                    $active = "active";
                    $checked = "checked";
                } else {
                    $active = "";
                    $checked = "";
                }
                $data_group[] = array("id" => $group["id"], "icon" => $group["icon"], "text" => $group["text"], "active" => $active, "checked" => $checked);
            }
            $edit_access = array();
            foreach ($user_groups as $user_group) {
                $checked = "";
                if (isset($field["edit_access"]) && in_array($user_group["user_group_id"], $field["edit_access"])) {
                    $checked = "checked=\"checked\"";
                }
                $edit_access[] = array("user_group_id" => $user_group["user_group_id"], "name" => $user_group["name"], "checked" => $checked);
            }
            $result[] = array("dbTable" => $dbTable, "disabled" => $disabled, "key" => $key, "name" => $name, "incl_checked" => $incl ? "checked" : "", "status_checked" => $status ? "checked" : "", "simple_field" => $simple_field, "sql" => $sql, "process_methods" => $process_methods, "eform" => $eform, "eforms" => $eforms, "action_methods" => $action_methods, "reload_onsave_checked" => $reload_onsave ? "checked" : "", "log_checked" => $log ? "checked" : "", "sum_checked" => $sum ? "checked" : "", "eparam" => isset($field["eparam"]) && $field["eparam"] ? $field["eparam"] : "", "eparam_title" => $eparam_title, "eparam_plholder" => $eparam_plholder, "api_valuelist" => $api_valuelist, "option_valuelist" => $option_valuelist, "ecustom_api" => $ecustom_api, "edit_access" => $edit_access, "data_group" => $data_group, "btn_drop" => $btn_drop);
        }
        return $result;
    }
    public function editAdminLogSql()
    {
        $this->init();
        $json = array();
        if (isset($this->request->get["log_sql"]) && $this->validate()) {
            $this->ompro->editAdminLogSql($this->request->get["log_sql"]);
            $json["success"] = $this->language->get("text_success_editor");
        } else {
            $json["error"] = $this->error["warning"];
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function editTableField()
    {
        $this->init();
        if (isset($this->request->post) && $this->validate()) {
            $post = $this->request->post;
            if (isset($post["table"]) && isset($post["field"]) && isset($post["action"])) {
                $table = $post["table"];
                $field = $post["field"];
                $action = $post["action"];
                if ($action == "add" && isset($post["params"])) {
                    $existColumns = $this->ompro->getColumnsFromTable($table);
                    if (!in_array(strtolower($field), $existColumns)) {
                        $query = $this->db->query("ALTER TABLE `" . DB_PREFIX . $table . "` ADD `" . $field . "` " . $post["params"] . "");
                        if ($this->db->countAffected()) {
                            $sql = "SELECT id FROM `" . DB_PREFIX . "ompro_fields_added` WHERE `table` = '" . $table . "' AND `field` = '" . $field . "';";
                            $query = $this->db->query($sql);
                            if (!$query->num_rows) {
                                $sql = "INSERT INTO `" . DB_PREFIX . "ompro_fields_added` SET `table` = '" . $table . "', `field` = '" . $field . "'";
                                $query = $this->db->query($sql);
                                if ($this->db->countAffected()) {
                                    $this->session->data["success"] = sprintf($this->language->get("text_success_add_field"), $field, DB_PREFIX . $table);
                                } else {
                                    $this->session->data["error_warning"] = $this->language->get("text_error_sql");
                                }
                            } else {
                                $this->session->data["error_warning"] = sprintf($this->language->get("text_error_field_exist"), $field, DB_PREFIX . "ompro_fields_added");
                            }
                        } else {
                            $this->session->data["error_warning"] = $this->language->get("text_error_sql");
                        }
                    } else {
                        $this->session->data["error_warning"] = sprintf($this->language->get("text_error_field_exist"), $field, DB_PREFIX . $table);
                    }
                }
                if ($action == "drop") {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . $table . "` DROP `" . $field . "`");
                    $this->db->query("DELETE FROM `" . DB_PREFIX . "ompro_fields_added` WHERE `table` = '" . $table . "' AND `field` = '" . $field . "' ");
                    $this->session->data["success"] = sprintf($this->language->get("text_success_drop_field"), $field, DB_PREFIX . $table);
                }
            }
        } else {
            $this->session->data["error_warning"] = $this->error["warning"];
        }
    }
    public function constructHTML($html = "", $all_orders_data)
    {
        $setting_user_group = $all_orders_data["param"]["setting_user_group"];
        $setting_page = $all_orders_data["param"]["setting_page"];
        $filter_data = $all_orders_data["param"]["filter_data"];
        $total = $all_orders_data["total"];
        if (!isset($this->request->get["constructor"])) {
            $pattern = "/\\[\\[([^\\[\\]]*?)\\{pageValueVar_\\S+\\}(.*?)\\]\\]/s";
            if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (preg_match("/\\{pageValueVar_\\S+\\}/", $match[0], $res)) {
                        $key = str_replace(array("{pageValueVar_", "}"), "", $res[0]);
                        $var_val = $this->omproapi->pageValueVars($key, $all_orders_data);
                        $html = $this->ompro->replaceMatches(array($match), $html, array("[[", "]]"), $res[0], $var_val);
                    }
                }
            }
        }
        $html_content_vars_list = $this->omproapi->pageHtmlElemVarsList();
        foreach ($html_content_vars_list as $res) {
            foreach ($res["vars"] as $var_key => $n) {
                $pattern = "/\\[\\[([^\\[\\]]*?)\\{" . $var_key . "\\}(.*?)\\]\\]/s";
                if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $content = $this->omproapi->pageHtmlElemVars($var_key, $all_orders_data);
                        $html = str_replace($match[0], $content, $html);
                    }
                }
            }
        }
        if (preg_match_all("/\\{filterID=\\S+\\}/", $html, $filtertpls, PREG_SET_ORDER)) {
            foreach ($filtertpls as $match) {
                $filter_id = trim(str_replace(array("{filterID=", "}"), "", $match[0]));
                if ($filter_id) {
                    $filter_info = $this->cache->get("ompro.template.filter_" . $filter_id);
                    if (!$filter_info) {
                        $filter_info = $this->ompro->getFilterTemplateByFilterId("filter", $filter_id);
                        $this->cache->set("ompro.template.filter_" . $filter_id, $filter_info);
                    }
                    if ($filter_info && $setting_page) {
                        $filters_top = json_decode(html_entity_decode($setting_page["filters_top"], ENT_QUOTES, "UTF-8"), true);
                        if (is_array($filters_top) && isset($filters_top[$filter_id])) {
                            $filter_info = array_replace($filter_info, $filters_top[$filter_id]);
                        }
                    }
                    $filter_reload = $filter_info["filter_reload"];
                    $filter_value = isset($filter_data["filters"][$filter_id]) ? $filter_data["filters"][$filter_id] : "";
                    $replace = html_entity_decode($this->omproapi->createHTMLFilter($setting_user_group, $filter_info, $filter_reload, $filter_value, "filter"), ENT_QUOTES, "UTF-8");
                    $html = str_replace($match[0], $replace, $html);
                }
            }
        }
        if (preg_match_all("/\\{filterProductID=\\S+\\}/", $html, $filter_product_tpls, PREG_SET_ORDER)) {
            foreach ($filter_product_tpls as $match) {
                $filter_id = trim(str_replace(array("{filterProductID=", "}"), "", $match[0]));
                if ($filter_id) {
                    $filter_info = $this->ompro->getFilterTemplateByFilterId("filter_product", $filter_id);
                    if ($filter_info && $setting_page) {
                        $filters_product = json_decode(html_entity_decode($setting_page["filters_product"], ENT_QUOTES, "UTF-8"), true);
                        if (is_array($filters_product) && isset($filters_product[$filter_id])) {
                            $filter_info = array_replace($filter_info, $filters_product[$filter_id]);
                        }
                    }
                    $filter_value = isset($filter_data["filters"][$filter_id]) ? $filter_data["filters"][$filter_id] : "";
                    $replace = html_entity_decode($this->omproapi->createHTMLFilter($setting_user_group, $filter_info, "0", $filter_value, "filter_product"), ENT_QUOTES, "UTF-8");
                    $html = str_replace($match[0], $replace, $html);
                }
            }
        }
        if (preg_match_all("/\\{orderTPL_\\S+\\}/", $html, $ordertpls, PREG_SET_ORDER)) {
            foreach ($ordertpls as $match) {
                $template_id = str_replace(array("{orderTPL_", "}"), "", $match[0]);
                if ($template_id) {
                    $all_orders_data["param"]["template_id"] = $template_id;
                    $all_orders_data["param"]["construct"] = true;
                    $replace = $this->getOrdersTable($all_orders_data);
                    $html = str_replace($match[0], $replace, $html);
                }
            }
        }
        if (preg_match("/{pagination}/", $html) || preg_match("/{pagination_results}/", $html)) {
            $pag_data = $this->getPagination($filter_data, $total);
            $html = str_replace("{pagination}", $pag_data["pagination"], $html);
            $html = str_replace("{pagination_results}", $pag_data["pagination_results"], $html);
        }
        if (preg_match_all("/\\{oc_dashboard_widget_\\S+\\}/", $html, $widgets, PREG_SET_ORDER)) {
            foreach ($widgets as $match) {
                $widget_code = str_replace(array("{oc_dashboard_widget_", "}"), "", $match[0]);
                if ($widget_code) {
                    if (230 <= $this->ompro->ocversion) {
                        $replace = $this->load->controller("extension/dashboard/" . $widget_code . "/dashboard");
                    } else {
                        $replace = $this->load->controller("dashboard/" . $widget_code);
                    }
                    $html = str_replace($match[0], $replace, $html);
                }
            }
        }
        if (preg_match_all("/\\{ompro_widget_\\S+\\}/", $html, $widgets, PREG_SET_ORDER)) {
            foreach ($widgets as $match) {
                $widget_code = str_replace(array("{ompro_widget_", "}"), "", $match[0]);
                if ($widget_code) {
                    $replace = $this->load->controller("sale/ompro_widget/" . $widget_code . "_html");
                    $html = str_replace($match[0], $replace, $html);
                }
            }
        }
        $btn_module_statuses = $this->omproapi->btn_module_statuses();
        $constructor = isset($this->request->get["constructor"]) ? true : false;
        foreach ($btn_module_statuses as $btn_key => $status) {
            $pattern = "/\\[\\[([^\\[\\]]*?)\\{" . $btn_key . "\\}(.*?)\\]\\]/s";
            if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if ($status || !$status && $constructor) {
                        $replace = str_replace(array("[[", "]]", "{" . $btn_key . "}"), "", $match[0]);
                        $html = str_replace($match[0], $replace, $html);
                    } else {
                        $html = str_replace($match[0], "", $html);
                    }
                }
            }
        }
        if (preg_match("/{nova_or_ukrposhta_list_dropdownmenu}/", $html)) {
            $menu = $this->omproapi->getPoshtaCNListMenu();
            $html = str_replace("{nova_or_ukrposhta_list_dropdownmenu}", $menu, $html);
        }
        $html = $this->ompro->replaceSelectorOptionsVars($html, $setting_user_group);
        return $html;
    }
    public function getOrdersTable($all_orders_data = array(), $expert_link = true)
    {
        $this->init();
        $json = array();
        if (!($all_orders_data && isset($all_orders_data["param"]["to_edit"])) || isset($this->request->get["to_edit"])) {
            if (isset($this->request->get["to_edit"])) {
                $to_edit = true;
            } else {
                $to_edit = false;
            }
        }
        if (!$all_orders_data) {
            $all_orders_data = $this->ompro->getOrdersDataAll(array(), $to_edit, $expert_link);
        }
        $construct = false;
        $setting_user_group = array();
        if ($all_orders_data) {
            $construct = $all_orders_data["param"]["construct"];
            $setting_user_group = $all_orders_data["param"]["setting_user_group"];
        }
        $html = $this->ompro->getOrdersTable($all_orders_data, $expert_link);
        $json["content"] = trim(html_entity_decode($html, ENT_QUOTES, "UTF-8"));
        if ($construct) {
            return $json["content"];
        }
        $json["content"] = $this->ompro->replaceSelectorOptionsVars($json["content"], $setting_user_group);
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function getOrderProductsTable()
    {
        $this->init();
        return $this->ompro->getOrderProductsTable();
    }
    public function getPagination($filter_data = array(), $total = "")
    {
        $pagination = "";
        $pagination_results = "";
        if ($filter_data) {
            if ($total === "") {
                $total = $this->ompro->getTotalOrders($filter_data);
            }
            $sort = $filter_data["sort"];
            $order = $filter_data["order"];
            $page = $filter_data["page"];
            $limit = $filter_data["limit"];
            $max_page = ceil($total / $limit);
            $page = $max_page < $page ? $max_page : $page;
            $pagination = new Pagination();
            $pagination->total = $total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->url = "&sort=" . $sort . "&order=" . $order . "&page={page}";
            $pagination = $pagination->render();
            $pagination_results = sprintf($this->language->get("text_pagination"), $total ? ($page - 1) * $limit + 1 : 0, $total - $limit < ($page - 1) * $limit ? $total : ($page - 1) * $limit + $limit, $total, ceil($total / $limit));
            $pagination = str_replace("href", "data-href", $pagination);
        }
        return array("pagination" => $pagination, "pagination_results" => $pagination_results);
    }
    public function getElements()
    {
        $this->init();
        $json = array();
        $html = "";
        if (isset($this->request->get["source"])) {
            $user_group_id = $this->user->getGroupId();
            $setting_user_group = $this->ompro->getSettingGroup($user_group_id);
            if ($this->request->get["source"] == "filter_top" || $this->request->get["source"] == "filter_product_top") {
                $filter_type = str_replace("_top", "", $this->request->get["source"]);
                if (isset($this->request->get["template_id"])) {
                    $html .= "<div class=\"row\">";
                    $html .= " <div class=\"col col-sm-2\">";
                    $html .= "  <div class=\"form-group\">";
                    $filter_reload = 0;
                    $filter_value = "";
                    $filter_info = $this->ompro->getTemplate($filter_type, $this->request->get["template_id"]);
                    $html .= "<label class=\"control-label\">" . $filter_info["name"] . ":</label>";
                    $html .= $this->omproapi->createHTMLFilter($setting_user_group, $filter_info["template"], $filter_reload, $filter_value, $filter_type);
                    $html .= "  </div>";
                    $html .= " </div>";
                    $html .= "</div>";
                } else {
                    $filters = $this->ompro->getAllTemplatesList($filter_type);
                    $row = "<div class=\"row\">";
                    $row .= " <div class=\"col col-sm-12\">";
                    $row .= "   <div class=\"box box-default no-border\">";
                    $row .= "    <div class=\"box-body well\">";
                    $i = 1;
                    foreach ($filters as $filter) {
                        if (1 < $i / 6) {
                            $i = 1;
                        }
                        if ($i == 1) {
                            $row .= "<div class=\"row\">";
                        }
                        $row .= "<div class=\"col col-sm-2\">";
                        $row .= " <div class=\"form-group\">";
                        $filter_reload = 0;
                        $filter_value = "";
                        $filter_info = $this->ompro->getTemplate($filter_type, $filter["template_id"]);
                        $row .= "<label class=\"control-label\">" . $filter_info["name"] . ":</label>";
                        $row .= $this->omproapi->createHTMLFilter($setting_user_group, $filter_info["template"], $filter_reload, $filter_value, $filter_type);
                        $row .= " </div>";
                        $row .= "</div>";
                        if ($i == 6) {
                            $row .= "</div>";
                        }
                        $i++;
                    }
                    $row .= "   </div>";
                    $row .= "  </div>";
                    $row .= " </div>";
                    $row .= "</div>";
                    $html .= $row;
                }
            } else {
                if ($this->request->get["source"] == "filter_tools") {
                    if (isset($this->request->get["template_id"])) {
                        $html .= "<div class=\"box box-default\">";
                        $html .= " <div class=\"box-header with-border\">";
                        $html .= "  <div class=\"box-tools\">";
                        $html .= "   <div class=\"btn-group btn-group-input\">";
                        $filter_reload = 0;
                        $filter_value = "";
                        $filter_info = $this->ompro->getTemplate("filter", $this->request->get["template_id"]);
                        $html .= $this->omproapi->createHTMLFilter($setting_user_group = array(), $filter_info["template"], $filter_reload, $filter_value, "filter");
                        $html .= "   </div>";
                        $html .= "  </div>";
                        $html .= " </div>";
                        $html .= "</div>";
                    }
                } else {
                    if ($this->request->get["source"] == "btngroups") {
                        $tpl_id = 0;
                        $quantity = 1;
                        if (isset($this->request->get["template_id"])) {
                            $tpl_id = $this->request->get["template_id"];
                        }
                        if (isset($this->request->get["quantity"])) {
                            $quantity = $this->request->get["quantity"];
                        }
                        if ($tpl_id) {
                            $elem = $this->omproapi->btngroups($tpl_id, $quantity);
                            $html .= $elem["tpl"];
                        } else {
                            $elems = $this->omproapi->btngroups();
                            foreach ($elems as $elem) {
                                $html .= $elem["tpl"];
                            }
                        }
                    } else {
                        if ($this->request->get["source"] == "order_table" && $this->request->get["template_id"]) {
                            $this->request->get["limit"] = 5;
                            $this->request->get["order"] = "DESC";
                            $all_orders_data = $this->ompro->getOrdersDataAll();
                            $all_orders_data["param"]["template_id"] = $this->request->get["template_id"];
                            $all_orders_data["param"]["construct"] = true;
                            $html .= "<div class=\"row\">";
                            $html .= " <div class=\"col col-sm-12\">";
                            $html .= $this->getOrdersTable($all_orders_data);
                            $html .= "</div>";
                            $html .= "</div>";
                        } else {
                            if ($this->request->get["source"] == "blocks_el" || $this->request->get["source"] == "column_el" || $this->request->get["source"] == "tools_el" || $this->request->get["source"] == "btngroup_el" && $this->request->get["template_id"]) {
                                $result = $this->ompro->getTemplateTemplate("page_block", $this->request->get["template_id"]);
                                if (isset($result["template"])) {
                                    $html .= html_entity_decode($result["template"], ENT_QUOTES, "UTF-8");
                                }
                                if (preg_match_all("/\\{oc_dashboard_widget_\\S+\\}/", $html, $widgets, PREG_SET_ORDER)) {
                                    foreach ($widgets as $match) {
                                        $widget_code = str_replace(array("{oc_dashboard_widget_", "}"), "", $match[0]);
                                        if ($widget_code) {
                                            if (230 <= $this->ompro->ocversion) {
                                                $replace = $this->load->controller("extension/dashboard/" . $widget_code . "/dashboard");
                                            } else {
                                                $replace = $this->load->controller("dashboard/" . $widget_code);
                                            }
                                            $html = str_replace($match[0], $replace, $html);
                                        }
                                    }
                                }
                                if (preg_match_all("/\\{ompro_widget_\\S+\\}/", $html, $widgets, PREG_SET_ORDER)) {
                                    foreach ($widgets as $match) {
                                        $widget_code = str_replace(array("{ompro_widget_", "}"), "", $match[0]);
                                        if ($widget_code) {
                                            $replace = $this->load->controller("sale/ompro_widget/" . $widget_code . "_html");
                                            $html = str_replace($match[0], $replace, $html);
                                        }
                                    }
                                }
                            } else {
                                if ($this->request->get["source"] == "pagination") {
                                    $pag_data = $this->getPagination($this->ompro->filterData($setting_user_group), $total = 0);
                                    $html .= "<div class=\"row pagination-row\">";
                                    $html .= " <div class=\"col col-sm-6 text-left\" ><div data-elem=\"pagination\">" . $pag_data["pagination"] . "</div></div>";
                                    $html .= " <div class=\"col col-sm-6 text-right\"><div data-elem=\"pagination_results\">" . $pag_data["pagination_results"] . "</div></div>";
                                    $html .= "</div>";
                                } else {
                                    if ($this->request->get["source"] == "api_blocks_el" || $this->request->get["source"] == "api_column_el" || $this->request->get["source"] == "api_tools_el" || $this->request->get["source"] == "api_btngroup_el" && $this->request->get["template_id"]) {
                                        $elem = $this->omproapi->pageHtmlElemVars($this->request->get["template_id"]);
                                        if ($this->request->get["source"] == "api_blocks_el") {
                                            $html .= $elem;
                                        } else {
                                            if ($this->request->get["source"] == "api_column_el") {
                                                $html .= "<div class=\"row\"><div class=\"col col-sm-12\">" . $elem . "</div></div>";
                                            } else {
                                                if ($this->request->get["source"] == "api_tools_el") {
                                                    $html .= "<div class=\"row\"><div class=\"col col-sm-12\"><div class=\"box box-default\"><div class=\"box-header with-border\"><div class=\"box-tools pull-right\">" . $elem . "</div></div></div></div></div>";
                                                } else {
                                                    if ($this->request->get["source"] == "api_btngroup_el") {
                                                        $html .= "<div class=\"row\"><div class=\"col col-sm-12\"><div class=\"btn-group\"" . $elem . "</div></div></div>";
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
            $html = $this->ompro->replaceSelectorOptionsVars($html, $setting_user_group);
        }
        $json["content"] = $html;
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function getUrlTpl()
    {
        $url = $get_page = "";
        if (isset($this->request->get["get_page"])) {
            $get_page = $this->request->get["get_page"];
            $url .= "&get_page=" . $this->request->get["get_page"];
        }
        if (($get_page == "block" || $get_page == "page_block") && isset($this->request->get["block_target"])) {
            $url .= "&block_target=" . $this->request->get["block_target"];
        }
        if (isset($this->request->get["filter_template_id"])) {
            $url .= "&filter_template_id=" . $this->request->get["filter_template_id"];
        }
        if (isset($this->request->get["filter_code"])) {
            $url .= "&filter_code=" . $this->request->get["filter_code"];
        }
        if (isset($this->request->get["filter_filter_id"])) {
            $url .= "&filter_filter_id=" . $this->request->get["filter_filter_id"];
        }
        if (isset($this->request->get["filter_name"])) {
            $url .= "&filter_name=" . $this->request->get["filter_name"];
        }
        if (isset($this->request->get["filter_date_added"])) {
            $url .= "&filter_date_added=" . $this->request->get["filter_date_added"];
        }
        if (isset($this->request->get["filter_date_modified"])) {
            $url .= "&filter_date_modified=" . $this->request->get["filter_date_modified"];
        }
        return $url;
    }
    public function savePage()
    {
        $this->init();
        $json = array();
        if (!$this->validate()) {
            $json["error"] = $this->error["warning"];
        } else {
            if (isset($this->request->get["pageid"])) {
                $pageid = $this->request->get["pageid"];
            } else {
                $pageid = 0;
            }
            if (isset($this->request->post["setting"])) {
                if ($pageid) {
                    $this->ompro->templateEdit("pages", $pageid, $this->request->post["setting"]);
                    $json["pageid"] = $pageid;
                } else {
                    $json["pageid"] = $this->ompro->addTemplate("pages", $this->request->post["setting"]);
                }
                $json["success"] = $this->language->get("text_success_editor");
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function templateList()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $data["has_permission"] = false;
        if ($this->user->hasPermission("modify", "sale/ompro")) {
            $data["has_permission"] = true;
        }
        if (isset($this->request->get["get_page"])) {
            $get_page = $this->request->get["get_page"];
        } else {
            $get_page = "orders";
        }
        $get_text = $get_page;
        if ($get_page == "block" && isset($this->request->get["block_target"])) {
            $get_text = "block_" . $this->request->get["block_target"];
            $filter_block_target = $this->request->get["block_target"];
            $block_target = "&block_target=" . $this->request->get["block_target"];
        } else {
            if ($get_page == "page_block" && isset($this->request->get["block_target"])) {
                $get_text = "page_block_" . $this->request->get["block_target"];
                $filter_block_target = $this->request->get["block_target"];
                $block_target = "&block_target=" . $this->request->get["block_target"];
            } else {
                $block_target = $filter_block_target = "";
            }
        }
        $data["heading_title"] = $this->language->get("heading_template_" . $get_text);
        $this->document->setTitle(strip_tags($data["heading_title"]));
        if (isset($this->request->get["filter_template_id"])) {
            $filter_template_id = $this->request->get["filter_template_id"];
        } else {
            $filter_template_id = "";
        }
        if (isset($this->request->get["filter_code"])) {
            $filter_code = $this->request->get["filter_code"];
        } else {
            $filter_code = "";
        }
        if (isset($this->request->get["filter_filter_id"])) {
            $filter_filter_id = $this->request->get["filter_filter_id"];
        } else {
            $filter_filter_id = "";
        }
        if (isset($this->request->get["filter_name"])) {
            $filter_name = $this->request->get["filter_name"];
        } else {
            $filter_name = "";
        }
        if (isset($this->request->get["filter_date_added"])) {
            $filter_date_added = $this->request->get["filter_date_added"];
        } else {
            $filter_date_added = NULL;
        }
        if (isset($this->request->get["filter_date_modified"])) {
            $filter_date_modified = $this->request->get["filter_date_modified"];
        } else {
            $filter_date_modified = NULL;
        }
        if (isset($this->request->get["sort"])) {
            $sort = $this->request->get["sort"];
        } else {
            $sort = "template_id";
        }
        if (isset($this->request->get["order"])) {
            $order = $this->request->get["order"];
        } else {
            $order = "DESC";
        }
        if (isset($this->request->get["page"])) {
            $page = $this->request->get["page"];
        } else {
            $page = 1;
        }
        if ($this->config->get("config_admin_limit")) {
            $limit = $this->config->get("config_admin_limit");
        } else {
            if ($this->config->get("config_limit_admin")) {
                $limit = $this->config->get("config_limit_admin");
            } else {
                $limit = 10;
            }
        }
        $url = $this->getUrlTpl();
        $data["breadcrumbs"] = array();
        $data["breadcrumbs"][] = "<li class=\" \"><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
        $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/templateList", $strtoken . $url, true) . "\" >" . $data["heading_title"] . "</a></li>";
        $data["insert"] = $this->url->link("sale/ompro/templateEdit", $strtoken . "&get_page=" . $get_page . $block_target, true);
        $data["copy"] = $this->url->link("sale/ompro/templateListEdit", $strtoken . "&get_page=" . $get_page . $block_target . "&action=copy" . $url, true);
        $data["delete"] = $this->url->link("sale/ompro/templateListEdit", $strtoken . "&get_page=" . $get_page . $block_target . "&action=delete" . $url, true);
        if (isset($this->error["warning"])) {
            $data["error_warning"] = $this->error["warning"];
        } else {
            $data["error_warning"] = "";
        }
        if (isset($this->session->data["success"])) {
            $data["success"] = $this->session->data["success"];
            unset($this->session->data["success"]);
        } else {
            $data["success"] = "";
        }
        $filter_data = array("filter_block_target" => $filter_block_target, "filter_template_id" => $filter_template_id, "filter_code" => $filter_code, "filter_filter_id" => $filter_filter_id, "filter_name" => $filter_name, "filter_date_added" => $filter_date_added, "filter_date_modified" => $filter_date_modified, "sort" => $sort, "order" => $order, "page" => $page, "start" => $limit * ($page - 1), "limit" => $limit);
        $total = $this->ompro->getTotalList($get_page, $filter_data);
        $max_page = ceil($total / $limit);
        $page = $max_page < $page ? $max_page : $page;
        $templates = $this->ompro->getList($get_page, $filter_data);
        $data["templates"] = array();
        foreach ($templates as $template) {
            $data["templates"][] = array("template_id" => $template["template_id"], "code" => $template["code"], "filter_id" => isset($template["filter_id"]) ? $template["filter_id"] : "", "name" => $template["name"], "description" => $template["description"], "date_added" => date("d.m.Y - H:i:s", strtotime($template["date_added"])), "date_modified" => date("d.m.Y - H:i:s", strtotime($template["date_modified"])), "edit" => $this->url->link("sale/ompro/templateEdit", $strtoken . "&get_page=" . $get_page . $block_target . "&template_id=" . $template["template_id"], true));
        }
        $url = $this->getUrlTpl();
        if ($order == "ASC") {
            $url .= "&order=DESC";
        } else {
            $url .= "&order=ASC";
        }
        if (isset($this->request->get["page"])) {
            $url .= "&page=" . $this->request->get["page"];
        }
        $data["sort_template_id"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=template_id", true);
        $data["sort_code"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=code", true);
        $data["sort_filter_id"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=filter_id", true);
        $data["sort_target"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=target", true);
        $data["sort_name"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=name", true);
        $data["sort_date_added"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=date_added", true);
        $data["sort_date_modified"] = $this->url->link("sale/ompro/templateList", $strtoken . $url . "&sort=date_modified", true);
        $url = $this->getUrlTpl();
        if (isset($this->request->get["sort"])) {
            $url .= "&sort=" . $this->request->get["sort"];
        }
        if (isset($this->request->get["order"])) {
            $url .= "&order=" . $this->request->get["order"];
        }
        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link("sale/ompro/templateList", $strtoken . "&get_page=" . $get_page . $url . "&page={page}", "SSL");
        $pagination = $pagination->render();
        $data["pagination"] = str_replace("<ul class=\"pagination\">", "<ul class=\"pagination pagination-sm\">", $pagination);
        $data["pagination_results"] = sprintf($this->language->get("text_pagination"), $total ? ($page - 1) * $limit + 1 : 0, $total - $limit < ($page - 1) * $limit ? $total : ($page - 1) * $limit + $limit, $total, ceil($total / $limit));
        $data["get_page"] = $get_page;
        $data["block_target"] = $block_target;
        $data["filter_template_id"] = $filter_template_id;
        $data["filter_code"] = $filter_code;
        $data["filter_filter_id"] = $filter_filter_id;
        $data["filter_name"] = $filter_name;
        $data["filter_date_added"] = $filter_date_added;
        $data["filter_date_modified"] = $filter_date_modified;
        $data["sort"] = $sort;
        $data["order"] = $order;
        $data["page"] = $page;
        $data["header"] = $this->load->controller("sale/ompro_header");
        $data["footer"] = $this->load->controller("sale/ompro_footer");
        $data["ocversion"] = $this->ompro->ocversion;
        $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
        $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_list" . $ending, $data));
    }
    public function templateListEdit()
    {
        $data = $this->init();
        $action = $this->request->get["action"];
        $get_page = $this->request->get["get_page"];
        $this->document->setTitle(strip_tags($this->language->get("heading_template_" . $get_page)));
        if (isset($this->request->post["selected"]) && $this->request->server["REQUEST_METHOD"] == "POST" && $this->validate()) {
            if ($action == "delete") {
                foreach ($this->request->post["selected"] as $template_id) {
                    $this->ompro->deleteTemplate($get_page, $template_id);
                }
                $this->session->data["success"] = $this->language->get("text_delete_success_template");
            }
            if ($action == "copy") {
                foreach ($this->request->post["selected"] as $template_id) {
                    $this->ompro->copyTemplate($get_page, $template_id);
                }
                $this->session->data["success"] = $this->language->get("text_copy_success_template");
            }
            $url = $this->getUrlTpl();
            $this->response->redirect($this->url->link("sale/ompro/templateList", $this->ompro->strtoken . $url, true));
        }
        $this->templateList();
    }
    public function templateEdit()
    {
        $data = $this->init();
        $get_page = $this->request->get["get_page"];
        $this->document->setTitle(strip_tags($this->language->get("heading_template_edit_" . $get_page)));
        if ($this->request->server["REQUEST_METHOD"] == "POST" && $this->validate()) {
            if (isset($this->request->post["template"]["tags"]) && $this->request->post["template"]["tags"]) {
                $tags = array();
                $tags = explode(",", $this->request->post["template"]["tags"]);
                if (isset($tags[0])) {
                    $this->request->post["template"]["tags"] = $tags;
                }
            }
            if (isset($this->request->post["template"]["option_value"]) && $this->request->post["template"]["option_value"]) {
                $option_value_ides = array();
                foreach ($this->request->post["template"]["option_value"] as $id => $option_value) {
                    if ($option_value["option_value_id"]) {
                        $option_value_ides[] = $option_value["option_value_id"];
                    }
                }
                $option_value_id = !empty($option_value_ides) ? max($option_value_ides) + 1 : 1;
                $option_values = array();
                foreach ($this->request->post["template"]["option_value"] as $id => $option_value) {
                    if (!$option_value["option_value_id"]) {
                        $option_value["option_value_id"] = $option_value_id;
                        $option_value_id++;
                    }
                    $this->request->post["template"]["option_value"][$id] = $option_value;
                }
            }
            if (isset($this->request->get["template_id"])) {
                $template_id = $this->request->get["template_id"];
            } else {
                $template_id = 0;
            }
            if ($template_id) {
                $template_id = $this->request->get["template_id"];
                $this->ompro->templateEdit($get_page, $template_id, $this->request->post["template"]);
                $this->session->data["success"] = $this->language->get("text_success_template");
            } else {
                $template_id = $this->ompro->addTemplate($get_page, $this->request->post["template"]);
                $this->session->data["success"] = $this->language->get("text_add_success_template");
                if (isset($this->request->get["block_target"])) {
                    $block_target = "&block_target=" . $this->request->get["block_target"];
                } else {
                    $block_target = "";
                }
                $this->response->redirect($this->url->link("sale/ompro/templateEdit", $this->ompro->strtoken . "&get_page=" . $get_page . $block_target . "&template_id=" . $template_id, true));
            }
        }
        $this->templateForm();
    }
    public function templateBackup()
    {
        $this->init();
        if (isset($this->request->get["type"]) && isset($this->request->get["template_id"])) {
            $type = $this->request->get["type"];
            $template_id = $this->request->get["template_id"];
            if (!$this->user->hasPermission("modify", "sale/ompro")) {
                $this->load->language("sale/ompro");
                $this->session->data["error_warning"] = $this->language->get("error_permission");
                $this->response->redirect($this->url->link("sale/ompro/templateEdit", $this->strToken() . "&get_page=" . $type . "&template_id=" . $template_id, true));
            } else {
                $datatype = "template_" . $type;
                $template_info = $this->ompro->getTemplate($this->request->get["type"], $template_id);
                $template_info["template"] = base64_encode(serialize($template_info["template"]));
                $template_info["datatype"] = $datatype;
                $this->response->addheader("Pragma: public");
                $this->response->addheader("Expires: 0");
                $this->response->addheader("Content-Description: File Transfer");
                $this->response->addheader("Content-Type: application/octet-stream");
                $this->response->addheader("Content-Disposition: attachment; filename=" . "ompro_" . $datatype . "_" . $template_id . "_" . date("Y-m-d_H-i-s") . ".settings");
                $this->response->addheader("Content-Transfer-Encoding: binary");
                $this->response->setOutput(serialize($template_info));
            }
        }
    }
    public function templateRestore()
    {
        $this->init();
        $get_page = $this->request->get["get_page"];
        $this->document->setTitle(strip_tags($this->language->get("heading_template_edit_" . $get_page)));
        if (isset($this->request->get["template_id"]) && $this->request->get["template_id"]) {
            if ($this->validate() && isset($this->request->files["import"]) && is_uploaded_file($this->request->files["import"]["tmp_name"])) {
                $content = file_get_contents($this->request->files["import"]["tmp_name"]);
                if ($content && preg_match("/(a|O|s|b)\\:[0-9]*?((\\:((\\{?(.+)\\})|(\\\"(.+)\\\"\\;)))|(\\;))/", $content)) {
                    $content = unserialize($content);
                }
                $datatype = "template_" . $get_page;
                if (is_array($content) && isset($content["datatype"]) && $content["datatype"] == $datatype) {
                    unset($content["datatype"]);
                    $template = $content;
                    $template = unserialize(base64_decode($template["template"]));
                    $this->ompro->templateEdit($get_page, $this->request->get["template_id"], $template);
                    $this->session->data["success"] = $this->language->get("text_success_template");
                } else {
                    $this->error["warning"] = $this->language->get("error_import_data");
                }
            }
        } else {
            $this->error["warning"] = $this->language->get("error_restore_no_template");
        }
        $this->templateForm();
    }
    public function templateForm()
    {
        $data = $this->init();
        $data["strtoken"] = $strtoken = $this->strToken();
        $data["version"] = $this->omproapi->version;
        if (isset($this->request->get["get_page"])) {
            $get_page = $this->request->get["get_page"];
        } else {
            $get_page = "orders";
        }
        if (!$this->checkLicense()) {
            $this->response->redirect($this->url->link($this->mod_path . "/license", $strtoken, true));
        } else {
            $data["has_permission"] = false;
            if ($this->user->hasPermission("modify", "sale/ompro")) {
                $data["has_permission"] = true;
            }
            $data = array_merge($data, $this->load->language("sale/ompro"));
            $data["heading_title"] = $this->language->get("heading_template_edit_" . $get_page);
            $this->load->model("user/user");
            $user_id = $this->session->data["user_id"];
            $user_info = $this->model_user_user->getUser($user_id);
            $url = $this->getUrlTpl();
            if (isset($this->request->get["template_id"])) {
                $template_id = $this->request->get["template_id"];
                $template_info = $this->ompro->getTemplate($get_page, $template_id);
            } else {
                $template_info = array();
                $template_id = 0;
            }
            $data["text_tpl_code"] = isset($template_info["code"]) && $template_info["code"] ? $template_info["code"] : $data["text_code_none"];
            $data["tpl_code"] = isset($template_info["code"]) && $template_info["code"] ? $template_info["code"] : $template_id;
            $data["action"] = $this->url->link("sale/ompro/templateEdit", $strtoken . $url . "&template_id=" . $template_id, true);
            if (!empty($template_info)) {
                $data["template"] = $template_info["template"];
            } else {
                $data["template"] = array();
            }
            $data["backup_link"] = $this->url->link("sale/ompro/templateBackup", $strtoken . "&type=" . $get_page . "&template_id=" . $template_id, "SSL");
            $data["restore_link"] = $this->url->link("sale/ompro/templateRestore", $strtoken . "&get_page=" . $get_page . "&template_id=" . $template_id, "SSL");
            $data["breadcrumbs"] = array();
            $data["breadcrumbs"][] = "<li><a href=\"" . $this->url->link("common/dashboard", $strtoken, true) . "\" ><i class=\"fa fa-home\"></i> " . $this->language->get("text_homepage") . "</a></li>";
            $data["breadcrumbs"][] = "<li><a class=\"text-blue\" href=\"" . $this->url->link("sale/ompro/templateEdit", $strtoken . $url . "&template_id=" . $template_id, true) . "\" >" . $data["heading_title"] . "</a></li>";
            $langCode = substr(strtolower($this->omproapi->getLanguageCode()), 0, 2);
            $data["langCode"] = $langCode == "en" || $langCode == "ru" ? $langCode : "ru";
            $data["summernote_lang"] = $langCode == "ru" ? "ru-RU" : "en-GB";
            if (isset($this->error["warning"])) {
                $data["error_warning"] = $this->error["warning"];
            } else {
                if (isset($this->session->data["error_warning"])) {
                    $data["error_warning"] = $this->session->data["error_warning"];
                    unset($this->session->data["error_warning"]);
                } else {
                    $data["error_warning"] = "";
                }
            }
            if (isset($this->session->data["success"])) {
                $data["success"] = $this->session->data["success"];
                unset($this->session->data["success"]);
            } else {
                $data["success"] = "";
            }
            $data["cancel"] = $this->url->link("sale/ompro/templateList", $strtoken . $url, true);
            $this->load->model("localisation/language");
            $system_languages = $this->model_localisation_language->getLanguages();
            $data["languages"] = array();
            foreach ($system_languages as $lang_id => $language) {
                if ($language["status"]) {
                    $data["languages"][$lang_id] = $language;
                }
            }
            $data["last_order"] = $this->ompro->getLastOrder();
            $data["template_id"] = $template_id;
            $data["header"] = $this->load->controller("sale/ompro_header");
            $data["footer"] = $this->load->controller("sale/ompro_footer");
            $data["ocversion"] = $this->ompro->ocversion;
            $ending = 230 <= $data["ocversion"] ? "" : ".tpl";
            if ($get_page == "excel_orders" || $get_page == "excel_orders_products") {
                $data["default_set_col_list"] = array("default_set_col_title" => "Заголовок", "default_set_col_data" => "Данные", "default_set_col_img" => "Изображение", "default_set_col_link" => "Гиперссылка");
                $data["stores"] = $this->omproapi->getStores();
                $data["col_format_list"] = $this->omproapi->getCellFormatList();
                $data["font_list"] = $this->omproapi->getFontList();
                $data["border_style_list"] = array("thin", "medium", "thick", "double", "hair", "dotted", "dashed", "dashdot", "dashdotdot", "mefiumdashdot", "mefiumdashdotdot", "mefiumdashed", "slantdashdot", "none");
            }
            $data["product_group_by_list"] = array();
            if ($get_page == "product") {
                $data["product_blocks"] = $this->ompro->getTemplateBlocksTarget("product");
                $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_product" . $ending, $data));
            } else {
                if ($get_page == "orders") {
                    $data["order_codes"] = $this->ompro->orderFieldsData("codes");
                    $data["order_blocks"] = $this->ompro->getTemplateBlocksTarget("order");
                    $data["filters"] = $this->ompro->getAllTemplatesList("filter");
                    $data["filters"] = array_merge($data["filters"], $this->omproapi->orderTableFilterRowElemList());
                    $set_columns = isset($data["template"]["columns"]) ? $data["template"]["columns"] : array();
                    foreach ($set_columns as $id => $column) {
                        $filter_template_id = $column["filter_template_id"];
                        if ($filter_template_id && is_numeric($filter_template_id)) {
                            $data["template"]["columns"][$id]["filter_template_id"] = $this->ompro->getFilterIDByTemplateId($filter_template_id);
                        }
                    }
                    $data["filter_size_list"] = $this->omproapi->getFilterSizeList();
                    $data["color_source_list"] = $this->omproapi->getColorSourceList();
                    $this->load->model("localisation/order_status");
                    $data["order_statuses"] = $this->model_localisation_order_status->getOrderStatuses();
                    $data["order_statuses"][] = array("order_status_id" => "0", "name" => $data["text_missing"]);
                    $data["server"] = $this->ompro->server;
                    $btn_tables = array("print_orders", "print_orders_table", "print_products_table", "send_mail", "send_sms", "send_tlgrm", "excel_orders", "excel_orders_products");
                    foreach ($btn_tables as $table) {
                        $data["table_btn_" . $table] = $this->omproapi->getTableBtnHtmlTemplates($table);
                    }
                    $data["table_btn_openwindow"] = $this->omproapi->getTableBtnOpenWindow();
                    $data["table_btn_action_adding"] = $this->omproapi->getTableBtnActionAdding();
                    $data["table_btn_action_quick_status"] = $this->omproapi->getTableBtnActionQuickStatus();
                    $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_orders" . $ending, $data));
                } else {
                    if ($get_page == "history") {
                        $data["history_vars_table"] = "<table class=\"table-mini full-width\" style=\"margin-bottom: 0;\">" . "<thead>" . "<tr><th colspan=\"2\">Переменные данных истории заказа (для шаблона)</th></tr>" . "<tr><th>Переменная</th><th>Название</th></tr>" . "</thead>" . "</tbody>" . "<tr><td>[[{date_added}]]</td><td>Дата</td></tr>" . "<tr><td>[[{order_status}]]</td><td>Статус заказа</td></tr>" . "<tr><td>[[{payment_status}]]</td><td>Статус оплаты</td></tr>" . "<tr><td>[[{shipping_status}]]</td><td>Статус доставки</td></tr>" . "<tr><td>[[{comment}]]</td><td>Комментарий</td></tr>" . "<tr><td>[[{log}]]</td><td>Лог изменений</td></tr>" . "<tr><td>[[{user}]]</td><td>Пользователь (менеджер)</td></tr>" . "<tr><td>[[{user_image}]]</td><td>Ссылка на изображение пользлвателя</td></tr>" . "<tr><td>[[{user_image_img}]]</td><td>Изображение пользлвателя (img)</td></tr>" . "<tr><td>[[{notify}]]</td><td>Покупатель уведомлен по Email</td></tr>" . "<tr><td>[[{notify_sms}]]</td><td>Покупатель уведомлен по SMS</td></tr>" . "<tr><td>[[{notify_tlgrm}]]</td><td>Покупатель уведомлен по Telegram (в разработке)</td></tr>" . "<tr><td>[[{file_name}]]</td><td>Прикрепленный файл</td></tr>" . "<tr><td>[[{users_class}]]</td><td>Доп. класс истории других пользователей</td></tr>" . "</tbody>" . "</table>";
                        $data["history_vars_table_add"] = "<table class=\"table-mini full-width\" style=\"margin-bottom: 0;\">" . "<thead>" . "<tr><th colspan=\"2\">Переменные для контейнера</th></tr>" . "<tr><th>Переменная</th><th>Название</th></tr>" . "</thead>" . "</tbody>" . "<tr><td>{history_template}</td><td>Шаблон истории</td></tr>" . "<tr><td>{history_total}</td><td>Кол-во сообщений (всего)</td></tr>" . "<tr><td>{pagination}</td><td>Нумерация (пагинация) страниц</td></tr>" . "<tr><td>{pagination_results}</td><td>Результаты пагинации</td></tr>" . "<tr><td>{order_id}</td><td>Номер заказа</td></tr>" . "<tr><td>{order_status_id}</td><td>Текущий ID статуса заказа</td></tr>" . "</tbody>" . "</table>";
                        $data["preview"] = $this->url->link("sale/ompro/orderHistories", $strtoken, "SSL");
                        $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_history" . $ending, $data));
                    } else {
                        if ($get_page == "filter") {
                            $data["log_sql"] = $this->ompro->getAdminLogSql();
                            $data["input_type_list"] = $this->omproapi->getFilterInputTypeList();
                            $data["input_validate_class_table"] = $this->omproapi->getFilterValidateClassTable();
                            $data["handler_type_list"] = $this->omproapi->getFilterInputHandlerTypeList();
                            $data["select_class_list"] = $this->omproapi->getFilterInputSelectClassList();
                            $data["datepicker_class_list"] = $this->omproapi->getDatepickerClassList();
                            $data["autocompletes"] = $this->omproapi->autocompleteTargetList();
                            $data["operator_list"] = $this->omproapi->getOperatorList();
                            $data["process_as_list"] = $this->omproapi->getProcessAsList();
                            $data["process_method_list"] = $this->omproapi->getFilterSelectorValuesApiMethodList();
                            $filter_codes = $this->ompro->orderFieldsData("codes");
                            $data["tags"] = array();
                            foreach ($filter_codes as $tag) {
                                if (isset($data["template"]["tags"]) && is_array($data["template"]["tags"]) && in_array($tag["code"], $data["template"]["tags"])) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }
                                $data["tags"][] = array("sort_key" => $tag["sort_key"], "code" => $tag["code"], "name" => $tag["name"], "selected" => $selected);
                            }
                            if (!isset($data["template"]["tags_separator"])) {
                                $data["template"]["tags_separator"] = "";
                            }
                            if (isset($data["template"]["tags_manual_status"]) && $data["template"]["tags_manual_status"] == 1) {
                                $data["tags_manual_status"] = true;
                            } else {
                                $data["tags_manual_status"] = false;
                            }
                            if (isset($data["template"]["tags_multiple_status"]) && $data["template"]["tags_multiple_status"] == 1) {
                                $data["tags_multiple_status"] = true;
                            } else {
                                $data["tags_multiple_status"] = false;
                            }
                            if (empty($data["template"]["tags_manual"])) {
                                $data["template"]["tags_manual"] = "";
                            }
                            $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_filter" . $ending, $data));
                        } else {
                            if ($get_page == "filter_product") {
                                $data["log_sql"] = $this->ompro->getAdminLogSql();
                                $data["input_type_list"] = $this->omproapi->getFilterInputTypeList();
                                $data["input_validate_class_table"] = $this->omproapi->getFilterValidateClassTable();
                                $data["handler_type_list"] = $this->omproapi->getFilterInputHandlerTypeList();
                                $data["select_class_list"] = $this->omproapi->getFilterInputSelectClassList();
                                $data["datepicker_class_list"] = $this->omproapi->getDatepickerClassList();
                                $data["autocompletes"] = $this->omproapi->autocompleteTargetList();
                                $data["operator_list"] = $this->omproapi->getOperatorList();
                                $data["process_as_list"] = $this->omproapi->getProcessAsList();
                                $data["process_method_list"] = $this->omproapi->getFilterSelectorValuesApiMethodList();
                                $filter_codes = $this->ompro->productFieldsData("codes");
                                $data["tags"] = array();
                                foreach ($filter_codes as $tag) {
                                    if (isset($data["template"]["tags"]) && is_array($data["template"]["tags"]) && in_array($tag["code"], $data["template"]["tags"])) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                                    $data["tags"][] = array("code" => $tag["code"], "name" => $tag["name"], "selected" => $selected);
                                }
                                if (!isset($data["template"]["tags_separator"])) {
                                    $data["template"]["tags_separator"] = "";
                                }
                                if (isset($data["template"]["tags_manual_status"]) && $data["template"]["tags_manual_status"] == 1) {
                                    $data["tags_manual_status"] = true;
                                } else {
                                    $data["tags_manual_status"] = false;
                                }
                                if (isset($data["template"]["tags_multiple_status"]) && $data["template"]["tags_multiple_status"] == 1) {
                                    $data["tags_multiple_status"] = true;
                                } else {
                                    $data["tags_multiple_status"] = false;
                                }
                                if (empty($data["template"]["tags_manual"])) {
                                    $data["template"]["tags_manual"] = "";
                                }
                                $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_filter_product" . $ending, $data));
                            } else {
                                if ($get_page == "option") {
                                    $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_option" . $ending, $data));
                                } else {
                                    if ($get_page == "mail") {
                                        $data["button_send_email"] = sprintf($this->language->get("button_send_email"), $user_info["email"]);
                                        $data["button_edit_product_table"] = $this->language->get("button_edit_product_table");
                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["logo_width"])) {
                                            $data["logo_width"] = $this->request->post["template"]["logo_width"];
                                        } else {
                                            if (!empty($template_info["template"]["logo_width"])) {
                                                $data["logo_width"] = $template_info["template"]["logo_width"];
                                            } else {
                                                $data["logo_width"] = 187;
                                            }
                                        }
                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["logo_height"])) {
                                            $data["logo_height"] = $this->request->post["template"]["logo_height"];
                                        } else {
                                            if (!empty($template_info["template"]["logo_height"])) {
                                                $data["logo_height"] = $template_info["template"]["logo_height"];
                                            } else {
                                                $data["logo_height"] = 50;
                                            }
                                        }
                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["user_image_width"])) {
                                            $data["user_image_width"] = $this->request->post["template"]["user_image_width"];
                                        } else {
                                            if (!empty($template_info["template"]["user_image_width"])) {
                                                $data["user_image_width"] = $template_info["template"]["user_image_width"];
                                            } else {
                                                $data["user_image_width"] = 64;
                                            }
                                        }
                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["user_image_height"])) {
                                            $data["user_image_height"] = $this->request->post["template"]["user_image_height"];
                                        } else {
                                            if (!empty($template_info["template"]["user_image_height"])) {
                                                $data["user_image_height"] = $template_info["template"]["user_image_height"];
                                            } else {
                                                $data["user_image_height"] = 64;
                                            }
                                        }
                                        $data["mail_blocks"] = $this->ompro->getTemplateBlocksTarget("mail");
                                        $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_mail" . $ending, $data));
                                    } else {
                                        if ($get_page == "sms") {
                                            $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_sms" . $ending, $data));
                                        } else {
                                            if ($get_page == "tlgrm") {
                                                $data["tlgrm_text_format_table"] = "<table class=\"table-mini full-width\" style=\"margin-bottom: 0;\">" . "<thead>" . "<tr><th colspan=\"2\">HTML Форматирование текста в Telegram</th></tr>" . "</thead>" . "</tbody>" . "<tr>" . "<td style=\"font-size: 18px;\"><code>&lt;b&gt;</code> <b>жирный_текст</b><code>&lt;/b&gt;</code> или <code>&lt;strong&gt;</code> <strong>жирный_текст</strong><code>&lt;/strong&gt;</code></td>" . "</tr>" . "<tr>" . "<td style=\"font-size: 18px;\"><code>&lt;i&gt;</code> <i>курсив</i><code>&lt;/i&gt;</code> или <code>&lt;em&gt;</code> <em>курсив</em><code>&lt;/em&gt;</code></td>" . "</tr>" . "<tr>" . "<td style=\"font-size: 18px;\"><code>&lt;code&gt;</code> <code>код</code> <code>&lt;/code&gt;</code> или <br><code>&lt;pre&gt;</code> <pre>код</pre><code>&lt;/pre&gt;</code></td>" . "</tr>" . "<tr>" . "<td style=\"font-size: 18px;\"><code>&lt;a href=\"Ссылка\"&gt;</code>Text ссылки<code>&lt;/a&gt;</code></td>" . "</tr>" . "</tbody>" . "</table>";
                                                $data["user_telegram_id"] = $user_info["telegram_id"];
                                                $data["text_alert_test_telegram"] = sprintf($this->language->get("text_alert_test_telegram"), $this->url->link("user/user/edit", $strtoken . "&user_id=" . $user_id, "SSL"));
                                                $data["bot_token_status"] = $this->ompro->getTelegramBotToken() ? true : false;
                                                $data["button_send_tlgrm"] = sprintf($this->language->get("button_send_tlgrm"), $user_info["telegram_id"] !== "" ? $user_info["telegram_id"] : $this->language->get("text_not_specified"));
                                                $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_tlgrm" . $ending, $data));
                                            } else {
                                                if ($get_page == "comment") {
                                                    $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_comment" . $ending, $data));
                                                } else {
                                                    if ($get_page == "print_orders") {
                                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["logo_width"])) {
                                                            $data["logo_width"] = $this->request->post["template"]["logo_width"];
                                                        } else {
                                                            if (!empty($template_info["template"]["logo_width"])) {
                                                                $data["logo_width"] = $template_info["template"]["logo_width"];
                                                            } else {
                                                                $data["logo_width"] = 187;
                                                            }
                                                        }
                                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["logo_height"])) {
                                                            $data["logo_height"] = $this->request->post["template"]["logo_height"];
                                                        } else {
                                                            if (!empty($template_info["template"]["logo_height"])) {
                                                                $data["logo_height"] = $template_info["template"]["logo_height"];
                                                            } else {
                                                                $data["logo_height"] = 50;
                                                            }
                                                        }
                                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["user_image_width"])) {
                                                            $data["user_image_width"] = $this->request->post["template"]["user_image_width"];
                                                        } else {
                                                            if (!empty($template_info["template"]["user_image_width"])) {
                                                                $data["user_image_width"] = $template_info["template"]["user_image_width"];
                                                            } else {
                                                                $data["user_image_width"] = 64;
                                                            }
                                                        }
                                                        if (isset($this->request->post["template"]) && !empty($this->request->post["template"]["user_image_height"])) {
                                                            $data["user_image_height"] = $this->request->post["template"]["user_image_height"];
                                                        } else {
                                                            if (!empty($template_info["template"]["user_image_height"])) {
                                                                $data["user_image_height"] = $template_info["template"]["user_image_height"];
                                                            } else {
                                                                $data["user_image_height"] = 64;
                                                            }
                                                        }
                                                        $data["button_send_email"] = sprintf($this->language->get("button_send_email"), $user_info["email"]);
                                                        $data["button_edit_product_table"] = $this->language->get("button_edit_product_table");
                                                        $data["preview"] = $this->url->link("sale/ompro/printOrders", $strtoken . "&template_id=" . $template_id, "SSL");
                                                        $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_print_orders" . $ending, $data));
                                                    } else {
                                                        if ($get_page == "print_orders_table") {
                                                            $data["preview"] = $this->url->link("sale/ompro/printOrdersTable", $strtoken, "SSL");
                                                            $data["print_blocks"] = $this->ompro->getTemplateBlocksTarget("print_orders_table");
                                                            $data["orders_tables"] = $this->omproapi->getTableTemplates("orders");
                                                            $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_print_orders_table" . $ending, $data));
                                                        } else {
                                                            if ($get_page == "print_products_table") {
                                                                $data["product_group_by_list"] = $this->omproapi->getProductGroupByList();
                                                                $data["template"]["product_group_by"] = isset($data["template"]["product_group_by"]) ? $data["template"]["product_group_by"] : "op.product_id";
                                                                $data["preview"] = $this->url->link("sale/ompro/printProductsTable", $strtoken . "&template_id=" . $template_id, "SSL");
                                                                $data["print_products_table_blocks"] = $this->ompro->getTemplateBlocksTarget("print_products_table");
                                                                $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_print_products_table" . $ending, $data));
                                                            } else {
                                                                if ($get_page == "excel_orders") {
                                                                    $data["preview"] = $this->url->link("sale/ompro/excelOrders", $strtoken . "&template_id=" . $template_id, "SSL");
                                                                    $data["row_list"] = array("header" => "ЗАГОЛОВОК", "order" => "ДАННЫЕ ЗАКАЗА", "product" => "ДАННЫЕ ТОВАРА", "footer" => "ПОДВАЛ");
                                                                    $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_excel_orders" . $ending, $data));
                                                                } else {
                                                                    if ($get_page == "excel_orders_products") {
                                                                        $data["preview"] = $this->url->link("sale/ompro/excelOrdersProducts", $strtoken . "&template_id=" . $template_id, "SSL");
                                                                        $data["product_group_by_list"] = $this->omproapi->getProductGroupByList();
                                                                        $data["template"]["product_group_by"] = isset($data["template"]["product_group_by"]) ? $data["template"]["product_group_by"] : "op.product_id";
                                                                        $data["row_list"] = array("header" => "ЗАГОЛОВОК", "product" => "ДАННЫЕ ТОВАРА", "footer" => "ПОДВАЛ");
                                                                        $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_excel_orders_products" . $ending, $data));
                                                                    } else {
                                                                        if ($get_page == "pages") {
                                                                            $data["btn_icons"] = $this->omproapi->genAwesomeIcons();
                                                                            $data["order_format_list"] = $this->omproapi->orderDataFormatTypeList();
                                                                            $data["product_format_list"] = $this->omproapi->productDataFormatTypeList();
                                                                            $data["order_format_method_list"] = $this->omproapi->orderDataFormatMethodList();
                                                                            $data["product_format_method_list"] = $this->omproapi->productDataFormatMethodList();
                                                                            $data["order_codes"] = $this->ompro->orderFieldsData("codes");
                                                                            $data["product_codes"] = $this->ompro->productFieldsData("codes");
                                                                            $data["pptriggers"] = $this->ompro->getTemplateBlocksTarget("pptrigger");
                                                                            $data["page_blocks"] = array();
                                                                            $data["page_blocks"][] = array("target" => "blocks_el", "class" => "blocks-el", "icon" => "columns", "text" => "Элементы конструктора: ряды, колонки, боксы", "templates" => $this->ompro->getTemplatePageBlocksTarget("blocks_el"));
                                                                            $data["page_blocks"][] = array("target" => "column_el", "class" => "column-el", "icon" => "server", "text" => "Элементы конструктора: эл-ты колонок", "templates" => $this->ompro->getTemplatePageBlocksTarget("column_el"));
                                                                            $data["page_blocks"][] = array("target" => "tools_el", "class" => "tools-el", "icon" => "ellipsis-h", "text" => "Элементы конструктора: эл-ты панели инструментов", "templates" => $this->ompro->getTemplatePageBlocksTarget("tools_el"));
                                                                            $data["page_blocks"][] = array("target" => "btngroup_el", "class" => "btngroup-el", "icon" => "object-group", "text" => "Элементы конструктора: эл-ты групп кнопок и полей", "templates" => $this->ompro->getTemplatePageBlocksTarget("btngroup_el"));
                                                                            $langCode = substr(strtolower($this->omproapi->getLanguageCode()), 0, 2);
                                                                            $langCode == "en" || $langCode == "ru" ? $langCode : "ru";
                                                                            $data["summernote_lang"] = $langCode == "ru" ? "ru-RU" : "en-GB";
                                                                            $data["page_html_content_vars"] = $this->omproapi->pageHtmlElemVarsList();
                                                                            $data["filters"] = $this->ompro->getAllTemplatesList("filter");
                                                                            $data["filters_product"] = $this->ompro->getAllTemplatesList("filter_product");
                                                                            $data["btngroups"] = $this->omproapi->btngroups();
                                                                            $data["ordertpl_list"] = $this->ompro->getAllTemplatesList("orders");
                                                                            $data["order_table_templates_list"] = $data["ordertpl_list"];
                                                                            $data["btn_actions"] = $this->omproapi->btnActionList(true);
                                                                            $data["pageid"] = $template_id;
                                                                            $data["setting"] = $data["template"];
                                                                            $pp_tpl = isset($data["setting"]["pp_template_id"]) ? $data["setting"]["pp_template_id"] : "";
                                                                            if ($pp_tpl && is_numeric($pp_tpl)) {
                                                                                $data["setting"]["pp_template_id"] = $this->ompro->getTemplateCode("product", $pp_tpl);
                                                                            }
                                                                            $product_table_templates = $this->ompro->getAllTemplatesList("product");
                                                                            $data["product_table_templates"] = array();
                                                                            foreach ($product_table_templates as $template) {
                                                                                if ($pp_tpl == $template["code"]) {
                                                                                    $selected = "selected";
                                                                                } else {
                                                                                    $selected = "";
                                                                                }
                                                                                $data["product_table_templates"][] = array("template_id" => $template["template_id"], "code" => $template["code"], "name" => $template["name"], "selected" => $selected);
                                                                            }
                                                                            $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_page" . $ending, $data));
                                                                        } else {
                                                                            if ($get_page == "page_block") {
                                                                                if (isset($this->request->get["block_target"])) {
                                                                                    $block_target = $this->request->get["block_target"];
                                                                                } else {
                                                                                    $block_target = "blocks_el";
                                                                                }
                                                                                $data["block_target"] = $block_target;
                                                                                $data["heading_title_small"] = $data["text_page_block_" . $block_target . "_list"];
                                                                                $data["table_btn_actions"] = $this->omproapi->getTableBtnAction();
                                                                                $data["table_option_vars"] = $this->omproapi->getTableOptionVars();
                                                                                $data["table_page_value_vars"] = $this->omproapi->getTablePageValueVars();
                                                                                $data["quick_filter_trigger_info"] = $this->omproapi->getQuickFilterTriggerInfo();
                                                                                $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_page_block" . $ending, $data));
                                                                            } else {
                                                                                if ($get_page == "block") {
                                                                                    if (isset($this->request->get["block_target"])) {
                                                                                        $block_target = $this->request->get["block_target"];
                                                                                    } else {
                                                                                        $block_target = "order";
                                                                                    }
                                                                                    $data["block_target"] = $block_target;
                                                                                    $data["heading_title_small"] = $data["text_block_" . $block_target . "_list"];
                                                                                    $data["doc_page"] = "";
                                                                                    if ($block_target == "product") {
                                                                                        $data["doc_page"] = "ompro_tpl_block_product.html";
                                                                                        $data["text_template_block_info"] = "Данный шаблон может быть загружен при редактировании данных с использовании редактора Summernote в ячейку шаблона Таблицы товаров. Для вставки данных используются переменные <a onclick=\"getTableCodes('product');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\"> см. Таблицу переменных товара</a>";
                                                                                        $data["html_codes_panel_heading"] = "<div class=\"panel-heading\"><a onclick=\"getTableCodes('product');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\">Таблица переменных</a></div>";
                                                                                        $data["html_codes_panel_body"] = "<div class=\"panel-body\"><div class=\"row\">" . "<div id=\"adding_codes_block\" class=\"col-lg-12\">" . "<h5><i class=\"fa fa-exclamation-triangle text-blue\"></i> <strong>" . $data["entry_adding_codes"] . "</strong></h5>" . "<div class=\"callout callout-default\"><p>" . $data["adding_codes_product_info"] . "</p></div>" . "</div></div></div>";
                                                                                    } else {
                                                                                        if ($block_target == "pptrigger") {
                                                                                            $data["doc_page"] = "ompro_tpl_block_product_trigger.html";
                                                                                            $data["text_template_block_info"] = "Данный шаблон может быть загружен с использовании редактора Summernote при редактировании шаблона Страниц заказов на вкладке `Настройка списка заказов` в окне шаблона `Кнопка модального окна товаров`.";
                                                                                            $data["html_codes_panel_heading"] = "";
                                                                                            $data["html_codes_panel_body"] = "<div class=\"panel-body\"><div class=\"row\">" . "<div class=\"col-lg-12\">" . "<h5><i class=\"fa fa-exclamation-triangle text-blue\"></i> <strong>" . $data["entry_codes"] . "</strong></h5>" . "<div class=\"callout callout-default\"><p>" . $data["adding_codes_pptrigger_info"] . "</p></div>" . "</div></div></div>";
                                                                                        } else {
                                                                                            if ($block_target == "order") {
                                                                                                $data["doc_page"] = "ompro_tpl_block_order.html";
                                                                                                $data["text_template_block_info"] = "Данный шаблон может быть загружен при редактировании данных с использовании редактора Summernote в ячейку шаблона Таблицы заказов. Для вставки данных используются переменные <a onclick=\"getTableCodes('order');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\"> см. Таблицу переменных заказа</a>";
                                                                                                $data["html_codes_panel_heading"] = "<div class=\"panel-heading\"><a data-toggle=\"collapse\" href=\"#btn-product-info\"><i class=\"fa fa-sort text-blue\"></i>&nbsp;&nbsp; ПЕРЕМЕННЫЕ (доп.)</a></div>";
                                                                                                $data["html_codes_panel_body"] = "<div id=\"btn-product-info\" class=\"panel-collapse collapse\"><div class=\"panel-body\"><div class=\"row\">" . "<div class=\"col-sm-12 table-responsive\">" . $this->omproapi->getTableTemplates("product") . "</div></div></div></div>";
                                                                                                $btn_tables = array("print_orders", "print_orders_table", "print_products_table", "send_mail", "send_sms", "send_tlgrm", "excel_orders", "excel_orders_products");
                                                                                                foreach ($btn_tables as $table) {
                                                                                                    $data["table_btn_" . $table] = $this->omproapi->getTableBtnHtmlTemplates($table);
                                                                                                }
                                                                                                $data["table_btn_openwindow"] = $this->omproapi->getTableBtnOpenWindow();
                                                                                                $data["table_btn_action_adding"] = $this->omproapi->getTableBtnActionAdding();
                                                                                                $data["table_btn_action_quick_status"] = $this->omproapi->getTableBtnActionQuickStatus();
                                                                                            } else {
                                                                                                if ($block_target == "mail") {
                                                                                                    $data["doc_page"] = "ompro_tpl_block_mail.html";
                                                                                                    $data["text_template_block_info"] = "Данный шаблон может быть загружен с использованием редактора Summernote в шаблонах Писем. Для вставки данных используются переменные <a onclick=\"getTableCodes('order');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\"> см. Таблицу переменных заказа</a>";
                                                                                                    $data["html_codes_panel_heading"] = "<div class=\"panel-heading\"><a data-toggle=\"collapse\" href=\"#btn-product-info\"><i class=\"fa fa-sort text-blue\"></i>&nbsp;&nbsp; ПЕРЕМЕННЫЕ (доп.)</a></div>";
                                                                                                    $data["html_codes_panel_body"] = "<div id=\"btn-product-info\" class=\"panel-collapse collapse\"><div class=\"panel-body\"><div class=\"row\">" . "<div class=\"col-lg-6 col-sm-12\">" . "<a class=\"btn btn-default btn-block\" onclick=\"getTableCodes('product_table');\"><i class=\"fa fa-th-list\"></i>&nbsp;  " . $data["btn_product_table_tpl"] . "</a>" . "<div class=\"btn-group btn-group-vertical btn-block\">" . "<a class=\"btn btn-default text-red\" onclick=\"getTableCodes('print_orders');\"><i class=\"fa fa-file-pdf-o\"></i>&nbsp;  " . $data["btn_print_orders_table_tpl"] . "</a><a class=\"btn btn-default text-red\" onclick=\"getTableCodes('print_orders_table');\"><i class=\"fa fa-file-pdf-o\"></i>&nbsp; " . $data["btn_print_orders_table_table_tpl"] . "</a><a class=\"btn btn-default text-red\" onclick=\"getTableCodes('print_products_table');\"><i class=\"fa fa-file-pdf-o\"></i>&nbsp;  " . $data["btn_print_products_table_table_tpl"] . "</a>" . "</div>" . "<div class=\"btn-group btn-group-vertical btn-block\">" . "<a class=\"btn btn-default text-green\" onclick=\"getTableCodes('excel_orders');\"><i class=\"fa fa-file-excel-o\"></i>&nbsp;  " . $data["btn_excel_orders_table_tpl"] . "</a><a class=\"btn btn-default text-green\" onclick=\"getTableCodes('excel_orders_products');\"><i class=\"fa fa-file-excel-o\"></i>&nbsp;  " . $data["btn_excel_orders_products_table_tpl"] . "</a>" . "</div>" . "</div>" . "<div class=\"col-lg-6 col-sm-12\"><div class=\"panel-body\">" . "<h5><i class=\"fa fa-exclamation-triangle text-blue\"></i> <strong>" . $data["entry_adding_codes"] . "</strong></h5>" . "<div class=\"callout callout-default\"><p>" . $data["adding_codes_mail_info"] . "</p></div>" . "</div></div></div></div></div>";
                                                                                                } else {
                                                                                                    if ($block_target == "print_orders_table") {
                                                                                                        $data["doc_page"] = "ompro_tpl_block_print_order.html";
                                                                                                        $data["text_template_block_info"] = "Данный шаблон может быть загружен с использованием редактора Summernote в Шаблон печати таблиц заказов. Для вставки данных в шаблон используются переменные таблиц заказов см. в блоке ПЕРЕМЕННЫЕ.";
                                                                                                        $data["html_codes_panel_heading"] = "<div class=\"panel-heading\"><a data-toggle=\"collapse\" href=\"#btn-orders-info\"><i class=\"fa fa-sort text-blue\"></i>&nbsp;&nbsp; ПЕРЕМЕННЫЕ</a></div>";
                                                                                                        $data["html_codes_panel_body"] = "<div id=\"btn-orders-info\" class=\"panel-collapse collapse\"><div class=\"panel-body\"><div class=\"row\">" . "<div class=\"col-sm-12 table-responsive\">" . $this->omproapi->getTableTemplates("orders") . "</div></div></div></div>";
                                                                                                    } else {
                                                                                                        if ($block_target == "print_products_table") {
                                                                                                            $data["doc_page"] = "ompro_tpl_block_print_product.html";
                                                                                                            $data["text_template_block_info"] = "Данный шаблон может быть загружен при редактировании данных с использовании редактора Summernote в ячейку шаблона Печати таблицы товаров. Для вставки данных используются переменные <a onclick=\"getTableCodes('product');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\"> см. Таблицу переменных товара</a>";
                                                                                                            $data["html_codes_panel_heading"] = "<div class=\"panel-heading\"><a onclick=\"getTableCodes('product');\" style=\"font-weight: normal; cursor: pointer; color: #3c8dbc;\">Таблица переменных</a></div>";
                                                                                                            $data["html_codes_panel_body"] = "<div class=\"panel-body\"><div class=\"row\">" . "<div id=\"adding_codes_block\" class=\"col-lg-12\">" . "<h5><i class=\"fa fa-exclamation-triangle text-blue\"></i> <strong>" . $data["entry_adding_codes"] . "</strong></h5>" . "<div class=\"callout callout-default\"><p>" . $data["adding_codes_product_info"] . "</p></div>" . "</div></div></div>";
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                    $this->response->setOutput($this->load->view("sale/ompro/ompro_tpl_block" . $ending, $data));
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
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function saveTemplateBlock()
    {
        $this->init();
        $json = array();
        if (isset($this->request->post["template"])) {
            $template_id = $this->ompro->addTemplate("block", $this->request->post["template"]);
            if ($template_id) {
                $json["template_id"] = $template_id;
                $json["success"] = $this->language->get("text_save_block_success");
            } else {
                $json["error"] = $this->language->get("text_error_save_block");
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function loadTemplateBlock()
    {
        $this->init();
        $json = array();
        $template_id = $this->request->get["template_id"];
        $result = $this->ompro->getTemplateBlock($template_id);
        if (isset($result["template"])) {
            $json["tpl"] = html_entity_decode($result["template"], ENT_QUOTES, "UTF-8");
        } else {
            $json["error"] = $this->language->get("text_error_load_block");
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function previewFilter()
    {
        $this->init();
        $json = array();
        $filter_info = array();
        $html = "";
        if (isset($this->request->get["type"]) && isset($this->request->get["template_id"])) {
            $filter_info = $this->ompro->getTemplateTemplate($this->request->get["type"], $this->request->get["template_id"]);
        } else {
            if (isset($this->request->get["type"]) && isset($this->request->get["filter_id"])) {
                $filter_info = $this->ompro->getFilterTemplateByFilterId($this->request->get["type"], $this->request->get["filter_id"]);
            }
        }
        if ($filter_info) {
            $filter_type = $this->request->get["type"];
            if (isset($this->request->get["location"]) && $this->request->get["location"] == "top") {
                $html .= "<label class=\"control-label\">" . $filter_info["name"] . ":</label>";
            }
            $user_group_id = $this->user->getGroupId();
            $setting_user_group = $this->ompro->getSettingGroup($user_group_id);
            $html .= $this->omproapi->createHTMLFilter($setting_user_group, $filter_info, $filter_reload = 0, $filter_value = "", $filter_type);
            $html .= "<script type=\"text/javascript\"><!-- \$(document).ready(function(){\$('[data-mask]').inputmask();}); --></script>";
            if (empty($html)) {
                $json["error"] = $this->language->get("text_no_results");
            } else {
                $json["tpl"] = trim(html_entity_decode($html, ENT_QUOTES, "UTF-8"));
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function previewMail()
    {
        $this->init();
        $subject = $message = "";
        $json = array();
        if ($this->ompro->checkOrder($this->request->get["order_id"])) {
            $order_id = $this->request->get["order_id"];
            $this->load->model("tool/image");
            $template_info = $this->ompro->getTemplateTemplate("mail", $this->request->get["template_id"]);
            $test_comment = trim(html_entity_decode(nl2br($this->request->get["test_comment"]), ENT_QUOTES, "UTF-8"));
            if (!empty($template_info)) {
                $subject = $message = "";
                $all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
                if (!empty($all_orders_data) && !empty($all_orders_data["param"]) && !empty($all_orders_data["orders"]) && !empty($all_orders_data["orders"][$order_id])) {
                    $language_id = isset($order_info["language_id"]) && $order_info["language_id"] ? $order_info["language_id"] : $this->config->get("config_language_id");
                    if (!empty($template_info["subject"][$language_id])) {
                        $subject = $template_info["subject"][$language_id];
                        $subject = $this->ompro->replaceVarsOrderData($subject, $order_id, $all_orders_data, $template_info, $language_id, $test_comment);
                        $subject = $this->ompro->replaceOneVar("recipient_name", "", $subject);
                    }
                    if (!empty($template_info["message"][$language_id])) {
                        $message = $template_info["message"][$language_id];
                        $message = $this->ompro->replaceVarsOrderData($message, $order_id, $all_orders_data, $template_info, $language_id, $test_comment);
                        $message = $this->ompro->replaceOneVar("recipient_name", "", $this->ompro->clearAttach($message));
                    }
                }
            }
        } else {
            $json["error"] = $this->language->get("error_not_found");
        }
        $json["subject"] = trim(html_entity_decode($subject, ENT_QUOTES, "UTF-8"));
        $json["message"] = trim(html_entity_decode($message, ENT_QUOTES, "UTF-8"));
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function sendTestMail()
    {
        $this->init();
        $json = array();
        $template_info = $this->ompro->getTemplateTemplate("mail", $this->request->get["template_id"]);
        $order_id = $this->request->get["order_id"];
        if (!empty($template_info)) {
            $test_comment = trim(html_entity_decode(nl2br($this->request->get["test_comment"]), ENT_QUOTES, "UTF-8"));
            $subject = $message = "";
            $all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
            if (!empty($all_orders_data) && !empty($all_orders_data["param"]) && !empty($all_orders_data["orders"]) && !empty($all_orders_data["orders"][$order_id])) {
                $order_info = $all_orders_data["orders"][$order_id]["order_info"];
                $language_id = isset($order_info["language_id"]) && $order_info["language_id"] ? $order_info["language_id"] : $this->config->get("config_language_id");
                if (!empty($template_info["subject"][$language_id])) {
                    $subject = $template_info["subject"][$language_id];
                    $subject = $this->ompro->replaceVarsOrderData($subject, $order_id, $all_orders_data, $template_info, $language_id, $test_comment);
                    $subject = $this->ompro->replaceOneVar("recipient_name", "", $subject);
                }
                $attachments = array();
                if (!empty($template_info["message"][$language_id])) {
                    $message = $template_info["message"][$language_id];
                    $message = $this->ompro->replaceVarsOrderData($message, $order_id, $all_orders_data, $template_info, $language_id, $test_comment);
                    $message = $this->ompro->replaceOneVar("recipient_name", "", $message);
                    $attach = $this->ompro->attachToMail($all_orders_data, $message);
                    $message = $attach["message"];
                    $attachments = $attach["attachments"];
                }
                $style_body = isset($template_info["style"]["body"]) ? $template_info["style"]["body"] : "";
                $style_body = str_replace(array("\r\n", "\r", "\n"), "", preg_replace("/[\\s]{2,}/", " ", trim($style_body)));
                $style_div = isset($template_info["style"]["div"]) ? $template_info["style"]["div"] : "";
                $style_div = str_replace(array("\r\n", "\r", "\n"), "", preg_replace("/[\\s]{2,}/", " ", trim($style_div)));
                $html = "<html dir=\"ltr\" lang=\"en\">" . "\n";
                $html .= "  <head>" . "\n";
                $html .= "    <title>" . $subject . "</title>" . "\n";
                $html .= "    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">" . "\n";
                $html .= "  </head>" . "\n";
                $html .= "  <body style=\"" . $style_body . "\">" . "\n";
                $html .= "    <div style=\"" . $style_div . "\">" . html_entity_decode($message, ENT_QUOTES, "UTF-8") . "</div>" . "\n";
                $html .= "  </body>" . "\n";
                $html .= "</html>" . "\n";
                $email = $all_orders_data["param"]["user_info"]["user_email"];
                $store_name = $order_info["store_name"];
                $store_email = $order_info["store_email"];
                if (preg_match("/^[^@]+@.*.[a-z]{2,15}\$/i", $email)) {
                    if (300 <= $this->ompro->ocversion) {
                        $mail = new Mail($this->config->get("config_mail_engine"));
                    } else {
                        $mail = new Mail();
                        $mail->protocol = $this->config->get("config_mail_protocol");
                    }
                    $mail->parameter = $this->config->get("config_mail_parameter");
                    $mail->smtp_hostname = $this->config->get("config_mail_smtp_hostname");
                    $mail->smtp_username = $this->config->get("config_mail_smtp_username");
                    $mail->smtp_password = html_entity_decode($this->config->get("config_mail_smtp_password"), ENT_QUOTES, "UTF-8");
                    $mail->smtp_port = $this->config->get("config_mail_smtp_port");
                    $mail->smtp_timeout = $this->config->get("config_mail_smtp_timeout");
                    if ($attachments) {
                        foreach ($attachments as $filename) {
                            $mail->addAttachment(DIR_DOWNLOAD . $filename);
                        }
                    }
                    $mail->setTo($email);
                    $mail->setFrom($store_email);
                    $mail->setSender(html_entity_decode($store_name, ENT_QUOTES, "UTF-8"));
                    $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, "UTF-8"));
                    $mail->setHtml($html);
                    $mail->send();
                    $json["success"] = $this->language->get("text_send_mail_success") . ": " . $order_id;
                } else {
                    $json["error"] = $this->language->get("text_send_mail_error") . ": " . $order_id;
                }
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function excelOrders()
    {
        $this->init();
        if (isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("excel_orders", $this->request->get["template_id"]);
            if (!empty($template_info)) {
                $this->ompro->excelOrders($this->ompro->selectedOrdersData(), $template_info);
            } else {
                $data["html"] = "Error: Template with ID = " . $this->request->get["template_id"] . " was not found!";
            }
        }
    }
    public function excelOrdersProducts()
    {
        $this->init();
        if (isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("excel_orders_products", $this->request->get["template_id"]);
            if (!empty($template_info)) {
                $order_ides = array();
                if (isset($this->request->post["selected"])) {
                    $order_ides = $this->request->post["selected"];
                } else {
                    if (isset($this->request->get["order_id"])) {
                        $order_ides[] = $this->request->get["order_id"];
                    }
                }
                $this->ompro->excelOrdersProducts($order_ides, $template_info);
            } else {
                $data["html"] = "Error: Template with ID = " . $this->request->get["template_id"] . " was not found!";
            }
        }
    }
    public function printHtml($data = array())
    {
        $this->document->setTitle($data["heading_title"]);
        $data["base"] = $this->ompro->server;
        $data["direction"] = $this->language->get("direction");
        $data["lang"] = $this->language->get("code");
        $ending = 230 <= $this->ompro->ocversion ? "" : ".tpl";
        $this->response->setOutput($this->load->view("sale/ompro/ompro_print" . $ending, $data));
    }
    public function printOrders()
    {
        $data = $this->init();
        $data["html"] = "";
        $data["title"] = $data["html"];
        if (isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("print_orders", $this->request->get["template_id"]);
            if (!empty($template_info)) {
                $data["title"] = $template_info["name"];
                $all_orders_data = $this->ompro->selectedOrdersData();
                if (isset($this->request->get["return_type"])) {
                    $data["html"] = $this->ompro->printOrdersHTML($all_orders_data, $template_info, $this->request->get["return_type"]);
                } else {
                    $data["html"] = $this->ompro->printOrdersHTML($all_orders_data, $template_info);
                }
            } else {
                $data["html"] = "Error: Template with ID = " . $this->request->get["template_id"] . " was not found!";
            }
        }
        $this->printHtml($data);
    }
    public function printOrdersTable()
    {
        $data = $this->init();
        $data["html"] = "";
        $data["title"] = $data["html"];
        if (isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("print_orders_table", $this->request->get["template_id"]);
            if (!empty($template_info)) {
                $data["title"] = $template_info["name"];
                $all_orders_data = $this->ompro->selectedOrdersData();
                if (isset($this->request->get["return_type"])) {
                    $data["html"] = $this->ompro->printOrdersTableHTML($all_orders_data, $template_info, $this->request->get["return_type"]);
                } else {
                    $data["html"] = $this->ompro->printOrdersTableHTML($all_orders_data, $template_info);
                }
            } else {
                $data["html"] = "Error: Template with ID = " . $this->request->get["template_id"] . " was not found!";
            }
        }
        $this->printHtml($data);
    }
    public function printProductsTable()
    {
        $data = $this->init();
        $data["html"] = "";
        $data["title"] = $data["html"];
        if (isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("print_products_table", $this->request->get["template_id"]);
            if (!empty($template_info)) {
                $data["title"] = $template_info["name"];
                $order_ides = array();
                if (isset($this->request->post["selected"])) {
                    $order_ides = $this->request->post["selected"];
                } else {
                    if (isset($this->request->get["order_id"])) {
                        $order_ides[] = $this->request->get["order_id"];
                    }
                }
                if (isset($this->request->get["return_type"])) {
                    $return_type = $this->request->get["return_type"];
                    $data["html"] = $this->ompro->printProductsTableHTML($order_ides, $template_info, $return_type);
                } else {
                    $data["html"] = $this->ompro->printProductsTableHTML($order_ides, $template_info);
                }
            } else {
                $data["html"] = "Error: Template with ID = " . $this->request->get["template_id"] . " was not found!";
            }
        }
        $this->printHtml($data);
    }
    public function orderHistories($order_id = 0, $template_id = 0, $return_html = false)
    {
        $this->init();
        if (!$order_id && isset($this->request->get["order_id"])) {
            $order_id = $this->request->get["order_id"];
        }
        if (!$template_id && isset($this->request->get["history_template_id"])) {
            $template_id = $this->request->get["history_template_id"];
        }
        $html = $this->ompro->orderHistories($order_id, $template_id, $return_html);
        if ($return_html) {
            return $html;
        }
        $this->response->setOutput(html_entity_decode($html, ENT_QUOTES, "UTF-8"));
    }
    public function getMapsJData($all_orders_data = array())
    {
        $this->load->language("sale/ompro");
        $map_orders = array();
        $points = array();
        $this->load->model("localisation/country");
        $this->load->model("localisation/zone");
        $param = $orders = array();
        if (!empty($all_orders_data)) {
            $param = $all_orders_data["param"];
            $orders = $all_orders_data["orders"];
        }
        if (!empty($orders)) {
            $statuses = array();
            if (!empty($param["setting_user_group"]["order_statuses"])) {
                $statuses = $param["setting_user_group"]["order_statuses"];
            }
            $apikey = "";
            if (isset($param["setting_page"]["ymap_apikey"]) && $param["setting_page"]["ymap_apikey"]) {
                $apikey = $param["setting_page"]["ymap_apikey"];
            }
            foreach ($orders as $order) {
                $order_info = $order["order_info"];
                $order_id = $order_info["order_id"];
                $preset = "islands#dotIcon";
                $iconColor = isset($statuses[$order_info["order_status_id"]]["text_color_id"]) && !empty($statuses[$order_info["order_status_id"]]["text_color_id"]) && $statuses[$order_info["order_status_id"]]["text_color_id"] !== "FFFFFF" ? "#" . $statuses[$order_info["order_status_id"]]["text_color_id"] : "#0095B6";
                if (isset($order_info["shipping_latitude"]) && $order_info["shipping_latitude"] !== "0.000000" && isset($order_info["shipping_longitude"]) && $order_info["shipping_longitude"] !== "0.000000") {
                    $coordinates = array($order_info["shipping_latitude"], $order_info["shipping_longitude"]);
                } else {
                    $address = "[[{shipping_country}]], [[{shipping_zone}]], [[{shipping_city}]], [[{shipping_address_1}]], [[{shipping_address_2}]]";
                    if (!empty($param["setting_page"]["map_address_format"])) {
                        $address = $param["setting_page"]["map_address_format"];
                    }
                    foreach ($order_info as $key => $val) {
                        $pattern = "/\\[\\[([^\\[\\]]*?)\\{" . $key . "\\}(.*?)\\]\\]/s";
                        if (preg_match_all($pattern, $address, $matches, PREG_SET_ORDER)) {
                            $address = $this->ompro->replaceMatches($matches, $address, array("[[", "]]"), "{" . $key . "}", $val);
                        }
                    }
                    $coordinates = $this->getCoordinats($address, $order_id, $apikey);
                }
                $balloon_no_coords = "";
                if (!$coordinates) {
                    $balloon_no_coords = $this->language->get("balloon_no_coords");
                    if (isset($param["setting_page"]["map_coords_default"]) && !empty($param["setting_page"]["map_coords_default"])) {
                        $coordinates = explode(",", $param["setting_page"]["map_coords_default"]);
                        if (!is_array($coordinates)) {
                            $coordinates = array("55.750358", "37.611149");
                        }
                    } else {
                        $coordinates = array("55.750358", "37.611149");
                    }
                }
                $geometry = array("type" => "Point", "coordinates" => $coordinates);
                $properties = array("balloonContent" => "", "clusterCaption" => $balloon_no_coords . $this->language->get("balloon_order_id") . $order_id, "hintContent" => $balloon_no_coords . "#" . $order_id . "&nbsp;" . $order_info["shipping_address_1"]);
                $options = array("preset" => $preset, "iconColor" => $iconColor);
                $points[] = array("type" => "Feature", "id" => $order_id, "geometry" => $geometry, "properties" => $properties, "options" => $options);
            }
        }
        $map_orders = array("type" => "FeatureCollection", "features" => $points);
        return json_encode($map_orders);
    }
    public function geocode($adress = "", $apikey = "")
    {
        $coords = array();
        if (!empty($adress) && !empty($apikey)) {
            $apikey = $apikey == "" ? "" : "apikey=" . trim($apikey);
            $xml = @simplexml_load_file("https://geocode-maps.yandex.ru/1.x/?" . @trim($apikey) . "&geocode=" . @urlencode($adress) . "&results=1");
            if ($xml !== false && 0 < $xml->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found) {
                $coords = explode(" ", $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);
                list($coords[0], $coords[1]) = array($coords[1], $coords[0]);
            }
        }
        return $coords;
    }
    public function getCoordinats($address = "", $order_id, $apikey = "")
    {
        $coords = $this->geocode($address, $apikey);
        if ($coords) {
            $this->db->query("UPDATE `" . DB_PREFIX . "order` SET shipping_latitude = '" . (double) $coords[0] . "', shipping_longitude = '" . (double) $coords[1] . "' WHERE order_id = '" . (int) $order_id . "'");
        }
        return $coords;
    }
    public function getMapBalloonData()
    {
        $this->init();
        $json = array();
        if (isset($this->request->get["order_id"]) && !empty($this->request->get["order_id"])) {
            $order_id = $this->request->get["order_id"];
            $ballooncontent = "";
            $orders = $this->ompro->getOrders($this->ompro->filterData(), array($order_id));
            $order_info = $orders[0];
            if (!empty($order_info)) {
                $telephone = !empty($order_info["telephone"]) ? " (" . $order_info["telephone"] . ") " : "";
                $order_info["order_status"] = $order_info["order_status"] == "" ? $this->language->get("text_missing") : $order_info["order_status"];
                $ballooncontent .= "<b>" . $this->language->get("balloon_order_id") . $order_id . "</b><br/>" . $this->language->get("balloon_status") . $order_info["order_status"] . "<br/>" . $this->language->get("balloon_recipient") . $order_info["shipping_firstname"] . " " . $order_info["shipping_lastname"] . $telephone . "<br/>" . $this->language->get("balloon_adress") . $order_info["shipping_address_1"] . "<br/>" . $this->language->get("balloon_total") . $this->currency->format($order_info["total"], $this->config->get("config_currency")) . "<br/>" . $this->language->get("balloon_shipping") . $order_info["shipping_method"] . "<br/>" . $this->language->get("balloon_payment") . $order_info["payment_method"];
            }
            $products = array();
            $order_products = $this->ompro->getOrderProducts($order_id);
            if (!empty($order_products)) {
                foreach ($order_products as $product) {
                    $products[] = array("product_id" => $product["product_id"], "name" => $product["name"], "quantity" => $product["quantity"], "price" => $this->currency->format($product["price"], $this->config->get("config_currency")));
                }
            }
            if (!empty($products)) {
                $ballooncontent .= "<table style=\"border-top:1px solid #DDDDDD;border-left:1px solid #DDDDDD;border-collapse:collapse;width:100%;margin-top:5px;\"><tbody>";
                foreach ($products as $product) {
                    $ballooncontent .= "<tr>";
                    $ballooncontent .= "<td style=\"text-align:center;border-right:1px solid #DDDDDD;border-bottom:1px solid #DDDDDD;\">" . $product["product_id"] . "</td>";
                    $ballooncontent .= "<td style=\"text-align:left;border-right:1px solid #DDDDDD;border-bottom:1px solid #DDDDDD;line-height:1;padding-left:3px;\">" . $product["name"] . "</td>";
                    $ballooncontent .= "<td style=\"text-align:right;border-right:1px solid #DDDDDD;border-bottom:1px solid #DDDDDD;white-space:nowrap;padding-left:3px;padding-right:3px;\">" . $product["price"] . "</td>";
                    $ballooncontent .= "<td style=\"text-align:right;border-right:1px solid #DDDDDD;border-bottom:1px solid #DDDDDD;padding-left:3px;padding-right:3px;\">" . $product["quantity"] . "</td>";
                    $ballooncontent .= "</tr>";
                }
                $ballooncontent .= "</tbody></table>";
            }
            $json = $ballooncontent;
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function orderTplView()
    {
        $this->init();
        if (isset($this->request->get["pageid"]) && $this->request->get["pageid"]) {
            $pageid = $this->request->get["pageid"];
        } else {
            $pageid = $this->ompro->getFirstPageID();
        }
        $html = "";
        if (isset($this->request->get["order_id"]) && isset($this->request->get["template_id"])) {
            $all_orders_data = $this->ompro->getOrdersDataAll(array($this->request->get["order_id"]));
            $all_orders_data["param"]["template_id"] = $this->request->get["template_id"];
            $all_orders_data["param"]["to_edit"] = isset($this->request->get["to_edit"]) ? true : false;
            $all_orders_data["param"]["construct"] = true;
            $all_orders_data["total"] = 0;
            $html = $this->getOrdersTable($all_orders_data);
            $html = $this->constructHTML($html, $all_orders_data);
        }
        $this->response->setOutput($html);
    }
    public function ordersTableView()
    {
        $this->init();
        $html = "";
        $filters = "";
        $template_id = 0;
        $all_orders_data = array();
        if (isset($this->request->get["filters"]) && isset($this->request->get["template_id"])) {
            $template_id = $this->request->get["template_id"];
            $filters = $this->request->get["filters"];
            $all_orders_data = $this->ompro->getOrdersDataAll();
            $all_orders_data["param"]["template_id"] = $template_id;
            $all_orders_data["param"]["to_edit"] = isset($this->request->get["to_edit"]) ? true : false;
            $all_orders_data["param"]["construct"] = true;
            $all_orders_data["total"] = 0;
            $html = $this->getOrdersTable($all_orders_data);
            $html = $this->constructHTML($html, $all_orders_data);
        }
        if (!empty($all_orders_data["param"]) && !empty($all_orders_data["param"]["filter_data"])) {
            $filter_data = $all_orders_data["param"]["filter_data"];
        } else {
            $filter_data = array();
        }
        $total = $this->ompro->getTotalOrders($filter_data);
        if (isset($this->request->get["orders_table_view_page"])) {
            $page = $filter_data["page"];
        } else {
            $page = 1;
        }
        $limit = $filter_data["limit"];
        $pagination = new Pagination();
        $pagination->total = $total;
        $pagination->page = $page;
        $pagination->limit = $limit;
        $pagination->url = $this->url->link("sale/ompro/ordersTableView", $this->strToken() . "&template_id=" . $template_id . "&filters=" . $filters . "&limit=" . $limit . "&orders_table_view_page=true&page={page}", true);
        $pagination = $pagination->render();
        $pagination_results = sprintf($this->language->get("text_pagination"), $total ? ($page - 1) * $limit + 1 : 0, $total - $limit < ($page - 1) * $limit ? $total : ($page - 1) * $limit + $limit, $total, ceil($total / $limit));
        $html .= "\n\t\t  <div class=\"panel-body\" style=\"padding-top: 20px;\">\n\t\t  <div class=\"row\">\n\t\t\t<div class=\"col-sm-6 text-left pagination-sm\">" . $pagination . "</div>\n\t\t\t<div class=\"col-sm-6 text-right\">" . $pagination_results . "</div>\n\t\t  </div></div>";
        $this->response->setOutput($html);
    }
    public function orderReload()
    {
        $this->init();
        $html = "";
        if (isset($this->request->get["pageid"]) && $this->request->get["pageid"]) {
            $pageid = $this->request->get["pageid"];
        } else {
            $pageid = $this->ompro->getFirstPageID();
        }
        $setting_page = $this->ompro->getPageTemplate($pageid);
        if (!empty($setting_page) && isset($this->request->get["template_id"]) && isset($this->request->get["order_id"])) {
            $all_orders_data = $this->ompro->getOrdersDataAll(array($this->request->get["order_id"]));
            $all_orders_data["param"]["template_id"] = $this->request->get["template_id"];
            $all_orders_data["param"]["to_edit"] = true;
            $all_orders_data["param"]["construct"] = true;
            $all_orders_data["total"] = 0;
            $html = $this->getOrdersTable($all_orders_data);
            $html = $this->constructHTML($html, $all_orders_data);
        }
        $this->response->setOutput($html);
    }
    public function batch()
    {
        $this->init();
        $json = $error = $success = $error_order_not_found = array();
        $error_invoice_no = $success_invoice_no = array();
        $error_reward_mail = $error_reward_sms = $error_reward_tlgrm = $error_reward_add = $success_reward_add = $error_reward_remove = $success_reward_remove = array();
        $error_commission_mail = $error_commission_sms = $error_commission_tlgrm = $error_commission_add = $success_commission_add = $error_commission_remove = $success_commission_remove = array();
        if (isset($this->request->post["selected"])) {
            $targets = $this->ompro->getNotifyTargets();
            $mail_target = $targets["mail_target"];
            $sms_target = $targets["sms_target"];
            $tlgrm_target = $targets["tlgrm_target"];
            $orders = $this->ompro->getOrders($this->ompro->filterData(), $this->request->post["selected"]);
            if (!empty($orders)) {
                foreach (array("mail", "sms", "tlgrm") as $notify) {
                    foreach (array("reward", "transaction") as $type) {
                        if (isset($this->request->post["notify_" . $notify]) && $this->request->post["notify_" . $notify] && isset($this->request->post[$type]) && $this->request->post[$type] == 1 && ${$notify . "_target"}[$type]) {
                            ${$notify . "_" . $type . "_template_info"} = $this->ompro->getTemplateTemplate($notify, ${$notify . "_target"}[$type]);
                        } else {
                            ${$notify . "_" . $type . "_template_info"} = array();
                        }
                    }
                }
                if (isset($this->request->post["comment"])) {
                    $comment = urldecode($this->request->post["comment"]);
                } else {
                    $comment = "";
                }
                foreach ($orders as $order_info) {
                    if ($order_info) {
                        $order_id = $order_info["order_id"];
                        $language_id = isset($order_info["language_id"]) && $order_info["language_id"] ? $order_info["language_id"] : $this->config->get("config_language_id");
                        if (isset($this->request->post["invoice"]) && $this->request->post["invoice"] == 1) {
                            if (!$order_info["invoice_no"]) {
                                $query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "order` WHERE invoice_prefix = '" . $this->db->escape($order_info["invoice_prefix"]) . "'");
                                if ($query->row["invoice_no"]) {
                                    $invoice_no = $query->row["invoice_no"] + 1;
                                } else {
                                    $invoice_no = 1;
                                }
                                $this->db->query("UPDATE `" . DB_PREFIX . "order` SET invoice_no = '" . (int) $invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info["invoice_prefix"]) . "' WHERE order_id = '" . (int) $order_id . "'");
                                $invoice_no = $order_info["invoice_prefix"] . $invoice_no;
                                if ($invoice_no) {
                                    $success_invoice_no[] = $order_id;
                                }
                            } else {
                                $error_invoice_no[] = $order_id;
                            }
                        }
                        if (isset($this->request->post["reward"]) && $this->request->post["reward"] == 1) {
                            if ($order_info["customer_id"] && 0 < $order_info["reward"]) {
                                $reward_total = $this->ompro->getTotalCustomerRewardsByOrderId($order_id);
                                if (!$reward_total) {
                                    $customer_id = $order_info["customer_id"];
                                    $customer_info = $this->ompro->getCustomer($customer_id);
                                    if ($customer_info) {
                                        $description = $this->language->get("text_order_id") . " #" . $order_id;
                                        if ($mail_target["reward"] || $sms_target["reward"] || $tlgrm_target["reward"]) {
                                            $reward_id = $this->ompro->addPoints($customer_id, $description, $order_info["reward"], $order_id);
                                            if ($reward_id) {
                                                $all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
                                                if ($mail_reward_template_info) {
                                                    $recipients = array();
                                                    $recipients[] = array("recipient_name" => "", "email" => $order_info["email"]);
                                                    $result_mail = $this->ompro->sendMail($order_id, $mail_reward_template_info, $language_id, $recipients, $all_orders_data, $comment, $attachments = array());
                                                    if (!$result_mail) {
                                                        $error_reward_mail[] = $order_id;
                                                    }
                                                }
                                                if ($sms_reward_template_info) {
                                                    $to = $order_info["telephone"];
                                                    $copies = array();
                                                    $result_sms = $this->ompro->sendSms($order_id, $sms_reward_template_info, $language_id, $to, $copies, $all_orders_data, $comment);
                                                    if (!$result_sms) {
                                                        $error_reward_sms[] = $order_id;
                                                    }
                                                }
                                                if ($tlgrm_reward_template_info) {
                                                    $chat_ids = array();
                                                    if (isset($order_info["telegram_id"]) && $order_info["telegram_id"]) {
                                                        $chat_ids[] = $order_info["telegram_id"];
                                                        $tlgrm_result = $this->ompro->sendToTelegram($order_id, $tlgrm_reward_template_info, $chat_ids, $all_orders_data, $comment);
                                                        $tlgrm_results = $this->ompro->prepareTelegramResults($order_id, $tlgrm_result);
                                                        if ($tlgrm_results["warning"]) {
                                                            $error_reward_tlgrm[] = $tlgrm_results["warning"];
                                                        }
                                                    }
                                                }
                                            } else {
                                                $error_reward_add[] = $order_id;
                                            }
                                        }
                                        if (!$mail_target["reward"]) {
                                            $this->omproapi->addReward($customer_info, $customer_id, $description, $order_info["reward"], $order_id);
                                        }
                                        $success_reward_add[] = $order_id;
                                    }
                                } else {
                                    $error_reward_add[] = $order_id;
                                }
                            } else {
                                $error_reward_add[] = $order_id;
                            }
                        }
                        if (isset($this->request->post["reward"]) && $this->request->post["reward"] == 2) {
                            if ($order_info["customer_id"] && 0 < $order_info["reward"]) {
                                $reward_total = $this->ompro->getTotalCustomerRewardsByOrderId($order_id);
                                if ($reward_total) {
                                    $this->ompro->deleteReward($order_id);
                                    $success_reward_remove[] = $order_id;
                                } else {
                                    $error_reward_remove[] = $order_id;
                                }
                            } else {
                                $error_reward_remove[] = $order_id;
                            }
                        }
                        if (isset($this->request->post["transaction"]) && $this->request->post["transaction"] == 1) {
                            if ($order_info["affiliate_id"] && 0 < $order_info["commission"]) {
                                if (!$this->ompro->getTotalTransactionsByOrderId($order_id)) {
                                    $affiliate_id = $order_info["affiliate_id"];
                                    $affiliate_info = $this->ompro->getAffiliate($affiliate_id);
                                    if ($affiliate_info) {
                                        $description = $this->language->get("text_order_id") . " #" . $order_id;
                                        if ($mail_target["transaction"] || $sms_target["transaction"] || $tlgrm_target["transaction"]) {
                                            $transaction_id = $this->ompro->addComission($affiliate_id, $description, $order_info["commission"], $order_id);
                                            if ($transaction_id) {
                                                $all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
                                                if ($mail_transaction_template_info) {
                                                    $recipients = array();
                                                    $recipients[] = array("recipient_name" => "", "email" => $affiliate_info["email"]);
                                                    $result_mail = $this->ompro->sendMail($order_id, $mail_transaction_template_info, $language_id, $recipients, $all_orders_data, $comment, $attachments = array());
                                                    if (!$result_mail) {
                                                        $error_commission_mail[] = $order_id;
                                                    }
                                                }
                                                if ($sms_transaction_template_info) {
                                                    $template_id = $sms_target["transaction"];
                                                    $template_info = $this->ompro->getTemplateTemplate("sms", $template_id);
                                                    $to = $affiliate_info["telephone"];
                                                    $copies = array();
                                                    $result_sms = $this->ompro->sendSms($order_id, $sms_transaction_template_info, $language_id, $to, $copies, $all_orders_data, $comment);
                                                    if (!$result_sms) {
                                                        $error_commission_sms[] = $order_id;
                                                    }
                                                }
                                                if ($tlgrm_transaction_template_info) {
                                                    $chat_ids = array();
                                                    if (isset($affiliate_info["telegram_id"]) && $affiliate_info["telegram_id"]) {
                                                        $chat_ids[] = $affiliate_info["telegram_id"];
                                                        $tlgrm_result = $this->ompro->sendToTelegram($order_id, $tlgrm_transaction_template_info, $chat_ids, $all_orders_data, $comment);
                                                        $tlgrm_results = $this->ompro->prepareTelegramResults($order_id, $tlgrm_result, true);
                                                        if ($tlgrm_results["warning"]) {
                                                            $error_commission_tlgrm[] = $tlgrm_results["warning"];
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        if (!$mail_target["transaction"]) {
                                            $this->ompro->addTransaction($affiliate_info, $affiliate_id, $description, $order_info["commission"], $order_id);
                                        }
                                        $success_commission_add[] = $order_id;
                                    }
                                } else {
                                    $error_commission_add[] = $order_id;
                                }
                            } else {
                                $error_commission_add[] = $order_id;
                            }
                        }
                        if (isset($this->request->post["transaction"]) && $this->request->post["transaction"] == 2) {
                            if ($order_info["affiliate_id"] && 0 < $order_info["commission"]) {
                                if ($this->ompro->getTotalTransactionsByOrderId($order_id)) {
                                    $this->ompro->deleteTransaction($order_id);
                                    $success_commission_remove[] = $order_id;
                                } else {
                                    $error_commission_remove[] = $order_id;
                                }
                            } else {
                                $error_commission_remove[] = $order_id;
                            }
                        }
                    } else {
                        $error_order_not_found[] = $order_id;
                    }
                }
            }
            if (!empty($error_invoice_no)) {
                $error["invoice_no"] = $this->language->get("text_error_invoice_no") . implode(", ", $error_invoice_no);
            }
            if (!empty($success_invoice_no)) {
                $success["invoice_no"] = $this->language->get("text_invoice_no_added") . implode(", ", $success_invoice_no);
            }
            if (!empty($error_reward_add)) {
                $error["reward_add"] = $this->language->get("text_error_reward_add") . implode(", ", $error_reward_add);
            }
            if (!empty($error_reward_mail)) {
                $error["reward_mail"] = $this->language->get("text_send_reward_mail_error") . implode(", ", $error_reward_mail);
            }
            if (!empty($error_reward_sms)) {
                $error["reward_sms"] = $this->language->get("text_send_reward_sms_error") . implode(", ", $error_reward_sms);
            }
            if (!empty($error_reward_tlgrm)) {
                $error["reward_tlgrm"] = $this->language->get("text_send_reward_tlgrm_error") . implode(", ", $error_reward_tlgrm);
            }
            if (!empty($success_reward_add)) {
                $success["reward_add"] = $this->language->get("text_reward_added") . implode(", ", $success_reward_add);
            }
            if (!empty($error_reward_remove)) {
                $error["reward_remove"] = $this->language->get("text_error_reward_remove") . implode(", ", $error_reward_remove);
            }
            if (!empty($success_reward_remove)) {
                $success["reward_remove"] = $this->language->get("text_reward_removed") . implode(", ", $success_reward_remove);
            }
            if (!empty($error_commission_add)) {
                $error["commission_add"] = $this->language->get("text_error_commission_add") . implode(", ", $error_commission_add);
            }
            if (!empty($error_commission_mail)) {
                $error["commission_mail"] = $this->language->get("text_send_commission_mail_error") . implode(", ", $error_commission_mail);
            }
            if (!empty($error_commission_sms)) {
                $error["commission_sms"] = $this->language->get("text_send_commission_sms_error") . implode(", ", $error_commission_sms);
            }
            if (!empty($error_commission_tlgrm)) {
                $error["commission_tlgrm"] = $this->language->get("text_send_commission_tlgrm_error") . implode(", ", $error_commission_tlgrm);
            }
            if (!empty($success_commission_add)) {
                $success["commission_add"] = $this->language->get("text_commission_added") . implode(", ", $success_commission_add);
            }
            if (!empty($error_commission_remove)) {
                $error["commission_remove"] = $this->language->get("text_error_commission_remove") . implode(", ", $error_commission_remove);
            }
            if (!empty($success_commission_remove)) {
                $success["commission_remove"] = $this->language->get("text_commission_removed") . implode(", ", $success_commission_remove);
            }
            if (!empty($error_order_not_found)) {
                $error["order_not_found"] = $this->language->get("text_error_order_not_found") . implode(", ", $error_order_not_found);
            }
            $batch_error = "";
            $i = 1;
            if ($error) {
                $batch_error = "<br>";
                foreach ($error as $error_text) {
                    $batch_error .= "<div>" . $i . ". " . $error_text . "</div>";
                    $i++;
                }
            }
            $json["warning"] = $batch_error;
            $batch_success = "";
            $ii = 1;
            if ($success) {
                foreach ($success as $success_text) {
                    $batch_success .= "<div>" . $ii . ". " . $success_text . "</div>";
                    $ii++;
                }
            }
            $json["success"] = $batch_success;
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function sendMail()
    {
        $this->init();
        $json = $success_orders = $error_orders = $error_results = array();
        $comment = "";
        if (isset($this->request->post["selected"]) && isset($this->request->get["template_id"]) && isset($this->request->get["sendto"])) {
            $template_info = $this->ompro->getTemplateTemplate("mail", $this->request->get["template_id"]);
            $sendto = $this->request->get["sendto"];
            if (isset($this->request->post["comment"])) {
                $comment = urldecode($this->request->post["comment"]);
            }
            $all_orders_data = $this->ompro->selectedOrdersData();
            if (!empty($all_orders_data) && !empty($all_orders_data["param"]) && !empty($all_orders_data["orders"])) {
                if ($sendto !== "customer" && $sendto !== "custom") {
                    $this->load->model("user/user");
                }
                foreach ($all_orders_data["orders"] as $order) {
                    $order_info = $order["order_info"];
                    $order_id = $order_info["order_id"];
                    $language_id = 0;
                    $recipient_name = $email = "";
                    $recipients = array();
                    if ($sendto == "customer") {
                        $language_id = isset($order_info["language_id"]) && $order_info["language_id"] ? $order_info["language_id"] : $this->config->get("config_language_id");
                        $email = $order_info["email"];
                        $recipients[] = array("recipient_name" => $recipient_name, "email" => $email);
                    } else {
                        if ($sendto == "user" && isset($this->request->get["user_id"])) {
                            $user_info = $this->model_user_user->getUser($this->request->get["user_id"]);
                            $email = $user_info["email"];
                            $recipient_name = $user_info["firstname"] . " " . $user_info["lastname"];
                            $recipients[] = array("recipient_name" => $recipient_name, "email" => $email);
                        } else {
                            if ($sendto == "manager" && $order_info["manager_user_id"]) {
                                $user_info = $this->model_user_user->getUser($order_info["manager_user_id"]);
                                $email = $user_info["email"];
                                $recipient_name = $user_info["firstname"] . " " . $user_info["lastname"];
                                $recipients[] = array("recipient_name" => $recipient_name, "email" => $email);
                            } else {
                                if ($sendto == "courier" && $order_info["courier_user_id"]) {
                                    $user_info = $this->model_user_user->getUser($order_info["courier_user_id"]);
                                    $email = $user_info["email"];
                                    $recipient_name = $user_info["firstname"] . " " . $user_info["lastname"];
                                    $recipients[] = array("recipient_name" => $recipient_name, "email" => $email);
                                } else {
                                    if ($sendto == "custom" && isset($this->request->get["recipients"])) {
                                        $emails = explode(",", urldecode($this->request->get["recipients"]));
                                        $emails = array_unique($emails);
                                        foreach ($emails as $email) {
                                            $recipients[] = array("recipient_name" => $recipient_name, "email" => $email);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($recipients)) {
                        $result_mail = $this->ompro->sendMail($order_id, $template_info, $language_id, $recipients, $all_orders_data, $comment);
                        if (!$result_mail) {
                            $error_results[] = $order_id;
                        } else {
                            $success_orders[] = $order_id;
                        }
                    } else {
                        $error_orders[] = $order_id;
                    }
                }
            }
            if (!empty($error_orders)) {
                $json["error"] = "Письмо не было отправлено отправлено: возможно не указан получатель, заказ №: " . implode(", ", $error_orders);
            }
            if (!empty($error_results)) {
                $json["error"] = "Письмо не было отправлено отправлено: возможно неправильно указан шаблон, заказ №: " . implode(", ", $error_results);
            }
            if (!empty($success_orders)) {
                $json["success"] = "Письмо успешно отправлено, заказ №: " . implode(", ", $success_orders);
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function sendSms()
    {
        $this->init();
        $json = $success_orders = $error_orders = array();
        $comment = "";
        if (isset($this->request->post["selected"]) && isset($this->request->get["template_id"]) && isset($this->request->get["sendto"])) {
            $template_info = $this->ompro->getTemplateTemplate("sms", $this->request->get["template_id"]);
            $sendto = $this->request->get["sendto"];
            if (isset($this->request->post["comment"])) {
                $comment = urldecode($this->request->post["comment"]);
            }
            $all_orders_data = $this->ompro->selectedOrdersData();
            if (!empty($template_info) && !empty($all_orders_data) && !empty($all_orders_data["orders"])) {
                if ($sendto !== "customer" && $sendto !== "custom") {
                    $this->load->model("user/user");
                }
                foreach ($all_orders_data["orders"] as $order) {
                    $order_info = $order["order_info"];
                    $to = "";
                    $copies = array();
                    $order_id = $order_info["order_id"];
                    $language_id = 0;
                    if ($sendto == "customer") {
                        $language_id = isset($order_info["language_id"]) && $order_info["language_id"] ? $order_info["language_id"] : $this->config->get("config_language_id");
                        $to = $order_info["telephone"];
                    } else {
                        if ($sendto == "user" && isset($this->request->get["user_id"])) {
                            $user_info = $this->model_user_user->getUser($this->request->get["user_id"]);
                            $to = $user_info["telephone"];
                        } else {
                            if ($sendto == "manager" && $order_info["manager_user_id"]) {
                                $user_info = $this->model_user_user->getUser($order_info["manager_user_id"]);
                                $to = $user_info["telephone"];
                            } else {
                                if ($sendto == "courier" && $order_info["courier_user_id"]) {
                                    $user_info = $this->model_user_user->getUser($order_info["courier_user_id"]);
                                    $to = $user_info["telephone"];
                                } else {
                                    if ($sendto == "custom" && isset($this->request->get["recipients"])) {
                                        $phones = explode(",", urldecode($this->request->get["recipients"]));
                                        $phones = array_unique($phones);
                                        foreach ($phones as $phone) {
                                            if ($to == "") {
                                                $to = $phone;
                                            } else {
                                                $copies[] = $phone;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($to)) {
                        $result_sms = $this->ompro->sendSms($order_id, $template_info, $language_id, $to, $copies, $all_orders_data, $comment);
                        if (!$result_sms) {
                            $error_orders[] = $order_id;
                        } else {
                            $success_orders[] = $order_id;
                        }
                    } else {
                        $error_orders[] = $order_id;
                    }
                }
            }
            if (!empty($error_orders)) {
                $json["error"] = "СМС не было отправлено отправлено, заказ №: " . implode(", ", $error_orders);
            }
            if (!empty($success_orders)) {
                $json["success"] = "СМС успешно отправлено, заказ №: " . implode(", ", $success_orders);
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function sendTelegram()
    {
        $this->init();
        $json = $success_orders = $error_orders = $error_results = array();
        $comment = "";
        if (isset($this->request->post["selected"]) && isset($this->request->get["template_id"]) && isset($this->request->get["sendto"])) {
            $template_info = $this->ompro->getTemplateTemplate("tlgrm", $this->request->get["template_id"]);
            $sendto = $this->request->get["sendto"];
            if (isset($this->request->post["comment"])) {
                $comment = urldecode($this->request->post["comment"]);
            }
            $all_orders_data = $this->ompro->selectedOrdersData();
            if (!empty($template_info) && !empty($all_orders_data) && !empty($all_orders_data["orders"])) {
                if ($sendto !== "custom") {
                    $this->load->model("user/user");
                }
                foreach ($all_orders_data["orders"] as $order) {
                    $order_info = $order["order_info"];
                    $chat_ids = array();
                    $order_id = $order_info["order_id"];
                    $language_id = 0;
                    if ($sendto == "user" && isset($this->request->get["user_id"])) {
                        $user_info = $this->model_user_user->getUser($this->request->get["user_id"]);
                        $chat_ids[] = $user_info["telegram_id"];
                    } else {
                        if ($sendto == "manager" && $order_info["manager_user_id"]) {
                            $user_info = $this->model_user_user->getUser($order_info["manager_user_id"]);
                            $chat_ids[] = $user_info["telegram_id"];
                        } else {
                            if ($sendto == "courier" && $order_info["courier_user_id"]) {
                                $user_info = $this->model_user_user->getUser($order_info["courier_user_id"]);
                                $chat_ids[] = $user_info["telegram_id"];
                            } else {
                                if ($sendto == "custom" && isset($this->request->get["recipients"])) {
                                    $ids = explode(",", urldecode($this->request->get["recipients"]));
                                    $ids = array_unique($ids);
                                    foreach ($ids as $id) {
                                        $chat_ids[] = $id;
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($chat_ids)) {
                        $tlgrm_result = $this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data, $comment);
                        $tlgrm_results = $this->ompro->prepareTelegramResults($order_id, $tlgrm_result);
                        if ($tlgrm_results["warning"]) {
                            $error_results[] = $tlgrm_results["warning"];
                        } else {
                            $success_orders[] = $order_id;
                        }
                    } else {
                        $error_orders[] = $order_id;
                    }
                }
            }
            if (!empty($error_results)) {
                $json["error"] = "Telegram уведомление не было отправлено: " . implode(", ", $error_results);
            } else {
                if (!empty($error_orders)) {
                    $json["error"] = "Уведомление не было отправлено, не указан Telegram ID, заказ №: " . implode(", ", $error_orders);
                }
            }
            if (!empty($success_orders)) {
                $json["success"] = "Telegram уведомление успешно отправлено, заказ №: " . implode(", ", $success_orders);
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function sendToTelegramTest()
    {
        $this->init();
        $json = array();
        if (isset($this->request->get["order_id"]) && isset($this->request->get["template_id"])) {
            $template_info = $this->ompro->getTemplateTemplate("tlgrm", $this->request->get["template_id"]);
            $order_id = $this->request->get["order_id"];
            $user_info = $this->db->query("SELECT * FROM `" . DB_PREFIX . "user` WHERE user_id = '" . (int) $this->session->data["user_id"] . "' ")->row;
            $chat_ids = array();
            if (isset($user_info["telegram_id"]) && $user_info["telegram_id"]) {
                $chat_ids[] = $user_info["telegram_id"];
            }
            $comment = "";
            if (isset($this->request->get["comment"])) {
                $comment = trim(html_entity_decode(nl2br($this->request->get["comment"]), ENT_QUOTES, "UTF-8"));
            }
            $all_orders_data = $this->ompro->getOrdersDataAll(array($order_id));
            if (!empty($template_info) && !empty($all_orders_data) && !empty($chat_ids)) {
                $results = $this->ompro->sendToTelegram($order_id, $template_info, $chat_ids, $all_orders_data, $comment);
                $tlgrm_results = $this->ompro->prepareTelegramResults($order_id, $results);
                $json["success"] = $tlgrm_results["success"];
                $json["warning"] = $tlgrm_results["warning"];
            } else {
                $json["error"] = $this->language->get("text_send_tlgrm_chat_ids_error") . ": " . $order_id;
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    public function xEditDbData()
    {
        $this->init();
        $post = $this->request->post;
        $post["value"] = isset($post["value"]) ? $post["value"] : "";
        if (isset($post["xEditCusrom"])) {
            $method = $post["xEditCusrom"];
            if (in_array($method, $this->custom_methods)) {
                $this->omproapicustom->{$method}($post);
            } else {
                $this->omproapi->{$method}($post);
            }
        } else {
            if (isset($post["dbTable"]) && isset($post["name"]) && isset($post["pkName"]) && isset($post["pk"])) {
                if ($post["name"] == "shipping_datetime_start" || $post["name"] == "shipping_datetime_end") {
                    $result = $this->omproapi->editSchedulerShipping($post["pk"], $post["name"], $post["value"]);
                    if (!$result) {
                        header("HTTP/1.1 400 Error: data is not saved!");
                    } else {
                        $this->omproapi->addHistoryLog($post);
                        if (isset($post["action"]) && $post["action"]) {
                            $method = $post["action"];
                            if (in_array($method, $this->custom_methods)) {
                                $this->omproapicustom->{$method}($post);
                            } else {
                                $this->omproapi->{$method}($post);
                            }
                        }
                        header("HTTP/1.1 200 ok");
                    }
                    exit;
                }
                $table = $post["dbTable"];
                if ($table == "order_product") {
                    $column = str_ireplace("op_", "", $post["name"]);
                } else {
                    $column = $post["name"];
                }
                $value = "";
                if (is_array($post["value"])) {
                    $tmp = array();
                    foreach ($post["value"] as $v) {
                        if ($v !== "") {
                            $tmp[] = $v;
                        }
                    }
                    $value = implode(",", $tmp);
                } else {
                    $value = $post["value"];
                    if (isset($post["editType"]) && $post["editType"] == "DateTime") {
                        $value = date("Y-m-d H:i:s", strtotime($value));
                    }
                }
                $columns = $this->ompro->getColumnsFromTable($table);
                if (!in_array(strtolower($column), $columns)) {
                    header("HTTP/1.1 400 Error: column " . $column . " not found! Set another method of editing.");
                } else {
                    $modified = "";
                    if (in_array("date_modified", $columns)) {
                        $modified = ", date_modified = NOW()";
                    }
                    if ($table == "order_simple_fields") {
                        $query = $this->db->query("SELECT `" . $post["pkName"] . "` FROM `" . DB_PREFIX . $table . "` WHERE `" . $post["pkName"] . "` = '" . (int) $this->db->escape($post["pk"]) . "' LIMIT 1 ");
                        if (!$query->row) {
                            $this->db->query("INSERT INTO `" . DB_PREFIX . $table . "` SET `" . $post["pkName"] . "` = '" . (int) $this->db->escape($post["pk"]) . "', `metadata` = '" . $column . "', `" . $column . "` = '" . $this->db->escape($value) . "' ");
                        } else {
                            $query = $this->db->query("SELECT `metadata` FROM `" . DB_PREFIX . $table . "` WHERE `" . $post["pkName"] . "` = '" . (int) $this->db->escape($post["pk"]) . "' LIMIT 1 ");
                            if (isset($query->row["metadata"])) {
                                $explode = explode(",", $query->row["metadata"]);
                                if (!in_array($column, $explode)) {
                                    $metadata = $query->row["metadata"] . "," . $column;
                                } else {
                                    $metadata = $query->row["metadata"];
                                }
                            } else {
                                $metadata = $column;
                            }
                            $this->db->query("UPDATE `" . DB_PREFIX . $table . "` SET `metadata` = '" . $metadata . "', `" . $column . "` = '" . $this->db->escape($value) . "' WHERE `" . $post["pkName"] . "` = '" . (int) $this->db->escape($post["pk"]) . "'");
                        }
                    } else {
                        $reset_coords = "";
                        if ($table == "order" && $column !== "shipping_latitude" && $column !== "shipping_longitude") {
                            $address_fields = $this->omproapi->addressFieldList();
                            if (in_array($column, $address_fields)) {
                                $reset_coords = ", shipping_latitude='0.000000', shipping_longitude='0.000000'";
                            }
                        }
                        $this->db->query("UPDATE `" . DB_PREFIX . $table . "` SET `" . $column . "` = '" . $this->db->escape($value) . "' " . $reset_coords . $modified . " WHERE `" . $post["pkName"] . "` = '" . (int) $this->db->escape($post["pk"]) . "'");
                    }
                    if ($this->db->countAffected() == 0) {
                        header("HTTP/1.1 400 Error: data is not saved!");
                    } else {
                        $this->omproapi->addHistoryLog($post);
                        if (isset($post["action"]) && $post["action"]) {
                            $method = $post["action"];
                            if (in_array($method, $this->custom_methods)) {
                                $this->omproapicustom->{$method}($post);
                            } else {
                                $this->omproapi->{$method}($post);
                            }
                        }
                        header("HTTP/1.1 200 ok");
                    }
                }
            } else {
                header("HTTP/1.1 400 Error: not all data in POST! Check selected API method of editing.");
            }
        }
    }
    public function xEditFile()
    {
        $this->init();
        $json = array();
        if (isset($this->request->post)) {
            $post = $this->request->post;
            $table = $post["dbTable"];
            $column = $post["name"];
            $value = $post["value"];
            $columns = $this->ompro->getColumnsFromTable($table);
            if (!in_array(strtolower($column), $columns)) {
                $json["error"] = "Error: column " . $column . " not found! Set another method of editing!";
            } else {
                $modified = "";
                if (in_array("date_modified", $columns)) {
                    $modified = ", date_modified = NOW()";
                }
                $this->db->query("UPDATE `" . DB_PREFIX . $table . "` SET `" . $column . "` = '" . $this->db->escape($value) . "' " . $modified . " WHERE `" . $post["pkname"] . "` = '" . (int) $this->db->escape($post["pk"]) . "' ");
                if ($this->db->countAffected() == 0) {
                    header("HTTP/1.1 400 Error: data is not saved!");
                } else {
                    header("HTTP/1.1 200 ok");
                    $this->omproapi->addHistoryLog($post);
                    $json["success"] = $this->language->get("text_filesave_success");
                }
            }
        }
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }
    protected function validate()
    {
        if (!$this->user->hasPermission("modify", "sale/ompro")) {
            $this->error["warning"] = $this->language->get("error_permission");
        }
        return !$this->error;
    }
    public function strToken()
    {
        if (300 <= $this->ompro->ocversion) {
            return "user_token=" . $this->session->data["user_token"];
        }
        return "token=" . $this->session->data["token"];
    }
}

?>