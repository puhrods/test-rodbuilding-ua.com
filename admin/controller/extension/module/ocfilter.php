<?php

class ControllerExtensionModuleOCFilter extends Controller { 
  private $setting;
  
  public function __construct($registry) {
    parent::__construct($registry);
    
    if (is_file(DIR_APPLICATION . 'controller/extension/module/ocfilter/setting.php')) {
      require_once(DIR_APPLICATION . 'controller/extension/module/ocfilter/setting.php');
      
      $this->setting = new Setting($registry);
    }
  }
   
  public function index() {      
    return $this->setting->Ccc8f24d5280bb8e();
  }
  
  public function copy($data = []) {      
    return $this->setting->Ae55cc7385415b25($data);
  } 
  
  public function getCopyDataTotals() {
    $this->load->language('extension/module/ocfilter');
    
    $this->load->model('extension/module/ocfilter/filter');
    
    $json = [];  
    
    $json['text_copy_attribute_total'] = sprintf($this->language->get('text_copy_attribute_total'), 
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartAttributes(), 0, '', ' '),
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartAttributeValues(), 0, '', ' ')
    );
    
    $json['text_copy_filter_total'] = sprintf($this->language->get('text_copy_filter_total'), 
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartFilters(), 0, '', ' '),
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartFilterValues(), 0, '', ' ')
    );

    $json['text_copy_option_total'] = sprintf($this->language->get('text_copy_option_total'), 
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartOptions(), 0, '', ' '),
      number_format($this->model_extension_module_ocfilter_filter->getTotalOpencartOptionValues(), 0, '', ' ')
    );      
    
    $this->ocfilter->opencart->responseJSON($json);
  }  
   
  public function getCopyLog() {
    if (is_file(DIR_LOGS . 'ocfilter.log')) {
      if (filesize(DIR_LOGS . 'ocfilter.log') > 1024 * 1024 * 1) {
        file_put_contents(DIR_LOGS . 'ocfilter.log', '');  
      }
      
      $log = file_get_contents(DIR_LOGS . 'ocfilter.log');
      $log = str_replace([ "\n", "\r", "\t" ], [ "<br>", "", "" ], $log);
      $log = trim($log);
      $log = trim($log, '<br>');
      
      $this->ocfilter->opencart->responseTEXT($log);
    }   
  }
  
  public function autocompleteAttribute() {
    $json = [];

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('catalog/attribute');

      $filter_data = [
        'filter_name' => $this->request->get['filter_name'],
        'start' => 0,
        'limit' => 10
      ];

      $results = $this->model_catalog_attribute->getAttributes($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'attribute_id' => $result['attribute_id'],
          'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          'attribute_group' => $result['attribute_group']
        ];
      }
    }

    $sort_order = [];

    foreach ($json as $key => $value) {
      $sort_order[$key] = $value['name'];
    }

    array_multisort($sort_order, SORT_ASC, $json);

    $this->ocfilter->opencart->responseJSON($json);
  }
  
  public function getCopyAttribute() {
    $json = [];

    $this->load->model('extension/module/ocfilter/filter');

    $results = $this->model_extension_module_ocfilter_filter->getCopyAttributes(!empty($this->request->get['exclude']));

    foreach ($results as $result) {
      $json[] = [
        'attribute_id' => $result['attribute_id'],
        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
      ];
    }

    $this->ocfilter->opencart->responseJSON($json);
  }

  public function autocompleteAttributeGroup() {
    $json = [];

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('extension/module/ocfilter/filter');

      $filter_data = [
        'filter_name' => $this->request->get['filter_name'],
        'start' => 0,
        'limit' => 10
      ];

      $results = $this->model_extension_module_ocfilter_filter->getAttributeGroups($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'attribute_group_id' => $result['attribute_group_id'],
          'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        ];
      }
    }

    $this->ocfilter->opencart->responseJSON($json);
  }  
  
  public function autocompleteCategory() {
    $json = [];

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('catalog/category');

      $filter_data = [
        'filter_name' => $this->request->get['filter_name'],
        'sort'        => 'name',
        'order'       => 'ASC',
        'start'       => 0,
        'limit'       => 15
      ];

      $results = $this->model_catalog_category->getCategories($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'category_id' => $result['category_id'],
          'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
        ];
      }
    }

    $this->ocfilter->opencart->responseJSON($json);
  }    
  
  public function install() {
    $this->clearOldFiles();
       
    $this->installPermission();
    
    $this->installEvent();    
    
    if ($this->registry->get('ocfilter') && property_exists($this->ocfilter, 'admin')) {
      $this->installDB();
    }
  }
  
  protected function clearOldFiles() {
    if (!is_file(DIR_APPLICATION . 'controller/module/ocfilter.php') && !is_file(DIR_CATALOG . 'model/catalog/ocfilter.php')) {
      return;
    }
    
    $this->deleteFile(DIR_APPLICATION . 'controller/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'controller/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'controller/module/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en-gb/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en-gb/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en-us/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/en-us/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/english/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/english/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/ru/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/ru/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/ru-ru/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/ru-ru/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'language/russian/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'language/russian/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'model/catalog/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'model/catalog/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'model/extension/ocfilter.php');
    $this->deleteFile(DIR_APPLICATION . 'model/extension/ocfilter_page.php');
    $this->deleteFile(DIR_APPLICATION . 'view/image/ocfilter/delete-value.png');
    $this->deleteFile(DIR_APPLICATION . 'view/image/ocfilter/select-text.png');
    $this->deleteFile(DIR_APPLICATION . 'view/image/ocfilter/sort-handler.png');
    $this->deleteFile(DIR_APPLICATION . 'view/template/catalog/ocfilter_form.tpl');
    $this->deleteFile(DIR_APPLICATION . 'view/template/catalog/ocfilter_list.tpl');
    $this->deleteFile(DIR_APPLICATION . 'view/template/catalog/ocfilter_page_form.tpl');
    $this->deleteFile(DIR_APPLICATION . 'view/template/catalog/ocfilter_page_list.tpl');    
    $this->deleteFile(DIR_APPLICATION . 'view/template/extension/module/ocfilter.twig');
    $this->deleteFile(DIR_APPLICATION . 'view/template/extension/module/ocfilter_form.twig');
    $this->deleteFile(DIR_APPLICATION . 'view/template/extension/module/ocfilter_list.twig');
    $this->deleteFile(DIR_APPLICATION . 'view/template/extension/module/ocfilter_page_form.twig');
    $this->deleteFile(DIR_APPLICATION . 'view/template/extension/module/ocfilter_page_list.twig');
    $this->deleteFile(DIR_APPLICATION . 'view/template/module/ocfilter.tpl');
    $this->deleteFile(DIR_CATALOG . 'controller/feed/ocfilter_sitemap.php');
    $this->deleteFile(DIR_CATALOG . 'controller/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/en/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/en-gb/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/en-us/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/english/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/ru/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/ru-ru/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'language/russian/module/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'model/catalog/ocfilter.php');
    $this->deleteFile(DIR_CATALOG . 'view/javascript/ocfilter/nouislider.min.css');
    $this->deleteFile(DIR_CATALOG . 'view/javascript/ocfilter/nouislider.min.js');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/image/ocfilter/diagram-bg-repeat.png');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/stylesheet/ocfilter/ocfilter.css');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/extension/module/ocfilter/filter_price.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/extension/module/ocfilter/filter_price.twig');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/filter_item.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/filter_list.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/filter_price.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/filter_slider_item.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/module.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/selected_filter.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/value_item.tpl');
    $this->deleteFile(DIR_CATALOG . 'view/theme/default/template/module/ocfilter/value_list.tpl');
    $this->deleteFile(DIR_SYSTEM . 'config/ocfilter.php');
    $this->deleteFile(DIR_SYSTEM . 'helper/ocfilter.php');
    $this->deleteFile(DIR_SYSTEM . 'library/ocfilter.php');    
  }
  
  public function installDB() {
    $this->load->model('extension/module/ocfilter/setting');
    
    $this->model_extension_module_ocfilter_setting->install();
  }
  
  public function installEvent() {
    if (version_compare(VERSION, '3.0', '>=')) {
      $this->load->model('setting/event');

      $this->model_setting_event->addEvent('ocfilter_api', 'catalog/view/*/before', 'extension/module/ocfilter/eventApi', 1);      
      $this->model_setting_event->addEvent('ocfilter_add_language', 'admin/model/localisation/language/addLanguage/after', 'extension/module/ocfilter/onAddLanguage', 1);  
    } else {
      $this->load->model('extension/event');
      
      $this->model_extension_event->addEvent('ocfilter_api', 'catalog/view/*/before', 'extension/module/ocfilter/eventApi', 1); 
      $this->model_extension_event->addEvent('ocfilter_add_language', 'admin/model/localisation/language/addLanguage/after', 'extension/module/ocfilter/onAddLanguage', 1);        
    }
  }

  public function installPermission() {
    $this->load->model('user/user_group');

    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/ocfilter/filter');
    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/ocfilter/filter');

    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/ocfilter/page');
    $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/ocfilter/page');
  }  

  public function uninstall() {
    if ($this->registry->get('ocfilter')) {
      $this->ocfilter->setting->add('status', 0);
    }
    
    if (version_compare(VERSION, '3.0', '>=')) {
      $this->load->model('setting/event');
      
      $this->model_setting_event->deleteEventByCode('ocfilter_api');
      $this->model_setting_event->deleteEventByCode('ocfilter_add_language');
    } else {
      $this->load->model('extension/event');
      
      $this->model_extension_event->deleteEvent('ocfilter_api');
      $this->model_extension_event->deleteEvent('ocfilter_add_language');
    }
  }  
  
  protected function checkInstall() {   
    $this->clearOldFiles();
    
    $this->load->language('extension/module/ocfilter');
    
    $this->load->model('extension/module/ocfilter/setting');       
             
    // Modification
    $update = false;
    
    if (is_file(DIR_SYSTEM . 'mega_filter.ocmod.xml')) {
      rename(DIR_SYSTEM . 'mega_filter.ocmod.xml', DIR_SYSTEM . 'mega_filter.ocmod.xml_');
      
      $update = true;
    }
    
    if (is_file(DIR_SYSTEM . 'oct_feelmart_ocfilter.ocmod.xml')) {
      rename(DIR_SYSTEM . 'oct_feelmart_ocfilter.ocmod.xml', DIR_SYSTEM . 'oct_feelmart_ocfilter.ocmod.xml_');
      
      $update = true;
    }

    if (is_file(DIR_SYSTEM . 'revolution_filter.ocmod.xml')) {
      rename(DIR_SYSTEM . 'revolution_filter.ocmod.xml', DIR_SYSTEM . 'revolution_filter.ocmod.xml_');
      
      $update = true;
    }
    
    if (is_file(DIR_SYSTEM . 'neoseo_filter.ocmod.xml')) {
      rename(DIR_SYSTEM . 'neoseo_filter.ocmod.xml', DIR_SYSTEM . 'neoseo_filter.ocmod.xml_');
      
      $update = true;
    }            
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE status = '1' AND `code` LIKE 'ocfilter%'");
    
    if ($query->num_rows) {
      $this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE `code` LIKE 'ocfilter%'");
      
      $update = true;
    }
    
    if (!$this->registry->get('ocfilter') || !property_exists($this->ocfilter, 'admin')) {
      if (!is_file(DIR_SYSTEM . 'ocfilter.ocmod.xml')) {
        exit($this->language->get('error_install_modification_not_found'));
      }       
      
      $update = true;
    }
    
    if ($update && !isset($this->request->get['attempt'])) {
      if ($this->refreshModification()) {
        if (version_compare(VERSION, '3.0', '>=')) {
          $this->response->redirect($this->url->link('extension/module/ocfilter', 'user_token=' . $this->session->data['user_token'] . '&attempt=1', true));
        } else {
          $this->response->redirect($this->url->link('extension/module/ocfilter', 'token=' . $this->session->data['token'] . '&attempt=1', 'SSL'));
        }
      } 
    }
    
    if (!$this->registry->get('ocfilter') || !property_exists($this->ocfilter, 'admin')) {
      exit($this->language->get('error_install_modification_update'));
    } else if (!$update && isset($this->request->get['attempt'])) {
      $this->response->redirect($this->url->link('extension/module/ocfilter', $this->ocfilter->admin->getToken(!0), 'SSL'));
    }
    
    // DB
    if (!$this->model_extension_module_ocfilter_setting->isTableExists('ocfilter_setting')) {
      $this->installDB();
      
      if (!$this->model_extension_module_ocfilter_setting->isTableExists('ocfilter_setting')) {
        exit($this->language->get('error_install_tables'));
      }
    }    

    // Events 
    if (!$this->model_extension_module_ocfilter_setting->getEventByCode('ocfilter_api')) {
      $this->installEvent();
    }

    // Permission
    if ($this->user->hasPermission('modify', 'extension/module/ocfilter') && !$this->user->hasPermission('modify', 'extension/module/ocfilter/filter')) {
      $this->installPermission();
    }
  }  
  
  protected function refreshModification() {
    if ($curl = curl_init()) {
      if (version_compare(VERSION, '3.0', '>=')) {
        $link = str_replace('&amp;', '&', $this->url->link('marketplace/modification/refresh', 'user_token=' . $this->session->data['user_token'], true));
      } else {
        $link = str_replace('&amp;', '&', $this->url->link('extension/modification/refresh', 'token=' . $this->session->data['token'], 'SSL'));
      }
    
      curl_setopt($curl, CURLOPT_TIMEOUT, 10);
      curl_setopt($curl, CURLOPT_URL, $link);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_COOKIE, implode('; ', array_map(function($k, $v) { return $k . '=' . $v; }, array_keys($this->request->cookie), $this->request->cookie)));
      
      session_write_close();
      
      $result = curl_exec($curl);

      curl_close($curl);
    
      if (isset($this->session->data['success'])) {
        unset($this->session->data['success']);
      }

      return true;
    }
    
    return false;
  }
  
  public function onAddLanguage(&$route, &$args, $language_id) {
    $this->load->model('extension/module/ocfilter/setting');
    
    $this->model_extension_module_ocfilter_setting->addLanguage($language_id);   
  }  
  
  protected function deleteFile($path) {
    if (is_file($path)) {
      @unlink($path);
    }
  }
}