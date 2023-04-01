<?php
/**********************************************************/
/*	@copyright	OCTemplates 2019.						  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**********************************************************/

class ModelOCTemplatesStickersOctStickers extends Model {
	public function getOCTStickers($result) {
		$oct_stickers_data = $oct_stikers = [];

		if ($this->config->get('oct_stickers_status')) {
			$oct_stickers = $this->config->get('oct_stickers_data');

			if (isset($result['special']) && (float)$result['special']) {
				$special = true;
			} else {
				$special = false;
			}

			if ((isset($oct_stickers['stickers']['new']['status']) && $oct_stickers['stickers']['new']['status']) && (isset($oct_stickers['stickers']['new']['auto']) && $oct_stickers['stickers']['new']['auto'] == 'on')) {
				if (strtotime($result['date_added']) >= strtotime("-".(int)$oct_stickers['stickers']['new']['count']." day", time())) {
					$oct_stickers_data['stickers']['stickers_new'] = [
						'title' => (isset($oct_stickers['stickers']['new']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['new']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['new']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_new'),
						'sort' => $oct_stickers['stickers']['new']['sort']
					];
				}
			}

			if ((isset($oct_stickers['stickers']['bestseller']['status']) && $oct_stickers['stickers']['bestseller']['status']) && (isset($oct_stickers['stickers']['bestseller']['auto']) && $oct_stickers['stickers']['bestseller']['auto'] == 'on')) {
				if ($this->model_catalog_product->getOCTBestSellerProducts($result['product_id']) >= (int)$oct_stickers['stickers']['bestseller']['count']) {
					$oct_stickers_data['stickers']['stickers_bestseller'] = [
						'title' => (isset($oct_stickers['stickers']['bestseller']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['bestseller']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['bestseller']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_bestseller'),
						'sort' => $oct_stickers['stickers']['bestseller']['sort']
					];
				}
			}

			if ((isset($oct_stickers['stickers']['popular']['status']) && $oct_stickers['stickers']['popular']['status']) && (isset($oct_stickers['stickers']['popular']['auto']) && $oct_stickers['stickers']['popular']['auto'] == 'on')) {
				if ($result['viewed'] > (int)$oct_stickers['stickers']['popular']['count']) {
					$oct_stickers_data['stickers']['stickers_popular'] = [
						'title' => (isset($oct_stickers['stickers']['popular']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['popular']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['popular']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_popular'),
						'sort' => $oct_stickers['stickers']['popular']['sort']
					];
				}
			}

			if ((isset($oct_stickers['stickers']['special']['status']) && $oct_stickers['stickers']['special']['status']) && (isset($oct_stickers['stickers']['special']['auto']) && $oct_stickers['stickers']['special']['auto'] == 'on')) {
				if ($special) {
					$oct_stickers_data['stickers']['stickers_special'] = [
						'title' => isset($oct_stickers['stickers']['special']['view_title']) ? ((isset($oct_stickers['stickers']['special']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['special']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['special']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_special')) : false,
						'sort' => $oct_stickers['stickers']['special']['sort']
					];
				}
			}

			if ((isset($oct_stickers['stickers']['sold']['status']) && $oct_stickers['stickers']['sold']['status']) && (isset($oct_stickers['stickers']['sold']['auto']) && $oct_stickers['stickers']['sold']['auto'] == 'on')) {
				if ((int)$result['quantity'] == (int)$oct_stickers['stickers']['sold']['count']) {
					$oct_stickers_data['stickers']['stickers_sold'] = [
						'title' => (isset($oct_stickers['stickers']['sold']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['sold']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['sold']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_sold'),
						'sort' => $oct_stickers['stickers']['sold']['sort']
					];
				}
			}

			if ((isset($oct_stickers['stickers']['ends']['status']) && $oct_stickers['stickers']['ends']['status']) && (isset($oct_stickers['stickers']['ends']['auto']) && $oct_stickers['stickers']['ends']['auto'] == 'on')) {
				if ((int)$result['quantity'] <= (int)$oct_stickers['stickers']['ends']['count'] && (int)$result['quantity'] > 0) {
					$oct_stickers_data['stickers']['stickers_ends'] = [
						'title' => (isset($oct_stickers['stickers']['ends']['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers['stickers']['ends']['title'][(int)$this->config->get('config_language_id')])) ? $oct_stickers['stickers']['ends']['title'][(int)$this->config->get('config_language_id')] : $this->language->get('entry_sticker_ends'),
						'sort' => $oct_stickers['stickers']['ends']['sort']
					];
				}
			}

			if (isset($result['oct_stickers']) && !empty($result['oct_stickers'])) {
				foreach ($result['oct_stickers'] as $oct_sticker) {
					$oct_t_sticker = explode('_',$oct_sticker);

					if ((isset($oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['status']) && $oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['status'])) {
						if (isset($oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['title'][(int)$this->config->get('config_language_id')]) && !empty($oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['title'][(int)$this->config->get('config_language_id')])) {
							$oct_stickers_data['stickers']['stickers_'.$oct_t_sticker[1]] = [
								'title' => $oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['title'][(int)$this->config->get('config_language_id')],
								'sort' => $oct_stickers[$oct_t_sticker[0]][$oct_t_sticker[1]]['sort']
							];
						}
					}
				}
			}

			$i_sort_order = [];

			if (isset($oct_stickers_data['stickers']) && !empty($oct_stickers_data['stickers'])) {
				foreach ($oct_stickers_data['stickers'] as $key => $value) {
					$i_sort_order[$key] = $value['sort'];
				}

				array_multisort($i_sort_order, SORT_ASC, $oct_stickers_data['stickers']);

				foreach ($oct_stickers_data['stickers'] as $st => $oct_stiker) {
					$oct_stikers['stickers'][$st] = $oct_stiker['title'];
				}
			}
		}

		return $oct_stikers;
	}
}
