<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerCatalogBundleExpertWidget extends Controller {
	private $error = array();

    private $token_name = array();
    private $token_value = array();
    private $path_extension_module = '';

    public function __construct($registry) {

        parent::__construct($registry);

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->token_name = 'token=';
            $this->token_value = $this->session->data['token'];
        }else{
            $this->token_name = 'user_token=';
            $this->token_value = $this->session->data['user_token'];
        }

        if (version_compare($this->bundle_expert->OC_VERSION, '2.3.0.2', '<')) {
            $this->path_extension_module = 'extension/module';
        }else {
            if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
                $this->path_extension_module = 'extension/extension';
            } else {
                $this->path_extension_module = 'marketplace/extension';
            }
        }
    }
    
	public function index() {
        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('catalog/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $this->load->language('catalog/product');
		$this->load->language('catalog/bundle_expert_widget');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/bundle_expert_widget');

        $this->load->model('catalog/bundle_expert');

        if(!$this->model_catalog_bundle_expert->isModuleInstalled()) {
            $this->response->redirect($this->url->link('common/dashboard', $this->token_name . $this->token_value . '', 'SSL'));
        }

		$this->getList();
	}

	public function add() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/bundle_expert_widget');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_bundle_expert_widget->addWidget($this->request->post);

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

			$this->response->redirect($this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/bundle_expert_widget');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_bundle_expert_widget->editWidget($this->request->get['widget_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/bundle_expert_widget');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $widget_id) {

				$this->model_catalog_bundle_expert_widget->deleteWidget($widget_id);
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

			$this->response->redirect($this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL'));
		}

		$this->getList();
	}


	protected function getList() {
        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('module/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/bundle_expert_widget/add', $this->token_name . $this->token_value . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/bundle_expert_widget/delete', $this->token_name . $this->token_value . $url, 'SSL');
		$data['repair'] = $this->url->link('catalog/bundle_expert_widget/repair', $this->token_name . $this->token_value . $url, 'SSL');

        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

		$data['widgets'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$kit_total = $this->model_catalog_bundle_expert_widget->getTotalWidgets();

		$results = $this->model_catalog_bundle_expert_widget->getWidgets($filter_data);

		foreach ($results as $result) {
			$data['widgets'][] = array(
				'widget_id' => $result['widget_id'],
				'name'        => $result['name'],
                'status'  => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('catalog/bundle_expert_widget/edit', $this->token_name . $this->token_value . '&widget_id=' . $result['widget_id'] . $url, 'SSL'),
				'delete'      => $this->url->link('catalog/bundle_expert_widget/delete', $this->token_name . $this->token_value . '&widget_id=' . $result['widget_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
        $data['entry_status'] = $this->language->get('entry_status');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');
        $data['column_status'] = $this->language->get('column_status');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

		$data['text_'] = '';

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . '&sort=name' . $url, 'SSL');
        $data['sort_status'] = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . '&sort=status' . $url, 'SSL');
        $data['sort_order'] = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . '&sort=order' . $url, 'SSL');


		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $kit_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($kit_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($kit_total - $this->config->get('config_limit_admin'))) ? $kit_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $kit_total, ceil($kit_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_widget_list.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_widget_list', $data));
        }


	}

	protected function getForm() {
        if(!$this->bundle_expert->isLicensed()) {
            $this->response->redirect($this->url->link('module/bundle_expert', $this->token_name . $this->token_value . '', 'SSL'));
        }

        
        if ($this->config->get('config_editor_default')) {
            $this->document->addScript('view/javascript/ckeditor/ckeditor.js');
            $this->document->addScript('view/javascript/ckeditor/ckeditor_init.js');
            $data['editor'] = 'ckeditor';
        } else {
            $this->document->addScript('view/javascript/summernote/summernote.js');
            $this->document->addScript('view/javascript/summernote/lang/summernote-' . $this->language->get('lang') . '.js');
            $this->document->addScript('view/javascript/summernote/opencart.js');
            $this->document->addStyle('view/javascript/summernote/summernote.css');
            $data['editor'] = 'summernote';
        }

        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/option');
        $this->load->model('catalog/bundle_expert_kit');
        $this->load->model('design/layout');

        $this->load->model('catalog/filter');
        $this->load->model('catalog/manufacturer');



		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['widget_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_content_top'] = $this->language->get('text_content_top');
        $data['text_content_bottom'] = $this->language->get('text_content_bottom');
        $data['text_column_left'] = $this->language->get('text_column_left');
        $data['text_column_right'] = $this->language->get('text_column_right');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_parent'] = $this->language->get('entry_parent');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_top'] = $this->language->get('entry_top');
		$data['entry_column'] = $this->language->get('entry_column');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_layout'] = $this->language->get('entry_layout');
        $data['entry_position'] = $this->language->get('entry_position');

		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_top'] = $this->language->get('help_top');
		$data['help_column'] = $this->language->get('help_column');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_remove'] = $this->language->get('button_remove');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_design'] = $this->language->get('tab_design');

        $data['text_widget_name'] = $this->language->get('text_widget_name');;
        $data['text_display_settings'] = $this->language->get('text_display_settings');;
        $data['text_display_mode'] = $this->language->get('text_display_mode');;
        $data['text_slider_mode'] = $this->language->get('text_slider_mode');;
        $data['text_display_method'] = $this->language->get('text_display_method');;
        $data['text_selector_preset'] = $this->language->get('text_selector_preset');;
        $data['text_custom_selector'] = $this->language->get('text_custom_selector');;
        $data['text_selector_value'] = $this->language->get('text_selector_value');;
        $data['text_selector_mode'] = $this->language->get('text_selector_mode');;
        $data['text_popup_position'] = $this->language->get('text_popup_position');;
        $data['text_layout'] = $this->language->get('text_layout');;
        $data['text_position'] = $this->language->get('text_position');;
        $data['text_specific_page_target'] = $this->language->get('text_specific_page_target');;
        $data['text_page_url'] = $this->language->get('text_page_url');;
        $data['text_page_route'] = $this->language->get('text_page_route');;
        $data['text_display_template'] = $this->language->get('text_display_template');;
        $data['text_free_product_table_mode'] = $this->language->get('text_free_product_table_mode');;
        $data['text_display_options_input'] = $this->language->get('text_display_options_input');;
        $data['text_main_settings'] = $this->language->get('text_main_settings');;
        $data['text_limit_width'] = $this->language->get('text_limit_width');;
        $data['text_image_size'] = $this->language->get('text_image_size');;
        $data['text_background_image_size'] = $this->language->get('text_background_image_size');;
        $data['text_width'] = $this->language->get('text_width');;
        $data['text_heigth'] = $this->language->get('text_heigth');;
        $data['text_kit'] = $this->language->get('text_kit');;
        $data['text_kits'] = $this->language->get('text_kits');;
        $data['text_cart_free_product_mode'] = $this->language->get('text_cart_free_product_mode');;
        $data['text_custom_template'] = $this->language->get('text_custom_template');;
        $data['text_product_click_mode'] = $this->language->get('text_product_click_mode');
        $data['text_product_click_mode_default'] = $this->language->get('text_product_click_mode_default');
        $data['text_product_click_mode_none'] = $this->language->get('text_product_click_mode_none');
        $data['text_product_click_mode_url'] = $this->language->get('text_product_click_mode_url');
        $data['text_product_click_mode_url_new_tab'] = $this->language->get('text_product_click_mode_url_new_tab');
        $data['text_slider_autoplay_status'] = $this->language->get('text_slider_autoplay_status');;
        $data['text_slider_autoplay_time'] = $this->language->get('text_slider_autoplay_time');;
        $data['entry_title_description'] =  $this->language->get('entry_title_description');
        $data['text_widget_mode_fieldset'] = $this->language->get('text_widget_mode_fieldset');;
        $data['text_main_mode'] = $this->language->get('text_main_mode');;

        $data['text_product_from_kit_mode_items'] = $this->language->get('text_product_from_kit_mode_items');;
        $data['text_product_from_kit_mode_kits'] = $this->language->get('text_product_from_kit_mode_kits');;
        $data['text_product_from_kit_mode_product_source'] = $this->language->get('text_product_from_kit_mode_product_source');;
        $data['text_link_to_products'] = $this->language->get('text_link_to_products_list');
        $data['text_input_name'] = $this->language->get('text_input_name');
        $data['text_product_combine_mode_intersect'] = $this->language->get('text_product_combine_mode_intersect');
        $data['text_product_combine_mode_union'] = $this->language->get('text_product_combine_mode_union');
        $data['text_product_combine_mode_subtract'] = $this->language->get('text_product_combine_mode_subtract');
        $data['text_html_element_action_none'] = $this->language->get('text_html_element_action_none');
        $data['text_html_element_action_hide'] = $this->language->get('text_html_element_action_hide');
        $data['text_html_element_action_remove'] = $this->language->get('text_html_element_action_remove');
        $data['text_html_element_action'] = $this->language->get('text_html_element_action');
        $data['text_link_to_categories'] = $this->language->get('text_link_to_categories');
        $data['text_kits_per_category_page'] = $this->language->get('text_kits_per_category_page');
        $data['text_link_category_filter_kits'] = $this->language->get('text_link_category_filter_kits');
        $data['text_link_category_filter_kits_values_all'] = $this->language->get('text_link_category_filter_kits_values_all');
        $data['text_link_category_filter_kits_values_selected'] = $this->language->get('text_link_category_filter_kits_values_selected');


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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', $this->token_name . $this->token_value, 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL')
		);

		if (!isset($this->request->get['widget_id'])) {
			$data['action'] = $this->url->link('catalog/bundle_expert_widget/add', $this->token_name . $this->token_value . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/bundle_expert_widget/edit', $this->token_name . $this->token_value . '&widget_id=' . $this->request->get['widget_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/bundle_expert_widget', $this->token_name . $this->token_value . $url, 'SSL');

        $data['bundle_expert_navigation'] = $this->load->controller('catalog/bundle_expert_navigation');

        $this->load->language('catalog/product');
        $this->load->language('catalog/bundle_expert_widget');

        $data['display_mode_list'] = $this->model_catalog_bundle_expert_widget->getListDisplayMode();

        $data['display_method_list'] = $this->model_catalog_bundle_expert_widget->getListDisplayMethod();

        $data['custom_page_mode_list'] = $this->model_catalog_bundle_expert_widget->getListSpecificPageMode();

        $data['display_template_list'] = $this->model_catalog_bundle_expert_widget->getListDisplayTemplate();

        $data['selector_mode_list'] = $this->model_catalog_bundle_expert_widget->getListSelectorMode();

        $data['popup_position_list'] = $this->model_catalog_bundle_expert_widget->getListPopupPosition();

        $data['selector_default_list_product_page'] = $this->model_catalog_bundle_expert_widget->getListSelectorDefaultProductPage();

        $data['selector_default_list_category_page'] = $this->model_catalog_bundle_expert_widget->getListSelectorDefaultCategoryPage();

        $data['selector_default_list_cart_page'] = $this->model_catalog_bundle_expert_widget->getListSelectorDefaultCartPage();

        $data['selector_default_list_checkout_page'] = $this->model_catalog_bundle_expert_widget->getListSelectorDefaultCheckoutPage();

        
        $data['display_template_list_custom'] = array();
        $template_list_custom_dir = DIR_CATALOG . 'view/theme/default/template/module/bundle_expert/custom_widget';
        $files = scandir($template_list_custom_dir);
        $files = array_diff($files, array('.', '..'));
        foreach ($files as $file){
            if(stripos($file, '_widget')!==false && stripos($file, '_old_')===false){
                $template_name = str_ireplace(array('_widget', '.tpl', '.twig'), '', $file);
                $data['display_template_list_custom'][$template_name] = array(
                    'template_name'=>$template_name,
                    'file'=>$file
                );
            }

        }

        if (isset($this->request->post['widget'])) {
            $widget = $this->request->post['widget'];
        } elseif (isset($this->request->get['widget_id'])) {
            $widget = $this->model_catalog_bundle_expert_widget->getWidget($this->request->get['widget_id']);
        } else {
            $widget = array();
        }

        $data['widget'] = $widget;

        $data['token_value'] = $this->token_value;
        $data['token_name'] = $this->token_name;

        if (isset($this->request->post['widget_id'])) {
            $data['widget_id'] = $this->request->post['widget_id'];
        } elseif (!empty($widget)) {
            $data['widget_id'] = $widget['widget_id'];
        } else {
            $data['widget_id'] = '';
        }

        if (isset($this->request->post['name'])) {
            $data['name'] = $this->request->post['name'];
        } elseif (!empty($widget)) {
            $data['name'] = $widget['name'];
        } else {
            $data['name'] = '';
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        foreach ($data['languages'] as $index=>$language){
            if (version_compare($this->bundle_expert->OC_VERSION, '2.2.0.0', '<')) {
                $img_url = "view/image/flags/" . $language['image'];
            }else{
                $img_url = "language/".$language['code']."/".$language['code'].".png";
            }
            $data['languages'][$index]['image_url'] = $img_url;
        }

        if (isset($this->request->post['widget_description'])) {
            $data['widget_description'] = $this->request->post['widget_description'];
        } elseif (!empty($widget)) {
            $data['widget_description'] = $this->model_catalog_bundle_expert_widget->getWidgetDescriptions($widget['widget_id']);
        } else {
            $data['widget_description'] = array();
        }

        if (isset($this->request->post['display_mode'])) {
            $data['display_mode'] = $this->request->post['display_mode'];
        } elseif (!empty($widget)) {
            $data['display_mode'] = $widget['display_mode'];
        } else {
            $data['display_mode'] = 'product_page';
        }


        if (isset($this->request->post['html_element_action_selector'])) {
            $data['html_element_action_selector'] = $this->request->post['html_element_action_selector'];
        } elseif (!empty($widget)) {
            $data['html_element_action_selector'] = $widget['html_element_action_selector'];
        } else {
            $data['html_element_action_selector'] = '';
        }

        if (isset($this->request->post['slider_mode'])) {
            $data['slider_mode'] = $this->request->post['slider_mode'];
        } elseif (!empty($widget)) {
            $data['slider_mode'] = $widget['slider_mode'];
        } else {
            $data['slider_mode'] = 1;
        }
        if (isset($this->request->post['slider_autoplay_status'])) {
            $data['slider_autoplay_status'] = $this->request->post['slider_autoplay_status'];
        } elseif (!empty($widget)) {
            $data['slider_autoplay_status'] = $widget['slider_autoplay_status'];
        } else {
            $data['slider_autoplay_status'] = 0;
        }

        if (isset($this->request->post['slider_autoplay_time'])) {
            $data['slider_autoplay_time'] = $this->request->post['slider_autoplay_time'];
        } elseif (!empty($widget)) {
            $data['slider_autoplay_time'] = $widget['slider_autoplay_time'];
        } else {
            $data['slider_autoplay_time'] = 3000;
        }
        if (isset($this->request->post['main_mode'])) {
            $data['main_mode'] = $this->request->post['main_mode'];
        } elseif (!empty($widget)) {
            $data['main_mode'] = $widget['main_mode'];
        } else {
            $data['main_mode'] = 'default';
        }







        if (isset($this->request->post['product_from_kit_mode_items'])) {
            $data['product_from_kit_mode_items'] = $this->request->post['product_from_kit_mode_items'];
        } elseif (!empty($widget)) {
            $data['product_from_kit_mode_items'] = $widget['product_from_kit_mode_items'];
        } else {
            $data['product_from_kit_mode_items'] = 'link_to_product_page';
        }
        if (isset($this->request->post['product_from_kit_mode_kits'])) {
            $data['product_from_kit_mode_kits'] = $this->request->post['product_from_kit_mode_kits'];
        } elseif (!empty($widget)) {
            $data['product_from_kit_mode_kits'] = $widget['product_from_kit_mode_kits'];
        } else {
            $data['product_from_kit_mode_kits'] = 'all_kits';
        }
        if (isset($this->request->post['product_from_kit_mode_product_source'])) {
            $data['product_from_kit_mode_product_source'] = $this->request->post['product_from_kit_mode_product_source'];
        } elseif (!empty($widget)) {
            $data['product_from_kit_mode_product_source'] = $widget['product_from_kit_mode_product_source'];
        } else {
            $data['product_from_kit_mode_product_source'] = 'from_page';
        }

        $data['main_mode_values'] = array(
            'default' => $this->language->get('text_main_mode_values_default'),
            'kit' => $this->language->get('text_main_mode_values_kit'),
            'series' => $this->language->get('text_main_mode_values_series'),
            'product_from_kit' => $this->language->get('text_main_mode_values_product_from_kit'),
        );




        $data['product_from_kit_mode_items_values'] = array(
            'link_to_product_page' => $this->language->get('text_product_from_kit_mode_items_values_link_to_product_page'),
            'show_simple_kit' => $this->language->get('text_product_from_kit_mode_items_values_show_simple_kit'),
        );
        $data['product_from_kit_mode_kits_values'] = array(
            'all_kits' => $this->language->get('text_product_from_kit_mode_kits_values_all_kits'),
            'linked_kits' => $this->language->get('text_product_from_kit_mode_kits_values_linked_kits'),
        );
        $data['product_from_kit_mode_product_source_values'] = array(
            'from_page' => $this->language->get('text_product_from_kit_mode_product_source_values_from_page'),
            'list' => $this->language->get('text_product_from_kit_mode_product_source_values_list'),
        );

        if (isset($this->request->post['config_module'])) {
            $data['config_module'] = $this->request->post['config_module'];
        } elseif (!empty($widget)) {
            $data['config_module'] = ($widget['config_module']);
        } else {
            $data['config_module'] = array(
                'module_id' => -1,
                'layout_id' => -1,
                'cart_free_product_mode' => false,
                'position' => 'top',
                'sort_order' => '0',
            );
        }

        if (isset($this->request->post['checkbox_mode'])) {
            $data['checkbox_mode'] = $this->request->post['checkbox_mode'];
        } elseif (!empty($widget)) {
            $data['checkbox_mode'] = $widget['checkbox_mode'];
        } else {
            $data['checkbox_mode'] = 0;
        }

        
        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->load->model('extension/module');
            $module = $this->model_extension_module->getModule($data['config_module']['module_id']);
        }else{
            $this->load->model('setting/module');
            $module = $this->model_setting_module->getModule($data['config_module']['module_id']);
        }

        if (empty($module))
            $data['config_module']['module_id'] = -1;

        
        $layout = $this->model_design_layout->getLayout($data['config_module']['layout_id']);
        if (empty($layout))
            $data['config_module']['layout_id'] = -1;

        $layouts = $this->model_design_layout->getLayouts(array());

        foreach ($layouts as $layout) {
            $data['layouts'][] = array(
                'layout_id' => $layout['layout_id'],
                'name'      => $layout['name'],
                'edit'      => $this->url->link('design/layout/edit', $this->token_name . $this->token_value . '&layout_id=' . $layout['layout_id'] . $url, 'SSL')
            );
        }

         if (isset($this->request->post['config_product_page'])) {
             $data['config_product_page'] = $this->request->post['config_product_page'];
         } elseif (!empty($widget)) {
             $data['config_product_page'] = ($widget['config_product_page']);
         } else {
             $data['config_product_page'] = array(
                 'display_method' => 'block',
                 'selector' => $data['selector_default_list_product_page'][0]['selector_value'],
                 'selector_mode' => $data['selector_default_list_product_page'][0]['selector_mode'],
                 'popup_position' => 'center',
             );
         }

        if (isset($this->request->post['config_category_page'])) {
            $data['config_category_page'] = $this->request->post['config_category_page'];
        } elseif (!empty($widget)) {
            $data['config_category_page'] = ($widget['config_category_page']);
        } else {
            $data['config_category_page'] = array(
                'display_method' => 'block',
                'selector' => $data['selector_default_list_category_page'][0]['selector_value'],
                'selector_mode' => $data['selector_default_list_category_page'][0]['selector_mode'],
                'popup_position' => 'center',
            );
        }

        if (isset($this->request->post['config_custom_page'])) {
            $data['config_custom_page'] = $this->request->post['config_custom_page'];
        } elseif (!empty($widget)) {
            $data['config_custom_page'] = ($widget['config_custom_page']);
        } else {
            $data['config_custom_page'] = array(
                'custom_page_mode' => 'url',
                'custom_page_layout_id' => '',
                'custom_page_url' => '',
                'custom_page_route' => '',
                'display_method' => 'block',
                'selector' => '',
                'selector_mode' => 'after',
                'popup_position' => 'center',
                'cart_free_product_mode'=>false
            );
        }

        
        $layout = $this->model_design_layout->getLayout($data['config_custom_page']['custom_page_layout_id']);
        if (empty($layout))
            $data['config_custom_page']['custom_page_layout_id'] = -1;


        if (isset($this->request->post['template'])) {
            $data['template'] = $this->request->post['template'];
        } elseif (!empty($widget)) {
            $data['template'] = $widget['template'];
        } else {
            $data['template'] = 'widget_template_1';
        }

        if (isset($this->request->post['table_mode_config'])) {
            $data['table_mode_config'] = $this->request->post['table_mode_config'];
        } elseif (!empty($widget) && isset($widget['table_mode_config'])) {
            $data['table_mode_config'] = ($widget['table_mode_config']);
        } else {
            $data['table_mode_config'] = array(
                'free_product_table_mode' => 0,
                'display_options_input' => 0,
            );
        }

        if (isset($this->request->post['product_click_mode'])) {
            $data['product_click_mode'] = $this->request->post['product_click_mode'];
        } elseif (!empty($widget) && isset($widget['product_click_mode'])) {
            $data['product_click_mode'] = $widget['product_click_mode'];
        } else {
            $data['product_click_mode'] = 'default';
        }

        $data['product_click_mode_values'] = array(
            array(
                'name' => $data['text_product_click_mode_none'],
                'value' => 'none'
            ),
            array(
                'name' => $data['text_product_click_mode_default'],
                'value' => 'default'
            ),
            array(
                'name' => $data['text_product_click_mode_url'],
                'value' => 'url'
            ),
            array(
                'name' => $data['text_product_click_mode_url_new_tab'],
                'value' => 'url_new_tab'
            ),

        );









        if (isset($this->request->post['widget_kit'])) {
            $widget_kits = $this->request->post['widget_kit'];
        } elseif (!empty($widget)) {
            $widget_kits = $this->model_catalog_bundle_expert_widget->getWidgetKits($widget['widget_id']);
        } else {
            $widget_kits = array();
        }

        if (isset($this->request->post['widget_width_mode'])) {
            $data['widget_width_mode'] = $this->request->post['widget_width_mode'];
        } elseif (!empty($widget)) {
            $data['widget_width_mode'] = $widget['widget_width_mode'];
        } else {
            $data['widget_width_mode'] = array(
                'limit' => false,
                'value' => '800'
            );
        }

        if (isset($this->request->post['set_image_size_mode'])) {
            $data['set_image_size_mode'] = $this->request->post['set_image_size_mode'];
        } elseif (!empty($widget)) {
            $data['set_image_size_mode'] = $widget['set_image_size_mode'];
        } else {
            $data['set_image_size_mode'] = array(
                'mode' => false,
                'width' => '80',
                'height' => '80'
            );
        }

        if (isset($this->request->post['background_image_size'])) {
            $data['background_image_size'] = $this->request->post['background_image_size'];
        } elseif (!empty($widget) && !empty($widget['background_image_size'])) {
            $data['background_image_size'] = $widget['background_image_size'];
        } else {
            $data['background_image_size'] = array(
                'mode' => false,
                'width' => 'auto',
                'height' => '200'
            );
        }

        $this->load->model('setting/store');

        $data['stores'] = $this->model_setting_store->getStores();

        if (isset($this->request->post['widget_store'])) {
            $data['widget_store'] = $this->request->post['widget_store'];
        } elseif (!empty($widget)) {
            $data['widget_store'] = $this->model_catalog_bundle_expert_widget->getWidgetStores($widget['widget_id']);
        } else {
            $data['widget_store'] = array(0);
        }

         if(in_array(0, $data['widget_store'] )) {
             $data['store_default_in_widget_store']=true;
         }else{
             $data['store_default_in_widget_store']=false;
         }

        foreach ($data['stores'] as $index=>$store){
            if (in_array($store['store_id'], $data['widget_store'])){
                $data['stores'][$index]['in_widget_store']=true;
            }else{
                $data['stores'][$index]['in_widget_store']=false;
            }
        }

        if (isset($this->request->post['sort_order'])) {
            $data['sort_order'] = $this->request->post['sort_order'];
        } elseif (!empty($widget)) {
            $data['sort_order'] = $widget['sort_order'];
        } else {
            $data['sort_order'] = 0;
        }

        if (isset($this->request->post['status'])) {
            $data['status'] = $this->request->post['status'];
        } elseif (!empty($widget)) {
            $data['status'] = $widget['status'];
        } else {
            $data['status'] = true;
        }


        if (isset($this->request->post['link_products_combine_mode'])) {
            $data['link_products_combine_mode'] = $this->request->post['link_products_combine_mode'];
        } elseif (!empty($widget) && !empty($widget['link_products_combine_mode'])) {
            $data['link_products_combine_mode'] = $widget['link_products_combine_mode'];
        } else {
            $data['link_products_combine_mode'] = 'union';
        }

        $data['product_combine_mode_values'] = array(
            'union' => $data['text_product_combine_mode_union'],
            'intersect' => $data['text_product_combine_mode_intersect'],
            'subtract' => $data['text_product_combine_mode_subtract'],
        );

        if (isset($this->request->post['html_element_action'])) {
            $data['html_element_action'] = $this->request->post['html_element_action'];
        } elseif (!empty($widget)) {
            $data['html_element_action'] = $widget['html_element_action'];
        } else {
            $data['html_element_action'] = 'html_element_action';
        }
        $data['html_element_action_values'] = array(
            '0' => $data['text_html_element_action_none'],
            '1' => $data['text_html_element_action_hide'],

        );


        $data['link_products'] = array();

        if (isset($this->request->post['link_product'])) {
            $link_products = $this->request->post['link_product'];
            if(empty($link_products))
                $link_products = array();
        } else if(!empty($widget)){
            $link_products = $this->model_catalog_bundle_expert_widget->getWidgetLinkProducts($widget['widget_id']);
        } else {
            $link_products = array();
        }

        foreach($link_products as $link_product){

            $deleted_item=false;

            $text = $this->language->get('text_'.$link_product['item_type']);

            switch ($link_product['item_type']){
                case 'product':
                    $product_info = $this->model_catalog_product->getProduct($link_product['item_id']);
                    if($product_info){
                        $name = $text . ' -> ' . $product_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'category':
                    $category_info = $this->model_catalog_category->getCategory($link_product['item_id']);
                    if($category_info){
                        $name = $text . ' -> ' . $category_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'filter':
                    $filter_info = $this->model_catalog_filter->getFilter($link_product['item_id']);
                    if($filter_info){
                        $name = $text . ' > ' . $filter_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'manufacturer':
                    $manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($link_product['item_id']);
                    if($manufacturer_info){
                        $name = $text . ' > ' . $manufacturer_info['name'];
                        $name = htmlspecialchars_decode($name);
                        $name = htmlspecialchars($name);
                    }else{
                        $deleted_item=true;
                    }
                    break;
                case 'model':
                case 'sku':
                case 'upc':
                case 'ean':
                case 'jan':
                case 'isbn':
                case 'mpn':
                    $name = $text.' > ' . $link_product['item_value'];
                    $name = htmlspecialchars_decode($name);
                    $name = htmlspecialchars($name);
                    break;
                default:
                    $deleted_item=false;
            }


            if(!$deleted_item) {
                $link_product['name'] = $name;

                $has_options = false;
                if ($link_product['item_type'] == 'product') {
                    $product_options = $this->model_catalog_product->getProductOptions($link_product['item_id']);
                    if (!empty($product_options))
                        $has_options = true;
                }

                $link_product['has_options'] = $has_options;

                $data['link_products'][] = $link_product;;
            }else{
                $item_warning=true;
            }

        }

        $data['link_products_count'] = count($data['link_products']);


        $data['link_category'] = array();

        if (isset($this->request->post['link_category'])) {
            $link_category = $this->request->post['link_category'];
            if(empty($link_category))
                $link_category = array();
        } else if(!empty($widget)){
            $link_category = $this->model_catalog_bundle_expert_widget->getWidgetLinkCategory($widget['widget_id']);
        } else {
            $link_category = array();
        }

        foreach($link_category as $link_category_item){

            $deleted_item=false;

            $text = $this->language->get('text_category');

            $category_info = $this->model_catalog_category->getCategory($link_category_item['category_id']);
            if($category_info){
                $name = $text . ' -> ' . $category_info['name'];
                $name = htmlspecialchars_decode($name);
                $name = htmlspecialchars($name);
            }else{
                $deleted_item=true;
            }


            if(!$deleted_item) {
                $link_category_item['name'] = $name;

                $has_options = false;

                $link_category_item['has_options'] = $has_options;

                $data['link_category'][] = $link_category_item;;
            }else{
                $item_warning=true;
            }

        }

        $data['link_category_count'] = count($data['link_category']);

        if (isset($this->request->post['link_category_filter_kits'])) {
            $data['link_category_filter_kits'] = $this->request->post['link_category_filter_kits'];
        } elseif (!empty($widget)) {
            $data['link_category_filter_kits'] = $widget['link_category_filter_kits'];
        } else {
            $data['link_category_filter_kits'] = 'all';
        }

        $data['link_category_filter_kits_values'] = array(
            'all' => $data['text_link_category_filter_kits_values_all'],
            'selected' => $data['text_link_category_filter_kits_values_selected'],
        );

        if (isset($this->request->post['kits_per_category_page'])) {
            $data['kits_per_category_page'] = $this->request->post['kits_per_category_page'];
        } elseif (!empty($widget)) {
            $data['kits_per_category_page'] = $widget['kits_per_category_page'];
        } else {
            $data['kits_per_category_page'] = 5;
        }









        $data['widget_kits'] = array();

        foreach ($widget_kits as $widget_kit) {

            $kit_info = $this->model_catalog_bundle_expert_kit->getKit($widget_kit['kit_id']);

            if($kit_info){

                $data['widget_kits'][] = array(
                    'kit_id'          => $kit_info['kit_id'],
                    'name' => $kit_info['name'],
                    'href' => $this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $kit_info['kit_id'] . $url, 'SSL'),
                    'sort_order'               => 0
                );
            }

        }

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        if (version_compare($this->bundle_expert->OC_VERSION, '3.0.0.0', '<')) {
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_widget_form.tpl', $data));
        }else{
            $this->response->setOutput($this->load->view('catalog/bundle_expert/bundle_expert_widget_form', $data));
        }


	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_widget')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}








		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/bundle_expert_widget')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    public function autocomplete_widget_kit() {
        $json = array();

        if (isset($this->request->post['filter_name'])) {
            $this->load->model('catalog/bundle_expert_kit');
            $this->load->model('catalog/category');
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');


            $filter_name = $this->request->post['filter_name'];
            $filter_name = trim(urldecode($filter_name));

            $filter_list = array();

            if (isset($this->request->post['widget_kits'])) {
                $widget_kits = $this->request->post['widget_kits'];

                foreach ($widget_kits as $widget_kit){
                    $filter_list[] = $widget_kit['kit_id'];
                }
            }

            $kits = $this->model_catalog_bundle_expert_kit->getKits();


            
            foreach ($kits as $index=>$kit) {
                $url = "";

                $kits[$index]['href'] = html_entity_decode($this->url->link('catalog/bundle_expert_kit/edit', $this->token_name . $this->token_value . '&kit_id=' . $kit['kit_id'] . $url, 'SSL'));

                if (in_array($kit['kit_id'], $filter_list)) {

                }
            }


        }

          
        if(!empty($filter_name)) {
            foreach ($kits as $index => $kit) {
                if (!mb_stristr($kit['name'], $filter_name)) {
                    unset($kits[$index]);
                }
            }
        }

        $json = $kits;

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }


}