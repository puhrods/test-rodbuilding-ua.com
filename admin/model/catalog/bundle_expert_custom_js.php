<?php
class ModelCatalogBundleExpertCustomJs extends Model {
    public function getHeaderCartJsTemplates(){
        $list = array();

        $element = array(
            'name' =>   $this->language->get('text_custom_header_cart_js_template_new'),
            'file' => '_blank.js',
        );

        $list[] = $element;

        $element = array(
          'name' => 'MoneyMaker #01',
          'file' => 'money_maker_01.js'
        );

        $list[] = $element;

        foreach ($list as $index=>$item) {
            $list[$index]['id'] = $item['file'];
        }

        return $list;

    }


    public function getHeaderCartJsTemplatesById($id){
        $result = null;

        $list = $this->getHeaderCartJsTemplates();

        foreach ($list as $item) {
            if ($item['id'] == $id) {
                $result = $item;

                $file = DIR_APPLICATION . 'view/template/catalog/bundle_expert/header_cart_js/' . $item['file'];
                if(file_exists($file)){
                    $script = file_get_contents($file);

                    $description_text = '//json[\'success\'] - Success text
//json[\'total\'] - Cart total info
//json[\'total_count\'] - cart products count
//json[\'total_price\'] - cart total
//json[\'action_code\'] - add_to_cart, remove_from_cart, update_in_cart

//Script code Start here
    
    ';

                    $script = $description_text . $script;

                    $result['script'] = $script;
                    break;
                }else{
                    continue;
                }

            }
        }

        return $result;

    }

    public function createHeaderCartJsFile($script) {

        $script = htmlspecialchars_decode($script);
        $source_file = DIR_APPLICATION . 'view/template/catalog/bundle_expert/header_cart_js/' . '_javascript_template.js';

        if(file_exists($source_file)){
            $source_script = file_get_contents($source_file);

            $target_file = DIR_CATALOG . 'view/javascript/bundle-expert/' . 'bundle-expert-custom-header-cart.js';

            $result = str_replace('{{script_template}}',$script,$source_script );

            file_put_contents($target_file, $result);
        }


    }


}
