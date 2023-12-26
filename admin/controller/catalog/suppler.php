<?php 
class ControllerCatalogSuppler extends Controller { 
	 private $error = array();
	
  	public function index() {
		$this->load->language('catalog/suppler');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/suppler');
				
    	$this->getList();
  	}
	
	public function getColumnName($table, $name) {
		$query = $this->db->query("SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name='" .$table."' AND  column_name = '". $this->db->escape($name) ."' AND table_schema ='" . DB_DATABASE . "'");
		
		$ok = 0;
		if (isset($query->row['COLUMN_NAME'])) $ok = 1;
		
		return $ok;
	}
  
  	public function insert() {
		$this->load->language('catalog/suppler');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/suppler');
						
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_suppler->addSuppler($this->request->post, 0);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
    		
    	$this->getForm();
  	} 
   
  	public function update() {
		$this->load->language('catalog/suppler');

    	$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('catalog/suppler');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_suppler->editSuppler($this->request->get['form_id'], $this->request->post);
					
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
        			
    	$this->getForm();
  	}   

  	public function delete() {

		$this->load->language('catalog/suppler');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('catalog/suppler');

    	if (isset($this->request->post['selected']) && $this->validateDelete() ) {
			foreach ($this->request->post['selected'] as $form_id) {
				$this->model_catalog_suppler->deleteSuppler($form_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
    	}
	
    	$this->getList();
  	}  
	
	public function base() {
		$this->load->language('catalog/suppler');
    	$this->document->setTitle($this->language->get('heading_base_title'));
		$this->load->model('catalog/suppler');		
		
		if (isset($this->request->post) and !empty($this->request->post['command'])) {	
		
			$err = $this->model_catalog_suppler->Action($this->request->post, $this->request->get['form_id']);
		
			switch ($err) {			
				case 0: $this->session->data['success'] = $this->language->get('text_base_success');					
					break;				
				case 1: $this->error['warning'] = $this->language->get('error_format');
					break;
				case 2: $this->error['warning'] = $this->language->get('error_vol');
					break;
				case 3: $this->error['warning'] = $this->language->get('error_open');
					break;
				case 4: $this->error['warning'] = $this->language->get('error_xml');
					break;
				case 5: $this->error['warning'] = $this->language->get('error_sos');
					break;
				case 6: $this->error['warning'] = $this->language->get('error_bad_sos');
					break;
				case 7: $this->error['warning'] = $this->language->get('error_rate');
					break;
				case 8: $this->error['warning'] = $this->language->get('error_cod');
					break;
				case 9: $this->error['warning'] = $this->language->get('error_pers');
					break;
				case 10: $this->error['warning'] = $this->language->get('error_schema_open');
					break;
				case 11: $this->error['warning'] = $this->language->get('error_dim');
					break;
				case 12: $this->error['warning'] = $this->language->get('error_related');
					break;
				case 13: $this->error['warning'] = $this->language->get('error_pic');
					break;
				case 14: $this->error['warning'] = $this->language->get('error_pars');
					break;
				case 15: $this->error['warning'] = $this->language->get('error_warranty');
					break;
				case 16: $this->error['warning'] = $this->language->get('error_item');
					break;
				case 17: $this->error['warning'] = $this->language->get('error_file'); // free
					break;
				case 18: $this->error['warning'] = $this->language->get('error_weight');
					break;
				case 19: $this->error['warning'] = $this->language->get('error_price');
					break;	
				case 20: $this->error['warning'] = $this->language->get('error_optsku');
					break;
				case 21: $this->error['warning'] = $this->language->get('error_data');
					break;
				case 22: $this->error['warning'] = $this->language->get('error_folder');
					break;
				case 23: $this->error['warning'] = $this->language->get('error_attribute2');
					break;
				case 24: $this->error['warning'] = $this->language->get('error_exception');
					break;			
				case 25: $this->error['warning'] = $this->language->get('error_cat');
					break;
				case 26: $this->error['warning'] = $this->language->get('error_uploads');
					break;
				case 27: $this->error['warning'] = $this->language->get('error_save');
					break;
				case 28: $this->error['warning'] = $this->language->get('error_ex');
					break;
				case 29: $this->error['warning'] = $this->language->get('error_empty'); // free
					break;	
				case 30: $this->error['warning'] = $this->language->get('error_category');
					break;
				case 31: $this->error['warning'] = $this->language->get('error_con');				
					break;
				case 32: $this->error['warning'] = $this->language->get('error_attribute1');				
					break;		
				case 33: $this->error['warning'] = $this->language->get('error_set_category');				
					break;
				case 34: $this->error['warning'] = $this->language->get('error_create_folder');				
					break;		
				case 35: $this->error['warning'] = $this->language->get('rename');				
					break;
				case 36: $this->error['warning'] = $this->language->get('error_filter');				
					break;
				case 37: $this->error['warning'] = $this->language->get('error_fields'); // free				
					break;
				case 38: $this->error['warning'] = $this->language->get('error_field_attribute');				
					break;
				case 39: $this->error['warning'] = $this->language->get('error_write_form');	
					break;
				case 40: $this->error['warning'] = $this->language->get('error_no_option');				
					break;
				case 41: $this->error['warning'] = $this->language->get('error_no_opt_val');				
					break;
				case 42: $this->error['warning'] = $this->language->get('error_88'); // free				
					break;
				case 43: $this->error['warning'] = $this->language->get('error_excel');		
					break;
				
			}		
			$this->getList();
			
		} else $this->getBaseForm();		
  	} 

	public function getBaseForm() {
		$this->load->language('catalog/suppler');

		$data['heading_base_title'] = $this->language->get('heading_base_title');
		$data['entry_restore'] = $this->language->get('entry_restore');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['button_start'] = $this->language->get('button_start');
		$data['button_base'] = $this->language->get('button_base');
		$data['tab_general'] = $this->language->get('tab_general');		

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
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => ' :: '
   		);
	
		$data['start'] = $this->url->link('catalog/suppler/start', 'user_token=' . $this->session->data['user_token']. '&form_id=' . $this->request->get['form_id'], true);
		
		$this->template = 'catalog/suppler_base.twig';
		$this->children = array(
			'common/header',
			'common/footer',			
		);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('catalog/suppler_base', $data));
		
	}
	
	public function scron() {
		$this->load->language('catalog/suppler');
    	$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('catalog/suppler');
		
    	$err = $this->model_catalog_suppler->StartCron($this->request->get['form_id']);
		
		$this->getList();
	}
	
	public function cstop() {
		$this->load->language('catalog/suppler');
    	$this->document->setTitle($this->language->get('heading_base_title'));
		$this->load->model('catalog/suppler');	
		$url = '';
		$wait = 0;
		$wait = $this->model_catalog_suppler->CronStop();
		if ($wait) $this->error['warning'] = $this->language->get('text_c_wait');
			
		$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}

	public function ccontinue() {
		$this->load->language('catalog/suppler');
    	$this->document->setTitle($this->language->get('heading_base_title'));
		$this->load->model('catalog/suppler');		
		$url = '';
		$this->model_catalog_suppler->ccontinue();			
		
		$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
	
	public function cstart() {
		$this->load->language('catalog/suppler');
    	$this->document->setTitle($this->language->get('heading_base_title'));
		$this->load->model('catalog/suppler');		
		$url = '';
		$this->model_catalog_suppler->cstart();			
	
		$this->response->redirect($this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true));
	}
	
	public function cronAction($param) {
		$this->load->language('catalog/suppler');    	
		$this->load->model('catalog/suppler');		
			
		$err = $this->model_catalog_suppler->Action($param, $param['form_id']);
		
		$_SESSION["e-r-r"] = $err;
	}	
	
	public function cronLoadfile($param) {
		$this->load->language('catalog/suppler');		
		$this->load->model('catalog/suppler');
		
		if (!empty($param['pr_name']) and empty($param['link'])) {			
			$file_name = $param['pr_name'];			
			$file_tmp = "./uploads/" . $param['pr_name'];
		} else {
			$file_name = $param['form_id'] . '.' . $param['ext'];
			$file_tmp = "./uploads/" . $param['form_id'] . '.' . $param['ext'];
		}	
	
		$err = $this->model_catalog_suppler->loadfile($file_tmp, $file_name, $param['form_id']);
		
		$_SESSION["e-r-r"] = $err;
	}
	
	public function onoff() {
		$this->load->language('catalog/suppler');
		$this->load->model('catalog/suppler');
		$id = $this->request->get['form_id'];
		$this->model_catalog_suppler->onoff($id);
		$this->getList();
	}
	
	public function load() {
		$this->load->language('catalog/suppler');
		$this->load->model('catalog/suppler');
		$file_tmp = 'usergio';
		$file_name = 'usergio.xml';
		$id = $this->request->get['form_id'];
		
		$err = $this->model_catalog_suppler->ftp($file_tmp, $file_name, $id);
	
		$errs = explode(";", $err);
		$err = $errs[0];
		if (!isset($errs[1])) $errs[1] = 0;
		if (!isset($errs[2])) $errs[2] = 0;
		
		$table = DB_PREFIX . "hpmodel_links";
		$tname = "parent_id";
	//	if ($this->getColumnName($table, $tname)) $this->load->controller('extension/module/hpmodel/update');
	
		switch ($err) {
				
		case 0: $this->session->data['success'] = $this->language->get('text_base_add') . $errs[1] . $this->language->get('text_base_up') . $errs[2] . '.  ' . $this->language->get('text_base_success');			
			break;	
		case 1: $this->error['warning'] = $this->language->get('error_format');
					break;
				case 2: $this->error['warning'] = $this->language->get('error_vol');
					break;
				case 3: $this->error['warning'] = $this->language->get('error_open');
					break;
				case 4: $this->error['warning'] = $this->language->get('error_xml');
					break;
				case 5: $this->error['warning'] = $this->language->get('error_sos');
					break;
				case 6: $this->error['warning'] = $this->language->get('error_bad_sos');
					break;
				case 7: $this->error['warning'] = $this->language->get('error_rate');
					break;
				case 8: $this->error['warning'] = $this->language->get('error_cod');
					break;
				case 9: $this->error['warning'] = $this->language->get('error_pers');
					break;
				case 10: $this->error['warning'] = $this->language->get('error_schema_open');
					break;
				case 11: $this->error['warning'] = $this->language->get('error_dim');
					break;
				case 12: $this->error['warning'] = $this->language->get('error_related');
					break;
				case 13: $this->error['warning'] = $this->language->get('error_pic');
					break;
				case 14: $this->error['warning'] = $this->language->get('error_pars');
					break;
				case 15: $this->error['warning'] = $this->language->get('error_warranty');
					break;
				case 16: $this->error['warning'] = $this->language->get('error_item');
					break;
				case 17: $this->error['warning'] = $this->language->get('error_file'); // free
					break;
				case 18: $this->error['warning'] = $this->language->get('error_weight');
					break;
				case 19: $this->error['warning'] = $this->language->get('error_price');
					break;	
				case 20: $this->error['warning'] = $this->language->get('error_optsku');
					break;
				case 21: $this->error['warning'] = $this->language->get('error_data');
					break;
				case 22: $this->error['warning'] = $this->language->get('error_folder');
					break;
				case 23: $this->error['warning'] = $this->language->get('error_attribute2');
					break;
				case 24: $this->error['warning'] = $this->language->get('error_exception');
					break;			
				case 25: $this->error['warning'] = $this->language->get('error_cat');
					break;
				case 26: $this->error['warning'] = $this->language->get('error_uploads');
					break;
				case 27: $this->error['warning'] = $this->language->get('error_save');
					break;
				case 28: $this->error['warning'] = $this->language->get('error_ex');
					break;
				case 29: $this->error['warning'] = $this->language->get('error_empty'); // free
					break;	
				case 30: $this->error['warning'] = $this->language->get('error_category');
					break;
				case 31: $this->error['warning'] = $this->language->get('error_con');				
					break;
				case 32: $this->error['warning'] = $this->language->get('error_attribute1');				
					break;		
				case 33: $this->error['warning'] = $this->language->get('error_set_category');				
					break;
				case 34: $this->error['warning'] = $this->language->get('error_create_folder');				
					break;		
				case 35: $this->error['warning'] = $this->language->get('rename');				
					break;
				case 36: $this->error['warning'] = $this->language->get('error_filter');				
					break;
				case 37: $this->error['warning'] = $this->language->get('error_fields'); // free				
					break;
				case 38: $this->error['warning'] = $this->language->get('error_field_attribute');				
					break;
				case 39: $this->error['warning'] = $this->language->get('error_write_form');	
					break;
				case 40: $this->error['warning'] = $this->language->get('error_no_option');				
					break;
				case 41: $this->error['warning'] = $this->language->get('error_no_opt_val');				
					break;
				case 42: $this->error['warning'] = $this->language->get('error_88'); // free				
					break;
				case 43: $this->error['warning'] = $this->language->get('error_excel');		
					break;
				case 44: $this->error['warning'] = $this->language->get('error_add_cat1') . $errs[1] . $this->language->get('error_add_cat2');
					break;	
		}
		$this->getList();
	}
	public function Start() {
			$this->load->language('catalog/suppler');
			$this->load->model('catalog/suppler');
									
			if ($this->request->server['REQUEST_METHOD'] == 'POST')  {
			if ((isset( $this->request->files['xmlfile'] )) && (is_uploaded_file($this->request->files['xmlfile']['tmp_name']))) {
				$file_name = $this->request->files['xmlfile']['name'];
				$file_tmp = $this->request->files['xmlfile']['tmp_name'];
				$err = $this->model_catalog_suppler->loadfile($file_tmp, $file_name, $this->request->get['form_id']);
				
				$errs = explode(";", $err);
				$err = $errs[0];
				if (!isset($errs[1])) $errs[1] = 0;
				if (!isset($errs[2])) $errs[2] = 0;
				
				$table = DB_PREFIX . "hpmodel_links";
				$tname = "parent_id";
		//		if ($this->getColumnName($table, $tname)) $this->load->controller('extension/module/hpmodel/update');
			
				switch ($err) {
				
				case 0: $this->session->data['success'] = $this->language->get('text_base_add') . $errs[1] . $this->language->get('text_base_up') . $errs[2] . '.  ' . $this->language->get('text_base_success');
					break;	
				case 1: $this->error['warning'] = $this->language->get('error_format');
					break;
				case 2: $this->error['warning'] = $this->language->get('error_vol');
					break;
				case 3: $this->error['warning'] = $this->language->get('error_open');
					break;
				case 4: $this->error['warning'] = $this->language->get('error_xml');
					break;
				case 5: $this->error['warning'] = $this->language->get('error_sos');
					break;
				case 6: $this->error['warning'] = $this->language->get('error_bad_sos');
					break;
				case 7: $this->error['warning'] = $this->language->get('error_rate');
					break;
				case 8: $this->error['warning'] = $this->language->get('error_cod');
					break;
				case 9: $this->error['warning'] = $this->language->get('error_pers');
					break;
				case 10: $this->error['warning'] = $this->language->get('error_schema_open');
					break;
				case 11: $this->error['warning'] = $this->language->get('error_dim');
					break;
				case 12: $this->error['warning'] = $this->language->get('error_related');
					break;
				case 13: $this->error['warning'] = $this->language->get('error_pic');
					break;
				case 14: $this->error['warning'] = $this->language->get('error_pars');
					break;
				case 15: $this->error['warning'] = $this->language->get('error_warranty');
					break;
				case 16: $this->error['warning'] = $this->language->get('error_item');
					break;
				case 17: $this->error['warning'] = $this->language->get('error_file'); // free
					break;
				case 18: $this->error['warning'] = $this->language->get('error_weight');
					break;
				case 19: $this->error['warning'] = $this->language->get('error_price');
					break;	
				case 20: $this->error['warning'] = $this->language->get('error_optsku');
					break;
				case 21: $this->error['warning'] = $this->language->get('error_data');
					break;
				case 22: $this->error['warning'] = $this->language->get('error_folder');
					break;
				case 23: $this->error['warning'] = $this->language->get('error_attribute2');
					break;
				case 24: $this->error['warning'] = $this->language->get('error_exception');
					break;			
				case 25: $this->error['warning'] = $this->language->get('error_cat');
					break;
				case 26: $this->error['warning'] = $this->language->get('error_uploads');
					break;
				case 27: $this->error['warning'] = $this->language->get('error_save');
					break;
				case 28: $this->error['warning'] = $this->language->get('error_ex');
					break;
				case 29: $this->error['warning'] = $this->language->get('error_empty'); // free
					break;	
				case 30: $this->error['warning'] = $this->language->get('error_category');
					break;
				case 31: $this->error['warning'] = $this->language->get('error_con');				
					break;
				case 32: $this->error['warning'] = $this->language->get('error_attribute1');				
					break;		
				case 33: $this->error['warning'] = $this->language->get('error_set_category');				
					break;
				case 34: $this->error['warning'] = $this->language->get('error_create_folder');				
					break;		
				case 35: $this->error['warning'] = $this->language->get('rename');				
					break;
				case 36: $this->error['warning'] = $this->language->get('error_filter');				
					break;
				case 37: $this->error['warning'] = $this->language->get('error_fields'); // free				
					break;
				case 38: $this->error['warning'] = $this->language->get('error_field_attribute');				
					break;
				case 39: $this->error['warning'] = $this->language->get('error_write_form');	
					break;
				case 40: $this->error['warning'] = $this->language->get('error_no_option');				
					break;
				case 41: $this->error['warning'] = $this->language->get('error_no_opt_val');				
					break;
				case 42: $this->error['warning'] = $this->language->get('error_88'); // free				
					break;
				case 43: $this->error['warning'] = $this->language->get('error_excel');		
					break;
				case 44: $this->error['warning'] = $this->language->get('error_add_cat1') . $errs[1] . $this->language->get('error_add_cat2');
					break;	
				}
			}	
		}		
		$this->getList();
	}
   
 	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		$this->request->get['page'] = 1;
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		$this->load->model('catalog/suppler');
		$results = $this->model_catalog_suppler->createTables();

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true),
      		'separator' => ' :: '
   		);
	
	$data['cstart'] = $this->url->link('catalog/suppler/cstart', 'user_token=' . $this->session->data['user_token'] . $url, true);
	$data['ccontinue'] = $this->url->link('catalog/suppler/ccontinue', 'user_token=' . $this->session->data['user_token'] . $url, true);
	$data['cstop'] = $this->url->link('catalog/suppler/cstop', 'user_token=' . $this->session->data['user_token'] . $url, true);
	$data['insert'] = $this->url->link('catalog/suppler/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
	$data['delete'] = $this->url->link('catalog/suppler/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);	
				
		$data['supplers'] = array();	
		
		$suppler_total = $this->model_catalog_suppler->getTotalSupplers();
	
		$results = $this->model_catalog_suppler->getSupplers($order);
 
    	foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true),
				'load' => $this->url->link('catalog/suppler/load', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true),
				'onoff' => $this->url->link('catalog/suppler/onoff', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
						
			$data['supplers'][] = array(
				'form_id'		  => $result['form_id'],
				'suppler_id' 	  => $result['suppler_id'],
				'name'            => $result['name'],
				'sort_order'      => $result['sort_order'],
				'save_form'       => $result['save_form'],
				'report'          => $result['report'],
				'errors'          => $result['errors'],
				'on_off'          => $result['on_off'],
				'go'         	  => $result['go'],
				'csort'        	  => $result['csort'],
				'sos'         	  => $result['sos'],
				'text'		  	  => $result['text'],
				'text1'		  	  => $result['text1'],
				'text2'		  	  => $result['text2'],
				'flag'         	  => $result['flag'],
				'flag1'        	  => $result['flag1'],
				'dj'			  => $result['dj'],
				'djssf'			  => $result['djssf'],
				'dhissf'		  => $result['dhissf'],
				'ddmhissf'		  => $result['ddmhissf'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['form_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}	
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_scode'] = $this->language->get('column_scode');
		$data['column_action'] = $this->language->get('column_action');		
		$data['column_file'] = $this->language->get('column_file');
		$data['column_load'] = $this->language->get('column_load');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_cstart'] = $this->language->get('button_cstart');
		$data['button_ccontinue'] = $this->language->get('button_ccontinue');
		$data['button_cstop'] = $this->language->get('button_cstop');		
		$data['column_exec'] = $this->language->get('column_exec');
		$data['column_report'] = $this->language->get('column_report');
		$data['column_errors'] = $this->language->get('column_errors');
		$data['column_status'] = $this->language->get('column_status');
		$data['entry_c_on'] = $this->language->get('entry_c_on');
		$data['entry_c_off'] = $this->language->get('entry_c_off');
		$data['entry_c_today'] = $this->language->get('entry_c_today');
		$data['entry_c_yday'] = $this->language->get('entry_c_yday');
		$data['entry_c_work'] = $this->language->get('entry_c_work');
		$data['entry_c_wait'] = $this->language->get('entry_c_wait');
		$data['button_insert'] = $this->language->get('button_insert');
		$data['user_token'] = $this->session->data['user_token'];
		$data['text_confirm'] = $this->language->get('text_confirm');
 
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

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];			
		}
		
		$data['sort_name'] = $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $suppler_total;
		$pagination->page = 1;
		$pagination->limit = 100;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
			
		$data['pagination'] = '';

		$data['sort'] = $sort;
		$data['order'] = $order;

		$this->template = 'catalog/suppler_list.twig';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
				
		$this->response->setOutput($this->load->view('catalog/suppler_list', $data));
	}
	
  	private function getForm() {
		
    	$data['heading_title'] = $this->language->get('heading_title');
    	$data['error_suppler'] = $this->language->get('error_suppler');
		$data['text_browse'] = $this->language->get('text_browse');
		$data['text_clear'] = $this->language->get('text_clear');			
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_confirm1'] = $this->language->get('text_confirm1');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_left'] = $this->language->get('text_left');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_ended'] = $this->language->get('text_ended');
		$data['text_dc'] = $this->language->get('text_dc');		
		$data['text_ddesc0'] = $this->language->get('text_ddesc0');
		$data['text_ddesc1'] = $this->language->get('text_ddesc1');
		$data['text_ddesc2'] = $this->language->get('text_ddesc2');		
		$data['text_wse'] = $this->language->get('text_wse');
		$data['text_only'] = $this->language->get('text_only');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_left1'] = $this->language->get('text_left1');
		$data['text_right'] = $this->language->get('text_right');
		$data['text_right1'] = $this->language->get('text_right1');
		$data['tags_no'] = $this->language->get('tags_no');
		$data['tags_yes'] = $this->language->get('tags_yes');
		$data['text_status1'] = $this->language->get('text_status1');
		$data['text_status2'] = $this->language->get('text_status2');
		$data['text_status3'] = $this->language->get('text_status3');
		$data['text_status4'] = $this->language->get('text_status4');
		$data['text_status5'] = $this->language->get('text_status5');
		$data['text_status6'] = $this->language->get('text_status6');
		$data['text_status7'] = $this->language->get('text_status7');
		$data['text_status8'] = $this->language->get('text_status8');
		$data['text_myprice1'] = $this->language->get('text_myprice1');
		$data['text_myprice2'] = $this->language->get('text_myprice2');
		$data['text_myprice3'] = $this->language->get('text_myprice3');
		$data['text_myprice4'] = $this->language->get('text_myprice4');
		$data['text_myprice5'] = $this->language->get('text_myprice5');
		$data['text_coder'] = $this->language->get('text_coder');
		$data['text_model'] = $this->language->get('text_model');
		$data['text_model0'] = $this->language->get('text_model0');
		$data['text_model1'] = $this->language->get('text_model1');
		$data['text_model2'] = $this->language->get('text_model2');
		$data['text_model3'] = $this->language->get('text_model3');
		$data['text_model4'] = $this->language->get('text_model4');
		$data['text_donor'] = $this->language->get('text_donor');
		$data['text_code0'] = $this->language->get('text_code0');
		$data['text_code1'] = $this->language->get('text_code1');
		$data['text_code2'] = $this->language->get('text_code2');
		$data['text_code3'] = $this->language->get('text_code3');
		$data['text_code4'] = $this->language->get('text_code4');
		$data['text_code5'] = $this->language->get('text_code5');
		$data['text_code6'] = $this->language->get('text_code6');
		$data['text_code7'] = $this->language->get('text_code7');
		$data['text_updte1'] = $this->language->get('text_updte1');
		$data['text_updte2'] = $this->language->get('text_updte2');
		$data['text_updte3'] = $this->language->get('text_updte3');
		$data['text_updte4'] = $this->language->get('text_updte4');
		$data['text_updte5'] = $this->language->get('text_updte5');
		$data['text_updtee4'] = $this->language->get('text_updtee4');
		$data['text_updtee5'] = $this->language->get('text_updtee5');
		$data['text_updtee6'] = $this->language->get('text_updtee6');
		$data['text_updtee7'] = $this->language->get('text_updtee7');
		$data['text_settings'] = $this->language->get('text_settings');
		$data['text_all'] = $this->language->get('text_all');
		$data['text_act'] = $this->language->get('text_act');
		$data['text_math1'] = $this->language->get('text_math1');
		$data['text_math2'] = $this->language->get('text_math2');
		$data['text_math3'] = $this->language->get('text_math3');
		$data['text_math4'] = $this->language->get('text_math4');
		$data['text_math5'] = $this->language->get('text_math5');
		$data['text_math6'] = $this->language->get('text_math6');
		$data['text_math7'] = $this->language->get('text_math7');
		$data['text_math8'] = $this->language->get('text_math8');
		$data['text_math9'] = $this->language->get('text_math9');
		$data['text_math10'] = $this->language->get('text_math10');
		$data['text_refer0'] = $this->language->get('text_refer0');
		$data['text_refer1'] = $this->language->get('text_refer1');
		$data['text_zero0'] = $this->language->get('text_zero0');
		$data['text_zero1'] = $this->language->get('text_zero1');
		$data['text_zero2'] = $this->language->get('text_zero2');
		$data['text_warring'] = $this->language->get('text_warring');
		$data['text_sl0'] = $this->language->get('text_sl0');
		$data['text_sl2'] = $this->language->get('text_sl2');
		$data['text_sl3'] = $this->language->get('text_sl3');
		$data['text_sl4'] = $this->language->get('text_sl4');
		$data['text_sl5'] = $this->language->get('text_sl5');
		$data['text_sl8'] = $this->language->get('text_sl8');
		$data['text_slr'] = $this->language->get('text_slr');	
		$data['text_ad0'] = $this->language->get('text_ad0');
		$data['text_ad1'] = $this->language->get('text_ad1');
		$data['text_ad2'] = $this->language->get('text_ad2');
		$data['text_ad3'] = $this->language->get('text_ad3');
		$data['text_ad4'] = $this->language->get('text_ad4');
		$data['text_ad5'] = $this->language->get('text_ad5');
		$data['text_ad6'] = $this->language->get('text_ad6');
		$data['text_ad7'] = $this->language->get('text_ad7');
		$data['text_ad8'] = $this->language->get('text_ad8');
		$data['text_ad9'] = $this->language->get('text_ad9');
		$data['text_ad10'] = $this->language->get('text_ad10');
		$data['text_ad11'] = $this->language->get('text_ad11');
		$data['text_ad12'] = $this->language->get('text_ad12');
		$data['text_ad13'] = $this->language->get('text_ad13');
		$data['text_ad14'] = $this->language->get('text_ad14');
		$data['text_ad15'] = $this->language->get('text_ad15');
		$data['text_ad16'] = $this->language->get('text_ad16');
		$data['text_kmenu'] = $this->language->get('text_kmenu');
		$data['text_kmenu0'] = $this->language->get('text_kmenu0');
		$data['text_kmenu1'] = $this->language->get('text_kmenu1');
		$data['text_kmenu2'] = $this->language->get('text_kmenu2');
		$data['text_kmenu3'] = $this->language->get('text_kmenu3');
		$data['text_kmenu4'] = $this->language->get('text_kmenu4');
		$data['text_kmenu5'] = $this->language->get('text_kmenu5');
		$data['text_kmenu6'] = $this->language->get('text_kmenu6');
		$data['text_kmenu7'] = $this->language->get('text_kmenu7');
		$data['text_kmenu8'] = $this->language->get('text_kmenu8');
		$data['text_kmenu9'] = $this->language->get('text_kmenu9');
		$data['text_kmenu10'] = $this->language->get('text_kmenu10');
		$data['text_kmenu11'] = $this->language->get('text_kmenu11');
		$data['text_kmenu12'] = $this->language->get('text_kmenu12');
		$data['text_kmenu13'] = $this->language->get('text_kmenu13');
		$data['text_kmenu14'] = $this->language->get('text_kmenu14');
		$data['text_kmenu15'] = $this->language->get('text_kmenu15');
		$data['text_kmenu16'] = $this->language->get('text_kmenu16');
		$data['text_kmenu17'] = $this->language->get('text_kmenu17');
		$data['text_kmenu18'] = $this->language->get('text_kmenu18');
		$data['text_kmenu19'] = $this->language->get('text_kmenu19');
		$data['text_kmenu20'] = $this->language->get('text_kmenu20');
		$data['text_kmenu21'] = $this->language->get('text_kmenu21');
		$data['text_kmenu22'] = $this->language->get('text_kmenu22');
		$data['text_kmenu23'] = $this->language->get('text_kmenu23');
		$data['text_kmenu24'] = $this->language->get('text_kmenu24');
		$data['text_gbotton'] = $this->language->get('text_gbotton');
		$data['text_competitors'] = $this->language->get('text_competitors');
		$data['text_command0'] = $this->language->get('text_command0');
		$data['text_command1'] = $this->language->get('text_command1');
		$data['text_command2'] = $this->language->get('text_command2');
		$data['text_command3'] = $this->language->get('text_command3');
		$data['text_command4'] = $this->language->get('text_command4');
		$data['text_command5'] = $this->language->get('text_command5');
		$data['text_command6'] = $this->language->get('text_command6');
		$data['text_command7'] = $this->language->get('text_command7');
		$data['text_command8'] = $this->language->get('text_command8');
		$data['text_command9'] = $this->language->get('text_command9');
		$data['text_command10'] = $this->language->get('text_command10');
		$data['text_command11'] = $this->language->get('text_command11');
		$data['text_command12'] = $this->language->get('text_command12');
		$data['text_command13'] = $this->language->get('text_command13');
		$data['text_command14'] = $this->language->get('text_command14');
		$data['text_command15'] = $this->language->get('text_command15');
		$data['text_command16'] = $this->language->get('text_command16');
		$data['text_command17'] = $this->language->get('text_command17');
		$data['text_command18'] = $this->language->get('text_command18');
		$data['text_command19'] = $this->language->get('text_command19');
		$data['text_command20'] = $this->language->get('text_command20');
		$data['text_command21'] = $this->language->get('text_command21');
		$data['text_command22'] = $this->language->get('text_command22');
		$data['text_command23'] = $this->language->get('text_command23');
		$data['text_command24'] = $this->language->get('text_command24');
		$data['text_command25'] = $this->language->get('text_command25');
		$data['text_command26'] = $this->language->get('text_command26');
		$data['text_command27'] = $this->language->get('text_command27');
		$data['text_command28'] = $this->language->get('text_command28');
		$data['text_command29'] = $this->language->get('text_command29');
		$data['text_command30'] = $this->language->get('text_command30');
		$data['text_command31'] = $this->language->get('text_command31');
		$data['text_command32'] = $this->language->get('text_command32');
		$data['text_command33'] = $this->language->get('text_command33');
		$data['text_command34'] = $this->language->get('text_command34');
		$data['text_command35'] = $this->language->get('text_command35');
		$data['text_command36'] = $this->language->get('text_command36');
		$data['text_command37'] = $this->language->get('text_command37');
		$data['text_command38'] = $this->language->get('text_command38');
		$data['text_command39'] = $this->language->get('text_command39');
		$data['text_command40'] = $this->language->get('text_command40');
		$data['text_command41'] = $this->language->get('text_command41');
		$data['text_command42'] = $this->language->get('text_command42');
		$data['text_command43'] = $this->language->get('text_command43');
		$data['text_command44'] = $this->language->get('text_command44');
		$data['text_command45'] = $this->language->get('text_command45');
		$data['text_command46'] = $this->language->get('text_command46');
		$data['text_command47'] = $this->language->get('text_command47');
		$data['text_command48'] = $this->language->get('text_command48');
		$data['text_command49'] = $this->language->get('text_command49');
		$data['text_command50'] = $this->language->get('text_command50');
		$data['text_command51'] = $this->language->get('text_command51');
		$data['text_command52'] = $this->language->get('text_command52');
		$data['text_command53'] = $this->language->get('text_command53');
		$data['text_command54'] = $this->language->get('text_command54');
		$data['text_command55'] = $this->language->get('text_command55');
		$data['text_command56'] = $this->language->get('text_command56');
		$data['text_command57'] = $this->language->get('text_command57');
		$data['text_command58'] = $this->language->get('text_command58');
		$data['text_command59'] = $this->language->get('text_command59');
		$data['text_command60'] = $this->language->get('text_command60');
		$data['text_command61'] = $this->language->get('text_command61');
		$data['text_command62'] = $this->language->get('text_command62');
		$data['text_command63'] = $this->language->get('text_command63');
		$data['text_command64'] = $this->language->get('text_command64');
		$data['text_command65'] = $this->language->get('text_command65');
		$data['text_command66'] = $this->language->get('text_command66');
		$data['text_command67'] = $this->language->get('text_command67');
		$data['text_command68'] = $this->language->get('text_command68');
		$data['text_command69'] = $this->language->get('text_command69');
		$data['text_command70'] = $this->language->get('text_command70');
		$data['text_command71'] = $this->language->get('text_command71');
		$data['text_command72'] = $this->language->get('text_command72');
		$data['text_command73'] = $this->language->get('text_command73');
		$data['text_command74'] = $this->language->get('text_command74');
		$data['text_command75'] = $this->language->get('text_command75');
		$data['text_command76'] = $this->language->get('text_command76');
		$data['text_command77'] = $this->language->get('text_command77');
		$data['text_command78'] = $this->language->get('text_command78');
		$data['text_command79'] = $this->language->get('text_command79');
		$data['text_command80'] = $this->language->get('text_command80');
		$data['text_command81'] = $this->language->get('text_command81');
		$data['text_command82'] = $this->language->get('text_command82');
		$data['text_command83'] = $this->language->get('text_command83');
		$data['text_command84'] = $this->language->get('text_command84');
		$data['text_command85'] = $this->language->get('text_command85');
		$data['text_command86'] = $this->language->get('text_command86');
		$data['text_command87'] = $this->language->get('text_command87');
		$data['text_command88'] = $this->language->get('text_command88');
		$data['text_command89'] = $this->language->get('text_command89');
		$data['text_command90'] = $this->language->get('text_command90');
		$data['text_command91'] = $this->language->get('text_command91');
		$data['text_command92'] = $this->language->get('text_command92');
		$data['text_command93'] = $this->language->get('text_command93');
		$data['text_command94'] = $this->language->get('text_command94');
		$data['text_command95'] = $this->language->get('text_command95');
		$data['text_command96'] = $this->language->get('text_command96');
		$data['text_command97'] = $this->language->get('text_command97');
		$data['text_command98'] = $this->language->get('text_command98');
		$data['text_command99'] = $this->language->get('text_command99');
		$data['text_command100'] = $this->language->get('text_command100');
		$data['text_command101'] = $this->language->get('text_command101');
		$data['text_command102'] = $this->language->get('text_command102');
		$data['text_command103'] = $this->language->get('text_command103');
		$data['text_command104'] = $this->language->get('text_command104');
		$data['text_command105'] = $this->language->get('text_command105');		
		$data['text_command106'] = $this->language->get('text_command106');
		$data['text_command107'] = $this->language->get('text_command107');
		$data['text_command108'] = $this->language->get('text_command108');
		$data['text_command109'] = $this->language->get('text_command109');
		$data['text_command110'] = $this->language->get('text_command110');
		$data['text_command111'] = $this->language->get('text_command111');
		$data['text_command112'] = $this->language->get('text_command112');
		$data['text_command113'] = $this->language->get('text_command113');
		$data['text_command114'] = $this->language->get('text_command114');
		$data['text_command115'] = $this->language->get('text_command115');
		$data['text_command116'] = $this->language->get('text_command116');
		$data['text_command117'] = $this->language->get('text_command117');
		$data['text_command118'] = $this->language->get('text_command118');
		$data['text_command119'] = $this->language->get('text_command119');
		$data['text_command120'] = $this->language->get('text_command120');
		$data['text_command121'] = $this->language->get('text_command121');
		$data['text_command122'] = $this->language->get('text_command122');
		$data['text_command123'] = $this->language->get('text_command123');
		$data['text_command124'] = $this->language->get('text_command124');
		$data['text_command125'] = $this->language->get('text_command125');
		$data['text_command126'] = $this->language->get('text_command126');
		$data['text_command127'] = $this->language->get('text_command127');
		$data['text_command128'] = $this->language->get('text_command128');
		$data['text_command129'] = $this->language->get('text_command129');
		$data['text_command130'] = $this->language->get('text_command130');
		$data['text_command131'] = $this->language->get('text_command131');
		$data['text_command132'] = $this->language->get('text_command132');
		$data['text_command133'] = $this->language->get('text_command133');
		$data['text_command134'] = $this->language->get('text_command134');
		$data['text_command135'] = $this->language->get('text_command135');
		$data['text_command136'] = $this->language->get('text_command136');
		$data['text_command137'] = $this->language->get('text_command137');
		$data['text_command138'] = $this->language->get('text_command138');
		$data['text_command139'] = $this->language->get('text_command139');
		$data['text_command140'] = $this->language->get('text_command140');
		$data['text_command141'] = $this->language->get('text_command141');
		$data['text_command142'] = $this->language->get('text_command142');
		$data['text_command143'] = $this->language->get('text_command143');
		$data['text_command144'] = $this->language->get('text_command144');
		$data['text_command145'] = $this->language->get('text_command145');
		$data['text_command146'] = $this->language->get('text_command146');
		$data['text_command147'] = $this->language->get('text_command147');
		$data['text_command148'] = $this->language->get('text_command148');
		$data['text_command149'] = $this->language->get('text_command149');
		$data['text_command150'] = $this->language->get('text_command150');
		$data['text_command151'] = $this->language->get('text_command151');
		$data['text_command152'] = $this->language->get('text_command152');
		$data['text_command153'] = $this->language->get('text_command153');
		$data['text_command154'] = $this->language->get('text_command154');
		$data['text_command155'] = $this->language->get('text_command155');
		$data['text_command156'] = $this->language->get('text_command156');
		$data['text_command157'] = $this->language->get('text_command157');
		$data['text_command158'] = $this->language->get('text_command158');
		$data['text_command159'] = $this->language->get('text_command159');
		$data['text_command160'] = $this->language->get('text_command160');
		$data['text_command161'] = $this->language->get('text_command161');
		$data['text_command162'] = $this->language->get('text_command162');
		$data['text_command163'] = $this->language->get('text_command163');
		$data['text_command164'] = $this->language->get('text_command164');
		$data['text_command165'] = $this->language->get('text_command165');
		$data['text_command166'] = $this->language->get('text_command166');
		$data['text_command167'] = $this->language->get('text_command167');
		$data['text_command168'] = $this->language->get('text_command168');
		$data['text_command169'] = $this->language->get('text_command169');
		$data['text_command170'] = $this->language->get('text_command170');
		$data['text_command171'] = $this->language->get('text_command171');
		$data['text_command172'] = $this->language->get('text_command172');
		$data['text_command173'] = $this->language->get('text_command173');
		$data['text_command174'] = $this->language->get('text_command174');	
		$data['text_command175'] = $this->language->get('text_command175');
		$data['text_command176'] = $this->language->get('text_command176');
		$data['text_command177'] = $this->language->get('text_command177');	
		$data['text_command178'] = $this->language->get('text_command178');
		$data['text_command179'] = $this->language->get('text_command179');
		$data['text_command180'] = $this->language->get('text_command180');
		$data['text_command181'] = $this->language->get('text_command181');
		$data['text_command182'] = $this->language->get('text_command182');
		$data['text_command183'] = $this->language->get('text_command183');
		$data['text_command184'] = $this->language->get('text_command184');
		$data['text_command185'] = $this->language->get('text_command185');
		$data['text_command186'] = $this->language->get('text_command186');
		$data['text_command187'] = $this->language->get('text_command187');
		$data['text_command188'] = $this->language->get('text_command188');
		$data['text_command189'] = $this->language->get('text_command189');
		$data['text_command190'] = $this->language->get('text_command190');
		$data['text_command191'] = $this->language->get('text_command191');
		$data['text_command192'] = $this->language->get('text_command192');
		$data['text_command193'] = $this->language->get('text_command193');
		$data['text_command194'] = $this->language->get('text_command194');
		$data['text_command195'] = $this->language->get('text_command195');
		$data['text_command196'] = $this->language->get('text_command196');
		$data['text_command197'] = $this->language->get('text_command197');
		$data['text_command198'] = $this->language->get('text_command198');
		$data['text_command199'] = $this->language->get('text_command199');
		$data['text_command200'] = $this->language->get('text_command200');
		$data['text_command201'] = $this->language->get('text_command201');
		$data['text_command202'] = $this->language->get('text_command202');
		$data['text_command203'] = $this->language->get('text_command203');
		$data['text_command204'] = $this->language->get('text_command204');
		$data['text_command205'] = $this->language->get('text_command205');
		$data['text_command206'] = $this->language->get('text_command206');
		$data['text_command207'] = $this->language->get('text_command207');
		$data['text_command208'] = $this->language->get('text_command208');
		$data['text_command209'] = $this->language->get('text_command209');
		$data['text_command210'] = $this->language->get('text_command210');
		$data['text_command211'] = $this->language->get('text_command211');
		$data['text_command212'] = $this->language->get('text_command212');
		$data['text_command213'] = $this->language->get('text_command213');
		$data['text_command214'] = $this->language->get('text_command214');
		$data['text_command215'] = $this->language->get('text_command215');
		$data['text_command216'] = $this->language->get('text_command216');
		$data['text_command217'] = $this->language->get('text_command217');
		$data['text_command218'] = $this->language->get('text_command218');
		$data['text_command219'] = $this->language->get('text_command219');
		$data['text_command220'] = $this->language->get('text_command220');
		$data['text_command221'] = $this->language->get('text_command221');
		$data['text_command222'] = $this->language->get('text_command222');
		$data['text_command223'] = $this->language->get('text_command223');
		$data['text_command224'] = $this->language->get('text_command224');
		$data['text_command225'] = $this->language->get('text_command225');
		$data['text_command226'] = $this->language->get('text_command226');
		$data['text_command227'] = $this->language->get('text_command227');
		$data['text_command228'] = $this->language->get('text_command228');
		$data['text_command229'] = $this->language->get('text_command229');
		$data['text_command230'] = $this->language->get('text_command230');
		$data['text_command231'] = $this->language->get('text_command231');
		$data['text_command232'] = $this->language->get('text_command232');
		$data['text_command233'] = $this->language->get('text_command233');
		$data['text_command234'] = $this->language->get('text_command234');
		$data['text_command235'] = $this->language->get('text_command235');
		$data['text_command236'] = $this->language->get('text_command236');
		$data['text_command237'] = $this->language->get('text_command237');
		$data['text_command238'] = $this->language->get('text_command238');
		$data['text_command239'] = $this->language->get('text_command239');
		$data['text_command240'] = $this->language->get('text_command240');
		$data['text_command241'] = $this->language->get('text_command241');
		$data['text_command242'] = $this->language->get('text_command242');
		$data['text_command243'] = $this->language->get('text_command243');
		$data['text_command244'] = $this->language->get('text_command244');
		$data['text_command245'] = $this->language->get('text_command245');
		$data['text_command246'] = $this->language->get('text_command246');
		$data['text_command247'] = $this->language->get('text_command247');
		$data['text_command248'] = $this->language->get('text_command248');
		$data['text_command249'] = $this->language->get('text_command249');
		$data['text_command250'] = $this->language->get('text_command250');
		$data['text_command251'] = $this->language->get('text_command251');
		$data['text_command252'] = $this->language->get('text_command252');
		$data['text_command253'] = $this->language->get('text_command253');
		$data['text_command254'] = $this->language->get('text_command254');
		$data['text_command255'] = $this->language->get('text_command255');
		$data['text_command256'] = $this->language->get('text_command256');
		$data['text_command257'] = $this->language->get('text_command257');
		$data['text_command258'] = $this->language->get('text_command258');
		$data['text_command259'] = $this->language->get('text_command259');
		$data['text_command260'] = $this->language->get('text_command260');
		$data['text_command261'] = $this->language->get('text_command261');
		$data['text_command262'] = $this->language->get('text_command262');
		$data['text_command263'] = $this->language->get('text_command263');
		$data['text_command264'] = $this->language->get('text_command264');
		$data['text_command265'] = $this->language->get('text_command265');
		$data['text_command266'] = $this->language->get('text_command266');
		$data['text_command267'] = $this->language->get('text_command267');
		$data['text_command268'] = $this->language->get('text_command268');
		$data['text_command269'] = $this->language->get('text_command269');
		$data['text_command270'] = $this->language->get('text_command270');
		$data['text_command271'] = $this->language->get('text_command271');
		$data['text_command272'] = $this->language->get('text_command272');
		$data['text_command273'] = $this->language->get('text_command273');
		$data['text_command274'] = $this->language->get('text_command274');
		$data['text_command275'] = $this->language->get('text_command275');
		$data['text_command276'] = $this->language->get('text_command276');
		$data['text_command277'] = $this->language->get('text_command277');
		$data['text_command278'] = $this->language->get('text_command278');
		$data['text_command279'] = $this->language->get('text_command279');
		$data['text_command280'] = $this->language->get('text_command280');
		$data['text_command281'] = $this->language->get('text_command281');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_suppler_id'] = $this->language->get('entry_suppler_id');
		$data['entry_cod'] = $this->language->get('entry_cod');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
    	$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_seo_title'] = $this->language->get('entry_seo_title');
		$data['entry_seo_h1'] = $this->language->get('entry_seo_h1');
		$data['entry_tags'] = $this->language->get('entry_tags');
		$data['entry_rate'] = $this->language->get('entry_rate');
		$data['entry_ratep'] = $this->language->get('entry_ratep');
		$data['entry_ratek'] = $this->language->get('entry_ratek');
		$data['entry_ad'] = $this->language->get('entry_ad');
		$data['entry_parent'] = $this->language->get('entry_parent');
		$data['entry_parent0'] = $this->language->get('entry_parent0');
		$data['entry_parent1'] = $this->language->get('entry_parent1');
		$data['entry_parent2'] = $this->language->get('entry_parent2');
		$data['entry_parent3'] = $this->language->get('entry_parent3');
		$data['entry_parent4'] = $this->language->get('entry_parent4');
		$data['entry_parent5'] = $this->language->get('entry_parent5');
		$data['entry_parent6'] = $this->language->get('entry_parent6');
		$data['entry_hide'] = $this->language->get('entry_hide');
		$data['entry_onoff'] = $this->language->get('entry_onoff');
		$data['entry_cat_ext'] = $this->language->get('entry_cat_ext');
		$data['entry_cat_int'] = $this->language->get('entry_cat_int');
		$data['entry_cat_plus'] = $this->language->get('entry_cat_plus');
		$data['entry_item'] = $this->language->get('entry_item');
		$data['entry_cat'] = $this->language->get('entry_cat');
		$data['entry_qu'] = $this->language->get('entry_qu');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_desc'] = $this->language->get('entry_desc');
		$data['entry_isattribute'] = $this->language->get('entry_isattribute');
		$data['entry_newphoto'] = $this->language->get('entry_newphoto');
		$data['entry_pic_ext'] = $this->language->get('entry_pic_ext');
		$data['entry_pic_int'] = $this->language->get('entry_pic_int');		
		$data['entry_manuf'] = $this->language->get('entry_manuf');
		$data['entry_warranty'] = $this->language->get('entry_warranty');
		$data['entry_attrib'] = $this->language->get('entry_attrib');
		$data['entry_attribute'] = $this->language->get('entry_attribute');
		$data['entry_foto'] = $this->language->get('entry_foto');
		$data['entry_opt'] = $this->language->get('entry_opt');
		$data['entry_option'] = $this->language->get('entry_option');
		$data['entry_art'] = $this->language->get('entry_art');
		$data['entry_ko'] = $this->language->get('entry_ko');
		$data['entry_pr'] = $this->language->get('entry_pr');
		$data['entry_po'] = $this->language->get('entry_po');	
		$data['entry_we'] = $this->language->get('entry_we');
		$data['entry_option_required'] = $this->language->get('entry_option_required');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_updte'] = $this->language->get('entry_updte');
		$data['entry_pmanuf'] = $this->language->get('entry_pmanuf');
		$data['entry_umanuf'] = $this->language->get('entry_umanuf');
		$data['entry_order'] = $this->language->get('entry_order');
		$data['entry_spec'] = $this->language->get('entry_spec');
		$data['entry_upurl'] = $this->language->get('entry_upurl');
		$data['entry_ref'] = $this->language->get('entry_ref');
		$data['entry_target'] = $this->language->get('entry_target');
		$data['entry_target0'] = $this->language->get('entry_target0');
		$data['entry_target1'] = $this->language->get('entry_target1');
		$data['entry_target2'] = $this->language->get('entry_target2');
		$data['entry_target3'] = $this->language->get('entry_target3');
		$data['entry_target4'] = $this->language->get('entry_target4');
		$data['entry_target5'] = $this->language->get('entry_target5');
		$data['entry_target6'] = $this->language->get('entry_target6');
		$data['entry_target7'] = $this->language->get('entry_target7');
		$data['entry_target8'] = $this->language->get('entry_target8');
		$data['entry_target9'] = $this->language->get('entry_target9');
		$data['entry_target10'] = $this->language->get('entry_target10');
		$data['entry_target11'] = $this->language->get('entry_target11');
		$data['entry_target12'] = $this->language->get('entry_target12');
		$data['entry_target13'] = $this->language->get('entry_target13');
		$data['entry_target14'] = $this->language->get('entry_target14');
		$data['entry_target15'] = $this->language->get('entry_target15');
		$data['entry_target16'] = $this->language->get('entry_target16');
		$data['entry_target17'] = $this->language->get('entry_target17');
		$data['entry_target18'] = $this->language->get('entry_target18');
		$data['entry_target19'] = $this->language->get('entry_target19');
		$data['entry_target20'] = $this->language->get('entry_target20');
		$data['entry_target21'] = $this->language->get('entry_target21');
		$data['entry_addattr'] = $this->language->get('entry_addattr');
		$data['entry_exsame'] = $this->language->get('entry_exsame');
		$data['entry_sku2'] = $this->language->get('entry_sku2');
		$data['entry_point'] = $this->language->get('entry_point');
		$data['entry_placep'] = $this->language->get('entry_placep');
		$data['entry_placep0'] = $this->language->get('entry_placep0');
		$data['entry_placep1'] = $this->language->get('entry_placep1');
		$data['entry_placep2'] = $this->language->get('entry_placep2');
		$data['entry_placep3'] = $this->language->get('entry_placep3');
		$data['entry_placep4'] = $this->language->get('entry_placep4');
		$data['entry_placep5'] = $this->language->get('entry_placep5');
		$data['entry_placep6'] = $this->language->get('entry_placep6');
		$data['entry_placep7'] = $this->language->get('entry_placep7');
		$data['entry_placep8'] = $this->language->get('entry_placep8');
		$data['entry_placep9'] = $this->language->get('entry_placep9');
		$data['entry_placep10'] = $this->language->get('entry_placep10');
		$data['entry_placep11'] = $this->language->get('entry_placep11');
		$data['entry_point1'] = $this->language->get('entry_point1');
		$data['entry_place'] = $this->language->get('entry_place');
		$data['entry_pars'] = $this->language->get('entry_pars');
		$data['entry_pars1'] = $this->language->get('entry_pars1');
		$data['entry_catcreate'] = $this->language->get('entry_catcreate');
		$data['entry_stay'] = $this->language->get('entry_stay');
		$data['entry_set_status'] = $this->language->get('entry_set_status');
		$data['entry_mycat'] = $this->language->get('entry_mycat');
		$data['entry_myqu'] = $this->language->get('entry_myqu');
		$data['entry_myprice'] = $this->language->get('entry_myprice');
		$data['entry_mydescrip'] = $this->language->get('entry_mydescrip');
		$data['entry_myphoto'] = $this->language->get('entry_myphoto');
		$data['entry_mymanuf'] = $this->language->get('entry_mymanuf');
		$data['entry_mymark'] = $this->language->get('entry_mymark');
		$data['entry_def_word'] = $this->language->get('entry_def_word');
		$data['entry_actcat'] = $this->language->get('entry_actcat');
		$data['entry_zactcat'] = $this->language->get('entry_zactcat');
		$data['entry_kol_status'] = $this->language->get('entry_kol_status');
		$data['entry_status_formula'] = $this->language->get('entry_status_formula');
		$data['entry_dimension'] = $this->language->get('entry_dimension');
		$data['entry_actmanuf'] = $this->language->get('entry_actmanuf');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_cod_from'] = $this->language->get('entry_cod_from');
		$data['entry_cod_to'] = $this->language->get('entry_cod_to');
		$data['entry_price_from'] = $this->language->get('entry_price_from');
		$data['entry_price_to'] = $this->language->get('entry_price_to');
		$data['entry_first'] = $this->language->get('entry_first');
		$data['entry_last'] = $this->language->get('entry_last');		
		$data['entry_actsuppler'] = $this->language->get('entry_actsuppler');
		$data['entry_actmult'] = $this->language->get('entry_actmult');
		$data['entry_attribut'] = $this->language->get('entry_attribut');
		$data['entry_noattribut'] = $this->language->get('entry_noattribut');
		$data['entry_inattribut'] = $this->language->get('entry_inattribut');
		$data['entry_inoption'] = $this->language->get('entry_inoption');
		$data['entry_isoptvalue'] = $this->language->get('entry_isoptvalue');		
		$data['entry_isphoto'] = $this->language->get('entry_isphoto');
		$data['entry_isvalue'] = $this->language->get('entry_isvalue');
		$data['entry_isoptions'] = $this->language->get('entry_isoptions');
		$data['entry_isoptions3'] = $this->language->get('entry_isoptions3');
		$data['entry_isoptions4'] = $this->language->get('entry_isoptions4');
		$data['entry_spec_disc'] = $this->language->get('entry_spec_disc');
		$data['entry_spec_disc0'] = $this->language->get('entry_spec_disc0');
		$data['entry_spec_disc1'] = $this->language->get('entry_spec_disc1');
		$data['entry_spec_disc2'] = $this->language->get('entry_spec_disc2');
		$data['entry_spec_disc3'] = $this->language->get('entry_spec_disc3');
		$data['entry_spec_disc4'] = $this->language->get('entry_spec_disc4');
		$data['entry_status_num'] = $this->language->get('entry_status_num');
		$data['entry_any'] = $this->language->get('entry_any');
		$data['entry_find'] = $this->language->get('entry_find');
		$data['entry_change'] = $this->language->get('entry_change');
		$data['entry_all'] = $this->language->get('entry_all');
		$data['entry_kol'] = $this->language->get('entry_kol');
		$data['entry_cheap'] = $this->language->get('entry_cheap');
		$data['entry_addopt'] = $this->language->get('entry_addopt');
		$data['entry_addseo'] = $this->language->get('entry_addseo');
		$data['entry_addseo0'] = $this->language->get('entry_addseo0');
		$data['entry_addseo1'] = $this->language->get('entry_addseo1');
		$data['entry_addseo2'] = $this->language->get('entry_addseo2');
		$data['entry_importseo'] = $this->language->get('entry_importseo');
		$data['entry_upname'] = $this->language->get('entry_upname');
		$data['entry_upattr'] = $this->language->get('entry_upattr');
		$data['entry_upattr0'] = $this->language->get('entry_upattr0');
		$data['entry_upattr1'] = $this->language->get('entry_upattr1');
		$data['entry_upattr2'] = $this->language->get('entry_upattr2');
		$data['entry_upattr3'] = $this->language->get('entry_upattr3');
		$data['entry_upattr4'] = $this->language->get('entry_upattr4');
		$data['entry_upattr5'] = $this->language->get('entry_upattr5');
		$data['entry_upattr6'] = $this->language->get('entry_upattr6');
		$data['entry_upopt'] = $this->language->get('entry_upopt');
		$data['entry_upopt0'] = $this->language->get('entry_upopt0');
		$data['entry_upopt1'] = $this->language->get('entry_upopt1');
		$data['entry_upopt2'] = $this->language->get('entry_upopt2');
		$data['entry_upopt3'] = $this->language->get('entry_upopt3');
		$data['entry_upopt4'] = $this->language->get('entry_upopt4');
		$data['entry_myplus'] = $this->language->get('entry_myplus');		
		$data['entry_cprice'] = $this->language->get('entry_cprice');
		$data['entry_minus'] = $this->language->get('entry_minus');
		$data['entry_chcode'] = $this->language->get('entry_chcode');
		$data['entry_newonly'] = $this->language->get('entry_newonly');
		$data['entry_off'] = $this->language->get('entry_off');
		$data['entry_joen'] = $this->language->get('entry_joen');
		$data['entry_site_nom'] = $this->language->get('entry_site_nom');
		$data['entry_site_ident'] = $this->language->get('entry_site_ident');
		$data['entry_site_param'] = $this->language->get('entry_site_param');
		$data['entry_site_point'] = $this->language->get('entry_site_point');
		$data['entry_site_math'] = $this->language->get('entry_site_math');
		$data['entry_onn'] = $this->language->get('entry_onn');
		$data['entry_refer'] = $this->language->get('entry_refer');
		$data['entry_disc'] = $this->language->get('entry_disc');
		$data['entry_offproduct'] = $this->language->get('entry_offproduct');
		$data['entry_offproduct0'] = $this->language->get('entry_offproduct0');
		$data['entry_offproduct1'] = $this->language->get('entry_offproduct1');
		$data['entry_offproduct2'] = $this->language->get('entry_offproduct2');
		$data['entry_descr'] = $this->language->get('entry_descr');
		$data['entry_plusopt'] = $this->language->get('entry_plusopt');
		$data['entry_addopt0'] = $this->language->get('entry_addopt0');
		$data['entry_addopt1'] = $this->language->get('entry_addopt1');
		$data['entry_addopt2'] = $this->language->get('entry_addopt2');
		$data['entry_upurl0'] = $this->language->get('entry_upurl0');
		$data['entry_upurl1'] = $this->language->get('entry_upurl1');
		$data['entry_upurl2'] = $this->language->get('entry_upurl2');
		$data['entry_newurl'] = $this->language->get('entry_newurl');
		$data['entry_newurl0'] = $this->language->get('entry_newurl0');
		$data['entry_newurl1'] = $this->language->get('entry_newurl1');
		$data['entry_newurl3'] = $this->language->get('entry_newurl3');
		$data['entry_fields'] = $this->language->get('entry_fields');
		$data['entry_data'] = $this->language->get('entry_data');
		$data['entry_seo_prod'] = $this->language->get('entry_seo_prod');		
		$data['entry_seo_title'] = $this->language->get('entry_seo_title');
		$data['entry_seo_desc'] = $this->language->get('entry_seo_desc');
		$data['entry_seo_cat_cat'] = $this->language->get('entry_seo_cat_cat');
		$data['entry_seo_description'] = $this->language->get('entry_seo_description');
		$data['entry_seo_manuf'] = $this->language->get('entry_seo_manuf');		
		$data['entry_seo_nb'] = $this->language->get('entry_seo_nb');
		$data['entry_seo_round'] = $this->language->get('entry_seo_round');
		$data['entry_seo_attribut'] = $this->language->get('entry_seo_attribut');
		$data['entry_seo_option'] = $this->language->get('entry_seo_option');		
		$data['entry_seo_price'] = $this->language->get('entry_seo_price');
		$data['entry_seo_name'] = $this->language->get('entry_seo_name');
		$data['entry_seo_sprice'] = $this->language->get('entry_seo_sprice');		
		$data['entry_seo_manufacturer'] = $this->language->get('entry_seo_manufacturer');		
		$data['entry_seo_category'] = $this->language->get('entry_seo_category');
		$data['entry_seo_pcategory'] = $this->language->get('entry_seo_pcategory');
		$data['entry_seo_default'] = $this->language->get('entry_seo_default');
		$data['entry_seo_column'] = $this->language->get('entry_seo_column');
		$data['entry_seo_keyword'] = $this->language->get('entry_seo_keyword');
		$data['entry_seo_url'] = $this->language->get('entry_seo_url');
		$data['entry_seo_h1'] = $this->language->get('entry_seo_h1');
		$data['entry_seo_photo'] = $this->language->get('entry_seo_photo');
		$data['entry_seo_number'] = $this->language->get('entry_seo_number');
		$data['entry_seo_code'] = $this->language->get('entry_seo_code');
		$data['entry_seo_random'] = $this->language->get('entry_seo_random');
		$data['entry_photo'] = $this->language->get('entry_photo');
		$data['entry_seo_brand'] = $this->language->get('entry_seo_brand');
		$data['entry_descrip'] = $this->language->get('entry_descrip');
		$data['entry_seo_att'] = $this->language->get('entry_seo_att');
		$data['entry_seo_opt'] = $this->language->get('entry_seo_opt');
		$data['entry_seo_vatt'] = $this->language->get('entry_seo_vatt');
		$data['entry_seo_vopt'] = $this->language->get('entry_seo_vopt');
		$data['entry_seo_text'] = $this->language->get('entry_seo_text');
		$data['entry_bonus'] = $this->language->get('entry_bonus');
		$data['entry_seo_sku'] = $this->language->get('entry_seo_sku');	
		$data['entry_seo_upc'] = $this->language->get('entry_seo_upc');
		$data['entry_seo_loc'] = $this->language->get('entry_seo_loc');
		$data['entry_seo_mpn'] = $this->language->get('entry_seo_mpn');
		$data['entry_seo_ean'] = $this->language->get('entry_seo_ean');
		$data['entry_seo_isbn'] = $this->language->get('entry_seo_isbn');
		$data['entry_seo_jan'] = $this->language->get('entry_seo_jan');	
		$data['entry_ddesc'] = $this->language->get('entry_ddesc');
		$data['entry_iprice'] = $this->language->get('entry_iprice');
		$data['entry_usd'] = $this->language->get('entry_usd');
		$data['entry_qu_discount'] = $this->language->get('entry_qu_discount');
		$data['entry_idcat'] = $this->language->get('entry_idcat');
		$data['entry_suppler_main'] = $this->language->get('entry_suppler_main');
		$data['entry_zero'] = $this->language->get('entry_zero');
		$data['entry_noprice'] = $this->language->get('entry_noprice');
		$data['entry_baseprice'] = $this->language->get('entry_baseprice');
		$data['entry_metka'] = $this->language->get('entry_metka');
		$data['entry_jopt'] = $this->language->get('entry_jopt');
		$data['entry_skuopt'] = $this->language->get('entry_skuopt');
		$data['entry_newproduct'] = $this->language->get('entry_newproduct');
		$data['entry_sdesc'] = $this->language->get('entry_sdesc');
		$data['entry_sname'] = $this->language->get('entry_sname');
		$data['entry_up'] = $this->language->get('entry_up');
		$data['entry_add'] = $this->language->get('entry_add');
		$data['entry_codid1'] = $this->language->get('entry_codid1');
		$data['entry_codid2'] = $this->language->get('entry_codid2');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_actfilter'] = $this->language->get('entry_actfilter');
		$data['entry_series'] = $this->language->get('entry_series');
		$data['entry_sleep'] = $this->language->get('entry_sleep');
		$data['entry_file'] = $this->language->get('entry_file');
		$data['entry_photodesc'] = $this->language->get('entry_photodesc');
		$data['entry_photoattr'] = $this->language->get('entry_photoattr');
		$data['entry_optsku0'] = $this->language->get('entry_optsku0');
		$data['entry_optsku1'] = $this->language->get('entry_optsku1');
		$data['entry_optsku2'] = $this->language->get('entry_optsku2');
		$data['entry_opt_fotos'] = $this->language->get('entry_opt_fotos');
		$data['opt_fotos0'] = $this->language->get('opt_fotos0');
		$data['opt_fotos1'] = $this->language->get('opt_fotos1');
		$data['opt_fotos2'] = $this->language->get('opt_fotos2');
		$data['opt_fotos3'] = $this->language->get('opt_fotos3');
		$data['opt_fotos4'] = $this->language->get('opt_fotos4');
		$data['entry_opt_prices'] = $this->language->get('entry_opt_prices');
		$data['entry_opt_pref'] = $this->language->get('entry_opt_pref');
		$data['opt_prices_s'] = $this->language->get('opt_prices_s');
		$data['opt_prices_min'] = $this->language->get('opt_prices_min');
		$data['opt_prices_plus'] = $this->language->get('opt_prices_plus');
		$data['entry_joen1'] = $this->language->get('entry_joen1');
		$data['entry_joen2'] = $this->language->get('entry_joen2');
		$data['entry_joen3'] = $this->language->get('entry_joen3');
		$data['entry_joen4'] = $this->language->get('entry_joen4');
		$data['entry_rprice'] = $this->language->get('entry_rprice');
		$data['entry_emopt'] = $this->language->get('entry_emopt');
		$data['entry_subfolder'] = $this->language->get('entry_subfolder');
		$data['entry_delimiter'] = $this->language->get('entry_delimiter');
		$data['entry_prefix'] = $this->language->get('entry_prefix');
		$data['entry_seo_pr_photo'] = $this->language->get('entry_seo_pr_photo');
		$data['entry_formdate'] = $this->language->get('entry_formdate');
		$data['entry_task1'] = $this->language->get('entry_task1');
		$data['entry_task2'] = $this->language->get('entry_task2');
		$data['entry_task3'] = $this->language->get('entry_task3');
		$data['entry_task4'] = $this->language->get('entry_task4');
		$data['entry_source'] = $this->language->get('entry_source');
		$data['entry_format'] = $this->language->get('entry_format');
		$data['entry_link'] = $this->language->get('entry_link');		
		$data['entry_ftp_name'] = $this->language->get('entry_ftp_name');
		$data['entry_ftp_pass'] = $this->language->get('entry_ftp_pass');		
		$data['entry_f_excel'] = $this->language->get('entry_f_excel');
		$data['entry_f_xml'] = $this->language->get('entry_f_xml');
		$data['entry_f_csv'] = $this->language->get('entry_f_csv');
		$data['entry_report1'] = $this->language->get('entry_report1');
		$data['entry_report2'] = $this->language->get('entry_report2');
		$data['entry_report3'] = $this->language->get('entry_report3');
		$data['entry_report4'] = $this->language->get('entry_report4');
		$data['entry_report5'] = $this->language->get('entry_report5');
		$data['entry_f_name'] = $this->language->get('entry_f_name');
		$data['entry_cron_sort'] = $this->language->get('entry_cron_sort');
		$data['entry_cron_off'] = $this->language->get('entry_cron_off');
		$data['entry_cron_on'] = $this->language->get('entry_cron_on');
		$data['entry_cron_start'] = $this->language->get('entry_cron_start');
		$data['entry_cron_hour'] = $this->language->get('entry_cron_hour');
		$data['entry_cron_day'] = $this->language->get('entry_cron_day');
		$data['entry_cron_week'] = $this->language->get('entry_cron_week');
		$data['entry_cron_zone'] = $this->language->get('entry_cron_zone');
		$data['entry_alt_cat'] = $this->language->get('entry_alt_cat');
		$data['entry_pars2'] = $this->language->get('entry_pars2');
		$data['entry_pars3'] = $this->language->get('entry_pars3');
		$data['entry_pars4'] = $this->language->get('entry_pars4');
		$data['entry_languageID'] = $this->language->get('entry_languageID');
		
    	$data['button_save'] = $this->language->get('button_save');
    	$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_base'] = $this->language->get('button_base');		
		
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_attribute'] = $this->language->get('tab_attribute');
		$data['tab_option'] = $this->language->get('tab_option');
		$data['tab_action'] = $this->language->get('tab_action');	
		$data['tab_price'] = $this->language->get('tab_price');
		$data['tab_seo'] = $this->language->get('tab_seo');
		$data['tab_cron'] = $this->language->get('tab_cron');
		
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
		
		$url = '';		
			
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
			
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true),
      		'separator' => ' :: '
   		);		
		
		if (!isset($this->request->get['form_id'])) {
			$data['action'] = $this->url->link('catalog/suppler/insert', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['scron'] = $this->url->link('catalog/suppler/scron', 'user_token=' . $this->session->data['user_token']. $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $this->request->get['form_id'] . $url, true);
			
			$data['scron'] = $this->url->link('catalog/suppler/scron', 'user_token=' . $this->session->data['user_token']. '&form_id=' . $this->request->get['form_id'] . $url, true);
		}
		
		$data['cancel'] = $this->url->link('catalog/suppler', 'user_token=' . $this->session->data['user_token'] . $url, true);		
		
		if (isset($this->request->get['form_id'])) {
			$data['base'] = $this->url->link('catalog/suppler/base', 'user_token=' . $this->session->data['user_token']. '&form_id=' . $this->request->get['form_id'] . $url, true);
		} else {
			$data['base'] = $this->url->link('catalog/suppler/base', 'user_token=' . $this->session->data['user_token']. $url, true);
		}		
			
		if (isset($this->request->get['form_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$suppler_info = $this->model_catalog_suppler->getSuppler($this->request->get['form_id']);
		}		
		
		$data['user_token'] = $this->session->data['user_token'];
	
		$data['start'] = 0;				
		$data['limit'] = 5000;			
	
		$data['suppler_all'] = $this->model_catalog_suppler->getSupplers($order);
		
		$this->load->model('catalog/attribute');		
		$attributes = $this->model_catalog_attribute->getAttributes($data);	
		$data['attributes'] = $this->getAttributes($attributes);
		
		$this->load->model('catalog/option');	
		$data['options'] = $this->model_catalog_option->getOptions($data);	
		
		$id = 0;
		if (isset($this->request->get['form_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
               $id = $this->request->get['form_id']; 
		}		
			
		$results = $this->model_catalog_suppler->getMySuppler($id);
		
		$this->load->model('catalog/manufacturer');		
    	$data['manufacturers'] = $this->model_catalog_suppler->getAllManufacturers($id); 		
							
		$categories = $this->model_catalog_suppler->getAllCategories($id);
		$data['categories'] = $this->getAllCategories($categories);
		
		$data['filters'] = $this->model_catalog_suppler->getAllFilters();
					
		$data['supplers'] = array();		
					
    	foreach ($results as $result) {
			$action = array();			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
			
			$data['supplers'] = array(
				'form_id' 	  	  => $result['form_id'],
				'suppler_id' 	  => $result['suppler_id'],
				'name'            => $result['name'],
				'main'            => $result['main'],
				'sort_order'      => $result['sort_order'],
				'rate'            => $result['rate'],
				'ratep'            => $result['ratep'],
				'ratek'            => $result['ratek'],
				'cod'             => $result['cod'],
				'related'         => $result['related'],
				'updte'           => $result['updte'],
				'item'            => $result['item'],
				'cat'             => $result['cat'],
				'qu'              => $result['qu'],
				'price'           => $result['price'],
				'descrip'         => $result['descrip'],
				'pic_ext'         => $result['pic_ext'],
				'manuf'           => $result['manuf'],
				'warranty'        => $result['warranty'],
				'status'		  => $result['status'],
				'ad'              => $result['ad'],				
				'my_cat'          => $result['my_cat'],
				'my_qu'           => $result['my_qu'],
				'my_price'        => $result['my_price'],
				'my_descrip'      => $result['my_descrip'],
				'newphoto'     	  => $result['newphoto'],
				'cheap'     	  => $result['cheap'],
				'my_manuf' 		  => $result['my_manuf'],
				'my_mark'         => $result['my_mark'],
				'my_photo'        => $result['my_photo'],
				'weight'         => $result['weight'],
				'length'         => $result['length'],
				'width'          => $result['width'],
				'height'         => $result['height'],
				'parent'         => $result['parent'],
				'hide'         	 => $result['hide'],
				'addopt'       	 => $result['addopt'],
				'addseo'       	 => $result['addseo'],
				'importseo'      => $result['importseo'],
				'pmanuf'       	 => $result['pmanuf'],
				'umanuf'       	 => $result['umanuf'],
				'upname'       	 => $result['upname'],
				'upattr'       	 => $result['upattr'],
				'upopt'       	 => $result['upopt'],
				'myplus'       	 => $result['myplus'],
				'cprice'       	 => $result['cprice'],
				'minus'       	 => $result['minus'],
				'chcode'       	 => $result['chcode'],
				'sorder'       	 => $result['sorder'],
				'spec'       	 => $result['spec'],
				'upurl'       	 => $result['upurl'],
				'newurl'       	 => $result['newurl'],
				'ref'       	 => $result['ref'],
				'ref1'       	 => $result['ref1'],
				'ref2'       	 => $result['ref2'],
				'ref3'       	 => $result['ref3'],
				'addattr'      	 => $result['addattr'],
				'exsame'      	 => $result['exsame'],
				'sku2'      	 => $result['sku2'],
				'parss'      	 => $result['parss'],
				'points'      	 => $result['points'],
				'places'      	 => $result['places'],
				'parsi'      	 => $result['parsi'],
				'pointi'      	 => $result['pointi'],
				'placei'      	 => $result['placei'],
				'parsc'      	 => $result['parsc'],
				'pointc'      	 => $result['pointc'],
				'placec'      	 => $result['placec'],
				'parsp'      	 => $result['parsp'],
				'pointp'      	 => $result['pointp'],
				'placep'      	 => $result['placep'],
				'parsd'      	 => $result['parsd'],
				'pointd'      	 => $result['pointd'],
				'placed'      	 => $result['placed'],
				'parsm'      	 => $result['parsm'],
				'pointm'      	 => $result['pointm'],
				'placem'      	 => $result['placem'],
				'parsk'      	 => $result['parsk'],
				'parsq'      	 => $result['parsq'],
				'pointq'      	 => $result['pointq'],
				'placeq'      	 => $result['placeq'],
				'bprice'      	 => $result['bprice'],
				'kmenu'      	 => $result['kmenu'],
				'catcreate'    	 => $result['catcreate'],
				'stay'    	 	 => $result['stay'],
				'off'       	 => $result['off'],
				'joen'       	 => $result['joen'],
				'onn'       	 => $result['onn'],
				'refer'       	 => $result['refer'],
				'disc'       	 => $result['disc'],
				'upc'       	 => $result['upc'],
				'ean'       	 => $result['ean'],
				'mpn'       	 => $result['mpn'],
				'location'     	 => $result['location'],
				'jan'       	 => $result['jan'],
				'isbn'       	 => $result['isbn'],
				'ddata'       	 => $result['ddata'],
				'bonus'       	 => $result['bonus'],
				'ddesc'       	 => $result['ddesc'],
				'qu_discount'  	 => $result['qu_discount'],
				'plusopt'        => $result['plusopt'],
				'idcat'          => $result['idcat'],
				't_ref'        	 => $result['t_ref'],
				't_ref1'         => $result['t_ref1'],
				't_ref2'       	 => $result['t_ref2'],
				't_ref3'         => $result['t_ref3'],
				't_status'       => $result['t_status'],
				'termin'         => $result['termin'],
				'onoff'          => $result['onoff'],
				'zero'           => $result['zero'],
				'metka'          => $result['metka'],
				'jopt'           => $result['jopt'],
				'optsku'         => $result['optsku'],
				'newproduct'     => $result['newproduct'],
				'opt_fotos'      => $result['opt_fotos'],
				'opt_prices'     => $result['opt_prices'],
				'usd'            => $result['usd'],	
				'serie'          => $result['serie'],
				'sleep'          => $result['sleep'],
				'ffile'          => $result['ffile'],
				'rprice'         => $result['rprice'],
				'subfolder'      => $result['subfolder'],
				'delimiter'      => $result['delimiter'],
				'skuprefix'      => $result['skuprefix'],
				'formdate'      => $result['formdate'],
				'codeprice'      => $result['codeprice'],
				'codedonor'      => $result['codedonor'],
				'model'     	 => $result['model'],
				'newonly'     	 => $result['newonly'],
				'minopt'     	 => $result['minopt'],
				'lang'     	 	 => $result['lang'],
								
				'selected'        => isset($this->request->post['selected']) && in_array($result['form_id'], $this->request->post['selected']),				
				'action'          => $action
			);
			
			$scron[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/scron', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
			
			$base = array();	
			$base[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/base', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
			
			$data['act'] = array(
				'act_cat'         => 0,
				'act_manuf'       => 0,
				'filter_date_start'     => '0000-00-00',
				'filter_date_end'       => '0000-00-00',
				'cod_from'       => 0,
				'cod_to'       => 0,
				'price_from'       => 0,
				'price_to'       => 0,
				'act_mult'       => '1.000',
				'isno'          => 0,
				'command'       => 0,
				'all'			=> 0,
				'zact_cat'      => 0,
				'act_attribut'  => 0,
				'act_noattribut'  => 0,
				'act_inattribut'  => 0,
				'act_isvalue'  => 0,
				'inoption'  => 0,
				'isoptvalue'  => 0,
				'act_find'  => '',
				'act_change'  => '',
				'offproduct'  => 1,
				'descr'      => 0,
				'up-add'      => 0,
				'cat-id'      => 0,
				
				'selected'        => isset($this->request->post['selected']) && in_array($result['form_id'], $this->request->post['selected']),				
				'base'          => $base
			);
		}		
		
		$data_total = $this->model_catalog_suppler->getTotalData($id);	
	
		$limit = 50;
		$rows = $this->model_catalog_suppler->getSupplerData($id);
		$n = ($page-1)*$limit;
		$data['suppler'] = array();
							
		for ($i=$n; $i<$limit+$n; $i++) {
			if (!isset($rows[$i]['cat_ext'])) break;
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $rows[$i]['form_id'] . $url, true)
			);
									   
			$data['suppler'][] = array(
				'form_id' 	  => $rows[$i]['form_id'],
				'cat_ext'		  => $rows[$i]['cat_ext'],
				'category_id'	  => $rows[$i]['category_id'],
				'pic_int'	  	  => $rows[$i]['pic_int'],
				'cat_plus'	  	  => $rows[$i]['cat_plus'],	
				'del'			  => '0',
				'nom_id'		  => $rows[$i]['nom_id'],
				'selected'        => isset($this->request->post['selected']) && in_array($rows[$i]['form_id'], $this->request->post['selected']),
				'action'          => $action
			);
			
			if (isset($this->request->post['category_id'])) {
				$data['category_id'] = $this->request->post['category_id'];
			} else {
				$data['category_id'][] = $rows[$i]['category_id'];
			} 
			if (isset($this->request->post['del'])) {
				$data['del'] = $this->request->post['del'];
			} else {
				$data['del'][] = '0';
			}
			if (isset($this->request->post['nom_id'])) {
				$data['nom_id'] = $this->request->post['nom_id'];
			} else {
				$data['nom_id'][] = $rows[$i]['nom_id'];
			}			
		}		
		$results = $this->model_catalog_suppler->getSupplerAttributes($id);

		$data['sa'] = array();
								
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
									   
			$data['sa'][] = array(
				'attr_ext'		  => $result['attr_ext'],
				'attr_point'	  => $result['attr_point'],
				'attribute_id'	  => $result['attribute_id'],
				'filter_group_id' => $result['filter_group_id'],
				'tags'	  	 	  => $result['tags'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['attribute_id'], $this->request->post['selected']),
				'action'          => $action
			);
			
			if (isset($this->request->post['attribute_id'])) {
				$data['attribute_id'] = $this->request->post['attribute_id'];
			} else {
				$data['attribute_id'][] = $result['attribute_id'];
			} 
			if (isset($this->request->post['filter_group_id'])) {
				$data['filter_group_id'] = $this->request->post['filter_group_id'];
			} else {
				$data['filter_group_id'][] = $result['filter_group_id'];
			}		
			if (isset($this->request->post['attr_point'])) {
				$data['attr_point'] = $this->request->post['attr_point'];
			} else {
				$data['attr_point'][] = $result['attr_point'];
			}
			if (isset($this->request->post['attr_ext'])) {
				$data['attr_ext'] = $this->request->post['attr_ext'];
			} else {
				$data['attr_ext'][] = $result['attr_ext'];
			}
			if (isset($this->request->post['tags'])) {
				$data['tags'] = $this->request->post['tags'];    	
			} else {	
				$data['tags'][] = $result['tags'];
			}			
		}

		$results = $this->model_catalog_suppler->getSupplerOptions($id);	
	
		$data['op'] = array();
								
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
									   
			$data['op'][] = array(
				'opt'		  => $result['opt'],
				'option_id'	  => $result['option_id'],
				'opt_point'	  => $result['opt_point'],
				'art'	  	  => $result['art'],
				'ko'	  	  => $result['ko'],
				'pr'	   	  => $result['pr'],
				'po'	 	  => $result['po'],
				'we'	 	  => $result['we'],
				'foto'	 	  => $result['foto'],
				'opt_pref'	  => $result['opt_pref'],
				'opt_margin'  => $result['opt_margin'],
				'option_required' 	  => $result['option_required'],				
				'selected'    => isset($this->request->post['selected']) && in_array($result['option_id'], $this->request->post['selected']),
				'action'      => $action
			);
			
			if (isset($this->request->post['option_id'])) {
				$data['option_id'] = $this->request->post['option_id'];
			} else {
				$data['option_id'][] = $result['option_id'];
			} 
			if (isset($this->request->post['opt'])) {
				$data['opt'] = $this->request->post['opt'];
			} else {
				$data['opt'][] = $result['opt'];
			}
			if (isset($this->request->post['opt_point'])) {
				$data['opt_point'] = $this->request->post['opt_point'];
			} else {
				$data['opt_point'][] = $result['opt_point'];
			}
			if (isset($this->request->post['art'])) {
				$data['art'] = $this->request->post['art'];    	
			} else {	
				$data['art'][] = $result['art'];
			}
			if (isset($this->request->post['ko'])) {
				$data['ko'] = $this->request->post['ko'];    	
			} else {	
				$data['ko'][] = $result['ko'];
			}
			if (isset($this->request->post['pr'])) {
				$data['pr'] = $this->request->post['pr'];    	
			} else {	
				$data['pr'][] = $result['pr'];
			}
			if (isset($this->request->post['po'])) {
				$data['po'] = $this->request->post['po'];    	
			} else {	
				$data['po'][] = $result['po'];
			}			
			if (isset($this->request->post['we'])) {
				$data['we'] = $this->request->post['we'];    	
			} else {	
				$data['we'][] = $result['we'];
			}
			if (isset($this->request->post['foto'])) {
				$data['foto'] = $this->request->post['foto'];    	
			} else {	
				$data['foto'][] = $result['foto'];
			}
			if (isset($this->request->post['option_required'])) {
				$data['option_required'] = $this->request->post['option_required'];    	
			} else {	
				$data['option_required'][] = $result['option_required'];
			}
			
		}
		
		$results = $this->model_catalog_suppler->getSupplerPrice($id);	
	
		$data['site'] = array();
								
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
									   
			$data['site'][] = array(
				'nom'		  => $result['nom'],
				'ident'	 	  => $result['ident'],
				'mratek'	 	  => $result['mratek'],
				'param'	  	  => $result['param'],
				'point'	   	  => $result['point'],
				'noprice'	  => $result['noprice'],
				'paramnp'	  => $result['paramnp'],
				'pointnp'	  => $result['pointnp'],
				'baseprice'	  => $result['baseprice'],
				'action'      => $action
			);
			
			if (isset($this->request->post['nom'])) {
				$data['nom'] = $this->request->post['nom'];
			} else {
				$data['nom'][] = $result['nom'];
			} 
			if (isset($this->request->post['ident'])) {
				$data['ident'] = $this->request->post['ident'];
			} else {
				$data['ident'][] = $result['ident'];
			}
			if (isset($this->request->post['mratek'])) {
				$data['mratek'] = $this->request->post['mratek'];
			} else {
				$data['mratek'][] = $result['mratek'];
			}
			if (isset($this->request->post['param'])) {
				$data['param'] = $this->request->post['param'];    	
			} else {	
				$data['param'][] = $result['param'];
			}
			if (isset($this->request->post['point'])) {
				$data['point'] = $this->request->post['point'];    	
			} else {	
				$data['point'][] = $result['point'];
			}
			if (isset($this->request->post['noprice'])) {
				$data['noprice'] = $this->request->post['noprice'];    	
			} else {	
				$data['noprice'][] = $result['noprice'];
			}
			if (isset($this->request->post['paramnp'])) {
				$data['paramnp'] = $this->request->post['paramnp'];    	
			} else {	
				$data['paramnp'][] = $result['paramnp'];
			}
			if (isset($this->request->post['pointnp'])) {
				$data['pointnp'] = $this->request->post['pointnp'];    	
			} else {	
				$data['pointnp'][] = $result['pointnp'];
			}
			if (isset($this->request->post['baseprice'])) {
				$data['baseprice'] = $this->request->post['baseprice'];    	
			} else {	
				$data['baseprice'][] = $result['baseprice'];
			}
		}

		$results = $this->model_catalog_suppler->getSupplerCron($id);
		
		foreach ($results as $result) {	
			$data['ccron'] = array();		
		    	
			$action = array();			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $result['form_id'] . $url, true)
			);
	
			$data['ccron'] = array(
				'form_id' 	  	  => $result['form_id'],
				'suppler_id' 	  => $result['suppler_id'],
				'cmd1'            => $result['cmd1'],
				'cmd2'            => $result['cmd2'],
				'cmd3'            => $result['cmd3'],
				'cmd4'            => $result['cmd4'],
				'cmd5'            => $result['cmd5'],
				'cmd6'            => $result['cmd6'],
				'cmd7'            => $result['cmd7'],
				'cmd8'            => $result['cmd8'],
				'cmd9'            => $result['cmd9'],				
				'act_find1'       => $result['act_find1'],
				'act_find2'       => $result['act_find2'],
				'act_find3'       => $result['act_find3'],
				'act_find4'       => $result['act_find4'],
				'act_find5'       => $result['act_find5'],
				'act_find6'       => $result['act_find6'],
				'act_find7'       => $result['act_find7'],
				'act_find8'       => $result['act_find8'],
				'act_find9'       => $result['act_find9'],				
				'act_change1'     => $result['act_change1'],
				'act_change2'     => $result['act_change2'],
				'act_change3'     => $result['act_change3'],
				'act_change4'     => $result['act_change4'],
				'act_change5'     => $result['act_change5'],
				'act_change6'     => $result['act_change6'],
				'act_change7'     => $result['act_change7'],
				'act_change8'     => $result['act_change8'],
				'act_change9'     => $result['act_change9'],				
				'all1'      	  => $result['all1'],
				'all2'     	      => $result['all2'],
				'all3'     	      => $result['all3'],
				'all4'     	      => $result['all4'],
				'all5'    	      => $result['all5'],
				'all6'    	      => $result['all6'],
				'all7'     	      => $result['all7'],
				'all8'    	      => $result['all8'],
				'all9'    	      => $result['all9'],
				'isno1'    	      => $result['isno1'],
				'isno2'    	      => $result['isno2'],
				'isno3'    	      => $result['isno3'],
				'isno4'    	      => $result['isno4'],
				'isno5'    	      => $result['isno5'],
				'isno6'    	      => $result['isno6'],
				'isno7'    	      => $result['isno7'],
				'isno8'    	      => $result['isno8'],
				'isno9'    	      => $result['isno9'],				
				'link'    	      => $result['link'],
				'ftp_name'    	  => $result['ftp_name'],
				'ftp_pass'    	  => $result['ftp_pass'],
				'ext'    	      => $result['ext'],
				'rtype'           => $result['rtype'],				
				'mail'    	      => $result['mail'],
				'pr_name'		  => $result['pr_name'],
				'port1'			  => $result['port1'],
				'text'		  	  => $result['text'],
				'text1'		  	  => $result['text1'],
				'text2'		  	  => $result['text2'],
				'on_off'		  => $result['on_off'],
				'csort'		 	  => $result['csort'],
				'action'      	  => $action
			);
			
			if (isset($this->request->post['cmd1'])) {
				$data['cmd1'] = $this->request->post['cmd1'];
			} else {
				$data['cmd1'] = $result['cmd1'];
			}
			if (isset($this->request->post['cmd2'])) {
				$data['cmd2'] = $this->request->post['cmd2'];
			} else {
				$data['cmd2'] = $result['cmd2'];
			}
			if (isset($this->request->post['cmd3'])) {
				$data['cmd3'] = $this->request->post['cmd3'];
			} else {
				$data['cmd3'] = $result['cmd3'];
			}
			if (isset($this->request->post['cmd4'])) {
				$data['cmd4'] = $this->request->post['cmd4'];
			} else {
				$data['cmd4'] = $result['cmd4'];
			}
			if (isset($this->request->post['cmd5'])) {
				$data['cmd5'] = $this->request->post['cmd5'];
			} else {
				$data['cmd5'] = $result['cmd5'];
			}
			if (isset($this->request->post['cmd6'])) {
				$data['cmd6'] = $this->request->post['cmd6'];
			} else {
				$data['cmd6'] = $result['cmd6'];
			}			
			if (isset($this->request->post['cmd7'])) {
				$data['cmd7'] = $this->request->post['cmd7'];
			} else {
				$data['cmd7'] = $result['cmd7'];
			}
			if (isset($this->request->post['cmd8'])) {
				$data['cmd8'] = $this->request->post['cmd8'];
			} else {
				$data['cmd8'] = $result['cmd8'];
			}
			if (isset($this->request->post['cmd9'])) {
				$data['cmd9'] = $this->request->post['cmd9'];
			} else {
				$data['cmd9'] = $result['cmd9'];
			}
			if (isset($this->request->post['act_find1'])) {
				$data['act_find1'] = $this->request->post['act_find1'];
			} else {
				$data['act_find1'] = $result['act_find1'];
			}
			if (isset($this->request->post['act_find2'])) {
				$data['act_find2'] = $this->request->post['act_find2'];
			} else {
				$data['act_find2'] = $result['act_find2'];
			}
			if (isset($this->request->post['act_find3'])) {
				$data['act_find3'] = $this->request->post['act_find3'];
			} else {
				$data['act_find3'] = $result['act_find3'];
			}
			if (isset($this->request->post['act_find4'])) {
				$data['act_find4'] = $this->request->post['act_find4'];
			} else {
				$data['act_find4'] = $result['act_find4'];
			}
			if (isset($this->request->post['act_find5'])) {
				$data['act_find5'] = $this->request->post['act_find5'];
			} else {
				$data['act_find5'] = $result['act_find5'];
			}
			if (isset($this->request->post['act_find6'])) {
				$data['act_find6'] = $this->request->post['act_find6'];
			} else {
				$data['act_find6'] = $result['act_find6'];
			}			
			if (isset($this->request->post['act_find7'])) {
				$data['act_find7'] = $this->request->post['act_find7'];
			} else {
				$data['act_find7'] = $result['act_find7'];
			}
			if (isset($this->request->post['act_find8'])) {
				$data['act_find8'] = $this->request->post['act_find8'];
			} else {
				$data['act_find8'] = $result['act_find8'];
			}
			if (isset($this->request->post['act_find9'])) {
				$data['act_find9'] = $this->request->post['act_find9'];
			} else {
				$data['act_find9'] = $result['act_find9'];
			}
			if (isset($this->request->post['act_change1'])) {
				$data['act_change1'] = $this->request->post['act_change1'];
			} else {
				$data['act_change1'] = $result['act_change1'];
			}
			if (isset($this->request->post['act_change2'])) {
				$data['act_change2'] = $this->request->post['act_change2'];
			} else {
				$data['act_change2'] = $result['act_change2'];
			}
			if (isset($this->request->post['act_change3'])) {
				$data['act_change3'] = $this->request->post['act_change3'];
			} else {
				$data['act_change3'] = $result['act_change3'];
			}
			if (isset($this->request->post['act_change4'])) {
				$data['act_change4'] = $this->request->post['act_change4'];
			} else {
				$data['act_change4'] = $result['act_change4'];
			}
			if (isset($this->request->post['act_change5'])) {
				$data['act_change5'] = $this->request->post['act_change5'];
			} else {
				$data['act_change5'] = $result['act_change5'];
			}
			if (isset($this->request->post['act_change6'])) {
				$data['act_change6'] = $this->request->post['act_change6'];
			} else {
				$data['act_change6'] = $result['act_change6'];
			}
			if (isset($this->request->post['act_change7'])) {
				$data['act_change7'] = $this->request->post['act_change7'];
			} else {
				$data['act_change7'] = $result['act_change7'];
			}
			if (isset($this->request->post['act_change8'])) {
				$data['act_change8'] = $this->request->post['act_change8'];
			} else {
				$data['act_change8'] = $result['act_change8'];
			}
			if (isset($this->request->post['act_change9'])) {
				$data['act_change9'] = $this->request->post['act_change9'];
			} else {
				$data['act_change9'] = $result['act_change9'];
			}
			if (isset($this->request->post['all1'])) {
				$data['all1'] = $this->request->post['all1'];
			} else {
				$data['all1'] = $result['all1'];
			}
			if (isset($this->request->post['all2'])) {
				$data['all2'] = $this->request->post['all2'];
			} else {
				$data['all2'] = $result['all2'];
			}
			if (isset($this->request->post['all3'])) {
				$data['all3'] = $this->request->post['all3'];
			} else {
				$data['all3'] = $result['all3'];
			}
			if (isset($this->request->post['all4'])) {
				$data['all4'] = $this->request->post['all4'];
			} else {
				$data['all4'] = $result['all4'];
			}
			if (isset($this->request->post['all5'])) {
				$data['all5'] = $this->request->post['all5'];
			} else {
				$data['all5'] = $result['all5'];
			}
			if (isset($this->request->post['all6'])) {
				$data['all6'] = $this->request->post['all6'];
			} else {
				$data['all6'] = $result['all6'];
			}
			if (isset($this->request->post['all7'])) {
				$data['all7'] = $this->request->post['all7'];
			} else {
				$data['all7'] = $result['all7'];
			}
			if (isset($this->request->post['all8'])) {
				$data['all8'] = $this->request->post['all8'];
			} else {
				$data['all8'] = $result['all8'];
			}
			if (isset($this->request->post['all9'])) {
				$data['all9'] = $this->request->post['all9'];
			} else {
				$data['all9'] = $result['all9'];
			}
			if (isset($this->request->post['isno1'])) {
				$data['isno1'] = $this->request->post['isno1'];
			} else {
				$data['isno1'] = $result['isno1'];
			}
			if (isset($this->request->post['isno2'])) {
				$data['isno2'] = $this->request->post['isno2'];
			} else {
				$data['isno2'] = $result['isno2'];
			}
			if (isset($this->request->post['isno3'])) {
				$data['isno3'] = $this->request->post['isno3'];
			} else {
				$data['isno3'] = $result['isno3'];
			}
			if (isset($this->request->post['isno4'])) {
				$data['isno4'] = $this->request->post['isno4'];
			} else {
				$data['isno4'] = $result['isno4'];
			}
			if (isset($this->request->post['isno5'])) {
				$data['isno5'] = $this->request->post['isno5'];
			} else {
				$data['isno5'] = $result['isno5'];
			}
			if (isset($this->request->post['isno6'])) {
				$data['isno6'] = $this->request->post['isno6'];
			} else {
				$data['isno6'] = $result['isno6'];
			}
			if (isset($this->request->post['isno7'])) {
				$data['isno7'] = $this->request->post['isno7'];
			} else {
				$data['isno7'] = $result['isno7'];
			}
			if (isset($this->request->post['isno8'])) {
				$data['isno8'] = $this->request->post['isno8'];
			} else {
				$data['isno8'] = $result['isno8'];
			}
			if (isset($this->request->post['isno9'])) {
				$data['isno9'] = $this->request->post['isno9'];
			} else {
				$data['isno9'] = $result['isno9'];
			}
			if (isset($this->request->post['link'])) {
				$data['link'] = $this->request->post['link'];
			} else {
				$data['link'] = $result['link'];
			}
			if (isset($this->request->post['ftp_name'])) {
				$data['ftp_name'] = $this->request->post['ftp_name'];
			} else {
				$data['ftp_name'] = $result['ftp_name'];
			}
			if (isset($this->request->post['ftp_pass'])) {
				$data['ftp_pass'] = $this->request->post['ftp_pass'];
			} else {
				$data['ftp_pass'] = $result['ftp_pass'];
			}
			if (isset($this->request->post['ext'])) {
				$data['ext'] = $this->request->post['ext'];
			} else {
				$data['ext'] = $result['ext'];
			}
			if (isset($this->request->post['rtype'])) {
				$data['rtype'] = $this->request->post['rtype'];
			} else {
				$data['rtype'] = $result['rtype'];
			}
			if (isset($this->request->post['mail'])) {
				$data['mail'] = $this->request->post['mail'];
			} else {
				$data['mail'] = $result['mail'];
			}
			if (isset($this->request->post['pr_name'])) {
				$data['pr_name'] = $this->request->post['pr_name'];
			} else {
				$data['pr_name'] = $result['pr_name'];
			}
		}
		
		require_once 'suppler_license/suppler_ins.php';	  //  Do not remove it !!!
		
		$data['statuses'] = $this->model_catalog_suppler->getStatus();
		
		if (isset($this->request->post['main'])) {
      		$data['main'] = $this->request->post['main'];			
    	} elseif (!empty($suppler_info)) {
			$data['main'] = $suppler_info['main'];			
		} else {
      		$data['supplers']['main'] = 0;
    	}		
		
		if (isset($this->request->post['suppler_id'])) {
      		$data['suppler_id'] = $this->request->post['suppler_id'];
    	} elseif (!empty($suppler_info)) {
			$data['suppler_id'] = $suppler_info['suppler_id'];
		} else {	
      		$data['supplers']['suppler_id'] = '1';
    	}
		
		if (isset($this->request->post['rate'])) {
      		$data['rate'] = $this->request->post['rate'];			
    	} elseif (!empty($suppler_info)) {
			$data['rate'] = $suppler_info['rate'];			
		} else {
      		$data['supplers']['rate'] = 1;
    	}

		if (isset($this->request->post['ratep'])) {
      		$data['ratep'] = $this->request->post['ratep'];			
    	} elseif (!empty($suppler_info)) {
			$data['ratep'] = $suppler_info['ratep'];			
		} else {
      		$data['supplers']['ratep'] = 1;
    	}

		if (isset($this->request->post['ratek'])) {
      		$data['ratek'] = $this->request->post['ratek'];			
    	} elseif (!empty($suppler_info)) {
			$data['ratek'] = $suppler_info['ratek'];			
		} else {
      		$data['supplers']['ratek'] = 0;
    	}		
		
		if (isset($this->request->post['cod'])) {
      		$data['cod'] = $this->request->post['cod'];
    	} elseif (!empty($suppler_info)) {
			$data['cod'] = $suppler_info['cod'];
		} else {
      		$data['supplers']['cod'] = '';
    	}

		if (isset($this->request->post['related'])) {
      		$data['related'] = $this->request->post['related'];
    	} elseif (!empty($suppler_info)) {
			$data['related'] = $suppler_info['related'];
		} else {
      		$data['supplers']['related'] = '';
    	}
		
		if (isset($this->request->post['updte'])) {
      		$data['updte'] = $this->request->post['updte'];
    	} elseif (!empty($suppler_info)) {
			$data['updte'] = $suppler_info['updte'];
		} else {
      		$data['supplers']['updte'] = '';
    	}
		
		if (isset($this->request->post['item'])) {
      		$data['item'] = $this->request->post['item'];
    	} elseif (!empty($suppler_info)) {
			$data['item'] = $suppler_info['item'];
		} else {
      		$data['supplers']['item'] = '';
    	}	
		
		if (isset($this->request->post['cat'])) {
      		$data['cat'] = $this->request->post['cat'];
    	} elseif (!empty($suppler_info)) {
			$data['cat'] = $suppler_info['cat'];
		} else {
      		$data['supplers']['cat'] = '';
    	}	
		
		if (isset($this->request->post['qu'])) {
      		$data['qu'] = $this->request->post['qu'];
    	} elseif (!empty($suppler_info)) {
			$data['qu'] = $suppler_info['qu'];
		} else {
      		$data['supplers']['qu'] = '';
    	}	
		
		if (isset($this->request->post['price'])) {
      		$data['price'] = $this->request->post['price'];
    	} elseif (!empty($suppler_info)) {
			$data['price'] = $suppler_info['price'];
		} else {
      		$data['supplers']['price'] = '';
    	}
		
		if (isset($this->request->post['usd'])) {
      		$data['usd'] = $this->request->post['usd'];
    	} elseif (!empty($suppler_info)) {
			$data['usd'] = $suppler_info['usd'];
		} else {
      		$data['supplers']['usd'] = '';
    	}		
		
		if (isset($this->request->post['descrip'])) {
      		$data['descrip'] = $this->request->post['descrip'];
    	} elseif (!empty($suppler_info)) {
			$data['descrip'] = $suppler_info['descrip'];
		} else {
      		$data['supplers']['descrip'] = '';
    	}	
		
		if (isset($this->request->post['pic_ext'])) {
      		$data['pic_ext'] = $this->request->post['pic_ext'];
    	} elseif (!empty($suppler_info)) {
			$data['pic_ext'] = $suppler_info['pic_ext'];
		} else {
      		$data['supplers']['pic_ext'] = '';
    	}

		if (isset($this->request->post['manuf'])) {
      		$data['manuf'] = $this->request->post['manuf'];
		} elseif (!empty($suppler_info)) {
			$data['manuf'] = $suppler_info['manuf'];
		} else {
      		$data['supplers']['manuf'] = '';
    	}
		
		if (isset($this->request->post['warranty'])) {
      		$data['warranty'] = $this->request->post['warranty'];
    	} elseif (!empty($suppler_info)) {
			$data['warranty'] = $suppler_info['warranty'];
		} else {
      		$data['supplers']['warranty'] = '';
    	}	
		
		if (isset($this->request->post['sku2'])) {
      		$data['sku2'] = $this->request->post['sku2'];
    	} elseif (!empty($suppler_info)) {
			$data['sku2'] = $suppler_info['sku2'];
		} else {
      		$data['supplers']['sku2'] = '';
    	}

		if (isset($this->request->post['parss'])) {
      		$data['parss'] = $this->request->post['parss'];
    	} elseif (!empty($suppler_info)) {
			$data['parss'] = $suppler_info['parss'];
		} else {
      		$data['supplers']['parss'] = '';
    	}

		if (isset($this->request->post['points'])) {
      		$data['points'] = $this->request->post['points'];
    	} elseif (!empty($suppler_info)) {
			$data['points'] = $suppler_info['points'];
		} else {
      		$data['supplers']['points'] = '';
    	}
		
		if (isset($this->request->post['places'])) {
      		$data['places'] = $this->request->post['places'];
    	} elseif (!empty($suppler_info)) {
			$data['places'] = $suppler_info['places'];
		} else {
      		$data['supplers']['places'] = '';
    	}
		
		if (isset($this->request->post['parsi'])) {
      		$data['parsi'] = $this->request->post['parsi'];
    	} elseif (!empty($suppler_info)) {
			$data['parsi'] = $suppler_info['parsi'];
		} else {
      		$data['supplers']['parsi'] = '';
    	}

		if (isset($this->request->post['pointi'])) {
      		$data['pointi'] = $this->request->post['pointi'];
    	} elseif (!empty($suppler_info)) {
			$data['pointi'] = $suppler_info['pointi'];
		} else {
      		$data['supplers']['pointi'] = '';
    	}
		
		if (isset($this->request->post['placei'])) {
      		$data['placei'] = $this->request->post['placei'];
    	} elseif (!empty($suppler_info)) {
			$data['placei'] = $suppler_info['placei'];
		} else {
      		$data['supplers']['placei'] = '';
    	}
		
		if (isset($this->request->post['parsc'])) {
      		$data['parsc'] = $this->request->post['parsc'];
    	} elseif (!empty($suppler_info)) {
			$data['parsc'] = $suppler_info['parsc'];
		} else {
      		$data['supplers']['parsc'] = '';
    	}

		if (isset($this->request->post['pointc'])) {
      		$data['pointc'] = $this->request->post['pointc'];
    	} elseif (!empty($suppler_info)) {
			$data['pointc'] = $suppler_info['pointc'];
		} else {
      		$data['supplers']['pointc'] = '';
    	}
		
		if (isset($this->request->post['placec'])) {
      		$data['placec'] = $this->request->post['placec'];
    	} elseif (!empty($suppler_info)) {
			$data['placec'] = $suppler_info['placec'];
		} else {
      		$data['supplers']['placec'] = '';
    	}
		
		if (isset($this->request->post['parsp'])) {
      		$data['parsp'] = $this->request->post['parsp'];
    	} elseif (!empty($suppler_info)) {
			$data['parsp'] = $suppler_info['parsp'];
		} else {
      		$data['supplers']['parsp'] = '';
    	}

		if (isset($this->request->post['pointp'])) {
      		$data['pointp'] = $this->request->post['pointp'];
    	} elseif (!empty($suppler_info)) {
			$data['pointp'] = $suppler_info['pointp'];
		} else {
      		$data['supplers']['pointp'] = '';
    	}
		
		if (isset($this->request->post['placep'])) {
      		$data['placep'] = $this->request->post['placep'];
    	} elseif (!empty($suppler_info)) {
			$data['placep'] = $suppler_info['placep'];
		} else {
      		$data['supplers']['placep'] = '';
    	}
		
		if (isset($this->request->post['parsd'])) {
      		$data['parsd'] = $this->request->post['parsd'];
    	} elseif (!empty($suppler_info)) {
			$data['parsd'] = $suppler_info['parsd'];
		} else {
      		$data['supplers']['parsd'] = '';
    	}

		if (isset($this->request->post['pointd'])) {
      		$data['pointd'] = $this->request->post['pointd'];
    	} elseif (!empty($suppler_info)) {
			$data['pointd'] = $suppler_info['pointd'];
		} else {
      		$data['supplers']['pointd'] = '';
    	}
		
		if (isset($this->request->post['placed'])) {
      		$data['placed'] = $this->request->post['placed'];
    	} elseif (!empty($suppler_info)) {
			$data['placed'] = $suppler_info['placed'];
		} else {
      		$data['supplers']['placed'] = '';
    	}
				
		if (isset($this->request->post['parsm'])) {
      		$data['parsm'] = $this->request->post['parsm'];
    	} elseif (!empty($suppler_info)) {
			$data['parsm'] = $suppler_info['parsm'];
		} else {
      		$data['supplers']['parsm'] = '';
    	}

		if (isset($this->request->post['pointm'])) {
      		$data['pointm'] = $this->request->post['pointm'];
    	} elseif (!empty($suppler_info)) {
			$data['pointm'] = $suppler_info['pointm'];
		} else {
      		$data['supplers']['pointm'] = '';
    	}
		
		if (isset($this->request->post['placem'])) {
      		$data['placem'] = $this->request->post['placem'];
    	} elseif (!empty($suppler_info)) {
			$data['placem'] = $suppler_info['placem'];
		} else {
      		$data['supplers']['placem'] = '';
    	}
		
		if (isset($this->request->post['parsk'])) {
      		$data['parsk'] = $this->request->post['parsk'];
    	} elseif (!empty($suppler_info)) {
			$data['parsk'] = $suppler_info['parsk'];
		} else {
      		$data['supplers']['parsk'] = '';
    	}
		
		if (isset($this->request->post['parsq'])) {
      		$data['parsq'] = $this->request->post['parsq'];
    	} elseif (!empty($suppler_info)) {
			$data['parsq'] = $suppler_info['parsq'];
		} else {
      		$data['supplers']['parsq'] = '';
    	}
		
		if (isset($this->request->post['pointq'])) {
      		$data['pointq'] = $this->request->post['pointq'];
    	} elseif (!empty($suppler_info)) {
			$data['pointq'] = $suppler_info['pointq'];
		} else {
      		$data['supplers']['pointq'] = '';			
    	}
		
		if (isset($this->request->post['placeq'])) {
      		$data['placeq'] = $this->request->post['placeq'];
    	} elseif (!empty($suppler_info)) {
			$data['placeq'] = $suppler_info['placeq'];
		} else {
      		$data['supplers']['placeq'] = '';			
    	}
		
		if (isset($this->request->post['bprice'])) {
      		$data['bprice'] = $this->request->post['bprice'];
    	} elseif (!empty($suppler_info)) {
			$data['bprice'] = $suppler_info['bprice'];
		} else {
      		$data['supplers']['bprice'] = '';			
    	}
		
		if (isset($this->request->post['kmenu'])) {
      		$data['kmenu'] = $this->request->post['kmenu'];
    	} elseif (!empty($suppler_info)) {
			$data['kmenu'] = $suppler_info['kmenu'];
		} else {
      		$data['supplers']['kmenu'] = '0';			
    	}
		
		if (isset($this->request->post['qu_discount'])) {
      		$data['qu_discount'] = $this->request->post['qu_discount'];
    	} elseif (!empty($suppler_info)) {
			$data['qu_discount'] = $suppler_info['qu_discount'];
		} else {
      		$data['supplers']['qu_discount'] = '';			
    	}
		
		if (isset($this->request->post['catcreate'])) {
      		$data['catcreate'] = $this->request->post['catcreate'];
    	} elseif (!empty($suppler_info)) {
			$data['catcreate'] = $suppler_info['catcreate'];
		} else {
      		$data['supplers']['catcreate'] = '0';
    	}
		
		if (isset($this->request->post['stay'])) {
      		$data['stay'] = $this->request->post['stay'];
    	} elseif (!empty($suppler_info)) {
			$data['stay'] = $suppler_info['stay'];
		} else {
      		$data['supplers']['stay'] = '0';
    	}
		
		if (isset($this->request->post['joen'])) {
      		$data['joen'] = $this->request->post['joen'];
    	} elseif (!empty($suppler_info)) {
			$data['joen'] = $suppler_info['joen'];
		} else {
      		$data['supplers']['joen'] = '0';
    	}
		
		if (isset($this->request->post['refer'])) {
      		$data['refer'] = $this->request->post['refer'];
    	} elseif (!empty($suppler_info)) {
			$data['refer'] = $suppler_info['refer'];
		} else {
      		$data['supplers']['refer'] = '0';
    	}
		
		if (isset($this->request->post['onn'])) {
      		$data['onn'] = $this->request->post['onn'];
    	} elseif (!empty($suppler_info)) {
			$data['onn'] = $suppler_info['onn'];
		} else {
      		$data['supplers']['onn'] = '0';
    	}
		
		if (isset($this->request->post['disc'])) {
      		$data['disc'] = $this->request->post['disc'];
    	} elseif (!empty($suppler_info)) {
			$data['disc'] = $suppler_info['disc'];
		} else {
      		$data['supplers']['disc'] = '0';
    	}
		
		if (isset($this->request->post['ad'])) {
      		$data['ad'] = $this->request->post['ad'];
    	} elseif (!empty($suppler_info)) {
			$data['ad'] = $suppler_info['ad'];
		} else {
      		$data['supplers']['ad'] = '1';
    	}
		
		if (isset($this->request->post['off'])) {
      		$data['off'] = $this->request->post['off'];
    	} elseif (!empty($suppler_info)) {
			$data['off'] = $suppler_info['off'];
		} else {
      		$data['supplers']['off'] = '0';
    	}
		
		if (isset($this->request->post['parent'])) {
      		$data['parent'] = $this->request->post['parent'];
    	} elseif (!empty($suppler_info)) {
			$data['parent'] = $suppler_info['parent'];
		} else {
      		$data['supplers']['parent'] = '0';
    	}
		
		if (isset($this->request->post['hide'])) {
      		$data['hide'] = $this->request->post['hide'];
    	} elseif (!empty($suppler_info)) {
			$data['hide'] = $suppler_info['hide'];
		} else {
      		$data['supplers']['hide'] = '1';
    	}
		
		if (isset($this->request->post['onoff'])) {
      		$data['onoff'] = $this->request->post['onoff'];
    	} elseif (!empty($suppler_info)) {
			$data['onoff'] = $suppler_info['onoff'];
		} else {
      		$data['supplers']['onoff'] = '1';
    	}
		
		if (isset($this->request->post['addopt'])) {
      		$data['addopt'] = $this->request->post['addopt'];
    	} elseif (!empty($suppler_info)) {
			$data['addopt'] = $suppler_info['addopt'];
		} else {
      		$data['supplers']['addopt'] = '1';
    	}
		
		if (isset($this->request->post['addseo'])) {
      		$data['addseo'] = $this->request->post['addseo'];
    	} elseif (!empty($suppler_info)) {
			$data['addseo'] = $suppler_info['addseo'];
		} else {
      		$data['supplers']['addseo'] = '0';
    	}
		
		if (isset($this->request->post['upurl'])) {
      		$data['upurl'] = $this->request->post['upurl'];
    	} elseif (!empty($suppler_info)) {
			$data['upurl'] = $suppler_info['upurl'];
		} else {
      		$data['supplers']['upurl'] = '0';
    	}
		
		if (isset($this->request->post['newurl'])) {
      		$data['newurl'] = $this->request->post['newurl'];
    	} elseif (!empty($suppler_info)) {
			$data['newurl'] = $suppler_info['newurl'];
		} else {
      		$data['supplers']['newurl'] = '0';
    	}
		
		if (isset($this->request->post['upc'])) {
      		$data['upc'] = $this->request->post['upc'];
    	} elseif (!empty($suppler_info)) {
			$data['upc'] = $suppler_info['upc'];
		} else {
      		$data['supplers']['upc'] = '';
    	}
		
		if (isset($this->request->post['ean'])) {
      		$data['ean'] = $this->request->post['ean'];
    	} elseif (!empty($suppler_info)) {
			$data['ean'] = $suppler_info['ean'];
		} else {
      		$data['supplers']['ean'] = '';
    	}
		
		if (isset($this->request->post['mpn'])) {
      		$data['mpn'] = $this->request->post['mpn'];
    	} elseif (!empty($suppler_info)) {
			$data['mpn'] = $suppler_info['mpn'];
		} else {
      		$data['supplers']['mpn'] = '';
    	}
		
		if (isset($this->request->post['location'])) {
      		$data['location'] = $this->request->post['location'];
    	} elseif (!empty($suppler_info)) {
			$data['location'] = $suppler_info['location'];
		} else {
      		$data['supplers']['location'] = '';
    	}
		
		if (isset($this->request->post['jan'])) {
      		$data['jan'] = $this->request->post['jan'];
    	} elseif (!empty($suppler_info)) {
			$data['jan'] = $suppler_info['jan'];
		} else {
      		$data['supplers']['jan'] = '';
    	}
		
		if (isset($this->request->post['isbn'])) {
      		$data['isbn'] = $this->request->post['isbn'];
    	} elseif (!empty($suppler_info)) {
			$data['isbn'] = $suppler_info['isbn'];
		} else {
      		$data['supplers']['isbn'] = '';
    	}
		
		if (isset($this->request->post['ddata'])) {
      		$data['ddata'] = $this->request->post['ddata'];
    	} elseif (!empty($suppler_info)) {
			$data['ddata'] = $suppler_info['ddata'];
		} else {
      		$data['supplers']['ddata'] = '';
    	}
		
		if (isset($this->request->post['ref'])) {
      		$data['ref'] = $this->request->post['ref'];
    	} elseif (!empty($suppler_info)) {
			$data['ref'] = $suppler_info['ref'];
		} else {
      		$data['supplers']['ref'] = '';
    	}
		
		if (isset($this->request->post['ref1'])) {
      		$data['ref1'] = $this->request->post['ref1'];
    	} elseif (!empty($suppler_info)) {
			$data['ref1'] = $suppler_info['ref1'];
		} else {
      		$data['supplers']['ref1'] = '';
    	}
		
		if (isset($this->request->post['t_ref'])) {
      		$data['t_ref'] = $this->request->post['t_ref'];
    	} elseif (!empty($suppler_info)) {
			$data['t_ref'] = $suppler_info['t_ref'];
		} else {
      		$data['supplers']['t_ref'] = '0';
    	}
		
		if (isset($this->request->post['t_ref1'])) {
      		$data['t_ref1'] = $this->request->post['t_ref1'];
    	} elseif (!empty($suppler_info)) {
			$data['t_ref1'] = $suppler_info['t_ref1'];
		} else {
      		$data['supplers']['t_ref1'] = '0';
    	}
		if (isset($this->request->post['ref2'])) {
      		$data['ref2'] = $this->request->post['ref2'];
    	} elseif (!empty($suppler_info)) {
			$data['ref2'] = $suppler_info['ref2'];
		} else {
      		$data['supplers']['ref2'] = '';
    	}
		
		if (isset($this->request->post['ref3'])) {
      		$data['ref3'] = $this->request->post['ref3'];
    	} elseif (!empty($suppler_info)) {
			$data['ref3'] = $suppler_info['ref3'];
		} else {
      		$data['supplers']['ref3'] = '';
    	}
		
		if (isset($this->request->post['t_ref2'])) {
      		$data['t_ref2'] = $this->request->post['t_ref2'];
    	} elseif (!empty($suppler_info)) {
			$data['t_ref2'] = $suppler_info['t_ref2'];
		} else {
      		$data['supplers']['t_ref2'] = '0';
    	}
		
		if (isset($this->request->post['t_ref3'])) {
      		$data['t_ref3'] = $this->request->post['t_ref3'];
    	} elseif (!empty($suppler_info)) {
			$data['t_ref3'] = $suppler_info['t_ref3'];
		} else {
      		$data['supplers']['t_ref3'] = '0';
    	}
		
		if (isset($this->request->post['addattr'])) {
      		$data['addattr'] = $this->request->post['addattr'];
    	} elseif (!empty($suppler_info)) {
			$data['addattr'] = $suppler_info['addattr'];
		} else {
      		$data['supplers']['addattr'] = '0';
    	}
		
		if (isset($this->request->post['exsame'])) {
      		$data['exsame'] = $this->request->post['exsame'];
    	} elseif (!empty($suppler_info)) {
			$data['exsame'] = $suppler_info['exsame'];
		} else {
      		$data['supplers']['exsame'] = '0';
    	}
		
		if (isset($this->request->post['importseo'])) {
      		$data['importseo'] = $this->request->post['importseo'];
    	} elseif (!empty($suppler_info)) {
			$data['importseo'] = $suppler_info['importseo'];
		} else {
      		$data['supplers']['importseo'] = '1';
    	}
		
		if (isset($this->request->post['pmanuf'])) {
      		$data['pmanuf'] = $this->request->post['pmanuf'];
    	} elseif (!empty($suppler_info)) {
			$data['pmanuf'] = $suppler_info['pmanuf'];
		} else {
      		$data['supplers']['pmanuf'] = '1';
    	}
		
		if (isset($this->request->post['umanuf'])) {
      		$data['umanuf'] = $this->request->post['umanuf'];
    	} elseif (!empty($suppler_info)) {
			$data['umanuf'] = $suppler_info['umanuf'];
		} else {
      		$data['supplers']['umanuf'] = '0';
    	}
		
		if (isset($this->request->post['sorder'])) {
      		$data['sorder'] = $this->request->post['sorder'];
    	} elseif (!empty($suppler_info)) {
			$data['sorder'] = $suppler_info['sorder'];
		} else {
      		$data['supplers']['sorder'] = '';
    	}
		
		if (isset($this->request->post['spec'])) {
      		$data['spec'] = $this->request->post['spec'];
    	} elseif (!empty($suppler_info)) {
			$data['spec'] = $suppler_info['spec'];
		} else {
      		$data['supplers']['spec'] = '';
    	}
		
		if (isset($this->request->post['myplus'])) {
      		$data['myplus'] = $this->request->post['myplus'];
    	} elseif (!empty($suppler_info)) {
			$data['myplus'] = $suppler_info['myplus'];
		} else {
      		$data['supplers']['myplus'] = '';
    	}
		
		if (isset($this->request->post['cprice'])) {
      		$data['cprice'] = $this->request->post['cprice'];
    	} elseif (!empty($suppler_info)) {
			$data['cprice'] = $suppler_info['cprice'];
		} else {
      		$data['supplers']['cprice'] = '';
    	}
		
		if (isset($this->request->post['minopt'])) {
      		$data['minopt'] = $this->request->post['minopt'];
    	} elseif (!empty($suppler_info)) {
			$data['minopt'] = $suppler_info['minopt'];
		} else {
      		$data['supplers']['minopt'] = '0';
    	}
		
		if (isset($this->request->post['minus'])) {
      		$data['minus'] = $this->request->post['minus'];
    	} elseif (!empty($suppler_info)) {
			$data['minus'] = $suppler_info['minus'];
		} else {
      		$data['supplers']['minus'] = '1';
    	}
		
		if (isset($this->request->post['chcode'])) {
      		$data['chcode'] = $this->request->post['chcode'];
    	} elseif (!empty($suppler_info)) {
			$data['chcode'] = $suppler_info['chcode'];
		} else {
      		$data['supplers']['chcode'] = '0';
    	}
		if (isset($this->request->post['newonly'])) {
      		$data['newonly'] = $this->request->post['newonly'];
    	} elseif (!empty($suppler_info)) {
			$data['newonly'] = $suppler_info['newonly'];
		} else {
      		$data['supplers']['newonly'] = '0';
    	}
		
		if (isset($this->request->post['upname'])) {
      		$data['upname'] = $this->request->post['upname'];
    	} elseif (!empty($suppler_info)) {
			$data['upname'] = $suppler_info['upname'];
		} else {
      		$data['supplers']['upname'] = '0';
    	}
		
		if (isset($this->request->post['upattr'])) {
      		$data['upattr'] = $this->request->post['upattr'];
    	} elseif (!empty($suppler_info)) {
			$data['upattr'] = $suppler_info['upattr'];
		} else {
      		$data['supplers']['upattr'] = '0';
    	}
		
		if (isset($this->request->post['upopt'])) {
      		$data['upopt'] = $this->request->post['upopt'];
    	} elseif (!empty($suppler_info)) {
			$data['upopt'] = $suppler_info['upopt'];
		} else {
      		$data['supplers']['upopt'] = '0';
    	}
		
		if (isset($this->request->post['status'])) {
      		$data['status'] = $this->request->post['status'];
    	} elseif (!empty($suppler_info)) {
			$data['status'] = $suppler_info['status'];
		} else {
      		$data['supplers']['status'] = 5;
    	}
		
		if (isset($this->request->post['my_cat'])) {
      		$data['my_cat'] = $this->request->post['my_cat'];
    	} elseif (!empty($suppler_info)) {
			$data['my_cat'] = $suppler_info['my_cat'];
		} else {
      		$data['supplers']['my_cat'] = '';
    	}
		
		if (isset($this->request->post['my_qu'])) {
      		$data['my_qu'] = $this->request->post['my_qu'];
    	} elseif (!empty($suppler_info)) {
			$data['my_qu'] = $suppler_info['my_qu'];
		} else {
      		$data['supplers']['my_qu'] = '99';
    	}
		
		if (isset($this->request->post['my_price'])) {
      		$data['my_price'] = $this->request->post['my_price'];
    	} elseif (!empty($suppler_info)) {
			$data['my_price'] = $suppler_info['my_price'];
		} else {
      		$data['supplers']['my_price'] = '1';
    	}
		
		if (isset($this->request->post['my_descrip'])) {
      		$data['my_descrip'] = $this->request->post['my_descrip'];
    	} elseif (!empty($suppler_info)) {
			$data['my_descrip'] = $suppler_info['my_descrip'];
		} else {
      		$data['supplers']['my_descrip'] = '';
    	}

		if (isset($this->request->post['my_manuf'])) {
      		$data['my_manuf'] = $this->request->post['my_manuf'];
		} elseif (!empty($suppler_info)) {
			$data['my_manuf'] = $suppler_info['my_manuf'];
		} else {
      		$data['supplers']['my_manuf'] = '0';
    	}

		if (isset($this->request->post['my_mark'])) {
      		$data['my_mark'] = $this->request->post['my_mark'];
    	} elseif (!empty($suppler_info)) {
			$data['my_mark'] = $suppler_info['my_mark'];
		} else {
      		$data['supplers']['my_mark'] = '';
    	}
		
		if (isset($this->request->post['my_photo'])) {
      		$data['my_photo'] = $this->request->post['my_photo'];
    	} elseif (!empty($suppler_info)) {
			$data['my_photo'] = $suppler_info['my_photo'];
		} else {
      		$data['supplers']['my_photo'] = '';
    	}
		
		if (isset($this->request->post['newphoto'])) {
      		$data['newphoto'] = $this->request->post['newphoto'];
    	} elseif (!empty($suppler_info)) {
			$data['newphoto'] = $suppler_info['newphoto'];
		} else {
      		$data['supplers']['newphoto'] = '1';			
    	}
		
		if (isset($this->request->post['cheap'])) {
      		$data['cheap'] = $this->request->post['cheap'];
    	} elseif (!empty($suppler_info)) {
			$data['cheap'] = $suppler_info['cheap'];
		} else {
      		$data['supplers']['cheap'] = '';			
    	}
		
		if (isset($this->request->post['weight'])) {
      		$data['weight'] = $this->request->post['weight'];
    	} elseif (!empty($suppler_info)) {
			$data['weight'] = $suppler_info['weight'];
		} else {
      		$data['supplers']['weight'] = '';
    	}
		
		if (isset($this->request->post['length'])) {
      		$data['length'] = $this->request->post['length'];
    	} elseif (!empty($suppler_info)) {
			$data['length'] = $suppler_info['length'];
		} else {
      		$data['supplers']['length'] = '';
    	}
		
		if (isset($this->request->post['width'])) {
      		$data['width'] = $this->request->post['width'];
    	} elseif (!empty($suppler_info)) {
			$data['width'] = $suppler_info['width'];
		} else {
      		$data['supplers']['width'] = '';
    	}
		
		if (isset($this->request->post['height'])) {
      		$data['height'] = $this->request->post['height'];
    	} elseif (!empty($suppler_info)) {
			$data['height'] = $suppler_info['height'];
		} else {
      		$data['supplers']['height'] = '';
    	}		
		
		if (isset($this->request->post['sort_order'])) {
      		$data['sort_order'] = $this->request->post['sort_order'];
    	} elseif (!empty($suppler_info)) {
			$data['sort_order'] = $suppler_info['sort_order'];
		} else {
      		$data['sort_order'] = '0';
    	}
		
		if (isset($this->request->post['bonus'])) {
      		$data['bonus'] = $this->request->post['bonus'];
    	} elseif (!empty($suppler_info)) {
			$data['bonus'] = $suppler_info['bonus'];
		} else {
      		$data['bonus'] = '';
    	}	

		if (isset($this->request->post['ddesc'])) {
      		$data['ddesc'] = $this->request->post['ddesc'];
    	} elseif (!empty($suppler_info)) {
			$data['ddesc'] = $suppler_info['ddesc'];
		} else {
      		$data['ddesc'] = '0';
    	}
		
		if (isset($this->request->post['plusopt'])) {
      		$data['plusopt'] = $this->request->post['plusopt'];
    	} elseif (!empty($suppler_info)) {
			$data['plusopt'] = $suppler_info['plusopt'];
		} else {
      		$data['supplers']['plusopt'] = '0';
    	}
		
		if (isset($this->request->post['jopt'])) {
      		$data['jopt'] = $this->request->post['jopt'];
    	} elseif (!empty($suppler_info)) {
			$data['jopt'] = $suppler_info['jopt'];
		} else {
      		$data['supplers']['jopt'] = '0';
    	}
		
		if (isset($this->request->post['optsku'])) {
      		$data['optsku'] = $this->request->post['optsku'];
    	} elseif (!empty($suppler_info)) {
			$data['optsku'] = $suppler_info['optsku'];
		} else {
      		$data['supplers']['optsku'] = '0';
    	}
		
		if (isset($this->request->post['newproduct'])) {
      		$data['newproduct'] = $this->request->post['newproduct'];
    	} elseif (!empty($suppler_info)) {
			$data['newproduct'] = $suppler_info['newproduct'];
		} else {
      		$data['supplers']['newproduct'] = '';
    	}
		
		if (isset($this->request->post['idcat'])) {
      		$data['idcat'] = $this->request->post['idcat'];
    	} elseif (!empty($suppler_info)) {
			$data['idcat'] = $suppler_info['idcat'];
		} else {
      		$data['supplers']['idcat'] = '0';
    	}
		
		if (isset($this->request->post['termin'])) {
      		$data['termin'] = $this->request->post['termin'];
    	} elseif (!empty($suppler_info)) {
			$data['termin'] = $suppler_info['termin'];
		} else {
      		$data['supplers']['termin'] = '';
    	}
		
		if (isset($this->request->post['t_status'])) {
      		$data['t_status'] = $this->request->post['t_status'];
    	} elseif (!empty($suppler_info)) {
			$data['t_status'] = $suppler_info['t_status'];
		} else {
      		$data['supplers']['t_status'] = '';
    	}
		
		if (isset($this->request->post['zero'])) {
      		$data['zero'] = $this->request->post['zero'];
    	} elseif (!empty($suppler_info)) {
			$data['zero'] = $suppler_info['zero'];
		} else {
      		$data['supplers']['zero'] = '';
    	}
		
		if (isset($this->request->post['metka'])) {
      		$data['metka'] = $this->request->post['metka'];
    	} elseif (!empty($suppler_info)) {
			$data['metka'] = $suppler_info['metka'];
		} else {
      		$data['supplers']['metka'] = '';
    	}
		
		if (isset($this->request->post['opt_prices'])) {
      		$data['opt_prices'] = $this->request->post['opt_prices'];
    	} elseif (!empty($suppler_info)) {
			$data['opt_prices'] = $suppler_info['opt_prices'];
		} else {
      		$data['supplers']['opt_prices'] = 1;
    	}
		
		if (isset($this->request->post['opt_fotos'])) {
      		$data['opt_fotos'] = $this->request->post['opt_fotos'];
    	} elseif (!empty($suppler_info)) {
			$data['opt_fotos'] = $suppler_info['opt_fotos'];
		} else {
      		$data['supplers']['opt_fotos'] = 0;
    	}
		
		if (isset($this->request->post['serie'])) {
      		$data['serie'] = $this->request->post['serie'];
    	} elseif (!empty($suppler_info)) {
			$data['serie'] = $suppler_info['serie'];
		} else {
      		$data['supplers']['serie'] = '';
    	}
		
		if (isset($this->request->post['sleep'])) {
      		$data['sleep'] = $this->request->post['sleep'];
    	} elseif (!empty($suppler_info)) {
			$data['sleep'] = $suppler_info['sleep'];
		} else {
      		$data['supplers']['sleep'] = 0;
    	}
		
		if (isset($this->request->post['ffile'])) {
      		$data['ffile'] = $this->request->post['ffile'];
    	} elseif (!empty($suppler_info)) {
			$data['ffile'] = $suppler_info['ffile'];
		} else {
      		$data['supplers']['ffile'] = 0;
    	}
		
		if (isset($this->request->post['rprice'])) {
      		$data['rprice'] = $this->request->post['rprice'];
    	} elseif (!empty($suppler_info)) {
			$data['rprice'] = $suppler_info['rprice'];
		} else {
      		$data['supplers']['rprice'] = '';
    	}
		
		if (isset($this->request->post['subfolder'])) {
      		$data['subfolder'] = $this->request->post['subfolder'];
    	} elseif (!empty($suppler_info)) {
			$data['subfolder'] = $suppler_info['subfolder'];
		} else {
      		$data['supplers']['subfolder'] = 1;
    	}
		
		if (isset($this->request->post['delimiter'])) {
      		$data['delimiter'] = $this->request->post['delimiter'];
    	} elseif (!empty($suppler_info)) {
			$data['delimiter'] = $suppler_info['delimiter'];
		} else {
      		$data['supplers']['delimiter'] = ',';
    	}
		
		if (isset($this->request->post['skuprefix'])) {
      		$data['skuprefix'] = $this->request->post['skuprefix'];
    	} elseif (!empty($suppler_info)) {
			$data['skuprefix'] = $suppler_info['skuprefix'];
		} else {
      		$data['supplers']['skuprefix'] = '';
    	}
		
		if (isset($this->request->post['formdate'])) {
      		$data['formdate'] = $this->request->post['formdate'];
    	} elseif (!empty($suppler_info)) {
			$data['formdate'] = $suppler_info['formdate'];
		} else {
      		$data['supplers']['formdate'] = '2000-01-01';
    	}
		if (isset($this->request->post['codeprice'])) {
      		$data['codeprice'] = $this->request->post['codeprice'];
    	} elseif (!empty($suppler_info)) {
			$data['codeprice'] = $suppler_info['codeprice'];
		} else {
      		$data['supplers']['codeprice'] = '';
    	}
		if (isset($this->request->post['codedonor'])) {
      		$data['codedonor'] = $this->request->post['codedonor'];
    	} elseif (!empty($suppler_info)) {
			$data['codedonor'] = $suppler_info['codedonor'];
		} else {
      		$data['supplers']['codedonor'] = '';
    	}
		if (isset($this->request->post['model'])) {
      		$data['model'] = $this->request->post['model'];
    	} elseif (!empty($suppler_info)) {
			$data['model'] = $suppler_info['model'];
		} else {
      		$data['supplers']['model'] = 1;
    	}
		if (isset($this->request->post['lang'])) {
      		$data['lang'] = $this->request->post['lang'];
    	} elseif (!empty($suppler_info)) {
			$data['lang'] = $suppler_info['lang'];
		} else {
      		$data['supplers']['lang'] = '';
    	}
				
		if (isset($this->error['warning']) or isset($this->error['name'])) {
			$data['supplers']['name'] = '';
			$data['supplers']['rate'] = '1.000';
			$data['supplers']['ratep'] = '1.000';
			$data['supplers']['ratek'] = '0';
			$data['supplers']['cod'] = '';
			$data['supplers']['related'] = '';
			$data['supplers']['updte'] = '0';
			$data['supplers']['item'] = '';
			$data['supplers']['cat'] = '';
			$data['supplers']['qu'] = '';
			$data['supplers']['price'] = '';
			$data['supplers']['descrip'] = '';
			$data['supplers']['pic_ext'] = '';
			$data['supplers']['pic_ext'] = '';
			$data['supplers']['manuf'] = '';
			$data['supplers']['warranty'] = '';
			$data['supplers']['myplus'] = '';
			$data['supplers']['status'] = '5';
			$data['supplers']['my_cat'] = '';
			$data['supplers']['my_qu'] = '';
			$data['supplers']['my_price'] = '';
			$data['supplers']['my_descrip'] = '';
			$data['supplers']['my_manuf'] = '';
			$data['supplers']['my_photo'] = '';
			$data['supplers']['weight'] = '';
			$data['supplers']['length'] = '';
			$data['supplers']['width'] = '';
			$data['supplers']['height'] = '';
			$data['supplers']['ad'] = '1';
			$data['supplers']['parent'] = '0';
			$data['supplers']['newphoto'] = '0';
			$data['supplers']['hide'] = '1';			
			$data['supplers']['cheap'] = '0';
			$data['supplers']['addopt'] = '1';
			$data['supplers']['minus'] = '0';
			$data['supplers']['minopt'] = '0';
			$data['supplers']['chcode'] = '0';
			$data['supplers']['newonly'] = '0';
			$data['supplers']['sku2'] = '0';
			$data['supplers']['pointp'] = '';
			$data['supplers']['pointq'] = '';
			$data['supplers']['placeq'] = '';
			$data['supplers']['bprice'] = '';
			$data['supplers']['kmenu'] = '0';
			$data['supplers']['qu_discount'] = '';
			$data['supplers']['idcat'] = '0';
			$data['supplers']['plusopt'] = '0';
			$data['sort_order'] = 0;
			$data['keyword'] = '';
			$data['supplers']['t_ref'] = '0';
			$data['supplers']['t_status'] = '';
			$data['supplers']['termin'] = '';
			$data['supplers']['zero'] = '0';
			$data['supplers']['metka'] = '0';
			$data['supplers']['newproduct'] = '';
			$data['supplers']['opt_fotos'] = 0;
			$data['supplers']['opt_prices'] = 0;
			$data['supplers']['series'] = '';
			$data['supplers']['sleep'] = 0;
			$data['supplers']['ffile'] = 0;
			$data['supplers']['rprice'] = '';
			$data['supplers']['subfolder'] = 1;
			$data['supplers']['delimiter'] = ',';
			$data['supplers']['skuprefix'] = '';
			$data['supplers']['formdate'] = '2000-01-01';
			$data['supplers']['codeprice'] = '';
			$data['supplers']['codedonor'] = '';
			$data['supplers']['model'] = 1;
			$data['supplers']['lang'] = '';
		}
		
		$pagination = new Pagination();
		$pagination->total = $data_total;
		$pagination->page = $page;
		$pagination->limit = 50;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('catalog/suppler/update', 'user_token=' . $this->session->data['user_token'] . '&form_id=' . $id . $url . '&page={page}', true);
			
		$data['pagination'] = $pagination->render();		
		
		$this->template = 'catalog/suppler_form.twig';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');	
	
		$this->response->setOutput($this->load->view('catalog/suppler_form', $data));
	} 	
	
  	private function validateForm() { 
    	if (!$this->user->hasPermission('modify', 'catalog/suppler')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}		
						
    	if (!$this->error) {
			return true;
    	} else {
      		return false;
    	}
  	}

	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'catalog/suppler')) {
      		$this->error['warning'] = $this->language->get('error_permission'); 
		}  	 	
				
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
	
  	private function getAttributes($attributes, $attribute_group_id = 0, $attribute_group = '') {
		$output = array();
		
		foreach ($attributes as $attribute) {
			if ($attribute['attribute_group'] != '') $attribute['attribute_group'] .= $this->language->get('text_separator');
			
			$output[$attribute['attribute_id']] = array(
			'attribute_id' => $attribute['attribute_id'],
			'name'        => $attribute['attribute_group'] . $attribute['name']
			);
				
		}	
		return $output;    
    }
	 
	private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
		$output = array();

		if (array_key_exists($parent_id, $categories)) {
			if ($parent_name != '') {
				$parent_name .= $this->language->get('text_separator');
			}

			foreach ($categories[$parent_id] as $category) {
				$output[$category['category_id']] = array(
					'category_id' => $category['category_id'],
					'name'        => $parent_name . $category['name']
				);

				$output += $this->getAllCategories($categories, $category['category_id'], $parent_name . $category['name']);
			}
		}

		return $output;
    
    }
	
}
?>