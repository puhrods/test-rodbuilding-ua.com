<?php
/**************************************************************/
/*	@copyright	OCTemplates 2015-2019.						  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ModelOCTemplatesModuleOctMegamenu extends Model {
    public function addMegamenu($data = []) {
	    $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
	    $data['sub_categories'] = (isset($data['sub_categories']) && $data['sub_categories'] == 'on') ? 1 : 0;
	    $data['show_img'] = (isset($data['show_img']) && $data['show_img'] == 'on') ? 1 : 0;
	    $data['info_text'] = (isset($data['info_text']) && $data['info_text'] == 'on') ? 1 : 0;
	    $data['open_link_type'] = (isset($data['open_link_type']) && $data['open_link_type'] == 'on') ? 1 : 0;

        $this->db->query("
        	INSERT INTO " . DB_PREFIX . "oct_megamenu
        	SET
        		sort_order = '" . (int)$data['sort_order'] . "',
        		item_type = '" . (int)$data['item_type'] . "',
        		status = '" . (int)$data['status'] . "',
        		img_width = '" . (int)$data['img_width'] . "',
        		img_height = '" . (int)$data['img_height'] . "',
        		limit_item = '" . (int)$data['limit_item'] . "',
        		show_img = '" . (int)$data['show_img'] . "',
        		info_text = '" . (int)$data['info_text'] . "',
        		sub_categories = '" . (int)$data['sub_categories'] . "',
        		open_link_type = '" . (int)$data['open_link_type'] . "',
        		display_type = '" . (int)$data['display_type'] . "',
        		banner_image = '" . $this->db->escape($data['oct_megamenu_banner_image']) . "',
        		banner_image_color = '" . $this->db->escape($data['oct_megamenu_banner_image_color']) . "',
        		banner_image_text_color = '" . $this->db->escape($data['oct_megamenu_banner_image_text_color']) . "',
        		banner_image_title_color = '" . $this->db->escape($data['oct_megamenu_banner_image_title_color']) . "',
        		banner_image_button_color = '" . $this->db->escape($data['oct_megamenu_banner_image_button_color']) . "',
        		banner_image_button_hover_color = '" . $this->db->escape($data['oct_megamenu_banner_image_button_hover_color']) . "',
        		banner_image_link_color = '" . $this->db->escape($data['oct_megamenu_banner_image_link_color']) . "',
        		banner_image_link_hover_color = '" . $this->db->escape($data['oct_megamenu_banner_image_link_hover_color']) . "',
        		date_added = NOW()
        ");

        $megamenu_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("
            	UPDATE " . DB_PREFIX . "oct_megamenu
            	SET
            		image = '" . $this->db->escape($data['image']) . "'
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");
        }

        foreach ($data['oct_megamenu_description'] as $language_id => $value) {
            $this->db->query("
            	INSERT INTO " . DB_PREFIX . "oct_megamenu_description
            	SET
            		megamenu_id = '" . (int)$megamenu_id . "',
            		language_id = '" . (int)$language_id . "',
            		link = '" . $this->db->escape($value['link']) . "',
            		title = '" . $this->db->escape($value['title']) . "',
            		custom_html = '" . $this->db->escape($value['custom_html']) . "',
            		banner_title = '" . $this->db->escape($value['banner_title']) . "',
            		banner_text = '" . $this->db->escape($value['banner_text']) . "',
            		banner_link = '" . $this->db->escape($value['banner_link']) . "',
            		banner_button = '" . $this->db->escape($value['banner_button']) . "'
            ");
        }

        if (isset($data['store'])) {
            foreach ($data['store'] as $store_id) {
                $this->db->query("
                	INSERT INTO " . DB_PREFIX . "oct_megamenu_to_store
                	SET
                		megamenu_id = '" . (int)$megamenu_id . "',
                		store_id = '" . (int)$store_id . "'
                ");
            }
        }

        if (isset($data['customer_group'])) {
            foreach ($data['customer_group'] as $customer_group_id) {
                $this->db->query("
                	INSERT INTO " . DB_PREFIX . "oct_megamenu_to_customer_group
                	SET
                		megamenu_id = '" . (int)$megamenu_id . "',
                		customer_group_id = '" . (int)$customer_group_id . "'
                ");
            }
        }

        if (isset($data['item_type'])) {
            if ($data['item_type'] == 2) {
                if (isset($data['oct_megamenu_categories'])) {
                    foreach ($data['oct_megamenu_categories'] as $category_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_category
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND category_id = '" . (int)$category_id . "'
                        ");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_category
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		category_id = '" . (int)$category_id . "'
                        ");
                    }
                }
            }

            if ($data['item_type'] == 3) {
                if (isset($data['oct_megamenu_manufacturers'])) {
                    foreach ($data['oct_megamenu_manufacturers'] as $manufacturer_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_manufacturer
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND manufacturer_id = '" . (int)$manufacturer_id . "'
                        ");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_manufacturer
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		manufacturer_id = '" . (int)$manufacturer_id . "'
                        ");
                    }
                }
            }

            if ($data['item_type'] == 4) {
                if (isset($data['oct_megamenu_products'])) {
                    $this->load->model('catalog/product');

                    foreach ($data['oct_megamenu_products'] as $product_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_product
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND product_id = '" . (int)$product_id . "'
						");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_product
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		product_id = '" . (int)$product_id . "'
                        ");
                    }
                }
            }

            if ($data['item_type'] == 5) {
                if (isset($data['oct_megamenu_informations'])) {
                    foreach ($data['oct_megamenu_informations'] as $information_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_information
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND information_id = '" . (int)$information_id . "'
                        ");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_information
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		information_id = '" . (int)$information_id . "'
                        ");
                    }
                }
            }

            if ($data['item_type'] == 8) {
                if (isset($data['oct_megamenu_blogcategories'])) {
                    foreach ($data['oct_megamenu_blogcategories'] as $blogcategory_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_blogcategory
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND blogcategory_id = '" . (int)$blogcategory_id . "'
                        ");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_blogcategory
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		blogcategory_id = '" . (int)$blogcategory_id . "'
                        ");
                    }
                }
            }
        }

        $this->cache->delete('octemplates');

        return $megamenu_id;
    }

    public function editMegamenu($megamenu_id, $data = []) {
	    $data['status'] = (isset($data['status']) && $data['status'] == 'on') ? 1 : 0;
	    $data['sub_categories'] = (isset($data['sub_categories']) && $data['sub_categories'] == 'on') ? 1 : 0;
	    $data['show_img'] = (isset($data['show_img']) && $data['show_img'] == 'on') ? 1 : 0;
	    $data['info_text'] = (isset($data['info_text']) && $data['info_text'] == 'on') ? 1 : 0;
	    $data['open_link_type'] = (isset($data['open_link_type']) && $data['open_link_type'] == 'on') ? 1 : 0;

        $this->db->query("
        	UPDATE " . DB_PREFIX . "oct_megamenu
        	SET
        		sort_order = '" . (int)$data['sort_order'] . "',
        		item_type = '" . (int)$data['item_type'] . "',
        		status = '" . (int)$data['status'] . "',
        		img_width = '" . (int)$data['img_width'] . "',
        		img_height = '" . (int)$data['img_height'] . "',
        		limit_item = '" . (int)$data['limit_item'] . "',
        		show_img = '" . (int)$data['show_img'] . "',
        		info_text = '" . (int)$data['info_text'] . "',
        		sub_categories = '" . (int)$data['sub_categories'] . "',
        		open_link_type = '" . (int)$data['open_link_type'] . "',
        		display_type = '" . (int)$data['display_type'] . "',
        		banner_image = '" . $this->db->escape($data['oct_megamenu_banner_image']) . "',
        		banner_image_color = '" . $this->db->escape($data['oct_megamenu_banner_image_color']) . "',
        		banner_image_text_color = '" . $this->db->escape($data['oct_megamenu_banner_image_text_color']) . "',
        		banner_image_title_color = '" . $this->db->escape($data['oct_megamenu_banner_image_title_color']) . "',
        		banner_image_button_hover_color = '" . $this->db->escape($data['oct_megamenu_banner_image_button_hover_color']) . "',
        		banner_image_link_hover_color = '" . $this->db->escape($data['oct_megamenu_banner_image_link_hover_color']) . "',
        		banner_image_button_color = '" . $this->db->escape($data['oct_megamenu_banner_image_button_color']) . "',
        		banner_image_link_color = '" . $this->db->escape($data['oct_megamenu_banner_image_link_color']) . "'
        	WHERE
        		megamenu_id = '" . (int)$megamenu_id . "'
        ");

        if (isset($data['image'])) {
            $this->db->query("
            	UPDATE " . DB_PREFIX . "oct_megamenu
            	SET
            		image = '" . $this->db->escape($data['image']) . "'
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");
        }

        $this->db->query("
        	DELETE FROM " . DB_PREFIX . "oct_megamenu_description
        	WHERE
        		megamenu_id = '" . (int)$megamenu_id . "'
        ");

        foreach ($data['oct_megamenu_description'] as $language_id => $value) {
            $this->db->query("
            	INSERT INTO " . DB_PREFIX . "oct_megamenu_description
            	SET
            		megamenu_id = '" . (int)$megamenu_id . "',
            		language_id = '" . (int)$language_id . "',
            		link = '" . $this->db->escape($value['link']) . "',
            		title = '" . $this->db->escape($value['title']) . "',
            		custom_html = '" . $this->db->escape($value['custom_html']) . "',
            		banner_title = '" . $this->db->escape($value['banner_title']) . "',
            		banner_text = '" . $this->db->escape($value['banner_text']) . "',
            		banner_link = '" . $this->db->escape($value['banner_link']) . "',
            		banner_button = '" . $this->db->escape($value['banner_button']) . "'
            ");
        }

        $this->db->query("
        	DELETE FROM " . DB_PREFIX . "oct_megamenu_to_store
        	WHERE
        		megamenu_id = '" . (int)$megamenu_id . "'
        ");

        if (isset($data['store'])) {
            foreach ($data['store'] as $store_id) {
                $this->db->query("
                	INSERT INTO " . DB_PREFIX . "oct_megamenu_to_store
                	SET
                		megamenu_id = '" . (int)$megamenu_id . "',
                		store_id = '" . (int)$store_id . "'
                ");
            }
        }

        $this->db->query("
        	DELETE FROM " . DB_PREFIX . "oct_megamenu_to_customer_group
        	WHERE
        		megamenu_id = '" . (int)$megamenu_id . "'
        ");

        if (isset($data['customer_group'])) {
            foreach ($data['customer_group'] as $customer_group_id) {
                $this->db->query("
                	INSERT INTO " . DB_PREFIX . "oct_megamenu_to_customer_group
                	SET
                		megamenu_id = '" . (int)$megamenu_id . "',
                		customer_group_id = '" . (int)$customer_group_id . "'
                ");
            }
        }

        if (isset($data['item_type'])) {
            $this->db->query("
            	DELETE FROM " . DB_PREFIX . "oct_megamenu_category
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");

            if (isset($data['oct_megamenu_categories']) && $data['item_type'] == 2) {
                foreach ($data['oct_megamenu_categories'] as $category_id) {
                    $this->db->query("
                    	DELETE FROM " . DB_PREFIX . "oct_megamenu_category
                    	WHERE
                    		megamenu_id = '" . (int)$megamenu_id . "'
                    		AND category_id = '" . (int)$category_id . "'
                    ");

                    $this->db->query("
                    	INSERT INTO " . DB_PREFIX . "oct_megamenu_category
                    	SET
                    		megamenu_id = '" . (int)$megamenu_id . "',
                    		category_id = '" . (int)$category_id . "'
                    ");
                }
            }

            $this->db->query("
            	DELETE FROM " . DB_PREFIX . "oct_megamenu_manufacturer
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");

            if (isset($data['oct_megamenu_manufacturers']) && $data['item_type'] == 3) {
                foreach ($data['oct_megamenu_manufacturers'] as $manufacturer_id) {
                    $this->db->query("
                    	DELETE FROM " . DB_PREFIX . "oct_megamenu_manufacturer
                    	WHERE
                    		megamenu_id = '" . (int)$megamenu_id . "'
                    		AND manufacturer_id = '" . (int)$manufacturer_id . "'
                    ");

                    $this->db->query("
                    	INSERT INTO " . DB_PREFIX . "oct_megamenu_manufacturer
                    	SET
                    		megamenu_id = '" . (int)$megamenu_id . "',
                    		manufacturer_id = '" . (int)$manufacturer_id . "'
                    ");
                }
            }

            $this->db->query("
            	DELETE FROM " . DB_PREFIX . "oct_megamenu_product
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");

            if (isset($data['oct_megamenu_products']) && $data['item_type'] == 4) {
                $this->load->model('catalog/product');

                foreach ($data['oct_megamenu_products'] as $product_id) {
                    $this->db->query("
                    	DELETE FROM " . DB_PREFIX . "oct_megamenu_product
                    	WHERE
                    		megamenu_id = '" . (int)$megamenu_id . "'
                    		AND product_id = '" . (int)$product_id . "'
                    ");

                    $this->db->query("
                    	INSERT INTO " . DB_PREFIX . "oct_megamenu_product
                    	SET
                    		megamenu_id = '" . (int)$megamenu_id . "',
                    		product_id = '" . (int)$product_id . "'
                    ");
                }
            }

            $this->db->query("
            	DELETE FROM " . DB_PREFIX . "oct_megamenu_information
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");

            if (isset($data['oct_megamenu_informations']) && $data['item_type'] == 5) {
                foreach ($data['oct_megamenu_informations'] as $information_id) {
                    $this->db->query("
                    	DELETE FROM " . DB_PREFIX . "oct_megamenu_information
                    	WHERE
                    		megamenu_id = '" . (int)$megamenu_id . "'
                    		AND information_id = '" . (int)$information_id . "'
                    ");

                    $this->db->query("
                    	INSERT INTO " . DB_PREFIX . "oct_megamenu_information
                    	SET
                    		megamenu_id = '" . (int)$megamenu_id . "',
                    		information_id = '" . (int)$information_id . "'
					");
                }
            }

            	$this->db->query("
            	DELETE FROM " . DB_PREFIX . "oct_megamenu_blogcategory
            	WHERE
            		megamenu_id = '" . (int)$megamenu_id . "'
            ");

            	if ($data['item_type'] == 8) {
                if (isset($data['oct_megamenu_blogcategories'])) {
                    foreach ($data['oct_megamenu_blogcategories'] as $blogcategory_id) {
                        $this->db->query("
                        	DELETE FROM " . DB_PREFIX . "oct_megamenu_blogcategory
                        	WHERE
                        		megamenu_id = '" . (int)$megamenu_id . "'
                        		AND blogcategory_id = '" . (int)$blogcategory_id . "'
                        ");

                        $this->db->query("
                        	INSERT INTO " . DB_PREFIX . "oct_megamenu_blogcategory
                        	SET
                        		megamenu_id = '" . (int)$megamenu_id . "',
                        		blogcategory_id = '" . (int)$blogcategory_id . "'
                        ");
                    }
                }
            }
        }

        $this->cache->delete('octemplates');
    }

    public function deleteMegamenu($megamenu_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_description WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_to_store WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_product WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_category WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_blogcategory WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_manufacturer WHERE megamenu_id = '" . (int)$megamenu_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "oct_megamenu_to_customer_group WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        $this->cache->delete('octemplates');
    }

    public function getMegamenuProducts($data = []) {
        $sql = "SELECT p.product_id";

        if (!empty($data['filter_sub_category'])) {
            $sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
        } else {
            $sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
        }

        $sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";

        $sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW()";

        if (!empty($data['filter_category_id'])) {
	        $implode = [];

	        foreach ($data['filter_category_id'] as $filter_id) {
				$implode[] = (int)$filter_id;
			}

            if (!empty($data['filter_sub_category'])) {
                $sql .= " AND cp.path_id IN (" . implode(',', $implode) . ")";
            } else {
                $sql .= " AND p2c.category_id IN (" . implode(',', $implode) . ")";
            }
        }

        if (!empty($data['filter_manufacturer_id'])) {
	        $implode_man_id = [];

	        foreach ($data['filter_manufacturer_id'] as $man_id) {
				$implode_man_id[] = (int)$man_id;
			}

            $sql .= " AND p.manufacturer_id IN (" . implode(',', $implode_man_id) . ")";
        }

        $sql .= " GROUP BY p.product_id";

        $product_data = [];

        $query = $this->db->query($sql);

        $this->load->model('catalog/product');

        foreach ($query->rows as $result) {
            $product_data[] = $result['product_id'];
        }

        return $product_data;
    }

    public function getMegamenu($megamenu_id) {
        $query = $this->db->query("
        	SELECT DISTINCT * FROM " . DB_PREFIX . "oct_megamenu ocmm
        	LEFT JOIN " . DB_PREFIX . "oct_megamenu_description ocmmd
        		ON (ocmm.megamenu_id = ocmmd.megamenu_id)
        	WHERE
        		ocmm.megamenu_id = '" . (int)$megamenu_id . "'
        		AND ocmmd.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ");

        return $query->row;
    }

    public function getMegamenus($data = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "oct_megamenu ocmm
        	LEFT JOIN " . DB_PREFIX . "oct_megamenu_description ocmmd
        	ON (ocmm.megamenu_id = ocmmd.megamenu_id)
        	WHERE
        		ocmmd.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ";

        if (!empty($data['filter_name'])) {
            $sql .= " AND ocmmd.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
        }

        $sql .= " GROUP BY ocmm.megamenu_id";

        $sort_data = [
            'ocmmd.title',
            'ocmm.sort_order'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY ocmmd.title";
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

    public function getMegamenuDescriptions($megamenu_id) {
        $oct_megamenu_description_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_description WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $oct_megamenu_description_data[$result['language_id']] = [
                'link' => $result['link'],
                'title' => $result['title'],
                'custom_html' => $result['custom_html'],
                'banner_title' => $result['banner_title'],
                'banner_text' => $result['banner_text'],
                'banner_link' => $result['banner_link'],
                'banner_button' => $result['banner_button']
            ];
        }

        return $oct_megamenu_description_data;
    }

    public function getMegamenuStores($megamenu_id) {
        $oct_megamenu_store_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_to_store WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $oct_megamenu_store_data[] = $result['store_id'];
        }

        return $oct_megamenu_store_data;
    }

    public function getMegamenuCustomerGroups($megamenu_id) {
        $oct_megamenu_customer_group_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_to_customer_group WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $oct_megamenu_customer_group_data[] = $result['customer_group_id'];
        }

        return $oct_megamenu_customer_group_data;
    }

    public function getTotalMegamenus() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "oct_megamenu");

        return $query->row['total'];
    }

    public function getMegamenuProduct($megamenu_id) {
        $product_oct_megamenu_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_product WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $product_oct_megamenu_data[] = $result['product_id'];
        }

        return $product_oct_megamenu_data;
    }

    public function getMegamenuBlogCategory($megamenu_id) {
        $category_oct_megamenu_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_blogcategory WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $category_oct_megamenu_data[] = $result['blogcategory_id'];
        }

        return $category_oct_megamenu_data;
    }

    public function getMegamenuCategory($megamenu_id) {
        $category_oct_megamenu_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_category WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $category_oct_megamenu_data[] = $result['category_id'];
        }

        return $category_oct_megamenu_data;
    }

    public function getCategories() {
        $query = $this->db->query("
	        	SELECT * FROM " . DB_PREFIX . "category c
	        	LEFT JOIN " . DB_PREFIX . "category_description cd
	        		ON (c.category_id = cd.category_id)
	        	LEFT JOIN " . DB_PREFIX . "category_to_store c2s
	        		ON (c.category_id = c2s.category_id)
	        	WHERE
	        		cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
	        	ORDER BY
	        		c.parent_id,
	        		c.sort_order,
	        		cd.name
        ");

        $category_data = [];

        foreach ($query->rows as $row) {
            $category_data[$row['parent_id']][$row['category_id']] = $row;
        }

        return $category_data;
    }

    public function getBlogCategories() {
	    $category_data = [];

	    if ($this->checkOctTable('oct_blogcategory')) {
	        $query = $this->db->query("
		        	SELECT * FROM " . DB_PREFIX . "oct_blogcategory c
		        	LEFT JOIN " . DB_PREFIX . "oct_blogcategory_description cd
		        		ON (c.blogcategory_id = cd.blogcategory_id)
		        	LEFT JOIN " . DB_PREFIX . "oct_blogcategory_to_store c2s
		        		ON (c.blogcategory_id = c2s.blogcategory_id)
		        	WHERE
		        		cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		        	ORDER BY
		        		c.parent_id,
		        		c.sort_order,
		        		cd.name
	        ");

	        foreach ($query->rows as $row) {
	            $category_data[$row['parent_id']][$row['blogcategory_id']] = $row;
	        }
        }

        return $category_data;
    }

    private function checkOctTable($table) {
	    $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");

	    return $query->num_rows;
    }

    public function getManufacturers($data = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

        $sort_data = [
            'name',
            'sort_order'
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

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getMegamenuManufacturer($megamenu_id) {
        $manufacturer_oct_megamenu_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_manufacturer WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $manufacturer_oct_megamenu_data[] = $result['manufacturer_id'];
        }

        return $manufacturer_oct_megamenu_data;
    }

    public function getInformations($data = []) {
        $sql = "SELECT * FROM " . DB_PREFIX . "information i
        	LEFT JOIN " . DB_PREFIX . "information_description id
        		ON (i.information_id = id.information_id)
        	WHERE
        		id.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ";

        $sort_data = [
            'id.title',
            'i.sort_order'
        ];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY id.title";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getMegamenuInformation($megamenu_id) {
        $information_oct_megamenu_data = [];

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "oct_megamenu_information WHERE megamenu_id = '" . (int)$megamenu_id . "'");

        foreach ($query->rows as $result) {
            $information_oct_megamenu_data[] = $result['information_id'];
        }

        return $information_oct_megamenu_data;
    }

    public function makeDB() {
        $sql1 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu` (";
        $sql1 .= "`megamenu_id` int(11) NOT NULL AUTO_INCREMENT, ";
        $sql1 .= "`image` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`date_added` datetime NOT NULL, ";
        $sql1 .= "`item_type` int(11) NOT NULL, ";
        $sql1 .= "`sort_order` int(3) NOT NULL DEFAULT '0', ";
        $sql1 .= "`status` tinyint(1) NOT NULL DEFAULT '1', ";
        $sql1 .= "`info_text` tinyint(1) NOT NULL DEFAULT '0', ";
        $sql1 .= "`img_width` int(11) NOT NULL DEFAULT '100', ";
        $sql1 .= "`img_height` int(11) NOT NULL DEFAULT '100', ";
        $sql1 .= "`limit_item` int(11) NOT NULL DEFAULT '5', ";
        $sql1 .= "`show_img` tinyint(1) NOT NULL DEFAULT '1', ";
        $sql1 .= "`display_type` int(11) NOT NULL DEFAULT '1', ";
        $sql1 .= "`sub_categories` tinyint(1) NOT NULL DEFAULT '0', ";
        $sql1 .= "`open_link_type` tinyint(1) NOT NULL DEFAULT '0', ";
        $sql1 .= "`banner_image` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_color` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_text_color` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_title_color` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_button_color` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_link_color` varchar(255) DEFAULT NULL, ";
        $sql1 .= "`banner_image_button_hover_color` varchar(255) NOT NULL, ";
        $sql1 .= "`banner_image_link_hover_color` varchar(255) NOT NULL, ";
        $sql1 .= "PRIMARY KEY (`megamenu_id`) ";
        $sql1 .= ") ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;";

        $this->db->query($sql1);

        $sql2 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_description` (";
        $sql2 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql2 .= "`language_id` int(11) NOT NULL, ";
        $sql2 .= "`link` text NOT NULL, ";
        $sql2 .= "`title` varchar(255) NOT NULL, ";
        $sql2 .= "`custom_html` text NOT NULL, ";
        $sql2 .= "`banner_title` varchar(255) NOT NULL, ";
        $sql2 .= "`banner_text` varchar(255) NOT NULL, ";
        $sql2 .= "`banner_link` varchar(255) NOT NULL, ";
        $sql2 .= "`banner_button` varchar(64) NOT NULL, ";
        $sql2 .= "PRIMARY KEY (`megamenu_id`,`language_id`) ";
        $sql2 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql2);

        $sql3 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_to_customer_group` (";
        $sql3 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql3 .= "`customer_group_id` int(11) NOT NULL, ";
        $sql3 .= "PRIMARY KEY (`megamenu_id`,`customer_group_id`) ";
        $sql3 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql3);

        $sql4 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_to_store` (";
        $sql4 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql4 .= "`store_id` int(11) NOT NULL, ";
        $sql4 .= "PRIMARY KEY (`megamenu_id`,`store_id`) ";
        $sql4 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql4);

        $sql5 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_product` (";
        $sql5 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql5 .= "`product_id` int(11) NOT NULL, ";
        $sql5 .= "PRIMARY KEY (`megamenu_id`,`product_id`) ";
        $sql5 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql5);

        $sql6 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_category` (";
        $sql6 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql6 .= "`category_id` int(11) NOT NULL, ";
        $sql6 .= "PRIMARY KEY (`megamenu_id`,`category_id`) ";
        $sql6 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql6);

        $sql7 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_manufacturer` (";
        $sql7 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql7 .= "`manufacturer_id` int(11) NOT NULL, ";
        $sql7 .= "PRIMARY KEY (`megamenu_id`,`manufacturer_id`) ";
        $sql7 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql7);

        $sql8 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_information` (";
        $sql8 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql8 .= "`information_id` int(11) NOT NULL, ";
        $sql8 .= "PRIMARY KEY (`megamenu_id`,`information_id`) ";
        $sql8 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql8);

        $sql9 = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oct_megamenu_blogcategory` (";
        $sql9 .= "`megamenu_id` int(11) NOT NULL, ";
        $sql9 .= "`blogcategory_id` int(11) NOT NULL, ";
        $sql9 .= "PRIMARY KEY (`megamenu_id`,`blogcategory_id`) ";
        $sql9 .= ") ENGINE=MyISAM DEFAULT CHARSET=utf8;";

        $this->db->query($sql9);

        $field_page_group_links = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` WHERE Field='page_group_links'")->num_rows;

		if (!$field_page_group_links) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `page_group_links` text NOT NULL AFTER `status`;");
		}

		$field_oct_image = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "category` WHERE Field='oct_image'")->num_rows;

		if (!$field_oct_image) {
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "category` ADD `oct_image` varchar(255) COLLATE 'utf8_general_ci' NULL AFTER `image`;");
		}
    }

    public function removeDB() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_description`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_to_store`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_product`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_category`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_blogcategory`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_manufacturer`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_information`;");
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "oct_megamenu_to_customer_group`;");
    }
}
