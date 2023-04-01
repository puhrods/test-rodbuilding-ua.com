<?php
/**************************************************************/
/*	@copyright	OCTemplates 2015-2019.						  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ModelOCTemplatesDesignOctSlideshowPlus extends Model {
    public function addSlideshow($data) {
	    $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
	    $data['position_banners'] = (isset($data['position_banners']) && $data['position_banners'] == 1) ? 1 : 0;
	    $data['status_additional_banners'] = (isset($data['status_additional_banners']) && $data['status_additional_banners'] == 'on') ? 1 : 0;

        $this->db->query("
			INSERT INTO " . DB_PREFIX . "oct_slideshow_plus
			SET
				name = '" . $this->db->escape($data['name']) . "',
				pag_background = '" . $this->db->escape($data['pag_background']) . "',
				pag_background_active = '" . $this->db->escape($data['pag_background_active']) . "',
				status_additional_banners = '" . (int)$data['status_additional_banners'] . "',
				position_banners = '" . (int)$data['position_banners'] . "',
				status = '" . (int)$data['status'] . "'
		");

        $slideshow_id = $this->db->getLastId();

        if (isset($data['oct_slideshow_plus_image'])) {
            foreach ($data['oct_slideshow_plus_image'] as $oct_slideshow_plus_image) {
                $this->db->query("
					INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_image
					SET
						slideshow_id = '" . (int)$slideshow_id . "',
						image = '" . $this->db->escape($oct_slideshow_plus_image['image']) . "',
						background_color = '" . $this->db->escape($oct_slideshow_plus_image['background_color']) . "',
						title_color = '" . $this->db->escape($oct_slideshow_plus_image['title_color']) . "',
						text_color = '" . $this->db->escape($oct_slideshow_plus_image['text_color']) . "',
						button_color = '" . $this->db->escape($oct_slideshow_plus_image['button_color']) . "',
						button_background = '" . $this->db->escape($oct_slideshow_plus_image['button_background']) . "',
						button_color_hover = '" . $this->db->escape($oct_slideshow_plus_image['button_color_hover']) . "',
						button_background_hover = '" . $this->db->escape($oct_slideshow_plus_image['button_background_hover']) . "',
						sort_order = '" . (int)$oct_slideshow_plus_image['sort_order'] . "'
				");

                $slideshow_image_id = $this->db->getLastId();

                foreach ($oct_slideshow_plus_image['oct_slideshow_plus_image_description'] as $language_id => $oct_slideshow_plus_image_description) {
                    $this->db->query("
						INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_image_description
						SET
							slideshow_image_id = '" . (int)$slideshow_image_id . "',
							language_id = '" . (int)$language_id . "',
							slideshow_id = '" . (int)$slideshow_id . "',
							title = '" . $this->db->escape($oct_slideshow_plus_image_description['title']) . "',
							link = '" . $this->db->escape($oct_slideshow_plus_image_description['link']) . "',
							button = '" . $this->db->escape($oct_slideshow_plus_image_description['button']) . "',
							description = '" . $this->db->escape($oct_slideshow_plus_image_description['description']) . "'
					");
                }
            }
        }

        if (isset($data)) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_banner
				SET
					slideshow_id = '" . (int)$slideshow_id . "',
					b1_image = '" . $this->db->escape($data['b1_image']) . "',
					b1_button_background = '" . $this->db->escape($data['b1_button_background']) . "',
					b1_button_background_hover = '" . $this->db->escape($data['b1_button_background_hover']) . "',
					b1_button_color = '" . $this->db->escape($data['b1_button_color']) . "',
					b1_button_color_hover = '" . $this->db->escape($data['b1_button_color_hover']) . "',
					b1_title_background = '" . $this->db->escape($data['b1_title_background']) . "',
					b1_title_color = '" . $this->db->escape($data['b1_title_color']) . "',
					b2_image = '" . $this->db->escape($data['b2_image']) . "',
					b2_button_background = '" . $this->db->escape($data['b2_button_background']) . "',
					b2_button_background_hover = '" . $this->db->escape($data['b2_button_background_hover']) . "',
					b2_button_color = '" . $this->db->escape($data['b2_button_color']) . "',
					b2_button_color_hover = '" . $this->db->escape($data['b2_button_color_hover']) . "',
					b2_title_background = '" . $this->db->escape($data['b2_title_background']) . "',
					b2_title_color = '" . $this->db->escape($data['b2_title_color']) . "'
			");

			foreach ($data['oct_slideshow_plus_banner_description'] as $language_id => $banner_descr) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_banner_description
					SET
						language_id = '" . (int)$language_id . "',
						slideshow_id = '" . (int)$slideshow_id . "',
						b1_banner_title = '" . $this->db->escape($banner_descr['b1_banner_title']) . "',
						b1_banner_button = '" . $this->db->escape($banner_descr['b1_banner_button']) . "',
						b1_banner_link = '" . $this->db->escape($banner_descr['b1_banner_link']) . "',
						b2_banner_title = '" . $this->db->escape($banner_descr['b2_banner_title']) . "',
						b2_banner_button = '" . $this->db->escape($banner_descr['b2_banner_button']) . "',
						b2_banner_link = '" . $this->db->escape($banner_descr['b2_banner_link']) . "'
				");
			}
        }

        return $slideshow_id;
    }

    public function editSlideshow($slideshow_id, $data) {
	    $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
	    $data['position_banners'] = (isset($data['position_banners']) && $data['position_banners'] == 1) ? 1 : 0;
	    $data['status_additional_banners'] = (isset($data['status_additional_banners']) && $data['status_additional_banners'] == 'on') ? 1 : 0;

        $this->db->query("
			UPDATE " . DB_PREFIX . "oct_slideshow_plus
			SET
				name = '" . $this->db->escape($data['name']) . "',
				pag_background = '" . $this->db->escape($data['pag_background']) . "',
				pag_background_active = '" . $this->db->escape($data['pag_background_active']) . "',
				status_additional_banners = '" . (int) $data['status_additional_banners'] . "',
				position_banners = '" . (int) $data['position_banners'] . "',
				status = '" . (int)$data['status'] . "'
			WHERE
				slideshow_id = '" . (int)$slideshow_id . "'
		");

        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_image WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_image_description WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_banner WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_banner_description WHERE slideshow_id = '" . (int)$slideshow_id . "'");

        if (isset($data['oct_slideshow_plus_image'])) {
            foreach ($data['oct_slideshow_plus_image'] as $oct_slideshow_plus_image) {
                $this->db->query("
					INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_image
					SET
						slideshow_id = '" . (int)$slideshow_id . "',
						image = '" . $this->db->escape($oct_slideshow_plus_image['image']) . "',
						background_color = '" . $this->db->escape($oct_slideshow_plus_image['background_color']) . "',
						title_color = '" . $this->db->escape($oct_slideshow_plus_image['title_color']) . "',
						text_color = '" . $this->db->escape($oct_slideshow_plus_image['text_color']) . "',
						button_color = '" . $this->db->escape($oct_slideshow_plus_image['button_color']) . "',
						button_background = '" . $this->db->escape($oct_slideshow_plus_image['button_background']) . "',
						button_color_hover = '" . $this->db->escape($oct_slideshow_plus_image['button_color_hover']) . "',
						button_background_hover = '" . $this->db->escape($oct_slideshow_plus_image['button_background_hover']) . "',
						sort_order = '" . (int)$oct_slideshow_plus_image['sort_order'] . "'
				");

                $slideshow_image_id = $this->db->getLastId();

                foreach ($oct_slideshow_plus_image['oct_slideshow_plus_image_description'] as $language_id => $oct_slideshow_plus_image_description) {
                    $this->db->query("
						INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_image_description
						SET
							slideshow_image_id = '" . (int)$slideshow_image_id . "',
							language_id = '" . (int)$language_id . "',
							slideshow_id = '" . (int)$slideshow_id . "',
							title = '" . $this->db->escape($oct_slideshow_plus_image_description['title']) . "',
							link = '" . $this->db->escape($oct_slideshow_plus_image_description['link']) . "',
							button = '" . $this->db->escape($oct_slideshow_plus_image_description['button']) . "',
							description = '" . $this->db->escape($oct_slideshow_plus_image_description['description']) . "'
					");
                }
            }
        }

        if (isset($data)) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_banner
				SET
					slideshow_id = '" . (int)$slideshow_id . "',
					b1_image = '" . $this->db->escape($data['b1_image']) . "',
					b1_button_background = '" . $this->db->escape($data['b1_button_background']) . "',
					b1_button_background_hover = '" . $this->db->escape($data['b1_button_background_hover']) . "',
					b1_button_color = '" . $this->db->escape($data['b1_button_color']) . "',
					b1_button_color_hover = '" . $this->db->escape($data['b1_button_color_hover']) . "',
					b1_title_background = '" . $this->db->escape($data['b1_title_background']) . "',
					b1_title_color = '" . $this->db->escape($data['b1_title_color']) . "',
					b2_image = '" . $this->db->escape($data['b2_image']) . "',
					b2_button_background = '" . $this->db->escape($data['b2_button_background']) . "',
					b2_button_background_hover = '" . $this->db->escape($data['b2_button_background_hover']) . "',
					b2_button_color = '" . $this->db->escape($data['b2_button_color']) . "',
					b2_button_color_hover = '" . $this->db->escape($data['b2_button_color_hover']) . "',
					b2_title_background = '" . $this->db->escape($data['b2_title_background']) . "',
					b2_title_color = '" . $this->db->escape($data['b2_title_color']) . "'
			");

			foreach ($data['oct_slideshow_plus_banner_description'] as $language_id => $banner_descr) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "oct_slideshow_plus_banner_description
					SET
						language_id = '" . (int)$language_id . "',
						slideshow_id = '" . (int)$slideshow_id . "',
						b1_banner_title = '" . $this->db->escape($banner_descr['b1_banner_title']) . "',
						b1_banner_button = '" . $this->db->escape($banner_descr['b1_banner_button']) . "',
						b1_banner_link = '" . $this->db->escape($banner_descr['b1_banner_link']) . "',
						b2_banner_title = '" . $this->db->escape($banner_descr['b2_banner_title']) . "',
						b2_banner_button = '" . $this->db->escape($banner_descr['b2_banner_button']) . "',
						b2_banner_link = '" . $this->db->escape($banner_descr['b2_banner_link']) . "'
				");
			}
        }

    }

    public function deleteSlideshow($slideshow_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_image WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_image_description WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_banner WHERE slideshow_id = '" . (int)$slideshow_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_slideshow_plus_banner_description WHERE slideshow_id = '" . (int)$slideshow_id . "'");
    }

    public function getSlideshow($slideshow_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "oct_slideshow_plus WHERE slideshow_id = '" . (int)$slideshow_id . "'");

        return $query->row;
    }

    public function getSlideshowBanner($slideshow_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "oct_slideshow_plus_banner WHERE slideshow_id = '" . (int)$slideshow_id . "'");

        return $query->row;
    }

    public function getSlideshows($data = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "oct_slideshow_plus";

        $sort_data = [
            'name',
            'status'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getSlideshowImages($slideshow_id) {
        $oct_slideshow_plus_image_data = [];

        $oct_slideshow_plus_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_slideshow_plus_image WHERE slideshow_id = '" . (int)$slideshow_id . "' ORDER BY sort_order ASC");

        foreach ($oct_slideshow_plus_image_query->rows as $oct_slideshow_plus_image) {
            $oct_slideshow_plus_image_description_data = [];

            $oct_slideshow_plus_image_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_slideshow_plus_image_description WHERE slideshow_image_id = '" . (int)$oct_slideshow_plus_image['slideshow_image_id'] . "' AND slideshow_id = '" . (int)$slideshow_id . "'");

            foreach ($oct_slideshow_plus_image_description_query->rows as $oct_slideshow_plus_image_description) {
                $oct_slideshow_plus_image_description_data[$oct_slideshow_plus_image_description['language_id']] = [
                    'title' => $oct_slideshow_plus_image_description['title'],
                    'link' => $oct_slideshow_plus_image_description['link'],
                    'button' => $oct_slideshow_plus_image_description['button'],
                    'description' => $oct_slideshow_plus_image_description['description']
                ];
            }

            $oct_slideshow_plus_image_data[] = [
                'oct_slideshow_plus_image_description' => $oct_slideshow_plus_image_description_data,
                'image' => $oct_slideshow_plus_image['image'],
                'background_color' => $oct_slideshow_plus_image['background_color'],
                'title_color' => $oct_slideshow_plus_image['title_color'],
                'text_color' => $oct_slideshow_plus_image['text_color'],
                'button_color' => $oct_slideshow_plus_image['button_color'],
                'button_background' => $oct_slideshow_plus_image['button_background'],
                'button_color_hover' => $oct_slideshow_plus_image['button_color_hover'],
                'button_background_hover' => $oct_slideshow_plus_image['button_background_hover'],
                'sort_order' => $oct_slideshow_plus_image['sort_order']
            ];
        }

        return $oct_slideshow_plus_image_data;
    }

    public function getSlideshowBanners($slideshow_id) {
		$oct_slideshow_plus_banner_data = [];

		$oct_slideshow_plus_banner_query_main = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_slideshow_plus_banner WHERE slideshow_id = '" . (int)$slideshow_id . "'");

		foreach ($oct_slideshow_plus_banner_query_main->rows as $banner_data) {
			$oct_slideshow_plus_banner_description_data = [];

			$oct_slideshow_plus_banner_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_slideshow_plus_banner_description WHERE slideshow_id = '" . (int)$slideshow_id . "'");

			foreach ($oct_slideshow_plus_banner_description_query->rows as $oct_slideshow_plus_banner_description) {
				$oct_slideshow_plus_banner_description_data[$oct_slideshow_plus_banner_description['language_id']] = [
					'b1_banner_title' => $oct_slideshow_plus_banner_description['b1_banner_title'],
					'b1_banner_button' => $oct_slideshow_plus_banner_description['b1_banner_button'],
					'b1_banner_link' => $oct_slideshow_plus_banner_description['b1_banner_link'],
					'b2_banner_title' => $oct_slideshow_plus_banner_description['b2_banner_title'],
					'b2_banner_button' => $oct_slideshow_plus_banner_description['b2_banner_button'],
					'b2_banner_link' => $oct_slideshow_plus_banner_description['b2_banner_link']
				];
			}

			$oct_slideshow_plus_banner_data = [
				'b1_image' => $banner_data['b1_image'],
				'b1_button_background' => $banner_data['b1_button_background'],
				'b1_button_background_hover' => $banner_data['b1_button_background_hover'],
				'b1_button_color' => $banner_data['b1_button_color'],
				'b1_button_color_hover' => $banner_data['b1_button_color_hover'],
				'b1_title_background' => $banner_data['b1_title_background'],
				'b1_title_color' => $banner_data['b1_title_color'],
				'b2_image' => $banner_data['b2_image'],
				'b2_button_background' => $banner_data['b2_button_background'],
				'b2_button_background_hover' => $banner_data['b2_button_background_hover'],
				'b2_button_color' => $banner_data['b2_button_color'],
				'b2_button_color_hover' => $banner_data['b2_button_color_hover'],
				'b2_title_background' => $banner_data['b2_title_background'],
				'b2_title_color' => $banner_data['b2_title_color']
			];

			$oct_slideshow_plus_banner_data['oct_slideshow_plus_banner_description'] =  $oct_slideshow_plus_banner_description_data;
		}

        return $oct_slideshow_plus_banner_data;
    }

    public function getTotalSlideshows() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "oct_slideshow_plus");

        return $query->row['total'];
    }

    public function createDBTables() {
        $sql_01 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_slideshow_plus` ( ";
        $sql_01 .= "`slideshow_id` int(11) NOT NULL AUTO_INCREMENT, ";
        $sql_01 .= "`name` varchar(64) NOT NULL, ";
        $sql_01 .= "`pag_background` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_01 .= "`pag_background_active` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_01 .= "`status_additional_banners` tinyint(1) NOT NULL DEFAULT '1', ";
        $sql_01 .= "`position_banners` tinyint(1) NOT NULL DEFAULT '1', ";
        $sql_01 .= "`status` tinyint(1) NOT NULL, ";
        $sql_01 .= "PRIMARY KEY (`slideshow_id`) ";
        $sql_01 .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";

        $this->db->query($sql_01);

        $sql_02 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_slideshow_plus_image` ( ";
        $sql_02 .= "`slideshow_image_id` int(11) NOT NULL AUTO_INCREMENT, ";
        $sql_02 .= "`slideshow_id` int(11) NOT NULL, ";
        $sql_02 .= "`image` varchar(255) NOT NULL, ";
        $sql_02 .= "`background_color` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`title_color` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`text_color` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`button_color` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`button_background` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`button_color_hover` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`button_background_hover` varchar(255) COLLATE 'utf8_general_ci' NOT NULL, ";
        $sql_02 .= "`sort_order` int(3) NOT NULL DEFAULT '0', ";
        $sql_02 .= "PRIMARY KEY (`slideshow_image_id`) ";
        $sql_02 .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ; ";

        $this->db->query($sql_02);

        $sql_03 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_slideshow_plus_image_description` ( ";
        $sql_03 .= "`slideshow_image_id` int(11) NOT NULL, ";
        $sql_03 .= "`language_id` int(11) NOT NULL, ";
        $sql_03 .= "`slideshow_id` int(11) NOT NULL, ";
        $sql_03 .= "`title` varchar(64) NOT NULL, ";
        $sql_03 .= "`link` varchar(255) NOT NULL, ";
        $sql_03 .= "`button` varchar(64) NOT NULL, ";
        $sql_03 .= "`description` text NOT NULL, ";
        $sql_03 .= "PRIMARY KEY (`slideshow_image_id`,`language_id`) ";
        $sql_03 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ";

        $this->db->query($sql_03);

        $sql_04 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_slideshow_plus_banner` ( ";
        $sql_04 .= "`slideshow_id` int(11) NOT NULL, ";
        $sql_04 .= "`b1_image` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_button_background` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_button_background_hover` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_button_color` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_button_color_hover` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_title_background` varchar(255) NOT NULL, ";
        $sql_04 .= "`b1_title_color` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_image` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_button_background` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_button_background_hover` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_button_color` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_button_color_hover` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_title_background` varchar(255) NOT NULL, ";
        $sql_04 .= "`b2_title_color` varchar(255) NOT NULL ";
        $sql_04 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ";

        $this->db->query($sql_04);

        $sql_05 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_slideshow_plus_banner_description` ( ";
        $sql_05 .= "`language_id` int(11) NOT NULL, ";
        $sql_05 .= "`slideshow_id` int(11) NOT NULL, ";
        $sql_05 .= "`b1_banner_button` varchar(64) NOT NULL, ";
        $sql_05 .= "`b1_banner_title` varchar(255) NOT NULL, ";
        $sql_05 .= "`b1_banner_link` varchar(255) NOT NULL, ";
        $sql_05 .= "`b2_banner_button` varchar(64) NOT NULL, ";
        $sql_05 .= "`b2_banner_title` varchar(255) NOT NULL, ";
        $sql_05 .= "`b2_banner_link` varchar(255) NOT NULL ";
        $sql_05 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8 ; ";

        $this->db->query($sql_05);
    }
}
