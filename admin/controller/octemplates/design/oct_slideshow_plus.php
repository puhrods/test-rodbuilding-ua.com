<?php
/**************************************************************/
/*	@copyright	OCTemplates 2019.							  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ControllerOCTemplatesDesignOctSlideshowPlus extends Controller {
    private $error = [];
    
    public function index() {
        $this->load->language('octemplates/design/oct_slideshow_plus');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('octemplates/design/oct_slideshow_plus');
        
        $this->getList();
    }
    
    public function add() {
        $this->load->language('octemplates/design/oct_slideshow_plus');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('octemplates/design/oct_slideshow_plus');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_octemplates_design_oct_slideshow_plus->addSlideshow($this->request->post);
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
            
            $this->response->redirect($this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        
        $this->getForm();
    }
    
    public function edit() {
        $this->load->language('octemplates/design/oct_slideshow_plus');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('octemplates/design/oct_slideshow_plus');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_octemplates_design_oct_slideshow_plus->editSlideshow($this->request->get['slideshow_id'], $this->request->post);
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
            
            $this->response->redirect($this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        
        $this->getForm();
    }
    
    public function delete() {
        $this->load->language('octemplates/design/oct_slideshow_plus');
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $this->load->model('octemplates/design/oct_slideshow_plus');
        
        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $slideshow_id) {
                $this->model_octemplates_design_oct_slideshow_plus->deleteSlideshow($slideshow_id);
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
            
            $this->response->redirect($this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
        
        $data['breadcrumbs']   = [];
        
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true)
        ];
        
        $data['add']                 = $this->url->link('octemplates/design/oct_slideshow_plus/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['delete']              = $this->url->link('octemplates/design/oct_slideshow_plus/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['oct_slideshow_pluss'] = [];
        
        $filter_data = [
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        ];
        
        $oct_slideshow_plus_total = $this->model_octemplates_design_oct_slideshow_plus->getTotalSlideshows();
        $results                  = $this->model_octemplates_design_oct_slideshow_plus->getSlideshows($filter_data);
        
        foreach ($results as $result) {
            $data['oct_slideshow_pluss'][] = [
                'slideshow_id' => $result['slideshow_id'],
                'name' => $result['name'],
                'status' => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
                'edit' => $this->url->link('octemplates/design/oct_slideshow_plus/edit', 'user_token=' . $this->session->data['user_token'] . '&slideshow_id=' . $result['slideshow_id'] . $url, true)
            ];
        }
        
        $data['heading_title']   = $this->language->get('heading_title');
        $data['text_list']       = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm']    = $this->language->get('text_confirm');
        $data['column_name']     = $this->language->get('column_name');
        $data['column_status']   = $this->language->get('column_status');
        $data['column_action']   = $this->language->get('column_action');
        $data['button_add']      = $this->language->get('button_add');
        $data['button_edit']     = $this->language->get('button_edit');
        $data['button_delete']   = $this->language->get('button_delete');
        
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
            $data['selected'] = (array) $this->request->post['selected'];
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
        
        $data['sort_name']   = $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
        $data['sort_status'] = $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
        
        $url = '';
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }
        
        $pagination          = new Pagination();
        $pagination->total   = $oct_slideshow_plus_total;
        $pagination->page    = $page;
        $pagination->limit   = $this->config->get('config_limit_admin');
        $pagination->url     = $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}
		', true);
        
        $data['pagination']  = $pagination->render();
        $data['results']     = sprintf($this->language->get('text_pagination'), ($oct_slideshow_plus_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($oct_slideshow_plus_total - $this->config->get('config_limit_admin'))) ? $oct_slideshow_plus_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $oct_slideshow_plus_total, ceil($oct_slideshow_plus_total / $this->config->get('config_limit_admin')));
        
        $data['sort']        = $sort;
        $data['order']       = $order;
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('octemplates/design/oct_slideshow_plus_list', $data));
    }
    
    protected function getForm() {
	    $this->document->addScript('view/javascript/octemplates/bootstrap-notify/bootstrap-notify.min.js');
		$this->document->addScript('view/javascript/octemplates/oct_main.js');
		$this->document->addStyle('view/stylesheet/oct_ultrastore.css');
		
		//Add Spectrum
		$this->document->addStyle('view/javascript/octemplates/spectrum/spectrum.css');
		$this->document->addScript('view/javascript/octemplates/spectrum/spectrum.js');
	    
        $data['text_form'] = !isset($this->request->get['slideshow_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        
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
        
        if (isset($this->error['oct_slideshow_plus_image'])) {
            $data['error_oct_slideshow_plus_image'] = $this->error['oct_slideshow_plus_image'];
        } else {
            $data['error_oct_slideshow_plus_image'] = [];
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
        
        $data['breadcrumbs']   = [];
        
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true)
        ];
        
        if (!isset($this->request->get['slideshow_id'])) {
            $data['action'] = $this->url->link('octemplates/design/oct_slideshow_plus/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('octemplates/design/oct_slideshow_plus/edit', 'user_token=' . $this->session->data['user_token'] . '&slideshow_id=' . $this->request->get['slideshow_id'] . $url, true);
        }
        
        $data['cancel'] = $this->url->link('octemplates/design/oct_slideshow_plus', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
        if (isset($this->request->get['slideshow_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $oct_slideshow_plus_info = $this->model_octemplates_design_oct_slideshow_plus->getSlideshow($this->request->get['slideshow_id']);
            $oct_slideshow_plus_info_banner = $this->model_octemplates_design_oct_slideshow_plus->getSlideshowBanner($this->request->get['slideshow_id']);
        }

        $data['user_token'] = $this->session->data['user_token'];
        
        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['name'] = $oct_slideshow_plus_info['name'];
        } else {
            $data['name'] = '';
        }
        
        if (isset($this->request->post['pag_background'])) {
            $data['pag_background'] = $this->request->post['pag_background'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['pag_background'] = $oct_slideshow_plus_info['pag_background'];
        } else {
            $data['pag_background'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b1_button_background'])) {
            $data['b1_button_background'] = $this->request->post['b1_button_background'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_button_background'])) {
            $data['b1_button_background'] = $oct_slideshow_plus_info_banner['b1_button_background'];
        } else {
            $data['b1_button_background'] = 'rgb(113, 190, 0)';
        }
		
        if (isset($this->request->post['b1_button_background_hover'])) {
            $data['b1_button_background_hover'] = $this->request->post['b1_button_background_hover'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_button_background_hover'])) {
            $data['b1_button_background_hover'] = $oct_slideshow_plus_info_banner['b1_button_background_hover'];
        } else {
            $data['b1_button_background_hover'] = 'rgb(97, 97, 97)';
        }
		
        if (isset($this->request->post['b1_button_color'])) {
            $data['b1_button_color'] = $this->request->post['b1_button_color'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_button_color'])) {
            $data['b1_button_color'] = $oct_slideshow_plus_info_banner['b1_button_color'];
        } else {
            $data['b1_button_color'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b1_button_color_hover'])) {
            $data['b1_button_color_hover'] = $this->request->post['b1_button_color_hover'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_button_color_hover'])) {
            $data['b1_button_color_hover'] = $oct_slideshow_plus_info_banner['b1_button_color_hover'];
        } else {
            $data['b1_button_color_hover'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b1_title_background'])) {
            $data['b1_title_background'] = $this->request->post['b1_title_background'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_title_background'])) {
            $data['b1_title_background'] = $oct_slideshow_plus_info_banner['b1_title_background'];
        } else {
            $data['b1_title_background'] = 'rgba(48, 54, 61, 0.8)';
        }
		
        if (isset($this->request->post['b1_title_color'])) {
            $data['b1_title_color'] = $this->request->post['b1_title_color'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b1_title_color'])) {
            $data['b1_title_color'] = $oct_slideshow_plus_info_banner['b1_title_color'];
        } else {
            $data['b1_title_color'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b2_button_background'])) {
            $data['b2_button_background'] = $this->request->post['b2_button_background'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_button_background'])) {
            $data['b2_button_background'] = $oct_slideshow_plus_info_banner['b2_button_background'];
        } else {
            $data['b2_button_background'] = 'rgb(113, 190, 0)';
        }
		
        if (isset($this->request->post['b2_button_background_hover'])) {
            $data['b2_button_background_hover'] = $this->request->post['b2_button_background_hover'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_button_background_hover'])) {
            $data['b2_button_background_hover'] = $oct_slideshow_plus_info_banner['b2_button_background_hover'];
        } else {
            $data['b2_button_background_hover'] = 'rgb(97, 97, 97)';
        }
		
        if (isset($this->request->post['b2_button_color'])) {
            $data['b2_button_color'] = $this->request->post['b2_button_color'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_button_color'])) {
            $data['b2_button_color'] = $oct_slideshow_plus_info_banner['b2_button_color'];
        } else {
            $data['b2_button_color'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b2_button_color_hover'])) {
            $data['b2_button_color_hover'] = $this->request->post['b2_button_color_hover'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_button_color_hover'])) {
            $data['b2_button_color_hover'] = $oct_slideshow_plus_info_banner['b2_button_color_hover'];
        } else {
            $data['b2_button_color_hover'] = 'rgb(255, 255, 255)';
        }
		
        if (isset($this->request->post['b2_title_background'])) {
            $data['b2_title_background'] = $this->request->post['b2_title_background'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_title_background'])) {
            $data['b2_title_background'] = $oct_slideshow_plus_info_banner['b2_title_background'];
        } else {
            $data['b2_title_background'] = 'rgba(48, 54, 61, 0.8)';
        }
		
        if (isset($this->request->post['b2_title_color'])) {
            $data['b2_title_color'] = $this->request->post['b2_title_color'];
        } else if (!empty($oct_slideshow_plus_info_banner) && !empty($oct_slideshow_plus_info_banner['b2_title_color'])) {
            $data['b2_title_color'] = $oct_slideshow_plus_info_banner['b2_title_color'];
        } else {
            $data['b2_title_color'] = 'rgb(255, 255, 255)';
        }
        
        if (isset($this->request->post['pag_background_active'])) {
            $data['pag_background_active'] = $this->request->post['pag_background_active'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['pag_background_active'] = $oct_slideshow_plus_info['pag_background_active'];
        } else {
            $data['pag_background_active'] = 'rgb(113, 190, 0)';
        }
		
        if (isset($this->request->post['status_additional_banners'])) {
            $data['status_additional_banners'] = $this->request->post['status_additional_banners'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['status_additional_banners'] = $oct_slideshow_plus_info['status_additional_banners'];
        } else {
            $data['status_additional_banners'] = 1;
        }
        
        if (isset($this->request->post['position_banners'])) {
            $data['position_banners'] = $this->request->post['position_banners'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['position_banners'] = $oct_slideshow_plus_info['position_banners'];
        } else {
            $data['position_banners'] = 1;
        }
        
        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } else if (!empty($oct_slideshow_plus_info)) {
            $data['status'] = $oct_slideshow_plus_info['status'];
        } else {
            $data['status'] = true;
        }
        
        $this->load->model('localisation/language');
        
        $data['languages'] = $this->model_localisation_language->getLanguages();
        
        $this->load->model('tool/image');
		
        if (isset($this->request->post['oct_slideshow_plus_image']) && isset($this->request->post['oct_slideshow_plus_banner_description'])) {
            $oct_slideshow_plus_images = $this->request->post['oct_slideshow_plus_image'];
			$oct_slideshow_plus_banners = $this->request->post['oct_slideshow_plus_banner_description'];
		} else if (isset($this->request->get['slideshow_id'])) {
            $oct_slideshow_plus_images = $this->model_octemplates_design_oct_slideshow_plus->getSlideshowImages($this->request->get['slideshow_id']);
            $oct_slideshow_plus_banners = $this->model_octemplates_design_oct_slideshow_plus->getSlideshowBanners($this->request->get['slideshow_id']);
        } else {
            $oct_slideshow_plus_images = [];
            $oct_slideshow_plus_banners = [];
        }
		
        $data['oct_slideshow_plus_images'] = [];

        foreach ($oct_slideshow_plus_images as $oct_slideshow_plus_image) {
            if (is_file(DIR_IMAGE . $oct_slideshow_plus_image['image'])) {
                $image = $oct_slideshow_plus_image['image'];
                $thumb = $oct_slideshow_plus_image['image'];
            } else {
                $image = '';
                $thumb = 'no_image.png';
            }
            
            $data['oct_slideshow_plus_images'][] = [
                'oct_slideshow_plus_image_description' => $oct_slideshow_plus_image['oct_slideshow_plus_image_description'],
                'image' => $image,
                'thumb' => $this->model_tool_image->resize($thumb, 100, 100),
                'background_color' => $oct_slideshow_plus_image['background_color'] ? $oct_slideshow_plus_image['background_color'] : 'rgb(53, 62, 72)',
                'title_color' => $oct_slideshow_plus_image['title_color'] ? $oct_slideshow_plus_image['title_color'] : 'rgb(255, 255, 255)',
                'text_color' => $oct_slideshow_plus_image['text_color'] ? $oct_slideshow_plus_image['text_color'] : 'rgb(222, 222, 222)',
                'button_color' => $oct_slideshow_plus_image['button_color'] ? $oct_slideshow_plus_image['button_color'] : 'rgb(255, 255, 255)',
                'button_background' => $oct_slideshow_plus_image['button_background'] ? $oct_slideshow_plus_image['button_background'] : 'rgb(136, 136, 136)',
                'button_color_hover' => $oct_slideshow_plus_image['button_color_hover'] ? $oct_slideshow_plus_image['button_color_hover'] : 'rgb(255, 255, 255)',
                'button_background_hover' => $oct_slideshow_plus_image['button_background_hover'] ? $oct_slideshow_plus_image['button_background_hover'] : 'rgb(184, 184, 184)',
                'sort_order' => $oct_slideshow_plus_image['sort_order']
            ];
        }
		
		if (isset($oct_slideshow_plus_banners['b1_image']) && is_file(DIR_IMAGE . $oct_slideshow_plus_banners['b1_image'])) {
			$b1_image = $oct_slideshow_plus_banners['b1_image'];
			$b1_thumb = $oct_slideshow_plus_banners['b1_image'];
		} else {
			$b1_image = '';
			$b1_thumb = 'no_image.png';
		}
		
		if (isset($oct_slideshow_plus_banners['b2_image']) && is_file(DIR_IMAGE . $oct_slideshow_plus_banners['b2_image'])) {
			$b2_image = $oct_slideshow_plus_banners['b2_image'];
			$b2_thumb = $oct_slideshow_plus_banners['b2_image'];
		} else {
			$b2_image = '';
			$b2_thumb = 'no_image.png';
		}
		
		$data['oct_slideshow_plus_banner_description'] = isset($oct_slideshow_plus_banners['oct_slideshow_plus_banner_description']) ? $oct_slideshow_plus_banners['oct_slideshow_plus_banner_description'] : '';
		
		$data['b1_image'] = $b1_image;
		$data['b1_thumb'] = $this->model_tool_image->resize($b1_thumb, 100, 100);
		$data['b2_image'] = $b2_image;
		$data['b2_thumb'] = $this->model_tool_image->resize($b2_thumb, 100, 100);

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
        
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('octemplates/design/oct_slideshow_plus_form', $data));
    }
    
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'octemplates/design/oct_slideshow_plus')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
            $this->error['name'] = $this->language->get('error_name');
        }
        
        if (isset($this->request->post['oct_slideshow_plus_image'])) {
            foreach ($this->request->post['oct_slideshow_plus_image'] as $slideshow_image_id => $oct_slideshow_plus_image) {
                foreach ($oct_slideshow_plus_image['oct_slideshow_plus_image_description'] as $language_id => $oct_slideshow_plus_image_description) {
                    if ((utf8_strlen($oct_slideshow_plus_image_description['title']) < 2) || (utf8_strlen($oct_slideshow_plus_image_description['title']) > 64)) {
                        $this->error['oct_slideshow_plus_image'][$slideshow_image_id][$language_id] = $this->language->get('error_title');
                    }
                }
            }
        }
        
        return !$this->error;
    }
    
    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'octemplates/design/oct_slideshow_plus')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        
        return !$this->error;
    }
}