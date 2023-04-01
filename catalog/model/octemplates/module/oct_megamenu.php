<?php
/**************************************************************/
/*	@copyright	OCTemplates 2019.							  */
/*	@support	https://octemplates.net/					  */
/*	@license	LICENSE.txt									  */
/**************************************************************/

class ModelOCTemplatesModuleOctMegamenu extends Model {
    public function getMegamenus() {
        $customer_group_id = ($this->customer->isLogged()) ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id');
        
        $query = $this->db->query("
        	SELECT DISTINCT * FROM `" . DB_PREFIX . "oct_megamenu` ocmm 
        	LEFT JOIN `" . DB_PREFIX . "oct_megamenu_description` ocmmd 
        		ON (ocmm.megamenu_id = ocmmd.megamenu_id) 
        	LEFT JOIN `" . DB_PREFIX . "oct_megamenu_to_customer_group` ocmm2cg 
        		ON (ocmm.megamenu_id = ocmm2cg.megamenu_id) 
        	LEFT JOIN `" . DB_PREFIX . "oct_megamenu_to_store` ocmm2s 
        		ON (ocmm.megamenu_id = ocmm2s.megamenu_id) 
        	WHERE 
        		ocmm2cg.customer_group_id = '" . (int) $customer_group_id . "' 
        		AND ocmm2s.store_id = '" . (int) $this->config->get('config_store_id') . "' 
        		AND ocmmd.language_id = '" . (int) $this->config->get('config_language_id') . "' 
        		AND ocmm.status = '1' 
        	ORDER BY 
        		ocmm.sort_order ASC
        ");
        
        return $query->rows;
    }
    
    public function getMegamenuCategory($megamenu_id) {
        $category_oct_megamenu_data = [];
        
        $query = $this->db->query("
        	SELECT * FROM `" . DB_PREFIX . "oct_megamenu_category` 
        	WHERE 
        		megamenu_id = '" . (int) $megamenu_id . "'
        ");
        
        foreach ($query->rows as $result) {
            $category_oct_megamenu_data[] = $result['category_id'];
        }
        
        return $category_oct_megamenu_data;
    }
    
    public function getMegamenuBlogCategory($megamenu_id) {
        $category_oct_megamenu_data = [];
        	
        if ($this->checkOctTable('oct_blogcategory')) {
	        $query = $this->db->query("
	        	SELECT * FROM `" . DB_PREFIX . "oct_megamenu_blogcategory` 
	        	WHERE 
	        		megamenu_id = '" . (int) $megamenu_id . "'
	        ");
	        
	        foreach ($query->rows as $result) {
	            $category_oct_megamenu_data[] = $result['blogcategory_id'];
	        }
        }
        
        return $category_oct_megamenu_data;
    }
    
    private function checkOctTable($table) {
	    $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $table . "'");
	    
	    return $query->num_rows;
    }
    
    public function getMegamenuManufacturer($megamenu_id) {
        $manufacturer_oct_megamenu_data = [];
        
        $query = $this->db->query("
        	SELECT * FROM `" . DB_PREFIX . "oct_megamenu_manufacturer` 
        	WHERE 
        		megamenu_id = '" . (int) $megamenu_id . "'
        ");
        
        foreach ($query->rows as $result) {
            $manufacturer_oct_megamenu_data[] = $result['manufacturer_id'];
        }
        
        return $manufacturer_oct_megamenu_data;
    }
    
    public function getMegamenuInformation($megamenu_id) {
        $information_oct_megamenu_data = [];
        
        $query = $this->db->query("
        	SELECT * FROM `" . DB_PREFIX . "oct_megamenu_information` 
        	WHERE 
        		megamenu_id = '" . (int) $megamenu_id . "'
        ");
        
        foreach ($query->rows as $result) {
            $information_oct_megamenu_data[] = $result['information_id'];
        }
        
        return $information_oct_megamenu_data;
    }
    
    public function getMegamenuProduct($megamenu_id) {
        $product_oct_megamenu_data = [];
        
        $query = $this->db->query("
        	SELECT * FROM `" . DB_PREFIX . "oct_megamenu_product` 
        	WHERE 
        		megamenu_id = '" . (int) $megamenu_id . "'
        ");
        
        foreach ($query->rows as $result) {
            $product_oct_megamenu_data[] = $result['product_id'];
        }
        
        return $product_oct_megamenu_data;
    }
}