<?php

namespace OCFilter;

class Admin extends Factory {
  private $controller_params = array();
  private $controller_entity = array();
  private $controller_entity_config = false;

  public function setControllerParams($params) {
    $this->controller_params = $params;
  }
  
  public function getControllerParams() {
    return $this->controller_params;
  }  
  
  public function setControllerEntity($entity) {
    $this->controller_entity = $entity;
  }
  
  public function setControllerEntityConfig($is_config) {
    $this->controller_entity_config = $is_config;
  }  
  
  public function getControllerEntity() {
    return $this->controller_entity;
  }    
  
  public function getURL() {
    $ignore = func_get_args();

    $url = array();
   
    $url[$this->getTokenIndex()] = $this->getToken();

    foreach ($this->controller_params as $key => $default) {
      if (isset($this->opencart->request->get[$key])) {
        if (is_numeric($this->opencart->request->get[$key])) {
          $url[$key] = (int)$this->opencart->request->get[$key];
        } else {
          $url[$key] = $this->opencart->request->get[$key]; 
        }        
      } 
    }

    $url = array_diff_key($url, array_fill_keys($ignore, ''));

    if ($url) {
      return '&' . http_build_query($url);
    } else {
      return '';
    }
  }    
  
  public function getEntityValue($key, $default = 0) {
    if (isset($this->opencart->request->post[$key])) {
      return $this->opencart->request->post[$key];
    } else if (!$this->controller_entity_config && isset($this->controller_entity[$key])) {
      return $this->controller_entity[$key];
    } else if ($this->controller_entity_config) {
      if ($this->opencart->request->server['REQUEST_METHOD'] == 'POST') {
        return (is_array($default) ? [] : '');
      } else if (!is_null($this->config($key))) {
        return $this->config($key);
      }            
    }
    
    return $default;
  }    

  public function getToken($param = false) {
    $index = $this->getTokenIndex();
    
    if ($index) {
      return ($param ? $index . '=' : '') . $this->opencart->session->data[$index];
    }
    
    return '';
  }
  
  public function getTokenIndex() {
    if ($this->opencart->version >= 30 && isset($this->opencart->session->data['user_token'])) {
      return 'user_token';
    } else if (isset($this->opencart->session->data['token'])) {
      return 'token';
    }
    
    return '';
  }
  
  public function getBoolControl($language) {
    return function($name, $status, $text_mode = 'e/d') use($language) {
      $data = array();

      if ($text_mode == 'e/d') {
        $data['text_true'] = $language['text_enabled'];
        $data['text_false'] = $language['text_disabled'];
      } else if ($text_mode == 'y/n') {
        $data['text_true'] = $language['text_yes'];
        $data['text_false'] = $language['text_no'];
      }

      $data['name'] = $name;
      $data['true'] = $status;

      echo $this->opencart->renderTemplate('extension/module/ocfilter/control/bool_button', $data);
    };
  }  

  public function getSortOrderControl($language) {
    $data = $language;

    return function($name, $sort_order) use($data) {
      $data['name'] = $name;
      $data['sort_order'] = $sort_order;
      
      echo $this->opencart->renderTemplate('extension/module/ocfilter/control/sort_order', $data);
    };
  }    
}