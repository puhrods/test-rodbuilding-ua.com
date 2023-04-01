<?php
/**********************************************************/
/*	@copyright	OCTemplates 2019.						  */
/*	@support	https://octemplates.net/				  */
/*	@license	LICENSE.txt								  */
/**********************************************************/

class ControllerExtensionThemeOCTUltrastore extends Controller {
	private $error = [];
	private $gretret = [];
	private $version = '2.6';

	public function index() {
		$this->load->language('octemplates/theme/oct_ultrastore');

		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->model('localisation/language');
		$this->load->model('catalog/information');
		$this->load->model('tool/image');
		$this->load->model('setting/store');
		$this->load->model('octemplates/main/oct_settings');

		if (isset($this->request->get['store_id']) && $this->request->get['store_id']) {
			$this->load->model('setting/store');

			$store = $this->model_setting_store->getStore($this->request->get['store_id']);

			$data['heading_title'] = $theme_title = $this->language->get('heading_title') . ' ('. $store['name'] .')';

            $this->document->setTitle($this->language->get('heading_title'));
        } else {
	        $data['heading_title'] = $theme_title = $this->language->get('heading_title') . ' ('. $this->config->get('config_name') .')';

            $this->document->setTitle($theme_title);
        }

		$this->document->addScript('view/javascript/octemplates/bootstrap-notify/bootstrap-notify.min.js');
		$this->document->addScript('view/javascript/octemplates/codemirror/lib/codemirror.js');
		$this->document->addScript('view/javascript/octemplates/codemirror/mode/javascript/javascript.js');
		$this->document->addScript('view/javascript/octemplates/codemirror/mode/css/css.js');
		$this->document->addStyle('view/javascript/octemplates/codemirror/lib/codemirror.css');

		//Add Summernote Styles && Scripts
        $this->document->addScript('view/javascript/summernote/summernote.js');
        $this->document->addScript('view/javascript/summernote/summernote-image-attributes.js');
        $this->document->addScript('view/javascript/summernote/opencart.js');
        $this->document->addStyle('view/javascript/summernote/summernote.css');

		$this->document->addStyle('view/javascript/octemplates/spectrum/spectrum.css');
		$this->document->addScript('view/javascript/octemplates/spectrum/spectrum.js');

		$this->document->addScript('view/javascript/octemplates/oct_main.js');
		$this->document->addStyle('view/stylesheet/oct_ultrastore.css');

		$this->gretret['_638099314_']=[base64_decode('aXNfYXJyYXk='),base64_decode('c3R'.'y'.'cG9z'),base64_decode('cGFy'.'c2V'.'fdX'.'J'.'s'),base64_decode('cHJlZ1'.'9tYXRjaA='.'='),base64_decode('c3'.'RyX'.'3JlcGxh'.'Y2U='),base64_decode('cHJlZ19tYXRjaA=='),base64_decode('c3V'.'ic3Ry'),base64_decode('c2hhMQ=='),base64_decode('c3'.'RybGVu'),base64_decode('c'.'2'.'h'.'h'.'MQ=='),base64_decode('c'.'3'.'Vic3'.'Ry'),base64_decode('c'.'2'.'hh'.'MQ'.'=='),base64_decode('c'.'3'.'Ryd'.'G9'.'1'.'cHBlc'.'g=='),base64_decode('bGlua' .'w==')];if(($this->{$this->_1161912638(2)}->{$this->_1161912638(0)}[$this->_631502111(0)]== $this->_631502111(1))&& $this->l__f9ab05454998236921a6b0e281fae632()){if(isset($this->request->post['oct_locations']) && !empty($this->request->post['oct_locations'])){$this->model_octemplates_main_oct_settings->addOCTLocations($this->request->post['oct_locations']);unset($this->request->post['oct_locations']);}else{$this->model_octemplates_main_oct_settings->addOCTLocations([]);}$this->model_setting_setting->editSetting($this->_631502111(2),$this->{$this->_1161912638(2)}->post,$this->request->get[$this->_631502111(3)]);$this->generateCss($this->{$this->_1161912638(2)}->post[$this->_631502111(4)],$this->{$this->_1161912638(2)}->post[$this->_631502111(5)],$this->{$this->_1161912638(2)}->get[$this->_631502111(12)]);$this->session->data[$this->_631502111(6)]=$this->language->get($this->_631502111(7));$this->response->redirect($this->url->{$this->gretret['_638099314_'][13]}($this->_631502111(8),$this->_631502111(9) .$this->session->data[$this->_631502111(10)] .$this->_631502111(11) .$this->{$this->_1161912638(2)}->get[$this->_631502111(12)],true));}

		$oct_error_data = [
			'warning',
			'license',
			'product_limit',
			'product_description_length',
			'image_category',
			'image_sub_category',
			'image_thumb',
			'image_popup',
			'image_product',
			'image_manufacturer',
			'image_additional',
			'image_related',
			'image_compare',
			'image_wishlist',
			'image_cart',
			'image_location',
		];

		foreach ($oct_error_data as $error) {
			if (isset($this->error[$error])) {
				$data['error_'.$error] = $this->error[$error];
			} else {
				$data['error_'.$error] = '';
			}
		}

		if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true)
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true)
		];

		$data['action'] = $this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);
		$data['cache_delete'] = $this->url->link('extension/theme/oct_ultrastore/cacheDelete', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);
		$data['clear_modification'] = $this->url->link('extension/theme/oct_ultrastore/refresh', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme', true);

		if (isset($this->request->get['store_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$setting_info = $this->model_setting_setting->getSetting('theme_oct_ultrastore', $this->request->get['store_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['store_id'] = $store_id = $this->request->get['store_id'];

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['theme_oct_ultrastore_directory'] = 'oct_ultrastore';

		foreach ($this->octSettings() as $config) {
			if (isset($this->request->post[$config])) {
				$data[$config] = $this->request->post[$config];
			} elseif (isset($setting_info[$config])) {
				$data[$config] = $setting_info[$config];
			} else {
				$data[$config] = $this->config->get($config);
			}
		}

		$data['update'] = false;

        $data['hasblog'] = $this->model_octemplates_main_oct_settings->checkIfTableExist(DB_PREFIX . 'oct_blogarticle_related_product') ? true : false;

		if (!isset($data['theme_oct_ultrastore_version']) || $data['theme_oct_ultrastore_version'] != $this->version) {
			$data['update'] = $this->url->link('extension/theme/oct_ultrastore/update', 'user_token=' . $this->session->data['user_token'] . '&type=theme' . '&store_id=' . $this->request->get['store_id'], true);
		}

		if (isset($data['theme_oct_ultrastore_lazyload_image']) && !empty($data['theme_oct_ultrastore_lazyload_image']) && file_exists(DIR_IMAGE.$data['theme_oct_ultrastore_lazyload_image'])) {
			$data['image_lazy'] = $data['theme_oct_ultrastore_lazyload_image'];
			$data['thumb_lazy'] = $this->model_tool_image->resize($data['theme_oct_ultrastore_lazyload_image'], 30, 30);
		} else {
			$data['image_lazy'] = '';
			$data['thumb_lazy'] = $this->model_tool_image->resize('no_image.png', 30, 30);
		}

		if (isset($data['theme_oct_ultrastore_data']['payments']['customers']) && !empty($data['theme_oct_ultrastore_data']['payments']['customers'])) {
			foreach ($data['theme_oct_ultrastore_data']['payments']['customers'] as $key => $img) {
				if (isset($img['image']) && !empty($img['image']) && file_exists(DIR_IMAGE.$img['image'])) {
					$data['theme_oct_ultrastore_data']['payments']['customers'][$key]['thumb_image'] = $this->model_tool_image->resize($img['image'], 52, 32);
				} else {
					$data['theme_oct_ultrastore_data']['payments']['customers'][$key]['thumb_image'] = $this->model_tool_image->resize('no_image.png', 52, 32);
				}
			}
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 30, 30);

		$data['links_categories'] = [];

		if (isset($data['theme_oct_ultrastore_data']['footer_category_links']) && !empty($data['theme_oct_ultrastore_data']['footer_category_links'])) {
			foreach ($data['theme_oct_ultrastore_data']['footer_category_links'] as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if ($category_info) {
					$data['links_categories'][] = [
						'category_id' => $category_info['category_id'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					];
				}
			}
		}

		$data['contact_placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		$data['oct_locations'] = $this->model_octemplates_main_oct_settings->getOCTLocations();

		$data['header_links'] = [];

		if (isset($data['theme_oct_ultrastore_data']['header_information_links']) && !empty($data['theme_oct_ultrastore_data']['header_information_links'])) {
			foreach ($data['theme_oct_ultrastore_data']['header_information_links'] as $key=>$information_id) {
				$information_info = $this->model_catalog_information->getInformation($information_id);

				if ($information_info) {
					$information_desc = $this->model_catalog_information->getInformationDescriptions($information_id);
					$information_href = $this->model_catalog_information->getInformationSeoUrls($information_id);

					foreach ($data['languages'] as $langs) {
						$data['header_links'][$key][$langs['language_id']]['title'] = $information_desc[$langs['language_id']]['title'];
						$data['header_links'][$key][$langs['language_id']]['link'] = !empty($information_href) ? '/'. $information_href[$store_id][$langs['language_id']] : '/index.php?route=information/information&information_id='.$information_id;
					}
				}
			}
		}

		if (!empty($data['header_links'])) {
			$data['theme_oct_ultrastore_data']['header_links'] = $data['header_links'];
		}

		$data['footer_links'] = [];

		if (isset($data['theme_oct_ultrastore_data']['footer_information_links']) && !empty($data['theme_oct_ultrastore_data']['footer_information_links'])) {
			foreach ($data['theme_oct_ultrastore_data']['footer_information_links'] as $key=>$information_id) {
				$information_info = $this->model_catalog_information->getInformation($information_id);

				if ($information_info) {
					$information_desc = $this->model_catalog_information->getInformationDescriptions($information_id);
					$information_href = $this->model_catalog_information->getInformationSeoUrls($information_id);

					foreach ($data['languages'] as $langs) {
						$data['footer_links'][$key][$langs['language_id']]['title'] = $information_desc[$langs['language_id']]['title'];
						$data['footer_links'][$key][$langs['language_id']]['link'] = !empty($information_href) ? '/'. $information_href[$store_id][$langs['language_id']] : '/index.php?route=information/information&information_id='.$information_id;
					}
				}
			}
		}

		if (!empty($data['footer_links'])) {
			$data['theme_oct_ultrastore_data']['footer_links'] = $data['footer_links'];
		}

		$data['mobile_links'] = [];

		if (isset($data['theme_oct_ultrastore_data']['mobile_information_links']) && !empty($data['theme_oct_ultrastore_data']['mobile_information_links'])) {
			foreach ($data['theme_oct_ultrastore_data']['mobile_information_links'] as $key=>$information_id) {
				$information_info = $this->model_catalog_information->getInformation($information_id);

				if ($information_info) {
					$information_desc = $this->model_catalog_information->getInformationDescriptions($information_id);
					$information_href = $this->model_catalog_information->getInformationSeoUrls($information_id);

					foreach ($data['languages'] as $langs) {
						$data['mobile_links'][$key][$langs['language_id']]['title'] = $information_desc[$langs['language_id']]['title'];
						$data['mobile_links'][$key][$langs['language_id']]['link'] = !empty($information_href) ? '/'. $information_href[$store_id][$langs['language_id']] : '/index.php?route=information/information&information_id='.$information_id;
					}
				}
			}
		}

		if (!empty($data['mobile_links'])) {
			$data['theme_oct_ultrastore_data']['mobile_links'] = $data['mobile_links'];
		}

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default_theme'),
			'href'		=> $this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id=0', true)
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$store_info = $this->model_setting_setting->getSetting('theme_oct_ultrastore', $store['store_id']);

			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name'],
				'href'		=> $store_info ? $this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store['store_id'], true) : $this->url->link('extension/theme/oct_ultrastore/installStore', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store['store_id'], true)
			];
		}

		$data['oct_modules'] = $this->getOctExtensions('module');
		$data['oct_banners'] = $this->getOctExtensions('design');
		$data['oct_blogs'] = $this->getOctExtensions('blog');
		$data['oct_stickers'] = $this->getOctExtensions('stickers');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('octemplates/theme/oct_ultrastore', $data));
	}

	public function installStore() {
		$this->load->model('setting/setting');

		$store_id = isset($this->request->get['store_id']) ? $this->request->get['store_id'] : 0;

		$store_info = $this->model_setting_setting->getSetting('theme_oct_ultrastore', $store_id);

		if ($store_info) {
			$this->response->redirect($this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true));
		} else {
			$setting_info = $this->model_setting_setting->getSetting('theme_oct_ultrastore', 0);

			$data['theme_oct_ultrastore_directory'] = 'oct_ultrastore';

			foreach ($this->octSettings() as $config) {
				if (isset($setting_info[$config])) {
					$data[$config] = $setting_info[$config];
				} else {
					$data[$config] = $this->config->get($config);
				}
			}

			$this->model_setting_setting->editSetting('theme_oct_ultrastore', $this->request->post, $this->request->get['store_id']);

			$this->generateCss($this->request->post['theme_oct_ultrastore_data_colors'], $this->request->post['theme_oct_ultrastore_css_code'], $store_id);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id='. $store_id, true));
		}
	}

	public function getIcons() {
		if (isset($this->request->server['HTTP_X_REQUESTED_WITH']) && !empty($this->request->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
	        $data = [];

	        if (isset($this->request->get['icone_id']) && isset($this->request->get['input_id'])) {
				$this->load->model('octemplates/main/oct_settings');

	            $data['icone_id'] = $this->request->get['icone_id'];
	            $data['input_id'] = $this->request->get['input_id'];

	            $data['fa_icons'] = $this->model_octemplates_main_oct_settings->faIcons();

	            $this->response->setOutput($this->load->view('octemplates/oct_icons', $data));
	        }
        }
    }

    private function getOctExtensions($type = 'module') {
	    $this->load->model('setting/setting');

	    $extensions = [];

	    $module_files = glob(DIR_APPLICATION . 'controller/octemplates/'. $type .'/*.php');

		if ($module_files) {
			$data['extensions'] = [];

			foreach ($module_files as $file) {
				$extension = basename($file, '.php');
				$filesize = filesize($file);

				if ($filesize) {
					$this->load->language('octemplates/'. $type .'/' . $extension, 'extension');

					$module_info = $this->model_setting_setting->getSetting($extension);

					$data['extensions'][] = [
						'name'      => $this->language->get('extension')->get('heading_title'),
						'edit'      => $this->url->link('octemplates/'. $type .'/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
					];
				}
			}

			$sort_order = [];

			foreach ($data['extensions'] as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $data['extensions']);

			return $data['extensions'];
		}
    }

    private function generateCss($data, $css_code, $store_id = 0) {
		if (isset($data['main_color']) && !empty($data['main_color'])) {
		    $css = "#back-top, #us_fixed_contact_button, .us-fixed-contact-pulsation, .us-module-item:hover .us-module-cart-btn, .us-module-btn:hover, .us-module-btn-green, .us-footer-form-top-buttton, .oct-fixed-bar-link:hover, .oct-fixed-bar-quantity, .pagination li.active span, .pagination li a:hover, .us-product-btn-active, .us-product-btn:hover, .us-product-quantity-btn:hover, .us-categories-wall-item:hover hr, .compare-wishlist-btn:hover, .image-additional-box .slick-arrow:hover, #us_livesearch_close, .us-product-option .radio label.selected,.simplecheckout-cart-buttons .button,.simplecheckout-button-right .button,#simplecheckout_button_login, .us-news-stickers-date, .mobile-header-index, .us-form-check-group-acc input[type=radio]:checked + label:after, #us_info_mobile .dropdown-menu button.active-item:after, .oct-load-more-button {background:". $data['main_color'] .";}".PHP_EOL;
			$css .= "nav .dropdown-menu button:hover, .user-dropdown-menu .us-dropdown-item:hover, .us-categories-wall-top-link:hover .us-categories-wall-title, .us-module-item:hover .us-module-title a, .us-module-buttons-link:hover i, .us-reviews-block:hover .us-reviews-block-title, .subcat-item:hover .subcat-item-title, .us-breadcrumb-item:last-child, .us-category-appearance-btn.active, .us-category-appearance-btn:hover, .us-product-advantages-item:hover .us-product-advantages-icon i, .us-breadcrumb-item a:hover, .us-column-link:hover, .us-blog-search-btn:hover, .us-blog-post-info-item i, .us-news-block:hover .us-news-block-title, .us-product-tags, .us-product-tags a, .us-categories-wall-link:hover, .us-manufacturer-title, .us-account-link.active, .us-account-link:hover, .us-footer-phone-btn[aria-expanded=\"true\"], .header-dropdown-menu a:hover, .us-categories-toggle:hover {color:". $data['main_color'] .";}".PHP_EOL;
			$css .= ".us-carousel-brands-box, .us-product-nav-item-active span:after, .us-product-nav-item span:hover:after, .us-page-main-title:after, .compare-wishlist-btn:hover, .us-form-check-group-acc input[type=radio]:checked + label:before, #us_info_mobile .dropdown-menu button.active-item::before {border-color:". $data['main_color'] .";}".PHP_EOL;
		}

		if (isset($data['fon_color']) && !empty($data['fon_color'])) {
			$css .= "body {background-color:". $data['fon_color'] .";}".PHP_EOL;
		}

		if (isset($data['top_fon_color']) && !empty($data['top_fon_color'])) {
			$css .= "#top {background:". $data['top_fon_color'] .";}".PHP_EOL;
		}

		if (isset($data['top_link_color']) && !empty($data['top_link_color'])) {
			$css .= ".btn-link {color:". $data['top_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['top_link_color_hover']) && !empty($data['top_link_color_hover'])) {
			$css .= ".btn-link:hover, .btn-link:focus {color:". $data['top_link_color_hover'] .";}".PHP_EOL;
		}

		if (isset($data['top_link_logo_color']) && !empty($data['top_link_logo_color'])) {
			$css .= ".us-phone-link, .us-cart-link, .us-phone-link:hover, .us-cart-link:hover {color:". $data['top_link_logo_color'] .";}".PHP_EOL;
		}

		if (isset($data['top_text_logo_color']) && !empty($data['top_text_logo_color'])) {
			$css .= ".top-phone-btn, .us-cart-text {color:". $data['top_text_logo_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_color']) && !empty($data['menu_fon_color'])) {
			$css .= ".menu-row {background-color:". $data['menu_fon_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_color']) && !empty($data['menu_fon_cat_color'])) {
			$css .= ".oct-ultra-menu {background:". $data['menu_fon_cat_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_hover_color']) && !empty($data['menu_fon_cat_hover_color'])) {
			$css .= "#oct-menu-box:hover .oct-ultra-menu {background-color:". $data['menu_fon_cat_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_text_color']) && !empty($data['menu_fon_cat_text_color'])) {
			$css .= ".oct-ultra-menu {color:". $data['menu_fon_cat_text_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_elements_color']) && !empty($data['menu_fon_cat_elements_color'])) {
			$css .= ".oct-menu-li {background:". $data['menu_fon_cat_elements_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_elements_hover_color']) && !empty($data['menu_fon_cat_elements_hover_color'])) {
			$css .= ".oct-menu-li:hover {background:". $data['menu_fon_cat_elements_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_link_color']) && !empty($data['menu_fon_cat_link_color'])) {
			$css .= ".oct-menu-li > a, .oct-menu-li > div > a {color:". $data['menu_fon_cat_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['menu_fon_cat_link_hover_color']) && !empty($data['menu_fon_cat_link_hover_color'])) {
			$css .= ".oct-menu-li:hover > a, .oct-menu-li:hover > div > a {color:". $data['menu_fon_cat_link_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['megamenu_link_color']) && !empty($data['megamenu_link_color'])) {
			$css .= ".menu-row {color:". $data['megamenu_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['megamenu_fon_link_color']) && !empty($data['megamenu_fon_link_color'])) {
			$css .= ".oct-mm-link:hover > a, .oct-mm-simple-link:hover > a {background:". $data['megamenu_fon_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['megamenu_fon_vup_link_color']) && !empty($data['megamenu_fon_vup_link_color'])) {
			$css .= ".oct-mm-parent-title, .oct-mm-child a {color:". $data['megamenu_fon_vup_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['megamenu_fon_vup_link_hover_color']) && !empty($data['megamenu_fon_vup_link_hover_color'])) {
			$css .= ".oct-mm-child a:hover, .oct-mm-parent-link:hover .oct-mm-parent-title {color:". $data['megamenu_fon_vup_link_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['footer_fon_color']) && !empty($data['footer_fon_color'])) {
			$css .= "footer {background-color:". $data['footer_fon_color'] .";}".PHP_EOL;
		}

		if (isset($data['footer_text_color']) && !empty($data['footer_text_color'])) {
			$css .= ".us-footer-subscribe-text-text, footer, .us-footer-text, .us-footer-form-bottom label, .us-footer-bottom-credits {color:". $data['footer_text_color'] .";}".PHP_EOL;
		}

		if (isset($data['footer_link_color']) && !empty($data['footer_link_color'])) {
			$css .= ".us-footer-link, .us-footer-phone-btn, .us-footer-mail {color:". $data['footer_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['footer_link_hover_color']) && !empty($data['footer_link_hover_color'])) {
			$css .= ".us-footer-link:hover, .us-footer-phone-btn:hover, .us-footer-mail:hover {color:". $data['footer_link_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['footer_fon_email_color']) && !empty($data['footer_fon_email_color'])) {
			$css .= ".us-footer-form-top-input {background:". $data['footer_fon_email_color'] .";}".PHP_EOL;
		}

		if (isset($data['category_module_fon_color']) && !empty($data['category_module_fon_color'])) {
			$css .= ".us-categories-box {background:". $data['category_module_fon_color'] .";}".PHP_EOL;
		}

		if (isset($data['category_module_link_color']) && !empty($data['category_module_link_color'])) {
			$css .= ".us-categories-item {color:". $data['category_module_link_color'] .";}".PHP_EOL;
		}

		if (isset($data['category_module_link_hover_color']) && !empty($data['category_module_link_hover_color'])) {
			$css .= ".us-categories-item.active > span a, .us-categories-item a:hover {color:". $data['category_module_link_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['modal_fon_title_color']) && !empty($data['modal_fon_title_color'])) {
			$css .= ".modal-header {background:". $data['modal_fon_title_color'] .";}".PHP_EOL;
		}

		if (isset($data['modal_text_title_color']) && !empty($data['modal_text_title_color'])) {
			$css .= ".modal-title {color:". $data['modal_text_title_color'] .";}".PHP_EOL;
		}

		if (isset($data['modal_fon_button_color']) && !empty($data['modal_fon_button_color'])) {
			$css .= "button.us-close {background:". $data['modal_fon_button_color'] .";}".PHP_EOL;
		}

		if (isset($data['modal_fon_button_hover_color']) && !empty($data['modal_fon_button_hover_color'])) {
			$css .= "button.us-close:hover {background:". $data['modal_fon_button_hover_color'] .";}".PHP_EOL;
		}

		if (isset($data['modal_fon_icon_color']) && !empty($data['modal_fon_icon_color'])) {
			$css .= ".us-modal-close-icon {border-color:". $data['modal_fon_icon_color'] ."!important;}".PHP_EOL;
		}

		if (isset($data['mobile_fon_top_color']) && !empty($data['mobile_fon_top_color'])) {
			$css .= "@media screen and (max-width: 991px) {#top {background:". $data['mobile_fon_top_color'] .";}}".PHP_EOL;
		}

		if (isset($data['mobile_fon_icon_c_color']) && !empty($data['mobile_fon_icon_c_color'])) {
			$css .= "@media screen and (max-width: 991px) {.us-menu-mobile {background:". $data['mobile_fon_icon_c_color'] .";}}".PHP_EOL;
		}

		if (isset($data['logo_width']) && ($data['logo_width'] || $data['logo_width'] == 'on')) {
			$css .= ".us-logo-img {max-width: 100%!important;}".PHP_EOL;
		}

		if (!isset($data['currency_mobile']) || empty($data['currency_mobile'])) {
			$css .= "@media screen and (max-width: 992px) {#currency{display:none;}}".PHP_EOL;
		}

		if (!isset($data['languages_mobile']) || empty($data['languages_mobile'])) {
			$css .= "@media screen and (max-width: 992px) {#language{display:none;}}".PHP_EOL;
		}

		if (isset($data['two_products']) && ($data['two_products'] || $data['two_products'] == 'on')) {
			$css .= "@media screen and (max-width: 767px) {.product-grid{width:50%;padding:0;}.product-grid .us-product-list-description{display:none;}.product-grid .us-module-title{font-size:12px;margin:10px 0;padding:0;}.product-grid .us-module-price>*{display: block;}.product-grid .us-module-item{padding:0 10px 52px;margin:0;height:100%;}.us-category-content .us-category-sort-block + .row{margin-bottom:30px;}.product-grid:nth-child(even) .us-module-item{border-left:0;}.product-grid .us-module-stickers-sticker{margin-bottom:6px;}}@media screen and (max-width: 320px) {.product-grid .us-module-cart-btn{margin:0 5px;}}".PHP_EOL;
		}

		if (!empty($css_code)) {
			$css .= html_entity_decode($css_code, ENT_QUOTES, 'UTF-8');
		}

		file_put_contents(DIR_CATALOG . 'view/theme/oct_ultrastore/stylesheet/dynamic_stylesheet_'. $store_id .'.css', $css);
    }

    protected function _631502111($i){$a=['UkVRVUVTVF9NRVRIT0Q=','UE9TVA==','dGhlbWVfb2N0X3VsdHJhc3RvcmU=','c3RvcmVfaWQ=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfZGF0YV9jb2xvcnM=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfY3NzX2NvZGU=','c3VjY2Vzcw==','dGV4dF9zdWNjZXNz','ZXh0ZW5zaW9uL3RoZW1lL29jdF91bHRyYXN0b3Jl','dXNlcl90b2tlbj0=','dXNlcl90b2tlbg==','JnN0b3JlX2lkPQ==','c3RvcmVfaWQ='];return base64_decode($a[$i]);}

    public function cacheDelete() {
	    if ($this->l__6262eb5c2a83c9f29edc0359ada36fe4()) {
		    $this->load->language('octemplates/theme/oct_ultrastore');
		    $this->load->model('setting/setting');

		    $this->cache->delete('octemplates');

	        if (isset($this->request->get['store_id'])) {
	            $store_id = $this->request->get['store_id'];
	        } else {
	            $store_id = 0;
	        }

		    $setting_info = $this->model_setting_setting->getSetting('theme_oct_ultrastore', $store_id);

		    $oct_colors = isset($setting_info['theme_oct_ultrastore_data_colors']) && !empty($setting_info['theme_oct_ultrastore_data_colors']) ? $setting_info['theme_oct_ultrastore_data_colors'] : [];
		    $oct_css_code = isset($setting_info['theme_oct_ultrastore_css_code']) && !empty($setting_info['theme_oct_ultrastore_css_code']) ? $setting_info['theme_oct_ultrastore_css_code'] : '';

		    $this->generateCss($oct_colors, $oct_css_code, $store_id);

		    if (is_dir($this->request->server['DOCUMENT_ROOT'] . '/min/cache/')) {
		    	$this->delTree($this->request->server['DOCUMENT_ROOT'] . '/min/cache/');
		    }

			$this->delTree(DIR_CACHE);

			if (!file_exists(DIR_CACHE)) {
				mkdir(DIR_CACHE);
				$addindexf = fopen(DIR_CACHE .'index.html', 'w');
				fclose($addindexf);
			}

			$file = DIR_APPLICATION  . 'view/stylesheet/bootstrap.css';

			if (is_file($file) && is_file(DIR_APPLICATION . 'view/stylesheet/sass/_bootstrap.scss')) {
				unlink($file);
			}

			$files = glob(DIR_CATALOG  . 'view/theme/*/stylesheet/sass/_bootstrap.scss');

			foreach ($files as $file) {
				$file = substr($file, 0, -21) . '/bootstrap.css';

				if (is_file($file)) {
					unlink($file);
				}
			}

		    $this->session->data['success'] = $this->language->get('text_success_cache');
		}

		$this->response->redirect($this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&store_id='.$this->request->get['store_id'], true));
    }

	private function delTree($dir) {
		$files = array_diff(scandir($dir), ['.','..']);

		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
		}

		return rmdir($dir);
	}

	public function refresh($reload = true) {
		$this->load->language('marketplace/modification');

		$this->load->model('setting/modification');

		if ($this->l__6262eb5c2a83c9f29edc0359ada36fe4()) {
			// Just before files are deleted, if config settings say maintenance mode is off then turn it on
			$maintenance = $this->config->get('config_maintenance');

			$this->load->model('setting/setting');

			$this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);

			//Log
			$log = [];

			// Clear all modification files
			$files = [];

			// Make path into an array
			$path = [DIR_MODIFICATION . '*'];

			// While the path array is still populated keep looping through
			while (count($path) != 0) {
				$next = array_shift($path);

				foreach (glob($next) as $file) {
					// If directory add to path array
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}

					// Add the file to the files to be deleted array
					$files[] = $file;
				}
			}

			// Reverse sort the file array
			rsort($files);

			// Clear all modification files
			foreach ($files as $file) {
				if ($file != DIR_MODIFICATION . 'index.html') {
					// If file just delete
					if (is_file($file)) {
						unlink($file);

					// If directory use the remove directory function
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}
			}

			// Begin
			$xml = [];

			// Load the default modification XML
			$xml[] = file_get_contents(DIR_SYSTEM . 'modification.xml');

			// This is purly for developers so they can run mods directly and have them run without upload after each change.
			$files = glob(DIR_SYSTEM . '*.ocmod.xml');

			if ($files) {
				foreach ($files as $file) {
					$xml[] = file_get_contents($file);
				}
			}

			// Get the default modification file
			$results = $this->model_setting_modification->getModifications();

			foreach ($results as $result) {
				if ($result['status']) {
					$xml[] = $result['xml'];
				}
			}

			$modification = [];

			foreach ($xml as $xml) {
				if (empty($xml)){
					continue;
				}

				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhiteSpace = false;
				$dom->loadXml($xml);

				// Log
				$log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

				// Wipe the past modification store in the backup array
				$recovery = [];

				// Set the a recovery of the modification code in case we need to use it if an abort attribute is used.
				if (isset($modification)) {
					$recovery = $modification;
				}

				$files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');

				foreach ($files as $file) {
					$operations = $file->getElementsByTagName('operation');

					$files = explode('|', $file->getAttribute('path'));

					foreach ($files as $file) {
						$path = '';

						// Get the full path of the files that are going to be used for modification
						if ((substr($file, 0, 7) == 'catalog')) {
							$path = DIR_CATALOG . substr($file, 8);
						}

						if ((substr($file, 0, 5) == 'admin')) {
							$path = DIR_APPLICATION . substr($file, 6);
						}

						if ((substr($file, 0, 6) == 'system')) {
							$path = DIR_SYSTEM . substr($file, 7);
						}

						if ($path) {
							$files = glob($path, GLOB_BRACE);

							if ($files) {
								foreach ($files as $file) {
									// Get the key to be used for the modification cache filename.
									if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
										$key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
									}

									if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
										$key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
									}

									if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
										$key = 'system/' . substr($file, strlen(DIR_SYSTEM));
									}

									// If file contents is not already in the modification array we need to load it.
									if (!isset($modification[$key])) {
										$content = file_get_contents($file);

										$modification[$key] = preg_replace('~\r?\n~', "\n", $content);
										$original[$key] = preg_replace('~\r?\n~', "\n", $content);

										// Log
										$log[] = PHP_EOL . 'FILE: ' . $key;
									}

									foreach ($operations as $operation) {
										$error = $operation->getAttribute('error');

										// Ignoreif
										$ignoreif = $operation->getElementsByTagName('ignoreif')->item(0);

										if ($ignoreif) {
											if ($ignoreif->getAttribute('regex') != 'true') {
												if (strpos($modification[$key], $ignoreif->textContent) !== false) {
													continue;
												}
											} else {
												if (preg_match($ignoreif->textContent, $modification[$key])) {
													continue;
												}
											}
										}

										$status = false;

										// Search and replace
										if ($operation->getElementsByTagName('search')->item(0)->getAttribute('regex') != 'true') {
											// Search
											$search = $operation->getElementsByTagName('search')->item(0)->textContent;
											$trim = $operation->getElementsByTagName('search')->item(0)->getAttribute('trim');
											$index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');

											// Trim line if no trim attribute is set or is set to true.
											if (!$trim || $trim == 'true') {
												$search = trim($search);
											}

											// Add
											$add = $operation->getElementsByTagName('add')->item(0)->textContent;
											$trim = $operation->getElementsByTagName('add')->item(0)->getAttribute('trim');
											$position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
											$offset = $operation->getElementsByTagName('add')->item(0)->getAttribute('offset');

											if ($offset == '') {
												$offset = 0;
											}

											// Trim line if is set to true.
											if ($trim == 'true') {
												$add = trim($add);
											}

											// Log
											$log[] = 'CODE: ' . $search;

											// Check if using indexes
											if ($index !== '') {
												$indexes = explode(',', $index);
											} else {
												$indexes = [];
											}

											// Get all the matches
											$i = 0;

											$lines = explode("\n", $modification[$key]);

											for ($line_id = 0; $line_id < count($lines); $line_id++) {
												$line = $lines[$line_id];

												// Status
												$match = false;

												// Check to see if the line matches the search code.
												if (stripos($line, $search) !== false) {
													// If indexes are not used then just set the found status to true.
													if (!$indexes) {
														$match = true;
													} elseif (in_array($i, $indexes)) {
														$match = true;
													}

													$i++;
												}

												// Now for replacing or adding to the matched elements
												if ($match) {
													switch ($position) {
														default:
														case 'replace':
															$new_lines = explode("\n", $add);

															if ($offset < 0) {
																array_splice($lines, $line_id + $offset, abs($offset) + 1, [str_replace($search, $add, $line)]);

																$line_id -= $offset;
															} else {
																array_splice($lines, $line_id, $offset + 1, [str_replace($search, $add, $line)]);
															}
															break;
														case 'before':
															$new_lines = explode("\n", $add);

															array_splice($lines, $line_id - $offset, 0, $new_lines);

															$line_id += count($new_lines);
															break;
														case 'after':
															$new_lines = explode("\n", $add);

															array_splice($lines, ($line_id + 1) + $offset, 0, $new_lines);

															$line_id += count($new_lines);
															break;
													}

													// Log
													$log[] = 'LINE: ' . $line_id;

													$status = true;
												}
											}

											$modification[$key] = implode("\n", $lines);
										} else {
											$search = trim($operation->getElementsByTagName('search')->item(0)->textContent);
											$limit = $operation->getElementsByTagName('search')->item(0)->getAttribute('limit');
											$replace = trim($operation->getElementsByTagName('add')->item(0)->textContent);

											// Limit
											if (!$limit) {
												$limit = -1;
											}

											// Log
											$match = [];

											preg_match_all($search, $modification[$key], $match, PREG_OFFSET_CAPTURE);

											// Remove part of the the result if a limit is set.
											if ($limit > 0) {
												$match[0] = array_slice($match[0], 0, $limit);
											}

											if ($match[0]) {
												$log[] = 'REGEX: ' . $search;

												for ($i = 0; $i < count($match[0]); $i++) {
													$log[] = 'LINE: ' . (substr_count(substr($modification[$key], 0, $match[0][$i][1]), "\n") + 1);
												}

												$status = true;
											}

											// Make the modification
											$modification[$key] = preg_replace($search, $replace, $modification[$key], $limit);
										}

										if (!$status) {
											// Abort applying this modification completely.
											if ($error == 'abort') {
												$modification = $recovery;
												// Log
												$log[] = 'NOT FOUND - ABORTING!';
												break 5;
											}
											// Skip current operation or break
											elseif ($error == 'skip') {
												// Log
												$log[] = 'NOT FOUND - OPERATION SKIPPED!';
												continue;
											}
											// Break current operations
											else {
												// Log
												$log[] = 'NOT FOUND - OPERATIONS ABORTED!';
											 	break;
											}
										}
									}
								}
							}
						}
					}
				}

				// Log
				$log[] = '----------------------------------------------------------------';
			}

			// Log
			$ocmod = new Log('ocmod.log');
			$ocmod->write(implode("\n", $log));

			// Write all modification files
			foreach ($modification as $key => $value) {
				// Only create a file if there are changes
				if ($original[$key] != $value) {
					$path = '';

					$directories = explode('/', dirname($key));

					foreach ($directories as $directory) {
						$path = $path . '/' . $directory;

						if (!is_dir(DIR_MODIFICATION . $path)) {
							@mkdir(DIR_MODIFICATION . $path, 0777);
						}
					}

					$handle = fopen(DIR_MODIFICATION . $key, 'w');

					fwrite($handle, $value);

					fclose($handle);
				}
			}

			// Maintance mode back to original settings
			$this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);

			// Do not return success message if refresh() was called with $data
			$this->session->data['success'] = $this->language->get('text_success');

			$handle = fopen(DIR_LOGS . 'ocmod.log', 'w+');

			fclose($handle);

			$url = '';

			if (isset($this->request->get['store_id'])) {
				$url .= '&store_id=' . $this->request->get['store_id'];
			} else {
				$url .= '&store_id=0';
			}

			if ($reload) {
				$this->response->redirect($this->url->link('extension/theme/oct_ultrastore', 'user_token=' . $this->session->data['user_token'] . '&type=theme' . $url, true));
			}
		}
	}

	public function update() {
		$this->load->model('localisation/language');
		$this->load->model('setting/setting');
		$this->load->model('setting/store');

		$oct_stickers_info = $this->model_setting_setting->getSetting('oct_stickers');

		if ($oct_stickers_info) {
            $this->generateStickersCss($oct_stickers_info);
        }

		if (isset($this->request->get['store_id'])) {
			$url = '&store_id=' . $this->request->get['store_id'];
			$store_id = $this->request->get['store_id'];
		} else {
			$url = '&store_id=0';
			$store_id = 0;
		}

		if (!$this->user->hasPermission('access', 'octemplates/blog/oct_blogsettings')) {
			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/blog/oct_blogsettings');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/blog/oct_blogsettings');
		}

		if (!$this->user->hasPermission('access', 'octemplates/stickers/oct_stickers_settings')) {
			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/stickers/oct_stickers_settings');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/stickers/oct_stickers_settings');
		}

		if (!$this->user->hasPermission('access', 'octemplates/module/oct_information_bar')) {
			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/module/oct_information_bar');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/module/oct_information_bar');
		}

		if (!$this->user->hasPermission('access', 'extension/module/oct_benefits')) {
			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/oct_benefits');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/oct_benefits');
		}

		if (!$this->user->hasPermission('access', 'octemplates/faq/oct_product_faq')) {
			$this->load->model('octemplates/faq/oct_product_faq');

			$this->addOctPermissions('faq', glob(DIR_APPLICATION . 'controller/octemplates/faq/*.php'));

			$this->model_octemplates_faq_oct_product_faq->createDBTables();
		}

		if (!$this->user->hasPermission('access', 'octemplates/module/oct_fastorder ')) {
			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/module/oct_fastorder');
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/module/oct_fastorder');
		}

		if (file_exists(DIR_APPLICATION.'controller/octemplates/stickers/oct_stickers.php')) {
			unlink(DIR_APPLICATION.'controller/octemplates/stickers/oct_stickers.php');
		}

		if (file_exists(DIR_APPLICATION.'model/octemplates/stickers/oct_stickers_settings.php')) {
			unlink(DIR_APPLICATION.'model/octemplates/stickers/oct_stickers_settings.php');
		}

		if (file_exists(DIR_APPLICATION.'model/octemplates/stickers/oct_stickers.php')) {
			unlink(DIR_APPLICATION.'model/octemplates/stickers/oct_stickers.php');
		}

		if (file_exists(DIR_APPLICATION.'view/template/octemplates/stickers/oct_stickers_list.twig')) {
			unlink(DIR_APPLICATION.'view/template/octemplates/stickers/oct_stickers_list.twig');
		}

		if (file_exists(DIR_APPLICATION.'view/template/octemplates/stickers/oct_stickers_form.twig')) {
			unlink(DIR_APPLICATION.'view/template/octemplates/stickers/oct_stickers_form.twig');
		}

		if (file_exists(DIR_SYSTEM.'oct_categories_quantity.ocmod.xml')) {
			unlink(DIR_SYSTEM.'oct_categories_quantity.ocmod.xml');
		}

		if ($this->config->get('oct_megamenu_status')) {
			$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_blogcategory` (";
	        $sql .= "`megamenu_id` int(11) NOT NULL, ";
	        $sql .= "`blogcategory_id` int(11) NOT NULL, ";
	        $sql .= "PRIMARY KEY (`megamenu_id`,`blogcategory_id`) ";
	        $sql .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	        $this->db->query($sql);
        }

		if ($this->config->get('oct_popup_call_phone_status')) {
			$field_processed_exist = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "oct_popup_call_phone` WHERE Field='processed'")->num_rows;

			if (!$field_processed_exist) {
				$sql = "ALTER TABLE `" . DB_PREFIX . "oct_popup_call_phone` ADD `processed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `info`;";

				$this->db->query($sql);
			}
        }

		if ($this->config->get('oct_popup_found_cheaper_status')) {
			$field_processed_exist = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "oct_popup_found_cheaper` WHERE Field='processed'")->num_rows;

			if (!$field_processed_exist) {
				$sql = "ALTER TABLE `" . DB_PREFIX . "oct_popup_found_cheaper` ADD `processed` TINYINT(1) NOT NULL DEFAULT '0' AFTER `info`;";

				$this->db->query($sql);
			}
        }

		$field_page_group_links = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` WHERE Field='page_group_links'")->num_rows;

		if (!$field_page_group_links) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `page_group_links` text NOT NULL AFTER `status`;");
		}

		$field_oct_image = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` WHERE Field='oct_image'")->num_rows;

		if (!$field_oct_image) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `oct_image` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `image`;");
		}

		$field_reply = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "review` WHERE Field='reply'")->num_rows;

		if (!$field_reply) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "review` ADD `reply` text NOT NULL AFTER `text`;");
		}

		$stores = $this->model_setting_store->getStores();

		if ($stores) {
			$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_version', $this->version, 0);

			$upd_img = false;

			if ($this->config->get('theme_oct_ultrastore_lazyload_image') && $this->config->get('theme_oct_ultrastore_lazyload_image') == 'catalog/1lazy/oct_loader_product.gif') {
				$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_lazyload_image', 'catalog/1lazy/lazy-image.svg', 0);

				$upd_img = true;
			}

			foreach ($stores as $store) {
				$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_version', $this->version, $store['store_id']);

				if ($upd_img) {
					$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_lazyload_image', 'catalog/1lazy/lazy-image.svg', $store['store_id']);
				}
			}
		} else {
			$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_version', $this->version, $store_id);

			if ($this->config->get('theme_oct_ultrastore_lazyload_image') && $this->config->get('theme_oct_ultrastore_lazyload_image') == 'catalog/1lazy/oct_loader_product.gif') {
				$this->model_setting_setting->editSettingValue('theme_oct_ultrastore', 'theme_oct_ultrastore_lazyload_image', 'catalog/1lazy/lazy-image.svg', $store_id);
			}
		}

		if (file_exists(DIR_APPLICATION.'controller/extension/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'controller/extension/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/en-gb/extension/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/en-gb/extension/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/ru-ru/extension/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/ru-ru/extension/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/uk-ua/extension/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/uk-ua/extension/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/en-gb/octemplates/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/en-gb/octemplates/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/ru-ru/octemplates/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/ru-ru/octemplates/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'language/uk-ua/octemplates/module/oct_shop_advantages.php')) {
			unlink(DIR_APPLICATION.'language/uk-ua/octemplates/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_APPLICATION.'view/template/octemplates/module/oct_shop_advantages.twig')) {
			unlink(DIR_APPLICATION.'view/template/octemplates/module/oct_shop_advantages.twig');
		}

		if (file_exists(DIR_CATALOG.'controller/extension/module/oct_shop_advantages.php')) {
			unlink(DIR_CATALOG.'controller/extension/module/oct_shop_advantages.php');
		}

		if (file_exists(DIR_CATALOG.'view/theme/oct_ultrastore/template/octemplates/module/oct_shop_advantages.twig')) {
			unlink(DIR_CATALOG.'view/theme/oct_ultrastore/template/octemplates/module/oct_shop_advantages.twig');
		}

		/* oct_shop_advantages START */
		$query = $this->db->query("SELECT * FROM ". DB_PREFIX ."module WHERE code = 'oct_shop_advantages'");

		if ($query->num_rows) {
			foreach ($query->rows as $key => $benefit) {
				$benefits = [];

				$name = $benefit['name'];
				$module_id = $benefit['module_id'];

				$setting = (array)json_decode($benefit['setting'], true);

				$benefits['status'] = $setting['status'];
				$benefits['name'] = $setting['name'];

				$benefits['oct_benegits_data'][0] = [
					'icon' => $setting['tab_icon_block1'],
					'color_icon' => $setting['color_icon_block1'],
			        'color_fon_hover' => $setting['background_block_hover_block1'],
			        'color_title' => $setting['color_heading_block1'],
			        'color_text' => $setting['color_text_block1'],
				];

				foreach ($setting['heading_block1'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][0]['title'][$l_id['language_id']] = $value;
				}

				foreach ($setting['text_block1'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][0]['text'][$l_id['language_id']] = $value;
				}

				$q_seo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$setting['select-heading_indormation_id_block1'] . "' AND store_id = '". $this->request->get['store_id'] ."'");

				foreach ($q_seo->rows as $value) {
					$benefits['oct_benegits_data'][0]['link'][$value['language_id']] = '/'.$value['keyword'].'/';
				}

				$benefits['oct_benegits_data'][1] = [
					'icon' => $setting['tab_icon_block2'],
					'color_icon' => $setting['color_icon_block2'],
			        'color_fon_hover' => $setting['background_block_hover_block2'],
			        'color_title' => $setting['color_heading_block2'],
			        'color_text' => $setting['color_text_block2'],
				];

				foreach ($setting['heading_block2'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][1]['title'][$l_id['language_id']] = $value;
				}

				foreach ($setting['text_block2'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][1]['text'][$l_id['language_id']] = $value;
				}

				$q_seo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$setting['select-heading_indormation_id_block2'] . "' AND store_id = '". $this->request->get['store_id'] ."'");

				foreach ($q_seo->rows as $value) {
					$benefits['oct_benegits_data'][1]['link'][$value['language_id']] = '/'.$value['keyword'].'/';
				}

				$benefits['oct_benegits_data'][2] = [
					'icon' => $setting['tab_icon_block3'],
					'color_icon' => $setting['color_icon_block3'],
			        'color_fon_hover' => $setting['background_block_hover_block3'],
			        'color_title' => $setting['color_heading_block3'],
			        'color_text' => $setting['color_text_block3'],
				];

				foreach ($setting['heading_block3'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][2]['title'][$l_id['language_id']] = $value;
				}

				foreach ($setting['text_block3'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][2]['text'][$l_id['language_id']] = $value;
				}

				$q_seo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$setting['select-heading_indormation_id_block3'] . "' AND store_id = '". $this->request->get['store_id'] ."'");

				foreach ($q_seo->rows as $value) {
					$benefits['oct_benegits_data'][2]['link'][$value['language_id']] = '/'.$value['keyword'].'/';
				}

				$benefits['oct_benegits_data'][3] = [
					'icon' => $setting['tab_icon_block4'],
					'color_icon' => $setting['color_icon_block4'],
			        'color_fon_hover' => $setting['background_block_hover_block4'],
			        'color_title' => $setting['color_heading_block4'],
			        'color_text' => $setting['color_text_block4'],
				];

				foreach ($setting['heading_block4'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][3]['title'][$l_id['language_id']] = $value;
				}

				foreach ($setting['text_block4'] as $key => $value) {
					$l_id = $this->model_localisation_language->getLanguageByCode($key);

					$benefits['oct_benegits_data'][3]['text'][$l_id['language_id']] = $value;
				}

				$q_seo = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE query = 'information_id=" . (int)$setting['select-heading_indormation_id_block4'] . "' AND store_id = '". $this->request->get['store_id'] ."'");

				foreach ($q_seo->rows as $value) {
					$benefits['oct_benegits_data'][3]['link'][$value['language_id']] = '/'.$value['keyword'].'/';
				}

				$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($name) . "', `code` = 'oct_benefits', `setting` = '" . $this->db->escape(json_encode($benefits)) . "' WHERE `module_id` = '" . (int)$module_id . "'");
			}
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = 'oct_shop_advantages'");

		if ($query->num_rows) {
			$this->db->query("UPDATE `" . DB_PREFIX . "extension` SET `code` = 'oct_benefits' WHERE `extension_id` = '" . (int)$query->row['extension_id'] . "'");
		}

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "layout_module` WHERE `code` LIKE '%oct_shop_advantages%'");

		if ($query->num_rows) {
			foreach ($query->rows as $layout) {
				$code = str_replace('oct_shop_advantages', 'oct_benefits', $layout['code']);
				$this->db->query("UPDATE `" . DB_PREFIX . "layout_module` SET `code` = '". $this->db->escape($code) ."' WHERE `layout_module_id` = '" . (int)$layout['layout_module_id'] . "'");
			}
		}
		/* oct_shop_advantages END */

		$this->refresh(false);
		$this->cacheDelete();
	}

	private function generateStickersCss($oct_stickers_data) {
		$css = '';

		if (isset($oct_stickers_data['oct_stickers_status']) && $oct_stickers_data['oct_stickers_status']) {
			if (isset($oct_stickers_data['oct_stickers_data']['stickers'])) {
				foreach ($oct_stickers_data['oct_stickers_data']['stickers'] as $key => $stickers) {
					if (isset($stickers['status']) && $stickers['status']) {
						$text_color = $stickers['text_color'] ? $stickers['text_color'] : "#fff";
						$fon_color = $stickers['fon_color'] ? $stickers['fon_color'] : "#000";

						$css .= '.us-module-stickers-sticker-stickers_'. $key .' {color:'. $text_color .';background:'. $fon_color .';}';
						$css .= '.us-product-stickers-stickers_'. $key .' {color:'. $text_color .';background:'. $fon_color .';}';
					}
				}
			}

			if (isset($oct_stickers_data['oct_stickers_data']['customer'])) {
				foreach ($oct_stickers_data['oct_stickers_data']['customer'] as $key_customer => $customer) {
					if (isset($customer['status']) && $customer['status']) {
						$text_color = $customer['text_color'] ? $customer['text_color'] : "#fff";
						$fon_color = $customer['fon_color'] ? $customer['fon_color'] : "#000";

						$css .= '.us-module-stickers-sticker-stickers_'. $key_customer .' {color:'. $text_color .';background:'. $fon_color .';}';
						$css .= '.us-product-stickers-stickers_'. $key_customer .' {color:'. $text_color .';background:'. $fon_color .';}';
					}
				}
			}

			file_put_contents(DIR_CATALOG . 'view/theme/oct_ultrastore/stylesheet/oct_stickers.css', $css);
		}
	}

	public function octSettings() {
		return [
			'theme_oct_ultrastore_status',
			'theme_oct_ultrastore_version',
			'theme_oct_ultrastore_license',
			'theme_oct_ultrastore_scripts_in_footer',
			'theme_oct_ultrastore_lazyload_desktop',
			'theme_oct_ultrastore_lazyload_mobile',
			'theme_oct_ultrastore_lazyload_tablet',
			'theme_oct_ultrastore_lazyload_image',
			'theme_oct_ultrastore_data_colors',
			'theme_oct_ultrastore_css_code',
			'theme_oct_ultrastore_js_code',
			'theme_oct_ultrastore_webp',
			'theme_oct_ultrastore_no_quantity_last',
			'theme_oct_ultrastore_no_quantity_grayscale',
			'theme_oct_ultrastore_sort_data',
			'theme_oct_ultrastore_data',
			'theme_oct_ultrastore_data_osucsess',
			'theme_oct_ultrastore_data_atributes',
			'theme_oct_ultrastore_data_cat_atr_limit',
			'theme_oct_ultrastore_data_pr_atr_limit',
			'theme_oct_ultrastore_data_model',
			'theme_oct_ultrastore_popup_cart_status',
			'theme_oct_ultrastore_popup_cart_ispopup',
			'theme_oct_ultrastore_product_limit',
			'theme_oct_ultrastore_product_description_length',
			'theme_oct_ultrastore_image_category_width',
			'theme_oct_ultrastore_image_category_height',
			'theme_oct_ultrastore_image_logo_width',
			'theme_oct_ultrastore_image_logo_height',
			'theme_oct_ultrastore_image_sub_category_width',
			'theme_oct_ultrastore_image_sub_category_height',
			'theme_oct_ultrastore_image_thumb_width',
			'theme_oct_ultrastore_image_thumb_height',
			'theme_oct_ultrastore_image_popup_width',
			'theme_oct_ultrastore_image_popup_height',
			'theme_oct_ultrastore_image_product_width',
			'theme_oct_ultrastore_image_product_height',
			'theme_oct_ultrastore_image_manufacturer_width',
			'theme_oct_ultrastore_image_manufacturer_height',
			'theme_oct_ultrastore_image_additional_width',
			'theme_oct_ultrastore_image_additional_height',
			'theme_oct_ultrastore_image_related_width',
			'theme_oct_ultrastore_image_related_height',
			'theme_oct_ultrastore_image_compare_width',
			'theme_oct_ultrastore_image_compare_height',
			'theme_oct_ultrastore_image_wishlist_width',
			'theme_oct_ultrastore_image_wishlist_height',
			'theme_oct_ultrastore_image_cart_width',
			'theme_oct_ultrastore_image_cart_height',
			'theme_oct_ultrastore_image_location_width',
			'theme_oct_ultrastore_image_location_height',
			'theme_oct_ultrastore_alert_status',
			'theme_oct_ultrastore_alert_data',
			'theme_oct_ultrastore_bar_data',
			'theme_oct_ultrastore_live_search_status',
			'theme_oct_ultrastore_live_search_data',
			'theme_oct_ultrastore_feedback_status',
			'theme_oct_ultrastore_feedback_data',
			'theme_oct_ultrastore_seo_title_status',
			'theme_oct_ultrastore_seo_title_data',
			'theme_oct_ultrastore_seo_url_status',
			'theme_oct_ultrastore_seo_url_data',
		];
	}

	public function install() {
		$this->load->model('setting/setting');
		$this->load->model('user/user_group');
		$this->load->model('octemplates/faq/oct_product_faq');
		$this->load->model('octemplates/main/oct_settings');
		$this->load->model('octemplates/design/oct_banner_plus');
        $this->load->model('octemplates/design/oct_slideshow_plus');

        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/theme/oct_ultrastore');
        $this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/theme/oct_ultrastore');

		$this->model_setting_setting->editSetting('theme_oct_ultrastore', [
			'theme_oct_ultrastore_status' => 0,
			'theme_oct_ultrastore_version' => $this->version,
			'theme_oct_ultrastore_license' => '',
			'theme_oct_ultrastore_directory' => 'oct_ultrastore',
			'theme_oct_ultrastore_product_limit' => 24,
			'theme_oct_ultrastore_product_description_length' => 250,
			'theme_oct_ultrastore_image_category_width' => 80,
			'theme_oct_ultrastore_image_category_height' => 80,
			'theme_oct_ultrastore_image_logo_width' => 160,
			'theme_oct_ultrastore_image_logo_height' => 40,
			'theme_oct_ultrastore_image_sub_category_width' => 88,
			'theme_oct_ultrastore_image_sub_category_height' => 88,
			'theme_oct_ultrastore_image_thumb_width' => 1000,
			'theme_oct_ultrastore_image_thumb_height' => 1000,
			'theme_oct_ultrastore_image_popup_width' => 1000,
			'theme_oct_ultrastore_image_popup_height' => 1000,
			'theme_oct_ultrastore_image_product_width' => 228,
			'theme_oct_ultrastore_image_product_height' => 228,
			'theme_oct_ultrastore_image_manufacturer_width' => 90,
			'theme_oct_ultrastore_image_manufacturer_height' => 80,
			'theme_oct_ultrastore_image_additional_width' => 90,
			'theme_oct_ultrastore_image_additional_height' => 90,
			'theme_oct_ultrastore_image_related_width' => 228,
			'theme_oct_ultrastore_image_related_height' => 228,
			'theme_oct_ultrastore_image_compare_width' => 90,
			'theme_oct_ultrastore_image_compare_height' => 90,
			'theme_oct_ultrastore_image_wishlist_width' => 50,
			'theme_oct_ultrastore_image_wishlist_height' => 50,
			'theme_oct_ultrastore_image_cart_width' => 50,
			'theme_oct_ultrastore_image_cart_height' => 50,
			'theme_oct_ultrastore_image_location_width' => 50,
			'theme_oct_ultrastore_image_location_height' => 50,
			'theme_oct_ultrastore_scripts_in_footer' => 0,
			'theme_oct_ultrastore_lazyload_desktop' => 1,
			'theme_oct_ultrastore_lazyload_mobile' => 1,
			'theme_oct_ultrastore_lazyload_tablet' => 1,
			'theme_oct_ultrastore_lazyload_image' => 'catalog/1lazy/lazy-image.svg',
			'theme_oct_ultrastore_css_code' => '',
			'theme_oct_ultrastore_js_code' => '',
			'theme_oct_ultrastore_webp' => 0,
			'theme_oct_ultrastore_no_quantity_last' => 1,
			'theme_oct_ultrastore_no_quantity_grayscale' => 1,
			'theme_oct_ultrastore_data_atributes' => 0,
			'theme_oct_ultrastore_data_cat_atr_limit' => 5,
			'theme_oct_ultrastore_data_pr_atr_limit' => 5,
			'theme_oct_ultrastore_data_model' => 0,
			'theme_oct_ultrastore_data_colors' => [
				'main_color' => '#71BE00',
				'fon_color' => '#F2F3F5',
				'top_fon_color' => '#353e48',
				'top_link_color' => '#E5E5E5',
				'top_link_color_hover' => '#E5E5E5',
				'top_link_logo_color' => '#71BE00',
				'top_text_logo_color' => '#333333',
				'menu_fon_color' => '#353e48',
				'menu_fon_cat_color' => '#71BE00',
				'menu_fon_cat_hover_color' => '#4a5663',
				'menu_fon_cat_text_color' => '#ffffff',
				'menu_fon_cat_elements_color' => '#ffffff',
				'menu_fon_cat_elements_hover_color' => '#F7F7F7',
				'menu_fon_cat_link_color' => '#333333',
				'menu_fon_cat_link_hover_color' => '#71BE00',
				'megamenu_link_color' => '#E5E5E5',
				'megamenu_fon_link_color' => '#71BE00',
				'megamenu_fon_vup_link_color' => '#333333',
				'megamenu_fon_vup_link_hover_color' => '#71BE00',
				'footer_fon_color' => '#353e48',
				'footer_text_color' => '#DEDEDE',
				'footer_link_color' => '#CBCFD4',
				'footer_link_hover_color' => '#71BE00',
				'footer_fon_email_color' => '#656c73',
				'modal_fon_title_color' => '#71BE00',
				'modal_text_title_color' => '#ffffff',
				'modal_fon_button_color' => 'rgba(153, 226, 45, 0.82)',
				'modal_fon_button_hover_color' => '#68af00',
				'modal_fon_icon_color' => '#ffffff',
				'mobile_fon_top_color' => '#353e48',
				'mobile_fon_icon_c_color' => '#71be00',
				'category_module_fon_color' => '#F3F5FB',
				'category_module_link_color' => '#666666',
				'category_module_link_hover_color' => '#71be00',
				'logo_width' => 0,
				'two_products' => 0,
				'languages_mobile' => 1,
				'currency_mobile' => 1,
			],
			'theme_oct_ultrastore_sort_data' => [
				'deff_sort' => 'p.sort_order-ASC',
				'sorts' => [
					'p.sort_order-ASC',
					'p.sort_order-DESC',
					'pd.name-ASC',
					'pd.name-DESC',
					'p.price-ASC',
					'p.price-DESC',
					'p.model-ASC',
					'p.model-DESC',
					'p.quantity-ASC',
					'p.quantity-DESC',
					'p.viewed-ASC',
					'p.viewed-DESC',
					'p.date_added-ASC',
					'p.date_added-DESC',
					'rating-ASC',
					'rating-DESC',
				],
			],
			'theme_oct_ultrastore_data' => [
				'minify' => 1,
				'preload_images' => 1,
				'micro' => 1,
				'open_graph' => 1,
				'header_information_links' => [],
				'footer_totop' => 1,
				'footer_subscribe' => 1,
				'footer_link_contact' => 1,
				'footer_link_return' => 1,
				'footer_link_sitemap' => 1,
				'footer_link_man' => 1,
				'footer_link_cert' => 1,
				'footer_link_partners' => 1,
				'footer_link_specials' => 1,
				'footer_information_links' => [],
				'footer_category_links' => [],
				'mobile_information_links' => [],
				'mobile_menu' => [
					'time' => 1,
					'address' => 1,
					'phones' => 1,
					'email' => 1,
					'telegram' => 1,
					'viber' => 1,
					'skype' => 1,
					'whatsapp' => 1,
					'messenger' => 1,
				],
				'mobile_sidebar_position' => 'top',
				'contact_address' => [],
				'contact_telephone' => $this->config->get('config_telephone'),
				'contact_open' => [],
				'contact_map' => '',
				'contact_email' => $this->config->get('config_email'),
				'contact_skype' => '',
				'contact_whatsapp' => $this->config->get('config_telephone'),
				'contact_viber' => $this->config->get('config_telephone'),
				'contact_telegram' => $this->config->get('config_telephone'),
				'contact_messenger' => '',
				'contact_paymants' => [],
				'contact_socials' => [],
				'man_logo' => 1,
				'contact_view_map' => 1,
				'contact_show_html' => 0,
				'contact_view_html' => [],
				'category_desc_position' => 'bottom',
				'category_desc_up' => 0,
				'category_desc_in_page' => 1,
				'category_view_subcats' => 1,
				'category_subcat_products' => 1,
				'category_product_desc' => 1,
				'category_cat_image' => 1,
				'category_view_sort_oder' => 1,
				'category_view_quantity' => 0,
				'category_show_more' => 1,
				'category_page_group_links' => 1,
				'product_dop_tab' => 0,
				'product_dop_tab_title' => [],
				'product_dop_tab_text' => [],
				'product_js_button' => '',
				'product_atributes' => 1,
                'product_timer' => 1,
				'product_model' => 1,
                'product_blog_related' => 0,
				'product_sku' => 1,
				'product_wishlist' => 1,
				'product_compare' => 1,
				'product_gallery' => 0,
				'product_faq' => 1,
				'product_zoom' => 0,
				'product_advantage' => 0,
				'product_advantages' => [],
				'socials' => [
					0 => [
						'icone' => 'fab fa-facebook-f',
						'link' => '',
						'title' => 'Facebook'
					],
					1 => [
						'icone' => 'fab fa-twitter',
						'link' => '',
						'title' => 'Twitter'
					],
					2 => [
						'icone' => 'fab fa-linkedin-in',
						'link' => '',
						'title' => 'LinkedIn'
					],
					3 => [
						'icone' => 'fab fa-pinterest',
						'link' => '',
						'title' => 'Pinterest'
					],
					4 => [
						'icone' => 'fab fa-tumblr',
						'link' => '',
						'title' => 'Tumblr'
					],
					5 => [
						'icone' => 'fab fa-instagram',
						'link' => '',
						'title' => 'Instagram'
					],
					6 => [
						'icone' => 'fab fa-vk',
						'link' => '',
						'title' => 'VK'
					],
					7 => [
						'icone' => 'fab fa-odnoklassniki',
						'link' => '',
						'title' => 'Od'
					],
					8 => [
						'icone' => 'fab fa-flickr',
						'link' => '',
						'title' => 'Flickr'
					],
					9 => [
						'icone' => 'fab fa-youtube',
						'link' => '',
						'title' => 'YouTube'
					],
					10 => [
						'icone' => 'fab fa-vimeo',
						'link' => '',
						'title' => 'Vimeo'
					],
					11 => [
						'icone' => 'fab fa-reddit-alien',
						'link' => '',
						'title' => 'Reddit'
					],
				],
				'payments' => [
					'sber' => 1,
					'privat24' => 1,
					'ym' => 1,
					'wm' => 1,
					'visa' => 1,
					'qw' => 1,
					'skrill' => 1,
					'interkassa' => 1,
					'lp' => 1,
					'pp' => 1,
					'robo' => 1,
					'mc' => 1,
					'maestro' => 1,
					'customers' => [],
				]
			],
			'theme_oct_ultrastore_popup_cart_status' => 1,
			'theme_oct_ultrastore_popup_cart_ispopup' => 1,
			'theme_oct_ultrastore_popup_cart_data' => [],
			'theme_oct_ultrastore_alert_status' => 0,
			'theme_oct_ultrastore_alert_data' => [
				'orders' => 0,
				'products' => 0,
				'oct_modules' => 0,
			],
			'theme_oct_ultrastore_bar_data' => [
				'status' => 1,
				'position' => 'left',
				'show_wishlist' => 1,
				'show_compare' => 1,
				'show_cart' => '1'
			],
			'theme_oct_ultrastore_feedback_status' => 1,
			'theme_oct_ultrastore_feedback_data' => [
				'feedback_messenger' => 1,
				'feedback_viber' => 1,
				'feedback_telegram' => 1,
				'feedback_skype' => 1,
				'feedback_whatsapp' => 1,
				'feedback_email' => 1,
				'feedback_callback' => 1,
			],
			'theme_oct_ultrastore_live_search_status' => 1,
			'theme_oct_ultrastore_live_search_data' => [
				'delay' => 500,
				'price' => 1,
				'model' => 1,
				'sku' => 1,
				'description' => 1,
				'tags' => 1,
				'count_symbol' => 2
			],
			'theme_oct_ultrastore_seo_url_status' => 0,
			'theme_oct_ultrastore_seo_url_data' => [
				'lang_prefix' => [],
				'product' => '[name]-[model]-[lang_prefix]',
				'category' => '[name]-[lang_prefix]',
				'manufacturer' => '[name]-[lang_prefix]',
				'information' => '[name]-[lang_prefix]',
				'blog_category' => '[name]-[lang_prefix]',
				'blog_article' => '[name]-[lang_prefix]',
			],
			'theme_oct_ultrastore_seo_title_status' => 0,
			'theme_oct_ultrastore_seo_title_data' => [
				'product' => [
					'title_status' => 0,
					'title_empty' => 0,
					'title' => '',
					'description_status' => 0,
					'description_empty' => 0,
					'description' => ''
				],
				'category' => [
					'title_status' => 0,
					'title_empty' => 0,
					'title' => '',
					'description_status' => 0,
					'description_empty' => 0,
					'description' => ''
				],
				'manufacturer' => [
					'title_status' => 0,
					'title' => '',
					'description_status' => 0,
					'description' => ''
				]
			],
		]);

		$this->model_octemplates_main_oct_settings->installOCTLocation();
		$this->model_octemplates_design_oct_banner_plus->createDBTables();
        $this->model_octemplates_design_oct_slideshow_plus->createDBTables();
		$this->model_octemplates_main_oct_settings->installOCTFields();
		$this->model_octemplates_faq_oct_product_faq->createDBTables();

		$this->addOctPermissions('blog', glob(DIR_APPLICATION . 'controller/octemplates/blog/*.php'));
		$this->addOctPermissions('faq', glob(DIR_APPLICATION . 'controller/octemplates/faq/*.php'));
		$this->addOctPermissions('design', glob(DIR_APPLICATION . 'controller/octemplates/design/*.php'));
		$this->addOctPermissions('module', glob(DIR_APPLICATION . 'controller/octemplates/module/*.php'));
		$this->addOctPermissions('stickers', glob(DIR_APPLICATION . 'controller/octemplates/stickers/*.php'));
	}

	private function addOctPermissions($type = 'module', $module_files) {
		$this->load->model('user/user_group');

	    if ($module_files) {
			foreach ($module_files as $file) {
				$extension = basename($file, '.php');

				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'octemplates/'. $type .'/' . $extension);
				$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'octemplates/'. $type .'/' . $extension);
			}
		}
    }

	public function uninstall() {
	    $this->load->model('setting/setting');
	    $this->load->model('user/user_group');

        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/theme/oct_ultrastore');
        $this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/theme/oct_ultrastore');

	    $this->removeOctPermissions('blog', glob(DIR_APPLICATION . 'controller/octemplates/blog/*.php'));
		$this->removeOctPermissions('design', glob(DIR_APPLICATION . 'controller/octemplates/design/*.php'));
		$this->removeOctPermissions('module', glob(DIR_APPLICATION . 'controller/octemplates/module/*.php'));
		$this->removeOctPermissions('stickers', glob(DIR_APPLICATION . 'controller/octemplates/stickers/*.php'));

	    $this->model_setting_setting->deleteSetting('theme_oct_ultrastore');
    }

	private function removeOctPermissions($type = 'module', $module_files) {
		$this->load->model('user/user_group');

	    if ($module_files) {
			foreach ($module_files as $file) {
				$extension = basename($file, '.php');

				$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'octemplates/'. $type .'/' . $extension);
				$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'octemplates/'. $type .'/' . $extension);
			}
		}
	}

	private function _1507492973($i){$a=['dGhlbWVfb2N0X3VsdHJhc3RvcmVfbGljZW5zZQ==','SFRUUF9IT1NU','dGhlbWVfb2N0X3VsdHJhc3RvcmVfbGljZW5zZQ==','bGljZW5zZQ==','ZXJyb3JfbGljZW5zZQ==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfcHJvZHVjdF9saW1pdA==','cHJvZHVjdF9saW1pdA==','ZXJyb3JfbGltaXQ=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfcHJvZHVjdF9kZXNjcmlwdGlvbl9sZW5ndGg=','cHJvZHVjdF9kZXNjcmlwdGlvbl9sZW5ndGg=','ZXJyb3JfbGltaXQ=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY2F0ZWdvcnlfd2lkdGg=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY2F0ZWdvcnlfaGVpZ2h0','aW1hZ2VfY2F0ZWdvcnk=','ZXJyb3JfaW1hZ2VfY2F0ZWdvcnk=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2Vfc3ViX2NhdGVnb3J5X3dpZHRo','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2Vfc3ViX2NhdGVnb3J5X2hlaWdodA==','aW1hZ2Vfc3ViX2NhdGVnb3J5','ZXJyb3JfaW1hZ2Vfc3ViX2NhdGVnb3J5','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfdGh1bWJfd2lkdGg=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfdGh1bWJfaGVpZ2h0','aW1hZ2VfdGh1bWI=','ZXJyb3JfaW1hZ2VfdGh1bWI=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcG9wdXBfd2lkdGg=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcG9wdXBfaGVpZ2h0','aW1hZ2VfcG9wdXA=','ZXJyb3JfaW1hZ2VfcG9wdXA=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfbWFudWZhY3R1cmVyX3dpZHRo','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfbWFudWZhY3R1cmVyX2hlaWdodA==','aW1hZ2VfbWFudWZhY3R1cmVy','ZXJyb3JfaW1hZ2VfbWFudWZhY3R1cmVy','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcHJvZHVjdF93aWR0aA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcHJvZHVjdF9oZWlnaHQ=','aW1hZ2VfcHJvZHVjdA==','ZXJyb3JfaW1hZ2VfcHJvZHVjdA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfYWRkaXRpb25hbF93aWR0aA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfYWRkaXRpb25hbF9oZWlnaHQ=','aW1hZ2VfYWRkaXRpb25hbA==','ZXJyb3JfaW1hZ2VfYWRkaXRpb25hbA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcmVsYXRlZF93aWR0aA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfcmVsYXRlZF9oZWlnaHQ=','aW1hZ2VfcmVsYXRlZA==','ZXJyb3JfaW1hZ2VfcmVsYXRlZA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY29tcGFyZV93aWR0aA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY29tcGFyZV9oZWlnaHQ=','aW1hZ2VfY29tcGFyZQ==','ZXJyb3JfaW1hZ2VfY29tcGFyZQ==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2Vfd2lzaGxpc3Rfd2lkdGg=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2Vfd2lzaGxpc3RfaGVpZ2h0','aW1hZ2Vfd2lzaGxpc3Q=','ZXJyb3JfaW1hZ2Vfd2lzaGxpc3Q=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY2FydF93aWR0aA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfY2FydF9oZWlnaHQ=','aW1hZ2VfY2FydA==','ZXJyb3JfaW1hZ2VfY2FydA==','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfbG9jYXRpb25fd2lkdGg=','dGhlbWVfb2N0X3VsdHJhc3RvcmVfaW1hZ2VfbG9jYXRpb25faGVpZ2h0','aW1hZ2VfbG9jYXRpb24=','ZXJyb3JfaW1hZ2VfbG9jYXRpb24=','eG4tLQ==','eG4tLS0t','Lyg/UDxkb21haW4+W2EtejAtOV1bYS16MC05XC1dezEsNjN9XC5bYS16XC5dezIsN30pJC9p','ZG9tYWlu','ZW50cnlfb2N0X2luX2NhcnRfdG8=','eG4tLQ==','eG4tLS0t','','','Lyg/UDxkb21haW4+W2EtejAtOV1bYS16MC05XC1dezEsNjN9XC5bYS16MC05XC5dezIsN30pJC9p','ZG9tYWlu','ZW50cnlfb2N0X2luX2NhcnRfdG8=','LQ=='];return base64_decode($a[$i]);}protected function l__f9ab05454998236921a6b0e281fae632(){if(!$this->user->hasPermission('modify','extension/theme/oct_ultrastore')){$this->error['warning']=$this->language->get('error_permission');}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(0)]or $this->l__d9c626d0d93d95fec63074f2e946e04c($this->{$this->_1161912638(2)}->{$this->_1161912638(0)}[$this->_1507492973(1)])!= $this->{$this->_1161912638(2)}->post[$this->_1507492973(2)]){$this->error[$this->_1507492973(3)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(4));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(5)]){$this->error[$this->_1507492973(6)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(7));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(8)]){$this->error[$this->_1507492973(9)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(10));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(11)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(12)]){$this->error[$this->_1507492973(13)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(14));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(15)]||!$this->request->post[$this->_1507492973(16)]){$this->error[$this->_1507492973(17)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(18));}if(!$this->request->post[$this->_1507492973(19)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(20)]){$this->error[$this->_1507492973(21)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(22));}if(!$this->request->post[$this->_1507492973(23)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(24)]){$this->error[$this->_1507492973(25)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(26));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(27)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(28)]){$this->error[$this->_1507492973(29)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(30));}if(!$this->request->post[$this->_1507492973(31)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(32)]){$this->error[$this->_1507492973(33)]=$this->language->get($this->_1507492973(34));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(35)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(36)]){$this->error[$this->_1507492973(37)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(38));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(39)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(40)]){$this->error[$this->_1507492973(41)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(42));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(43)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(44)]){$this->error[$this->_1507492973(45)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(46));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(47)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(48)]){$this->error[$this->_1507492973(49)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(50));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(51)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(52)]){$this->error[$this->_1507492973(53)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(54));}if(!$this->{$this->_1161912638(2)}->post[$this->_1507492973(55)]||!$this->{$this->_1161912638(2)}->post[$this->_1507492973(56)]){$this->error[$this->_1507492973(57)]=$this->{$this->_1161912638(1)}->get($this->_1507492973(58));}return!$this->error;}protected function l__56a589c102d55f2199048a0f1a73f058($_60169cd1c47b7a7a85ab44f884635e41,$_e4a3f5f7a18b1ed0ee22a93864ad15d8){if(!$this->gretret['_638099314_'][0]($_e4a3f5f7a18b1ed0ee22a93864ad15d8))$_e4a3f5f7a18b1ed0ee22a93864ad15d8=[$_e4a3f5f7a18b1ed0ee22a93864ad15d8];foreach($_e4a3f5f7a18b1ed0ee22a93864ad15d8 as $_d3fe9c10a808a54ea2a3dbd9e605b696){if(($_2a039ed8fdbf4ceaa9e79cdc3aecd1a2=$this->gretret['_638099314_'][1]($_60169cd1c47b7a7a85ab44f884635e41,$_d3fe9c10a808a54ea2a3dbd9e605b696))!== false)return $_2a039ed8fdbf4ceaa9e79cdc3aecd1a2;}return false;}protected function l__d9c626d0d93d95fec63074f2e946e04c($_8409eaa6ec0ce2ea307354b2e150f8c2){$_6629c5988eefcd88ea6f77a2ae672b96=$this->gretret['_638099314_'][2]($_8409eaa6ec0ce2ea307354b2e150f8c2,PHP_URL_PATH);if($this->l__56a589c102d55f2199048a0f1a73f058($_6629c5988eefcd88ea6f77a2ae672b96,[$this->_1507492973(59),$this->_1507492973(60)])=== false){if($this->gretret['_638099314_'][3]($this->_1507492973(61),$_6629c5988eefcd88ea6f77a2ae672b96,$_ca53e6c0538f536b092f4738d0baaaa1)){$_8409eaa6ec0ce2ea307354b2e150f8c2=$_ca53e6c0538f536b092f4738d0baaaa1[$this->_1507492973(62)] .$this->language->get($this->_1507492973(63));}}else{$_6629c5988eefcd88ea6f77a2ae672b96=$this->gretret['_638099314_'][4]([$this->_1507492973(64),$this->_1507492973(65)],[$this->_1507492973(66),$this->_1507492973(67)],$_6629c5988eefcd88ea6f77a2ae672b96);if($this->gretret['_638099314_'][5]($this->_1507492973(68),$_6629c5988eefcd88ea6f77a2ae672b96,$_ca53e6c0538f536b092f4738d0baaaa1)){$_8409eaa6ec0ce2ea307354b2e150f8c2=$_ca53e6c0538f536b092f4738d0baaaa1[$this->_1507492973(69)] .$this->language->get($this->_1507492973(70));}}$_e4a3f5f7a18b1ed0ee22a93864ad15d8=round(0+2.5+2.5);$_679e9b9234e2062f809dbd3325d37fb6=$this->gretret['_638099314_'][6]($this->gretret['_638099314_'][7]($_8409eaa6ec0ce2ea307354b2e150f8c2),round(0),$_e4a3f5f7a18b1ed0ee22a93864ad15d8);$_a16d2280393ce6a2a5428a4a8d09e354=$_e4a3f5f7a18b1ed0ee22a93864ad15d8;while($_a16d2280393ce6a2a5428a4a8d09e354<$this->gretret['_638099314_'][8]($this->gretret['_638099314_'][9]($_8409eaa6ec0ce2ea307354b2e150f8c2))){$_679e9b9234e2062f809dbd3325d37fb6 .= $this->_1507492973(71);$_679e9b9234e2062f809dbd3325d37fb6 .= $this->gretret['_638099314_'][10]($this->gretret['_638099314_'][11]($_8409eaa6ec0ce2ea307354b2e150f8c2),$_a16d2280393ce6a2a5428a4a8d09e354,$_e4a3f5f7a18b1ed0ee22a93864ad15d8);$_a16d2280393ce6a2a5428a4a8d09e354=$_a16d2280393ce6a2a5428a4a8d09e354+$_e4a3f5f7a18b1ed0ee22a93864ad15d8;}return $this->gretret['_638099314_'][12]($_679e9b9234e2062f809dbd3325d37fb6);}protected function _1161912638($i){$a=['c2VydmVy','bGFuZ3VhZ2U=','cmVxdWVzdA=='];return base64_decode($a[$i]);}protected function l__6262eb5c2a83c9f29edc0359ada36fe4(){if(!$this->user->hasPermission('modify','extension/theme/oct_ultrastore')){$this->error['warning']=$this->language->get('error_permission');}return!$this->error;}
}
