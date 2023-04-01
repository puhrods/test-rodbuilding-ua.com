<?php
class ControllerExtensionModuleOCFilterPage extends Controller {  
  protected $error = [];

  public function __construct($registry) {
    parent::__construct($registry);
    
    // Controller possible GET vars => default value  
    $this->ocfilter->admin->setControllerParams([
      'filter_name' => '',
      'filter_category_id' => null,
      'filter_status' => null,
      
      'page' => 1,    
    ]);
  }

  public function index() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    $this->getList($data);
  }

  public function add() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {      
      $page_id = $this->model_extension_module_ocfilter_page->addPage($this->preparePageData($this->request->post));

      $this->session->data['success'] = $this->language->get('text_success');

      if (isset($this->request->get['apply'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page/edit', $this->ocfilter->admin->getURL() . '&page_id=' . $page_id, 'SSL'));
      } else if (isset($this->request->get['apply_add'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page/add', $this->ocfilter->admin->getURL(), 'SSL'));        
      } else {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
      }  
    }

    $this->getForm($data);
  }
  
  public function addBatch() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');
    $this->load->model('extension/module/ocfilter/filter');
    $this->load->model('localisation/language');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAddBatch()) {           
      $ocfilter_filter = $this->request->post['ocfilter_filter'];
    
      // Set value descriptions
      $values_name = [];
      
      $set_value_name = function($filter_key, $value_id, $language_id, $name, $filter_description = null) use (&$values_name) { 
        $_name = strip_tags(html_entity_decode($name, ENT_QUOTES, 'UTF-8'));
        
        if ($filter_description && isset($filter_description[$language_id]['suffix'])) {
          $_name .= strip_tags(html_entity_decode($filter_description[$language_id]['suffix'], ENT_QUOTES, 'UTF-8'));
        }
      
        if (!isset($values_name[$filter_key])) {
          $values_name[$filter_key] = [];
        }  
        
        if (!isset($values_name[$filter_key][$value_id])) {
          $values_name[$filter_key][$value_id] = [];
        }          
      
        $values_name[$filter_key][$value_id][$language_id] = $_name; 
      };
      
      // Set special filters value names from lang files
      $languages = $this->model_localisation_language->getLanguages();    
      
      foreach ($languages as $language) {
        $_ = [];
        
        $file = DIR_LANGUAGE . $language['code'] . '/extension/module/ocfilter/filter.php';

        if (is_file($file)) {
          include($file);
          
          // Stock status
          if (isset($ocfilter_filter[$this->ocfilter->params->special('stock')->key()]) && !in_array('group', $ocfilter_filter[$this->ocfilter->params->special('stock')->key()])) {
            if (isset($_['text_in_stock']) && (in_array(0, $ocfilter_filter[$this->ocfilter->params->special('stock')->key()]) || in_array(2, $ocfilter_filter[$this->ocfilter->params->special('stock')->key()]))) {
              $set_value_name($this->ocfilter->params->special('stock')->key(), 2, $language['language_id'], $_['text_in_stock']);
            }
            
            if (isset($_['text_out_of_stock']) && (in_array(0, $ocfilter_filter[$this->ocfilter->params->special('stock')->key()]) || in_array(1, $ocfilter_filter[$this->ocfilter->params->special('stock')->key()]))) {
              $set_value_name($this->ocfilter->params->special('stock')->key(), 1, $language['language_id'], $_['text_out_of_stock']);
            }                
          }      
          
          // Discount
          if (isset($_['text_discount_only']) && isset($ocfilter_filter[$this->ocfilter->params->special('discount')->key()]) && !in_array('group', $ocfilter_filter[$this->ocfilter->params->special('discount')->key()])) {
            $set_value_name($this->ocfilter->params->special('discount')->key(), 1, $language['language_id'], $_['text_discount_only']);
          }
          
          // Newest
          if (isset($_['text_newest_only']) && isset($ocfilter_filter[$this->ocfilter->params->special('newest')->key()]) && !in_array('group', $ocfilter_filter[$this->ocfilter->params->special('newest')->key()])) {
            $set_value_name($this->ocfilter->params->special('newest')->key(), 1, $language['language_id'], $_['text_newest_only']);
          }     
        }
      }       

      // Another values descriptions
      foreach ($ocfilter_filter as $filter_key => $values) {
        if (in_array('group', $values)) {
          continue;
        }
        
        $filter_description = null;
        
        if (!$this->ocfilter->params->key($filter_key)->is('special')) {        
          $filter_description = $this->model_extension_module_ocfilter_filter->getFilterDescriptions($filter_key); 
          
          if (!$filter_description) {
            continue;
          }
        }
        
        foreach ($values as $value_id) {
          // Manufacturer
          if ($this->ocfilter->params->key($filter_key)->is('manufacturer')) {           
            if ($value_id > 0) {
              $value_description = $this->model_extension_module_ocfilter_filter->getManufacturerDescriptions($value_id); 
              
              foreach ($value_description as $language_id => $name) {                  
                $set_value_name($filter_key, $value_id, $language_id, $name);
              }                
            } else {
              $results = $this->model_extension_module_ocfilter_filter->getAllManufacturerDescriptions();
             
              foreach ($results as $value_id => $descriptions) {
                foreach ($descriptions as $language_id => $name) {
                  $set_value_name($filter_key, $value_id, $language_id, $name);
                }
              }
            }
          }
          
          // Stock status
          if ($this->ocfilter->params->key($filter_key)->is('stock') && $this->ocfilter->config('stock_status_method') == 'stock_status_id') {
            if ($value_id > 0) {
              $value_description = $this->model_extension_module_ocfilter_filter->getStockStatusDescriptions($value_id); 
              
              foreach ($value_description as $language_id => $name) {                  
                $set_value_name($filter_key, $value_id, $language_id, $name);
              }                                         
            } else {
              $results = $this->model_extension_module_ocfilter_filter->getAllStockStatusDescriptions();
             
              foreach ($results as $value_id => $descriptions) {
                foreach ($descriptions as $language_id => $name) {
                  $set_value_name($filter_key, $value_id, $language_id, $name);
                }
              }
            }  
          }   

          if (!$this->ocfilter->params->key($filter_key)->is('special')) {  
            if ($value_id > 0) {
              $value_description = $this->model_extension_module_ocfilter_filter->getFilterValueDescriptions($filter_key, $value_id); 
            
              foreach ($value_description as $language_id => $name) {                  
                $set_value_name($filter_key, $value_id, $language_id, $name, $filter_description);
              }                         
            } else {
              $results = $this->model_extension_module_ocfilter_filter->getAllFilterValueDescriptions($filter_key);
              
              foreach ($results as $value_id => $descriptions) {
                foreach ($descriptions as $language_id => $name) {
                  $set_value_name($filter_key, $value_id, $language_id, $name, $filter_description);
                }
              }
            } 
          }
        } // foreach $values  
      } // foreach POST ocfilter_filter  
                           
      // Set all posible combinations
      $filter_combinations = [ [] ];
      
      foreach ($values_name as $filter_key => $values) {         
        $tmp = [];
                
        $values_id = array_keys($values);
        
        foreach ($filter_combinations as $item) {
          foreach ($values_id as $value_id) {            
            $tmp[] = $item + [ $filter_key => $value_id ];
          }
        }
        
        $filter_combinations = $tmp;
      }  

      // Set groups
      foreach ($ocfilter_filter as $filter_key => $values) {
        if (!in_array('group', $values)) {
          continue; 
        }

        foreach ($filter_combinations as $key => $combination) {
          $filter_combinations[$key][$filter_key] = $values;
        }        
      }  
                       
      // Prepare page data    
      $set_page_description = function($mask, $name, $description) {
        return [
          'name'                => str_replace($mask, $name, $description['name']),
          'heading_title'       => str_replace($mask, $name, $description['heading_title']),
          'meta_title'          => str_replace($mask, $name, $description['meta_title']),
          'description_top'     => str_replace($mask, $name, $description['description_top']),
          'description_bottom'  => str_replace($mask, $name, $description['description_bottom']),
          'meta_description'    => str_replace($mask, $name, $description['meta_description']),
          'meta_keyword'        => str_replace($mask, $name, $description['meta_keyword']),
        ];        
      };
      
      $page_data = [
        'dynamic' => 0,
        'keyword' => null,
        'page_description' => [],
        'ocfilter_filter' => [],
        'category_id' => $this->request->post['add_category_id'],
        'status' => $this->request->post['add_status'], 
        'module' => $this->request->post['add_module'],
        'category' => $this->request->post['add_category'],
        'product' => $this->request->post['add_product'],
        'sitemap' => $this->request->post['add_sitemap'],
        'skip_seo_pro' => true
      ];      
      
      if (isset($this->request->post['add_page_store'])) {
        $page_data['page_store'] = $this->request->post['add_page_store'];
      } else {
        $page_data['page_store'] = [ 0 ];
      }
      
      if (isset($this->request->post['add_page_layout'])) {
        $page_data['page_layout'] = $this->request->post['add_page_layout'];
      }      

      foreach ($filter_combinations as $combination) {
        $page_data['ocfilter_filter'] = [];
        
        $page_data['page_description'] = $this->request->post['add_page_description'];
        
        $page_data['keyword'] = $this->request->post['add_keyword']; 
        
        foreach ($combination as $filter_key => $value_id) {
          // Group?
          if (is_array($value_id)) {
            $page_data['ocfilter_filter'][$filter_key] = $value_id;
            
            continue;
          }
          
          $page_data['ocfilter_filter'][$filter_key] = [ $value_id ];
                   
          foreach ($page_data['page_description'] as $language_id => $description) {
            if (isset($values_name[$filter_key][$value_id][$language_id])) {
              $value_name = $values_name[$filter_key][$value_id][$language_id];
            } else {
              $value_name = '';
            }
                              
            // Default               
            $description = $set_page_description('{F' . $filter_key . '}', $value_name, $description);
            
            // Lowercase
            $description = $set_page_description('{F' . $filter_key . '|L}', utf8_strtolower($value_name), $description);
     
            $page_data['page_description'][$language_id] = $description;
            
            // Keyword              
            if ($this->ocfilter->opencart->version >= 30 && is_array($page_data['keyword'])) {
              foreach ($page_data['keyword'] as $store_id => $keyword_languages) {
                foreach ($keyword_languages as $language_id => $keyword) {
                  if (utf8_strlen($keyword) > 0) {
                    $keyword = $page_data['keyword'][$store_id][$language_id];
                    
                    $keyword = str_replace('{F' . $filter_key . '}', $this->ocfilter->helper->translit($value_name), $keyword);
                    $keyword = str_replace('{F' . $filter_key . '|L}', $this->ocfilter->helper->translit($value_name), $keyword); 
                    
                    $page_data['keyword'][$store_id][$language_id] = $keyword;
                  }
                }
              }
            } else if ($this->ocfilter->opencart->version < 30 && $language_id == $this->config->get('config_language_id') && is_string($page_data['keyword']) && utf8_strlen($page_data['keyword']) > 0) {
              $keyword = $page_data['keyword'];
              
              $keyword = str_replace('{F' . $filter_key . '}', $this->ocfilter->helper->translit($value_name), $keyword);
              $keyword = str_replace('{F' . $filter_key . '|L}', $this->ocfilter->helper->translit($value_name), $keyword); 
              
              $page_data['keyword'] = $keyword;
            }
          } // end description foreach
        } // end combination foreach

        $page_id = $this->model_extension_module_ocfilter_page->addPage($page_data);        
      } // end combinations foreach

      $this->cache->delete('seo_pro');
      $this->cache->delete('seopro.keywords');
      $this->cache->delete('seopro.queries');

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    } else if ($this->request->server['REQUEST_METHOD'] != 'POST') {
      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    }

    $data['show_form'] = true;

    $this->getList($data);
  }  

  public function edit() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {     
      $this->model_extension_module_ocfilter_page->editPage($this->request->get['page_id'], $this->preparePageData($this->request->post));

      $this->session->data['success'] = $this->language->get('text_success');

      if (isset($this->request->get['apply'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page/edit', $this->ocfilter->admin->getURL() . '&page_id=' . $this->request->get['page_id'], 'SSL'));
      } else if (isset($this->request->get['apply_add'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page/add', $this->ocfilter->admin->getURL(), 'SSL'));        
      } else {
        $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
      }  
    }

    $this->getForm($data);
  }
    
  public function editBatch() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateEditBatch()) {       
      if ($this->model_extension_module_ocfilter_page->editPageBatch($this->request->post)) {
        $this->session->data['success'] = $this->language->get('text_success');
      }
      
      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    } else if ($this->request->server['REQUEST_METHOD'] != 'POST') {
      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    }

    $this->getList($data);
  }     

  public function delete() {
    $data = $this->load->language('extension/module/ocfilter/page');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    if (isset($this->request->post['selected']) && $this->validateDelete()) {
      foreach ($this->request->post['selected'] as $page_id) {
        $this->model_extension_module_ocfilter_page->deletePage($page_id);
      }

      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    } else if ($this->request->server['REQUEST_METHOD'] != 'POST') {
      $this->response->redirect($this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getURL(), 'SSL'));
    }

    $this->getList($data);
  }

  public function show() {
    $data = $this->load->language('extension/module/ocfilter/page');
    
    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/page');

    if (isset($this->request->get['page_id'])) {      
      $page_info = $this->model_extension_module_ocfilter_page->getPage($this->request->get['page_id']);
      
      if (!$page_info['dynamic']) {
        $this->response->redirect(HTTP_CATALOG . 'index.php?route=product/category&path=' . $page_info['category_id'] . '&ocfilter_page_id=' . $page_info['page_id']);
      } else {
        // TODO: Get any params from dynamic page        
      }            
    }

    $this->getList($data);
  }  
  

  private function setAddBatchData(&$data) {
    if (isset($this->error['name'])) {
      $data['error_name'] = $this->error['name'];
    } else {
      $data['error_name'] = [];
    }

    if (isset($this->error['heading_title'])) {
      $data['error_heading_title'] = $this->error['heading_title'];
    } else {
      $data['error_heading_title'] = [];
    }
    
    if (isset($this->error['meta_title'])) {
      $data['error_meta_title'] = $this->error['meta_title'];
    } else {
      $data['error_meta_title'] = [];
    }

    if (isset($this->error['meta_description'])) {
      $data['error_meta_description'] = $this->error['meta_description'];
    } else {
      $data['error_meta_description'] = [];
    }

    if (isset($this->error['meta_keyword'])) {
      $data['error_meta_keyword'] = $this->error['meta_keyword'];
    } else {
      $data['error_meta_keyword'] = [];
    }

    if (isset($this->error['keyword'])) {
      $data['error_add_keyword'] = $this->error['keyword'];
    } else {
      $data['error_add_keyword'] = '';
    }

    if (isset($this->error['category'])) {
      $data['error_add_category'] = $this->error['category'];
    } else {
      $data['error_add_category'] = '';
    }
    
    if (isset($this->error['filter'])) {
      $data['error_add_filter'] = $this->error['filter'];
    } else {
      $data['error_add_filter'] = '';
    }        
    
    // Data   
    $this->load->model('localisation/language');

    $data['languages'] = $this->model_localisation_language->getLanguages();   

    foreach ($data['languages'] as $key => $language) {
      if (is_file(DIR_LANGUAGE . strtolower($language['code']) . '/' . strtolower($language['code']) . '.png')) {
        $data['languages'][$key]['image'] = 'language/' . strtolower($language['code']) . '/' . strtolower($language['code']) . '.png';
      } else if (!empty($language['image'])) {
        $data['languages'][$key]['image'] = 'view/image/flags/' . $language['image'];
      } else {
        $data['languages'][$key]['image'] = '';
      }
    }
    
    if (isset($this->request->post['add_page_description'])) {
      $data['add_page_description'] = $this->request->post['add_page_description'];
    } else {
      $data['add_page_description'] = [];
    }

    if (isset($this->request->post['add_keyword'])) {
      $data['add_keyword'] = $this->request->post['add_keyword'];
    } else {
      $data['add_keyword'] = ($this->ocfilter->opencart->version >= 30 ? [] : '');
    }    
    
    $data['multilang_keyword'] = ($this->ocfilter->opencart->version >= 30);
    
    if (isset($this->request->post['add_status'])) {
      $data['add_status'] = $this->request->post['add_status'];
    } else {
      $data['add_status'] = '';
    }

    if (isset($this->request->post['add_category_id'])) {
      $data['add_category_id'] = $this->request->post['add_category_id'];
    } else {
      $data['add_category_id'] = '';
    }

    $data['add_category_name'] = '';
    
    if ($data['add_category_id']) {
      $this->load->model('catalog/category');
      
      $category_info = $this->model_catalog_category->getCategory($data['add_category_id']);
      
      if ($category_info) {
        $data['add_category_name'] = ($category_info['path'] ? $category_info['path'] . ' > ' . $category_info['name'] : $category_info['name']);
      }
    }

    if (isset($this->request->post['ocfilter_filter'])) {
      $data['add_ocfilter_filter'] = $this->request->post['ocfilter_filter'];
    } else {
      $data['add_ocfilter_filter'] = [];
    }
    
    if (isset($this->request->post['add_page_store'])) {
      $data['add_page_store'] = $this->request->post['add_page_store'];
    } else {
      $data['add_page_store'] = [ 0 ];
    }

    $this->load->model('setting/store');

    $data['stores'] = $this->model_setting_store->getStores();

    array_unshift($data['stores'], [
      'store_id' => 0,
      'name' => $this->language->get('text_default')
    ]);
    
    if (isset($this->request->post['add_page_layout'])) {
      $data['add_page_layout'] = $this->request->post['add_page_layout'];
    } else {
      $data['add_page_layout'] = [];
    }    

    $this->load->model('design/layout');

    $data['layouts'] = $this->model_design_layout->getLayouts();        

    if (isset($this->request->post['add_category'])) {
      $data['add_category'] = $this->request->post['add_category'];
    } else {
      $data['add_category'] = '';
    }  

    if (isset($this->request->post['add_module'])) {
      $data['add_module'] = $this->request->post['add_module'];
    } else {
      $data['add_module'] = '';
    }  

    if (isset($this->request->post['add_product'])) {
      $data['add_product'] = $this->request->post['add_product'];
    } else {
      $data['add_product'] = '';
    }  

    if (isset($this->request->post['add_sitemap'])) {
      $data['add_sitemap'] = $this->request->post['add_sitemap'];
    } else {
      $data['add_sitemap'] = '';
    }              
  }

  private function setEditBatchData(&$data) {
    // Data
    if (isset($this->request->post['edit_action'])) {
      $data['edit_action'] = $this->request->post['edit_action'];
    } else {
      $data['edit_action'] = 'replace';
    }
    
    if (isset($this->request->post['edit_text_1'])) {
      $data['edit_text_1'] = $this->request->post['edit_text_1'];
    } else {
      $data['edit_text_1'] = '';
    }

    if (isset($this->request->post['edit_text_2'])) {
      $data['edit_text_2'] = $this->request->post['edit_text_2'];
    } else {
      $data['edit_text_2'] = '';
    }

    if (isset($this->request->post['edit_destination'])) {
      $data['edit_destination'] = $this->request->post['edit_destination'];
    } else {
      $data['edit_destination'] = 'all';
    }

    if (isset($this->request->post['edit_target'])) {
      $data['edit_target'] = $this->request->post['edit_target'];
    } else {
      $data['edit_target'] = 'all';
    }

    if (isset($this->request->post['edit_position'])) {
      $data['edit_position'] = $this->request->post['edit_position'];
    } else {
      $data['edit_position'] = 'prepend';
    }

    if (isset($this->request->post['edit_category_id'])) {
      $data['edit_category_id'] = $this->request->post['edit_category_id'];
    } else {
      $data['edit_category_id'] = '*';
    }

    $data['edit_category_name'] = '';
    
    if ($data['edit_category_id'] && $data['edit_category_id'] != '*') {
      $this->load->model('catalog/category');
      
      $category_info = $this->model_catalog_category->getCategory($data['edit_category_id']);
      
      if ($category_info) {
        $data['edit_category_name'] = ($category_info['path'] ? $category_info['path'] . ' > ' . $category_info['name'] : $category_info['name']);
      }
    }

    if (isset($this->request->post['edit_status'])) {
      $data['edit_status'] = $this->request->post['edit_status'];
    } else {
      $data['edit_status'] = '*';
    }    
    
    if (isset($this->request->post['edit_category'])) {
      $data['edit_category'] = $this->request->post['edit_category'];
    } else {
      $data['edit_category'] = '*';
    }  

    if (isset($this->request->post['edit_module'])) {
      $data['edit_module'] = $this->request->post['edit_module'];
    } else {
      $data['edit_module'] = '*';
    }  

    if (isset($this->request->post['edit_product'])) {
      $data['edit_product'] = $this->request->post['edit_product'];
    } else {
      $data['edit_product'] = '*';
    }  

    if (isset($this->request->post['edit_sitemap'])) {
      $data['edit_sitemap'] = $this->request->post['edit_sitemap'];
    } else {
      $data['edit_sitemap'] = '*';
    }          
    
    // Filter list form data
    if (isset($this->request->post['filter_name'])) {
      $data['filter_name'] = $this->request->post['filter_name'];
    } else if (isset($this->request->get['filter_name'])) {
      $data['filter_name'] = $this->request->get['filter_name'];    
    } else {
      $data['filter_name'] = '';
    }  

    if (isset($this->request->post['filter_status'])) {
      $data['filter_status'] = $this->request->post['filter_status'];
    } else if (isset($this->request->get['filter_status'])) {
      $data['filter_status'] = $this->request->get['filter_status'];          
    } else {
      $data['filter_status'] = null;
    }  
    
    if (isset($this->request->post['filter_category_id'])) {
      $data['filter_category_id'] = $this->request->post['filter_category_id'];
    } else if (isset($this->request->get['filter_category_id'])) {
      $data['filter_category_id'] = $this->request->get['filter_category_id'];          
    } else {
      $data['filter_category_id'] = null;
    }  
    
    if (isset($this->request->post['filter_category_id'])) {
      $this->load->model('catalog/category');
      
      $category_info = $this->model_catalog_category->getCategory($this->request->post['filter_category_id']);
      
      if ($category_info) {
        $data['filter_category'] = ($category_info['path'] ? $category_info['path'] . ' > ' . $category_info['name'] : $category_info['name']);
      }
    }       
  }

  private function getList($data) {
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js?v=' . OCF_VERSION);   

    $this->document->addStyle('view/javascript/ocfilter/summernote/summernote.min.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/summernote/summernote.min.js?v=' . OCF_VERSION);    

    foreach ($this->ocfilter->admin->getControllerParams() as $key => $default) {
      if (isset($this->request->get[$key])) {
        ${$key} = $this->request->get[$key];
      } else {
        ${$key} = $default;
      }
    }

    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('common/dashboard', $this->ocfilter->admin->getToken(true), 'SSL'),
      'text' => $this->language->get('text_home')
    ];

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('extension/module/ocfilter/page', $this->ocfilter->admin->getToken(true), 'SSL'),
      'text' => $this->language->get('heading_title')
    ];

    $url = $this->ocfilter->admin->getURL();

    $data['language_id'] = $this->config->get('config_language_id');

    $data['add'] = $this->url->link('extension/module/ocfilter/page/add', $url, 'SSL');
    $data['add_batch'] = $this->url->link('extension/module/ocfilter/page/addBatch', $url, 'SSL');
    $data['edit_batch'] = $this->url->link('extension/module/ocfilter/page/editBatch', $url, 'SSL');
    $data['delete'] = $this->url->link('extension/module/ocfilter/page/delete', $url, 'SSL');

    $data['pages'] = [];

    $filter_data = [];

    foreach ($this->ocfilter->admin->getControllerParams() as $key => $default) {
      $filter_data[$key] = ${$key};
    }

    $filter_data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
    $filter_data['limit'] = $this->config->get('config_limit_admin');     

    $pages_total = $this->model_extension_module_ocfilter_page->getTotalPages($filter_data);

    $results = $this->model_extension_module_ocfilter_page->getPages($filter_data);

    foreach ($results as $result) {
      $data['pages'][] = [
        'page_id' => $result['page_id'],
        'dynamic_id' => $result['dynamic_id'],
        'dynamic' => $result['dynamic'],
        'category' => $result['category'],
        'module' => $result['module'],
        'product' => $result['product'],
        'sitemap' => $result['sitemap'],
        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'category_name' => strip_tags(html_entity_decode($result['category_name'], ENT_QUOTES, 'UTF-8')),
        'selected' => isset($this->request->post['selected']) && in_array($result['page_id'], $this->request->post['selected']),
        'status' => $result['status'],
        'edit' => $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $result['page_id'], 'SSL'),
        'show' => $this->url->link('extension/module/ocfilter/page/show', $url . '&page_id=' . $result['page_id'], 'SSL'),
      ];
    }

    $data[$this->ocfilter->admin->getTokenIndex()] = $this->ocfilter->admin->getToken();

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }
    
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $url = $this->ocfilter->admin->getURL('page');

    $pagination = new Pagination();
    $pagination->total = $pages_total;
    $pagination->page = $page;
    $pagination->limit = $this->config->get('config_limit_admin');
    $pagination->text = $this->language->get('text_pagination');
    $pagination->url = $this->url->link('extension/module/ocfilter/page', $url . '&page={page}', 'SSL');

    $data['pagination'] = $pagination->render();
    
    $data['results'] = sprintf($this->language->get('text_pagination'), 
      ($pages_total ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0), 
      ((($page - 1) * $this->config->get('config_limit_admin')) > ($pages_total - $this->config->get('config_limit_admin'))) ? $pages_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), 
      $pages_total, 
      ceil($pages_total / $this->config->get('config_limit_admin'))
    );
    
    foreach ($this->ocfilter->admin->getControllerParams() as $key => $default) {
      $data[$key] = ${$key};           
    }    

    $data['filter_category'] = '';

    if (isset($this->request->get['filter_category_id'])) {
      $this->load->model('catalog/category');
      
      $category_info = $this->model_catalog_category->getCategory($this->request->get['filter_category_id']);
      
      if ($category_info) {
        $data['filter_category'] = ($category_info['path'] ? $category_info['path'] . ' > ' . $category_info['name'] : $category_info['name']);
      }
    }       

    // Batch forms data & errors
    $this->setAddBatchData($data);
    $this->setEditBatchData($data);

    $data['tpl_bool_button'] = $this->ocfilter->admin->getBoolControl($this->load->language('extension/module/ocfilter/page'));

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/page_list', $data);
  }

  private function getForm($data) {
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js?v=' . OCF_VERSION);
    
    $this->document->addStyle('view/javascript/ocfilter/summernote/summernote.min.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/summernote/summernote.min.js?v=' . OCF_VERSION);           
    
    $data['text_form'] = !isset($this->request->get['page_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['name'])) {
      $data['error_name'] = $this->error['name'];
    } else {
      $data['error_name'] = [];
    }

    if (isset($this->error['heading_title'])) {
      $data['error_heading_title'] = $this->error['heading_title'];
    } else {
      $data['error_heading_title'] = [];
    }
    
    if (isset($this->error['meta_title'])) {
      $data['error_meta_title'] = $this->error['meta_title'];
    } else {
      $data['error_meta_title'] = [];
    }

    if (isset($this->error['meta_description'])) {
      $data['error_meta_description'] = $this->error['meta_description'];
    } else {
      $data['error_meta_description'] = [];
    }

    if (isset($this->error['meta_keyword'])) {
      $data['error_meta_keyword'] = $this->error['meta_keyword'];
    } else {
      $data['error_meta_keyword'] = [];
    }

    if (isset($this->error['keyword'])) {
      $data['error_keyword'] = $this->error['keyword'];
    } else {
      $data['error_keyword'] = '';
    }

    if (isset($this->error['category'])) {
      $data['error_category'] = $this->error['category'];
    } else {
      $data['error_category'] = '';
    }
    
    if (isset($this->error['filter'])) {
      $data['error_filter'] = $this->error['filter'];
    } else {
      $data['error_filter'] = '';
    }    

    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('common/dashboard', $this->ocfilter->admin->getToken(true), 'SSL'),
      'text' => $this->language->get('text_home')
    ];

    $url = $this->ocfilter->admin->getURL();

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('extension/module/ocfilter/page', $url, 'SSL'),
      'text' => $this->language->get('heading_title')
    ];
    
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }    

    if (!isset($this->request->get['page_id'])) {
      $data['action'] = $this->url->link('extension/module/ocfilter/page/add', $url, 'SSL');
    } else {
      $data['action'] = $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $this->request->get['page_id'], 'SSL');
    }
    
    if (!isset($this->request->get['page_id'])) {
      $data['save'] = $this->url->link('extension/module/ocfilter/page/add', $url, 'SSL');
      $data['apply'] = $this->url->link('extension/module/ocfilter/page/add', $url . '&apply=1', 'SSL');
      $data['apply_add'] = $this->url->link('extension/module/ocfilter/page/add', $url . '&apply_add=1', 'SSL');
    } else {
      $data['save'] = $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $this->request->get['page_id'], 'SSL');
      $data['apply'] = $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $this->request->get['page_id'] . '&apply=1', 'SSL');
      $data['apply_add'] = $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $this->request->get['page_id'] . '&apply_add=1', 'SSL');
    }     

    $data['cancel'] = $this->url->link('extension/module/ocfilter/page', $url, 'SSL');

    $data[$this->ocfilter->admin->getTokenIndex()] = $this->ocfilter->admin->getToken();

    $this->load->model('localisation/language');

    $data['languages'] = $this->model_localisation_language->getLanguages();

    foreach ($data['languages'] as $key => $language) {
      if (is_file(DIR_LANGUAGE . strtolower($language['code']) . '/' . strtolower($language['code']) . '.png')) {
        $data['languages'][$key]['image'] = 'language/' . strtolower($language['code']) . '/' . strtolower($language['code']) . '.png';
      } else if (!empty($language['image'])) {
        $data['languages'][$key]['image'] = 'view/image/flags/' . $language['image'];
      } else {
        $data['languages'][$key]['image'] = '';
      }
    }

    if (isset($this->request->get['page_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $this->ocfilter->admin->setControllerEntity($this->model_extension_module_ocfilter_page->getPage($this->request->get['page_id']));      
    }
    
    if (isset($this->request->post['page_description'])) {
      $data['page_description'] = $this->request->post['page_description'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['page_description'] = $this->model_extension_module_ocfilter_page->getPageDescriptions($this->request->get['page_id']);
    } else {
      $data['page_description'] = [];
    }

    if (isset($this->request->get['page_id'])) {
      $data['page_id'] = $this->request->get['page_id'];
      
      $data['breadcrumbs'][] = [
        'href' => $this->url->link('extension/module/ocfilter/page/edit', $url . '&page_id=' . $this->request->get['page_id'], 'SSL'),
        'text' => !empty($data['page_description'][$this->config->get('config_language_id')]['name']) ? $data['page_description'][$this->config->get('config_language_id')]['name'] : $this->language->get('text_form')
      ];        
    } else {
      $data['page_id'] = 0;
      
      $data['breadcrumbs'][] = [
        'href' => $this->url->link('extension/module/ocfilter/page/add', $url, 'SSL'),
        'text' => $this->language->get('text_add')
      ];      
    }

    $data['status'] = $this->ocfilter->admin->getEntityValue('status', 1);
    $data['dynamic'] = $this->ocfilter->admin->getEntityValue('dynamic', 0);

    $data['module'] = $this->ocfilter->admin->getEntityValue('module', 0);
    $data['sitemap'] = $this->ocfilter->admin->getEntityValue('sitemap', 1);
    $data['category'] = $this->ocfilter->admin->getEntityValue('category', 0);
    $data['product'] = $this->ocfilter->admin->getEntityValue('product', 1);

    $data['category_id'] = $this->ocfilter->admin->getEntityValue('category_id');
    
    $data['category_name'] = '';
    
    if ($data['category_id']) {
      $this->load->model('catalog/category');
      
      $category_info = $this->model_catalog_category->getCategory($data['category_id']);
      
      if ($category_info) {
        $data['category_name'] = ($category_info['path'] ? $category_info['path'] . ' > ' . $category_info['name'] : $category_info['name']);
      }
    }
    
    $this->load->model('design/layout');

    $data['layouts'] = $this->model_design_layout->getLayouts();

    if (isset($this->request->post['page_layout'])) {
      $data['page_layout'] = $this->request->post['page_layout'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['page_layout'] = $this->model_extension_module_ocfilter_page->getPageLayouts($this->request->get['page_id']);
    } else {
      $data['page_layout'] = [];
    }
    
    if (isset($this->request->post['ocfilter_filter'])) {     
      foreach ($this->request->post['ocfilter_filter'] as $filter_key => $values) {
        if (isset($values['min']) && isset($values['max'])) {
          if ((strlen($values['min']) + strlen($values['max'])) < 1) {
            unset($this->request->post['ocfilter_filter'][$filter_key]);
          }          
        }
      }
      
      $data['ocfilter_filter'] = $this->request->post['ocfilter_filter'];
    } else {
      $data['ocfilter_filter'] = [];
    }   

    $this->load->model('setting/store');

    $data['stores'] = $this->model_setting_store->getStores();
    
    array_unshift($data['stores'], [
      'store_id' => 0,
      'name' => $this->language->get('text_default')
    ]);    

    if (isset($this->request->post['page_store'])) {
      $data['page_store'] = $this->request->post['page_store'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['page_store'] = $this->model_extension_module_ocfilter_page->getPageStores($this->request->get['page_id']);
    } else {
      $data['page_store'] = [ 0 ];
    }
    
    if (isset($this->request->post['keyword'])) {
      $data['keyword'] = $this->request->post['keyword'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['keyword'] = $this->model_extension_module_ocfilter_page->getPageUrlKeyword($this->request->get['page_id']);
    } else {
      $data['keyword'] = ($this->ocfilter->opencart->version >= 30 ? [] : '');
    }    
    
    $data['multilang_keyword'] = ($this->ocfilter->opencart->version >= 30);

    $data['tpl_bool_button'] = $this->ocfilter->admin->getBoolControl($this->load->language('extension/module/ocfilter/page'));

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/page_form', $data);
  }
 
  protected function validateDelete() {
    if (!$this->user->hasPermission('modify', 'extension/module/ocfilter/page')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }

  protected function validateForm($data = []) {       
    if (!$this->user->hasPermission('modify', 'extension/module/ocfilter/page')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$data) {
      $data = $this->request->post;
    }
    
    if (!isset($data['ocfilter_filter'])) {
      $data['ocfilter_filter'] = [];
    }
    
    $mask_pattern = '/\{F[0-9\.]+(\|L)?\}/';
    
    $mask_required = $data['dynamic'] && (floor((count($data['ocfilter_filter'], true) - count($data['ocfilter_filter'])) / 2) > 1);   

    foreach ($data['page_description'] as $language_id => $value) {
      if (utf8_strlen($value['name']) > 128) {
        $this->error['name'][$language_id] = sprintf($this->language->get('error_name'), 128);
      } else if (utf8_strlen($value['name']) > 0 && $mask_required && !preg_match($mask_pattern, $value['name'])) {
        $this->error['name'][$language_id] = $this->language->get('error_mask');
      }
      
      if ((utf8_strlen($value['heading_title']) < 2) || (utf8_strlen($value['heading_title']) > 255)) {
        $this->error['heading_title'][$language_id] = sprintf($this->language->get('error_heading_title'), 2, 255);
      } else if ($mask_required && !preg_match($mask_pattern, $value['heading_title'])) {
        $this->error['heading_title'][$language_id] = $this->language->get('error_mask');
      }

      if ((utf8_strlen($value['meta_title']) < 2) || (utf8_strlen($value['meta_title']) > 255)) {
        $this->error['meta_title'][$language_id] = sprintf($this->language->get('error_meta_title'), 2, 255);
      } else if ($mask_required && !preg_match($mask_pattern, $value['meta_title'])) {
        $this->error['meta_title'][$language_id] = $this->language->get('error_mask');
      }

      if (utf8_strlen($value['meta_keyword']) > 255) {
        $this->error['meta_keyword'][$language_id] = sprintf($this->language->get('error_meta_keyword'), 255);
      } else if ($mask_required && utf8_strlen($value['meta_title']) > 0 && !preg_match($mask_pattern, $value['meta_title'])) {
        $this->error['meta_title'][$language_id] = $this->language->get('error_mask');
      }      

      if (utf8_strlen($value['meta_description']) > 255) {
        $this->error['meta_description'][$language_id] = sprintf($this->language->get('error_meta_description'), 255);
      } else if ($mask_required && utf8_strlen($value['meta_description']) > 0 && !preg_match($mask_pattern, $value['meta_description'])) {
        $this->error['meta_description'][$language_id] = $this->language->get('error_mask');
      }
    }

    if (empty($data['category_id'])) {
      $this->error['category'] = $this->language->get('error_category');
    } else if (empty($data['ocfilter_filter'])) {
      $this->error['filter'] = $this->language->get('error_filter');
    } else {
      // Keyword
      $getKeywordErrorText = function($keyword, $store_id = null, $language_id = null) use ($data, $mask_required, $mask_pattern) {
        if ((utf8_strlen($keyword) < 1 && $data['dynamic']) || ($mask_required && !preg_match($mask_pattern, $keyword))) {
          return $this->language->get('error_mask');
        } else {
          $url_alias_info = $this->model_extension_module_ocfilter_page->getSeoUrl($keyword, $data['category_id'], $store_id, $language_id);

          if ($url_alias_info && (!isset($this->request->get['page_id']) || (isset($this->request->get['page_id']) && $url_alias_info['query'] != 'page_id=' . $this->request->get['page_id']))) {
            $text = $this->language->get('error_keyword_exist');        
            
            list($entity, $id) = explode('=', $url_alias_info['query']);
                    
            if (!empty($id) && $this->language->get('error_keyword_exist_' . $entity) != 'error_keyword_exist_' . $entity) {
              if ($entity == 'page_id') {
                $link = $this->url->link('extension/module/ocfilter/page/edit', $url_alias_info['query'] . '&' . $this->ocfilter->admin->getToken(true), 'SSL');
              } else if ($entity == 'category_id') {
                $link = $this->url->link('catalog/category/edit', $url_alias_info['query'] . '&' . $this->ocfilter->admin->getToken(true), 'SSL');
              } else if ($entity == 'product_id') {
                $link = $this->url->link('catalog/product/edit', $url_alias_info['query'] . '&' . $this->ocfilter->admin->getToken(true), 'SSL');
              } else if ($entity == 'manufacturer_id') {
                $link = $this->url->link('catalog/manufacturer/edit', $url_alias_info['query'] . '&' . $this->ocfilter->admin->getToken(true), 'SSL');
              } else if ($entity == 'information_id') {
                $link = $this->url->link('catalog/information/edit', $url_alias_info['query'] . '&' . $this->ocfilter->admin->getToken(true), 'SSL');
              } else {
                $link = '';
              }
              
              $text .= ' ' . sprintf($this->language->get('error_keyword_exist_' . $entity), $link);
            }
          
            return $text;
          } 
        }
        
        return '';
      };
            
      if ($this->ocfilter->opencart->version >= 30 && is_array($data['keyword'])) {
        foreach ($data['keyword'] as $store_id => $keyword_languages) {
          foreach ($keyword_languages as $language_id => $keyword) {    
            if ($error = $getKeywordErrorText($keyword, $store_id, $language_id)) {
              if (!isset($this->error['keyword'])) {
                $this->error['keyword'] = [ [] ];
              }
              
              $this->error['keyword'][$store_id][$language_id] = $error;
            }
          }
        }
      } else if ($this->ocfilter->opencart->version < 30 && is_string($data['keyword'])) {
        if ($error = $getKeywordErrorText($data['keyword'])) {         
          $this->error['keyword'] = $error;
        }
      }
    }

    if ($this->error && !isset($this->error['warning'])) {
      $this->error['warning'] = $this->language->get('error_warning');
    }

    return !$this->error;
  }
  
  protected function validateAddBatch() {
    if (!$this->user->hasPermission('modify', 'extension/module/ocfilter/page')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    $data = $this->request->post;

    if (!isset($data['ocfilter_filter'])) {
      $data['ocfilter_filter'] = [];
    }

    $mask_pattern = '/\{F[0-9\.]+(\|L)?\}/';
    
    $mask_required = (floor((count($data['ocfilter_filter'], true) - count($data['ocfilter_filter'])) / 2) > 1);
    
    if (!$mask_required) {
      foreach ($data['ocfilter_filter'] as $values) {
        if (false !== array_search(0, $values)) {
          $mask_required = true;
          
          break;
        }
      }
    }

    foreach ($data['add_page_description'] as $language_id => $value) {
      if (utf8_strlen($value['name']) > 128) {
        $this->error['name'][$language_id] = sprintf($this->language->get('error_name'), 128);
      } else if (utf8_strlen($value['name']) > 0 && $mask_required && !preg_match($mask_pattern, $value['name'])) {
        $this->error['name'][$language_id] = $this->language->get('error_mask');
      }
      
      if ((utf8_strlen($value['heading_title']) < 2) || (utf8_strlen($value['heading_title']) > 255)) {
        $this->error['heading_title'][$language_id] = sprintf($this->language->get('error_heading_title'), 2, 255);
      } else if ($mask_required && !preg_match($mask_pattern, $value['heading_title'])) {
        $this->error['heading_title'][$language_id] = $this->language->get('error_mask');
      }

      if ((utf8_strlen($value['meta_title']) < 2) || (utf8_strlen($value['meta_title']) > 255)) {
        $this->error['meta_title'][$language_id] = sprintf($this->language->get('error_meta_title'), 2, 255);
      } else if ($mask_required && !preg_match($mask_pattern, $value['meta_title'])) {
        $this->error['meta_title'][$language_id] = $this->language->get('error_mask');
      }

      if (utf8_strlen($value['meta_keyword']) > 255) {
        $this->error['meta_keyword'][$language_id] = sprintf($this->language->get('error_meta_keyword'), 255);
      } else if (utf8_strlen($value['meta_keyword']) > 0 && $mask_required && !preg_match($mask_pattern, $value['meta_keyword'])) {
        $this->error['meta_keyword'][$language_id] = $this->language->get('error_mask');
      }     

      if (utf8_strlen($value['meta_description']) > 255) {
        $this->error['meta_description'][$language_id] = sprintf($this->language->get('error_meta_description'), 255);
      } else if (utf8_strlen($value['meta_description']) > 0 && $mask_required && !preg_match($mask_pattern, $value['meta_description'])) {
        $this->error['meta_description'][$language_id] = $this->language->get('error_mask');
      }
    }

    if (empty($data['add_category_id'])) {
      $this->error['category'] = $this->language->get('error_category');
    }
    
    if (empty($data['ocfilter_filter'])) {
      $this->error['filter'] = $this->language->get('error_filter');
    } else {    
      if ($this->ocfilter->opencart->version >= 30 && is_array($data['add_keyword'])) {
        foreach ($data['add_keyword'] as $store_id => $keyword_languages) {
          foreach ($keyword_languages as $language_id => $keyword) {    
            if (utf8_strlen($keyword) > 0 && $mask_required && !preg_match($mask_pattern, $keyword)) {
              if (!isset($this->error['keyword'])) {
                $this->error['keyword'] = [ [] ];
              }
              
              $this->error['keyword'][$store_id][$language_id] = $this->language->get('error_mask');
            }
          }
        }
      } else if ($this->ocfilter->opencart->version < 30 && is_string($data['add_keyword'])) {
        if (utf8_strlen($data['add_keyword']) > 0 && $mask_required && !preg_match($mask_pattern, $data['add_keyword'])) {         
          $this->error['keyword'] = $this->language->get('error_mask');
        }
      }
    }

    if ($this->error && !isset($this->error['warning'])) {
      $this->error['warning'] = $this->language->get('error_warning');
    }

    return !$this->error;
  }
  
  protected function validateEditBatch() {
    if (!$this->user->hasPermission('modify', 'extension/module/ocfilter/page')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    
    if ($this->request->post['edit_action'] == 'replace') {
      if (utf8_strlen($this->request->post['edit_text_1']) < 1) {
        $this->error['warning'] = $this->language->get('error_replace_text');
      }
    }
    
    if ($this->request->post['edit_action'] == 'add' && utf8_strlen($this->request->post['edit_text_1']) < 1) {
      $this->error['warning'] = $this->language->get('error_add_text');
    }    
    
    if ($this->request->post['edit_target'] == 'filter' && empty($this->request->post['filter_name']) && empty($this->request->post['filter_category_id']) && (!isset($this->request->post['filter_status']) || $this->request->post['filter_status'] == '*')) {
      $this->error['warning'] = $this->language->get('error_target_empty');
    } else if ($this->request->post['edit_target'] == 'selected' && empty($this->request->post['selected'])) {
      $this->error['warning'] = $this->language->get('error_target_empty');
    }
    
    if ($this->error && !isset($this->error['warning'])) {
      $this->error['warning'] = $this->language->get('error_warning');
    }

    return !$this->error;
  }
  
  protected function preparePageData($data) {
    if (isset($data['ocfilter_filter'])) {
      foreach ($data['ocfilter_filter'] as $filter_key => $values) {
        if (isset($values['min']) && isset($values['max'])) {
          if ((strlen($values['min']) + strlen($values['max'])) < 1) {
            unset($data['ocfilter_filter'][$filter_key]);
          }          
        } else {
          // If all values
          if (false !== ($key = array_search(0, $values))) {
            if ($data['dynamic']) {
              // - remove all another
              $data['ocfilter_filter'][$filter_key] = [ 0 ];
            } else {
              // - remove this
              unset($data['ocfilter_filter'][$filter_key][$key]);
            }           
          }
        }        
      }
    }   
       
    return $data;
  }
  
  public function editImmediately() {
    $json = [];

    if (isset($this->request->get['page_id']) && isset($this->request->post['field']) && isset($this->request->post['value'])) {
      $this->load->model('extension/module/ocfilter/page');
      
      $json['status'] = $this->model_extension_module_ocfilter_page->editPageImmediately($this->request->get['page_id'], $this->request->post);
    } else {
      $json['status'] = false;
    }

    $this->ocfilter->opencart->responseJSON($json);
  }   
  
  public function autocomplete() {
    $json = [];

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('extension/module/ocfilter/page');
      
      $filter_data = [
        'autocomplete' => true,
        
        'filter_name' => $this->request->get['filter_name'],
        'start' => 0,
        'limit' => 15
      ];

      $results = $this->model_extension_module_ocfilter_page->getPages($filter_data);

      foreach ($results as $result) {
        $json[] = [
          'page_id' => $result['page_id'],
          'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          'category' => strip_tags(html_entity_decode($result['category_name'], ENT_QUOTES, 'UTF-8')),
        ];
      }
    }

    $this->ocfilter->opencart->responseJSON($json);
  }  
}