<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelModuleBundleExpert extends Model {

    public function getKit($kit_id, $kit_unique_id = '', $get_kit_products_settings=array()) {
        return $this->bundle_expert->getKit($kit_id, $kit_unique_id, $get_kit_products_settings);
    }

    public function getWidgetKits($widget_id) {
        return $this->bundle_expert->getWidgetKits($widget_id);
    }

    public function getWidgetsByDisplayMode($display_mode, $data){
        return $this->bundle_expert->getWidgetsByDisplayMode($display_mode, $data);
    }

    public function getKitLinkProducts($kit_id) {
        return $this->bundle_expert->getKitLinkProducts($kit_id);
    }

    public function getKitProducts($kit_id, $settings) {
        return $this->bundle_expert->getKitProducts($kit_id, $settings);
    }


    public function getKitProduct($kit_id, $main_product_id = '', $item_position=-1, $product_id=-1, $admin_mode = false, $kit_unique_id='') {
        return $this->bundle_expert->getKitProduct($kit_id, $main_product_id, $item_position, $product_id, $admin_mode, $kit_unique_id);
    }

    public function getProductCategories($product_id) {
        return $this->bundle_expert->getProductCategories($product_id);
    }

    public function getProductManufacturers($product_id) {
        return $this->bundle_expert->getProductManufacturers($product_id);
    }

    public function getProductFilters($product_id) {
        return $this->bundle_expert->getProductFilters($product_id);
    }

    public function getCategoryProducts($category_id) {
        return $this->bundle_expert->getCategoryProducts($category_id);
    }

    public function findProductDataInKit($product_id, $kit_items, $item_position){
        return $this->bundle_expert->findProductDataInKit($product_id, $kit_items, $item_position);
    }

    public function getKitProductPriceData($kit_info, $kit_product, $product_info, $options){
        return $this->bundle_expert->getKitProductPriceData($kit_info, $kit_product, $product_info, $options);
    }

    public function calculateOptionsPrice($options, $product_id) {
        return $this->bundle_expert->calculateOptionsPrice($options, $product_id);
    }

    public function calculateKitTotal($kit_info, $kit_total){
        return $this->bundle_expert->calculateKitTotal($kit_info, $kit_total);
    }

    public function getKitProductFixedOptionsData($product_options, $kit_product){
        return $this->bundle_expert->getKitProductFixedOptionsData($product_options, $kit_product);

            }

    public function convertProductOptionsToCartOptionsFormat($product_options){
        return $this->bundle_expert->convertProductOptionsToCartOptionsFormat($product_options);
    }

    public function filterProductOptions($kit_product, $product_options){
        return $this->bundle_expert->filterProductOptions($kit_product, $product_options);
    }

    public function isDisableOption($product_option_id,  $disable_options){
        return $this->bundle_expert->isDisableOption($product_option_id,  $disable_options);
    }

    public function isDisableOptionValue($product_option_id, $product_option_value_id,  $disable_options){
        return $this->bundle_expert->isDisableOptionValue($product_option_id, $product_option_value_id,  $disable_options);
    }

    public function isFixedOptionValue($product_option_id, $product_option_value_id,  $fixed_options){
        return $this->bundle_expert->isFixedOptionValue($product_option_id, $product_option_value_id,  $fixed_options);
    }

    public function getProductsByManufacturer($manufacturer_id) {
        return $this->bundle_expert->getProductsByManufacturer($manufacturer_id);
    }

    public function getProductsByFilter($filter_id) {
        return $this->bundle_expert->getProductsByFilter($filter_id);
    }

    public function getOrderProductKitInfo($order_product_id){
        return $this->bundle_expert->getOrderProductKitInfo($order_product_id);
    }

    
    public function checkIsLinkProduct($product_id, $kit_id){
        return $this->bundle_expert->checkIsLinkProduct($product_id, $kit_id);
    }

    public function checkCustomPageUrl($widget){
        return $this->bundle_expert->checkCustomPageUrl($widget);
    }

    public function filterKitItemProducts($kit_items, $only_first=true){
        return $this->bundle_expert->filterKitItemProducts($kit_items, $only_first);
    }

    public function checkOptionInProduct($option, $product_options){
        return $this->bundle_expert->checkOptionInProduct($option, $product_options);
    }

    public function addWidgetHistoryStatus($widget_id, $widget_history_code){
        return $this->bundle_expert->addWidgetHistoryStatus($widget_id, $widget_history_code);
    }
    

}
