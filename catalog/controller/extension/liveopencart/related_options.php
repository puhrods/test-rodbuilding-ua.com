<?php
//  Related Options / Связанные опции 
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

class ControllerExtensionLiveopencartRelatedOptions extends Controller {
	
  public function getROFreeQuantities() {
    

		$this->load->model('extension/liveopencart/related_options');
    
    $product_id = isset($this->request->get['ro_product_id']) ? (int)$this->request->get['ro_product_id'] : 0;
		$options = isset($this->request->post['option']) ? $this->request->post['option'] : array();
		
    $json = array('quantity'=>false);
    
		$json = array_merge($json, $this->model_extension_liveopencart_related_options->getROFreeQuantitiesByOptions($product_id, $options) );
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
      

  }
  
  public function version() {
    \liveopencart\ext\ro::getInstance($this->registry);
    $this->response->setOutput( ''.$this->liveopencart_ext_ro->getExtensionCode().' '.$this->liveopencart_ext_ro->getCurrentVersion() );
  }
  
}
