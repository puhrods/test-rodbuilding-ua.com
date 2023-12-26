<?php
//  Product Option Image PRO / Изображения опций PRO
//  Support: support@liveopencart.com / Поддержка: help@liveopencart.ru

// model should be loaded only after the poip library (basically it should be loaded by the poip library object itself)
class ModelExtensionModuleProductOptionImagePro extends Model {
  
	protected $extra_table_columns_checked = false;
	
	protected $simple_db;
	
	public function __construct() {
		call_user_func_array( array('parent', '__construct') , func_get_args());
		if ( $this->liveopencart_ext_poip->installed() ) {
			$this->checkExtraTableColumns();
		}
		$this->load->model('tool/image');
	}
	
	protected function getSimpleDB() {
		
		if ( !$this->simple_db ) {
			$this->simple_db = new \liveopencart\lib\v0015\simple_db($this->registry);
		}
		return $this->simple_db;
		
	}
  
	public function deleteAllImages() {
		
		$poip_global_settings = $this->getGlobalSettings();
		if ( !empty($poip_global_settings['options_images_edit']) ) { // remove product images linked with options too
			$this->db->query("
				DELETE FROM ".DB_PREFIX."product_image 
				WHERE product_image_id IN (
					SELECT PI.product_image_id
					FROM ".DB_PREFIX."product_image PI
						,".DB_PREFIX."poip_option_image POIP
					WHERE PI.product_id = POIP.product_id
					  AND PI.image = POIP.image
				)
			");
		}
			
		$this->db->query("DELETE FROM ".DB_PREFIX."poip_option_image ");
	}
  
	public function deleteOptionImagesOfInexistentProductOptionValues() {
		$this->db->query("
			DELETE OI FROM ".DB_PREFIX."poip_option_image OI
			WHERE ( SELECT POV.product_id 
							FROM ".DB_PREFIX."product_option_value POV
							WHERE POV.product_id = OI.product_id
								AND POV.product_option_value_id = OI.product_option_value_id 
						) IS NULL
		");

	}
	
	
  
	public function getOptions() {
		$this->load->model('catalog/option');
		$poip_options = array();
		foreach ( $this->model_catalog_option->getOptions() as $option ) {
			$values = $this->model_catalog_option->getOptionValues($option['option_id']);
			if ( $values ) {
				$option['option_values'] = $values;
				$poip_options[] = $option;
			}
		}
		return $poip_options;
	}
	
	public function setFilterOptionValue($filter_id, $data) {
		
		$poip_ocfilter_option_value_id = isset($data['poip_ocfilter_option_value_id']) ? $data['poip_ocfilter_option_value_id'] : 0;
		$this->db->query("UPDATE ".DB_PREFIX."filter SET poip_ocfilter_option_value_id = ".(int)$poip_ocfilter_option_value_id." WHERE filter_id = ".(int)$filter_id." ");
	}
  
	public function getOptionsForImages($product_id) {
	  
		if ( !$this->liveopencart_ext_poip->installed() ) return;
		
		$query = $this->db->query("
			SELECT POIP.*, PO.option_id, POV.option_value_id, POIP.product_option_value_id
			FROM  ".DB_PREFIX."poip_option_image POIP
			LEFT JOIN ".DB_PREFIX."product_option_value POV ON (POIP.product_option_value_id = POV.product_option_value_id)
				, ".DB_PREFIX."product_option PO
				
			WHERE POIP.product_id = ".(int)$product_id."
				AND POIP.product_option_id = PO.product_option_id
				AND POIP.relatedoptions_id = 0
			ORDER BY sort_order ASC
		");
		
		$images = array();
		foreach ($query->rows as $row) {
		  
			// product_option_value_id = 0 - display for not selected option
			// product_option_value_id = -1 - display for all values
			if ( !$row['option_value_id'] && ($row['product_option_value_id']!= 0 && $row['product_option_value_id'] != -1) ) continue; 
			
			
			$image = $row['image'];
			$option_id = $row['option_id'];
			$option_value_id = $row['option_value_id'] ? $row['option_value_id'] : ( ($row['product_option_value_id'] == 0 || $row['product_option_value_id'] == -1) ? $row['product_option_value_id'] : 0 );
				
			if ( !isset($images[$image]) ) {
				$images[$image] = array();
			}
			if ( !isset($images[$image][$option_id]) ) {
				$images[$row['image']][$option_id] = array();
			}
			
			if ( !in_array($option_value_id, $images[$image][$option_id]) ) {
				$images[$image][$option_id][] = $option_value_id;
			}
		  
		}
			
		return $images;
	}
  
	public function saveOptionsForImages($product_id, $product_images) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return;
			
		$poip_global_settings = $this->getGlobalSettings();
		if ($poip_global_settings['options_images_edit'] != 1) return;
		
		$this->deleteProductOptionValueImages($product_id);
		
		$query = $this->db->query(" SELECT PO.product_option_id, PO.option_id, POV.product_option_value_id, POV.option_value_id
									FROM  ".DB_PREFIX."product_option PO
										, ".DB_PREFIX."product_option_value POV
									WHERE PO.product_id = ".(int)$product_id."
									  AND POV.product_option_id = PO.product_option_id
									");
		$product_options = array();
		foreach ($query->rows as $row) {
			if ( !isset($product_options[$row['option_id']]) ) {
				$product_options[$row['option_id']] = array();
				$product_options[$row['option_id']]['values'] = array();
			}
			
			$product_options[$row['option_id']]['product_option_id'] = $row['product_option_id'];
			$product_options[$row['option_id']]['values'][$row['option_value_id']] = $row['product_option_value_id'];
		  
		}
		
		foreach ($product_images as $product_image) {
		  
			if ( isset($product_image['poip']) && $product_image['poip'] ) {
			  
				foreach ($product_image['poip'] as $option_id => $option_values) {
				  
					foreach ($option_values as $option_value_id) {
					  
						if ( !empty( $product_options[$option_id]['values'][$option_value_id] ) || $option_value_id == 0 || $option_value_id == -1 ) {
						
							$product_option_id = $product_options[$option_id]['product_option_id'];
							if ( $option_value_id == 0 ) {
								$product_option_value_id = 0;
							} elseif ( $option_value_id == -1 ) {
								$product_option_value_id = -1;
							} else {
								$product_option_value_id = $product_options[$option_id]['values'][$option_value_id];
							}
									  
							$this->saveProductOptionValueImages($product_id, $product_option_id, $product_option_value_id, array( array('image'=>$product_image['image'], 'srt'=>(int)$product_image['sort_order']) ));
						}
					}
				}
			}
		}
	}
  
	// new
	public function getGlobalSettings() {
		$poip_settings = $this->config->get('poip_module');
		$settings_names = $this->liveopencart_ext_poip->getModuleSettingsIds(false);
		foreach ($settings_names as $setting_name) {
			if ( !isset($poip_settings[$setting_name]) ) {
				$poip_settings[$setting_name] = 0;
			}
		}
		return $poip_settings;
	}
  

  
  // for export
  public function getAllImages($include_options_without_images=false, $include_names=false, $first_product_id=0, $last_product_id=0) {
		
		$export_ro_ids = $this->liveopencart_ext_poip->versionUltimate() && $this->liveopencart_ext_poip->getSetting('images_for_ro');
		
		$sql_select_columns = " POIP.product_id ";
		if ( $export_ro_ids ) {
			$sql_select_columns.= ", IF (POIP.relatedoptions_id != 0, POIP.relatedoptions_id, '') relatedoptions_id ";
			$sql_select_columns.= ", IF (POIP.relatedoptions_id != 0, '', POV.option_value_id) option_value_id ";
			$sql_select_columns.= ", POIP.image, POIP.sort_order";
			if ($include_names) {
				$sql_select_columns.= ", PD.name product_name ";
				$sql_select_columns.= ", IF (POIP.relatedoptions_id != 0, '', OD.name) option_name ";
				$sql_select_columns.= ", IF (POIP.relatedoptions_id != 0, '', OVD.name) option_value_name ";
			}
		} else {
			$sql_select_columns.= ", POV.option_value_id";
			$sql_select_columns.= ", POIP.image, POIP.sort_order";
			if ($include_names) {
				$sql_select_columns.= ", PD.name product_name ";
				$sql_select_columns.= ", OD.name option_name ";
				$sql_select_columns.= ", OVD.name option_value_name ";
			}
		}
		
		$sql_where = "";
		if ( $first_product_id ) {
			$sql_where.= " POIP.product_id >= ".(int)$first_product_id." ";
		}
		if ( $last_product_id ) {
			$sql_where.= " AND POIP.product_id <= ".(int)$last_product_id." ";
		}
		
		$sql_order_by = "POIP.product_id ASC";
		if ( $export_ro_ids ) {
			$sql_order_by.= ", (POIP.relatedoptions_id != '') DESC, POIP.relatedoptions_id ASC";
		}
		$sql_order_by.= ", POV.option_value_id ASC, POIP.sort_order ASC";
		
		
		$query = $this->db->query("
			SELECT DISTINCT ".$sql_select_columns."
			FROM ".DB_PREFIX."poip_option_image POIP
				LEFT JOIN ".DB_PREFIX."product_option_value POV ON (POIP.product_option_value_id = POV.product_option_value_id)
				LEFT JOIN ".DB_PREFIX."product_description PD ON (PD.product_id = POIP.product_id AND PD.language_id = ".(int)$this->config->get('config_language_id').")
				LEFT JOIN ".DB_PREFIX."option_description OD ON (OD.option_id = POV.option_id AND OD.language_id = ".(int)$this->config->get('config_language_id').")
				LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = POV.option_value_id AND OVD.language_id = ".(int)$this->config->get('config_language_id').")
			 ".($sql_where ? " WHERE ".$sql_where : "")."
			ORDER BY ".$sql_order_by."
		");
		
		$results = $query->rows;
		
		if ( $results && $export_ro_ids ) {
			$query = $this->db->query("
				SELECT ROO.relatedoptions_id, OD.name option_name, OVD.name option_value_name
				FROM `".DB_PREFIX."relatedoptions_option` ROO
						,`".DB_PREFIX."option` O
						,`".DB_PREFIX."option_description` OD
						,`".DB_PREFIX."option_value` OV
						,`".DB_PREFIX."option_value_description` OVD
				WHERE ROO.option_id = O.option_id
					AND ROO.option_id = OD.option_id
					AND ROO.option_value_id = OV.option_value_id
					AND ROO.option_value_id = OVD.option_value_id
					AND OD.language_id = ".(int)$this->config->get('config_language_id')."
					AND OVD.language_id = ".(int)$this->config->get('config_language_id')."
					AND ROO.relatedoptions_id IN (
						SELECT relatedoptions_id
						FROM ".DB_PREFIX."poip_option_image POIP
						".($sql_where ? " WHERE ".$sql_where : "")."
					)
				ORDER BY relatedoptions_id ASC, O.sort_order ASC, OD.name ASC, OV.sort_order ASC, OVD.name ASC
			");
			$ro_option_names = array();
			foreach ( $query->rows as $row ) {
				if ( !isset($ro_option_names) ) {
					$ro_option_names[$row['relatedoptions_id']] = array();
				}
				$ro_option_names[$row['relatedoptions_id']][] = $row;
			}
			foreach ( $results as &$result ) {
				if ( $result['relatedoptions_id'] && !empty($ro_option_names[$result['relatedoptions_id']]) ) {
					foreach ( $ro_option_names[$result['relatedoptions_id']] as $ro_option_name ) {
						$result['option_name'].= ($result['option_name'] ? ' / ' : '').$ro_option_name['option_name'];
						$result['option_value_name'].= ($result['option_value_name'] ? ' / ' : '').$ro_option_name['option_value_name'];
					}
				}
			}
			unset($result);
		}
			
		
		if ( $include_options_without_images ) {
			$sql_select_columns_woi = " POV.product_id ";
			if ( $export_ro_ids ) {
				$sql_select_columns_woi.= ", '' relatedoptions_id ";
			}
			$sql_select_columns_woi.= ", POV.option_value_id, '' image, '' sort_order ";
			if ( $include_names ) {
				$sql_select_columns_woi.= ", PD.name product_name, OD.name option_name, OVD.name option_value_name";
			}
			$query = $this->db->query("
				SELECT ".$sql_select_columns_woi."
				FROM ".DB_PREFIX."product_option_value POV
							LEFT JOIN ".DB_PREFIX."product_description PD ON (PD.product_id = POV.product_id AND PD.language_id = ".(int)$this->config->get('config_language_id').")
							LEFT JOIN ".DB_PREFIX."option_description OD ON (OD.option_id = POV.option_id AND OD.language_id = ".(int)$this->config->get('config_language_id').")
							LEFT JOIN ".DB_PREFIX."option_value_description OVD ON (OVD.option_value_id = POV.option_value_id AND OVD.language_id = ".(int)$this->config->get('config_language_id').")
				WHERE NOT POV.product_option_value_id IN (SELECT product_option_value_id FROM ".DB_PREFIX."poip_option_image)
					".
						($first_product_id ? " AND POV.product_id >= ".(int)$first_product_id." " : "" )
					."
					".
						($last_product_id ? " AND POV.product_id <= ".(int)$last_product_id." " : "" )
					."
				ORDER BY POV.product_id ASC, POV.option_value_id ASC
			");
			
			$results = array_merge($results, $query->rows);
		}
		return $results;

	}
  
	public function getProductOrderImage($product_id, $option_data, $image) {
	  
		if (!$this->liveopencart_ext_poip->installed()) {
			return $image;
		}
		
		$selected_product_option = array();
		$selected_product_option_value = array();
		foreach ($option_data as $option_value_data) {
			if (!in_array($option_value_data['product_option_id'], $selected_product_option)) $selected_product_option[] = $option_value_data['product_option_id'];
			if (!in_array($option_value_data['product_option_value_id'], $selected_product_option_value)) $selected_product_option_value[] = $option_value_data['product_option_value_id'];
		}
		
		
		$product_images = $this->getProductOptionImages($product_id);
		if ( count($product_images) > 0 ) {
		  
			$product_settings = $this->liveopencart_ext_poip->getProductSettings($product_id);
			
			$cart_options = array();
			$filter_options = array();
			foreach ($product_images as $product_option_id => $product_option_images ) {
			
				if ( in_array($product_option_id, $selected_product_option) ) { // значение опции выбрано
				  
					$images_count = 0;
					
					foreach ($product_option_images as $product_option_value => $product_option_value_images) {
						$images_count = $images_count + count($product_option_value_images);
					}
					
					if ($images_count > 0) {
						if (isset($product_settings[$product_option_id]['img_cart']) && $product_settings[$product_option_id]['img_cart']) {
							$cart_options[] = $product_option_id;
							if ($product_settings[$product_option_id]['img_limit']) $filter_options[] = $product_option_id;
						}
					}
				}
			}
			  
			if (count($filter_options)>0) {
				
				$images = false;
				foreach ($product_images as $product_option_id => $product_option_images) {
					if (in_array($product_option_id, $filter_options)) {
						$current_images = array();
						foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
							if ( in_array($product_option_value_id, $selected_product_option_value) ) { // это выбранное значение опции
								foreach ($product_option_value_images as $image_info) {
									if (!in_array($image_info['image'], $current_images)) {
										$current_images[] = $image_info['image'];
									}
								}
							} 
						}
					  
						if (count($current_images) > 0) {
							if ($images === false) {
								$images = $current_images;
							} else {
								$images = array_values(array_intersect($images, $current_images));
							}
						}
					  
					}
				}
				
				if ($images && count($images)>0) {
					$image = $images[0];
				}
				
			  } elseif (count($cart_options)>0) { // first image of first option
				$product_option_id = $cart_options[0];
				foreach ($product_images[$product_option_id] as $product_option_value_id => $product_option_value_images) {
					if ( in_array($product_option_value_id, $selected_product_option_value) ) { // selected value
						foreach ($product_option_value_images as $image_info) {
							$image = $image_info['image'];
						}
					}
				}
			}
		  
		}
		
		return $image;
	  
	}
  
	// result:
	// 1 = image added
	// 0 = product option not found
	// -1 = image already exists
	public function addProductOptionValueImage($product_id, $option_value_id, $image, $sort_order=null, $relatedoptions_id=0) {
	  
		  
		if ( $relatedoptions_id && $this->liveopencart_ext_poip->versionUltimate() ) {
			return $this->liveopencart_ext_poiu->saveImageForROComb($product_id, $relatedoptions_id, $image, $sort_order);
		}
		
		$query = $this->db->query("
			SELECT product_option_id, product_option_value_id
			FROM ".DB_PREFIX."product_option_value
			WHERE product_id = ".(int)$product_id."
				AND option_value_id = ".(int)$option_value_id."
		");
		$pov = $query->row;
			
		if ( !$pov ) {
			$result = 0;
		} else {
				
			$product_option_id = !empty($pov['product_option_id']) ? $pov['product_option_id'] : 0;
			$product_option_value_id = !empty($pov['product_option_value_id']) ? $pov['product_option_value_id'] : 0;
	
			if ( is_null($sort_order) ) {
				$sort_order = 1+(int)$this->getProductOptionImageSortOrderMax($product_id);
			}
	
			
			if ( $this->existProductOptionImage($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id) ) {
				$result = -1;
			} else {
				$this->insertProductOptionImage($product_id, $query->row['product_option_id'], $query->row['product_option_value_id'], $image, $sort_order, $relatedoptions_id);
				$result = 1;
			}
			// in some cases duplicates can exist after previously failed importing attempts
			$this->removeProductOptionImageDuplicates($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id);
		  
		}
		
		return $result;
	  
	}
	
	protected function getProductOptionImageSortOrderMax($product_id) {
		$query = $this->db->query("
			SELECT sort_order
			FROM ".DB_PREFIX."poip_option_image
			WHERE product_id = ".(int)$product_id."
				AND relatedoptions_id = 0
			ORDER BY sort_order DESC
			LIMIT 1
		");
		if ($query->num_rows) {
			return $query->row['sort_order'];
		} else {
			$query = $this->db->query("
				SELECT sort_order
				FROM ".DB_PREFIX."product_image
				WHERE product_id = ".(int)$product_id."
				ORDER BY sort_order DESC
				LIMIT 1
			");
			if ($query->num_rows) {
				return $query->row['sort_order'];
			}
		}
	}
	
	public function existProductOptionImage($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id=0) {
		
		return !empty($this->getProductOptionImageExact($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id));
		
		//$query = $this->db->query("
		//	SELECT *
		//	FROM ".DB_PREFIX."poip_option_image
		//	WHERE product_id = ".(int)$product_id."
		//		AND product_option_id = ".(int)$product_option_id."
		//		AND product_option_value_id = ".(int)$product_option_value_id."
		//		AND image = '".$this->db->escape((string)$image)."'
		//		AND sort_order = ".(int)$sort_order."
		//		AND relatedoptions_id = ".(int)$relatedoptions_id."
		//");
		//return $query->num_rows;
		
	}
	
	protected function getProductOptionImageExact($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id=0) {
		$query = $this->db->query("
			SELECT *
			FROM ".DB_PREFIX."poip_option_image
			WHERE product_id = ".(int)$product_id."
				AND product_option_id = ".(int)$product_option_id."
				AND product_option_value_id = ".(int)$product_option_value_id."
				AND image = '".$this->db->escape((string)$image)."'
				AND sort_order = ".(int)$sort_order."
				AND relatedoptions_id = ".(int)$relatedoptions_id."
		");
		return $query->row;
	}
	
	protected function getProductOptionImagesExactIgnoringSortOrder($product_id, $product_option_id, $product_option_value_id, $image, $relatedoptions_id=0) {
		$query = $this->db->query("
			SELECT *
			FROM ".DB_PREFIX."poip_option_image
			WHERE product_id = ".(int)$product_id."
				AND product_option_id = ".(int)$product_option_id."
				AND product_option_value_id = ".(int)$product_option_value_id."
				AND image = '".$this->db->escape((string)$image)."'
				AND relatedoptions_id = ".(int)$relatedoptions_id."
		");
		return $query->rows;
	}
	
	protected function removeProductOptionImageDuplicates($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id=0) {
		
		$poip_image = $this->getProductOptionImageExact($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id);
		
		if ( $poip_image ) {
			
			$poip_images = $this->getProductOptionImagesExactIgnoringSortOrder($product_id, $product_option_id, $product_option_value_id, $image, $relatedoptions_id);
			
			if ( count($poip_images) > 1 ) {
				// removing the same images ignoring difference in sort order
				$query = $this->db->query("
					DELETE FROM ".DB_PREFIX."poip_option_image
					WHERE product_id = ".(int)$product_id."
						AND product_option_id = ".(int)$product_option_id."
						AND product_option_value_id = ".(int)$product_option_value_id."
						AND image = '".$this->db->escape((string)$image)."'
						AND relatedoptions_id = ".(int)$relatedoptions_id."
				");
				
				$this->insertProductOptionImage($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id);
			}
		}
		
	}
	
	public function resortProductsImagesByProductOptionImages($product_ids) {
		
		foreach ( $product_ids as $product_id ) {
			
			$poip_images = $this->getSimpleDB()->selectRowsByIds('poip_option_image', array('product_id'=>$product_id));
			if ( $poip_images ) {
				$poip_images_assoc = array();
				$min_sort_order_poip = false;
				foreach ( $poip_images as $poip_image ) {
					$min_sort_order_poip = $min_sort_order_poip === false ? $poip_image['sort_order'] : min($min_sort_order_poip, $poip_image['sort_order']);
					if ( !isset($poip_images_assoc[$poip_image['image']]) ) {
						$poip_images_assoc[$poip_image['image']] = $poip_image;
					}
				}
				$sort_order_shift = 1 - (int)$min_sort_order_poip;
				
				$product_images = $this->getSimpleDB()->selectRowsByIds('product_image', array('product_id'=>$product_id));
				$max_sort_order_product = false;
				foreach ( $product_images as $product_image ) {
					if ( !isset($poip_images_assoc[$product_image['image']]) ) {
						$max_sort_order_product = $max_sort_order_product===false ? $product_image['sort_order'] : max($max_sort_order_product, $product_image['sort_order']);
					}
				}
				$max_sort_order_product = (int)$max_sort_order_product;
				
				foreach ( $product_images as $product_image ) {
					if ( isset($poip_images_assoc[$product_image['image']]) ) {
						$poip_image = $poip_images_assoc[$product_image['image']];
						$current_sort_order = $max_sort_order_product + $sort_order_shift + $poip_image['sort_order'];
						$this->db->query("UPDATE `".DB_PREFIX."product_image` SET `sort_order` = ".(int)$current_sort_order." WHERE product_image_id = ".(int)$product_image['product_image_id']." ");
					}
				}
				
			}
			
		}
		
	}
  
	// thumbs to array of images
	public function addThumbsIfNeeded($images) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return $images;
		
			if (!$this->model_tool_image) {
				$this->load->model('tool/image');
			}
			
		foreach ($images as &$image) {
			if (!isset($image['thumb'])) {
				$image['thumb'] = $this->model_tool_image->resize($image['image'], 100, 100);
			}
		}
		unset($image);
		
		return $images;
	}
  
	public function getProductOptionImages($product_id) {
	  
		$images = array();
		
		if (!$this->liveopencart_ext_poip->installed()) return $images;
		
		$query = $this->db->query("
			SELECT *
			FROM ".DB_PREFIX."poip_option_image
			WHERE product_id = ".(int)$product_id."
				AND relatedoptions_id = 0
			ORDER BY sort_order
		");
		foreach ($query->rows as $row) {
			if (!isset($images[$row['product_option_id']])) {
				$images[$row['product_option_id']] = array();
			}
			if (!isset($images[$row['product_option_id']][$row['product_option_value_id']])) {
				$images[$row['product_option_id']][$row['product_option_value_id']] = array();
			}
			$images[$row['product_option_id']][$row['product_option_value_id']][] = $this->getImagePropertiesForProductForm($row);
		}
		
		return $images;
	  
	}
  
	public function getImagePropertiesForProductForm($row) {
		return array(
			'image'	=>	$row['image'],
			'srt'		=>	$row['sort_order'],
			'thumb'	=>	$this->model_tool_image->resize($row['image'], 100, 100),
		);
	}
  
	public function saveProductOptionValueImages($product_id, $product_option_id, $product_option_value_id, $images) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return;
		
		if (is_array($images)) {
			foreach ($images as $image) {
				if (is_array($image) && isset($image['image']) && $image['image']) {
					$this->insertProductOptionImage($product_id, $product_option_id, $product_option_value_id, $image['image'], (isset($image['srt']) ? (int)$image['srt'] : 0));
				}
			}
		}
	  
	}
	
	public function insertProductOptionImage($product_id, $product_option_id, $product_option_value_id, $image, $sort_order, $relatedoptions_id=0) {
		$this->db->query("
			INSERT INTO ".DB_PREFIX."poip_option_image
			SET product_id = ".(int)$product_id."
				, product_option_id = ".(int)$product_option_id."
				, product_option_value_id = ".(int)$product_option_value_id."
				, image = '".$this->db->escape((string)$image)."'
				, sort_order = ".(int)$sort_order."
				, relatedoptions_id = ".(int)$relatedoptions_id."
		");
	}
  
	public function deleteProductOptionValueImages($product_id) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return;
	  
		$this->db->query("DELETE FROM ".DB_PREFIX."poip_option_image WHERE product_id = ".(int)$product_id." AND relatedoptions_id = 0 ");
	  
	}
	
	
  
	// Real
	public function getRealOptionSettings($option_id) {
	  
		$settings = array();
		
		if (!$this->liveopencart_ext_poip->installed()) return $settings;
		
		if (!$option_id) return $settings;
		
		$settings_names = $this->liveopencart_ext_poip->getModuleSettingsIds();
		
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_main_option_settings WHERE option_id = ".(int)$option_id." ");
		if ($query->num_rows) {
			$row = $query->row;
			foreach ($settings_names as $setting_name) {
				$settings[$setting_name] = isset($row[$setting_name]) ? $row[$setting_name] : 0;
			}
		}
		
		return $settings;
	}
  
	public function getRealProductSettings($product_id) {
	  
		$settings = array();
		
		if (!$this->liveopencart_ext_poip->installed()) return $settings;
		
		if (!$product_id) return $settings;
		
		$settings_names = $this->liveopencart_ext_poip->getModuleSettingsIds();
		
		$query = $this->db->query("SELECT * FROM ".DB_PREFIX."poip_option_settings WHERE product_id = ".(int)$product_id." ");
		foreach ($query->rows as $row) {
			$settings[$row['product_option_id']] = array();
			foreach ($settings_names as $setting_name) {
				$settings[$row['product_option_id']][$setting_name] = isset($row[$setting_name]) ? $row[$setting_name] : 0;
			}
		  
		}
		
		return $settings;
	}
  
	public function deleteRealProductSettings($product_id) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return;
		$this->db->query("DELETE FROM ".DB_PREFIX."poip_option_settings WHERE product_id = ".(int)$product_id." ");
	  
	}
  
	public function deleteRealOptionSettings($option_id) {
	  
		if (!$this->liveopencart_ext_poip->installed()) return;
		$this->db->query("DELETE FROM ".DB_PREFIX."poip_main_option_settings WHERE option_id = ".(int)$option_id." ");
	  
	}
  
	public function setRealOptionSettings($option_id, $settings) {
    
		if (!$this->liveopencart_ext_poip->installed()) return;
	
		$this->deleteRealOptionSettings($option_id);
		
		$sql = "";
		$settings_names = $this->liveopencart_ext_poip->getModuleSettingsIds(true);
		foreach ($settings_names as $setting_name) {
			$sql .= ", ".$setting_name." = ".(isset($settings[$setting_name]) ? (int)$settings[$setting_name] : 0)." ";
		}
		
		$this->db->query("
			INSERT INTO ".DB_PREFIX."poip_main_option_settings
			SET option_id = ".(int)$option_id."
				".$sql."
		");
    
	}
  
	public function setRealProductSettings($product_id, $product_option_id, $settings) {
    
		if (!$this->liveopencart_ext_poip->installed()) return;
		
		$this->db->query("DELETE FROM ".DB_PREFIX."poip_option_settings WHERE product_option_id = ".(int)$product_option_id." ");
		
		$sql = "";
		$settings_names = $this->liveopencart_ext_poip->getModuleSettingsIds();
		foreach ($settings_names as $setting_name) {
		  $sql .= ", ".$setting_name." = ".(isset($settings[$setting_name]) ? (int)$settings[$setting_name] : 0)." ";
		}
		
		$this->db->query("
			INSERT INTO ".DB_PREFIX."poip_option_settings
			SET product_id = ".(int)$product_id."
			  , product_option_id = ".(int)$product_option_id."
			  ".$sql."
		");
    
	}
	
	
	public function getModuleSettingsDetails($for_option_or_product_page=false) {
	
		$fields = array();
		
		$settings_ids = $this->liveopencart_ext_poip->getModuleSettingsIds($for_option_or_product_page);
		
		$lang = $this->liveopencart_ext_poip->getLanguageOwn()->all();
		
		$field_childs = array('img_filter_by_comb'=>'img_limit'); // child_id => parent_id - only for the module setting page
		$children = array();
		
		foreach ($settings_ids as $setting_id) {
			$field = array('name'=>$setting_id);
			$field['title'] = $lang['entry_'.$setting_id];
			$field['help'] = $lang['entry_'.$setting_id.'_help'];
			if ( isset($lang['entry_'.$setting_id.'_details']) ) {
				$field['details'] = $lang['entry_'.$setting_id.'_details'];
			}
			$i_field_value = 0;
			while ( isset($lang['entry_'.$setting_id.'_v'.$i_field_value]) ) {
				$field['values'][$i_field_value] = $lang['entry_'.$setting_id.'_v'.$i_field_value];
				$i_field_value++;
			}
			if ( !$for_option_or_product_page && isset($field_childs[$setting_id]) ) {
				$field['parent'] = $field_childs[$setting_id];
				$children[$field_childs[$setting_id]][] = $field; 
			} else {
				$fields[] = $field;
			}
		}
		
		if ( !$for_option_or_product_page ) {
			foreach ($fields as &$field) {
				if ( isset($children[$field['name']]) ) {
					$field['children'] = $children[$field['name']];
				}
			}
			unset($field);
		}
		return $fields;
	}
	
	
	
	public function getProductOptions($product_id) {
		
		$this->load->model('catalog/product');
		
		if ( method_exists('ModelCatalogProduct', 'getProductOptions') ) {
			return $this->model_catalog_product->getProductOptions($product_id);
			
		} else {
			$this->load->model('catalog/product_option');
			return $this->model_catalog_product_option->getProductOptionsByProductId($product_id);
		}
		
	}
	
	public function getOptionValuesByArrayOfProductOptions($product_options) {
		$this->load->model('catalog/option');
		
		$option_values = array();

		foreach ($product_options as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($option_values[$product_option['option_id']])) {
					$option_values[$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}
		return $option_values;
	}
	
	
	
	public function addOptionsDataToAdditionalImages($product_id, $rows) {
		
		if ( $this->liveopencart_ext_poip->installed() ) {
			$poip_global_settings = $this->getGlobalSettings();
			if ( $poip_global_settings['options_images_edit'] == 1 ) {
				
				$poip_options_for_images = $this->getOptionsForImages($product_id);
				
				$sort_order_max = 0;
				
				$included_images = array();
				foreach ($rows as &$row) {
					if ( isset($poip_options_for_images[$row['image']]) ) {
						$row['poip'] = $poip_options_for_images[$row['image']];
						$included_images[] = $row['image'];
					} else {
						$row['poip'] = false;
					}
					$sort_order_max = max($sort_order_max, $row['sort_order']);
				}
				unset($row);
				
				// if option images is not set for additional image - add this image in additional images array
				foreach ($poip_options_for_images as $poip_image => $image_options) {
					if ( !in_array($poip_image, $included_images) ) {
						$rows[] = array('product_image_id'=>0, 'product_id'=>$product_id, 'image'=>$poip_image, 'sort_order'=>++$sort_order_max, 'poip'=>$image_options);
					}
				}
				
			}
		}
		
		return $rows;
	}
	
	
	
	public function reinsertProductOptionValue($product_option_value_id) { // reinsert_product_option_value
        
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_value_id = ".(int)$product_option_value_id." ");
		if ( $query->num_rows ) {
			
			$sql_set = "";
			foreach ($query->row as $key => $value) {
				$sql_set .= ", `".$key."` = '".$this->db->escape($value)."' ";
			}
			$sql_set = substr($sql_set, 1);
			$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_option_value_id = ".$product_option_value_id." ");
			$this->db->query("INSERT INTO ".DB_PREFIX."product_option_value SET ".$sql_set);
		}
	
	}
	
	public function getMaxProductId() {
		$query = $this->db->query("SELECT MAX(product_id) product_id FROM " . DB_PREFIX . "product ");
		if ( $query->num_rows ) {
			return $query->row['product_id'];
		} else {
			return '';
		}
	}
	
	public function getMinProductId() {
		$query = $this->db->query("SELECT MIN(product_id) product_id FROM " . DB_PREFIX . "product ");
		if ( $query->num_rows ) {
			return $query->row['product_id'];
		} else {
			return '';
		}
	}
  
	public function install() {
    
		$this->uninstall();
    
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_option_image` (
					`product_id` int(11) NOT NULL,
					`product_option_id` int(11) NOT NULL,
					`product_option_value_id` int(11) NOT NULL,
					`image` varchar(255) NOT NULL,
					`sort_order` int(11) NOT NULL,
					`relatedoptions_id` int(11) NOT NULL,
					KEY `relatedoptions_id` (`relatedoptions_id`),
					FOREIGN KEY (product_id) REFERENCES `".DB_PREFIX."product`(product_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_id) REFERENCES `".DB_PREFIX."product_option`(product_option_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_value_id) REFERENCES `".DB_PREFIX."product_option_value`(product_option_value_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
    
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_option_settings` (
					`product_id` int(11) NOT NULL,
					`product_option_id` int(11) NOT NULL,
					`img_change` tinyint(1) NOT NULL,
					`img_hover` tinyint(1) NOT NULL,
					`img_use` tinyint(1) NOT NULL,
					`img_limit` tinyint(1) NOT NULL,
					`img_gal` tinyint(1) NOT NULL,
					`img_option` tinyint(1) NOT NULL,
					`img_category` tinyint(1) NOT NULL,
					`img_first` tinyint(1) NOT NULL,
					`img_from_option` tinyint(1) NOT NULL,
					`img_sort` tinyint(1) NOT NULL,
					`img_select` tinyint(1) NOT NULL,
					`img_cart` tinyint(1) NOT NULL,
					`img_radio_checkbox` tinyint(1) NOT NULL,
					`dependent_thumbnails` tinyint(1) NOT NULL,
					FOREIGN KEY (product_id) REFERENCES `".DB_PREFIX."product`(product_id) ON DELETE CASCADE,
					FOREIGN KEY (product_option_id) REFERENCES `".DB_PREFIX."product_option`(product_option_id) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
    
		$this->db->query("
			CREATE TABLE IF NOT EXISTS
				`".DB_PREFIX."poip_main_option_settings` (
					`option_id` int(11) NOT NULL,
					`img_change` tinyint(1) NOT NULL,
					`img_hover` tinyint(1) NOT NULL,
					`img_use` tinyint(1) NOT NULL,
					`img_limit` tinyint(1) NOT NULL,
					`img_gal` tinyint(1) NOT NULL,
					`img_option` tinyint(1) NOT NULL,
					`img_category` tinyint(1) NOT NULL,
					`img_first` tinyint(1) NOT NULL,
					`img_from_option` tinyint(1) NOT NULL,
					`img_sort` tinyint(1) NOT NULL,
					`img_select` tinyint(1) NOT NULL,
					`img_cart` tinyint(1) NOT NULL,
					`img_radio_checkbox` tinyint(1) NOT NULL,
					`dependent_thumbnails` tinyint(1) NOT NULL,
					FOREIGN KEY (`option_id`) REFERENCES `".DB_PREFIX."option`(`option_id`) ON DELETE CASCADE
				) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");
    
		$this->checkExtraTableColumns();
    
		$this->load->model('setting/setting');
		$msettings = array('poip_module'=>array('img_change'=>1,'img_hover'=>1,'img_main_to_additional'=>1,'img_hover'=>1,'img_use'=>1,'img_limit'=>1,'img_gal'=>1,'img_cart'=>1));
		$this->model_setting_setting->editSetting('poip_module', $msettings);
    
	}
	
	protected function checkExtraTableColumns() {
		if ( !$this->extra_table_columns_checked ) {
			$this->extra_table_columns_checked = true;
			if ( $this->liveopencart_ext_poip->existTable('filter') ) {
				$query = $this->db->query("DESCRIBE `".DB_PREFIX."filter` 'poip_ocfilter_option_value_id' ");
				if ( !$query->num_rows ) {
					$this->db->query("ALTER TABLE `".DB_PREFIX."filter` ADD COLUMN `poip_ocfilter_option_value_id` INT(11) NOT NULL");
				}
			}
			if ( $this->liveopencart_ext_poip->existTable('poip_option_image') ) {
				$query = $this->db->query("DESCRIBE `".DB_PREFIX."poip_option_image` 'relatedoptions_id' ");
				if ( !$query->num_rows ) {
					$this->db->query("ALTER TABLE `".DB_PREFIX."poip_option_image` ADD COLUMN `relatedoptions_id` INT(11) NOT NULL");
					$this->db->query("ALTER TABLE `".DB_PREFIX."poip_option_image` ADD INDEX `relatedoptions_id`(`relatedoptions_id`) ");
				}
			}
		}
	}
	
	public function uninstall() {
    
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_option_image`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_main_option_settings`;");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "poip_option_settings`;");
    
	}
  
}
