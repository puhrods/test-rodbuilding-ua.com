<?php
//---------------------------------------
//Copyright © 2018-2021 by opencart-expert.com 
//All Rights Reserved. 
//---------------------------------------
class ModelExtensionTotalBundleExpertTotal extends Model {






    
    public function getTotal($total) {

        $rr = $this->registry->get('bundle_expert');
        if(isset($rr)){
            $this->bundle_expert->getTotalModule($total['totals'], $total['total'], $total['taxes']);

            $total['totals'] = $total['totals'];
            $total['total'] = $total['total'];
            $total['taxes'] = $total['taxes'];
        }




    }










































































































































}