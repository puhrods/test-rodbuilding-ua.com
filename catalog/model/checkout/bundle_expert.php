<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelCheckoutBundleExpert extends Model {
    
    
    
    public function checkKitActuality($kit_items, $kit_info){
        return $this->bundle_expert->checkKitActuality($kit_items, $kit_info);
    }

    
    
    public function getKitEnableStatus($kit_info, $kit_items, $main_product_id, $main_product_in_cart = false, $kit_in_cart = false, $status_log = true){
        return $this->bundle_expert->getKitEnableStatus($kit_info, $kit_items, $main_product_id, $main_product_in_cart, $kit_in_cart, $status_log);
    }

    public function getCartProductsQuantity(){
        return $this->bundle_expert->getCartProductsQuantity();
    }

    public function getCartOptionsQuantity(){
        return $this->bundle_expert->getCartOptionsQuantity();
    }


    public function checkKitItemProductStock($kit_item_product, $quantity_data){
        return $this->bundle_expert->checkKitItemProductStock($kit_item_product, $quantity_data);
    }

    
    public function getCartFreeProducts(){
        return $this->bundle_expert-> getCartFreeProducts();
    }

    public function getFreeMainProductInCart($product_id, $quantity) {
        return $this->bundle_expert->getFreeMainProductInCart($product_id, $quantity);
    }

    public function setPresetOptions($target_options, $source_options){
        return $this->bundle_expert->setPresetOptions($target_options, $source_options);
    }

    public function getCartKit($kit_unique_id, $admin_mode=false){
        return $this->bundle_expert->getCartKit($kit_unique_id, $admin_mode);
    }

    public function getCartKitMainProduct($kit_unique_id){
        return $this->bundle_expert->getCartKitMainProduct($kit_unique_id);
    }

    public function getOrderKits($order_id){
        return $this->bundle_expert->getOrderKits($order_id);
    }

    public function updateKitQuantity($kit_id, $quantity, $kit_from_cart_unique_id=''){
        return $this->bundle_expert->updateKitQuantity($kit_id, $quantity, $kit_from_cart_unique_id);
    }

    public function addOrderKitHistory($kit_id, $kit_unique_id, $main_product_id, $order_id){
        return $this->bundle_expert->addOrderKitHistory($kit_id, $kit_unique_id, $main_product_id, $order_id);
    }

    public function getKitInfoFromOrderHistory($kit_unique_id) {
        return $this->bundle_expert->getKitInfoFromOrderHistory($kit_unique_id);
    }

    
    public function checkProductHasOption($product_id, $product_option_id, $value){
        return $this->bundle_expert->checkProductHasOption($product_id, $product_option_id, $value);
    }

    public function checkCartProductHasDisabledOption($product_id, $product_option_id, $value, $disable_options){
        return $this->bundle_expert->checkCartProductHasDisabledOption($product_id, $product_option_id, $value, $disable_options);
    }

    public function checkCartProductHasFixedOption($product_id, $options, $fixed_option){
        return $this->bundle_expert->checkCartProductHasFixedOption($product_id, $options, $fixed_option);
    }

    public function findKitItemProduct($item_position, $item_product_id, $kit_items){
        return $this->bundle_expert->findKitItemProduct($item_position, $item_product_id, $kit_items);
    }

    public function getCartKitCount($kit_info, $main_product_id, $kits_in_cart = null){
        return $this->bundle_expert->getCartKitCount($kit_info, $main_product_id, $kits_in_cart);
    }


    public function correctCartQuantities($kit_items, $quantity_data){
        return $this->bundle_expert->correctCartQuantities($kit_items, $quantity_data);
    }

    public function addKitHistoryStatus($kit_info, $kit_enable_status){
        return $this->bundle_expert->addKitHistoryStatus($kit_info, $kit_enable_status);
    }
    
}