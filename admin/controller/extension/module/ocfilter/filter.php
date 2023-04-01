<?php
class ControllerExtensionModuleOCFilterFilter extends Controller {
  protected $error = [];

  public function __construct($registry) {
    parent::__construct($registry);
    
    // Controller possible GET vars => default value  
    $this->ocfilter->admin->setControllerParams([
      'filter_name' => '',
      'filter_category_id' => null,
      'filter_type' => null,
      'filter_source' => null,
      'filter_status' => null,
      
      'sort' => 'of.sort_order, ofd.name',
      'order' => 'DESC',
      'page' => 1,     
    ]);
  }

  public function index() {
    $data = $this->load->language('extension/module/ocfilter/filter');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/filter');

    $this->getList($data);
  }

  public function add() {
    $data = $this->load->language('extension/module/ocfilter/filter');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/filter');

    $this->convertFilterValuesPOST();

    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {            
      $filter_key = $this->model_extension_module_ocfilter_filter->addFilter($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      if (isset($this->request->get['apply'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter/edit', $this->ocfilter->admin->getURL() . '&filter_key=' . $filter_key, 'SSL'));
      } else if (isset($this->request->get['apply_add'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter/add', $this->ocfilter->admin->getURL(), 'SSL'));        
      } else {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter', $this->ocfilter->admin->getURL(), 'SSL'));
      }    
    }

    $this->getForm($data);
  }

  public function edit() {
    $data = $this->load->language('extension/module/ocfilter/filter');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/filter');
  
    $this->convertFilterValuesPOST();  
  
    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
      $this->model_extension_module_ocfilter_filter->editFilter($this->request->get['filter_key'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      if (isset($this->request->get['apply'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter/edit', $this->ocfilter->admin->getURL() . '&filter_key=' . $this->request->get['filter_key'], 'SSL'));
      } else if (isset($this->request->get['apply_add'])) {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter/add', $this->ocfilter->admin->getURL(), 'SSL'));        
      } else {
        $this->response->redirect($this->url->link('extension/module/ocfilter/filter', $this->ocfilter->admin->getURL(), 'SSL'));
      }  
    }

    $this->getForm($data);
  }

  public function delete() {
    $data = $this->load->language('extension/module/ocfilter/filter');
    
    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('extension/module/ocfilter/filter');

    if (isset($this->request->post['selected'])) {
      foreach ($this->request->post['selected'] as $filter_key) {
        $this->model_extension_module_ocfilter_filter->deleteFilter($filter_key);
      }
            
      $this->session->data['success'] = $this->language->get('text_success');

      $this->response->redirect($this->url->link('extension/module/ocfilter/filter', $this->ocfilter->admin->getURL(), 'SSL'));
    } else if ($this->request->server['REQUEST_METHOD'] != 'POST') {
      $this->response->redirect($this->url->link('extension/module/ocfilter/filter', $this->ocfilter->admin->getURL(), 'SSL'));
    }

    $this->getList($data);
  }

  /*
  * Bypass php input_max_vars limitation
  * Convert from one JSON string to POST array
  */
  protected function convertFilterValuesPOST() {    
    if (!empty($this->request->post['filter_value'])) {
      $filter_value = json_decode(html_entity_decode($this->request->post['filter_value'], ENT_QUOTES, 'UTF-8'), true);
      
      $this->request->post['filter_value'] = [];
      
      foreach ($filter_value as $item) {
        $this->request->post['filter_value'][] = [
          'value_id' => $item[0],
          'image' => $item[1],
          'color' => $item[2],
          'sort_order' => $item[3],
          'description' => array_map(function($v) { return [ 'name' => $v ]; }, $item[5]),
        ];
      }
    }
  }

  protected function getList($data) {
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js?v=' . OCF_VERSION);

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
      'href' => $this->url->link('extension/module/ocfilter/filter', $this->ocfilter->admin->getToken(true), 'SSL'),
      'text' => $this->language->get('heading_title')
    ];

    $url = $this->ocfilter->admin->getURL();

    $data['language_id'] = $this->config->get('config_language_id');

    $data['add'] = $this->url->link('extension/module/ocfilter/filter/add', $url, 'SSL');
    $data['delete'] = $this->url->link('extension/module/ocfilter/filter/delete', $url, 'SSL');

    $data['filters'] = [];

    $filter_data = [];

    foreach ($this->ocfilter->admin->getControllerParams() as $key => $default) {
      $filter_data[$key] = ${$key};
    }

    $filter_data['start'] = ($page - 1) * $this->config->get('config_limit_admin');
    $filter_data['limit'] = $this->config->get('config_limit_admin');

    $filter_total = $this->model_extension_module_ocfilter_filter->getTotalFilters($filter_data);
    
    $results = $this->model_extension_module_ocfilter_filter->getFilters($filter_data);

    foreach ($results as $result) {       
      $values_data = $this->model_extension_module_ocfilter_filter->getFilterValuesCondensed($result['filter_key']);
      
      $values_data = array_map(function($v) {
        return strip_tags(html_entity_decode($v, ENT_QUOTES, 'UTF-8'));
      }, $values_data);               
            
      $data['filters'][] = [
        'filter_key' => $result['filter_key'],
        'source' => $this->ocfilter->params->source($result['source'])->name(),
        'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
        'type' => $result['type'],
        'sort_order' => $result['sort_order'],
        'selected' => isset($this->request->post['selected']) && in_array($result['filter_key'], $this->request->post['selected']),
        'total_values' => (int)$result['total_values'],
        'values' => $values_data,
        'status' => $result['status'],
        'dropdown' => $result['dropdown'],
        'edit' => $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $result['filter_key'], 'SSL')
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

    $url = $this->ocfilter->admin->getURL('sort', 'order');

    $data['reset_sort'] = $this->url->link('extension/module/ocfilter/filter', $url, 'SSL');

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    $data['sort_name'] = $this->url->link('extension/module/ocfilter/filter', $url . '&sort=ofd.name', 'SSL');
    $data['sort_numeric'] = $this->url->link('extension/module/ocfilter/filter', $url . '&sort=numeric', 'SSL');
    $data['sort_total_values'] = $this->url->link('extension/module/ocfilter/filter', $url . '&sort=total_values', 'SSL');
    $data['sort_order'] = $this->url->link('extension/module/ocfilter/filter', $url . '&sort=of.sort_order', 'SSL');

    $url = $this->ocfilter->admin->getURL('page');
    
    $pagination = new Pagination();
    $pagination->total = $filter_total;
    $pagination->page = $page;
    $pagination->limit = $this->config->get('config_limit_admin');
    $pagination->text = $this->language->get('text_pagination');
    $pagination->url = $this->url->link('extension/module/ocfilter/filter', $url . '&page={page}', 'SSL');

    $data['pagination'] = $pagination->render();

    $data['results'] = sprintf($this->language->get('text_pagination'), 
      ($filter_total ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0), 
      ((($page - 1) * $this->config->get('config_limit_admin')) > ($filter_total - $this->config->get('config_limit_admin'))) ? $filter_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), 
      $filter_total, 
      ceil($filter_total / $this->config->get('config_limit_admin'))
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

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    
    $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/filter_list', $data);
  }

  protected function getForm($data) {
    $data['text_form'] = !isset($this->request->get['filter_key']) ? $this->language->get('text_add') : $this->language->get('text_edit');
    
    $this->document->addStyle('view/stylesheet/ocfilter/ocfilter.css?v=' . OCF_VERSION);
    $this->document->addScript('view/javascript/ocfilter/ocfilter.js?v=' . OCF_VERSION);

    if (isset($this->error['warning'])) {
      $data['error_warning'] = $this->error['warning'];
    } else {
      $data['error_warning'] = '';
    }

    if (isset($this->error['name'])) {
      $data['error_name'] = $this->error['name'];
    } else {
      $data['error_name'] = '';
    }
   
    if (isset($this->error['suffix'])) {
      $data['error_suffix'] = $this->error['suffix'];
    } else {
      $data['error_suffix'] = [];
    }

    if (isset($this->error['description'])) {
      $data['error_description'] = $this->error['description'];
    } else {
      $data['error_description'] = [];
    }

    if (isset($this->error['filter_value'])) {
      $data['error_filter_value'] = $this->error['filter_value'];
    } else {
      $data['error_filter_value'] = [];
    }

    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];
      
      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('common/dashboard', $this->ocfilter->admin->getToken(true), 'SSL'),
      'text' => $this->language->get('text_home')
    ];

    $url = $this->ocfilter->admin->getURL();

    $data['breadcrumbs'][] = [
      'href' => $this->url->link('extension/module/ocfilter/filter', $url, 'SSL'),
      'text' => $this->language->get('heading_title')
    ];   

    if (!isset($this->request->get['filter_key'])) {
      $data['action'] = $this->url->link('extension/module/ocfilter/filter/add', $url, 'SSL');
    } else {
      $data['action'] = $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $this->request->get['filter_key'], 'SSL');
    }
    
    if (!isset($this->request->get['filter_key'])) {
      $data['save'] = $this->url->link('extension/module/ocfilter/filter/add', $url, 'SSL');
      $data['apply'] = $this->url->link('extension/module/ocfilter/filter/add', $url . '&apply=1', 'SSL');
      $data['apply_add'] = $this->url->link('extension/module/ocfilter/filter/add', $url . '&apply_add=1', 'SSL');
    } else {
      $data['save'] = $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $this->request->get['filter_key'], 'SSL');
      $data['apply'] = $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $this->request->get['filter_key'] . '&apply=1', 'SSL');
      $data['apply_add'] = $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $this->request->get['filter_key'] . '&apply_add=1', 'SSL');
    }     

    $data['cancel'] = $this->url->link('extension/module/ocfilter/filter', $url, 'SSL');

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

    if (isset($this->request->get['filter_key']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $this->ocfilter->admin->setControllerEntity($this->model_extension_module_ocfilter_filter->getFilter($this->request->get['filter_key']));
    }

    if (isset($this->request->post['filter_description'])) {
      $data['filter_description'] = $this->request->post['filter_description'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['filter_description'] = $this->model_extension_module_ocfilter_filter->getFilterDescriptions($this->request->get['filter_key']);
    } else {
      $data['filter_description'] = [];
    }

    if (isset($this->request->get['filter_key'])) {
      $data['filter_key'] = $this->request->get['filter_key'];
      
      $data['breadcrumbs'][] = [
        'href' => $this->url->link('extension/module/ocfilter/filter/edit', $url . '&filter_key=' . $this->request->get['filter_key'], 'SSL'),
        'text' => !empty($data['filter_description'][$this->config->get('config_language_id')]['name']) ? $data['filter_description'][$this->config->get('config_language_id')]['name'] : $this->language->get('text_form')
      ];        
    } else {
      $data['filter_key'] = '';
      
      $data['breadcrumbs'][] = [
        'href' => $this->url->link('extension/module/ocfilter/filter/add', $url, 'SSL'),
        'text' => $this->language->get('text_add')
      ];      
    }

    if (isset($this->request->post['filter_value'])) {
      $data['filter_value'] = $this->request->post['filter_value'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['filter_value'] = $this->model_extension_module_ocfilter_filter->getFilterValuesDescriptions($this->request->get['filter_key']);
    } else {
      $data['filter_value'] = [];
    }

    $this->load->model('tool/image');

    $data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 22, 22);

    foreach ($data['filter_value'] as $key => $value) {
      if ($value['image'] && file_exists(DIR_IMAGE . $value['image'])) {
        $data['filter_value'][$key]['thumb'] = $this->model_tool_image->resize($value['image'], 22, 22);
      } else {
        $data['filter_value'][$key]['thumb'] = $data['no_image'];
      }
    }

    $data['status'] = $this->ocfilter->admin->getEntityValue('status', 1);
    $data['sort_order'] = $this->ocfilter->admin->getEntityValue('sort_order');
    $data['type'] = $this->ocfilter->admin->getEntityValue('type', 'checkbox');
    $data['dropdown'] = $this->ocfilter->admin->getEntityValue('dropdown', 0);
    $data['color'] = $this->ocfilter->admin->getEntityValue('color');
    $data['image'] = $this->ocfilter->admin->getEntityValue('image');
    $data['sort_order'] = $this->ocfilter->admin->getEntityValue('sort_order', 0);

    $data['type_items'] = [
      'checkbox' => $this->language->get('text_checkbox'),
      'radio' => $this->language->get('text_radio'),
      'slide' => $this->language->get('text_slide'),
      'slide_dual' => $this->language->get('text_slide_dual')
    ];

    if (isset($this->request->post['filter_category'])) {
      $data['filter_category'] = $this->request->post['filter_category'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['filter_category'] = $this->model_extension_module_ocfilter_filter->getFilterCategories($this->request->get['filter_key']);
    } else {
      $data['filter_category'] = [];
    }

    if (array_key_exists(0, $data['filter_category'])) {
      $data['filter_category'][0] = $this->language->get('text_all');
    }

    $this->load->model('setting/store');

    $data['stores'] = $this->model_setting_store->getStores();

    array_unshift($data['stores'], [
      'store_id' => 0,
      'name' => $this->language->get('text_default')
    ]);    

    if (isset($this->request->post['filter_store'])) {
      $data['filter_store'] = $this->request->post['filter_store'];
    } else if ($this->ocfilter->admin->getControllerEntity()) {
      $data['filter_store'] = $this->model_extension_module_ocfilter_filter->getFilterStores($this->request->get['filter_key']);
    } else {
      $data['filter_store'] = [ 0 ];
    }

    $data['tpl_bool_button'] = $this->ocfilter->admin->getBoolControl($this->load->language('extension/module/ocfilter/filter'));

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/filter_form', $data);
  }

  public function editImmediately() {
    $json = [];

    if (isset($this->request->get['filter_key']) && isset($this->request->post['field']) && isset($this->request->post['value'])) {
      $this->load->model('extension/module/ocfilter/filter');

      $json['status'] = $this->model_extension_module_ocfilter_filter->editFilterImmediately($this->request->get['filter_key'], $this->request->post);
    } else {
      $json['status'] = false;
    }

    $this->ocfilter->opencart->responseJSON($json);
  }
  
  protected function validateForm() {
    if (!$this->user->hasPermission('modify', 'extension/module/ocfilter/filter')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }
    
    foreach ($this->request->post['filter_description'] as $language_id => $value) {
      if (utf8_strlen($value['name']) < 2 || utf8_strlen($value['name']) > 128) {
        $this->error['name'][$language_id] = sprintf($this->language->get('error_name'), 2, 128);
      }
      
      if (utf8_strlen($value['suffix']) > 32) {
        $this->error['suffix'][$language_id] = sprintf($this->language->get('error_suffix'), 32);
      }

      if (utf8_strlen($value['description']) > 255) {
        $this->error['description'][$language_id] = sprintf($this->language->get('error_description'), 255);
      }      
    }
      
    if (!$this->error && isset($this->request->post['filter_value']) && is_array($this->request->post['filter_value'])) {
      foreach ($this->request->post['filter_value'] as $key => $value) {
        foreach ($value['description'] as $language_id => $description) {
          if (utf8_strlen($description['name']) < 1 || utf8_strlen($description['name']) > 255) {
            $this->error['filter_value'][$key]['name'] = sprintf($this->language->get('error_value_name'), 1, 255);
          }
        }
      }            
    }
    
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;    
  }  

  public function relation() {
    $data = $this->load->language('extension/module/ocfilter/filter');   
  
    $this->load->model('extension/module/ocfilter/filter');
    $this->load->model('extension/module/ocfilter/page');
   
    if (isset($this->request->get['category_id'])) {
      $filter_category_id = $this->request->get['category_id'];
    } else {
      $filter_category_id = null;
    }
    
    $filter_ignore_slide = isset($this->request->get['ignore_slide']);           
       
    // Get selecteds   
    $results = [];
       
    if (isset($this->request->get['selected']) && is_array($this->request->get['selected'])) {
      foreach ($this->request->get['selected'] as $filter_key => $values) {
        if (isset($values['min']) && isset($values['max']) && (strlen($values['min']) + strlen($values['max'])) > 0) {
          $results[] = [
            'filter_key' => $filter_key,
            'min' => $values['min'],
            'max' => $values['max'],
          ]; 
        } else {
          foreach ($values as $value_id) {
            $results[] = [
              'filter_key' => $filter_key,
              'value_id' => $value_id,
            ];          
          }
        }
      }
    } else if (isset($this->request->get['product_id'])) {
      $results = $this->model_extension_module_ocfilter_filter->getProductValues($this->request->get['product_id']);
      
      $results = array_merge($results, 
        $this->model_extension_module_ocfilter_filter->getProductRangeValues($this->request->get['product_id'])
      );
    } else if (isset($this->request->get['page_id'])) {
      $results = $this->model_extension_module_ocfilter_page->getPageValues($this->request->get['page_id']);
    }

    $results = array_map(function($v) {      
      return array_merge([
        'value_id' => 0,
        'min' => 0,
        'max' => 0,
      ], $v);
    }, $results);

    $selected = [];

    if ($results) {
      foreach ($results as $result) {
        if (!isset($selected[$result['filter_key']])) {
          $selected[$result['filter_key']] = [];
        }

        $selected[$result['filter_key']][$result['value_id']] = [ $result['min'], $result['max'] ];
      }      
    }   

    $results = []; 
     
    // Special filters
    if (isset($this->request->get['page_id'])) {
      $this->load->model('catalog/manufacturer');
      $this->load->model('localisation/stock_status');
      
      $results[] = [
        'filter_key' => $this->ocfilter->params->special('manufacturer')->key(),
        'source' => $this->ocfilter->params->source('special')->id(),
        'name' => $this->language->get('text_manufacturer'),
        'suffix' => '',
        'status' => $this->ocfilter->config('special_manufacturer'),
        'type' => $this->ocfilter->config('special_manufacturer_type'),
        'total_values' => $this->model_catalog_manufacturer->getTotalManufacturers(),
      ]; 
      
      if (!$filter_ignore_slide) {
        $results[] = [
          'filter_key' => $this->ocfilter->params->special('price')->key(),
          'source' => $this->ocfilter->params->source('special')->id(),
          'name' => $this->language->get('text_price'),
          'suffix' => '',        
          'status' => $this->ocfilter->config('special_price'),
          'type' => 'slide_dual',
          'total_values' => null,
        ];        
      }

      $results[] = [
        'filter_key' => $this->ocfilter->params->special('stock')->key(),
        'source' => $this->ocfilter->params->source('special')->id(),
        'name' => $this->language->get('text_stock_status'),
        'suffix' => '',        
        'status' => $this->ocfilter->config('special_stock'),
        'type' => $this->ocfilter->config('special_stock_type'),
        'total_values' => null,
      ];        

      $results[] = [
        'filter_key' => $this->ocfilter->params->special('discount')->key(),
        'source' => $this->ocfilter->params->source('special')->id(),
        'name' => $this->language->get('text_discount'),
        'suffix' => '',        
        'status' => $this->ocfilter->config('special_discount'),
        'type' => 'checkbox',
        'total_values' => 1,
      ];   

      $results[] = [
        'filter_key' => $this->ocfilter->params->special('newest')->key(),
        'source' => $this->ocfilter->params->source('special')->id(),
        'name' => $this->language->get('text_newest'),
        'suffix' => '',        
        'status' => $this->ocfilter->config('special_newest'),
        'type' => 'checkbox',
        'total_values' => 1,
      ]; 

      if (!$filter_ignore_slide) {       
        $results[] = [
          'filter_key' => $this->ocfilter->params->special('weight')->key(),
          'source' => $this->ocfilter->params->source('special')->id(),
          'name' => $this->language->get('text_weight'),
          'suffix' => '',        
          'status' => $this->ocfilter->config('special_weight'),
          'type' => 'slide_dual',
          'total_values' => null,
        ];    

        $results[] = [
          'filter_key' => $this->ocfilter->params->special('width')->key(),
          'source' => $this->ocfilter->params->source('special')->id(),
          'name' => $this->language->get('text_width'),
          'suffix' => '',        
          'status' => $this->ocfilter->config('special_width'),
          'type' => 'slide_dual',
          'total_values' => null,
        ]; 

        $results[] = [
          'filter_key' => $this->ocfilter->params->special('height')->key(),
          'source' => $this->ocfilter->params->source('special')->id(),
          'name' => $this->language->get('text_height'),
          'suffix' => '',        
          'status' => $this->ocfilter->config('special_height'),
          'type' => 'slide_dual',
          'total_values' => null,
        ]; 

        $results[] = [
          'filter_key' => $this->ocfilter->params->special('length')->key(),
          'source' => $this->ocfilter->params->source('special')->id(),
          'name' => $this->language->get('text_length'),
          'suffix' => '',        
          'status' => $this->ocfilter->config('special_length'),
          'type' => 'slide_dual',
          'total_values' => null,
        ];  
      }
    }
     
    // Regular
    $results = array_merge($results, $this->model_extension_module_ocfilter_filter->getFilters([ 
      'filter_category_id' => $filter_category_id, 
      'filter_ignore_slide' => $filter_ignore_slide, 
    ]));    
     
    // Format filters 
    $data['filters'] = [];

    foreach ($results as $filter) {      
      $filter_data = [
        'filter_key' => $filter['filter_key'],
        'source' => $filter['source'],
        'source_name' => $this->ocfilter->params->source($filter['source'])->name(),
        'name' => strip_tags(html_entity_decode($filter['name'], ENT_QUOTES, 'UTF-8')),
        'suffix' => strip_tags(html_entity_decode($filter['suffix'], ENT_QUOTES, 'UTF-8')),
        'type' => $filter['type'],
        'status' => $filter['status'],
        'selected' => isset($selected[$filter['filter_key']]),
        'selected_all' => (isset($selected[$filter['filter_key']][0]) && !array_sum(array_map('abs', $selected[$filter['filter_key']][0]))),
        'group' => isset($selected[$filter['filter_key']]['group']),
        'min' => '',
        'max' => '',
        'values' => [],
        'values_autocomplete' => (!($filter['type'] == 'slide' || $filter['type'] == 'slide_dual') && $filter['total_values'] > 100),
        'values_selected' => [],
        'total_values' => $filter['total_values'],
        'href' => $this->url->link('extension/module/ocfilter/filter/edit', $this->ocfilter->admin->getToken(true) . '&filter_key=' . $filter['filter_key']),
      ];

      if ($filter_data['selected'] && ($filter['type'] == 'slide' || $filter['type'] == 'slide_dual')) {
        $selected_value = array_shift($selected[$filter['filter_key']]);

        $filter_data['min'] = (strlen($selected_value[0]) > 0 ? (float)$selected_value[0] : '');
        $filter_data['max'] = (strlen($selected_value[1]) > 0 ? (float)$selected_value[1] : '');
      } else if (!$filter_data['values_autocomplete']) {
        if ($this->ocfilter->params->key($filter['filter_key'])->is('manufacturer')) {
          $values = $this->model_catalog_manufacturer->getManufacturers();
          
          foreach ($values as $value) {
            $filter_data['values'][] = [
              'value_id' => $value['manufacturer_id'],
              'name' => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8')),
              'selected' => isset($selected[$filter['filter_key']][$value['manufacturer_id']])
            ];     
          }    
        } else if ($this->ocfilter->params->key($filter['filter_key'])->is('stock')) {
          if ($this->ocfilter->config('stock_status_method') == 'stock_status_id') {
            $values = $this->model_localisation_stock_status->getStockStatuses();
            
            foreach ($values as $value) {
              $filter_value = [
                'value_id' => $value['stock_status_id'],
                'name' => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8')),
                'selected' => isset($selected[$filter['filter_key']][$value['stock_status_id']])
              ];

              $filter_data['values'][] = $filter_value;

              if ($filter_value['selected']) {
                $filter_data['values_selected'][] = $filter_value;
              }
            }              
          } else {
            $filter_value = [
              'value_id' => 2,
              'name' => $this->language->get('text_in_stock'),
              'selected' => isset($selected[$filter['filter_key']][2])
            ]; 
            
            $filter_data['values'][] = $filter_value;
            
            if ($filter_value['selected']) {
              $filter_data['values_selected'][] = $filter_value;
            }            
            
            if ($this->ocfilter->config('stock_out_value')) {
              $filter_value = [
                'value_id' => 1,
                'name' => $this->language->get('text_out_of_stock'),
                'selected' => isset($selected[$filter['filter_key']][1])
              ]; 
              
              $filter_data['values'][] = $filter_value;
              
              if ($filter_value['selected']) {
                $filter_data['values_selected'][] = $filter_value;
              }
            }            
          }
        } else if ($this->ocfilter->params->key($filter['filter_key'])->is('discount')) { // discount
          $filter_value = [
            'value_id' => 1,
            'name' => $this->language->get('text_discount_only'),
            'selected' => isset($selected[$filter['filter_key']][1])
          ]; 
          
          $filter_data['values'][] = $filter_value;
          
          if ($filter_value['selected']) {
            $filter_data['values_selected'][] = $filter_value;
          }
        } else if ($this->ocfilter->params->key($filter['filter_key'])->is('newest')) { // newest
          $filter_value = [
            'value_id' => 1,
            'name' => $this->language->get('text_newest_only'),
            'selected' => isset($selected[$filter['filter_key']][1])
          ]; 
          
          $filter_data['values'][] = $filter_value;
          
          if ($filter_value['selected']) {
            $filter_data['values_selected'][] = $filter_value;
          }          
        } else {
          $values = $this->model_extension_module_ocfilter_filter->getFilterValues([ 'filter_key' => $filter['filter_key'] ]);
          
          foreach ($values as $value) {
            $filter_data['values'][] = [
              'value_id' => $value['value_id'],
              'name' => strip_tags(html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8')),
              'selected' => isset($selected[$filter['filter_key']][$value['value_id']])
            ];
          }             
        }                       
      }
      
      if ($filter_data['selected']) {
        if ($this->ocfilter->params->key($filter['filter_key'])->is('manufacturer')) {
          foreach ($selected[$filter['filter_key']] as $value_id => $slide_value) {
            if ($value_id > 0) {
              $value_info = $this->model_catalog_manufacturer->getManufacturer($value_id);
              
              if ($value_info) {
                $filter_data['values_selected'][] = [
                  'value_id' => $value_info['manufacturer_id'],
                  'name' => strip_tags(html_entity_decode($value_info['name'], ENT_QUOTES, 'UTF-8')),
                ];
              } 
            }
          }    
        } else if ($filter['source'] != $this->ocfilter->params->source('special')->id()) {
          foreach ($selected[$filter['filter_key']] as $value_id => $slide_value) {
            if ($value_id > 0) {
              $value_info = $this->model_extension_module_ocfilter_filter->getFilterValue($value_id, $filter['source']);
              
              if ($value_info) {
                $filter_data['values_selected'][] = [
                  'value_id' => $value_info['value_id'],
                  'name' => strip_tags(html_entity_decode($value_info['name'], ENT_QUOTES, 'UTF-8')),
                ];
              } 
            }
          }    
        }
      }

      $data['filters'][] = $filter_data;
    }
    
    $source_special = $this->ocfilter->params->source('special')->id();
    
    uasort($data['filters'], function($a, $b) use ($source_special) {      
      if ($a['selected'] != $b['selected']) {
        return $a['selected'] < $b['selected'];
      }            
      
      if ($a['status'] != $b['status']) {
        return $a['status'] < $b['status'];
      }            
      
      if (($a['source'] == $source_special) != ($b['source'] == $source_special)) {        
        return $b['source'] < $a['source'];
      }
           
      return $b['name'] < $a['name'];
    });
    
    $data['page'] = isset($this->request->get['page_id']);
    $data['allow_group'] = isset($this->request->get['allow_group']);
    $data['product'] = isset($this->request->get['product_id']);
   
    $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/filter_relation_form', $data, true);
  }  
  
  public function autocompleteFilters() {
    $json = [];

    $this->load->language('extension/module/ocfilter/filter');

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('extension/module/ocfilter/filter');

      if (!empty($this->request->get['filter_status'])) {
        $filter_status = $this->request->get['filter_status'];
      } else {
        $filter_status = null;
      }
      
      if (!empty($this->request->get['filter_category_id'])) {
        $filter_category_id = $this->request->get['filter_category_id'];
      } else {
        $filter_category_id = null;
      }      

      $filter_data = [
        'autocomplete' => true,
        'filter_name' => $this->request->get['filter_name'],
        'filter_category_id' => $filter_category_id,
        'filter_status' => $filter_status,
        'start' => 0,
        'limit' => 15
      ];

      $results = $this->model_extension_module_ocfilter_filter->getFilters($filter_data);

      foreach ($results as $result) {
        $category = '';
        
        if (is_null($filter_category_id)) {
          $category_info = $this->model_extension_module_ocfilter_filter->getFilterCategory($result['filter_key']);
          
          if ($category_info) {
            if ($category_info['category_id'] > 0) {
              $category = strip_tags(html_entity_decode($category_info['name'], ENT_QUOTES, 'UTF-8'));
            } else {
              $category = $this->language->get('text_all');
            }         
          } else {
            $category = $this->language->get('text_without_category');
          }          
        }

        $json[] = [
          'filter_key' => $result['filter_key'],
          'filter_id' => $result['filter_id'],
          'source' => $result['source'],
          'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          'category' => $category,
        ];
      }
    }

    $this->ocfilter->opencart->responseJSON($json);
  }
  
  public function autocompleteValues() {
    $json = [];

    if (isset($this->request->get['filter_name'])) {
      $this->load->model('extension/module/ocfilter/filter');

      if (isset($this->request->get['filter_key'])) {
        $filter_key = $this->request->get['filter_key'];
      } else {
        $filter_key = 0;
      }

      $filter_data = [
        'autocomplete' => true,
        'filter_name' => $this->request->get['filter_name'],
        'filter_key' => $filter_key,
        'start' => 0,
        'limit' => 15
      ];
      
      if ($this->request->get['filter_name']) {
        $filter_data['limit'] = 25;
      }

      if ($this->ocfilter->params->key($filter_key)->is('manufacturer')) {       
        $results = $this->model_extension_module_ocfilter_filter->getManufacturers($filter_data);

        foreach ($results as $result) {
          $json[] = [
            'value_id' => $result['manufacturer_id'],
            'filter_key' => $filter_key,
            'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          ];
        }  
      } else {
        $results = $this->model_extension_module_ocfilter_filter->getFilterValues($filter_data);

        foreach ($results as $result) {
          $json[] = [
            'filter_key' => $result['filter_key'],
            'value_id' => $result['value_id'],
            'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
          ];
        }        
      }
    }

    $this->ocfilter->opencart->responseJSON($json);
  }  
  
  public function modalCategory() {
    $data = $this->load->language('extension/module/ocfilter/filter');

    if (isset($this->request->get['target'])) {
      $this->load->model('extension/module/ocfilter/filter');

      $data['selected'] = [];

      if (isset($this->request->get['filter_key'])) {
        $results = $this->model_extension_module_ocfilter_filter->getFilterCategories($this->request->get['filter_key']);

        if ($results) {
          $data['selected'] = array_keys($results);
        }
      }

  		 $data['categories'] = $this->model_extension_module_ocfilter_filter->getCategories();

      $data['target'] = $this->request->get['target'];

      $this->ocfilter->opencart->responseTemplate('extension/module/ocfilter/filter_form/category_relation_form', $data, true);
    }
  }  
}