<?php
/**************************************************************/
/*	@copyright	OCTemplates 2016-2019						  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ControllerExtensionModuleOctSlideshowPlus extends Controller {
	public function index($setting) {
		static $module = 0;

		$oct_ultrastore_data = $this->config->get('theme_oct_ultrastore_data');

		$this->load->model('octemplates/module/oct_slideshow_plus');
		$this->load->model('tool/image');

		$data['oct_slideshows_plus'] = $results = [];

		$this->document->addScript('catalog/view/theme/oct_ultrastore/js/slick/slick.min.js');
		$this->document->addStyle('catalog/view/theme/oct_ultrastore/js/slick/slick.min.css');

		if (isset($this->request->get['path'])) {
            $parts = explode('_', (string)$this->request->get['path']);
        } else {
            $parts = [];
        }

        $category_id = (int)array_pop($parts);

		if (isset($this->request->get['product_id'])) {
            $product_id = (int)$this->request->get['product_id'];
            unset($setting['show_in_categories']);
        } else {
            $product_id = 0;
        }

        if (isset($setting['show_in_categories']) && $setting['show_in_categories']) {
            if (in_array($category_id, $setting['show_in_categories'])) {
                $results = $this->model_octemplates_module_oct_slideshow_plus->getSlideshow($setting['slideshow_id']);
            }
        } else {
            if (isset($setting['show_in_products']) && $setting['show_in_products']) {
	            if (in_array($product_id, $setting['show_in_products'])) {
	                $results = $this->model_octemplates_module_oct_slideshow_plus->getSlideshow($setting['slideshow_id']);
	            }
	        } else {
	            $results = $this->model_octemplates_module_oct_slideshow_plus->getSlideshow($setting['slideshow_id']);
	        }
        }

		foreach ($results as $key => $result) {
			if (is_file(DIR_IMAGE.$result['image'])) {
				if (isset($oct_ultrastore_data['preload_images']) && $oct_ultrastore_data['preload_images'] && $result['image'] && $key == 0) {
					$this->document->setOCTPreload($this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']));
				}

				$data['pag_background']				= $result['pag_background'];
				$data['pag_background_active']		= $result['pag_background_active'];
				$data['status_additional_banners']	= $result['status_additional_banners'];
				$data['position_banners']			= $result['position_banners'];

				$data['oct_slideshows_plus'][] = [
					'title'                  => $result['title'],
					'button'                 => $result['button'],
					'link'                   => ($result['link'] == '#' or empty($result['link'])) ? 'javascript:;' : $result['link'],
					'background_color'       => $result['background_color'],
					'title_color'            => $result['title_color'],
					'text_color'             => $result['text_color'],
					'button_color'           => $result['button_color'],
					'button_background'      => $result['button_background'],
					'button_color_hover'     => $result['button_color_hover'],
					'button_background_hover' => $result['button_background_hover'],
					'description'            => html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'),
					'image'                  => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
					'width'					 => $setting['width'],
					'height'				 => $setting['height']
				];
			}
		}

		$result_banners = $this->model_octemplates_module_oct_slideshow_plus->getSlideshowBanners($setting['slideshow_id']);

		$data['oct_slideshows_plus_banners'] = [
            'b1_image'						=> $this->model_tool_image->resize($result_banners['b1_image'], $setting['dop_width'], $setting['dop_height']),
            'b1_button_background'			=> $result_banners['b1_button_background'],
            'b1_button_background_hover'	=> $result_banners['b1_button_background_hover'],
            'b1_button_color'				=> $result_banners['b1_button_color'],
            'b1_button_color_hover'			=> $result_banners['b1_button_color_hover'],
            'b1_title_background'			=> $result_banners['b1_title_background'],
            'b1_title_color'				=> $result_banners['b1_title_color'],
            'b2_image'						=> $this->model_tool_image->resize($result_banners['b2_image'], $setting['dop_width'], $setting['dop_height']),
			'width'					 		=> $setting['dop_width'],
			'height'				 		=> $setting['dop_height'],
            'b2_button_background'			=> $result_banners['b2_button_background'],
            'b2_button_background_hover'	=> $result_banners['b2_button_background_hover'],
            'b2_button_color'				=> $result_banners['b2_button_color'],
            'b2_button_color_hover'			=> $result_banners['b2_button_color_hover'],
            'b2_title_background'			=> $result_banners['b2_title_background'],
            'b2_title_color'				=> $result_banners['b2_title_color'],
            'b1_banner_title'				=> $result_banners['b1_banner_title'],
            'b1_banner_button'				=> $result_banners['b1_banner_button'],
            'b1_banner_link'				=> ($result_banners['b1_banner_link'] == '#' or empty($result_banners['b1_banner_link'])) ? 'javascript:;' : $result_banners['b1_banner_link'],
            'b2_banner_title'				=> $result_banners['b2_banner_title'],
            'b2_banner_button'				=> $result_banners['b2_banner_button'],
			'b2_banner_link'				=> ($result_banners['b2_banner_link'] == '#' or empty($result_banners['b2_banner_link'])) ? 'javascript:;' : $result_banners['b2_banner_link']
		];

		$data['module'] = $module++;

		$data['slideshow_id']                         = $setting['slideshow_id'];

		return $this->load->view('octemplates/module/oct_slideshow_plus', $data);
	}
}
