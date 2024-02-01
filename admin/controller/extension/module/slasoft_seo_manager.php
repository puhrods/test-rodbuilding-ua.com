<?php
class ControllerExtensionModuleSlaSoftSeoManager extends Controller {
	private $error = array();
	private $error_slugs = array();
	
	private $version = "3.0.2";
	private $path = 'extension/module/slasoft_seo_manager';

	private $slugify;
	
	private $language_code;

	private $allowed_category_patterns = array(
		'[id]'		=> 'category',
		'[name]'	  => 'category',
		'[lang_code]' => 'language code',
		'[lang_id]'   => 'language id',
		'[store_id]'  => 'store id',
		'[path]'	  => 'path',
	);

	private $allowed_blog_patterns = array(
		'[id]'		=> 'blog id',
		'[name]'	  => 'blog title',
		'[lang_code]' => 'language code',
		'[lang_id]'   => 'language id',
		'[store_id]'  => 'store id',
		
	);

	private $allowed_product_patterns = array(
		'[name]'	  => 'product name',
		'[model]'	 => 'product model',
		'[id]'		=> 'product id',
		'[mpn]'	   => 'product mpn',
		'[sku]'	   => 'product sku',
		'[isbn]'	  => 'product isbn',
		'[jan]'	   => 'product jan ',
		'[m_name]'	=> 'manufacturer name',
		'[lang_code]' => 'language code',
		'[lang_id]'   => 'language id',
		'[store_id]'  => 'store id',
		
	);

	private $allowed_information_patterns  = array(
		'[name]'	  => 'information name',
		'[id]'		=> 'information id',
		'[lang_code]' => 'language code',
		'[lang_id]'   => 'language id',
		'[store_id]'  => 'store id',
	);

	private $allowed_manufacturer_patterns = array(
		'[name]'	  => 'manufacturer name',
		'[id]'		=> 'manufacturer id',
		'[lang_code]' => 'language code',
		'[lang_id]'   => 'language id',
		'[store_id]'  => 'store id',
	);
	
	public function __construct($registry) {
		parent::__construct($registry);
		$this->load->model($this->path);
		$this->model = $this->model_extension_module_slasoft_seo_manager;
		$this->config->load('slasoft_seo_manager');
	}

	private function language_module() {
		return $this->language->load($this->path);
	}

	public function index() {
		$data = $this->language_module();

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'ua.query';
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

		$url = '';
		$filter_allow = array(
			'filter_query'	   => false,
			'filter_type'		=> false,
			'filter_keyword'	 => false,
			'filter_language_id' => false,
			'filter_store_id'	=> false,

			'sort'  => 'ua.query',
			'order' => 'ASC',
			'page'  => 1,
		);
			

		$data['filter'] = array();
		foreach ($filter_allow as $filter_key=>$value) {
			if (isset($this->request->get[$filter_key])) {
				$url .= '&' . $filter_key . '=' . $this->request->get[$filter_key];
				$data['filter'][$filter_key] = $this->request->get[$filter_key];
			} else {
				$data['filter'][$filter_key] = $value;
			}
		}

		$this->breadcrumbs($data);
	  
		$data['delete'] = $this->makeUrl($this->path . '/delete', $url);
		$data['save'] = $this->makeUrl($this->path . '/update', $url);
		$data['self'] = $this->makeUrl($this->path);

		$data['clear'] = $this->makeUrl($this->path . '/clear', $url);
		$data['cancel'] = $this->makeUrl('marketplace/extension', 'type=module') ;

		$data['export'] = $this->makeUrl($this->path . '/export');
		$data['import'] = $this->makeUrl($this->path . '/import');

		$data['url_aliases'] = array();

		$filter_data = array_merge($data['filter'], array(
			'start' => ($data['filter']['page'] - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		));

		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name' => strip_tags($this->language->get('text_default'))
		);

		$data['stores'] = array_merge($data['stores'],$stores);
		$stores = array();
		foreach ($data['stores'] as $store) {
			$stores[$store['store_id']] = $store['name'];
		}
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['bulkCopy'] = $this->makeUrlScript($this->path . '/bulkCopy');
		$data['bulkLange'] = $this->makeUrlScript($this->path . '/bulkLange');
		$languages = array();
		foreach ($data['languages'] as $language) {
			$languages[$language['language_id']] = $language;
		}

		$url_alias_total = $this->model->getTotalUrlAliases($filter_data);

		$results = $this->model->getUrlAliases($filter_data);
		foreach ($results as $result) {
			$queries = explode('=',$result['query']);
			$action_edit = false;
			$name = '';
			if (isset($queries[1])) {
				switch ($queries[0]){
					case 'product_id' : 
						$action_edit = $this->makeUrl('catalog/product/edit', $queries[0] .'=' . $queries[1]);
						$name = $this->model->getProduct($queries[1]);
						break;
					case 'category_id' : 
						$action_edit = $this->makeUrl('catalog/category/edit', $queries[0] .'=' . $queries[1]);
						$name = $this->model->getCategory($queries[1]);
						break;
					case 'information_id' : 
						$action_edit = $this->makeUrl('catalog/information/edit', $queries[0] .'=' . $queries[1]);
						$name = $this->model->getInformation($queries[1]);
						break;
					case 'manufacturer_id' : 
						$action_edit = $this->makeUrl('catalog/manufacturer/edit', $queries[0] .'=' . $queries[1]);
						$name = $this->model->getManufacturer($queries[1]);
						break;
				}

			}
			$data['url_aliases'][] = array(
				'seo_url_id'   => $result['seo_url_id'],
				'name'		 => $name,
				'query'		=> $result['query'],
				'keyword'	  => $result['keyword'],
				'store_id'	 => $result['store_id'],
				'store_name'   => array_key_exists($result['store_id'],$stores)?$stores[$result['store_id']]:'unknown',
				'language_name'=> array_key_exists($result['language_id'],$languages)?$languages[$result['language_id']]['name']:'unknown',
				'language_id'  => $result['language_id'],
				'selected'	 => isset($this->request->post['selected']) && in_array($result['seo_url_id'], $this->request->post['selected']),
				'action_text'  => $this->language->get('text_edit'),
				'action_edit'  => $action_edit,
			);
		}
		$data['filter_typies'] = array(
			'product'	  => $this->language->get('text_type_product'),
			'category'	 => $this->language->get('text_type_category'),
			'information'  => $this->language->get('text_type_information'),
			'manufacturer' => $this->language->get('text_type_manufacturer'),
			'other'		=> $this->language->get('text_type_other')
		);

		$data['heading_title'] = $this->language->get('heading_title') . ' ' . $this->version;

		$data['column_query']   = $this->language->get('column_query');
		$data['column_keyword'] = $this->language->get('column_keyword');
		$data['column_action']  = $this->language->get('column_action');

		$data['button_add']		 = $this->language->get('button_add');
		$data['button_delete']	  = $this->language->get('button_delete');
		$data['button_edit']		= $this->language->get('button_edit');
		$data['button_save']		= $this->language->get('button_save');
		$data['button_cancel']	  = $this->language->get('button_cancel');
		$data['button_clear_cache'] = $this->language->get('button_clear_cache');
		$data['button_filter']	  = $this->language->get('button_filter');

		$data['text_type_product']	  = $this->language->get('text_type_product');
		$data['text_type_other']		= $this->language->get('text_type_other');
		$data['text_type_category']	 = $this->language->get('text_type_category');
		$data['text_type_information']  = $this->language->get('text_type_information');
		$data['text_type_manufacturer'] = $this->language->get('text_type_manufacturer');
		$data['text_selected']		  = $this->language->get('text_selected');
		$data['text_no_results']		= $this->language->get('text_no_results');
		$data['text_confirm']		   = $this->language->get('text_confirm');

		$this->nav($this->path,$data);


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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}
		
		$url = '';

		$filter_allow = array(
			'filter_query'	   => false,
			'filter_type'		=> false,
			'filter_keyword'	 => false,
			'filter_language_id' => false,
			'filter_store_id'	=> false,
		);
		foreach ($filter_allow as $filter_key=>$value) {
			if (isset($this->request->get[$filter_key])) {
				$url .= '&' . $filter_key . '=' . $this->request->get[$filter_key];
			}
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_query'] = $this->makeUrl($this->path , 'sort=ua.query' . $url);
		$data['sort_keyword'] = $this->makeUrl($this->path , 'sort=ua.keyword' . $url);
		$data['sort_store'] = $this->makeUrl($this->path , 'sort=store' . $url);
		$data['sort_language'] = $this->makeUrl($this->path , 'sort=language' . $url);

		$url = '';
		$filter_allow = array(
			'filter_query'	   => false,
			'filter_type'		=> false,
			'filter_keyword'	 => false,
			'filter_language_id' => false,
			'filter_store_id'	=> false,

			'sort' => 'ua.query',
			'order' => 'ASC',
		);
		foreach ($filter_allow as $filter_key=>$value) {
			if (isset($this->request->get[$filter_key])) {
				$url .= '&' . $filter_key . '=' . $this->request->get[$filter_key];
			}
		}

		$pagination = new Pagination();
		$pagination->total = $url_alias_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->makeUrl($this->path , $url . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($url_alias_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($url_alias_total - $this->config->get('config_limit_admin'))) ? $url_alias_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $url_alias_total, ceil($url_alias_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['addAny'] = $this->makeUrl($this->path . '/addAny');
		$this->footer('', $data);

	}

	public function addAny() {
		$this->load->language($this->path);
		if ($this->validate()) {
			$this->model->installExtendedUrl();
			$this->session->data['success'] = $this->language->get('text_success_extended');
		}
		$this->response->redirect($this->makeUrl($this->path, 'filter_type=other'));
	}

	public  function export() {
		$this->load->language($this->path);
		$this->load->model($this->path);

		$filter_data = [
		];

		$total =  $this->model->getTotalUrlAliases($filter_data);

		$filter_data = array(
			'limit' => $total,
			'page' => 1,
		);
		
		$seo_urls =  $this->model->getUrlAliases($filter_data);

		header("Content-Type: text/csv");
		header("Content-Disposition: attachment; filename=seo_manager-".date('d-m-Y').".csv");
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies
		$out = fopen('php://output', 'w');
		$export_head = array(
			"query",
			"keyword",
			"language_id",
			"store_id"
		);
		fputcsv($out, $export_head);
		
		if ($seo_urls)
			foreach ($seo_urls as $url) {
				$export = [
					$url['query'],
					$url['keyword'],
					$url['language_id'],
					$url['store_id'],
				];
			fputcsv($out, $export);
			}

		fclose($out);		
	}

	public  function import() {
		$data = $this->load->language($this->path);
		if (!$this->validate()) {
			$this->response->redirect($this->makeUrl($this->path, (isset($this->request->get['filter']) ? '&' . http_build_query(array("filter" => $this->request->get['filter'])) : ""), true));
		}

		$this->load->model($this->path);
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$result = false;
		$errors = array();
		if (!isset($this->request->files['filename']) || $this->request->files['filename']['error'] != 0)
			$errors[] = $this->language->get('error_uploadfile');
		else {
			$results = [
				'all'	   => [
					'text' => $this->language->get('text_success_result_all'),
					'cnt' => 0
				],
				'update'	=> [
					'text' => $this->language->get('text_success_result_update'),
					'cnt' => 0
				],
				'insert'	=> [
					'text' => $this->language->get('text_success_result_insert'),
					'cnt' => 0
				],
				'error'	 => [
					'text' => $this->language->get('text_success_result_error'),
					'cnt' => 0
				],
			];
			$delimiters = array(
				';',
				"\t",
				','
			);
			if (in_array($this->request->post['delimiter'],$delimiters)) {
				$delimiter = $this->request->post['delimiter'];
			} else {
				$delimiter = ',';
			}
			$line = 1;
			
			$fp = fopen($this->request->files['filename']['tmp_name'], "r");
			if ($fp !== false) {
				while (($export = fgetcsv($fp, 1000, $delimiter)) !== false) {
					if (count($export)  < 4) {
						$errors[] = sprintf($this->language->get('error_data'), $line);
						$results['error']['cnt']++;
					} else {
						if (isset($export[0]) && stristr($export[0],'query')) continue;
						$url['query'] = $export[0];
						$url['keyword'] = $export[1];
						$url['language_id'] = $export[2];
						$url['store_id'] = $export[3];

							$this->model->insertKeyword($url);
							$results['insert']['cnt']++;
					}
				$line++;
				$results['all']['cnt']++;
				}
			}
		}
		// view 
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['cancel'] = $this->makeUrl('marketplace/extension','type=module');
		$data['link_return'] = $this->makeUrl($this->path_module);
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data[$this->token] = $this->session->data[$this->token];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->makeUrl('common/dashboard')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->$this->makeUrl('marketplace/extension','type=module')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->makeUrl($this->path)
		);


		$data['errors'] = $errors;
		$data['results'] = $results;

		$this->footer('_import', $data);
	}

	public function bulkCopy() {
		$this->load->language($this->path);
		$json = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate() && 
		isset($this->request->post['from_store']) &&
		isset($this->request->post['to_store']) &&
		$this->request->post['to_store'] != $this->request->post['from_store']) {
			$this->load->model($this->path);
			$total = $this->model->bulkCopy($this->request->post);

			if (isset($this->request->post['entity'])) {
				$json['redirect'] = $this->makeUrlScript($this->path . '/tool');
			} else {
				$json['redirect'] = $this->makeUrlScript($this->path);
			}
			$this->session->data['success'] = sprintf($this->language->get('text_success_bulk'), $total);
		} else {
			
			$json['error'] = $this->language->get('text_error_bulk');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}

	public function bulkLange() {
		$this->load->language($this->path);
		$json = array();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate() && 
		isset($this->request->post['from_language']) &&
		isset($this->request->post['to_language']) &&
		$this->request->post['to_language'] != $this->request->post['from_language']) {
			$this->load->model($this->path);
			$total = $this->model->bulkLange($this->request->post);

			if (isset($this->request->post['entity'])) {
				$json['redirect'] = $this->makeUrlScript($this->path . '/tool');
			} else {
				$json['redirect'] = $this->makeUrlScript($this->path);
			}
			$this->session->data['success'] = sprintf($this->language->get('text_success_bulk'), $total);
		} else {
			
			$json['error'] = $this->language->get('text_error_bulk');
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

	}
	public function about() {
		$data = $this->language_module();
		$this->document->setTitle($this->language->get('heading_title_about'));

 		$this->breadcrumbs($data);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_nav_about'),
			'href' => $this->makeUrl($this->path . '/about')
		);


		$data['heading_title'] = $this->language->get('heading_title_about');
		$this->nav($this->path . '/about',$data);
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

		$this->footer('_about', $data);
	}

	public function setting() {
		$data = $this->language_module();
		$this->document->setTitle($this->language->get('heading_title_setting'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('slasoft_seo_manager', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->makeurl($this->path . '/setting'));
			
		}
		
 		$this->breadcrumbs($data);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_nav_setting'),
			'href' => $this->makeUrl($this->path . '/setting')
		);

		if (isset($this->request->post['slasoft_seo_manager_separator'])) {
			$data['slasoft_seo_manager_separator'] = $this->request->post['slasoft_seo_manager_separator'];
		} else {
			$data['slasoft_seo_manager_separator'] = $this->config->get('slasoft_seo_manager_separator');
		}

		if (isset($this->request->post['slasoft_seo_manager_secret'])) {
			$data['slasoft_seo_manager_secret'] = $this->request->post['slasoft_seo_manager_secret'];
		} else {
			$data['slasoft_seo_manager_secret'] = $this->config->get('slasoft_seo_manager_secret');
		}

		$data['cron_path'] = HTTP_CATALOG . 'index.php?route=' . $this->path;

		if (isset($this->request->post['slasoft_seo_manager_limit'])) {
			$data['slasoft_seo_manager_limit'] = $this->request->post['slasoft_seo_manager_limit'];
		} elseif ($this->config->has('slasoft_seo_manager_limit')) {
			$data['slasoft_seo_manager_limit'] = $this->config->get('slasoft_seo_manager_limit');
		} else {
			$data['slasoft_seo_manager_limit'] = 200;
		}

		if (isset($this->request->post['slasoft_seo_manager_limit_url'])) {
			$data['slasoft_seo_manager_limit_url'] = $this->request->post['slasoft_seo_manager_limit_url'];
		} elseif ($this->config->has('slasoft_seo_manager_limit_url')) {
			$data['slasoft_seo_manager_limit_url'] = $this->config->get('slasoft_seo_manager_limit_url');
		} else {
			$data['slasoft_seo_manager_limit_url'] = 60;
		}

		if (isset($this->request->post['slasoft_seo_manager_dublicate'])) {
			$data['slasoft_seo_manager_dublicate'] = $this->request->post['slasoft_seo_manager_dublicate'];
		} elseif ($this->config->has('slasoft_seo_manager_dublicate')) {
			$data['slasoft_seo_manager_dublicate'] = $this->config->get('slasoft_seo_manager_dublicate');
		} else {
			$data['slasoft_seo_manager_dublicate'] = 'order';
		}

		$data['separators'] = array();
		$data['separators'][] = array(
			'value' => '-',
			'text' => $this->language->get('text_separator_hype')
		);
		$data['separators'][] = array(
			'value' => '_',
			'text' => $this->language->get('text_separator_under')
		);

		$data['duplacaties'] = array();
		$data['duplacaties'][] = array(
			'value' => 'order',
			'text' => $this->language->get('text_dublicate_order')
		);
		$data['duplacaties'][] = array(
			'value' => 'random_number',
			'text' => $this->language->get('text_dublicate_random_number')
		);
		$data['duplacaties'][] = array(
			'value' => 'random_symbol',
			'text' => $this->language->get('text_dublicate_symbol')
		);
		$data['duplacaties'][] = array(
			'value' => 'entity_id',
			'text' => $this->language->get('text_dublicate_id')
		);
		
		$data['heading_title'] = $this->language->get('heading_title_setting');
		$this->nav($this->path . '/setting',$data);
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
		$data['action'] = $this->makeUrl($this->path . '/setting');
		$data['cancel'] = $this->makeUrl('marketplace/extension', 'type=module');
		
		$this->footer('_setting', $data);
	}

	public function tool() {
		$data = $this->language_module();
		$this->document->setTitle($this->language->get('heading_title_tool'));

		$this->breadcrumbs($data);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_nav_tool'),
			'href' => $this->makeUrl($this->path . '/tool')
		);
		
		$data['heading_title'] = $this->language->get('heading_title_tool');

		$this->nav($this->path . '/tool',$data);
		
		$data['heading_generator'] = $this->language->get('heading_generator');
		
		$data['tab_category'] = $this->language->get('tab_category');
		$data['tab_product'] = $this->language->get('tab_product');
		$data['tab_manufacturer'] = $this->language->get('tab_manufacturer');
		$data['tab_information'] = $this->language->get('tab_information');
		$data['tab_blog'] = $this->language->get('tab_blog');
		
		$data['blog_exist'] = $this->model->checkBlog();
		
		$data['text_pattern'] = $this->language->get('text_pattern');
		$data['text_allowed_patern'] = $this->language->get('text_allowed_patern');
		
		$data['text_language_source'] = $this->language->get('text_language_source');
		$data['text_allow_rewrite'] = $this->language->get('text_allow_rewrite');
		$data['text_save_setting'] = $this->language->get('text_save_setting');
		$data['text_generate'] = $this->language->get('text_generate');
		
		$data['button_generate'] = $this->language->get('button_generate');
		$data['button_save_setting'] = $this->language->get('button_save_setting');
		
		$d = array();
		foreach ($this->allowed_category_patterns as $key=>$element) {
			$d[] .= $key . ' - ' . $element;
		}
		$data['allowed_category_patterns'] = implode(', ', $d);

		$d = array();
		foreach ($this->allowed_product_patterns as $key=>$element) {
			$d[] .= $key . ' - ' . $element;
		}
		$data['allowed_product_patterns'] = implode(', ', $d);
		
		$d = array();
		foreach ($this->allowed_information_patterns as $key=>$element) {
			$d[] .= $key . ' - ' . $element;
		}
		$data['allowed_information_patterns'] = implode(', ', $d);

		$d = array();
		foreach ($this->allowed_manufacturer_patterns as $key=>$element) {
			$d[] .= $key . ' - ' . $element;
		}
		$data['allowed_manufacturer_patterns'] = implode(', ', $d);

		$d = array();
		foreach ($this->allowed_blog_patterns as $key=>$element) {
			$d[] .= $key . ' - ' . $element;
		}
		$data['allowed_blog_patterns'] = implode(', ', $d);

		$data['language_id_category'] = $this->config->get('language_id_category');
		$data['language_id_product'] = $this->config->get('language_id_product');
		$data['language_id_information'] = $this->config->get('language_id_information');
		$data['language_id_manufacturer'] = $this->config->get('language_id_manufacturer');
		$data['language_id_blog'] = $this->config->get('language_id_blog');
		
		$data['pattern_manufacturer'] = $this->config->get('seo_manager_pattern_manufacturer');
		$data['pattern_category'] = $this->config->get('seo_manager_pattern_category');
		$data['pattern_information'] = $this->config->get('seo_manager_pattern_information');
		$data['pattern_product'] = $this->config->get('seo_manager_pattern_product');
		$data['pattern_blog'] = $this->config->get('seo_manager_pattern_blog');
		

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

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name' => strip_tags($this->language->get('text_default'))
		);

		$data['stores'] = array_merge($data['stores'],$stores);
		$data['bulkCopy'] = $this->makeUrlScript($this->path . '/bulkCopy');
		$data['bulkLange'] = $this->makeUrlScript($this->path . '/bulkLange');

		$this->footer('_tool', $data);
	}

	public function update() {
		$this->load->language($this->path);
		$this->document->setTitle($this->language->get('heading_title'));
   
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
   
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				$this->model->updateUrlAlias($this->request->post);
				$this->session->data['success'] = $this->language->get('text_success');
		}
		$this->response->redirect($this->makeUrl($this->path, $url));
	}

	public function autocomplete() {
		$json = array();
		$data_filter = array (
			'keyword' => $this->request->get['filter_name'],
			'limit' => 20
		);
		$json = $this->model->getAlias($data_filter);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function save() {
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->load->model('setting/setting');
			$data_config = $this->model_setting_setting->getSetting('seo_manager');
			$data = array();
			$form_id = $this->request->post['form_id'];
			unset ($this->request->post['form_id']);
			
			foreach ($this->request->post as $key=>$value) {
				$data[str_replace('_' . $form_id, '', $key )] = $value;
			}
			$data_config['seo_manager_pattern_' . $form_id] = $data;
			
			$this->model_setting_setting->editSetting('seo_manager',$data_config);
		}
	}
	   
	public function check() {
		$data = $this->language_module();
		$this->document->setTitle($this->language->get('heading_title_check'));

 		$this->breadcrumbs($data);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_nav_check'),
			'href' => $this->makeUrl($this->path . '/check', true)
		);

		$data['heading_title'] = $this->language->get('heading_title_check');
		$this->nav($this->path . '/check',$data);
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
		
		if (isset($this->request->get['check']) && $this->request->get['check']) {
			$check = $this->request->get['check'];
		} else {
			$check = '';
		}

		$data['heading_title_check'] = '';

		$template = '';
		$this->allow_nav_check =  array (
			'keyword' => array(
				'heading' => $this->language->get('heading_title_check_double_keywords'),
				'text' => $this->language->get('text_nav_check_double_keywords'),
				'template' => ''
			),
			'query'		  => array(
				'heading'=> $this->language->get('heading_title_check_double_query'),
				'text' => $this->language->get('text_nav_check_double_query'),
				'template' => ''
			),
/*			
			'category'	   => array(
				'heading'=> $this->language->get('heading_title_check_category'),
				'text' => $this->language->get('text_nav_check_category'),
				'template' => ''
			),
			'product'		=> array(
				'heading'=> $this->language->get('heading_title_check_product'),
				'text' => $this->language->get('text_nav_check_product'),
				'template' => ''
			),
			'information'	=> array(
				'heading'=> $this->language->get('heading_title_check_information'),
				'text' => $this->language->get('text_nav_check_information'),
				'template' => ''
			),
			
			'p2c'			=> array(
				'heading'=>$this->language->get('heading_title_check_p2c'),
				'text' =>$this->language->get('text_nav_check_p2c'),
				'template' => ''
			),
			'p2categorymain' => array(
				'heading'=>$this->language->get('heading_title_check_p2category_main'),
				'text' =>$this->language->get('text_nav_check_p2category_main'),
				'template' => ''
			),
*/			
		);
		
		$this->nav_check($check,$data);
		
		$filter_data =  array();

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		$limit = $this->config->get('config_limit_admin');
			
		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $limit
		);

		$results = array(
			'total' =>0,
			'results' => array()
		);
		switch ($check) {
			case 'query': 
				$results = array(
					'total' => $this->model->checkTotalQuery($filter_data),
					'results' => $this->model->checkQuery($filter_data)
				);
				$filter = array (
					'filter' => 'filter_query=',
					'value' => 'query'
				);
				break;
			case 'keyword': 
				$results = array(
					'total' => $this->model->checkTotalKeyword($filter_data),
					'results' => $this->model->checkKeyword($filter_data)
				);
				$filter = array (
					'filter' => 'filter_keyword=',
					'value' => 'keyword'
				);
				break;
			case 'category':
				$results = array(
					'total' => $this->model->categoryWithoutUrlTotal($filter_data),
					'results' => $this->model->categoryWithoutUrl($filter_data)
				);
				$filter = false;
				break;
			case 'product':
				$results = array(
					'total' => $this->model->productWithoutUrlTotal($filter_data),
					'results' => $this->model->productWithoutUrl($filter_data)
				);
				$filter = false;
				break;
			case 'information':
				$results = array(
					'total' => $this->model->informationWithoutUrlTotal($filter_data),
					'results' => $this->model->informationWithoutUrl($filter_data)
				);
				$filter = false;
				break;
			case 'p2c':
				$results = array(
					'total' => $this->model->productWithoutCategoryTotal($filter_data),
					'results' => $this->model->productWithoutCategory($filter_data)
				);
				$filter = false;
				break;
			case 'p2categorymain':
				$results = array(
					'total' => $this->model->productWithoutCategoryMainTotal($filter_data),
					'results' => $this->model->productWithoutCategoryMain($filter_data)
				);
				$filter = false;
				break;
		}

		$data = array_merge(
			$data,				
			$this->pagination($results['total'],$page,$limit,$this->path . '/check', 'check=' . $check)
		);

		if ($results['results']) {
			$data['url_aliases'] = array();
			foreach ($results['results'] as $result) {
				if ($filter) {
					$href_filter = $this->makeUrl($this->path, $filter['filter'] . $result[$filter['value']]);
				} else {
					$href_filter = false;
				}
				$data['url_aliases'][] = array(
					'seo_url_id' => $result['seo_url_id'],
					'query' => $result['query'],
					'keyword' => $result['keyword'],
					'language_id' => $result['language_id'],
					'store_id' => $result['store_id'],
					'href_filter' => $href_filter,
					'action_text' => $this->language->get('text_edit')
				);
			}
		}

		$data['delete'] = $this->makeUrl($this->path . '/delete' . '&redirect=' . $check, true);
		$data['save'] = $this->makeUrl($this->path . '/update' . '&check=' . $check, true);
		$this->load->model('localisation/language');
		$languages_code = $this->model_localisation_language->getLanguages();
		$languages = array();
		foreach ($languages_code as $language) {
			$data['languages'][$language['language_id']] = $language;
		}
		
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		$data['stores'] = array();
		$data['stores']['0'] = array(
			'store_id' => 0,
			'name' => strip_tags($this->language->get('text_default'))
		);
		foreach ($stores as $store) {
			$data['stores'][$store['store_id']] = $store; 
		}

		$this->footer('_check' . $template, $data);
	}

	public function check_seo() {
		$data = $this->language_module();
		$this->document->setTitle($this->language->get('heading_title_check'));

 		$this->breadcrumbs($data);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_nav_check_seo'),
			'href' => $this->makeUrl($this->path . '/check_seo')
		);
		$this->load->model('localisation/language');
		$languages_code = $this->model_localisation_language->getLanguages();
		$languages = array();
		foreach ($languages_code as $language) {
			$languages[$language['language_id']] = $language;
		}

		$data['heading_title'] = $this->language->get('heading_title_check_seo');
		$this->nav($this->path . '/check_seo',$data);
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
		
		if (isset($this->request->get['check']) && $this->request->get['check']) {
			$check = $this->request->get['check'];
		} else {
			$check = '_';
		}

		$data['heading_title_check'] = '';
		$data['nav_checks_seo'] = array();
		if ( $check == 'product_title') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_product_title');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=product_title'),
			'text' => $this->language->get('text_nav_check_product_title')
			,'active' => $active
		);
		
		if ( $check == 'product_description') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_product_description');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=product_description'),
			'text' => $this->language->get('text_nav_check_product_description')
			,'active' => $active
		);

		$data['nav_checks_seo'] = array();
		if ( $check == 'product_title') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_product_title');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=product_title'),
			'text' => $this->language->get('text_nav_check_product_title')
			,'active' => $active
		);
		if ( $check == 'product_description') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_product_description');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=product_description'),
			'text' => $this->language->get('text_nav_check_product_description')
			,'active' => $active
		);
		if ( $check == 'category_title') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_category_title');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=category_title'),
			'text' => $this->language->get('text_nav_check_category_title')
			,'active' => $active
		);
		if ( $check == 'category_description') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_category_description');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=category_description'),
			'text' => $this->language->get('text_nav_check_category_description')
			,'active' => $active
		);

		if ( $check == 'manufacturer_title') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_manufacturer_title');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=manufacturer_title'),
			'text' => $this->language->get('text_nav_check_manufacturer_title')
			,'active' => $active
		);
		if ( $check == 'manufacturer_description') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_manufacturer_description');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=manufacturer_description'),
			'text' => $this->language->get('text_nav_check_manufacturer_description')
			,'active' => $active
		);

		if ( $check == 'information_title') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_information_title');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=information_title'),
			'text' => $this->language->get('text_nav_check_information_title')
			,'active' => $active
		);
		if ( $check == 'information_description') {
			$data['heading_title_check'] = $this->language->get('heading_title_check_information_description');
			$active = 'active';
		} else $active = '';
		$data['nav_checks_seo'][] = array(
			'href' => $this->makeUrl($this->path . '/check_seo' . '&check=information_description'),
			'text' => $this->language->get('text_nav_check_information_description')
			,'active' => $active
		);

		$action = explode('_',$check);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		if (isset($this->request->get['length'])) {
			$length = $this->request->get['length'];
		} else {
			$length = 60;
		}
		$data['length'] = $length;
		$limit = $this->config->get('config_limit_admin');
		$filter_data = array(
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $limit,
			'table' => $action[0],
			'length' => $length
		);
		
		switch ($action[1]) {
			case 'title':
				$results = $this->model->checkSeoTitle($filter_data);
				$total = $this->model->checkSeoTitleTotal($filter_data);
				$data['column_meta_data'] = $this->language->get('column_meta_data_title');
				break;
			case 'description':
				$results = $this->model->checkSeoDescription($filter_data);
				$total = $this->model->checkSeoDescriptionTotal($filter_data);
				$data['column_meta_data'] = $this->language->get('column_meta_data_description');
				break;
		}
		

		if (isset($results)) {
			$data = array_merge(
					$data,				
					$this->pagination($total,$page,$limit,$this->path . '/check_seo', '&check=' . $check)
			);
			$data['url_aliases'] = array();
			
			if ($action[0] == 'information') {
				$path_store = 'information/information';
				$path_admin = 'catalog/information';
			} elseif ($action[0] == 'category') {
				$path_store = 'product/category';
				$path_admin = 'catalog/category';
			} elseif ($action[0] == 'product') {
				$path_store = 'product/product';
				$path_admin = 'catalog/product';
			} elseif ($action[0] == 'manufacturer') {
				$path_store = 'product/manufacturer/info';
				$path_admin = 'catalog/manufacturer';
			}
			foreach ($results as $result) {
				$data['url_aliases'][] = array(
					'url_store'   => HTTP_CATALOG . 'index.php?route=' . $path_store . '&amp;' . ($action[0] == 'category'?'path=' . $result['id']: $action[0] . '_id=' . $result['id']),
					'url_admin'   => $this->makeUrl($path_admin . '/edit' , $action[0] . '_id=' . $result['id'],true),
					'id'		  => $result['id'],
					'name'		  => $result['name'],
					'meta_data'   => $result['meta_data'],
					'language'	=> $languages[$result['language_id']]['name'],
				);
			}
		}

		$this->footer('_check_seo', $data);
	}

	private function progress_generate($count,$total) {
		if ($total && $count) {
			$percent = (100 * $count) / $total;
			$progress = $this->request->post['progress'] + $percent;
			return $progress;
		} else {
			return  100;
		}
	}

	public function generate() {
		$json =array();
		$this->load->language($this->path);
		if ($this->validate()) {
			$data = $this->request->post;
			
			if ($this->config->get('slasoft_seo_manager_type') == 'URLify') {
				require_once(DIR_SYSTEM . 'library/vendor/urlify.php');
			} else {
				$this->slugify = new Cocur\Slugify\Slugify(array(
					'separator' => $this->config->get('slasoft_seo_manager_separator')?$this->config->get('slasoft_seo_manager_separator'):'-',
					'strip_tags' => false,
					'lowercase' => true,
					'trim' => true,
				));
			}

			$update = isset($this->request->post['update']) && $this->request->post['update']
				&& (isset($this->request->post['last_id'])?!(boolean)$this->request->post['last_id']: 1);
				
			$filter_data = array(
				'update' => isset($this->request->post['update']) && $this->request->post['update'],
				'language_from' => $this->request->post['language_from'],
				'language_id'   => $this->request->post['language_id'],
				'store_id'      => $this->request->post['store_id'],
				'last_id'       => isset($this->request->post['last_id'])?$this->request->post['last_id']: 0,
			);
			$filter_data['limit'] = 200;
			if ((int)$this->config->get('slasoft_seo_manager_limit')) {
				$filter_data['limit'] = $this->config->get('slasoft_seo_manager_limit');
			}
			$count = 0;
			$total = 0;
			$last_id = 0;
			$push = false;//$this->model->checkPush();
			
			$this->load->model('localisation/language');

			$languages = $this->model_localisation_language->getLanguages();
			$this->language_code = '';
			foreach ($languages as $language) {
				if ($language['language_id'] == $this->request->post['language_id']) {
					$languag_c = explode('-', $language['code']);
					$this->language_code = $languag_c[0];
				}
			};
			
			
			switch ($this->request->post['alias_type']) {
				case 'category' :
					if ($update) {
						$this->model->deleteSeoUrl(array(
							'query' => $push?'path':'category_id',
							'language_id' => $this->request->post['language_id'],
							'store_id'	=> $this->request->post['store_id']
							)
						);
					}

					$categories = $this->model->getCategories($filter_data);
					$total_categories = $this->model->getTotalCategories($filter_data);

					$patterns = array_keys($this->allowed_category_patterns);
					$pattern = trim($this->db->escape($this->request->post['pattern']));

					if ($categories) {
						$count = count($categories);
						foreach ($categories as $row) {
							$replaced = array(
								'[id]'	   => $row['category_id'], 
								'[name]'	 => $row['name'],
								'[lang_code]' => $this->language_code,
								'[lang_id]'  => $this->request->post['language_id'],
								'[store_id]' => $this->request->post['store_id'],
								'[path]'	 => $row['path'],
							);

							$replaced_new = array();
							foreach ($patterns as $key=>$value) {
								if (array_key_exists($value,$replaced)) {
									$replaced_new[$value] = $replaced[$value];
								} else {
									$replaced_new[$key] = '';
								}
							}
							$keyword = str_replace($patterns,$replaced_new, $pattern);
							if ($push) {
								$path = $this->model->categoryGetPath($row['category_id']);
							}
							$data_insert = array(
								'query'	   => ($push)?('path=' . $path):('category_id=' . (int)$row['category_id']), 
								'entity_id'   => $row['category_id'],
								'keyword'	 => $keyword,
								'language_id' => $this->request->post['language_id'],
								'store_id'	=> $this->request->post['store_id'],
								'push' => ($push)?'route=product/category&path=' . $path:false

							);

							$total = $this->insertKeyword($data_insert, $total);
							$last_id = $row['category_id'];
						}
					}
					
					$json['last_id'] = $last_id;
					$json['progress'] = $this->progress_generate($count,$total_categories);
					$json['message'] = 'category: proccessed  ' . $count  . 'from ' . $total_categories;

					break;
				case 'product' :
					if ($update) {
						$this->model->deleteSeoUrl(array(
							'query' =>'product_id',
							'language_id' => $this->request->post['language_id'],
							'store_id'	=> $this->request->post['store_id']
							)
						);
					}
				
					$patterns = array_keys($this->allowed_product_patterns);
					$pattern = trim($this->db->escape($this->request->post['pattern']));

					$products = $this->model->getProducts($filter_data);
					$total_products = $this->model->getTotalProducts($filter_data);

					if ($products) {
						$count = count($products);
						foreach ($products as $row) {
/*							$first_symb = explode(' ', $row['m_name']);
							$f_s = array();
							foreach ($first_symb as $element) {
								if ($element)
									$f_s[] = $element[0];
							}
							$row['m_first'] = implode('', $f_s);
							if (!$row['m_first']) { $row['m_first'] = '_' . $row['m_first'];}
*/							
							$replaced = array(
								'[name]'	  => $row['name'],
								'[model]'	 => $row['model'],
								'[id]'		=> $row['product_id'],
								'[mpn]'		=> $row['mpn'],
								'[sku]'	   => $row['sku'],
								'[isbn]'	  => $row['isbn'],
								'[jan]'	   => $row['jan'],
								'[m_name]'	=> $row['m_name'],
								'[lang_code]' => $this->language_code,
								'[lang_id]'   => $this->request->post['language_id'],
								'[store_id]'  => $this->request->post['store_id'],
							);

							$replaced_new = array();
							foreach ($patterns as $key=>$value) {
								if (array_key_exists($value,$replaced)) {
									$replaced_new[$value] = $replaced[$value];
								} else {
									$replaced_new[$key] = '';
								}
							}
							
							$keyword = str_replace($patterns,$replaced_new, $pattern);
							
							if (preg_match_all('#\[(.*?)\]#',$keyword,$matches)) {
								
								foreach ($matches[0] as $index=>$key){
									$paterns[$key] = '';
									$option = explode('-',$matches[1][$index]);
									if (count($option) >1) {
										if ($option[0] == 'option') {
											$result = $this->db->query(
											"SELECT * FROM " . DB_PREFIX . "option_value_description ovd
											JOIN " . DB_PREFIX . "product_option po ON ovd.option_id = po.option_id
											WHERE ovd.language_id = " . (int)$this->request->post['language_id'] . "
											AND ovd.option_id = " . (int)$option[1]);
											if ($result->num_rows) {
												$paterns[$key] = $result->row['name'];
											}
										}
									}
								};
							$keyword = str_replace(array_keys($paterns),$paterns, $keyword);
							}
							
							$data_insert = array(
								'query'	   => 'product_id=' . (int)$row['product_id'], 
								'entity_id'   => $row['product_id'],
								'keyword'	 => $keyword,
								'language_id' => $this->request->post['language_id'],
								'store_id'	=> $this->request->post['store_id'],
								'push' => ($push)?'route=product/product&product_id=' . (int)$row['product_id']:false
							);
							$total = $this->insertKeyword($data_insert, $total, '');
							$last_id = $row['product_id'];
						}
					}

					$json['last_id'] = $last_id;
					$json['progress'] = $this->progress_generate($count,$total_products);
					$json['message'] = 'product: proccessed  ' . $count  . 'from ' . $total_products;

					break;
				case 'information' :

					if ($update) {
						$this->model->deleteSeoUrl(array(
							'query' =>'information_id',
							'language_id' => $this->request->post['language_id'],
							'store_id'	=> $this->request->post['store_id']
							)
						);
					}

					$informations = $this->model->getInformations($filter_data);
					
					$total_informations = $this->model->getTotalInformations($filter_data);
					
					$patterns = array_keys($this->allowed_information_patterns);
					$pattern = trim($this->db->escape($this->request->post['pattern']));

					if ($informations) {
						$count = count($informations);
						foreach ($informations as $row) {
							$replaced = array(
								'[name]'	 => $row['title'],
								'[id]'	   => $row['information_id'], 
								'[lang_code]' => $this->language_code,
								'[lang_id]'  => $this->request->post['language_id'],
								'[store_id]' => $this->request->post['store_id'],
							);

							$replaced_new = array();
							foreach ($patterns as $key=>$value) {
								if (array_key_exists($value,$replaced)) {
									$replaced_new[$value] = $replaced[$value];
								} else {
									$replaced_new[$key] = '';
								}
							}
							
							$keyword = str_replace($patterns,$replaced_new, $pattern);
							
							$data_insert = array(
								'query'	   => 'information_id=' . (int)$row['information_id'], 
								'entity_id'   => $row['information_id'],
								'keyword'	 => $keyword,
								'language_id' => $this->request->post['language_id'],
								'store_id'	=> $this->request->post['store_id'],
								'push' => ($push)?'route=information/information&information_id=' . (int)$row['information_id']:false
								
							);

							$total = $this->insertKeyword($data_insert, $total);
							$last_id = $row['information_id'];
						}
					}

					$json['last_id'] = $last_id;
					$json['progress'] = $this->progress_generate($count,$total_informations);
					$json['message'] = 'information: proccessed  ' . $count  . 'from ' . $total_informations;

					break;
				case 'blog' :

					if ($update) {
						$this->model->deleteSeoUrl(array(
							'query'	   => 'blog_id',
							'language_id' => $this->request->post['language_id'],
							'store_id'	=> $this->request->post['store_id']
							)
						);
					}

					$blogs = $this->model->getBlogs($filter_data);
					
					$total_blogs = $this->model->getTotalBlogs($filter_data);
					
					$patterns = array_keys(array(
						'[name]'	 => '',
						'[id]'	   => '',
						'[lang_code]'=> '',
						'[lang_id]'  => '',
						'[store_id]' => '',
					));

					$pattern = trim($this->db->escape($this->request->post['pattern']));

					if ($blogs) {
						$count = count($blogs);
						foreach ($blogs as $row) {
							$replaced = array(
								'[name]'	 => $row['title'],
								'[id]'	   => $row['information_id'], 
								'[lang_code]' => $this->language_code,
								'[lang_id]'  => $this->request->post['language_id'],
								'[store_id]' => $this->request->post['store_id'],
							);

							$replaced_new = array();
							foreach ($patterns as $key=>$value) {
								if (array_key_exists($value,$replaced)) {
									$replaced_new[$value] = $replaced[$value];
								} else {
									$replaced_new[$key] = '';
								}
							}
				
							$keyword = str_replace($patterns,$replaced_new, $pattern);
							
							$data_insert = array(
								'query'	   => 'blog_id=' . (int)$row['blog_id'], 
								'entity_id'   => $row['blog_id'],
								'keyword'	 => $keyword,
								'language_id' => $this->request->post['language_id'],
								'store_id'	=> $this->request->post['store_id'],
								'push' => false
								
							);

							$total = $this->insertKeyword($data_insert, $total);
							$last_id = $row['blog_id'];
						}
					}

					$json['last_id'] = $last_id;
					$json['progress'] = $this->progress_generate($count,$total_blogs);
					$json['message'] = 'blog: proccessed  ' . $count  . 'from ' . $total_blogs;

					break;
				case 'manufacturer' :
					if ($update) {
						$this->model->deleteSeoUrl(array(
							'query' =>'manufacturer_id',
							'language_id' => $this->request->post['language_id'],
							'store_id'	=> $this->request->post['store_id']
							)
						);
					}

					$manufacturers = $this->model->getManufacturers($filter_data);
					$total_manufacturers = $this->model->getTotalManufacturers($filter_data);
					$total = 0;

					$patterns = array_keys($this->allowed_manufacturer_patterns);
					$pattern = trim($this->db->escape($this->request->post['pattern']));

					if ($manufacturers) {
						$count = count($manufacturers);
						foreach ($manufacturers as $row) {
							$replaced = array(
								'[name]'	 => $row['name'],
								'[id]'	   => $row['manufacturer_id'], 
								'[lang_code]' => $this->language_code,
								'[lang_id]'  => $this->request->post['language_id'],
								'[store_id]' => $this->request->post['store_id'],
							);

							$replaced_new = array();
							foreach ($patterns as $key=>$value) {
								if (array_key_exists($value,$replaced)) {
									$replaced_new[$value] = $replaced[$value];
								} else {
									$replaced_new[$key] = '';
								}
							}
							
							$keyword = str_replace($patterns,$replaced_new, $pattern);

							$data_insert = array(
								'query'	   => 'manufacturer_id=' . (int)$row['manufacturer_id'], 
								'entity_id'   => $row['manufacturer_id'],
								'keyword'	 => $keyword,
								'language_id' => $this->request->post['language_id'],
								'store_id'	=> $this->request->post['store_id'],
								'push' => ($push)?'product/manufacturer/info&manufacturer_id=' . (int)$row['manufacturer_id']:false
							);


							$total = $this->insertKeyword($data_insert, $total);
							$last_id = $row['manufacturer_id'];
						}
					}

					$json['last_id'] = $last_id;
					$json['progress'] = $this->progress_generate($count,$total_manufacturers);
					$json['message'] = 'information: proccessed  ' . $count  . 'from ' . $total_manufacturers;
					break;
			}
			$json['error_slug'] = $this->error_slugs;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		} else {
			$json['progress'] = -100;
			$json['message'] = 'not access';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		}
	}

	public function clear() {
		$this->load->language($this->path);
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
		$this->cache->delete('seo_pro');
		$this->cache->delete('seo_url');
	
		$this->session->data['success'] = $this->language->get('text_success_clear');
		$this->response->redirect($this->makeUrl($this->path, $url));
	}

	public function delete() {
		$this->load->language($this->path);
		$url = '';

		$filter_allow = array(
			'filter_query'	   => false,
			'filter_type'		=> false,
			'filter_keyword'	 => false,
			'filter_language_id' => false,
			'filter_store_id'	=> false,

			'sort'  => 'ua.query',
			'order' => 'ASC',
		);

		foreach ($filter_allow as $filter_key=>$value) {
			if (isset($this->request->get[$filter_key])) {
				$url .= '&' . $filter_key . '=' . $this->request->get[$filter_key];
			}
		}

		if (isset($this->request->post['selected']) && $this->validate()) {
			foreach ($this->request->post['selected'] as $seo_url_id) {
				$this->model->deleteUrlAlias($seo_url_id);
			}
			$this->session->data['success'] = $this->language->get('text_success');
		}
		if (isset($this->request->get['redirect'])) {
			$this->response->redirect($this->makeUrl($this->path . '/check', 'check=' . $this->request->get['redirect']));
		} else {
			$this->response->redirect($this->makeUrl($this->path, $url));
		}
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', $this->path)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
   
	public function install() {
		$this->model->install();	  
	}
   
	public function uninstall() {
		$this->model->uninstall();	  
	}   

	private function breadcrumbs(&$data) {
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->makeUrl('common/dashboard')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->makeUrl('marketplace/extension' , 'type=module')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->makeUrl($this->path)
		);
	}
	
	private function nav($route,&$data) {
		$allow_nav =  array (
			$this->path => $this->language->get('text_nav_manager'),
			$this->path . '/tool' => $this->language->get('text_nav_tool'),
			$this->path . '/check' => $this->language->get('text_nav_check'),
//			$this->path . '/check_seo' => $this->language->get('text_nav_check_seo'),
			$this->path . '/setting' => $this->language->get('text_nav_setting'),
			$this->path . '/about' => $this->language->get('text_nav_about')
		);
		
		$data['navs'] = array();
		
		foreach ($allow_nav as $route_nav=>$text) {
			if ($route == $route_nav) $active = 'active'; else $active = '';
			$data['navs'][] = array(
			
				'href' => $this->makeUrl($route_nav),
				'text' => $text,
				'active' => $active
			);
		}
	}

	private function nav_check($check, &$data) {
		$data['nav_checks'] = array();
		foreach ($this->allow_nav_check as $check_key=>$text) {
			if ($check == 'check_key') {
				$active = 'active';
				$data['heading_title_check'] = $text['heading'];
			} else {
				$active = '';
			}
			$data['nav_checks'][] = array(
				'href' => $this->makeUrl($this->path . '/check', 'check=' . $check_key),
				'text' => $text['text'],
				'active' => $active
			);
		}

	}

	private function makeUrlScript($route, $arg='') {
		return str_replace('&amp;','&',$this->makeUrl($route, $arg));
	}
	
	private function makeUrl($route, $arg='') {
		if ($arg) {
			$arg = '&' . ltrim($arg, '&');
		}
		return $this->url->link($route, 'user_token=' . $this->session->data['user_token'] . $arg, true);	
	}

	private function footer($template, $data) {
		$template = $template?'/slasoft_seo_manager'.$template:'';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$data['user_token'] = $this->session->data['user_token'];
		$data['path_module'] = $this->path;

		$this->response->setOutput($this->load->view($this->path . $template, $data));
	}

	private function pagination($total, $page, $limit, $route, $url='') {
		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->makeUrl($route , $url . '&page={page}');
		return array (
			'pagination' => $pagination->render(),
			'results' => sprintf($this->language->get('text_pagination'), ($total) ? ((($page - 1) * $limit) + 1) : 0, (($page - 1) * $limit) > ($total - $limit) ?$total : ((($page - 1) * $limit) + $limit), $total, ceil($total / $limit))
		);
	}

	private function transliteration($keyword) {
		if ($this->config->get('slasoft_seo_manager_type') == 'URLify') {
			$keyword = URLify::filter(
				$keyword,
				$this->config->get('slasoft_seo_manager_limit_url')?(int)$this->config->get('slasoft_seo_manager_limit_url'):60,
				$this->language_code, 
				false, 
				true,
				true,
				true,
				$this->config->get('slasoft_seo_manager_separator')?$this->config->get('slasoft_seo_manager_separator'):'-'
			);
		} else {
			$keyword = $this->slugify->slugify($keyword);
		}
		return $keyword;
	}

	private function insertKeyword($data_insert, $total, $prefix='') {
		if ($data_insert['keyword']) {
			$keyword = html_entity_decode($data_insert['keyword'], ENT_QUOTES, 'UTF-8');

			$keyword = $this->transliteration($keyword);

			$slugs = $this->model->getExistingSlugs($keyword);
			$check_error = $this->config->get('slasoft_seo_manager_error');
			$skip = false;
			if ($slugs) {
				if ($check_error == 'ignore' ) {
					$this->error_slugs[] = array(
						'entity' => $data_insert['query'],
						'keyword' => $keyword,
						'action' => 'ignore',
					);
				} elseif ($check_error == 'skip') {
					$skip = true;
				} else {
					$keyword = $this->model->getUniqueSlugs($keyword , $data_insert,$this->config->get('slasoft_seo_manager_separator'));
					$this->error_slugs[] = array(
						'entity' => $data_insert['query'],
						'keyword' => $keyword,
						'action' => 'correct',
					);
				}
			}

			if (!$skip) {
				$data_insert['keyword'] = $keyword . $this->suffix;
				$last_id = $this->model->insertKeyword($data_insert,$prefix);
				$total++;
			}
		}
		return $total;
	}
	
}