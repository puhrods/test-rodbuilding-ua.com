<?php 

class ControllerExtensionModuleRemoveDemoData extends Controller
{
  private $categories_status = "unchanged";
  private $products_status = "unchanged";
  private $products_images_status = "unchanged";
  private $recurring_profiles_status = "unchanged";
  private $filters_status = "unchanged";
  private $attributes_status = "unchanged";
  private $attributegroups_status = "unchanged";
  private $options_status = "unchanged";
  private $manufacturers_status = "unchanged";
  private $coupons_status = "unchanged";
  private $blogs_status = "unchanged";
  private $statusMessage = "The data for the following components has been deleted: <br/>";
  private $componentsCounter = 0;
	private $error = array();
	
	public function index() {  
    if (($this->request->server['REQUEST_METHOD'] == 'POST')){
      if(isset($this->request->post['categories'])){
        $this->load->model('catalog/category');
        $categories = $this->model_catalog_category->getCategories([]);
        foreach($categories as $category){
          $this->model_catalog_category->deleteCategory($category['category_id']);
        }
        $this->categories_status = "deleted";
        $this->statusMessage .= "categories";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['products'])){
        $this->load->model('catalog/product');
        $products = $this->model_catalog_product->getProducts([]);
        foreach($products as $product){

          if(isset($this->request->post['productimages'])){
            if(!empty($product['image'])){
              $productMainImage = $product['image'];
              $file_path_main = DIR_IMAGE . $productMainImage;
              if(file_exists($file_path_main)){
                unlink($file_path_main);
              }
            }

            $productImages = $this->model_catalog_product->getProductImages($product['product_id']);
            foreach($productImages as $productImage){
              if(!empty($productImage['image'])){
                $file_path_image = DIR_IMAGE .$productImage['image'];
                if(file_exists($file_path_image)){
                  unlink($file_path_image);
                }
              }
            }
          }
          $this->products_images_status = "deleted";
          $this->model_catalog_product->deleteProduct($product['product_id']);
        }

        $this->products_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "products";
        $this->componentsCounter++;

        if($this->products_images_status === "deleted"){
          if($this->componentsCounter>0){
            $this->statusMessage .= ", ";
          }
          $this->statusMessage .= "products images";
          $this->componentsCounter++;
        }

      }
      if(isset($this->request->post['recurringprofiles'])){
        $this->load->model('catalog/recurring');
        $recurringProfiles = $this->model_catalog_recurring->getRecurrings([]);
        foreach($recurringProfiles as $recurringProfile){
          $this->model_catalog_recurring->deleteRecurring($recurringProfile['recurring_id']);
        }
        $this->recurring_profiles_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "recurring profiles";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['filters'])){
        $this->load->model('catalog/filter');
        $filters = $this->model_catalog_filter->getFilters([]);
        foreach($filters as $filter){
          $this->model_catalog_filter->deleteFilter($filter['filter_id']);
        }
        $this->filters_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "filters";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['attributes'])){
        $this->load->model('catalog/attribute');
        $attributes = $this->model_catalog_attribute->getAttributes([]);
        foreach($attributes as $attribute){
          $this->model_catalog_attribute->deleteAttribute($attribute['attribute_id']);
        }
        $this->attributes_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "attributes";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['attributegroups'])){
        $this->load->model('catalog/attribute_group');
        $attributegroups = $this->model_catalog_attribute_group->getAttributeGroups([]);
        foreach($attributegroups as $attributegroup){
          $this->model_catalog_attribute_group->deleteAttributeGroup($attributegroup['attribute_group_id']);
        }
        $this->attributegroups_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "attribute groups";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['options'])){
        $this->load->model('catalog/option');
        $options = $this->model_catalog_option->getOptions([]);
        foreach($options as $option){
          $this->model_catalog_option->deleteOption($option['option_id']);
        }
        $this->options_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "options";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['manufacturers'])){
        $this->load->model('catalog/manufacturer');
        $manufacturers = $this->model_catalog_manufacturer->getManufacturers([]);
        foreach($manufacturers as $manufacturer){
          $this->model_catalog_manufacturer->deleteManufacturer($manufacturer['manufacturer_id']);
        }
        $this->manufacturers_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "manufacturers";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['coupons'])){
        $this->load->model('marketing/coupon');
        $coupons = $this->model_marketing_coupon->getCoupons([]);
        foreach($coupons as $coupon){
          $this->model_marketing_coupon->deleteCoupon($coupon['coupon_id']);
        }
        $this->coupons_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "coupons";
        $this->componentsCounter++;
      }
      if(isset($this->request->post['blogs'])){
        $this->load->model('templatetrip/ttblog');
        $blogs = $this->model_templatetrip_ttblog->getBlogs([]);
        foreach($blogs as $blog){
          $this->model_templatetrip_ttblog->deleteBlog($blog['tt_blog_id']);
        }
        $this->blogs_status = "deleted";
        if($this->componentsCounter>0){
          $this->statusMessage .= ", ";
        }
        $this->statusMessage .= "blogs";
        $this->componentsCounter++;
      }
      if($this->componentsCounter > 0){
        $data['statusMessage'] = $this->statusMessage;
      }
    }

    //$status = unlink(DIR_IMAGE."catalog/demo/product/iphone.jpg");

    $this->load->language('extension/module/remove_demo_data');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('setting/module');
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    $this->response->setOutput($this->load->view('extension/module/remove_demo_data', $data));
	}
	

	protected function editModule($module_id) {
		$data = array(); 
		$data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
		$htmlOutput = $this->load->view('extension/module/remove_demo_data', $data);
		$this->response->setOutput($htmlOutput);
	}
	public function validate() {
	}
	
	public function install() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('module_remove_demo_data', ['module_remove_demo_data_status'=>1]);
	}
	
	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_remove_demo_data');
	}


}