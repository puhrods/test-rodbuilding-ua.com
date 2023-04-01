<?php
/********************************************************/
/*	@copyright	OCTemplates 2019.						*/
/*	@support	https://octemplates.net/				*/
/*	@license	LICENSE.txt								*/
/********************************************************/

class ControllerOCTemplatesModuleOctMegamenu extends Controller {
    private $error = [];

    public function index() {
        $this->load->language('octemplates/module/oct_megamenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('octemplates/module/oct_megamenu');

        $this->load->model('setting/setting');

	    $oct_megamenu_info = $this->model_setting_setting->getSetting('oct_megamenu');

	    if (!$oct_megamenu_info) {
            $this->response->redirect($this->url->link('octemplates/module/oct_megamenu/install', 'user_token=' . $this->session->data['user_token'], true));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
	        if (empty($this->request->post)) {
		        $this->request->post['oct_megamenu_status'] = 0;
		        $this->request->post['oct_megamenu_categories'] = 0;
		        $this->request->post['oct_megamenu_categories_icon'] = 0;
		        $this->request->post['oct_megamenu_categories_page'] = 0;
		        $this->request->post['oct_megamenu_mobile_categories'] = 0;
		        $this->request->post['oct_megamenu_mobile_categories_icon'] = 0;
		        $this->request->post['oct_megamenu_mobile_st_categories'] = 0;
		        $this->request->post['oct_megamenu_brands'] = 0;
		        $this->request->post['oct_megamenu_informations'] = 0;
		        $this->request->post['oct_megamenu_links'] = 0;
		        $this->request->post['oct_megamenu_blog'] = 0;
	        }

            $this->model_setting_setting->editSetting('oct_megamenu', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->cache->delete('octemplates');

            $this->response->redirect($this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getList();
    }

    public function add() {
        $this->load->language('octemplates/module/oct_megamenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('octemplates/module/oct_megamenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_octemplates_module_oct_megamenu->addMegamenu($this->request->post);

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

            $this->response->redirect($this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('octemplates/module/oct_megamenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('octemplates/module/oct_megamenu');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_octemplates_module_oct_megamenu->editMegamenu($this->request->get['megamenu_id'], $this->request->post);

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

            $this->response->redirect($this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('octemplates/module/oct_megamenu');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('octemplates/module/oct_megamenu');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $megamenu_id) {
                $this->model_octemplates_module_oct_megamenu->deleteMegamenu($megamenu_id);
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

            $this->response->redirect($this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
	    $this->document->addScript('view/javascript/octemplates/bootstrap-notify/bootstrap-notify.min.js');
		$this->document->addScript('view/javascript/octemplates/oct_main.js');
	    $this->document->addStyle('view/stylesheet/oct_ultrastore.css');

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'ocmm.sort_order';
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

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true)
        ];

        $data['action'] = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'], true);
        $data['add']    = $this->url->link('octemplates/module/oct_megamenu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete'] = $this->url->link('octemplates/module/oct_megamenu/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

        $data['oct_megamenus'] = [];

        $filter_data = [
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        ];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $oct_megamenu_total = $this->model_octemplates_module_oct_megamenu->getTotalMegamenus();

        $results = $this->model_octemplates_module_oct_megamenu->getMegamenus($filter_data);

        foreach ($results as $result) {
            $data['oct_megamenus'][] = [
                'megamenu_id' => $result['megamenu_id'],
                'title' => $result['title'],
                'link' => $result['link'],
                'sort_order' => $result['sort_order'],
                'edit' => $this->url->link('octemplates/module/oct_megamenu/edit', 'user_token=' . $this->session->data['user_token'] . '&megamenu_id=' . $result['megamenu_id'] . $url, true)
            ];
        }

        	$oct_megamenu_settings = [
	        	'oct_megamenu_status',
	        	'oct_megamenu_categories',
	        	'oct_megamenu_categories_page',
	        	'oct_megamenu_categories_icon',
	        	'oct_megamenu_categories_limit',
	        	'oct_megamenu_mobile_categories',
	        	'oct_megamenu_mobile_categories_icon',
	        	'oct_megamenu_mobile_st_categories',
	        	'oct_megamenu_brands',
	        	'oct_megamenu_informations',
	        	'oct_megamenu_links',
	        	'oct_megamenu_blog',
                'oct_megamenu_title',
	        	'oct_megamenu_mobile_title',
        	];

        	foreach ($oct_megamenu_settings as $oct_megamenu_setting) {
	        	if (isset($this->request->post[$oct_megamenu_setting])) {
	            $data[$oct_megamenu_setting] = $this->request->post[$oct_megamenu_setting];
	        } else {
	            $data[$oct_megamenu_setting] = $this->config->get($oct_megamenu_setting);
	        }
        	}

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
            $data['selected'] = [];
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

        $data['sort_title']      = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . '&sort=otmmd.title' . $url, true);
        $data['sort_link']       = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . '&sort=otmm.link' . $url, true);
        $data['sort_sort_order'] = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . '&sort=otmm.sort_order' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination        = new Pagination();
        $pagination->total = $oct_megamenu_total;
        $pagination->page  = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url   = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($oct_megamenu_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($oct_megamenu_total - $this->config->get('config_limit_admin'))) ? $oct_megamenu_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $oct_megamenu_total, ceil($oct_megamenu_total / $this->config->get('config_limit_admin')));

        $data['sort']  = $sort;
        $data['order'] = strtolower($order);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('octemplates/module/oct_megamenu_list', $data));
    }

    protected function getForm() {
        //Add Codemirror Styles && Scripts
        $this->document->addScript('view/javascript/codemirror/lib/codemirror.js');
        $this->document->addScript('view/javascript/codemirror/lib/xml.js');
        $this->document->addScript('view/javascript/codemirror/lib/formatting.js');
        $this->document->addStyle('view/javascript/codemirror/lib/codemirror.css');
        $this->document->addStyle('view/javascript/codemirror/theme/monokai.css');

        //Add Summernote Styles && Scripts
        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/summernote-image-attributes.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');
        $this->document->addStyle('view/javascript/summernote/summernote.css');

		//Add Spectrum
		$this->document->addStyle('view/javascript/octemplates/spectrum/spectrum.css');
		$this->document->addScript('view/javascript/octemplates/spectrum/spectrum.js');
		$this->document->addScript('view/javascript/octemplates/bootstrap-notify/bootstrap-notify.min.js');
		$this->document->addScript('view/javascript/octemplates/oct_main.js');
		$this->document->addStyle('view/stylesheet/oct_ultrastore.css');

        $data['text_form']     = !isset($this->request->get['megamenu_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['heading_title'] = $this->language->get('heading_title');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['link'])) {
            $data['error_link'] = $this->error['link'];
        } else {
            $data['error_link'] = '';
        }

        if (isset($this->error['title'])) {
            $data['error_title'] = $this->error['title'];
        } else {
            $data['error_title'] = [];
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

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true)
        ];

        if (isset($this->request->get['megamenu_id'])) {
            $data['breadcrumbs'][] = [
                'text' => $this->language->get('text_edit'),
                'href' => $this->url->link('octemplates/module/oct_megamenu/edit', 'user_token=' . $this->session->data['user_token'] . '&megamenu_id=' . $this->request->get['megamenu_id'] . $url, true)
            ];
        }

        if (!isset($this->request->get['megamenu_id'])) {
            $data['action'] = $this->url->link('octemplates/module/oct_megamenu/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('octemplates/module/oct_megamenu/edit', 'user_token=' . $this->session->data['user_token'] . '&megamenu_id=' . $this->request->get['megamenu_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'] . $url, true);

        if (isset($this->request->get['megamenu_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $oct_megamenu_info = $this->model_octemplates_module_oct_megamenu->getMegamenu($this->request->get['megamenu_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->post['oct_megamenu_description'])) {
            $data['oct_megamenu_description'] = $this->request->post['oct_megamenu_description'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['oct_megamenu_description'] = $this->model_octemplates_module_oct_megamenu->getMegamenuDescriptions($this->request->get['megamenu_id']);
        } else {
            $data['oct_megamenu_description'] = [];
        }

        $this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        // Image
		if (isset($this->request->post['oct_megamenu_banner_image'])) {
			$data['oct_megamenu_banner_image'] = $this->request->post['oct_megamenu_banner_image'];
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['oct_megamenu_banner_image'], 100, 100);
		} elseif (!empty($oct_megamenu_info) && (isset($oct_megamenu_info['banner_image']) && !empty($oct_megamenu_info['banner_image']))) {
			$data['oct_megamenu_banner_image'] = $oct_megamenu_info['banner_image'];
			$data['thumb'] = $this->model_tool_image->resize($oct_megamenu_info['banner_image'], 100, 100);
		} else {
			$data['oct_megamenu_banner_image'] = '';
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

        if (isset($this->request->post['oct_megamenu_banner_image_color'])) {
            $data['oct_megamenu_banner_image_color'] = $this->request->post['oct_megamenu_banner_image_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_color'] = $oct_megamenu_info['banner_image_color'];
        } else {
            $data['oct_megamenu_banner_image_color'] = 'rgb(53, 62, 72)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_text_color'])) {
            $data['oct_megamenu_banner_image_text_color'] = $this->request->post['oct_megamenu_banner_image_text_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_text_color'] = $oct_megamenu_info['banner_image_text_color'];
        } else {
            $data['oct_megamenu_banner_image_text_color'] = 'rgb(222, 222, 222)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_title_color'])) {
            $data['oct_megamenu_banner_image_title_color'] = $this->request->post['oct_megamenu_banner_image_title_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_title_color'] = $oct_megamenu_info['banner_image_title_color'];
        } else {
            $data['oct_megamenu_banner_image_title_color'] = 'rgb(222, 222, 222)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_button_color'])) {
            $data['oct_megamenu_banner_image_button_color'] = $this->request->post['oct_megamenu_banner_image_button_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_button_color'] = $oct_megamenu_info['banner_image_button_color'];
        } else {
            $data['oct_megamenu_banner_image_button_color'] = 'rgb(102, 102, 102)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_button_hover_color'])) {
            $data['oct_megamenu_banner_image_button_hover_color'] = $this->request->post['oct_megamenu_banner_image_button_hover_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_button_hover_color'] = $oct_megamenu_info['banner_image_button_hover_color'];
        } else {
            $data['oct_megamenu_banner_image_button_hover_color'] = 'rgb(128, 212, 3)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_link_color'])) {
            $data['oct_megamenu_banner_image_link_color'] = $this->request->post['oct_megamenu_banner_image_link_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_link_color'] = $oct_megamenu_info['banner_image_link_color'];
        } else {
            $data['oct_megamenu_banner_image_link_color'] = 'rgb(255, 255, 255)';
        }

        if (isset($this->request->post['oct_megamenu_banner_image_link_hover_color'])) {
            $data['oct_megamenu_banner_image_link_hover_color'] = $this->request->post['oct_megamenu_banner_image_link_hover_color'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['oct_megamenu_banner_image_link_hover_color'] = $oct_megamenu_info['banner_image_link_hover_color'];
        } else {
            $data['oct_megamenu_banner_image_link_hover_color'] = 'rgb(255, 255, 255)';
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['store'])) {
            $data['menu_stores'] = $this->request->post['store'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['menu_stores'] = $this->model_octemplates_module_oct_megamenu->getMegamenuStores($this->request->get['megamenu_id']);
        } else {
            $data['menu_stores'] = [
                0
            ];
        }

        $this->load->model('customer/customer_group');

        $data['customer_groups'] = [];

        foreach ($this->model_customer_customer_group->getCustomerGroups() as $customer_group) {
            $data['customer_groups'][] = [
                'customer_group_id' => $customer_group['customer_group_id'],
                'name' => $customer_group['name']
            ];
        }

        if (isset($this->request->post['customer_group'])) {
            $data['menu_customer_groups'] = $this->request->post['customer_group'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['menu_customer_groups'] = $this->model_octemplates_module_oct_megamenu->getMegamenuCustomerGroups($this->request->get['megamenu_id']);
        } else {
            $data['menu_customer_groups'] = [
                0
            ];
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['status'] = $oct_megamenu_info['status'];
        } else {
            $data['status'] = true;
        }

        if (isset($this->request->post['info_text'])) {
            $data['info_text'] = $this->request->post['info_text'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['info_text'] = $oct_megamenu_info['info_text'];
        } else {
            $data['info_text'] = false;
        }

        if (isset($this->request->post['sub_categories'])) {
            $data['sub_categories'] = $this->request->post['sub_categories'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['sub_categories'] = $oct_megamenu_info['sub_categories'];
        } else {
            $data['sub_categories'] = false;
        }

        if (isset($this->request->post['open_link_type'])) {
            $data['open_link_type'] = $this->request->post['open_link_type'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['open_link_type'] = $oct_megamenu_info['open_link_type'];
        } else {
            $data['open_link_type'] = false;
        }

        if (isset($this->request->post['show_img'])) {
            $data['show_img'] = $this->request->post['show_img'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['show_img'] = $oct_megamenu_info['show_img'];
        } else {
            $data['show_img'] = true;
        }

        if (isset($this->request->post['image'])) {
            $data['image'] = $this->request->post['image'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['image'] = $oct_megamenu_info['image'];
        } else {
            $data['image'] = '';
        }

        if (isset($this->request->post['item_type'])) {
            $data['item_type'] = $this->request->post['item_type'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['item_type'] = $oct_megamenu_info['item_type'];
        } else {
            $data['item_type'] = 0;
        }

        if (isset($this->request->post['display_type'])) {
            $data['display_type'] = $this->request->post['display_type'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['display_type'] = $oct_megamenu_info['display_type'];
        } else {
            $data['display_type'] = '';
        }

        if (isset($this->request->post['img_width'])) {
            $data['img_width'] = $this->request->post['img_width'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['img_width'] = $oct_megamenu_info['img_width'];
        } else {
            $data['img_width'] = 100;
        }

        if (isset($this->request->post['img_height'])) {
            $data['img_height'] = $this->request->post['img_height'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['img_height'] = $oct_megamenu_info['img_height'];
        } else {
            $data['img_height'] = 100;
        }

        if (isset($this->request->post['limit_item'])) {
            $data['limit_item'] = $this->request->post['limit_item'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['limit_item'] = $oct_megamenu_info['limit_item'];
        } else {
            $data['limit_item'] = 5;
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($oct_megamenu_info)) {
            $data['sort_order'] = $oct_megamenu_info['sort_order'];
        } else {
            $data['sort_order'] = '';
        }

        $this->load->model('catalog/product');

        $data['products'] = [];

        if (isset($this->request->post['oct_megamenu_products'])) {
            $products = $this->request->post['oct_megamenu_products'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $products = $this->model_octemplates_module_oct_megamenu->getMegamenuProduct($this->request->get['megamenu_id']);
        } else {
            $products = [];
        }

        if ($products) {
            foreach ($products as $product_id) {
                $product_info = $this->model_catalog_product->getProduct($product_id);

                if ($product_info) {
                    $data['products'][] = [
                        'product_id' => $product_info['product_id'],
                        'name' => $product_info['name']
                    ];
                }
            }
        }

        $categories = $this->model_octemplates_module_oct_megamenu->getCategories();

        $data['categories'] = $this->getAllCategories($categories);

        if (isset($this->request->post['oct_megamenu_categories'])) {
            $data['category_id'] = $this->request->post['oct_megamenu_categories'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['category_id'] = $this->model_octemplates_module_oct_megamenu->getMegamenuCategory($this->request->get['megamenu_id']);
        } else {
            $data['category_id'] = [];
        }

		if ($this->config->get('oct_blogsettings_status')) {
			$blog_categories = $this->model_octemplates_module_oct_megamenu->getBlogCategories();

			$data['blog_categories'] = $this->getAllBlogCategories($blog_categories);
		}

        if (isset($this->request->post['oct_megamenu_blogcategories'])) {
            $data['blogcategory_id'] = $this->request->post['oct_megamenu_blogcategories'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['blogcategory_id'] = $this->model_octemplates_module_oct_megamenu->getMegamenuBlogCategory($this->request->get['megamenu_id']);
        } else {
            $data['blogcategory_id'] = [];
        }

        $data['manufacturers'] = $this->model_octemplates_module_oct_megamenu->getManufacturers([
            'sort' => 'name',
            'order' => 'ASC'
        ]);

        if (isset($this->request->post['oct_megamenu_manufacturers'])) {
            $data['manufacturer_id'] = $this->request->post['oct_megamenu_manufacturers'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['manufacturer_id'] = $this->model_octemplates_module_oct_megamenu->getMegamenuManufacturer($this->request->get['megamenu_id']);
        } else {
            $data['manufacturer_id'] = [];
        }

        $data['informations'] = $this->model_octemplates_module_oct_megamenu->getInformations([
            'sort' => 'id.title',
            'order' => 'ASC'
        ]);

        if (isset($this->request->post['oct_megamenu_informations'])) {
            $data['information_id'] = $this->request->post['oct_megamenu_informations'];
        } elseif (isset($this->request->get['megamenu_id'])) {
            $data['information_id'] = $this->model_octemplates_module_oct_megamenu->getMegamenuInformation($this->request->get['megamenu_id']);
        } else {
            $data['information_id'] = [];
        }

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('octemplates/module/oct_megamenu_form', $data));
    }

    private function getAllCategories($categories, $parent_id = 0, $parent_name = '') {
        $output = [];

        if (array_key_exists($parent_id, $categories)) {
            if ($parent_name != '') {
                $parent_name .= ' &gt; ';
            }

            foreach ($categories[$parent_id] as $category) {
                $output[$category['category_id']] = [
                    'category_id' => $category['category_id'],
                    'name' => $parent_name . $category['name']
                ];

                $output += $this->getAllCategories($categories, $category['category_id'], $parent_name . $category['name']);
            }
        }

        uasort($output, [$this, 'sortByName']);

        return $output;
    }

    private function getAllBlogCategories($blog_categories, $parent_id = 0, $parent_name = '') {
        $output = [];

        if (array_key_exists($parent_id, $blog_categories)) {
            if ($parent_name != '') {
                $parent_name .= ' &gt; ';
            }

            foreach ($blog_categories[$parent_id] as $blogcategory) {
                $output[$blogcategory['blogcategory_id']] = [
                    'blogcategory_id' => $blogcategory['blogcategory_id'],
                    'name' => $parent_name . $blogcategory['name']
                ];

                $output += $this->getAllBlogCategories($blog_categories, $blogcategory['blogcategory_id'], $parent_name . $blogcategory['name']);
            }
        }

        uasort($output, [$this, 'sortByName']);

        return $output;
    }

    function sortByName($a, $b) {
        return strcmp($a['name'], $b['name']);
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'octemplates/module/oct_megamenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        foreach ($this->request->post['oct_megamenu_description'] as $language_id => $value) {
            if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
                $this->error['title'][$language_id] = $this->language->get('error_title');
            }
        }

        if ($this->error && !isset($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_warning');
        }

        return !$this->error;
    }

    public function install() {
        $this->load->language('octemplates/module/oct_megamenu');

        $this->load->model('setting/setting');
        $this->load->model('user/user_group');
        $this->load->model('octemplates/module/oct_megamenu');

        $this->model_octemplates_module_oct_megamenu->makeDB();

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/module/oct_megamenu');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/module/oct_megamenu');

        $this->model_setting_setting->editSetting('oct_megamenu', [
            'oct_megamenu_status' => '1',
            'oct_megamenu_categories' => '1',
            'oct_megamenu_categories_page' => '0',
            'oct_megamenu_mobile_categories' => '0',
            'oct_megamenu_brands' => '0',
            'oct_megamenu_informations' => '0',
            'oct_megamenu_links' => '0',
            'oct_megamenu_mobile_st_categories' => '1',
            'oct_megamenu_blog' => '1',
            'oct_megamenu_title' => [],
	        'oct_megamenu_mobile_title' => [],
        ]);

        $this->session->data['success'] = $this->language->get('text_success_install');

        $this->response->redirect($this->url->link('octemplates/module/oct_megamenu', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function uninstall() {
        $this->load->model('extension/extension');
        $this->load->model('setting/setting');
        $this->load->model('octemplates/module/oct_megamenu');
        $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'octemplates/module/oct_megamenu');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'octemplates/module/oct_megamenu');

        $this->model_octemplates_module_oct_megamenu->removeDB();

        $this->model_setting_setting->deleteSetting('oct_megamenu');
    }


    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'octemplates/module/oct_megamenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'octemplates/module/oct_megamenu')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
