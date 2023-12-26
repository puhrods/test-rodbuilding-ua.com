<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ControllerExtensionModuleBundleExpert extends Controller {
	public function index($setting) {
        $bundle_expert = $this->bundle_expert;
        if(isset($bundle_expert)) {
            if ($this->config->get('bundle_expert_status_for_customer')) {
                if ($setting) {
                    return $this->load->controller('module/bundle_expert', $setting);
                }
            }
        }
	}

}