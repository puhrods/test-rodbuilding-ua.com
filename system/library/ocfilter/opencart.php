<?php
  
namespace OCFilter;

final class OpenCart {  
  protected $registry;

  public $version = 0;
  
  private $admin_logged = null;
  private $stores = null;
  private $languages = null;
  
  public function __construct($registry) {   
    $this->registry = $registry;       
    
    if (!defined('VERSION')) {
      if (is_file(DIR_SYSTEM . 'engine/router.php')) {
        $version = '3.0';
      } else if (is_file(DIR_SYSTEM . 'framework.php')) {
        $version = '2.3';
      } else {
        $version = '2.1';
      }
    } else {
      $version = VERSION;
    }
    
    $this->version = (int)str_pad(substr(str_replace('.', '', $version), 0, 2), 2, '0');
    
    $this->db->query("SET SQL_BIG_SELECTS = 1");
  }
  
  public function __get($key) {   
    return $this->registry->get($key);
  }
      
  public function isAdmin() {
    return is_file(DIR_APPLICATION . 'controller/user/user.php') && defined('DIR_CATALOG');
  }  

  public function isAdminLogged() {    
    if (is_null($this->admin_logged)) {
      // Admin status for debug/quick settings
      $user = new \Cart\User($this->registry);

      $this->admin_logged = (bool)$user->isLogged();            
    }    
    
    return $this->admin_logged;
  }      
      
  public function getStores() {
    if (!is_null($this->stores)) {
      return $this->stores;
    }
    
    $query = $this->db->query("SELECT store_id FROM " . DB_PREFIX . "store");
    
    $this->stores = [ 0 ];
    
    foreach ($query->rows as $result) {
      $this->stores[] = $result['store_id'];
    }
    
    return $this->stores;
  }
  
  public function getLanguages() {
    if (!is_null($this->languages)) {
      return $this->languages;
    }
    
    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language");
    
    foreach ($query->rows as $result) {
      $this->languages[] = $result;
    }
    
    return $this->languages;
  }      
      
  public function renderTemplate($template, $data) {
    if ($this->version < 22) {
      if ($this->isAdmin()) {
        $template .= '.tpl';
      } else {
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/' . $template . '.tpl')) {
          $template = $this->config->get('config_template') . '/template/' . $template . '.tpl';
        } else {
          $template = 'default/template/' . $template . '.tpl';
        }                
      }
    }

    return $this->load->view($template, $data);
  }   
  
  public function getThemeFile($path) {
    $file = 'catalog/view/theme/default/' . $path;
  
    if ($this->version < 22) {
      if (is_file(DIR_TEMPLATE . $this->config->get('config_template') . '/' . $path)) {
        $file = 'catalog/view/theme/' . $this->config->get('config_template') . '/' . $path;
      }     
    } else {     
      if (is_file(DIR_TEMPLATE . $this->config->get($this->config->get('config_theme') . '_directory') . '/' . $path)) {
        $file = 'catalog/view/theme/' . $this->config->get($this->config->get('config_theme') . '_directory') . '/' . $path;
      }
    }    
    
    return $file;
  }  
  
  public function responseTemplate($template, $data, $minify = false) {
    $html = $this->renderTemplate($template, $data);
  
    if ($minify) {
      // https://stackoverflow.com/a/6225706     
      $search = [
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
      ];

      $replace = [
        '>',
        '<',
        '\\1',
        ''
      ];

      $html = preg_replace($search, $replace, $html);      
    }
  
    $this->response->setOutput($html);
  }
  
  public function responseJSON($data) {
    $this->response->addHeader('Content-Type: application/json; charset=utf-8');
    $this->response->setOutput(json_encode($data));
  }  
  
  public function responseTEXT($data) {
    $this->response->addHeader('Content-Type: text/plain; charset=utf-8');
    $this->response->setOutput($data);
  }    
  
  public function responseXML($data) {
    $this->response->addHeader('Content-Type: application/xml; charset=utf-8');
    $this->response->setOutput($data);
  } 
}