<?php

namespace OCFilter;

define('OCF_VERSION', '4.8.0.19.1');
define('OCF_RELEASE_DATE', '2021-11-01');

class Core extends Factory {    
  private $has_started = false;  
  
  public $debug_queries = [];
  public $debug_queries_time = 0;  
  
  public function __construct() {
    parent::__construct($this);
  }

  public function init($registry) {
    $this->opencart = new OpenCart($registry);         
        
    $this->setting = new Setting();
    $this->cache = new Cache();
    $this->helper = new Helper();    
    $this->params = new Params();
  }

  protected function catalog() {
    $this->opencart->load->model('extension/module/ocfilter');
    $this->opencart->load->model('catalog/product');
    $this->opencart->load->model('catalog/category');
    $this->opencart->load->model('tool/image'); 

    $this->seo = new Seo();
    $this->placement = new Placement();    
    $this->filter = new Filter();
    $this->api = new Api();
  }
  
  protected function admin() {
    $this->admin = new Admin();
  }

  // Public methods 
  public function startup() {
    if ($this->has_started) {
      return true;
    }

    $this->seo->startup();
    $this->api->startup();
    
    if (!$this->isEnabled()) {
      return false;
    }    
    
    $this->has_started = true;
    
    return true;
  }
   
  public function config($key, $default = null) {      
    $value = $this->setting->get($key);
    
    if (is_null($value) && !is_null($default)) {
      return $default;
    }
    
    return $value;
  }  
  
  public function query($sql) {  
    $debug = ($this->config('debug') && $this->opencart->isAdminLogged());
    
    if ($debug) {
      $time_start = microtime(true);
    }
    
    $query = $this->opencart->db->query($sql);   

    if ($debug) {
      $time = microtime(true) - $time_start;

      $this->debug_queries_time += $time;

      $this->debug_queries[] = [
        'caller' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 6),
        'sql' => $sql,
        'time' => $time
      ];
    }
  
    return $query;
  }    
  
  public function isEnabled() {
    $enabled = $this->config('status') && !$this->seo->isDisabledRoute();

    if ($enabled && $this->opencart->isAdmin()) {
      $enabled = false;
    }

    if ($enabled) {    
      $enabled = (
        $this->placement->isManufacturer() || 
        $this->placement->isSearch() || 
        $this->placement->isSpecial() || 
        $this->placement->isCategory() || 
        $this->placement->isCustomPage() ||
        $this->placement->isProduct()
      );
    }
    
    if ($enabled && $this->placement->isCategory() && $this->config('category_visibility') == 'last_level') {
      $enabled = $this->placement->isLastChildCategory();
    }

    return $enabled;
  }  
  
  // Frame compatibility
  public function getParams() {
    return $this->seo->getParams();
  }
  
  public function getPageDescription() {
    return $this->seo->getPageDescription('top') . '<!--more-->' . $this->seo->getPageDescription('bottom');
  }
}